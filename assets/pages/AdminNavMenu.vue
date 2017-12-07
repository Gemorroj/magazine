<template>
    <main>
        <el-menu :default-active="activeItem" mode="horizontal" @select="clickMenu">
            <el-menu-item index="main">Категории/Товары</el-menu-item>
            <el-menu-item index="contact">Контактная информация</el-menu-item>
            <el-menu-item index="misc">Дополнительно</el-menu-item>
        </el-menu>
    </main>
</template>


<script>
    export default {
        props: ['activeItem'],
        methods: {
            clickMenu (key) {
                this.$router.push({ name: 'admin-' + key });
            }
        }
    };
</script>
