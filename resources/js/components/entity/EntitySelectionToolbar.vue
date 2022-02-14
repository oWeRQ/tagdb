<template>
    <v-toolbar flat color="blue lighten-5">
        <v-btn icon @click="$emit('input', [])">
            <v-icon>mdi-close</v-icon>
        </v-btn>

        <v-toolbar-title>{{ value.length }} selected</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn icon @click="showAddTag">
            <v-icon>mdi-tag-plus</v-icon>
        </v-btn>

        <v-btn icon @click="showRemoveTag">
            <v-icon>mdi-tag-minus</v-icon>
        </v-btn>

        <v-btn icon @click="deleteItems">
            <v-icon>mdi-delete</v-icon>
        </v-btn>
    </v-toolbar>
</template>

<script>
    import api from '../../api';
    import TagAddDialog from './TagAddDialog.vue';
    import TagRemoveDialog from './TagRemoveDialog.vue';

    export default {
        props: {
            value: {
                type: Array,
                default: () => [],
            },
            queryTagNames: {
                type: Array,
                default: () => [],
            },
        },
        computed: {
            selectedId() {
                return this.value.map(item => item.id);
            },
            availableTags() {
                const tags = [];
                for (let item of this.value) {
                    for (let tag of item.tags) {
                        if (!tags.some(t => t.id === tag.id))
                            tags.push(tag);
                    }
                }
                return tags.sort((a, b) => a.name.localeCompare(b.name));
            },
        },
        methods: {
            showAddTag() {
                api.tags.index({
                    with_tags: this.queryTagNames.filter(name => name[0] !== '-'),
                }).then((tags) => {
                    this.$root.showDialog(TagAddDialog, {
                        tags,
                    }, {
                        select: this.addTag,
                    });
                });
            },
            showRemoveTag() {
                this.$root.showDialog(TagRemoveDialog, {
                    tags: this.availableTags,
                }, {
                    select: this.removeTag,
                });
            },
            addTag(tag) {
                api.tags.attachEntities(tag.id, { id: this.selectedId }).then(() => {
                    this.$emit('update');
                    this.$emit('input', []);
                });
            },
            removeTag(tag) {
                api.tags.detachEntities(tag.id, { id: this.selectedId }).then(() => {
                    this.$emit('update');
                    this.$emit('input', []);
                });
            },
            deleteItems() {
                this.$root.confirm('Delete selected items?').then(() => {
                    api.entities.destroyMany({ id: this.selectedId }).then(() => {
                        this.$emit('update');
                        this.$emit('input', []);
                    });
                });
            },
        },
    }
</script>
