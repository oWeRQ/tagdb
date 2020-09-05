<template>
    <div>
        <v-autocomplete
            v-model="value.tags"
            :rules="rules.tags"
            :items="tags"
            color="blue darken-1"
            label="Tags"
            item-text="name"
            item-value="name"
            chips
            multiple
            return-object
            hide-selected
            hide-no-data
            deletable-chips
            :autofocus="!value.tags.length"
        ></v-autocomplete>

        <v-text-field v-model="value.name" :rules="rules.name" label="Name" :autofocus="!!value.tags.length"></v-text-field>

        <template v-for="field in editedFields">
            <div :key="field.id">
                <v-menu v-if="field.type === 'date'" v-model="menu[field.id]" :close-on-content-click="false" offset-y min-width="290px">
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field v-model="value.contents[field.id]" :label="field.name" readonly clearable v-bind="attrs" v-on="on"></v-text-field>
                    </template>
                    <v-date-picker v-model="value.contents[field.id]" @input="menu[field.id] = false" first-day-of-week="1"></v-date-picker>
                </v-menu>

                <v-menu v-else-if="field.type === 'time'" v-model="menu[field.id]" :close-on-content-click="false" offset-y min-width="290px">
                    <template v-slot:activator="{ on, attrs }">
                        <v-text-field v-model="value.contents[field.id]" :label="field.name" readonly clearable v-bind="attrs" v-on="on"></v-text-field>
                    </template>
                    <v-time-picker v-model="value.contents[field.id]" @click:minute="menu[field.id] = false" format="24hr"></v-time-picker>
                </v-menu>

                <v-text-field v-else v-model="value.contents[field.id]" :type="field.type" :label="field.name"></v-text-field>
            </div>
        </template>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: Object,
            },
            editedFields: {
                type: Array,
            },
            tags: {
                type: Array,
            },
        },
        data() {
            return {
                menu: {},
            };
        },
        computed: {
            rules() {
                return {
                    tags: [
                        v => !!v.length || 'Required',
                    ],
                    name: [
                        v => !!v || 'Required',
                    ],
                };
            },
        },
    };
</script>
