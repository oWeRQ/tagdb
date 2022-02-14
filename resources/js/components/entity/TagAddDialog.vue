<template>
    <v-dialog v-model="visible" max-width="320px" scrollable>
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
                        @click="select(item)"
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
                <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
        },
        tags: {
            type: Array,
        },
    },
    data() {
        return {
            visible: false,
        };
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
        show() {
            this.visible = true;
        },
        close() {
            this.visible = false;
        },
    },
};
</script>
