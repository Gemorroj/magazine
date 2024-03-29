<template>
    <section style="display: inline">
        <el-button size="small" @click="productFormVisible = true">Добавить товар</el-button>

        <el-table :data="products" border show-summary>
            <el-table-column prop="id" label="ID" width="100" sortable></el-table-column>
            <el-table-column label="Название" sortable>
                <template v-slot="scope">
                    <el-popover trigger="hover" placement="top">
                        <p>Дата создания: {{ (new Date(scope.row.dateCreate)).toLocaleString() }}</p>
                        <p>Дата обновления: {{ scope.row.dateUpdate ? (new Date(scope.row.dateUpdate)).toLocaleString() : '' }}</p>
                        <div slot="reference">
                            <el-tag size="medium">{{ scope.row.name }}</el-tag>
                        </div>
                    </el-popover>
                </template>
            </el-table-column>
            <el-table-column prop="price" label="Цена" width="150" sortable></el-table-column>
            <el-table-column label="Действия" fixed="right" width="250">
                <template v-slot="scope">
                    <el-button-group>
                        <el-button size="mini" @click="productEdit(scope.row)" icon="el-icon-edit">Редактировать</el-button>
                        <el-button size="mini" type="danger" @click="productDelete(scope.row)" icon="el-icon-delete">Удалить</el-button>
                    </el-button-group>
                </template>
            </el-table-column>
        </el-table>


        <el-dialog :title="product.id ? 'Редактирование товара' : 'Добавление товара'" :visible.sync="productFormVisible" @close="$refs.productForm.resetFields(); product = {}; fileList = []">
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
                <el-form-item label="Размер" prop="size">
                    <el-input v-model="product.size"></el-input>
                </el-form-item>
                <el-form-item label="Состав" prop="composition">
                    <el-input v-model="product.composition"></el-input>
                </el-form-item>
                <el-form-item label="Производитель" prop="manufacturer">
                    <el-input v-model="product.manufacturer"></el-input>
                </el-form-item>

                <el-form-item label="Фотографии" required>
                    <el-upload
                            multiple
                            :limit="10"
                            accept="image/*"
                            action="/api/private/photo"
                            :on-preview="handlePreview"
                            :on-remove="handleRemove"
                            :on-success="handleSuccess"
                            :headers="{'Authorization': 'Bearer ' + this.$auth.token()}"
                            :file-list="fileList"
                            list-type="picture">
                        <el-tooltip effect="dark" content="Фотография должна быть не более 500кб" placement="right-start">
                            <el-button size="small" type="primary">Добавить</el-button>
                        </el-tooltip>
                    </el-upload>
                </el-form-item>


                <el-form-item>
                    <el-button type="primary" @click="submitProductForm">Готово</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>

        <el-dialog :visible.sync="photoPreviewVisible">
            <img width="100%" :src="photoPreviewImageUrl" />
        </el-dialog>
    </section>
</template>


<script>
import {mapGetters} from 'vuex';

export default {
        data() {
            return {
                fileList: [],
                productFormVisible: false,
                photoPreviewVisible: false,
                photoPreviewImageUrl: '',

                product: {
                    id: null,
                    name: '',
                    description: '',
                    price: '',
                    size: '',
                    composition: '',
                    manufacturer: '',
                    photos: []
                },

                productRules: {
                    name: [
                        { required: true, message: 'Название товара обязательно', trigger: 'blur' },
                        { min: 3, max: 255, message: 'Название товара должно быть от 3 до 255 символов', trigger: 'blur' }
                    ],
                    description: [
                        { required: true, message: 'Описание товара обязательно', trigger: 'blur' },
                        { min: 3, max: 5000, message: 'Название товара должно быть от 3 до 5000 символов', trigger: 'blur' }
                    ],
                    price: [
                        { type: 'number', required: true, message: 'Цена обязательна'}
                    ],
                    size: [
                        {max: 255, message: 'Размер должен быть до 255 символов', trigger: 'blur'}
                    ],
                    composition: [
                        {max: 255, message: 'Состав должен быть до 255 символов', trigger: 'blur'}
                    ],
                    manufacturer: [
                        {max: 255, message: 'Производитель должен быть до 255 символов', trigger: 'blur'}
                    ],
                }
            };
        },
        computed: mapGetters({
            products: 'private/products',
            activeCategory: 'private/activeCategory'
        }),
        methods: {
            handleRemove(file, fileList) {
                //console.log('remove', file, fileList);
                this.product.photos = fileList.map(photo => ({'name': photo.name, 'path': photo.url}));
            },
            handlePreview(file) {
                //console.log('preview', file);
                this.photoPreviewImageUrl = file.url;
                this.photoPreviewVisible = true;
            },
            handleSuccess(res, file) {
                //console.log('success', res, file);
                this.product.photos = [...this.product.photos, res];
            },
            productEdit(product) {
                this.product = Object.assign({}, product);
                this.fileList = this.product.photos.map(photo => ({'name': photo.name, 'url': photo.path}));
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
            submitProductForm() {
                this.$refs.productForm.validate((valid) => {
                    if (valid) {
                        if (this.product.id) {
                            this.$store.dispatch('private/UPDATE_PRODUCT', {product: this.product}).then(() => {
                                this.$notify({
                                    title: 'Success',
                                    message: `Товар ${this.product.name} обновлен`,
                                    type: 'success'
                                });
                                this.productFormVisible = false;
                            });
                        } else {
                            this.$store.dispatch('private/ADD_PRODUCT', {product: this.product, category: this.activeCategory}).then(() => {
                                this.$notify({
                                    title: 'Success',
                                    message: `Товар ${this.product.name} добавлен`,
                                    type: 'success'
                                });
                                this.productFormVisible = false;
                            });
                        }
                    }
                });
            }
        }
    };
</script>
