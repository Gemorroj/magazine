import Vue from 'vue';

export default {
    //namespaced: true,
    state: {
        categories: [],
        activeCategory: null
    },
    actions: {
        FETCH_CATEGORIES({commit}) {
            return Vue.resource('public/categories').get().then(response => {
                commit('SET_CATEGORIES', response.body);
            });
        },
        SET_ACTIVE_CATEGORY({commit}, category) {
            commit('SET_ACTIVE_CATEGORY', category);
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
        }
    },
    mutations: {
        ADD_CATEGORY(state, category) {
            state.categories.push(category);
        },
        DELETE_CATEGORY(state, category) {
            state.categories = state.categories.filter(item => {
                return item.id !== category.id;
            });
        },
        UPDATE_CATEGORY(state, category) {
            state.categories = state.categories.map(item => {
                if (item.id === category.id) {
                    return category;
                }
                return item;
            });
        },
        SET_CATEGORIES(state, categories) {
            state.categories = categories;
        },
        SET_ACTIVE_CATEGORY(state, activeCategory) {
            state.activeCategory = activeCategory;
        }
    },
    getters: {
        categories: state => state.categories,
        activeCategory: state => state.activeCategory
    }
};
