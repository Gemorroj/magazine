<template>
    <main>
        <AdminNavMenu activeItem="main" />

        <AdminCategories />

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
    </main>
</template>

<script>
    import AdminNavMenu from './Components/AdminNavMenu.vue';
    import AdminCategories from './Components/AdminCategories.vue';
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
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
                    ]
                }
            };
        },
        computed: mapGetters({
            categories: 'private/categories',
            activeCategory: 'private/activeCategory',
            products: 'private/products',
        }),
        watch: {
            activeCategory(newActiveCategory, oldActiveCategory) {
                this.$store.dispatch('private/FETCH_PRODUCTS', newActiveCategory.id);
            }
        },
        components: {
            AdminNavMenu,
            AdminCategories
        },
        methods: {
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
                    }
                });
            },
        }
    };
</script>
