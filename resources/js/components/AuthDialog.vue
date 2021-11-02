<template>
    <v-dialog :value="visible" persistent max-width="400px">
        <v-card>
            <v-tabs v-model="tab" grow background-color="blue lighten-5">
                <v-tab>Login</v-tab>
                <v-tab>Register</v-tab>
            </v-tabs>
            <v-tabs-items v-model="tab">
                <v-tab-item>
                    <v-form @submit.prevent="submitLogin">
                        <v-card-text>
                            <v-text-field
                                v-model="email"
                                name="email"
                                label="Email"
                                required
                            ></v-text-field>
                            <v-text-field
                                v-model="password"
                                name="password"
                                label="Password"
                                type="password"
                                required
                            ></v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text type="submit">Login</v-btn>
                        </v-card-actions>
                    </v-form>
                </v-tab-item>
                <v-tab-item>
                    <v-form @submit.prevent="submitRegister">
                        <v-card-text>
                            <v-text-field
                                v-model="email"
                                name="email"
                                label="Email"
                                required
                            ></v-text-field>
                            <v-text-field
                                v-model="password"
                                name="email"
                                label="Password"
                                type="password"
                                required
                            ></v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue darken-1" text type="submit">Register</v-btn>
                        </v-card-actions>
                    </v-form>
                </v-tab-item>
            </v-tabs-items>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        props: {
            visible: Boolean,
        },
        data() {
            return {
                tab: null,
                email: null,
                password: null,
            };
        },
        methods: {
            submitLogin() {
                this.$emit('success');
            },
            submitRegister() {
                this.$emit('success');
            },
        },
    };
</script>
