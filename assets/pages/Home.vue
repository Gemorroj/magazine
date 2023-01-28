<template>
    <main>
        <Categories :categoryId="$route.params.categoryId"/>
        <Products v-if="products" />
    </main>
</template>

<script>
import {mapGetters} from 'vuex';
import Categories from './Components/Categories.vue';
import Products from './Components/Products.vue';

export default {
        components: {
            Categories,
            Products,
        },
        computed: mapGetters({
            products: 'public/products',
            activeCategory: 'public/activeCategory'
        }),
        watch: {
            activeCategory(newActiveCategory, oldActiveCategory) {
                this.$store.dispatch('public/FETCH_PRODUCTS', newActiveCategory.id);
            }
        }
    };
</script>
