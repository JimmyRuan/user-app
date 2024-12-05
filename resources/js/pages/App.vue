<!-- resources/js/components/App.vue -->
<template>
    <div>
        <FlashMessage />
        <LoadingModal />
        <router-view />
    </div>
</template>

<script>
import FlashMessage from "../components/notifications/FlashMessage.vue";
import LoadingModal from "../components/LoadingModal.vue";

export default {
    name: 'App',
    components: {LoadingModal, FlashMessage},
};
</script>
