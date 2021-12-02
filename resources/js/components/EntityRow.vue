<template>
    <tr :class="{'v-data-table__selected': isSelected, 'v-data-table__mobile-table-row': isMobile}">
        <td v-for="header in headers" :key="header.value" :class="{[`text-${header.align}`]: header.align, 'v-data-table__mobile-row': isMobile}">
            <span v-if="isMobile" class="v-data-table__mobile-row__header">
                {{ header.text }}
            </span>
            <span class="v-data-table__mobile-row__cell">
                <RowCheckbox v-if="header.value === 'data-table-select'" :isSelected="isSelected" :select="select"></RowCheckbox>
                <RowActions v-else-if="header.value === 'actions'" :item="item" @edit="$emit('edit')"></RowActions>
                <RowTags v-else-if="header.value === 'tags'" :item="item" :query="query" @click:tag="$emit('click:tag', $event)"></RowTags>
                <RowName v-else-if="header.value === 'name'" :item="item" @edit="$emit('edit')"></RowName>
                <RowDate v-else-if="header.type === 'date'" :item="item" :header="header"></RowDate>
                <RowUrl v-else-if="header.type === 'url'" :item="item" :header="header"></RowUrl>
                <RowColor v-else-if="header.type === 'color'" :item="item" :header="header"></RowColor>
                <RowRating v-else-if="header.type === 'rating'" :item="item" :header="header"></RowRating>
                <RowText v-else :item="item" :header="header"></RowText>
            </span>
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
            isMobile: {
                type: Boolean,
                default: false,
            },
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
