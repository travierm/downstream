<template>
  <div>
    <v-list flat>
      <v-list-item-group dark color="white" :ripple="false">
        <v-list-item
          exact-active-class="no-active"
          v-for="(item, i) in lists"
          :key="i"
          @click="clickedListItem(item)"
        >
          <v-list-item-icon>
            <v-icon>{{
              item.itemAdded ? mdiCheckboxIntermediate : mdiCheckboxBlankOutline
            }}</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title v-text="item.name"></v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>

    <v-divider dark></v-divider>
    
    <div class="ml-4 mr-4 mt-4">
      <v-text-field
        solo
        light
        dense
        v-model="listNameInput"
        label="Create new Mood"
        required
        clearable
        hide-details
      ></v-text-field>

      <v-btn @click='createList' class="mt-2" color="primary">Create</v-btn>
    </div>
  </div>
</template>

<script>
import _ from 'lodash'
import { mdiCheckboxIntermediate, mdiCheckboxBlankOutline } from '@mdi/js'

const lists = []

lists.push({
  name: 'WRX Chill',
  itemAdded: false,
})

lists.push({
  name: 'EDM',
  itemAdded: true,
})

lists.push({
  name: 'Lil Peep',
  itemAdded: false,
})

export default {
  name: 'MoodList',
  components: {},
  data: () => {
    return {
      listNameInput: '',
      mdiCheckboxIntermediate,
      mdiCheckboxBlankOutline,
      lists,
    }
  },
  mounted() {},
  methods: {
    createList() {
      if(this.listNameInput == '') {
        return
      }

      const existingItem = _.find(this.lists, { name: this.listNameInput})
      if(existingItem) {
        return
      }

      this.lists.push({
        name: this.listNameInput,
        itemAdded: false
      })

      this.listNameInput = ''
    },
    clickedListItem(item) {
      item.itemAdded = !item.itemAdded
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
no-active::before {
  opacity: 0 !important;
}
</style>
