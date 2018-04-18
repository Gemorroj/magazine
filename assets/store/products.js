import Vue from 'vue';

export default {
    //namespaced: true,
    state: {
        products: [],
        activeProduct: null
    },
    actions: {
        FETCH_PRODUCTS({commit}, category) {
            return Vue.resource('public/categories/{id}/products').get({id: category.id}).then(response => {
                commit('SET_PRODUCTS', response.body);
            });
        },
        DELETE_PRODUCT({commit}, {product, fn}) {
            let formData = new FormData();
            formData.append('productId', product.id);

            return Vue.resource('private/products/delete').save(formData).then(() => {
                commit('DELETE_PRODUCT', product);
                fn();
            });
        },
        SET_ACTIVE_PRODUCT({commit}, product) {
            commit('SET_ACTIVE_PRODUCT', product);
        }
    },
    mutations: {
        SET_PRODUCTS(state, products) {
            state.products = products;
        },
        DELETE_PRODUCT(state, product) {
            state.products = state.products.filter(item => {
                return item.id !== product.id;
            });
        },
        SET_ACTIVE_PRODUCT(state, activeProduct) {
            state.activeProduct = activeProduct;
        }
    },
    getters: {
        products: state => state.products,
        activeProduct: state => state.activeProduct
    }
};
