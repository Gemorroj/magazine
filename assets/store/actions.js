import Vue from 'vue';

export default {
    FETCH_CATEGORIES({commit}) {
        return Vue.resource('public/categories').get().then(response => {
            commit('SET_CATEGORIES', response.body);
        });
    },
    SET_ACTIVE_CATEGORY({commit}, category) {
        commit('SET_ACTIVE_CATEGORY', category);
    },
    FETCH_PRODUCTS({commit}, categoryId) {
        return Vue.resource('public/categories/{id}/products').get({id: categoryId}).then(response => {
            commit('SET_PRODUCTS', response.body);
        });
    },
    SET_ACTIVE_PRODUCT({commit}, product) {
        commit('SET_ACTIVE_PRODUCT', product);
    },
    FETCH_PRODUCT({commit}, id) {
        return Vue.resource('public/products/{id}').get({id: id}).then(response => {
            commit('SET_ACTIVE_PRODUCT', response.body);
        });
    },
    ADD_CATEGORY({commit}, {categoryName, fn}) {
        let formData = new FormData();
        formData.append('categoryName', categoryName);

        return Vue.resource('private/categories/add').save(formData).then(response => {
            commit('ADD_CATEGORY', response.body);
            fn(response.body);
        });
    },
    DELETE_CATEGORY({commit}, {category, fn}) {
        let formData = new FormData();
        formData.append('categoryId', category.id);

        return Vue.resource('private/categories/delete').save(formData).then(() => {
            commit('DELETE_CATEGORY', category);
            fn();
        });
    },
    UPDATE_CATEGORY({commit}, {category, fn}) {
        let formData = new FormData();
        formData.append('categoryId', category.id);
        formData.append('categoryName', category.name);

        return Vue.resource('private/categories/update').save(formData).then(response => {
            commit('UPDATE_CATEGORY', response.body);
            fn(response.body);
        });
    },
    DELETE_PRODUCT({commit}, {product, fn}) {
        let formData = new FormData();
        formData.append('productId', product.id);

        return Vue.resource('private/products/delete').save(formData).then(() => {
            commit('DELETE_PRODUCT', product);
            fn();
        });
    }
};
