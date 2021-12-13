<template>
    <v-dialog v-if="value" v-model="visible" max-width="560px" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    {{ headline }}
                </v-card-title>
                <v-card-text>
                    <FieldForm v-model="value" @submit="submit" class="mt-3"></FieldForm>
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
    import FieldForm from './FieldForm';

    export default {
        components: {
            FieldForm,
        },
        props: {
            value: {
                type: Object,
                default: () => ({}),
            },
        },
        computed: {
            isNew() {
                return !this.value.id;
            },
            headline() {
                return (this.isNew ? 'Create' : 'Update') + ' Field';
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
                this.$root.confirm(`Delete Field?`).then(() => {
                    api.fields.destroy(this.value.id).then(response => {
                        this.$emit('delete', this.value);
                        this.$store.dispatch('deleteField', this.value);
                        this.close();
                    });
                });
            },
            submit() {
                if (!this.isValid) {
                    this.$refs.form.validate();
                    return;
                }

                api.fields.save(this.value.id, this.value).then(field => {
                    this.$emit('input', field);
                    this.$store.dispatch('saveField', field);
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
