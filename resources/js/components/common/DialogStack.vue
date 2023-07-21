<template>
    <div>
        <component
            v-for="dialog in dialogs"
            :key="dialog.id"
            :is="dialog.component"
            ref="dialogs"
            v-bind="dialog.bind"
            v-on="dialog.on"
            @close="close(dialog)"
        ></component>
    </div>
</template>

<script>
export default {
    data() {
        return {
            id: 0,
            dialogs: [],
        };
    },
    methods: {
        show(component, bind, on) {
            return new Promise(resolve => {
                const dialog = { id: ++this.id, component, bind, on };
                this.dialogs.push(dialog);
                this.$nextTick(() => {
                    const idx = this.dialogs.indexOf(dialog);
                    const instance = this.$refs.dialogs[idx];
                    if (instance) {
                        resolve(instance.show());
                    }
                });
            });
        },
        close(dialog) {
            setTimeout(() => {
                this.dialogs = this.dialogs.filter(m => m !== dialog);
            }, 200);
        },
    },
};
</script>

