<template>
    <v-btn v-if="!isShow" icon @click="show">
        <v-icon>mdi-magnify</v-icon>
    </v-btn>
    <v-text-field
        v-else
        v-model="inputValue"
        placeholder="Search"
        density="compact"
        variant="solo"
        single-line
        hide-details
        prepend-inner-icon="mdi-magnify"
        append-inner-icon="mdi-close"
        class="shrink mx-2"
        autofocus
        @blur="blur"
        @click:append-inner="clear"
    ></v-text-field>
</template>

<script>
import { debounce } from 'lodash';

export default {
    props: {
        modelValue: {
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
                return this.modelValue;
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
            this.$emit('update:modelValue', value);
        }, this.wait);
    },
    methods: {
        show() {
            this.isShow = true;
        },
        blur() {
            this.isShow = !!this.modelValue;
        },
        clear() {
            this.$el.focus();
            if (this.modelValue) {
                this.$emit('update:modelValue', '');
            }
        },
    },
};
</script>

<style lang="scss" scoped>
    .v-input {
        min-width: 240px;
    }
</style>