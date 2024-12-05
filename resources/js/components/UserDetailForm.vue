<template>
    <div
        v-if="visible"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
        @click.self="closeForm"
    >
        <div
            class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden"
            role="dialog"
            aria-modal="true"
            aria-labelledby="form-title"
        >
            <!-- Close Button -->
            <button
                @click="closeForm"
                class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 focus:outline-none"
                aria-label="Close form"
            >
                <i class="fas fa-times text-xl"></i>
            </button>

            <!-- Form Content -->
            <div class="p-6">
                <h2 id="form-title" class="text-2xl font-semibold mb-4 text-gray-800">
                    Edit User Details
                </h2>
                <form @submit.prevent="submitForm">
                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter user name"
                        />
                    </div>

                    <div class="mb-4">
                        <label for="date-of-birth" class="block text-gray-700 font-medium mb-2">
                            Date of Birth
                        </label>
                        <input
                            id="date-of-birth"
                            v-model="form.date_of_birth"
                            type="date"
                            class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button
                            type="button"
                            @click="closeForm"
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
    name: 'UserDetailForm',
    props: {
        visible: {
            type: Boolean,
            default: false,
        },
        userId: {
            type: [Number, null],
            required: true,
        },
    },
    data() {
        return {
            form: {
                name: '',
                date_of_birth: '',
            },
        };
    },
    watch: {
        userId: {
            immediate: true,
            handler(newUserId) {
                if (newUserId) {
                    this.loadUserDetails(newUserId);
                } else {
                    this.clearForm();
                }

            },
        },
    },
    methods: {
        ...mapActions("loading", ["showLoading", "hideLoading"]),
        ...mapActions("notification", ["triggerNotification"]),
        ...mapActions('users', ['fetchUserDetails', 'updateUserDetails', 'createUserDetails']),
        async loadUserDetails(userId) {
            try {
                const userDetails = await this.fetchUserDetails(userId);
                this.form.name = userDetails.name;
                this.form.date_of_birth = userDetails.date_of_birth;
            } catch (error) {
                console.error('Failed to load user details:', error);
            }
        },
        async submitForm() {
            try {
                this.showLoading();
                let action;
                if (this.userId) {
                    await this.updateUserDetails({ id: this.userId, data: this.form });
                    action = 'updated';
                } else {
                    await this.createUserDetails(this.form);
                    action = 'created';
                    this.$router.push({ name: 'home' });
                }
                await this.triggerNotification({
                    message: `User details have been ${action}.`,
                    type: "info",
                    timeout: 5000,
                });
                this.closeForm();
            } catch (error) {
                console.error('Failed to update user details:', error);
                await this.triggerNotification({
                    message: "Failed to update user detail, please try later",
                    type: "error",
                    timeout: 5000,
                });
            } finally {
                this.hideLoading();
            }
        },
        closeForm() {
            //clear the form whenever the form is closed
            this.$emit('close');
        },
        clearForm() {
            this.form = {};
        }
    },
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
