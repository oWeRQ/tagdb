<template>
    <v-menu
        v-if="account"
        offset-y
    >
        <template v-slot:activator="{ props }">
            <v-btn
                text
                v-bind="props"
                class="ml-4 text-capitalize"
            >
                <v-icon class="mr-1">mdi-account</v-icon>
                {{ account.name }}
                <v-icon class="ml-1" size="20">mdi-chevron-down</v-icon>
            </v-btn>
        </template>

        <v-list dense>
            <v-list-item @click="logout">
                <template v-slot:prepend>
                    <v-icon>mdi-logout</v-icon>
                </template>
                <v-list-item-title>Logout</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script>
import { mapActions, mapState } from 'vuex';

export default {
    computed: {
        ...mapState({
            account: state => state.auth.account,
        }),
    },
    methods: {
        ...mapActions([
            'logout',
        ]),
    },
};
</script>
