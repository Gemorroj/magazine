import Vue from 'vue';
import Vuex from 'vuex';

import actions from './store/actions';
import mutations from './store/mutations';
import getters from './store/getters';

Vue.use(Vuex);

export default new Vuex.Store({
    strict: process.env.NODE_ENV !== 'production',
    modules: {
        'public': {
            namespaced: true,
            state: {
                categories: [],
                activeCategory: null,
                products: [],
                activeProduct: null
            },
            actions,
            mutations,
            getters
        },
        'private': {
            namespaced: true,
            state: {
                categories: [],
                products: [],
            },
            actions,
            mutations,
            getters
        }
    }
});
