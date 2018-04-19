import Vue from 'vue';

export default {
    SET_CATEGORIES(state, categories) {
        state.categories = categories;
    },
    SET_ACTIVE_CATEGORY(state, activeCategory) {
        state.activeCategory = activeCategory;
    },
    SET_PRODUCTS(state, products) {
        state.products = products;
    },
    SET_ACTIVE_PRODUCT(state, activeProduct) {
        state.activeProduct = activeProduct;
    },
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
    DELETE_PRODUCT(state, product) {
        state.products = state.products.filter(item => {
            return item.id !== product.id;
        });
    }
};
