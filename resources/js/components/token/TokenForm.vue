<template>
    <div>
        <v-text-field v-model="value.name" label="Name" autofocus />

        <v-text-field :value="value.apikey" label="Api Key" disabled />

        <div v-for="(item, i) in items" :key="i">
            <div class="caption">
                {{ getPresetName(item.presetId) }}
            </div>
            <v-row justify="space-between" class="ml-0 mr-4">
                <v-checkbox
                    label="Create"
                    :input-value="item.can_create"
                    :true-value="1"
                    :false-value="0"
                />
                <v-checkbox
                    label="Read"
                    :input-value="item.can_read"
                    :true-value="1"
                    :false-value="0"
                />
                <v-checkbox
                    label="Update"
                    :input-value="item.can_update"
                    :true-value="1"
                    :false-value="0"
                />
                <v-checkbox
                    label="Delete"
                    :input-value="item.can_delete"
                    :true-value="1"
                    :false-value="0"
                />
            </v-row>
            <v-divider class="mb-4"></v-divider>
        </div>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import keyBy from '../../functions/keyBy';

export default {
    data() {
        return {
            test: true,
        };
    },
    props: {
        editable: {
            type: Array,
        },
        value: {
            type: Object,
            default: () => {},
        },
    },
    computed: {
        ...mapState({
            presets: state => state.project.presets,
        }),
        presetsById() {
            return keyBy(this.presets ?? [], v => v.id);
        },
        items() {
            const accessByPresetId = keyBy(this.value?.access ?? [], v => v.preset_id);

            return this.presets.map(preset => {
                return {
                    presetId: preset.id,
                    can_create: 0,
                    can_read: 0,
                    can_update: 0,
                    can_delete: 0,
                    ...(accessByPresetId[preset.id] ?? {}),
                };
            });
        },
    },
    methods: {
        getPresetName(presetId) {
            return this.presetsById[presetId]?.name;
        },
    },
};
</script>
