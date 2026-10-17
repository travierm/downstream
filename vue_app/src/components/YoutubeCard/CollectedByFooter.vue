<template>
  <v-card-actions>
    <v-list-item class="flex-grow-1">
      <v-list-item-title>{{ collectedBy }}</v-list-item-title>
      <template #append>
        <span class="text-subtitle-1">{{ collectedAt }}</span>
      </template>
    </v-list-item>
  </v-card-actions>
</template>

<script>
import { mdiPlaylistMusic } from '@mdi/js'

export default {
    name: 'CollectedByFooter',
    components: {
    },
    props: {
        collectedAt: String,
        collectedBy: String,
    },
    data() {
        return {
            mdiPlaylistMusic,
        }
    },
    methods: {
        toggleDialog() {
            // this.$store.dispatch('playlist/getAll')
            this.$refs.playlistDialog.openDialog()
        }
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
