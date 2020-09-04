<template>
    <div>
        <v-text-field v-model="value.name" label="Tag" autofocus></v-text-field>

        <v-card>
            <v-list>
                <v-list-item v-for="(field, i) in value.fields" :key="'item' + i">
                    <v-list-item-action @click="remove(field)">
                        <v-icon>mdi-close</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-row no-gutters>
                            <v-col>
                                <v-select :items="types" v-model="field.type" label="Type" hide-details class="mr-4"></v-select>
                            </v-col>
                            <v-col>
                                <v-text-field v-model="field.name" label="Name" hide-details class="mr-4"></v-text-field>
                            </v-col>
                            <v-col>
                                <v-text-field v-model="field.code" label="Code" hide-details></v-text-field>
                            </v-col>
                        </v-row>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item @click="add">
                    <v-list-item-action>
                        <v-icon>mdi-plus</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>Add</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-card>
    </div>
</template>

<script>
    export default {
        props: {
            editable: {
                type: Array,
            },
            value: {
                type: Object,
            },
        },
        computed: {
            types: () => [
                { text: 'String', value: 'string' },
                { text: 'Text', value: 'text' },
                { text: 'Date', value: 'date' },
                { text: 'Time', value: 'time' },
                { text: 'DateTime', value: 'datetiem' },
            ],
        },
        methods: {
            add() {
                console.log(this.value);

                this.value.fields.push({
                    type: 'string',
                    name: '',
                    code: '',
                });
            },
            remove(field) {
                this.value.fields = this.value.fields.filter(item => item !== field);
            },
        },
    }
</script>
