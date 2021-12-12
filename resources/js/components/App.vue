<template>
    <v-app>
        <v-navigation-drawer v-if="isReady" v-model="drawer" app clipped class="elevation-2">
            <DrawerNavList></DrawerNavList>
        </v-navigation-drawer>

        <v-app-bar app clipped-left color="indigo" dark>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>TagDB</v-toolbar-title>
            <HeaderProjectMenu></HeaderProjectMenu>
            <v-spacer></v-spacer>
            <HeaderAccountMenu></HeaderAccountMenu>
        </v-app-bar>

        <v-main>
            <slot></slot>
            <router-view v-if="isReady"></router-view>
        </v-main>
    </v-app>
</template>

<script>
    import { mapState } from 'vuex';
    import DrawerNavList from './common/DrawerNavList';
    import HeaderAccountMenu from './common/HeaderAccountMenu';
    import HeaderProjectMenu from './common/HeaderProjectMenu';

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
            ...mapState([
                'isReady',
            ]),
        },
    }
</script>

<style>
    html {
        overflow-y: auto;
    }

    html,
    body {
        height: 100%;
    }

    .v-application,
    .v-application--wrap {
        min-height: -webkit-fill-available;
    }

    .v-data-table__wrapper {
        flex: 1 1 0;
    }

    .v-dialog__content {
        align-items: flex-start;
    }
</style>
