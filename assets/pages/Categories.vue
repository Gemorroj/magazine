<template>
    <div class="category-wrapper">
        <span v-for="category in categories" class="category">
            <el-button @click="clickCategory(category)" :type="category === activeCategory ? 'primary' : 'text'">{{ category.name }}</el-button>
        </span>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        props: {
            categoryId: {
                type: [String, Number],
                required: false
            },
            activateCategoryCallback: {
                type: Function,
                requited: false
            }
        },
        computed: mapGetters({
            categories: 'public/categories',
            activeCategory: 'public/activeCategory'
        }),
        mounted() {
            this.$store.dispatch('public/FETCH_CATEGORIES').then(() => {
                let category = this.categories[0]; // по умолчанию первая категория
                if (this.$props.categoryId) {
                    for (let i = 0; i < this.categories.length; ++i) {
                        if (this.categories[i].id == this.$props.categoryId) {
                            category = this.categories[i];
                            break;
                        }
                    }
                }

                this.$store.dispatch('public/SET_ACTIVE_CATEGORY', category);
                if (this.$props.activateCategoryCallback) {
                    this.$props.activateCategoryCallback(category);
                }
            });
        },
        methods: {
            clickCategory(category) {
                this.$store.dispatch('public/SET_ACTIVE_CATEGORY', category);
                this.$router.push({name: 'category', params: { categoryId: category.id }});
                if (this.$props.activateCategoryCallback) {
                    this.$props.activateCategoryCallback(category);
                }
            }
        }
    };
</script>
