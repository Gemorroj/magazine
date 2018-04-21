<template>
    <main>
        <Categories :activateCategoryCallback="activateCategoryCallback" :categoryId="$route.params.categoryId"/>
        <Products v-if="products" />
    </main>
</template>

<script>
    import { mapGetters } from 'vuex';
    import Categories from './Components/Categories.vue';
    import Products from './Components/Products.vue';

    export default {
        components: {
            Categories,
            Products,
        },
        computed: mapGetters({
            products: 'public/products'
        }),
        methods: {
            activateCategoryCallback(category) {
                this.$store.dispatch('public/FETCH_PRODUCTS', category.id);
            }
        }
    };
</script>
