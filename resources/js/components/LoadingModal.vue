<template>
    <div v-if="isLoading" class="loading-modal">
        <div class="loading-animation">
            <div class="loading-ball"></div>
            <div class="loading-ball"></div>
            <div class="loading-ball"></div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    name: 'LoadingModal',
    computed: {
        ...mapGetters('loading', ['isLoading']),
    },
};
</script>

<style scoped>
.loading-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000; /* Ensure it appears above other content */
}

.loading-animation {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 60px;
}

.loading-ball {
    width: 12px;
    height: 12px;
    background-color: #4F46E5; /* Customize color */
    border-radius: 50%;
    animation: bounce 0.6s infinite alternate;
}

.loading-ball:nth-child(2) {
    animation-delay: 0.2s;
}

.loading-ball:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes bounce {
    from {
        transform: translateY(0);
    }
    to {
        transform: translateY(-20px);
    }
}
</style>
