<template>
    <v-toolbar flat color="blue lighten-5">
        <v-btn icon @click="$emit('input', [])">
            <v-icon>mdi-close</v-icon>
        </v-btn>

        <v-toolbar-title>{{ value.length }} selected</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-autocomplete
            v-model="selectedTag"
            :items="tags"
            item-text="name"
            label="Tag"
            hide-details
            single-line
            :style="{maxWidth: '240px'}"
        ></v-autocomplete>

        <v-btn icon @click="toggleTag(true)">
            <v-icon>mdi-tag-plus</v-icon>
        </v-btn>

        <v-btn icon @click="toggleTag(false)">
            <v-icon>mdi-tag-minus</v-icon>
        </v-btn>

        <v-btn icon @click="deleteItems">
            <v-icon>mdi-delete</v-icon>
        </v-btn>
    </v-toolbar>
</template>

<script>
    import axios from 'axios';

    export default {
        props: {
            resource: {
                type: String,
            },
            tagResource: {
                type: String,
                default: '/api/v1/tags',
            },
            value: {
                type: Array,
                default: () => [],
            },
        },
        data() {
            return {
                selectedTag: null,
            };
        },
        computed: {
            tags() {
                return this.$root.tags;
            },
        },
        methods: {
            toggleTag(state) {
                const tag = this.tags.find(tag => tag.name === this.selectedTag);
                if (!tag)
                    return;

                const url = `${this.tagResource}/${tag.id}/entities`;
                const data = {
                    id: this.value.map(item => item.id),
                };
                const success = () => {
                    this.$emit('update');
                    this.$emit('input', []);
                };

                if (state) {
                    axios.post(url, data).then(success);
                } else {
                    axios.delete(url, {data}).then(success);
                }
            },
            deleteItems() {
                this.$root.confirm('Delete selected items?').then(() => {
                    const id = this.value.map(item => item.id);
                    axios.delete(this.resource + '/' + id.join(',')).then(() => {
                        this.$emit('update');
                        this.$emit('input', []);
                    });
                });
            },
        },
    }
</script>
