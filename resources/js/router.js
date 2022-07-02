import { createWebHistory, createRouter } from 'vue-router';
import TestVueRouter from './components/pages/testvuerouter';
import CheckInOutStock from './pages/staff/check-in-out-stock'
import ViewInventory from './pages/staff/view-inventory';
import CycleCounting from './pages/staff/cycle-counting';
import ManageInventory from './pages/admin/manage-inventory';
import Dashboard from './components/dashboard-test';
import ManageCycleCounting from './pages/manager/manage-cycle-counting';
import ViewCycleCounting from './pages/manager/view-cycle-counting';
import StartCycleCounting from './pages/manager/start-cycle-counting';
import ManagerViewInventory from './pages/manager/view-inventory';
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
            if (store.getters.getUser.role == 'Staff') {
                return { path: '/staff-check-in-out-stock' }
            }
            else if (store.getters.getUser.role == 'Admin') {
                return { path: '/admin-manage-inventory' }
            }
            else {
                return { path: '/manager-view-inventories' }
            }

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
                path: '/staff-cycle-counting',
                name: 'cycle-counting',
                component: CycleCounting
            },
            {
                path: '/admin-manage-inventory',
                name: 'admin-manage-inventory',
                component: ManageInventory
            },
            {
                path: '/manager-manage-cycle-counting',
                name: 'manager-manage-cycle-counting',
                component: ManageCycleCounting
            },
            {
                path: '/manager-start-cycle-counting',
                name: 'manager-start-cycle-counting',
                component: StartCycleCounting
            },
            {
                path: '/manager-view-cycle-counting',
                name: 'manager-view-cycle-counting',
                component: ViewCycleCounting
            },
            {
                path: '/manager-view-inventories',
                name: 'manager-view-inventories',
                component: ManagerViewInventory
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