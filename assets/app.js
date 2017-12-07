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

Vue.use(require('@websanova/vue-auth'), {
    auth: require('@websanova/vue-auth/drivers/auth/basic.js'),
    http: require('@websanova/vue-auth/drivers/http/vue-resource.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
    rolesVar: 'role',
    loginData: {url: 'public/login', method: 'POST', redirect: '/admin', fetchUser: true},
    fetchData: {url: 'private/user', method: 'GET'},
    refreshData: {url: 'private/user/refresh', method: 'GET', enabled: false},
});

new Vue({
    router,
    store,
    render(h) {
        return h(AppLayout);
    }
}).$mount('#app');
