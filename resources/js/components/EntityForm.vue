<template>
    <div>
        <CrudDialog
            ref="tagDialog"
            title="Tag"
            :form="tagForm"
            :resource="tagResource"
            :value="editedTag"
            @input="saveTag"
            @delete="deleteTag"
        ></CrudDialog>

        <TagsField
            return-object
            v-model="value.tags"
            :rules="rules.tags"
            :autofocus="!value.tags.length"
            @click:tag="showTag($event)"
        ></TagsField>

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

                <template v-else-if="field.type === 'rating'">
                    <div class="text-caption">{{ field.name }}</div>
                    <v-rating
                        :value="+value.contents[field.id]"
                        @input="value.contents[field.id] = $event"
                        hover
                        half-increments
                        color="orange"
                        background-color="grey lighten-1"
                    ></v-rating>
                </template>

                <v-textarea v-else-if="field.type === 'text'" v-model="value.contents[field.id]" :type="field.type" :label="field.name" filled></v-textarea>

                <v-text-field v-else v-model="value.contents[field.id]" :type="field.type" :label="field.name"></v-text-field>
            </div>
        </template>

        <v-btn @click="addField" :disabled="!firstSavedTag" text x-small color="blue darken-1" class="mt-4 ml-n1">
            <v-icon left>mdi-plus</v-icon>
            Add Field
        </v-btn>

        <CrudDialog
            ref="fieldDialog"
            title="Field"
            :form="fieldForm"
            :resource="fieldResource"
            :value="editedField"
            @input="saveField"
        ></CrudDialog>
    </div>
</template>

<script>
    import cloneDeep from 'clone-deep';
    import CrudDialog from './CrudDialog';
    import TagForm from './TagForm';
    import FieldForm from './FieldForm';
    import TagsField from './TagsField';

    export default {
        components: {
            CrudDialog,
            TagsField,
        },
        props: {
            value: {
                type: Object,
            },
        },
        data() {
            return {
                menu: {},
                editedTag: null,
                tagForm: TagForm,
                tagResource: '/api/v1/tags',
                editedField: null,
                fieldForm: FieldForm,
                fieldResource: '/api/v1/fields',
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
            editedFields() {
                return this.value.tags.flatMap(item => item.fields);
            },
            firstSavedTag() {
                return this.value.tags.find(tag => tag.id);
            },
        },
        methods: {
            showTag(tag) {
                this.originalTag = tag;
                this.editedTag = cloneDeep(tag);
                this.$refs.tagDialog.show();
            },
            saveTag(tag) {
                Object.assign(this.originalTag, tag);
            },
            deleteTag(tag) {
                this.value.tags = this.value.tags.filter(item => item.id !== tag.id);
            },
            addField() {
                this.editedField = {tag_id: this.firstSavedTag?.id, type: 'string'};
                this.$refs.fieldDialog.show();
            },
            saveField(field) {
                const tag = this.value.tags.find(tag => tag.id == field.tag_id);
                if (tag) {
                    tag.fields.push(field);
                }
            },
        },
    };
</script>
