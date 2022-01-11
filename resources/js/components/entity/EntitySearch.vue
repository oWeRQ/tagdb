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
        clearable
        prepend-inner-icon="mdi-magnify"
        class="shrink mx-2"
        autofocus
        @blur="blur"
    ></v-text-field>
</template>

<script>
import _ from 'lodash';

export default {
    props: {
        value: {},
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
    methods: {
        show() {
            this.isShow = true;
        },
        blur() {
            this.isShow = !!this.value;
        },
        emitInput: _.debounce(function(value) {
            this.$emit('input', value);
        }, 500),
    },
};
</script>
