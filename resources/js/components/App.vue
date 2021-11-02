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

                    <v-list-group
                        prepend-icon="mdi-cog"
                        no-action
                    >
                        <template v-slot:activator>
                            <v-list-item-title>Admin</v-list-item-title>
                        </template>

                        <v-list-item link to="/projects">
                            <v-list-item-title>Projects</v-list-item-title>
                        </v-list-item>
                        <v-list-item link to="/tags">
                            <v-list-item-title>Tags</v-list-item-title>
                        </v-list-item>
                        <v-list-item link to="/presets" exact>
                            <v-list-item-title>Presets</v-list-item-title>
                        </v-list-item>
                        <v-list-item link to="/fields">
                            <v-list-item-title>Fields</v-list-item-title>
                        </v-list-item>
                        <v-list-item link to="/values">
                            <v-list-item-title>Values</v-list-item-title>
                        </v-list-item>
                    </v-list-group>
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

            <CrudDialog
                ref="projectDialog"
                title="Project"
                :resource="projectResource"
                :editable="projectEditable"
                :deletable="projectDeletable"
                :value="projectEdited"
                @input="saveProject"
                @delete="deleteProject"
            ></CrudDialog>

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
                        <v-icon class="ml-1" size="20">mdi-chevron-down</v-icon>
                    </v-btn>
                </template>

                <v-list dense>
                    <v-list-item
                        v-for="project in projects"
                        :key="project.id"
                        :class="{'v-list-item--active primary--text': project.id === currentProject.id}"
                        @click="setCurrentProject(project)"
                    >
                        <v-list-item-icon class="mr-4">
                            <v-icon v-if="project.id === currentProject.id">mdi-check</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>{{ project.name }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item @click="updateProject">
                        <v-list-item-icon class="mr-4">
                            <v-icon>mdi-pencil</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Edit Project</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item @click="createProject">
                        <v-list-item-icon class="mr-4">
                            <v-icon>mdi-plus</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>New Project</v-list-item-title>
                        </v-list-item-content>
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
    import cloneDeep from 'clone-deep';
    import CrudDialog from './CrudDialog';

    export default {
        components: {
            CrudDialog,
        },
        data: () => ({
            drawer: null,
            projectResource: '/api/v1/projects',
            projectEditable: [
                { text: 'Name', value: 'name' },
            ],
            projectEdited: {},
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
            projectDeletable() {
                return this.projects.length > 1;
            },
        },
        methods: {
            setCurrentProject(project) {
                this.$root.setCurrentProject(project);
            },
            updateProject() {
                this.projectEdited = cloneDeep(this.currentProject);
                this.$refs.projectDialog.show();
            },
            createProject() {
                this.projectEdited = {name: ''};
                this.$refs.projectDialog.show();
            },
            saveProject(project) {
                this.setCurrentProject(project);
                this.$root.getProjects();
            },
            deleteProject({id}) {
                this.setCurrentProject(this.projects.find(project => project.id != id));
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
