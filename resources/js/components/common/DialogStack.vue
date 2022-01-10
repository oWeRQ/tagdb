<template>
    <div>
        <component
            v-for="dialog in dialogs"
            :key="dialog.id"
            :is="dialog.component"
            ref="dialogs"
            v-bind="dialog.bind"
            v-on="dialog.on"
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
                    instance.$watch('visible', (visible) => {
                        if (!visible) {
                            setTimeout(() => {
                                this.dialogs = this.dialogs.filter(m => m !== dialog);
                            }, 1000);
                        }
                    });
                    resolve(instance.show());
                });
            });
        },
    },
};
</script>
