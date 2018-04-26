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
    ADD_CATEGORY({commit}, {categoryName}) {
        let formData = new FormData();
        formData.append('categoryName', categoryName);

        return Vue.resource('private/categories/add').save(formData).then(response => {
            commit('ADD_CATEGORY', response.body);
            return response.body;
        });
    },
    DELETE_CATEGORY({commit}, {category}) {
        let formData = new FormData();
        formData.append('categoryId', category.id);

        return Vue.resource('private/categories/delete').save(formData).then(() => {
            commit('DELETE_CATEGORY', category);
        });
    },
    UPDATE_CATEGORY({commit}, {category}) {
        let formData = new FormData();
        formData.append('categoryId', category.id);
        formData.append('categoryName', category.name);

        return Vue.resource('private/categories/update').save(formData).then(response => {
            commit('UPDATE_CATEGORY', response.body);
            return response.body;
        });
    },
    DELETE_PRODUCT({commit}, {product}) {
        let formData = new FormData();
        formData.append('productId', product.id);

        return Vue.resource('private/products/delete').save(formData).then(() => {
            commit('DELETE_PRODUCT', product);
        });
    },
    UPDATE_PRODUCT({commit}, {product}) {
        let formData = new FormData();
        formData.append('id', product.id);
        formData.append('name', product.name);
        formData.append('description', product.description);
        formData.append('price', product.price);
        formData.append('size', product.size);
        formData.append('composition', product.composition);
        formData.append('manufacturer', product.manufacturer);

        for (let i = 0; i < product.photos.length; ++i) {
            formData.append('photos[]', product.photos[i].path);
        }

        return Vue.resource('private/products/update').save(formData).then(response => {
            commit('UPDATE_PRODUCT', response.body);
            return response.body;
        });
    },
    ADD_PRODUCT({commit}, {product, category}) {
        let formData = new FormData();
        formData.append('categoryId', category.id);
        formData.append('name', product.name);
        formData.append('description', product.description);
        formData.append('price', product.price);
        formData.append('size', product.size);
        formData.append('composition', product.composition);
        formData.append('manufacturer', product.manufacturer);

        for (let i = 0; i < product.photos.length; ++i) {
            formData.append('photos[]', product.photos[i].path);
        }

        return Vue.resource('private/products/add').save(formData).then(response => {
            commit('ADD_PRODUCT', response.body);
            return response.body;
        });
    }
};
