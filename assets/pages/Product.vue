<template>
    <main v-if="product">
        <h1>{{ product.name }}</h1>
        <p>{{ product.description }}</p>

        <fieldset v-if="product" class="product-info">
            <div>
                <span>Цена</span>
                <span>{{ product.price }}</span>
            </div>
            <div>
                <span>Размер</span>
                <span>{{ product.size }}</span>
            </div>
            <div>
                <span>Состав</span>
                <span>{{ product.composition }}</span>
            </div>
            <div>
                <span>Производитель</span>
                <span>{{ product.manufacturer }}</span>
            </div>
        </fieldset>

        <div>
            <div class="product-photos">
                <img v-for="(photo, index) in product.photos" :src="'https://api.rethumb.com/v1/width/200/' + photo.path" @click="openGallery(index)" />
            </div>
            <lightbox :images="prepareLightbox()" ref="lightbox" :show-light-box="false"></lightbox>
        </div>
    </main>
</template>

<script>
    import Vue from 'vue';
    import { mapGetters } from 'vuex';
    import VueLazyLoad from 'vue-lazyload';
    import VueTouch from 'vue-touch';
    import Lightbox from 'vue-image-lightbox';
    import 'vue-image-lightbox/dist/vue-image-lightbox.min.css';

    Vue.use(VueLazyLoad);
    Vue.use(VueTouch, { name: 'v-touch' });

    export default {
        components: {
            Lightbox
        },
        computed: mapGetters({
            product: 'product'
        }),
        methods: {
            openGallery(index) {
                this.$refs.lightbox.showImage(index);
            },
            prepareLightbox() {
                return this.product.photos.map(photo => {
                    return {
                        thumb: 'https://api.rethumb.com/v1/width/200/' + photo.path,
                        src: photo.path,
                    };
                });
            }
        },
        mounted() {
            this.$store.dispatch('FETCH_PRODUCT', this.$route.params.id);
        }
    };
</script>
