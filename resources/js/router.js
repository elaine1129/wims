import { createWebHistory, createRouter } from 'vue-router';
import TestVueRouter from './components/pages/testvuerouter';
import CheckInOutStock from './staff/check-in-out-stock'
import ViewInventory from './staff/view-inventory';
import ManageInventory from './admin/manage-inventory';
import Dashboard from './components/dashboard-test';
import Login from './auth/login';
import store from './store';

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
        name: 'Login',
        component: Login
    },
    {
        path: '/',
        redirect: to => {
            if (store.state.user.role == 'Staff') {
                return { path: '/staff-check-in-out-stock' }
            }
            return { path: '/admin-manage-inventory' }

        },
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true },
        children: [
            {
                path: '/staff-check-in-out-stock',
                name: 'staff-check-in-out-stock',
                component: CheckInOutStock

            },
            {
                path: '/staff-view-inventory',
                name: 'view-inventory',
                component: ViewInventory
            },
            {
                path: '/admin-manage-inventory',
                name: 'admin-manage-inventory',
                component: ManageInventory
            }
        ]
    }
]
const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !store.state.user.token) {
        next({ name: 'Login' });
    } else if (store.state.user.token && (to.name === 'Login')) {
        next({ name: 'Dashboard' });
    }
    else {
        next();
    }
})
export default router;