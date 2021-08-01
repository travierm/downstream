<template>
  <v-tooltip top>
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        v-on="on"
        v-bind="attrs"
        icon
        color="green accent-3"
        @click="pushItem"
      >
        <v-icon>{{ mdiArrowUpThick }}</v-icon>
      </v-btn>
    </template>
    <span>Push an item to the top of your collection</span>
  </v-tooltip>
</template>

<script>
import { mdiArrowUpThick } from '@mdi/js'
import CollectionService from '../../services/api/CollectionService'

export default {
  name: 'PushAction',
  props: {
    mediaId: {
      type: Number,
      default: 0,
      required: false,
    },
  },
  data() {
    return {
      mdiArrowUpThick,
    }
  },
  methods: {
    pushItem() {
      CollectionService.pushItem(this.mediaId).catch(() => {
        dd('Failed to push item', this.mediaId)
      })
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
