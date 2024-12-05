import { createRouter, createWebHistory } from 'vue-router';
import GuestUserListPage from "./pages/GuestUserListPage.vue";

const routes = [
    {
        path: '/',
        name: 'home',
        component: GuestUserListPage,
        props: route => ({ page: parseInt(route.query.page) || 1 }),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
