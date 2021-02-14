<template>
  <v-text-field
    color="secondary"
    v-model="query"
    solo
    dense
    flat
    label="Search Collection...."
    clearable
    hide-details
    hide-selected
    light
  ></v-text-field>
</template>

<script>
import { mapGetters } from "vuex";
import YoutubePlayerManager from "../../services/YoutubePlayerManager";

export default {
  name: "CollectionSearchInput",
  computed: {
    ...mapGetters({
      collection: "collection/collectionSearchResults",
    }),
  },
  data: () => {
    return {
      items: [],
      query: "",
      value: "",
    };
  },
  mounted() {},
  methods: {},
  watch: {
    query: function(val) {
      this.$store.commit("collection/SET_SEARCH_QUERY", val);

      // Stop playing current card so it doesn't get lost when the index is updated
      YoutubePlayerManager.stopPlayingCard();

      let indexes = _.map(this.collection, "guid");
      this.$store.dispatch("player/setGuidIndex", indexes);
    },
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
