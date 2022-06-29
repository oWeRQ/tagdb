<template>
    <v-toolbar flat color="blue lighten-5">
        <v-btn icon @click="$emit('input', [])">
            <v-icon>mdi-close</v-icon>
        </v-btn>

        <v-toolbar-title class="mr-6">{{ value.length }} selected</v-toolbar-title>

        <v-btn icon @click="showAddTag">
            <v-icon>mdi-tag-plus</v-icon>
        </v-btn>

        <v-btn icon @click="showRemoveTag">
            <v-icon>mdi-tag-minus</v-icon>
        </v-btn>

        <v-btn icon @click="showUpdateValues">
            <v-icon>mdi-form-textbox</v-icon>
        </v-btn>

        <v-btn icon @click="showMerge">
            <v-icon>mdi-merge</v-icon>
        </v-btn>

        <v-btn icon @click="deleteItems">
            <v-icon>mdi-delete</v-icon>
        </v-btn>
    </v-toolbar>
</template>

<script>
    import api from '../../api';
    import TagSelectDialog from './TagSelectDialog.vue';
    import UpdateValuesDialog from './UpdateValuesDialog.vue';
    import EntityMergeDialog from './EntityMergeDialog.vue';

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
                let found;
                for (let item of this.value) {
                    for (let tag of item.tags) {
                        if (found = tags.find(t => t.id === tag.id))
                            found.entities_count++;
                        else
                            tags.push({ ...tag, entities_count: 1 });
                    }
                }
                return tags.sort((a, b) => b.entities_count - a.entities_count || a.name.localeCompare(b.name));
            },
        },
        methods: {
            showAddTag() {
                api.tags.index({
                    with_tags: this.queryTagNames.filter(name => name[0] !== '-'),
                }).then((tags) => {
                    this.$root.showDialog(TagSelectDialog, {
                        title: 'Add',
                        tags,
                        hasCreate: true,
                    }, {
                        select: this.addTags,
                    });
                });
            },
            showRemoveTag() {
                this.$root.showDialog(TagSelectDialog, {
                    title: 'Remove',
                    tags: this.availableTags,
                }, {
                    select: this.removeTags,
                });
            },
            showUpdateValues() {
                this.$root.showDialog(UpdateValuesDialog, {
                    selectedId: this.selectedId,
                    queryTagNames: this.queryTagNames,
                }, {
                    done: this.done,
                });
            },
            showMerge() {
                this.$root.showDialog(EntityMergeDialog, {
                    selectedId: this.selectedId,
                }, {
                    done: this.done,
                });
            },
            addTags(tags) {
                const requests = tags.map(tag => api.tags.attachEntities(tag.id, { id: this.selectedId }));
                Promise.all(requests).then(this.done);
            },
            removeTags(tags) {
                const requests = tags.map(tag => api.tags.detachEntities(tag.id, { id: this.selectedId }));
                Promise.all(requests).then(this.done);
            },
            deleteItems() {
                this.$root.confirm('Delete selected items?').then(() => {
                    api.entities.destroyMany({ id: this.selectedId }).then(this.done);
                });
            },
            done() {
                this.$emit('update');
                this.$emit('input', []);
            },
        },
    }
</script>
