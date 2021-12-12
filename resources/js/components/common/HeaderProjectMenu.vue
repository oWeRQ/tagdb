<template>
    <v-menu
        v-if="account"
        offset-y
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                text
                v-bind="attrs"
                v-on="on"
                class="ml-4 text-capitalize"
            >
                <v-icon class="mr-1">mdi-folder-outline</v-icon>
                {{ currentProject && currentProject.name }}
                <v-icon class="ml-1" size="20">mdi-chevron-down</v-icon>
            </v-btn>

            <ProjectDialog
                ref="projectDialog"
                :deletable="projectDeletable"
                :value="projectEdited"
                @input="saveProject"
                @delete="deleteProject"
            ></ProjectDialog>
        </template>

        <v-list dense>
            <v-list-item
                v-for="project in projects"
                :key="project.id"
                :class="{'v-list-item--active primary--text': currentProject && currentProject.id === project.id}"
                @click="switchProject(project)"
            >
                <v-list-item-icon class="mr-4">
                    <v-icon v-if="currentProject && currentProject.id === project.id">mdi-check</v-icon>
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
</template>

<script>
import { mapActions, mapState } from 'vuex';
import cloneDeep from 'clone-deep';
import ProjectDialog from '../project/ProjectDialog';

export default {
    components: {
        ProjectDialog,
    },
    data: () => ({
        projectEdited: {},
    }),
    computed: {
        ...mapState([
            'isReady',
            'account',
            'currentProject',
            'projects',
        ]),
        projectDeletable() {
            return this.projects.length > 1;
        },
    },
    methods: {
        ...mapActions([
            'switchProject',
            'fetchProjects',
        ]),
        updateProject() {
            this.projectEdited = cloneDeep(this.currentProject);
            this.$refs.projectDialog.show();
        },
        createProject() {
            this.projectEdited = {
                name: '',
                users: [],
            };
            this.$refs.projectDialog.show();
        },
        saveProject(project) {
            this.switchProject(project);
            this.fetchProjects();
        },
        deleteProject({id}) {
            this.switchProject(this.projects.find(project => project.id != id));
        },
    },
};
</script>
