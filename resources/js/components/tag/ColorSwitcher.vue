<template>
    <v-input class="mt-4">
        <v-avatar v-for="color in colors" :key="color" :color="color || 'grey'" size="24" class="mr-2" @click="$emit('update:modelValue', color)">
            <v-icon v-if="color === modelValue" dark>mdi-checkbox-marked-circle</v-icon>
        </v-avatar>
    </v-input>
</template>

<script>
    export default {
        props: {
            label: {
                type: String,
            },
            modelValue: {
                type: String,
                default: '',
            },
            colors: {
                type: Array,
                default: () => [
                    '',
                    'black',
                    'red',
                    'orange',
                    'yellow',
                    'lime',
                    'green',
                    'blue',
                    'indigo',
                    'purple',
                ],
            },
        },
    };
</script>
