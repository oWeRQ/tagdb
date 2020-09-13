<template>
    <v-app>
        <v-navigation-drawer v-model="drawer" app clipped class="elevation-2">
            <v-list nav dense>
                <v-list-item-group color="primary">
                    <v-list-item link to="/">
                        <v-list-item-action>
                            <v-icon>mdi-home</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Entities</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link to="/tags">
                        <v-list-item-action>
                            <v-icon>mdi-tag</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Tags</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link to="/presets" exact>
                        <v-list-item-action>
                            <v-icon>mdi-database-settings</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Presets</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link to="/fields">
                        <v-list-item-action>
                            <v-icon>mdi-pencil-box-multiple</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Fields</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item link to="/values">
                        <v-list-item-action>
                            <v-icon>mdi-pencil</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Values</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-item-group>
                <v-list-item-group color="primary" v-if="presets.length">
                    <v-subheader>Presets</v-subheader>
                    <v-list-item v-for="preset in presets" :key="preset.id" link :to="{ name: 'preset', params: { name: preset.name }}">
                        <v-list-item-action>
                            <v-icon>mdi-database</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ preset.name }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-item-group>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app clipped-left color="indigo" dark>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>TagDB</v-toolbar-title>
        </v-app-bar>

        <v-main>
            <router-view></router-view>
        </v-main>
    </v-app>
</template>

<script>
    import axios from 'axios';

    export default {
        data: () => ({
            drawer: null,
        }),
        computed: {
            presets() {
                return this.$root.presets;
            },
        },
    }
</script>

<style>
    html {
        overflow-y: auto;
    }

    .v-data-table__wrapper {
        flex: 1 1 0;
    }
</style>
