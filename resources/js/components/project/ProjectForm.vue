<template>
    <div>
        <v-text-field
            v-model="value.name"
            :error-messages="errors.name"
            :rules="rules.required"
            label="Name"
            autofocus
        ></v-text-field>

        <v-text-field
            v-if="value.owner"
            v-model="value.owner.email"
            :error-messages="errors.owner"
            label="Owner"
            disabled
        ></v-text-field>

         <v-card>
            <v-list>
                <v-list-item v-for="(user, i) in value.users" :key="i">
                    <v-list-item-action>
                        <v-icon @click="remove(user)" color="grey lighten-1">mdi-close</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-row no-gutters>
                            <v-col>
                                <v-text-field v-model="user.email" :rules="rules.required" label="Email" hide-details class="mr-4"></v-text-field>
                            </v-col>
                            <!--
                            <v-col cols="3">
                                <v-select :items="roles" v-model="user.membership.role" :rules="rules.required" label="Role" hide-details></v-select>
                            </v-col>
                            -->
                        </v-row>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item @click="add">
                    <v-list-item-action>
                        <v-icon>mdi-plus</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>Add User</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-card>
        <v-alert
            v-for="error in errors.users"
            :key="error"
            type="error"
            dense
            class="mt-2"
        >
            {{ error }}
        </v-alert>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: Object,
                default: () => {},
            },
            errors: {
                type: Object,
                default: () => {},
            },
        },
        computed: {
            accountId() {
                return this.$store.state.account?.id;
            },
            roles: () => [
                { text: 'Owner', value: 'owner' },
                { text: 'Update', value: 'update' },
                { text: 'Read', value: 'read' },
            ],
            rules: () => ({
                required: [
                    v => !!v || 'Required',
                ],
            }),
        },
        methods: {
            add() {
                this.value.users.push({
                    email: '',
                    membership: {
                        role: 'update',
                    },
                });
            },
            remove(user) {
                this.value.users = this.value.users.filter(item => item !== user);
            },
        },
    }
</script>
