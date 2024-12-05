<template>
    <div
        v-if="visible"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
        @click.self="closeModal"
    >
    <div
        class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-title"
    >
        <!-- Close Button -->
        <button
            @click="closeModal"
            class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 focus:outline-none"
            aria-label="Close modal"
        >
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Modal Content -->
        <div class="p-6">
            <!-- User Details -->
            <h2 id="modal-title" class="text-2xl font-semibold mb-4 text-gray-800">
                User Details
            </h2>
            <template v-if="userDetails">
                <div class="space-y-2">
                    <div class="flex items-center">
                        <i class="fas fa-id-badge text-gray-500 mr-2"></i>
                        <p class="text-gray-700"><strong>ID:</strong> {{ userDetails.id }}</p>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-user text-gray-500 mr-2"></i>
                        <p class="text-gray-700"><strong>Name:</strong> {{ userDetails.name }}</p>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-birthday-cake text-gray-500 mr-2"></i>
                        <p class="text-gray-700"><strong>Date of Birth:</strong> {{ userDetails.date_of_birth }}</p>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-calendar-alt text-gray-500 mr-2"></i>
                        <p class="text-gray-700"><strong>Created On:</strong> {{ formatDate(userDetails.created_on) }}</p>
                    </div>
                </div>
            </template>
            <p v-else class="text-gray-500">Loading user details...</p>
        </div>

        <!-- Action Buttons (Optional) -->
        <div class="bg-gray-100 px-6 py-4 flex justify-center">
            <!-- Edit Button -->
            <button
                @click="handleEditClick"
                class="bg-green-500 text-white w-28 py-2 rounded-md hover:bg-green-600 focus:outline-none mr-2"
            >
                Edit
            </button>

            <button
                @click="closeModal"
                class="bg-blue-500 text-white w-28 py-2 rounded-md hover:bg-blue-600 focus:outline-none"
            >
                Close
            </button>
        </div>
    </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import {formatDate} from "../services/helper.js";

export default {
    name: 'UserDetailModal',
    props: {
        visible: {
            type: Boolean,
            default: false,
        },
        userId: {
            type: Number,
            default: null,
        },
    },
    computed: {
        ...mapGetters('users', ['getUserDetails']),
        userDetails() {
            return this.getUserDetails(this.userId);
        },
    },
    methods: {
        formatDate,
        closeModal() {
            this.$emit('close');
        },
        handleEditClick() {
            console.log("I am here at 100", {id: this.userDetails.id});

            if (this.userDetails && this.userDetails.id) {
                this.$emit('editUser', this.userDetails.id);
            } else {
                console.error('User details are not available.');
            }
        },
    },
};
</script>

<style scoped>
/* Add transition for the modal background */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>
