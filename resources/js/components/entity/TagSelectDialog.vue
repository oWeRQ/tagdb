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
            <v-card-text class="pa-0 subtitle-1">
                <v-list>
                    <v-list-item
                        v-for="item in filteredTags"
                        :key="item.id"
                        @click="select(item)"
                    >
                        <v-icon @click="keepActive" left :class="iconColor(item)">
                            mdi-tag-plus-outline
                        </v-icon>
                        <span :class="textColor(item)">{{ item.name }}</span>
                        <span v-if="item.entities_count" class="caption grey--text text--lighten-1 ml-2">({{ item.entities_count }})</span>
                    </v-list-item>
                    <v-list-item v-if="canCreate" @click="create">
                        <v-icon @click="keepActive" left>mdi-plus</v-icon>
                        Create "{{ this.search }}" tag
                    </v-list-item>
                    <v-list-item v-else-if="!filteredTags.length">
                        Not found
                    </v-list-item>
                </v-list>
            </v-card-text>
            <v-divider></v-divider>
            <v-chip-group v-if="isSelected" column class="mx-2">
                <v-chip
                    v-for="tag in selected"
                    :key="tag.name"
                    closable
                    @click:close="removeSelected(tag)"
                    class="lighten-2"
                    :color="tag.color"
                    :dark="!!tag.color"
                >{{ tag.name }}</v-chip>
            </v-chip-group>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                <v-btn v-if="isSelected" color="blue darken-1" text @click="confirm">Confirm</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import fuzzyFilter from '../../functions/fuzzyFilter';

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
            selected: [],
            isKeepActive: false,
        };
    },
    computed: {
        allTags() {
            return this.$store.state.project.tags;
        },
        isSelected() {
            return Boolean(this.selected.length);
        },
        unselectedTags() {
            return this.tags.filter(tag => !this.selected.includes(tag));
        },
        filteredTags() {
            return fuzzyFilter(this.search, this.unselectedTags, 'name');
        },
        isExists() {
            return this.allTags.some(tag => tag.name === this.search);
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
        iconColor(item) {
            const prefix = (item.color || 'grey') + '--text text--';
            if (item.color) {
                return prefix + (item.entities_count ? 'lighten-1' : 'lighten-3');
            } else {
                return prefix + (item.entities_count ? 'darken-1' : 'lighten-1');
            }
        },
        textColor(item) {
            const prefix = (item.color || 'grey') + '--text text--';
            if (item.color) {
                return prefix + (item.entities_count ? 'darken-2' : 'lighten-2');
            } else {
                return prefix + (item.entities_count ? 'darken-3' : 'lighten-0');
            }
        },
        removeSelected(tag) {
            this.selected = this.selected.filter(t => t !== tag);
        },
        keepActive() {
            this.isKeepActive = true;
        },
        select(tag) {
            if (this.isKeepActive || this.isSelected) {
                this.selected.push(tag);
                this.isKeepActive = false;
            } else {
                this.$emit('select', [tag]);
                this.close();
            }
        },
        confirm() {
            this.$emit('select', this.selected);
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
