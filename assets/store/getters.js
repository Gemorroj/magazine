import Vue from 'vue';

export default {
    categories: state => state.categories,
    activeCategory: state => state.activeCategory,
    products: state => state.products,
    activeProduct: state => state.activeProduct
};
