<template>
    <tr :class="{'v-data-table__selected': isSelected, 'v-data-table__mobile-table-row': isMobile}">
        <td v-for="header in headers" :key="header.value" :class="{[`text-${header.align}`]: header.align, 'v-data-table__mobile-row': isMobile}">
            <span v-if="isMobile" class="v-data-table__mobile-row__header">
                {{ header.text }}
            </span>
            <span class="v-data-table__mobile-row__cell">
                <component
                    :is="getComponent(header)"
                    :header="header"
                    :item="item"
                    @edit="$emit('edit')"
                    @click:tag="$emit('click:tag', $event)"
                ></component>
            </span>
        </td>
    </tr>
</template>

<script>
    import RowActions from '../row/RowActions.vue';
    import RowCheckbox from '../row/RowCheckbox.vue';
    import RowColor from '../row/RowColor.vue';
    import RowDate from '../row/RowDate.vue';
    import RowName from '../row/RowName.vue';
    import RowRating from '../row/RowRating.vue';
    import RowTags from '../row/RowTags.vue';
    import RowText from '../row/RowText.vue';
    import RowUrl from '../row/RowUrl.vue';

    export default {
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
        methods: {
            getComponent(header) {
                if (header.value === 'data-table-select')
                    return RowCheckbox;

                if (header.value === 'actions')
                    return RowActions;

                if (header.value === 'tags')
                    return RowTags;

                if (header.value === 'name')
                    return RowName;

                if (header.type === 'date')
                    return RowDate;

                if (header.type === 'url')
                    return RowUrl;

                if (header.type === 'color')
                    return RowColor;

                if (header.type === 'rating')
                    return RowRating;

                return RowText;
            },
        },
    }
</script>
