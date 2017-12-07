<template>
    <main>
        <p>Login page</p>
        <form v-on:submit.prevent="userLogin()">
            <input placeholder="Логин" v-model="login" />
            <input placeholder="Пароль" type="password" v-model="password" />
            <input type="submit" value="Войти" />
        </form>
    </main>
</template>

<script>
    import Vue from 'vue';

    export default {
        data() {
            return {
                login: '',
                password: ''
            };
        },
        methods: {
            userLogin() {
                this.$auth.login({
                    body: {
                        login: this.login,
                        password: this.password
                    },
                    success: function () {},
                    error: function () {},
                    rememberMe: true,
                    fetchUser: true
                });
            }
        }
    };
</script>
