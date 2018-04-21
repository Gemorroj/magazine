<template>
    <main>
        <admin-nav-menu activeItem="main" />

        <el-select placeholder="Категория" value-key="id" v-model="category" @change="selectCategory">
            <el-option v-for="item in categories" :key="item.id" :label="item.name" :value="item"></el-option>
        </el-select>

        <el-button-group>
            <el-button size="small" @click="categoryEditForm = Object.assign({}, category); categoryEditFormVisible = true">Изменить</el-button>
            <el-button size="small" @click="categoryAddForm = {id: null, name: ''}; categoryAddFormVisible = true">Добавить</el-button>
            <el-button size="small" type="danger" @click="categoryDelete(category)">Удалить</el-button>
        </el-button-group>
        <el-button-group>
            <el-button size="small" @click="productFormVisible = true">Добавить товар</el-button>
        </el-button-group>

        <el-table :data="products">
            <el-table-column prop="name" label="Название"></el-table-column>
            <el-table-column label="Действия">
                <template slot-scope="scope">
                    <el-button size="small" @click="productEdit(scope.row)">Редактировать</el-button>
                    <el-button size="small" type="danger" @click="productDelete(scope.row)">Удалить</el-button>
                </template>
            </el-table-column>
        </el-table>


        <el-dialog :title="product.id ? 'Редактирование товара' : 'Добавление товара'" :visible.sync="productFormVisible" @close="$refs.productForm.resetFields()">
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
                    <el-button type="primary" @click="submitProductForm">Готово</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>

        <el-dialog title="Редактирование категории" :visible.sync="categoryEditFormVisible" @close="categoryEditForm = {id: null, name: ''}">
            <el-form :model="categoryEditForm" label-position="right" label-width="15%" :rules="categoryRules" ref="categoryEditForm">
                <el-form-item label="Название" prop="name">
                    <el-input v-model="categoryEditForm.name"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="submitCategoryEditForm">Готово</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
        <el-dialog title="Добавление категории" :visible.sync="categoryAddFormVisible" @close="categoryAddForm = {id: null, name: ''}">
            <el-form :model="categoryAddForm" label-position="right" label-width="15%" :rules="categoryRules" ref="categoryAddForm">
                <el-form-item label="Название" prop="name">
                    <el-input v-model="categoryAddForm.name"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="primary" @click="submitCategoryAddForm">Готово</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </main>
</template>

<script>
    import AdminNavMenu from '../Components/AdminNavMenu.vue';
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
                productFormVisible: false,
                categoryAddFormVisible: false,
                categoryEditFormVisible: false,

                product: {
                    id: null,
                    name: '',
                    description: '',
                    price: ''
                },
                category: {
                    id: null,
                    name: ''
                },
                categoryEditForm: {
                    id: null,
                    name: ''
                },
                categoryAddForm: {
                    id: null,
                    name: ''
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
                    ]
                },
                categoryRules: {
                    name: [
                        { required: true, message: 'Навазние категории обязательно', trigger: 'blur' },
                        { min: 3, max: 255, message: 'Навазние категории должно быть от 3 до 255 символов', trigger: 'blur' }
                    ]
                }
            };
        },
        computed: mapGetters({
            categories: 'private/categories',
            products: 'private/products',
        }),
        mounted() {
            this.$store.dispatch('private/FETCH_CATEGORIES').then(() => {
                this.selectCategory(Object.assign({}, this.categories[0])); // по умолчанию берем первую категорию
            });
        },
        components: {
            AdminNavMenu
        },
        methods: {
            selectCategory(category) {
                this.category = category;
                this.$store.dispatch('private/FETCH_PRODUCTS', this.category.id);
            },
            categoryDelete(category) {
                this.$confirm('Вы действительно хотите удалить категорию со всеми товарами в ней?', 'Удаление категории', {
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    type: 'warning'
                }).then(() => {
                    this.$store.dispatch('private/DELETE_CATEGORY', {category: category}).then(() => {
                        this.$notify({
                            title: 'Success',
                            message: `Категория ${category.name} удалена`,
                            type: 'success'
                        });

                        this.$store.dispatch('private/FETCH_CATEGORIES').then(() => {
                            this.selectCategory(Object.assign({}, this.categories[0])); // по умолчанию берем первую категорию
                        });
                    });
                }).catch(() => {
                    // ???
                });
            },
            productEdit(product) {
                this.product.id = product.id;
                this.product.name = product.name;
                this.product.description = product.description;
                this.product.price = product.price;
                this.productFormVisible = true;
            },
            productDelete(product) {
                this.$confirm('Вы действительно хотите удалить товар?', 'Удаление товара', {
                    confirmButtonText: 'OK',
                    cancelButtonText: 'Cancel',
                    type: 'warning'
                }).then(() => {
                    this.$store.dispatch('private/DELETE_PRODUCT', {product: product}).then(() => {
                        this.$notify({
                            title: 'Success',
                            message: `Товар ${product.name} удален`,
                            type: 'success'
                        });
                    });
                }).catch(() => {
                    // ???
                });
            },
            submitProductForm(val) {
                console.log(val);

                this.$refs.productForm.validate((valid) => {
                    if (valid) {
                        alert('submit!');
                        this.productFormVisible = false;
                    } else {
                        alert('error validate!');
                    }
                });
            },
            submitCategoryEditForm() {
                this.$refs.categoryEditForm.validate((valid) => {
                    if (valid) {
                        this.$store.dispatch('private/UPDATE_CATEGORY', {category: this.categoryEditForm}).then(categoryResp => {
                            this.$notify({
                                title: 'Success',
                                message: `Категория ${this.category.name} переименована в ${categoryResp.name}`,
                                type: 'success'
                            });

                            this.categoryEditFormVisible = false;
                            this.category = categoryResp;
                            this.selectCategory(this.category);
                        });
                    } else {
                        alert('error validate!');
                    }
                });
            },
            submitCategoryAddForm() {
                this.$refs.categoryAddForm.validate((valid) => {
                    if (valid) {
                        this.$store.dispatch('private/ADD_CATEGORY', {categoryName: this.categoryAddForm.name}).then(categoryResp => {
                            this.$notify({
                                title: 'Success',
                                message: `Категория ${categoryResp.name} создана`,
                                type: 'success'
                            });

                            this.categoryAddFormVisible = false;
                            this.category = categoryResp;
                            this.selectCategory(this.category);
                        });
                    } else {
                        alert('error validate!');
                    }
                });
            }
        }
    }
</script>
