<template>
    <v-dialog v-model="visible" :max-width="maxWidth">
        <v-card>
            <v-card-title v-if="title">
                {{ title }}
            </v-card-title>
            <v-card-text v-if="text">
                {{ text }}
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                <v-btn color="blue darken-1" text @click="confirm">OK</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    export default {
        props: {
            maxWidth: {
                type: String,
                default: '400px',
            },
            title: {
                type: String,
            },
            text: {
                type: String,
            },
        },
        data() {
            return {
                visible: false,
            };
        },
        methods: {
            show() {
                this.visible = true;

                return new Promise((resolve) => {
                    this.resolve = resolve;
                });
            },
            close() {
                this.visible = false;
            },
            confirm() {
                this.resolve();
                this.close();
            },
        },
    }
</script>
