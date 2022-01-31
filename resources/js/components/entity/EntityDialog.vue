<template>
    <v-dialog v-model="visible" :persistent="persistent" max-width="560px" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    {{ headline }}
                </v-card-title>
                <v-card-text>
                    <EntityForm v-model="data" @submit="submit" class="mt-3"></EntityForm>
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
    import cloneDeep from 'clone-deep';
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
        },
        data() {
            return {
                visible: false,
                persistent: true,
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
                return (this.isNew ? 'Create' : 'Update') + ' Entity';
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
            data: {
                deep: true,
                handler() {
                    this.persistent = true;
                },
            },
        },
        methods: {
            setData(data) {
                const contents = {};
                for (let value of data.values) {
                    contents[value.field.id] = value.content;
                }
                this.data = { ...data, contents };
                this.$nextTick(() => {
                    this.persistent = false;
                });
            },
            fetch() {
                if (this.id) {
                    api.entities.show(this.id).then(this.setData);
                }
            },
            remove() {
                this.$root.confirm(`Delete Entity?`).then(() => {
                    api.entities.destroy(this.id).then(response => {
                        this.$emit('delete', this.data);
                        this.$store.dispatch('deleteEntity', this.data);
                        this.close();
                    });
                });
            },
            submit() {
                if (!this.isValid) {
                    this.$refs.form.validate();
                    return;
                }

                api.entities.save(this.id, this.data).then(entity => {
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
