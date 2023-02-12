<template>
    <div>
        <v-text-field :modelValue="modelValue.name" @update:modelValue="changeName($event)" label="Name" autofocus />

        <v-text-field :modelValue="modelValue.apikey" label="Api Key" disabled />

        <div v-for="(item, i) in items" :key="i">
            <div class="caption">
                {{ getPresetName(item.preset_id) }}
            </div>
            <v-row justify="space-between" class="ml-0 mr-4">
                <v-checkbox
                    label="Create"
                    @change="changeAccess(item.preset_id, 'can_create', $event)"
                    :input-value="item.can_create"
                    :true-value="1"
                    :false-value="0"
                />
                <v-checkbox
                    label="Read"
                    @change="changeAccess(item.preset_id, 'can_read', $event)"
                    :input-value="item.can_read"
                    :true-value="1"
                    :false-value="0"
                />
                <v-checkbox
                    label="Update"
                    @change="changeAccess(item.preset_id, 'can_update', $event)"
                    :input-value="item.can_update"
                    :true-value="1"
                    :false-value="0"
                />
                <v-checkbox
                    label="Delete"
                    @change="changeAccess(item.preset_id, 'can_delete', $event)"
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
    props: {
        editable: {
            type: Array,
        },
        modelValue: {
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
        accessById() {
            return keyBy(this.modelValue?.access ?? [], v => v.preset_id);
        },
        items() {
            return this.getItems();
        },
    },
    methods: {
        changeName(name) {
            this.$emit('update:modelValue', {
                ...this.modelValue,
                name,
            });
        },
        changeAccess(preset_id, perm, value) {
            const access = this.items.map(item => {
                if (item.preset_id === preset_id) {
                    return {
                        ...item,
                        [perm]: value,
                    };
                }

                return item;
            });

            this.$emit('update:modelValue', {
                ...this.modelValue,
                access,
            });
        },
        getItems() {
            return this.presets.map(preset => this.getAccessById(preset.id));
        },
        getAccessById(preset_id) {
            return {
                preset_id,
                can_create: 0,
                can_read: 0,
                can_update: 0,
                can_delete: 0,
                ...(this.accessById[preset_id] ?? {}),
            };
        },
        getPresetName(preset_id) {
            return this.presetsById[preset_id]?.name;
        },
    },
};
</script>
