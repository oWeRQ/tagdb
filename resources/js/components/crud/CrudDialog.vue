<template>
    <v-dialog v-if="value" v-model="visible" :max-width="maxWidth" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    {{ headline }}
                </v-card-title>
                <v-card-text>
                    <component :is="form" :editable="editable" v-model="value" @submit="submit" class="mt-3"></component>
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
    import CrudForm from './CrudForm';

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
            value: {
                type: Object,
                default: () => ({}),
            },
            processValue: {
                type: Function,
                default: value => value,
            },
            maxWidth: {
                type: String,
                default: '560px',
            },
        },
        computed: {
            isNew() {
                return !this.value.id;
            },
            headline() {
                return (this.isNew ? 'Create' : 'Update') + ' ' + this.title;
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
                this.$root.confirm(`Delete ${this.title}?`).then(() => {
                    this.api.destroy(this.value.id).then(response => {
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

                this.api.save(this.value.id, this.value).then(result => {
                    this.$emit('input', this.processValue(result));
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
