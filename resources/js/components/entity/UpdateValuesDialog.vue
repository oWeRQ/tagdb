<template>
    <v-dialog v-model="visible" :persistent="persistent" max-width="560px" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    Update Values
                </v-card-title>
                <v-card-text class="pt-1">
                    <v-autocomplete
                        label="Field"
                        v-model="field"
                        :items="fieldItems"
                        :filter="fieldFilter"
                        item-text="name"
                        item-value="id"
                        clearable
                        outlined
                        dense
                        required
                    >
                        <template v-slot:selection="{ item }">
                            <TagChip v-if="item.tag" :tag="item.tag" small class="mr-2"></TagChip>
                            {{ item.name }}
                        </template>
                        <template v-slot:item="{ item }">
                            <TagChip v-if="item.tag" :tag="item.tag" small class="mr-2"></TagChip>
                            {{ item.name }}
                        </template>
                    </v-autocomplete>
                    <v-text-field
                        label="Content"
                        v-model="content"
                        clearable
                        outlined
                        dense
                    ></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue darken-1" text type="submit">Update</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
    import { mapState } from 'vuex';
    import api from '../../api';
    import fuzzyMatch from '../../functions/fuzzyMatch';
    import TagChip from '../tag/TagChip.vue';

    export default {
        components: {
            TagChip,
        },
        props: {
            selectedId: {
                type: Array,
            },
            queryTagNames: {
                type: Array,
            },
        },
        data() {
            return {
                visible: false,
                persistent: false,
                isValid: true,
                field: undefined,
                value: '',
            };
        },
        computed: {
            ...mapState([
                'tags',
                'fields',
            ]),
            fieldItems() {
                return this.tags.filter(tag => this.queryTagNames.includes(tag.name)).flatMap(item => item.fields).map(field => ({
                    ...field,
                    tag: this.tags.find(tag => tag.id == field.tag_id),
                }));
            },
        },
        watch: {
            visible(value) {
                if (!value) {
                    this.$emit('close');
                }
            },
        },
        methods: {
            fieldFilter(item, queryText) {
                return fuzzyMatch(`${item.tag?.name} ${item.name}`, queryText);
            },
            show() {
                this.field = this.fieldItems[0];
                this.visible = true;
            },
            close() {
                this.visible = false;
            },
            submit() {
                this.close();
                api.fields.updateValues(this.field.id, {
                    id: this.selectedId,
                    content: this.content,
                }).then(() => {
                    this.$emit('done');
                });
            },
        },
    };
</script>
