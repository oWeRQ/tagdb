<template>
    <div class="v-rating d-inline-block v-rating--readonly">
        <button type="button" aria-label="Rating 1 of 5" class="v-icon notranslate v-icon--link theme--light mdi" :class="value >= 1 ? fullClass : emptyClass" style="font-size: 18px;"></button>
        <button type="button" aria-label="Rating 2 of 5" class="v-icon notranslate v-icon--link theme--light mdi" :class="value >= 2 ? fullClass : emptyClass" style="font-size: 18px;"></button>
        <button type="button" aria-label="Rating 3 of 5" class="v-icon notranslate v-icon--link theme--light mdi" :class="value >= 3 ? fullClass : emptyClass" style="font-size: 18px;"></button>
        <button type="button" aria-label="Rating 4 of 5" class="v-icon notranslate v-icon--link theme--light mdi" :class="value >= 4 ? fullClass : emptyClass" style="font-size: 18px;"></button>
        <button type="button" aria-label="Rating 5 of 5" class="v-icon notranslate v-icon--link theme--light mdi" :class="value >= 5 ? fullClass : emptyClass" style="font-size: 18px;"></button>
    </div>
</template>

<script>
import { getObjectValueByPath } from 'vuetify/lib/util/helpers';

export default {
    props: {
        header: {
            type: Object,
        },
        item: {
            type: Object,
        },
        emptyClass: {
            default: 'mdi-star-outline grey--text text--lighten-1',
        },
        fullClass: {
            default: 'mdi-star orange--text',
        },
    },
    computed: {
        value() {
            return Number(getObjectValueByPath(this.item, this.header.value));
        },
    },
};
</script>
