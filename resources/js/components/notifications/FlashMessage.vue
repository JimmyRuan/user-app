<template>
    <div>
        <!-- Filler div to occupy space when flash message is visible -->
        <div
            v-if="message"
            class="relative w-full"
            :style="{ height: fillerHeight + 'px' }"
        ></div>

        <!-- Flash message itself -->
        <transition name="fade">
            <div
                v-if="message"
                class="fixed top-0 left-0 w-full p-4 z-50 text-white"
                :class="{
                'bg-green-500': type === 'info',
                'bg-yellow-500': type === 'warning',
                'bg-red-500': type === 'error',
              }"
            >
                <div class="flex justify-center items-center">
                    <span class="flex-grow text-center">{{ message }}</span>
                    <button @click="clearNotification" class="ml-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex';

export default {
    data() {
        return {
            fillerHeight: 55, // Default height of the flash message (adjustable as needed)
        };
    },
    computed: {
        ...mapGetters('notification', ['message', 'type', 'timeout']),
    },
    methods: {
        ...mapActions('notification', ['clearNotification']),
    },
    watch: {
        message(newMessage) {
            if (newMessage && this.timeout !== null && this.timeout !== undefined) {
                // Trigger timeout only if timeout is not null
                setTimeout(() => {
                    this.clearNotification();
                }, this.timeout || 3000); // Default to 3 seconds if no timeout is provided
            }
        },
    },
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter, .fade-leave-to {
    opacity: 0;
}

/* Ensure the notification is not overlapping the navigation bar */
.fixed {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000; /* Ensures it stays above the navigation bar */
}

.flex-grow {
    flex-grow: 1;
}
</style>
