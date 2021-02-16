<template>
  <v-dialog v-model="showDialog" persistent max-width="400">
    <v-card>
      <v-card-title class="headline">
        Mood Lists
      </v-card-title>

      <v-card-text>
        <MoodList />
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn @click="handleClose">
          Close
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import MoodList from './MoodList'

export default {
  name: 'MoodListDialog',
  props: {},
  components: {
    MoodList
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
