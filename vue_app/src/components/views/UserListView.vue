<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12">
        <h1>Top Users</h1>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="4" v-for="user in users" :key="user.id">
        <v-card>
          <v-card-title>{{ user.display_name }}</v-card-title>
          <v-card-text>
            <p>Collection Size: {{ user.media_count }}</p>
          </v-card-text>
          <v-card-actions>
            <v-btn text color="primary" :to="'/profile/' + user.hash">
              View Profile
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import BottomBar from '@/components/BottomBar'
import FollowerService from '@/services/api/FollowerService'

export default {
  name: 'UserListView',
  components: {
    BottomBar,
  },
  data: () => ({
    users: [],
  }),
  computed: {},
  methods: {},
  mounted() {
    FollowerService.getActiveUsers().then((response) => {
      this.users = response.data
    })
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
