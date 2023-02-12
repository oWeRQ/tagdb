<template>
    <div>
        <v-textarea
            v-model="modelValue.name"
            :rules="rules.name"
            v-autoselect="autofocus == 'name'"
            spellcheck="false"
            label="Name"
            auto-grow
            rows="1"
            prepend-icon="mdi-table-row"
            @keydown.enter.exact.prevent="$emit('submit')"
        ></v-textarea>

        <div v-for="field in editedFields" :key="field.id">
            <v-menu v-if="field.type === 'date'" v-model="menu[field.id]" :close-on-content-click="false" offset-y min-width="290px">
                <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="modelValue.contents[field.id]"
                        v-autoselect="autofocus == field.id"
                        :label="field.name"
                        readonly
                        clearable
                        v-bind="attrs"
                        v-on="on"
                        prepend-icon="mdi-calendar"
                    ></v-text-field>
                </template>
                <v-date-picker v-model="modelValue.contents[field.id]" @input="menu[field.id] = false" first-day-of-week="1"></v-date-picker>
            </v-menu>

            <v-menu v-else-if="field.type === 'time'" v-model="menu[field.id]" :close-on-content-click="false" offset-y min-width="290px">
                <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="modelValue.contents[field.id]"
                        v-autoselect="autofocus == field.id"
                        :label="field.name"
                        readonly
                        clearable
                        v-bind="attrs"
                        v-on="on"
                        prepend-icon="mdi-clock-outline"
                    ></v-text-field>
                </template>
                <v-time-picker v-model="modelValue.contents[field.id]" @click:minute="menu[field.id] = false" format="24hr"></v-time-picker>
            </v-menu>

            <div v-else-if="field.type === 'rating'">
                <div class="text-caption ml-8">{{ field.name }}</div>
                <v-input prepend-icon="mdi-backspace-reverse-outline" @click:prepend="modelValue.contents[field.id] = 0">
                    <v-rating
                        :modelValue="+modelValue.contents[field.id]"
                        @input="modelValue.contents[field.id] = $event"
                        hover
                        half-increments
                        color="orange"
                        background-color="grey"
                        class="mt-n1"
                    ></v-rating>
                </v-input>
            </div>

            <v-textarea
                v-else-if="field.type === 'text'"
                v-model="modelValue.contents[field.id]"
                v-autoselect="autofocus == field.id"
                :type="field.type"
                :label="field.name"
                filled
                auto-grow
                prepend-icon="mdi-text-box-outline"
            ></v-textarea>

            <v-text-field
                v-else-if="field.type === 'url'"
                v-model="modelValue.contents[field.id]"
                v-autoselect="autofocus == field.id"
                :type="field.type"
                :label="field.name"
                prepend-icon="mdi-link"
                @click:prepend="open(field)"
            ></v-text-field>

            <v-text-field
                v-else
                v-model="modelValue.contents[field.id]"
                v-autoselect="autofocus == field.id"
                type="text"
                :label="field.name"
                prepend-icon="mdi-form-textbox"
            ></v-text-field>
        </div>

        <TagsField
            return-object
            v-model="modelValue.tags"
            :rules="rules.tags"
            :autofocus="autofocus == 'tags'"
            @click:tag="showTag"
            @click:plus="showTag"
            prepend-icon="mdi-tag-multiple-outline"
        ></TagsField>

        <v-btn @click="addField" :disabled="!firstSavedTag" text x-small color="blue darken-1">
            <v-icon left>mdi-plus</v-icon>
            Add Field
        </v-btn>
    </div>
</template>

<script>
    import tagsCompare from '../../functions/tagsCompare';
    import FieldDialog from '../field/FieldDialog';
    import TagDialog from '../tag/TagDialog';
    import TagsField from './TagsField';

    export default {
        components: {
            TagsField,
        },
        props: {
            modelValue: {
                type: Object,
            },
            focus: {
                type: String,
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
            sortedTags() {
                return [...this.modelValue.tags].sort(tagsCompare);
            },
            editedFields() {
                return this.sortedTags.flatMap(item => item.fields);
            },
            firstSavedTag() {
                return this.modelValue.tags.find(tag => tag.id);
            },
            autofocus() {
                return this.focus ? this.focus.replace(/^contents\./, '') : 'name';
            },
        },
        watch: {
            editedFields(fields) {
                const contents = {};
                for (const field of fields) {
                    contents[field.id] = this.modelValue.contents[field.id];
                }
                this.modelValue.contents = contents;
            },
        },
        methods: {
            showTag(tag) {
                this.originalTag = tag;
                this.$root.showDialog(TagDialog, {
                    value: tag,
                }, {
                    input: this.saveTag,
                    delete: this.deleteTag,
                });
            },
            saveTag(tag) {
                Object.assign(this.originalTag, tag);
            },
            deleteTag(tag) {
                this.modelValue.tags = this.modelValue.tags.filter(item => item.id !== tag.id);
            },
            addField() {
                this.editField({
                    tag_id: this.firstSavedTag?.id,
                    type: 'string',
                    name: '',
                    code: '',
                });
            },
            editField(field) {
                this.$root.showDialog(FieldDialog, {
                    value: field,
                }, {
                    input: this.saveField,
                });
            },
            saveField(field) {
                const tag = this.modelValue.tags.find(tag => tag.id == field.tag_id);
                if (tag) {
                    tag.fields.push(field);
                }
            },
            open(field) {
                window.open(this.modelValue.contents[field.id], '_blank');
            },
        },
    };
</script>
