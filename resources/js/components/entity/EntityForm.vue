<template>
    <div>
        <TagsField
            return-object
            v-model="value.tags"
            :rules="rules.tags"
            :autofocus="autofocus == 'tags'"
            @click:tag="showTag($event)"
            prepend-icon="mdi-tag-multiple-outline"
        ></TagsField>

        <v-textarea
            v-model="value.name"
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
                        v-model="value.contents[field.id]"
                        v-autoselect="autofocus == field.id"
                        :label="field.name"
                        readonly
                        clearable
                        v-bind="attrs"
                        v-on="on"
                        prepend-icon="mdi-calendar"
                    ></v-text-field>
                </template>
                <v-date-picker v-model="value.contents[field.id]" @input="menu[field.id] = false" first-day-of-week="1"></v-date-picker>
            </v-menu>

            <v-menu v-else-if="field.type === 'time'" v-model="menu[field.id]" :close-on-content-click="false" offset-y min-width="290px">
                <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="value.contents[field.id]"
                        v-autoselect="autofocus == field.id"
                        :label="field.name"
                        readonly
                        clearable
                        v-bind="attrs"
                        v-on="on"
                        prepend-icon="mdi-clock-outline"
                    ></v-text-field>
                </template>
                <v-time-picker v-model="value.contents[field.id]" @click:minute="menu[field.id] = false" format="24hr"></v-time-picker>
            </v-menu>

            <div v-else-if="field.type === 'rating'">
                <div class="text-caption ml-8">{{ field.name }}</div>
                <v-input prepend-icon="mdi-backspace-reverse-outline" @click:prepend="value.contents[field.id] = 0">
                    <v-rating
                        :value="+value.contents[field.id]"
                        @input="value.contents[field.id] = $event"
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
                v-model="value.contents[field.id]"
                v-autoselect="autofocus == field.id"
                :type="field.type"
                :label="field.name"
                filled
                auto-grow
            ></v-textarea>

            <v-text-field
                v-else-if="field.type === 'url'"
                v-model="value.contents[field.id]"
                v-autoselect="autofocus == field.id"
                :type="field.type"
                :label="field.name"
                prepend-icon="mdi-link"
                @click:prepend="open(field)"
            ></v-text-field>

            <v-text-field
                v-else
                v-model="value.contents[field.id]"
                v-autoselect="autofocus == field.id"
                type="text"
                :label="field.name"
                prepend-icon="mdi-form-textbox"
            ></v-text-field>
        </div>

        <v-btn @click="addField" :disabled="!firstSavedTag" text x-small color="blue darken-1">
            <v-icon left>mdi-plus</v-icon>
            Add Field
        </v-btn>
    </div>
</template>

<script>
    import FieldDialog from '../field/FieldDialog';
    import TagDialog from '../tag/TagDialog';
    import TagsField from './TagsField';

    export default {
        components: {
            TagsField,
        },
        props: {
            value: {
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
            editedFields() {
                return this.value.tags.flatMap(item => item.fields);
            },
            firstSavedTag() {
                return this.value.tags.find(tag => tag.id);
            },
            autofocus() {
                return this.focus ? this.focus.replace(/^contents\./, '') : (this.value.tags.length ? 'name' : 'tags');
            },
        },
        watch: {
            editedFields(fields) {
                const contents = {};
                for (const field of fields) {
                    contents[field.id] = this.value.contents[field.id];
                }
                this.value.contents = contents;
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
                this.value.tags = this.value.tags.filter(item => item.id !== tag.id);
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
                const tag = this.value.tags.find(tag => tag.id == field.tag_id);
                if (tag) {
                    tag.fields.push(field);
                }
            },
            open(field) {
                window.open(this.value.contents[field.id], '_blank');
            },
        },
    };
</script>
