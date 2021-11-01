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
                    <v-list-item link to="/projects">
                        <v-list-item-action>
                            <v-icon>mdi-folder-multiple</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Projects</v-list-item-title>
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
            <v-menu
                v-if="currentProject"
                bottom
                left
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        text
                        v-bind="attrs"
                        v-on="on"
                        class="ml-4 text-capitalize"
                    >
                        <v-icon class="mr-1">mdi-folder-outline</v-icon>
                        {{ currentProject.name }}
                        <v-icon size="14">mdi-chevron-down</v-icon>
                    </v-btn>
                </template>

                <v-list>
                    <v-list-item
                        v-for="project in projects"
                        :key="project.id"
                        :class="{'v-list-item--active': project.id === currentProject.id}"
                        @click="setCurrentProject(project)"
                    >
                        <v-list-item-title>{{ project.name }}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>

        <v-main>
            <slot></slot>
            <router-view v-if="currentProject"></router-view>
        </v-main>
    </v-app>
</template>

<script>
    export default {
        data: () => ({
            drawer: null,
        }),
        computed: {
            currentProject() {
                return this.$root.currentProject;
            },
            projects() {
                return this.$root.projects;
            },
            presets() {
                return this.$root.presets;
            },
        },
        methods: {
            setCurrentProject(project) {
                this.$root.setCurrentProject(project);
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
