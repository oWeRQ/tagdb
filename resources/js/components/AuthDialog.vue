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
                                :error-messages="loginErrors.email"
                            ></v-text-field>
                            <v-text-field
                                v-model="password"
                                name="password"
                                label="Password"
                                type="password"
                                required
                                :error-messages="loginErrors.password"
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
                                v-model="name"
                                name="name"
                                label="Name"
                                required
                                :error-messages="registerErrors.name"
                            ></v-text-field>
                            <v-text-field
                                v-model="email"
                                name="email"
                                label="Email"
                                required
                                :error-messages="registerErrors.email"
                            ></v-text-field>
                            <v-text-field
                                v-model="password"
                                name="password"
                                label="Password"
                                type="password"
                                required
                                :error-messages="registerErrors.password"
                            ></v-text-field>
                            <v-text-field
                                v-model="password_confirmation"
                                name="password"
                                label="Confirmation"
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
    import axios from 'axios';

    export default {
        props: {
            visible: Boolean,
        },
        data() {
            return {
                tab: null,
                name: null,
                email: null,
                password: null,
                password_confirmation: null,
                loginErrors: {},
                registerErrors: {},
            };
        },
        methods: {
            submitLogin() {
                axios.post('/login', {
                    email: this.email,
                    password: this.password,
                }, {
                    headers: {'Accept': 'application/json'},
                }).then(() => {
                    this.$emit('success');
                }).catch((error) => {
                    this.loginErrors = error.response.data.errors || {};
                });
            },
            submitRegister() {
                axios.post('/register', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                }, {
                    headers: {'Accept': 'application/json'},
                }).then(() => {
                    this.$emit('success');
                }).catch((error) => {
                    this.registerErrors = error.response.data.errors || {};
                });
            },
        },
    };
</script>
