<template>
    <v-btn v-if="!isShow" icon @click="show">
        <v-icon>mdi-magnify</v-icon>
    </v-btn>
    <v-text-field
        v-else
        v-model="inputValue"
        placeholder="Search"
        dense
        solo
        single-line
        hide-details
        prepend-inner-icon="mdi-magnify"
        append-icon="mdi-close"
        class="shrink mx-2"
        autofocus
        @blur="blur"
        @click:append="clear"
    ></v-text-field>
</template>

<script>
import { debounce } from 'lodash';

export default {
    props: {
        value: {
            type: String,
            default: '',
        },
        wait: {
            type: Number,
            default: 500,
        },
    },
    data() {
        return {
            isShow: false,
        };
    },
    computed: {
        inputValue: {
            get() {
                return this.value;
            },
            set(value) {
                this.emitInput(value);
            },
        },
    },
    watch: {
        value: 'blur',
    },
    mounted() {
        this.emitInput = debounce(function(value) {
            this.$emit('input', value);
        }, this.wait);
    },
    methods: {
        show() {
            this.isShow = true;
        },
        blur() {
            this.isShow = !!this.value;
        },
        clear() {
            this.isShow = false;
            if (this.value) {
                this.$emit('input', '');
            }
        },
    },
};
</script>
