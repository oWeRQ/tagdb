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
                                :type="passwordType"
                                required
                                :error-messages="loginErrors.password"
                                :append-icon="passwordIcon"
                                @click:append="togglePassword"
                            ></v-text-field>
                            <v-checkbox
                                v-model="remember"
                                name="remember"
                                label="Remember"
                                class="mt-0"
                                hide-details
                            ></v-checkbox>
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
                                :type="passwordType"
                                required
                                :error-messages="registerErrors.password"
                                :append-icon="passwordIcon"
                                @click:append="togglePassword"
                            ></v-text-field>
                            <v-text-field
                                v-model="password_confirmation"
                                name="password"
                                label="Confirmation"
                                :type="passwordType"
                                required
                                :error-messages="registerErrors.password_confirmation"
                                :append-icon="passwordIcon"
                                @click:append="togglePassword"
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
        data() {
            return {
                tab: null,
                name: null,
                email: null,
                password: null,
                password_confirmation: null,
                show_password: false,
                remember: null,
                loginErrors: {},
                registerErrors: {},
            };
        },
        computed: {
            visible() {
                return this.$store.state.isAuth;
            },
            passwordType() {
                return (this.show_password ? 'text' : 'password');
            },
            passwordIcon() {
                return (this.show_password ? 'mdi-eye-off' : 'mdi-eye');
            },
        },
        methods: {
            submitLogin() {
                this.$store.dispatch('login', {
                    email: this.email,
                    password: this.password,
                    remember: this.remember,
                }).catch((error) => {
                    this.loginErrors = error.response.data.errors || {};
                });
            },
            submitRegister() {
                this.$store.dispatch('register', {
                    name: this.name,
                    email: this.email,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                }).catch((error) => {
                    this.registerErrors = error.response.data.errors || {};
                });
            },
            togglePassword() {
                this.show_password = !this.show_password;
            },
        },
    };
</script>
