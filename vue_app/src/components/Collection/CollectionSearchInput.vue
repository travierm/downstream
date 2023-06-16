<template>
  <v-text-field
    color="secondary"
    v-model="query"
    solo
    dense
    flat
    :label="labelText"
    clearable
    hide-details
    hide-selected
    light
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
      this.$store.dispatch('collection/setSearchQuery', urlSearchQuery)
    }
  },
  methods: {
    updateSearchQuery: _.debounce((self, newVal) => {
      self.$store.dispatch('collection/setSearchQuery', newVal)
    }, 100),
    handleInputClear() {
      this.updateSearchQuery(null)
    }
  },
  watch: {
    query: function (newVal) {
      this.updateSearchQuery(this, newVal)
    },
  },
}
</script>
