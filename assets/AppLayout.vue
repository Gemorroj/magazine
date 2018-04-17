<template>
    <div class="grid">
        <header class="grid-header">
            <h1><router-link :to="{name: 'home'}">Umberto Imperial</router-link></h1>

            <nav>
                <router-link :to="{name: 'contact'}">Контакты</router-link>
                <router-link :to="{name: 'about'}">О нас</router-link>
            </nav>
        </header>

        <router-view class="grid-main"></router-view>

        <footer class="grid-footer">
            Магазин одежды Umberto Imperial
        </footer>
    </div>
</template>
