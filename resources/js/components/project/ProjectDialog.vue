<template>
    <v-dialog v-model="visible" max-width="560px" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    {{ headline }}
                </v-card-title>
                <v-card-text>
                    <ProjectForm v-model="data" :errors="errors" @submit="submit" class="mt-3"></ProjectForm>
                </v-card-text>
                <v-card-actions>
                    <v-btn v-if="!isNew && deletable" color="grey" icon @click="remove"><v-icon>mdi-delete</v-icon></v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" text type="submit">{{ isNew ? 'Create' : 'Update' }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
    import api from '../../api';
    import cloneDeep from 'clone-deep';
    import ProjectForm from './ProjectForm.vue';

    export default {
        components: {
            ProjectForm,
        },
        props: {
            deletable: {
                type: Boolean,
                default: true,
            },
            value: {
                type: Object,
                default: () => ({}),
            },
        },
        data() {
            return {
                visible: false,
                isValid: false,
                data: {},
                errors: {},
            };
        },
        computed: {
            id() {
                return this.data?.id;
            },
            isNew() {
                return !this.id;
            },
            headline() {
                return (this.isNew ? 'Create' : 'Update') + ' Project';
            },
        },
        watch: {
            visible(value) {
                if (!value) {
                    this.$emit('close');
                }
            },
            value: {
                immediate: true,
                handler(value) {
                    this.setData(cloneDeep(value));
                    this.fetch();
                },
            },
        },
        methods: {
            setData(data) {
                this.data = data;
            },
            fetch() {
                if (this.id) {
                    api.projects.show(this.id).then(this.setData);
                }
            },
            remove() {
                this.$root.confirm(`Delete Project?`).then(() => {
                    api.projects.destroy(this.id).then(response => {
                        this.$emit('delete', this.data);
                        this.$store.dispatch('deleteProject', this.data);
                        this.close();
                    });
                });
            },
            submit() {
                if (!this.isValid) {
                    this.$refs.form.validate();
                    return;
                }

                return api.projects.save(this.id, this.data).then(project => {
                    this.$emit('input', project);
                    this.$store.dispatch('saveProject', project);
                    this.close();
                }).catch(error => {
                    this.errors = error.response.data.errors || {};
                })
            },
            show() {
                this.resetValidation();
                this.errors = {};
                this.visible = true;
            },
            close() {
                this.visible = false;
            },
            resetValidation() {
                this.$refs.form && this.$refs.form.resetValidation();
            },
        },
    };
</script>
