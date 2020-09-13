<template>
    <tr>
        <td v-for="header in headers" :key="header.value" :class="header.align && 'text-' + header.align">
            <template v-if="header.value === 'data-table-select'">
                <v-simple-checkbox :value="isSelected" @input="select" class="v-data-table__checkbox"></v-simple-checkbox>
            </template>
            <template v-else-if="header.value === 'actions'">
                <v-icon @click="$emit('edit')" color="grey darken-1" class="mr-2" :title="'ID: ' + item.id">
                    mdi-pencil
                </v-icon>
                <v-icon @click="$emit('delete')" color="grey darken-1">
                    mdi-delete
                </v-icon>
            </template>
            <template v-else-if="header.value === 'tags'">
                <v-chip-group multiple active-class="primary--text" v-model="query.tags">
                    <v-chip v-for="tag in item.tags" :key="tag.name" :value="tag.name" :color="query.tags.includes(tag.name) ? null : tag.color" :dark="!!tag.color" small class="lighten-2">
                        {{ tag.name }}
                        <sup v-if="tag.fields.length">{{ tag.fields.length }}</sup>
                    </v-chip>
                </v-chip-group>
            </template>
            <template v-else-if="header.value === 'created_at'">
                {{ item.created_at | date }}
            </template>
            <template v-else-if="header.type === 'url'">
                <a :href="item | value(header.value)" :title="item | value(header.value)" target="_blank">{{ item | value(header.value) | truncate }}</a>
            </template>
            <template v-else-if="header.type === 'color'">
                <v-chip>
                    <v-avatar left :color="item | value(header.value)"></v-avatar>
                    {{ item | value(header.value) }}
                </v-chip>
            </template>
            <template v-else>
                {{ item | value(header.value) | truncate }}
            </template>
        </td>
    </tr>
</template>

<script>
    import { getObjectValueByPath } from 'vuetify/lib/util/helpers'
    import date from '../functions/date';
    import truncate from '../functions/truncate';

    export default {
        filters: {
            date,
            truncate,
            value: getObjectValueByPath,
        },
        props: {
            query: {
                type: Object,
                default: () => ({tags: []}),
            },
            item: {
                type: Object,
            },
            headers: {
                type: Array,
            },
            isSelected: {
                type: Boolean,
            },
            select: {
                type: Function,
            },
        },
    }
</script>
