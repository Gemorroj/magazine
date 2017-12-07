import Vue from 'vue';

const product = {
    //namespaced: true,
    state: {
        product: null
    },
    actions: {
        FETCH_PRODUCT({commit}, id) {
            return Vue.resource('public/products/{id}').get({id: id}).then(response => {
                commit('SET_PRODUCT', response.body);
            });
        }
    },
    mutations: {
        SET_PRODUCT(state, product) {
            state.product = product;
        }
    },
    getters: {
        product(state) {
            return state.product;
        }
    }
};

export default product;
