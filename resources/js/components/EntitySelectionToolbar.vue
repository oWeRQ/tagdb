<template>
    <v-toolbar flat color="grey lighten-2">
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

                const items = this.value.filter(item => {
                    const idx = item.tags.findIndex(tag => tag.name === this.selectedTag);
                    if (state && idx === -1) {
                        item.tags.push(tag);
                        return true;
                    }
                    if (!state && idx !== -1) {
                        item.tags.splice(idx, 1);
                        return true;
                    }
                    return false;
                });

                const requests = items.map(item => axios.put(this.resource + '/' + item.id, {tags: item.tags}));
                this.$emit('input', []);
                Promise.all(requests).then(this.$emit('update'));
            },
            deleteItems() {
                this.$root.confirm('Delete selected items?').then(() => {
                    const requests = this.value.map(item => axios.delete(this.resource + '/' + item.id));
                    this.$emit('input', []);
                    Promise.all(requests).then(this.$emit('update'));
                });
            },
        },
    }
</script>
