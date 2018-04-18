<template>
    <main>
        <div class="category-wrapper">
            <span v-for="category in categories" class="category">
                <el-button @click="clickCategory(category)" type="text" :disabled="category === activeCategory">{{ category.name }}</el-button>
            </span>
        </div>

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
                        <router-link :to="{ name: 'product', params: { id: product.id }}">Подробнее</router-link>
                    </div>
                </el-card>
            </el-col>
        </el-row>
    </main>
</template>

<script>
    export default {
        computed: {
            categories() {
                return this.$store.getters.categories;
            },
            activeCategory() {
                return this.$store.getters.activeCategory;
            },
            products() {
                return this.$store.getters.products;
            }
        },
        mounted() {
            if (this.categories.length && this.products.length) {
                return;
            }

            this.$store.dispatch('FETCH_CATEGORIES').then(() => {
                this.clickCategory(this.categories[0]);
            });
        },
        methods: {
            clickCategory(category) {
                this.$store.dispatch('SET_ACTIVE_CATEGORY', category);
                this.$store.dispatch('FETCH_PRODUCTS', category);
            }
        }
    };
</script>
