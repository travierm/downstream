<template>
  <v-text-field
    color="secondary"
    v-model="query"
    variant="solo"
    density="compact"
    flat
    :label="labelText"
    clearable
    hide-details
    hide-selected
    theme="light"
    @click:clear="handleInputClear"
  ></v-text-field>
</template>

<script>
import { getUrlParam } from '@/services/GlobalFunctions'
import _ from 'lodash'
import { mapGetters } from 'vuex'

export default {
  name: 'CollectionSearchInput',
  props: ['collectionCount'],
  computed: {
    ...mapGetters({
      collection: 'collection/collectionSearchResults',
    }),
    labelText() {
      return `Search ${this.collectionCount} items in your collection`
    },
  },
  data: () => {
    return {
      items: [],
      query: '',
      value: '',
    }
  },
  mounted() {
    const urlSearchQuery = getUrlParam('search')

    if(urlSearchQuery) {
      this.query = urlSearchQuery
      this.$store.dispatch('collection/setSearchQuery', urlSearchQuery)
    }
  },
  beforeUnmount() {
    this.updateSearchQuery.cancel()
  },
  methods: {
    updateSearchQuery: _.debounce((self, newVal) => {
      self.$store.dispatch('collection/setSearchQuery', newVal)
    }, 100),
    handleInputClear() {
      this.query = ''
    }
  },
  watch: {
    query: function (newVal) {
      this.updateSearchQuery(this, newVal)
    },
  },
}
</script>
