<template>
    <v-list nav dense>
        <v-list-item link to="/">
            <template v-slot:prepend>
                <v-icon>mdi-home</v-icon>
            </template>
            <v-list-item-title>Entities</v-list-item-title>
        </v-list-item>
        <v-list-item link to="/tags">
            <template v-slot:prepend>
                <v-icon>mdi-tag</v-icon>
            </template>
            <v-list-item-title>Tags</v-list-item-title>
        </v-list-item>
        <v-list-item link to="/fields">
            <template v-slot:prepend>
                <v-icon>mdi-pencil-box-multiple</v-icon>
            </template>
            <v-list-item-title>Fields</v-list-item-title>
        </v-list-item>
        <v-list-item link to="/tokens">
            <template v-slot:prepend>
                <v-icon>mdi-folder-key</v-icon>
            </template>
            <v-list-item-title>Tokens</v-list-item-title>
        </v-list-item>

        <template v-if="presets.length">
            <v-list-subheader>Presets</v-list-subheader>
            <v-list-item v-for="preset in presets" :key="preset.id" link :to="{ name: 'preset', params: { name: preset.name }}">
                <template v-slot:prepend>
                    <v-icon>mdi-database</v-icon>
                </template>
                <v-list-item-title>{{ preset.name }}</v-list-item-title>
            </v-list-item>
        </template>
    </v-list>
</template>

<script>
import { mapState } from 'vuex';

export default {
    computed: {
        ...mapState({
            presets: state => state.project.presets,
        }),
    },
};
</script>
