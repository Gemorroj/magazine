<template>
    <main>
        <h1>Авторизация</h1>

        <el-form :inline="true" :model="form" :rules="loginRules" ref="loginForm" style="margin: 6px 0 0">
            <el-form-item prop="login">
                <el-input placeholder="Логинн" v-model="form.login" style="width: 250px"></el-input>
            </el-form-item>
            <el-form-item prop="password">
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
                },
                loginRules: {
                    login: [
                        { required: true, message: 'Заполните логин', trigger: 'blur' },
                        { min: 3, max: 255, message: 'Логин должен быть от 3 до 255 символов', trigger: 'blur' }
                    ],
                    password: [
                        { required: true, message: 'Заполните пароль', trigger: 'blur' },
                        { min: 3, max: 255, message: 'Пароль должен быть от 3 до 255 символов', trigger: 'blur' }
                    ]
                }
            };
        },
        methods: {
            userLogin() {
                this.$refs.loginForm.validate((valid) => {
                    if (valid) {
                        let data = new FormData(); // multipart/form-data
                        data.append("login", this.form.login);
                        data.append("password", this.form.password);

                        this.$auth.login({
                            body: data,
                            rememberMe: true
                        });
                    }
                });
            }
        }
    };
</script>
