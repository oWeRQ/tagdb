<template>
    <v-menu
        v-if="account"
        offset-y
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                text
                v-bind="attrs"
                v-on="on"
                class="ml-4 text-capitalize"
            >
                <v-icon class="mr-1">mdi-account</v-icon>
                {{ account.name }}
                <v-icon class="ml-1" size="20">mdi-chevron-down</v-icon>
            </v-btn>
        </template>

        <v-list dense>
            <v-list-item @click="logout">
                <v-list-item-icon class="mr-4">
                    <v-icon>mdi-logout</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Logout</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script>
import { mapActions, mapState } from 'vuex';

export default {
    computed: {
        ...mapState([
            'account',
        ]),
    },
    methods: {
        ...mapActions([
            'logout',
        ]),
    },
};
</script>
