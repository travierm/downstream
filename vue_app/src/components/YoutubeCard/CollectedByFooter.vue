<template>
    <v-card-actions>
          <v-list-item class="grow">
            <v-list-item-content>
              <v-list-item-title>{{ collectedBy }}</v-list-item-title>
            </v-list-item-content>

            <v-row
              align="center"
              justify="end"
            >
              <span class="subheading">{{ collectedAt }}</span>
            </v-row>
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
