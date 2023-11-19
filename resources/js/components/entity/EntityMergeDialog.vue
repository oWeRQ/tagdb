<template>
    <v-dialog v-model="visible" max-width="560px" scrollable>
        <v-form ref="form" v-model="isValid" @submit.prevent="submit">
            <v-card>
                <v-card-title>
                    Merge
                </v-card-title>
                <v-card-text class="pt-1">
                    <h4>Merge to</h4>
                    <v-radio-group v-model="baseEntity" :rules="rules.required">
                        <v-radio
                            v-for="entity in entities"
                            :key="entity.id"
                            :label="`${entity.name} (${date(entity.created_at)})`"
                            :value="entity"
                        ></v-radio>
                    </v-radio-group>

                    <h4>Conflicts</h4>
                    <div v-for="conflictField in conflictFields" :key="conflictField.id">
                        <v-radio-group v-model="conflictContents[conflictField.id]" :rules="rules.required">
                            <template v-slot:label>
                                {{ conflictField.name }}
                            </template>
                            <v-radio
                                v-for="value in conflictField.values"
                                :key="value"
                                :label="value"
                                :value="value"
                            ></v-radio>
                        </v-radio-group>
                    </div>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" text @click="close">Cancel</v-btn>
                    <v-btn color="blue-darken-1" text type="submit">Merge</v-btn>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
import { groupBy, uniqBy, uniq } from 'lodash';
import api from '../../api';
import date from '../../functions/date';

export default {
    props: {
        selectedId: {
            type: Array,
        },
    },
    data() {
        return {
            visible: false,
            isValid: true,
            entities: [],
            baseEntity: null,
            conflictContents: {},
        };
    },
    computed: {
        rules() {
            return {
                required: [value => !!value || 'Required.'],
            };
        },
        conflictFields() {
            if (this.entities.length === 0)
                return [];

            const fields = groupBy(this.entities.flatMap(item => item.values), 'field_id');

            return Object.values(fields).map(values => ({
                id: values[0].field.id,
                name: values[0].field.name,
                values: uniq(values.map(value => value.content)),
            })).filter(field => field.values.length > 1);
        },
        resolvedContents() {
            return this.entities.flatMap(item => item.values).reduce((acc, cur) => {
                acc[cur.field_id] = cur.content;
                return acc;
            }, {});
        },
        resolvedTags() {
            return uniqBy(this.entities.flatMap(item => item.tags), 'id');
        },
        mergedData() {
            return {
                ...this.baseEntity,
                tags: this.resolvedTags,
                contents: {
                    ...this.resolvedContents,
                    ...this.conflictContents,
                },
            };
        },
        mergedId() {
            return this.baseEntity.id;
        },
        destroyIds() {
            return this.entities.map(entity => entity.id).filter(id => id !== this.mergedId);
        },
    },
    watch: {
        visible(value) {
            if (!value) {
                this.$emit('close');
            }
        },
        selectedId: {
            immediate: true,
            handler: 'fetchEntities',
        },
    },
    methods: {
        date,
        fetchEntities () {
            const filter = {
                id: { in: this.selectedId },
            };
            api.entities.index({ query: { filter }, sort: 'created_at' }).then(entities => {
                this.entities = entities;
            });
        },
        submit() {
            if (!this.isValid)
                return;

            api.entities.update(this.mergedId, this.mergedData).then(() => {
                return api.entities.destroyMany({ id: this.destroyIds });
            }).then(() => {
                this.$emit('done');
                this.close();
            });
        },
        show() {
            this.visible = true;
        },
        close() {
            this.visible = false;
        },
    },
};
</script>
