<template>
    <main>
        <Categories :activateCategoryCallback="activateCategoryCallback" :categoryId="$route.params.categoryId"/>

        <el-row v-if="products">
            <el-col :xs="24" :sm="8" :md="8" :lg="8" v-for="product in products" :key="product.id">
                <el-card>
                    <img :src="'https://api.rethumb.com/v1/width/200/' + product.photos[0].path" />
                    <div>
                        <div>{{ product.name }}</div>
                        <div>{{ product.price }} рубля</div>
                        <div>{{ product.composition }}</div>
                    </div>
                    <div>
                        <router-link :to="{ name: 'product', params: {categoryId: product.category.id, productId: product.id }}">Подробнее</router-link>
                    </div>
                </el-card>
            </el-col>
        </el-row>
    </main>
</template>

<script>
    import { mapGetters } from 'vuex';
    import Categories from './Categories.vue';

    export default {
        components: {
            Categories
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
