import Vue from 'vue';

import VueResource from 'vue-resource';

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/ru-RU';

import auth from '@websanova/vue-auth/dist/v2/vue-auth.common.js';
import authBearer from '@websanova/vue-auth/dist/drivers/auth/bearer.js';

import httpVueResource from '@websanova/vue-auth/dist/drivers/http/vue-resource.1.x.js';
import routerVueRouter from '@websanova/vue-auth/dist/drivers/router/vue-router.2.x.js';

import './app.css';
import store from './store';
import router from './router';

import AppLayout from './AppLayout.vue';

Vue.use(ElementUI, { locale });
Vue.use(VueResource);

Vue.http.options.root = '/api';
Vue.router = router;

Vue.http.interceptors.push(function (request, next) {
    next(function (res) {
        if (res.status === 401) {
            ElementUI.Notification.error({
                title: 'Ошибка',
                message: 'Не удалось авторизоваться'
            });
        }
        if (res.status === 500) {
            ElementUI.Notification.error({
                title: 'Ошибка',
                message: 'Ошибка сервера, пожалуйста, сообщите о ней администрации'
            });
        }
        if (res.body && res.body.status === 'error') { // обработка наших собственных ошибок (валидация, например)
            ElementUI.Notification.error({
                title: 'Ошибка',
                message: res.body.message
            });
        }
    });
});

Vue.use(auth, {
    auth: authBearer,
    http: httpVueResource,
    router: routerVueRouter,
    authRedirect: {path: '/login'},
    loginData: {url: 'private/login', method: 'POST', redirect: '/admin', fetchUser: false},
    fetchData: {enabled: false},
    refreshData: {enabled: false},
});

new Vue({
    router,
    store,
    render: h => h(AppLayout)
}).$mount('#app');
