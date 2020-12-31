<template>
    <v-dialog v-model="showDialog" persistent max-width="290">
        <v-card>
            <v-card-title class="headline">
                Confirm
            </v-card-title>
            <v-card-text>{{ message }}</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="green darken-1" text @click="handleClose">
                    Close
                </v-btn>
                <v-btn color="green darken-1" text @click="handleConfirm">
                    Confirm
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "ConfimDialog",
    props: {
        show: {
            type: Boolean,
            default: false,
            required: true,
        },
        message: {
            type: String,
            required: true,
        },
    },
    data: function() {
        return {
            showDialog: this.show,
        }
    },
    watch: {
        show(propVal) {
            this.showDialog = propVal
        },
    },
    methods: {
        handleConfirm() {
            this.showDialog = false
            this.$emit("confirmed", true)
        },
        handleClose() {
            this.showDialog = false
        },
    },
}
</script>
