<template>
  <v-btn
    icon
    color="green accent-3"
    class="collectBtn"
    v-if="!inCollection"
    @click="collectItem"
  >
    <v-icon class="">{{ mdiMusicNotePlus }}</v-icon>
  </v-btn>

  <v-btn icon class="removeBtn" v-else @click="showRemoveConfirmDialog">
    <v-icon color="red">{{ mdiMinusCircle }}</v-icon>
    <ConfirmDialog
      :show="showConfirmDialog"
      :message="removeConfirmMessage"
      v-on:closed="showConfirmDialog = false"
      v-on:confirmed="removeItem"
    />
  </v-btn>
</template>

<script>
import ConfirmDialog from '../Shared/ConfirmDialog'
import { mdiMusicNotePlus, mdiMinusCircle } from '@mdi/js'
import YouTubePlayerManager from '../../services/YoutubePlayerManager'

const removeConfirmMessage =
  'Are you sure you want to remove this item from your collection?'

export default {
  name: 'CollectAction',
  components: {
    ConfirmDialog,
  },
  props: {
    mediaId: {
      type: Number,
      default: 0,
      required: false,
    },
    videoId: {
      type: String,
      required: true,
    },
    collected: {
      type: Boolean,
      default: false,
      required: true,
    },
  },
  data() {
    return {
      mdiMinusCircle,
      mdiMusicNotePlus,
      removeConfirmMessage,
      inCollection: this.collected,
      showConfirmDialog: false,
    }
  },
  methods: {
    collectItem() {
      this.$store
        .dispatch('collection/collectItem', this.videoId)
        .then(() => {
          this.$store.dispatch('collection/fetchCollection')
          this.inCollection = true
        })
        .catch(() => {
          this.inCollection = false
        })
    },
    removeItem() {
      this.$store
        .dispatch('collection/removeItem', this.mediaId)
        .then(() => {
          YouTubePlayerManager.removeCard(this.guid)
          this.inCollection = false
        })
        .catch(() => {
          this.inCollection = true
        })
    },
    showRemoveConfirmDialog() {
      this.showConfirmDialog = true
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
