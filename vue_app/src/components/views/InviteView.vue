<template>
  <v-container>
    <h1>Invite Friends</h1>
    <p>Anyone can join Downstream. Share this registration link with your friends.</p>
    <a :href="link">{{ link }}</a>
  </v-container>
</template>

<script>
export default {
  name: 'InviteView',
  computed: {
    link() {
      return new URL(this.$router.resolve('/register').href, window.location.origin).href
    },
  },
}
</script>
