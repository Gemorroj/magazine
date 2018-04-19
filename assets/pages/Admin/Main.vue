<template>
    <main>
        <admin-nav-menu active-item="main"></admin-nav-menu>

        <el-select placeholder="Категория" value-key="id" v-model="category" @change="selectCategory" filterable allowCreate>
            <el-option v-for="item in categories" :key="item.id" :label="item.name" :value="item"></el-option>
        </el-select>

        <span v-if="category">
            <el-button size="small" type="danger" @click="categoryDelete(category)">Удалить</el-button>
            <el-input style="width: auto" placeholder="Категория" v-model="categoryName" :value="category.name">
                <el-button slot="append" icon="el-icon-edit" @click="categoryEdit(category, categoryName)"></el-button>
            </el-input>
        </span>
        <span v-if="category">
            <el-button size="small" @click.prevent="productAdd()">Добавить товар</el-button>
        </span>

        <el-table :data="products" v-if="category">
            <el-table-column prop="name" label="Название"></el-table-column>
            <el-table-column label="Действия">
                <template slot-scope="scope">
                    <el-button  size="small" @click="productEdit(scope.row)">Редактировать</el-button>
                    <el-button size="small" type="danger" @click="productDelete(scope.row)">Удалить</el-button>
                </template>
            </el-table-column>
        </el-table>


        <el-dialog :title="product.id ? 'Редактирование товара' : 'Добавление товара'" :visible.sync="productFormVisible" @close="resetProductForm">
            <el-form :model="product" label-position="right" label-width="15%" :rules="productRules" ref="productForm">
                <el-form-item label="Название" prop="name">
                    <el-input v-model="product.name"></el-input>
                </el-form-item>
                <el-form-item label="Описание" prop="description">
                    <el-input type="textarea" autosize v-model="product.description"></el-input>
                </el-form-item>
                <el-form-item label="Цена" prop="price">
                    <el-input-number v-model="product.price" :step="0.01"></el-input-number>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="submitProductForm(product)">Готово</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </main>
</template>

<script>
    import AdminNavMenu from './AdminNavMenu.vue';
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
                category: null,
                categoryName: '',
                productFormVisible: false,

                product: {
                    id: null,
                    name: '',
                    description: '',
                    price: ''
                },

                productRules: {
                    name: [
                        { required: true, message: 'Навазние товара обязательно', trigger: 'blur' },
                        { min: 3, max: 255, message: 'Навазние товара должно быть от 3 до 255 символов', trigger: 'blur' }
                    ],
                    description: [
                        { required: true, message: 'Описание товара обязательно', trigger: 'blur' },
                        { min: 3, max: 5000, message: 'Навазние товара должно быть от 3 до 5000 символов', trigger: 'blur' }
                    ],
                    price: [
                        { type: 'number', required: true, message: 'Цена обязательна'}
                    ],
                }
            };
        },
        computed: mapGetters({
            categories: 'private/categories',
            activeCategory: 'private/activeCategory',
            products: 'private/products',
        }),
        mounted() {
            if (!this.categories.length) {
                this.$store.dispatch('private/FETCH_CATEGORIES');
            }
        },
        components: {
            AdminNavMenu
        },
        methods: {
            selectCategory() {
                this.categoryName = '';

                if (!this.category) {
                    this.$store.dispatch('private/SET_ACTIVE_CATEGORY', null);
                    return;
                }

                if (!this.category.id) {
                    return this.$store.dispatch('private/ADD_CATEGORY', {categoryName: this.category, fn: category => {
                        this.$notify({
                            title: 'Success',
                            message: '"' + category.name + '" создана',
                            type: 'success'
                        });

                        this.$store.dispatch('private/SET_ACTIVE_CATEGORY', category);
                        this.$store.dispatch('private/FETCH_PRODUCTS', category);
                        this.category = category;
                        this.categoryName = category.name;
                    }});
                }

                this.$store.dispatch('private/SET_ACTIVE_CATEGORY', this.category);
                this.$store.dispatch('private/FETCH_PRODUCTS', this.category);
                this.categoryName = this.category.name;
            },
            categoryEdit(category, categoryName) {
                this.$store.dispatch('private/UPDATE_CATEGORY', {category: {id: category.id, name: categoryName}, fn: category => {
                    this.$notify({
                        title: 'Success',
                        message: '"' + this.category.name + '" переименована в "' + category.name + '"',
                        type: 'success'
                    });

                    this.$store.dispatch('private/SET_ACTIVE_CATEGORY', category);
                    this.category = category;
                }});
            },
            categoryDelete(category) {
                this.$store.dispatch('private/DELETE_CATEGORY', {category: category, fn: () => {
                    this.$notify({
                        title: 'Success',
                        message: '"' + category.name + '" удалена',
                        type: 'success'
                    });

                    this.category = null;
                    this.categoryName = '';
                }});
            },
            productEdit(product) {
                console.log(product);
                this.product.id = product.id;
                this.product.name = product.name;
                this.product.description = product.description;
                this.product.price = product.price;
                this.productFormVisible = true;
            },
            productAdd() {
                this.productFormVisible = true;
            },
            productDelete(product) {
                this.$store.dispatch('private/DELETE_PRODUCT', {product: product, fn: () => {
                    this.$notify({
                        title: 'Success',
                        message: '"' + product.name + '" удален',
                        type: 'success'
                    });
                }});
            },
            submitProductForm(product) {
                console.log(product);

                this.$refs['productForm'].validate((valid) => {
                    if (valid) {
                        alert('submit!');
                        this.productFormVisible = false;
                    } else {
                        alert('error submit!!');
                    }
                });
            },
            resetProductForm() {
                this.$refs['productForm'].resetFields();
            }
        }
    }
</script>
