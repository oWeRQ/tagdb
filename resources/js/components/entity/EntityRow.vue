<template>
    <tr :class="{'v-data-table__selected': isSelected, 'v-data-table__mobile-table-row': isMobile}">
        <td v-for="header in headers" :key="header.key" :class="{'v-data-table__td': true, 'v-data-table-column--no-padding': true, [`text-${header.align}`]: header.align, 'v-data-table__mobile-row': isMobile}">
            <span v-if="isMobile" :class="{'v-data-table__mobile-row__header': isMobile}">
                {{ header.text }}
            </span>
            <span :class="{'v-data-table__mobile-row__cell': isMobile}">
                <RowCheckbox
                    v-if="header.key === 'data-table-select'"
                    :isSelected="isSelected"
                    :select="select"
                ></RowCheckbox>
                <RowTags
                    v-else-if="header.key === 'tags'"
                    :item="item"
                    :tags="tags"
                    @click:tag="$emit('click:tag', $event)"
                    @edit="$emit('edit', header.key)"
                ></RowTags>
                <component
                    v-else
                    :is="getComponent(header)"
                    :header="header"
                    :item="item"
                    @edit="$emit('edit', header.key)"
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
        components: {
            RowCheckbox,
            RowTags,
        },
        props: {
            isMobile: {
                type: Boolean,
                default: false,
            },
            tags: {
                type: Array,
                default: () => [],
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
                default: () => [],
            },
        },
        methods: {
            getComponent(header) {
                if (header.key === 'actions')
                    return RowActions;

                if (header.key === 'name')
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
