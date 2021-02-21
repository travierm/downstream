<template>
  <v-dialog v-model="showDialog" persistent max-width="400">
    <v-card>
      <v-card-title class="headline">
        Confirm
      </v-card-title>
      <v-card-text
        ><h3>{{ message }}</h3></v-card-text
      >
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn id="confirmDialogYesBtn" color="red" @click="handleConfirm">
          Yes
        </v-btn>
        <v-btn @click="handleClose">
          No
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'ConfimDialog',
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
      this.$emit('confirmed', true)
    },
    handleClose() {
      this.showDialog = false
      this.$emit('closed', true)
    },
  },
}
</script>
