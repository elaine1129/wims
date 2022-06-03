import { createWebHistory, createRouter } from 'vue-router';
import TestVueRouter from './components/pages/testvuerouter';
import CheckInOutStock from './staff/check-in-out-stock'
const routes = [
    {
        path: '/test-vue-router',
        component: TestVueRouter
    },
    {
        path: '/staff-check-in-out-stock',
        component: CheckInOutStock
    }
]
const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;