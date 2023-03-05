<template>
    <div class="d-flex" @click="$emit('edit')">
        <span aria-label="Rating 1 of 5" class="v-icon mr-2 mdi" :class="value == 0.5 ? halfClass : (value >= 1 ? fullClass : emptyClass)" style="font-size: 18px;"></span>
        <span aria-label="Rating 2 of 5" class="v-icon mr-2 mdi" :class="value == 1.5 ? halfClass : (value >= 2 ? fullClass : emptyClass)" style="font-size: 18px;"></span>
        <span aria-label="Rating 3 of 5" class="v-icon mr-2 mdi" :class="value == 2.5 ? halfClass : (value >= 3 ? fullClass : emptyClass)" style="font-size: 18px;"></span>
        <span aria-label="Rating 4 of 5" class="v-icon mr-2 mdi" :class="value == 3.5 ? halfClass : (value >= 4 ? fullClass : emptyClass)" style="font-size: 18px;"></span>
        <span aria-label="Rating 5 of 5" class="v-icon mdi" :class="value == 4.5 ? halfClass : (value >= 5 ? fullClass : emptyClass)" style="font-size: 18px;"></span>
    </div>
</template>

<script>
import getPath from '../../functions/getPath';

export default {
    props: {
        header: {
            type: Object,
        },
        item: {
            type: Object,
        },
        emptyClass: {
            default: 'mdi-star-outline text-grey',
        },
        halfClass: {
            default: 'mdi-star-half-full text-orange',
        },
        fullClass: {
            default: 'mdi-star text-orange',
        },
    },
    computed: {
        value() {
            return Number(getPath(this.item, this.header.key));
        },
    },
};
</script>
