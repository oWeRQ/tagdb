<template>
    <v-list nav dense>
        <v-list-item-group color="primary">
            <v-list-item link to="/">
                <v-list-item-action>
                    <v-icon>mdi-home</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>Entities</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/tags">
                <v-list-item-action>
                    <v-icon>mdi-tag</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>Tags</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/fields">
                <v-list-item-action>
                    <v-icon>mdi-pencil-box-multiple</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>Fields</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list-item-group>
        <v-list-item-group color="primary" v-if="presets.length">
            <v-subheader>Presets</v-subheader>
            <v-list-item v-for="preset in presets" :key="preset.id" link :to="{ name: 'preset', params: { name: preset.name }}">
                <v-list-item-action>
                    <v-icon>mdi-database</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{ preset.name }}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list-item-group>
    </v-list>
</template>

<script>
import { mapState } from 'vuex';

export default {
    computed: {
        ...mapState([
            'presets',
        ]),
    },
};
</script>
