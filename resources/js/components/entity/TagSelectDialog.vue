<template>
    <v-dialog v-model="visible" max-width="320px" scrollable>
        <v-card>
            <v-card-title>
                {{ title }}
            </v-card-title>
            <v-text-field
                placeholder="Tag"
                v-model="search"
                @keyup.enter="enter"
                autofocus
                hide-details
                filled
                rounded
                dense
                class="mx-2 mb-2"
            ></v-text-field>
            <v-divider></v-divider>
            <v-card-text class="pa-0">
                <v-list>
                    <v-list-item
                        v-for="item in filteredTags"
                        :key="item.id"
                        @click="select(item)"
                    >
                        <v-avatar :color="item.color" size="8" class="mr-2"></v-avatar>
                        <span :class="{'grey--text text--darken-2': !item.entities_count}">{{ item.name }}</span>
                        <v-spacer></v-spacer>
                        <span v-if="item.entities_count" class="caption grey--text text--darken-1">{{ item.entities_count }}</span>
                    </v-list-item>
                    <v-list-item v-if="canCreate" @click="create">
                        <v-icon left>mdi-plus</v-icon>
                        Create "{{ this.search }}" tag
                    </v-list-item>
                    <v-list-item v-else-if="!filteredTags.length">
                        Not found
                    </v-list-item>
                </v-list>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import fuzzyMatch from '../../functions/fuzzyMatch';

export default {
    props: {
        title: {
            type: String,
        },
        tags: {
            type: Array,
        },
        hasCreate: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            visible: false,
            search: '',
        };
    },
    computed: {
        filteredTags() {
            return this.tags.filter(tag => fuzzyMatch(tag.name, this.search));
        },
        isExists() {
            return this.$store.state.tags.some(tag => tag.name === this.search);
        },
        canCreate() {
            return this.hasCreate && !this.isExists && !!this.search.trim();
        },
    },
    watch: {
        visible(value) {
            if (!value) {
                this.$emit('close');
            }
        },
    },
    methods: {
        select(tag) {
            this.$emit('select', tag);
            this.close();
        },
        enter() {
            if (this.filteredTags.length) {
                this.select(this.filteredTags[0]);
            } else if (this.canCreate) {
                this.create();
            }
        },
        create() {
            this.$store.dispatch('createTag', {
                name: this.search,
            }).then(tag => this.select(tag));
        },
        show() {
            this.search = '';
            this.visible = true;
        },
        close() {
            this.visible = false;
        },
    },
};
</script>
