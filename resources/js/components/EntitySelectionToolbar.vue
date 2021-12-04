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

        <v-dialog v-model="visibleAddTag" max-width="320px" scrollable>
            <v-card>
                <v-card-title>
                    Add Tag
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="pa-0">
                    <v-list>
                        <v-list-item
                            v-for="item in tags"
                            :key="item.id"
                            @click="addTag(item)"
                        >
                            <v-avatar :color="item.color" size="8" class="mr-2"></v-avatar>
                            <span :class="{'grey--text text--darken-2': !item.entities_count}">{{ item.name }}</span>
                            <v-spacer></v-spacer>
                            <span v-if="item.entities_count" class="caption grey--text text--darken-1">{{ item.entities_count }}</span>
                        </v-list-item>
                    </v-list>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="visibleAddTag = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="visibleRemoveTag" max-width="320px" scrollable>
            <v-card>
                <v-card-title>
                    Remove Tag
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="pa-0">
                    <v-list>
                        <v-list-item
                            v-for="item in availableTags"
                            :key="item.id"
                            @click="removeTag(item)"
                        >
                            <v-avatar :color="item.color" size="8" class="mr-2"></v-avatar>
                            <span :class="{'grey--text text--darken-2': !item.entities_count}">{{ item.name }}</span>
                            <v-spacer></v-spacer>
                            <span v-if="item.entities_count" class="caption grey--text text--darken-1">{{ item.entities_count }}</span>
                        </v-list-item>
                    </v-list>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="visibleRemoveTag = false">Cancel</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-toolbar>
</template>

<script>
    import api from '../api';

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
            queryTags: {
                type: Array,
                default: () => [],
            },
        },
        data() {
            return {
                tags: [],
                visibleAddTag: false,
                visibleRemoveTag: false,
            };
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
                return tags;
            },
        },
        methods: {
            fetchTags() {
                api.tags.index({
                    with_tags: this.queryTags.map(tag => tag.name),
                }).then(response => {
                    this.tags = response.data.data;
                });
            },
            showAddTag() {
                this.fetchTags();
                this.visibleAddTag = true;
            },
            showRemoveTag() {
                this.visibleRemoveTag = true;
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
                    api.entities.destroy(this.selectedId).then(() => {
                        this.$emit('update');
                        this.$emit('input', []);
                    });
                });
            },
        },
    }
</script>
