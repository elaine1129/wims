import { createWebHistory, createRouter } from 'vue-router';
import TestVueRouter from './components/pages/testvuerouter';
const routes = [
    {
        path: '/test-vue-router',
        component: TestVueRouter
    }
]
const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;