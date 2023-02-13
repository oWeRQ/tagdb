<template>
    <v-dialog v-model="visible" :max-width="maxWidth" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    {{ headline }}
                </v-card-title>
                <v-card-text>
                    <component :is="form" :editable="editable" v-model="data" @submit="submit" class="mt-3"></component>
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
    import cloneDeep from 'clone-deep';
    import CrudForm from './CrudForm.vue';

    export default {
        props: {
            title: {
                type: String,
                default: '',
            },
            form: {
                type: Object,
                default: () => CrudForm,
            },
            api: {
                type: Object,
                required: true,
            },
            deletable: {
                type: Boolean,
                default: true,
            },
            editable: {
                type: Array,
            },
            processItem: {
                type: Function,
                default: value => value,
            },
            value: {
                type: Object,
                default: () => ({}),
            },
            maxWidth: {
                type: String,
                default: '560px',
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
                return (this.isNew ? 'Create' : 'Update') + ' ' + this.title;
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
                this.data = this.processItem(data);
            },
            fetch() {
                if (this.id) {
                    this.api.show(this.id).then(this.setData);
                }
            },
            remove() {
                this.$root.confirm(`Delete ${this.title}?`).then(() => {
                    this.api.destroy(this.id).then(response => {
                        this.$emit('delete', this.data);
                        this.close();
                    });
                });
            },
            submit() {
                if (!this.isValid) {
                    this.$refs.form.validate();
                    return;
                }

                this.api.save(this.id, this.data).then(result => {
                    this.$emit('save', result);
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
