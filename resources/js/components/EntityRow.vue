<template>
    <tr>
        <td v-for="header in headers" :key="header.value" :class="header.align && 'text-' + header.align">
            <template v-if="header.value === 'data-table-select'">
                <RowCheckbox :isSelected="isSelected" :select="select"></RowCheckbox>
            </template>
            <template v-else-if="header.value === 'actions'">
                <RowActions :item="item" @edit="$emit('edit')"></RowActions>
            </template>
            <template v-else-if="header.value === 'tags'">
                <RowTags :item="item" :query="query" @click:tag="$emit('click:tag', $event)"></RowTags>
            </template>
            <template v-else-if="header.value === 'name'">
                <RowName :item="item" @edit="$emit('edit')"></RowName>
            </template>
            <template v-else-if="header.type === 'date'">
                <RowDate :item="item" :header="header"></RowDate>
            </template>
            <template v-else-if="header.type === 'url'">
                <RowUrl :item="item" :header="header"></RowUrl>
            </template>
            <template v-else-if="header.type === 'color'">
                <RowColor :item="item" :header="header"></RowColor>
            </template>
            <template v-else-if="header.type === 'rating'">
                <RowRating :item="item" :header="header"></RowRating>
            </template>
            <template v-else>
                <RowText :item="item" :header="header"></RowText>
            </template>
        </td>
    </tr>
</template>

<script>
    import RowActions from './row/RowActions.vue';
    import RowCheckbox from './row/RowCheckbox.vue';
    import RowColor from './row/RowColor.vue';
    import RowDate from './row/RowDate.vue';
    import RowName from './row/RowName.vue';
    import RowRating from './row/RowRating.vue';
    import RowTags from './row/RowTags.vue';
    import RowText from './row/RowText.vue';
    import RowUrl from './row/RowUrl.vue';

    export default {
        components: {
            RowActions,
            RowCheckbox,
            RowColor,
            RowDate,
            RowName,
            RowRating,
            RowTags,
            RowText,
            RowUrl,
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
