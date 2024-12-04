import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/views/HomeView.vue';
import NotFoundView from '@/views/NotFoundView.vue'

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home,
    },
    {
        path: '/:catchAll(.*)',
        name: 'NotFound',
        component: NotFoundView,
        beforeEnter: (to, from, next) => {
            if (!to.path.startsWith('/api')) {
                next();
            } else {
                next(false);
            }
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
