<template>
  <v-dialog v-model="showDialog" persistent max-width="400">
    <v-card>
      <v-card-title class="headline">
        Mood List
      </v-card-title>
      <v-card-text></v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn id="confirmDialogYesBtn" color="green" @click="handleConfirm">
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
  name: 'MoodListDialog',
  props: {},
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
    openDialog() {
      this.showDialog = true
    },
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
