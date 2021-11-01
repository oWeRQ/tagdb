<template>
    <v-dialog v-if="value" v-model="visible" :max-width="maxWidth">
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    <span class="headline">{{ headline }}</span>
                </v-card-title>
                <v-card-text>
                    <component :is="form" :editable="editable" v-model="value"></component>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" text type="submit" :disabled="!isValid">Save</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
    import axios from 'axios';
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
            resource: {
                type: String,
                required: true,
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
                default: '500px',
            },
        },
        computed: {
            headline() {
                return (this.value.id ? 'Update' : 'Create') + ' ' + this.title;
            },
        },
        data() {
            return {
                visible: false,
                isValid: false,
            };
        },
        methods: {
            submit() {
                if (!this.isValid)
                    return;

                if (this.value.id) {
                    axios.put(this.resource + '/' + this.value.id, this.value).then(this.success);
                } else {
                    axios.post(this.resource, this.value).then(this.success);
                }
            },
            success(response) {
                this.$emit('input', this.processValue(response.data.data));
                this.close();
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
