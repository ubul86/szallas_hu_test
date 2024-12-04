import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/views/HomeView.vue';
import NotFoundView from '@/views/NotFoundView.vue'
import CompanyView from "@/views/CompanyView.vue";

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home,
    },
    {
        path: '/company/:id',
        name: 'CompanyView',
        component: CompanyView,
        props: true,
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
