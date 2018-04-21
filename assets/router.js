import Vue from 'vue';
import VueRouter from 'vue-router';

import Home from './pages/Home.vue';
import About from './pages/About.vue';
import Contact from './pages/Contact.vue';
import Product from './pages/Product.vue';
import Login from './pages/Login.vue';
import AdminMain from './pages/Admin/Main.vue';
import AdminContact from './pages/Admin/Contact.vue';
import AdminMisc from './pages/Admin/Misc.vue';

Vue.use(VueRouter);

export default new VueRouter({
    mode: 'history',
    base: __dirname,
    routes: [
        { path: '/', name: 'home', component: Home },
        { path: '/category/:categoryId', name: 'category', component: Home },
        { path: '/product/:categoryId/:productId', name: 'product', component: Product },
        { path: '/about', name: 'about', component: About },
        { path: '/contact', name: 'contact', component: Contact },
        { path: '/login', name: 'login', component: Login },
        { path: '/admin/main', alias: '/admin', name: 'admin-main', component: AdminMain, meta: {auth: true} },
        { path: '/admin/contact', name: 'admin-contact', component: AdminContact, meta: {auth: true} },
        { path: '/admin/misc', name: 'admin-misc', component: AdminMisc, meta: {auth: true} }
    ]
});
