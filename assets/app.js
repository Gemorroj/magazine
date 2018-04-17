import Vue from 'vue';

import VueResource from 'vue-resource';

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import locale from 'element-ui/lib/locale/lang/ru-RU';

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
            console.log(res);
            ElementUI.Notification.error({
                title: 'Ошибка',
                message: 'Ошибка сервера, пожалуйста, сообщите о ней администрации'
            });
        }
    });
});

Vue.use(require('@websanova/vue-auth'), {
    auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
    http: require('@websanova/vue-auth/drivers/http/vue-resource.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
    authRedirect: {path: '/login'},
    loginData: {url: 'private/login', method: 'POST', redirect: '/admin', fetchUser: false},
    fetchData: {url: 'private/user', enabled: false}, // не смотря на то, что отключено, эта хрень шлет запросы
    refreshData: {enabled: false},
});

new Vue({
    router,
    store,
    render(h) {
        return h(AppLayout);
    }
}).$mount('#app');
