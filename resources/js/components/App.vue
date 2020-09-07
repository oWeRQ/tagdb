<template>
    <v-app>
        <v-navigation-drawer v-model="drawer" app clipped>
            <v-list dense>
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
                <v-list-item link to="/presets">
                    <v-list-item-action>
                        <v-icon>mdi-database</v-icon>
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
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app clipped-left color="indigo" dark>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>TagDB</v-toolbar-title>
        </v-app-bar>

        <v-main>
            <v-container fluid>
                <router-view></router-view>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        data: () => ({
            drawer: null,
        }),
    }
</script>
