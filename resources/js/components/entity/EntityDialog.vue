<template>
    <v-dialog v-if="value" v-model="visible" max-width="560px" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    {{ headline }}
                </v-card-title>
                <v-card-text>
                    <EntityForm v-model="value" @submit="submit" class="mt-3"></EntityForm>
                </v-card-text>
                <v-card-actions>
                    <v-btn v-if="!isNew" color="grey" icon @click="remove"><v-icon>mdi-delete</v-icon></v-btn>
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
    import EntityForm from './EntityForm';

    export default {
        components: {
            EntityForm,
        },
        props: {
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
            isNew() {
                return !this.value.id;
            },
            headline() {
                return (this.isNew ? 'Create' : 'Update') + ' Entity';
            },
        },
        data() {
            return {
                visible: false,
                isValid: false,
            };
        },
        methods: {
            remove() {
                this.$root.confirm(`Delete Entity?`).then(() => {
                    api.entities.destroy(this.value.id).then(response => {
                        this.$emit('delete', this.value);
                        this.$store.dispatch('deleteEntity', this.value);
                        this.close();
                    });
                });
            },
            submit() {
                if (!this.isValid) {
                    this.$refs.form.validate();
                    return;
                }

                api.entities.save(this.value.id, this.value).then(response => {
                    const entity = this.processValue(response.data.data);
                    this.$emit('input', entity);
                    this.$store.dispatch('saveEntity', entity);
                    this.close();
                });
            },
            show() {
                this.resetValidation();
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
