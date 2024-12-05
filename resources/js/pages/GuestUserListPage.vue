<template>
    <div class="max-w-4xl mx-auto px-4 py-8 min-h-[70vh]">
        <h1 class="text-3xl font-bold mb-4 text-center">List of Users</h1>

        <!-- Add New User Button -->
        <div class="text-center mb-4">
            <button
                @click="addNewUser"
                class="bg-green-500 text-white px-6 py-2 rounded-md hover:bg-green-600 focus:outline-none"
            >
                Add New User
            </button>
        </div>

        <!-- Display users -->
        <transition-group name="list" tag="ul" class="divide-y divide-gray-200">
            <li v-for="user in users" :key="user.id" class="py-4">
                <div>
                    <p class="font-semibold text-gray-800">{{ user.name }}</p>
                    <p class="text-gray-600">Date of Birth: {{ user.date_of_birth }}</p>
                    <p class="text-gray-600">Created On: {{ formatDate(user.created_on) }}</p>
                    <p class="text-gray-600">User ID: {{ user.id }}</p>
                </div>
                <div class="flex space-x-2">
                    <!-- Show Button -->
                    <button
                        @click="showUserDetails(user.id)"
                        class="w-20 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        Show
                    </button>

                    <!-- Edit Button -->
                    <button
                        @click="editUser(user.id)"
                        class="w-20 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        Edit
                    </button>

                    <!-- Delete Button -->
                    <button
                        @click="deleteUser(user.id)"
                        :disabled="deletingUsers.includes(user.id)"
                        class="w-20 py-2 text-white rounded shadow"
                        :class="deletingUsers.includes(user.id)
                            ? 'bg-gray-400 cursor-not-allowed'
                            : 'bg-red-500 hover:bg-red-600'"
                    >
                        {{ deletingUsers.includes(user.id) ? 'Deleting...' : 'Delete' }}
                    </button>
                </div>
            </li>
        </transition-group>

        <!-- Display message if no users -->
        <p v-if="!users.length" class="text-center text-gray-600">
            There are no users at this moment. Please go to previous pages at page {{ pagination.current_page }}
        </p>

        <!-- Display pagination -->
        <pagination
            v-if="pagination.total > pagination.per_page && users.length > 0"
            :pagination="pagination"
            @page-changed="updatePage"
        />

        <!-- User Detail Modal -->
        <UserDetailModal
            :visible="isModalVisible"
            :userId="selectedUserId"
            @close="isModalVisible = false"
            @editUser="editUser"
        />

        <UserDetailForm
            :visible="isEditFormVisible"
            :userId="selectedUserId"
            @close="isEditFormVisible = false"
        />
    </div>
</template>

<script>
import Pagination from "../components/Pagination.vue";
import { mapActions, mapState } from "vuex";
import _ from "lodash";
import UserDetailModal from "../components/UserDetailModal.vue";
import {formatDate} from "../services/helper.js";
import UserDetailForm from "../components/UserDetailForm.vue";

export default {
    name: "UsersListPage",
    components: {
        UserDetailForm,
        UserDetailModal,
        Pagination,
    },
    props: {
        page: {
            type: Number,
            default: 1,
        },
    },
    data() {
        return {
            pagination: {
                current_page: this.page,
                per_page: 10,
                total: 0,
                last_page: 1,
            },
            deletingUsers: [],
            isModalVisible: false,
            selectedUserId: null,
            isEditFormVisible: false,
        };
    },
    computed: {
        ...mapState("users", ["usersPages"]),
        users() {
            return this.usersPages[this.pagination.current_page]?.data || [];
        },
    },
    watch: {
        // Fetch data when page changes
        page: {
            immediate: true,
            handler(newPage) {
                this.fetchUsers(newPage);
            },
        },
    },
    methods: {
        formatDate,
        ...mapActions("loading", ["showLoading", "hideLoading"]),
        ...mapActions("notification", ["triggerNotification"]),
        ...mapActions("users", ["fetchUsersPage", "deleteUserInfo", "fetchUserDetails"]),
        async fetchUsers(page = 1) {
            this.showLoading();

            try {
                const pageData = await this.fetchUsersPage(page);

                if (pageData) {
                    const { meta } = pageData;
                    this.pagination = {
                        current_page: meta.current_page || 1,
                        per_page: meta.per_page || 10,
                        total: meta.total || 0,
                        last_page: meta.last_page || 1,
                    };
                }

                // Update the URL only if the page changes
                if (this.$route.query.page !== String(page)) {
                    this.$router.push({ name: 'home', query: { page } });
                }
            } catch (error) {
                console.error(error);
                await this.triggerNotification({
                    message: 'Failed to load users. Please try again later.',
                    type: 'error',
                    timeout: 5000,
                });
            } finally {
                this.hideLoading();
            }
        },
        updatePage(newPage) {
            this.fetchUsers(newPage);
        },
        async deleteUser(userId) {
            if (this.deletingUsers.includes(userId)) return;

            this.deletingUsers.push(userId);
            console.log("I am here 177", userId);

            try {
                await this.deleteUserInfo({
                    userId,
                    currentPage: this.pagination.current_page,
                });

                await this.triggerNotification({
                    message: "User deleted successfully.",
                    type: "info",
                    timeout: 3000,
                });
            } catch (error) {
                console.error(error);
                await this.triggerNotification({
                    message: "Failed to delete user. Please try again later.",
                    type: "error",
                    timeout: 5000,
                });
            } finally {
                this.deletingUsers = this.deletingUsers.filter((id) => id !== userId);
            }
        },
        editUser(userId) {
            this.selectedUserId = userId;
            this.isEditFormVisible = true;
            this.isModalVisible = false;
        },
        addNewUser() {
            this.editUser(null);
        },
        async showUserDetails(userId) {
            try {
                this.showLoading();
                await this.fetchUserDetails(userId);
                this.selectedUserId = userId;
                this.isModalVisible = true;
            } catch (error) {
                console.error("Failed to load user details:", error);
                await this.triggerNotification({
                    message: "Failed to load user details. Please try again later.",
                    type: "error",
                    timeout: 5000,
                });
            } finally {
                this.hideLoading();
            }
        },
    },
};
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
    transition: opacity 0.5s, transform 0.5s;
}
.list-enter,
.list-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
</style>
