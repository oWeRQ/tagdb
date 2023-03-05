<template>
    <v-app>
        <v-app-bar app clipped-left color="indigo" dark>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title class="flex-grow-0" :style="{'flex-basis': 'auto'}">TagDB</v-toolbar-title>
            <HeaderProjectMenu></HeaderProjectMenu>
            <v-spacer></v-spacer>
            <HeaderAccountMenu></HeaderAccountMenu>
        </v-app-bar>

        <v-navigation-drawer v-if="isReady" v-model="drawer" app clipped class="elevation-2">
            <DrawerNavList></DrawerNavList>
        </v-navigation-drawer>

        <v-main>
            <slot></slot>
            <router-view v-if="isReady"></router-view>
        </v-main>
    </v-app>
</template>

<script>
    import { mapState } from 'vuex';
    import DrawerNavList from './common/DrawerNavList.vue';
    import HeaderAccountMenu from './common/HeaderAccountMenu.vue';
    import HeaderProjectMenu from './common/HeaderProjectMenu.vue';

    export default {
        components: {
            DrawerNavList,
            HeaderAccountMenu,
            HeaderProjectMenu,
        },
        data: () => ({
            drawer: null,
        }),
        computed: {
            ...mapState({
                isReady: state => state.auth.isReady,
            }),
        },
    }
</script>

<style>
    html {
        overflow-y: auto;
    }

    .v-main {
        overflow: auto;
        flex: 1 1 0 !important;
    }

    .v-table__wrapper {
        flex: 1 1 0;
    }
</style>
