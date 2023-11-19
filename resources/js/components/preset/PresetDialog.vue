<template>
    <v-dialog v-model="visible" max-width="560px" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    {{ headline }}
                </v-card-title>
                <v-card-text>
                    <PresetForm v-model="data" @submit="submit" class="mt-3"></PresetForm>
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
    import PresetForm from './PresetForm.vue';

    export default {
        components: {
            PresetForm,
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
                return (this.isNew ? 'Create' : 'Update') + ' Preset';
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
                    api.presets.show(this.id).then(this.setData);
                }
            },
            remove() {
                this.$root.confirm(`Delete Preset?`).then(() => {
                    api.presets.destroy(this.id).then(response => {
                        this.$emit('delete', this.data);
                        this.$store.dispatch('deletePreset', this.data);
                        this.close();
                    });
                });
            },
            submit() {
                if (!this.isValid) {
                    this.$refs.form.validate();
                    return;
                }

                api.presets.save(this.id, this.data).then(preset => {
                    this.$emit('save', preset);
                    this.$store.dispatch('savePreset', preset);
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
