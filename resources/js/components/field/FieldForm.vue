<template>
    <div>
        <v-text-field v-model="modelValue.name" @change="nameChanged(modelValue)" :rules="rules.required" label="Name" autofocus />
        <v-text-field v-model="modelValue.code" @change="nameChanged(modelValue)" :rules="rules.required" label="Code" />
        <v-autocomplete :items="types" v-model="modelValue.type" :rules="rules.required" label="Type" />
        <v-autocomplete :items="tags" item-value="id" item-title="name" v-model="modelValue.tag_id" :rules="rules.required" label="Tag" chips />
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import makeCode from '../../functions/makeCode';

    export default {
        props: {
            modelValue: {
                type: Object,
                default: () => {},
            },
        },
        computed: {
            ...mapState({
                tags: state => state.project.tags,
            }),
            types: () => [
                { title: 'String', value: 'string' },
                { title: 'Text', value: 'text' },
                { title: 'Url', value: 'url' },
                { title: 'Email', value: 'email' },
                { title: 'Color', value: 'color' },
                { title: 'Date', value: 'date' },
                { title: 'Time', value: 'time' },
                { title: 'Rating', value: 'rating' },
            ],
            rules() {
                return ({
                    required: [
                        v => !!v || 'Required',
                    ],
                });
            },
        },
        methods: {
            nameChanged(field) {
                if (!field.code) {
                    field.code = makeCode(field.name);
                }
            },
        },
    }
</script>
