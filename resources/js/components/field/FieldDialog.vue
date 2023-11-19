<template>
    <v-dialog v-model="visible" max-width="560px" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    {{ headline }}
                </v-card-title>
                <v-card-text>
                    <FieldForm v-model="data" @submit="submit" class="mt-3"></FieldForm>
                </v-card-text>
                <v-card-actions>
                    <v-btn v-if="!isNew" color="grey" icon @click="remove"><v-icon>mdi-delete</v-icon></v-btn>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue-darken-1" text type="submit">{{ isNew ? 'Create' : 'Update' }}</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
    import api from '../../api';
    import cloneDeep from 'clone-deep';
    import FieldForm from './FieldForm.vue';

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
        data() {
            return {
                visible: false,
                isValid: false,
                data: {},
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
                return (this.isNew ? 'Create' : 'Update') + ' Field';
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
                    api.fields.show(this.id).then(this.setData);
                }
            },
            remove() {
                this.$root.confirm(`Delete Field?`).then(() => {
                    api.fields.destroy(this.id).then(response => {
                        this.$emit('delete', this.data);
                        this.$store.dispatch('deleteField', this.data);
                        this.close();
                    });
                });
            },
            submit() {
                if (!this.isValid) {
                    this.$refs.form.validate();
                    return;
                }

                api.fields.save(this.id, this.data).then(field => {
                    this.$emit('save', field);
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
