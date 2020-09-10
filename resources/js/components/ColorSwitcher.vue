<template>
    <v-input class="mt-4">
        <v-avatar v-for="color in colors" :key="color" :color="color" size="24" class="mr-2" @click="$emit('input', color)">
            <v-icon v-if="color === value" dark>mdi-checkbox-marked-circle</v-icon>
        </v-avatar>
    </v-input>
</template>

<script>
    export default {
        props: {
            label: {
                type: String,
            },
            value: {
                type: String,
                default: 'black',
            },
            colors: {
                type: Array,
                default: () => [
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
