import { createWebHistory, createRouter } from 'vue-router';
import TestVueRouter from './components/pages/testvuerouter';
import CheckInOutStock from './staff/check-in-out-stock'
import Dashboard from './components/dashboard-test';
import Login from './components/pages/login';

const routes = [
    {
        path: '/test-vue-router',
        component: TestVueRouter
    },
    // {
    //     path: '/staff-check-in-out-stock',
    // },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/',
        redirect: '/staff-check-in-out-stock',
        name: 'dashboard',
        component: Dashboard,
        children: [
            {
                path: '/staff-check-in-out-stock',
                name: 'Check In/Out Stock',
                component: CheckInOutStock

            }
        ]
    }
]
const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;