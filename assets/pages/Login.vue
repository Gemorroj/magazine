<template>
    <main>
        <h1>Авторизация</h1>

        <el-form :inline="true" :model="form" style="margin: 6px 0 0">
            <el-form-item required="true">
                <el-input placeholder="Логинн" v-model="form.login" style="width: 250px"></el-input>
            </el-form-item>
            <el-form-item required="true">
                <el-input placeholder="Пароль" v-model="form.password" type="password" style="width: 250px"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="userLogin">Войти</el-button>
            </el-form-item>
        </el-form>
    </main>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    login: '',
                    password: ''
                }
            };
        },
        methods: {
            userLogin() {
                this.$auth.login({
                    body: {
                        login: this.form.login,
                        password: this.form.password
                    },
                    rememberMe: true
                });
            }
        }
    };
</script>
