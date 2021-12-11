<template>
    <v-dialog v-if="value" v-model="visible" max-width="560px" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    {{ headline }}
                </v-card-title>
                <v-card-text>
                    <ProjectForm v-model="value" :errors="errors" @submit="submit" class="mt-3"></ProjectForm>
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
    import ProjectForm from './ProjectForm';

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
            processValue: {
                type: Function,
                default: value => value,
            },
        },
        computed: {
            id() {
                return this.value.id;
            },
            isNew() {
                return !this.id;
            },
            headline() {
                return (this.isNew ? 'Create' : 'Update') + ' Project';
            },
        },
        data() {
            return {
                visible: false,
                isValid: false,
                errors: {},
            };
        },
        methods: {
            remove() {
                this.$root.confirm(`Delete Project?`).then(() => {
                    api.projects.destroy(this.id).then(response => {
                        this.$emit('delete', this.value);
                        this.close();
                    });
                });
            },
            submit() {
                if (!this.isValid) {
                    this.$refs.form.validate();
                    return;
                }

                return api.projects.save(this.id, this.value).then(response => {
                    this.$emit('input', this.processValue(response.data.data));
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
