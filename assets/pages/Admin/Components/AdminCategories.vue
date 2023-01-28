<template>
    <section style="display: inline">
        <el-select placeholder="Категория" value-key="id" v-model="selectedCategory" @change="selectCategory">
            <el-option v-for="item in categories" :key="item.id" :label="item.name" :value="item"></el-option>
        </el-select>

        <el-button-group>
            <el-button size="small" @click="categoryEditForm = Object.assign({}, selectedCategory); categoryEditFormVisible = true" icon="el-icon-edit">Редактировать</el-button>
            <el-button size="small" type="danger" @click="categoryDelete(selectedCategory)" icon="el-icon-delete">Удалить</el-button>
        </el-button-group>
        <el-button size="small" @click="categoryAddForm = {id: null, name: ''}; categoryAddFormVisible = true">Добавить категорию</el-button>


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
    </section>
</template>


<script>
import {mapGetters} from 'vuex';

export default {
        data() {
            return {
                categoryAddFormVisible: false,
                categoryEditFormVisible: false,

                selectedCategory: {
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

                categoryRules: {
                    name: [
                        { required: true, message: 'Название категории обязательно', trigger: 'blur' },
                        { min: 3, max: 255, message: 'Название категории должно быть от 3 до 255 символов', trigger: 'blur' }
                    ]
                }
            };
        },
        computed: mapGetters({
            categories: 'private/categories',
            activeCategory: 'private/activeCategory'
        }),
        mounted() {
            this.$store.dispatch('private/FETCH_CATEGORIES').then(() => {
                this.selectedCategory = this.categories[0]; // по умолчанию берем первую категорию
                this.selectCategory();
            });
        },
        methods: {
            selectCategory() {
                this.$store.dispatch('private/SET_ACTIVE_CATEGORY', this.selectedCategory);
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
                            this.selectedCategory = this.categories[0]; // по умолчанию берем первую категорию
                            this.selectCategory();
                        });
                    });
                }).catch(() => {
                    // ???
                });
            },
            submitCategoryEditForm() {
                this.$refs.categoryEditForm.validate((valid) => {
                    if (valid) {
                        this.$store.dispatch('private/UPDATE_CATEGORY', {category: this.categoryEditForm}).then(categoryResp => {
                            this.$notify({
                                title: 'Success',
                                message: `Категория ${this.selectedCategory.name} переименована в ${categoryResp.name}`,
                                type: 'success'
                            });

                            this.categoryEditFormVisible = false;
                            this.selectedCategory = categoryResp;
                            this.selectCategory();
                        });
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
                            this.selectedCategory = categoryResp;
                            this.selectCategory();
                        });
                    }
                });
            }
        }
    };
</script>
