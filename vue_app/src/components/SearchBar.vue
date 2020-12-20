<template>
    <v-autocomplete 
        class="main-search" 
        solo 
        dense 
        flat
        item-text="item-text"
        item-value="item-text"
        :items="autocompleteData"
        @keydown.enter="dispatchSearchRoute()"
    >

    </v-autocomplete>
</template>

<script>
import { mapState } from 'vuex';

export default {
  name: 'SearchBar',
  components: {},
  computed: {
    ...mapState({
      autocompleteData: state => state.search.autocomplete
    })
  },
  data: () => {
    return {}
  },
  mounted() {
  },
  methods:{
      dispatchSearchRoute() {
          this.$router.push('/search')
      }
  }
}
</script>

<style scoped>

</style>