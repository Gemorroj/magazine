import Vue from 'vue';
import Vuex from 'vuex';

import categories from './store/categories';
import product from './store/product';
import products from './store/products';

Vue.use(Vuex);

const store = new Vuex.Store({
    strict: true,
    modules: {
        categories,
        product,
        products
    }
});

export default store;
