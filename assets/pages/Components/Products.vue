<template>
    <el-row>
        <el-col :xs="24" :sm="8" :md="8" :lg="8" v-for="product in products" :key="product.id">
            <el-card>
                <img :src="'/api/public/photos/' + product.photos[0].id" />
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
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        computed: mapGetters({
            products: 'public/products'
        })
    };
</script>
