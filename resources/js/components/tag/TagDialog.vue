<template>
    <v-dialog v-model="visible" max-width="560px" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    {{ headline }}
                </v-card-title>
                <v-card-text>
                    <TagForm v-model="data" @submit="submit" class="mt-3"></TagForm>
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
    import TagForm from './TagForm.vue';

    export default {
        components: {
            TagForm,
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
                return (this.isNew ? 'Create' : 'Update') + ' Tag';
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
                    api.tags.show(this.id).then(this.setData);
                }
            },
            remove() {
                this.$root.confirm(`Delete Tag?`).then(() => {
                    api.tags.destroy(this.id).then(response => {
                        this.$emit('delete', this.data);
                        this.$store.dispatch('deleteTag', this.data);
                        this.close();
                    });
                });
            },
            submit() {
                if (!this.isValid) {
                    this.$refs.form.validate();
                    return;
                }

                api.tags.save(this.id, this.data).then(tag => {
                    this.$emit('save', tag);
                    this.$store.dispatch('saveTag', tag);
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
