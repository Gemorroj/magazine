<template>
    <main>
        <Categories :categoryId="$route.params.categoryId" />

        <section v-if="activeProduct">
            <h1>{{ activeProduct.name }}</h1>
            <p>{{ activeProduct.description }}</p>

            <fieldset class="product-info">
                <div>
                    <span>Цена</span>
                    <span>{{ activeProduct.price }}</span>
                </div>
                <div>
                    <span>Размер</span>
                    <span>{{ activeProduct.size }}</span>
                </div>
                <div>
                    <span>Состав</span>
                    <span>{{ activeProduct.composition }}</span>
                </div>
                <div>
                    <span>Производитель</span>
                    <span>{{ activeProduct.manufacturer }}</span>
                </div>
            </fieldset>

            <div>
                <div class="product-photos">
                    <img v-for="(photo, index) in activeProduct.photos" :src="'/api/public/photos/' + photo.id" @click="openGallery(index)" />
                </div>
                <lightbox :images="prepareLightbox()" ref="lightbox" :show-light-box="false"></lightbox>
            </div>
        </section>
    </main>
</template>

<script>
    import Vue from 'vue';
    import { mapGetters } from 'vuex';
    import VueLazyLoad from 'vue-lazyload';
    import Lightbox from 'vue-image-lightbox';
    import 'vue-image-lightbox/dist/vue-image-lightbox.min.css';
    import Categories from './Components/Categories.vue';

    Vue.use(VueLazyLoad);

    export default {
        components: {
            Lightbox,
            Categories
        },
        computed: mapGetters({
            activeProduct: 'public/activeProduct'
        }),
        methods: {
            openGallery(index) {
                this.$refs.lightbox.showImage(index);
            },
            prepareLightbox() {
                return this.activeProduct.photos.map(photo => {
                    return {
                        thumb: '/api/public/photos/' + photo.id,
                        src: photo.path,
                    };
                });
            }
        },
        mounted() {
            this.$store.dispatch('public/FETCH_PRODUCT', this.$route.params.productId);
        }
    };
</script>
