<template>
    <div>
        <v-text-field v-model="modelValue.name" :rules="rules.required" label="Tag" autofocus></v-text-field>

        <ColorSwitcher v-model="modelValue.color" label="Color"></ColorSwitcher>

        <v-card>
            <v-list>
                <v-list-item v-for="(field, i) in modelValue.fields" :key="'item' + i">
                    <template v-slot:prepend>
                        <v-icon @click="remove(field)" color="grey-lighten-1">mdi-close</v-icon>
                    </template>
                    <v-row no-gutters>
                        <v-col>
                            <v-text-field v-model="field.name" @change="nameChanged(field)" :rules="rules.required" label="Name" hide-details class="mr-4"></v-text-field>
                        </v-col>
                        <v-col>
                            <v-text-field v-model="field.code" @change="nameChanged(field)" :rules="rules.required" label="Code" hide-details class="mr-4"></v-text-field>
                        </v-col>
                        <v-col>
                            <v-select :items="types" v-model="field.type" :rules="rules.required" label="Type" hide-details></v-select>
                        </v-col>
                    </v-row>
                </v-list-item>

                <v-list-item @click="add">
                    <template v-slot:prepend>
                        <v-icon>mdi-plus</v-icon>
                    </template>
                    <v-list-item-title>Add Field</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-card>
    </div>
</template>

<script>
    import makeCode from '../../functions/makeCode';
    import ColorSwitcher from './ColorSwitcher.vue';

    export default {
        components: {
            ColorSwitcher,
        },
        props: {
            modelValue: {
                type: Object,
            },
        },
        computed: {
            types: () => [
                { title: 'String', value: 'string' },
                { title: 'Text', value: 'text' },
                { title: 'Url', value: 'url' },
                { title: 'Email', value: 'email' },
                { title: 'Color', value: 'color' },
                { title: 'Date', value: 'date' },
                { title: 'Time', value: 'time' },
                { title: 'Rating', value: 'rating' },
            ],
            rules() {
                return ({
                    required: [
                        v => !!v || 'Required',
                    ],
                });
            },
        },
        methods: {
            add() {
                this.modelValue.fields.push({
                    type: 'string',
                    name: '',
                    code: '',
                });
            },
            remove(field) {
                this.modelValue.fields = this.modelValue.fields.filter(item => item !== field);
            },
            nameChanged(field) {
                if (!field.code) {
                    field.code = makeCode(field.name);
                }
            },
        },
    }
</script>
