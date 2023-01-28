<template>
    <main>
        <AdminNavMenu activeItem="main" />

        <AdminCategories />

        <AdminProducts />
    </main>
</template>

<script>
import AdminNavMenu from './Components/AdminNavMenu.vue';
import AdminCategories from './Components/AdminCategories.vue';
import AdminProducts from './Components/AdminProducts.vue';
import {mapGetters} from 'vuex';

export default {
        computed: mapGetters({
            activeCategory: 'private/activeCategory'
        }),
        watch: {
            activeCategory(newActiveCategory, oldActiveCategory) {
                this.$store.dispatch('private/FETCH_PRODUCTS', newActiveCategory.id);
            }
        },
        components: {
            AdminNavMenu,
            AdminCategories,
            AdminProducts
        }
    };
</script>
