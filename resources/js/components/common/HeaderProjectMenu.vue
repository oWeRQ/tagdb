<template>
<div>
    <v-menu
        v-if="account"
        offset-y
    >
        <template v-slot:activator="{ props }">
            <v-btn
                text
                v-bind="props"
                class="ml-4 text-capitalize"
            >
                <v-icon class="mr-1">mdi-folder-outline</v-icon>
                {{ currentProject && currentProject.name }}
                <v-icon class="ml-1" size="20">mdi-chevron-down</v-icon>
            </v-btn>
        </template>

        <v-list dense>
            <v-list-item
                v-for="project in projects"
                :key="project.id"
                :class="{'v-list-item--active primary--text': currentProject && currentProject.id === project.id}"
                @click="switchProject(project)"
            >
                <template v-slot:prepend>
                    <v-icon v-if="currentProject && currentProject.id === project.id">mdi-check</v-icon>
                </template>
                <v-list-item-title>{{ project.name }}</v-list-item-title>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item @click="editProject(currentProject)">
                <template v-slot:prepend>
                    <v-icon>mdi-pencil</v-icon>
                </template>
                <v-list-item-title>Edit Project</v-list-item-title>
            </v-list-item>
            <v-list-item @click="addProject">
                <template v-slot:prepend>
                    <v-icon>mdi-plus</v-icon>
                </template>
                <v-list-item-title>New Project</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-menu>
</div>
</template>

<script>
import { mapActions, mapState } from 'vuex';
import ProjectDialog from '../project/ProjectDialog.vue';

export default {
    computed: {
        ...mapState({
            isReady: state => state.auth.isReady,
            account: state => state.auth.account,
            currentProject: state => state.auth.currentProject,
            projects: state => state.auth.projects,
        }),
        projectDeletable() {
            return this.projects.length > 1;
        },
    },
    methods: {
        ...mapActions([
            'switchProject',
        ]),
        addProject() {
            this.editProject({
                name: '',
                users: [],
            });
        },
        editProject(item) {
            this.$root.showDialog(ProjectDialog, {
                deletable: this.projectDeletable,
                value: item,
            }, {
                save: this.saveProject,
                delete: this.deleteProject,
            });
        },
        saveProject(project) {
            this.switchProject(project);
        },
        deleteProject({id}) {
            this.switchProject(this.projects.find(project => project.id != id));
        },
    },
};
</script>
