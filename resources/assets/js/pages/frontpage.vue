<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
    </div>
  </div>
</template>

<script>
  import { mapGetters, mapActions } from 'vuex'

  export default {
    mounted() {
      this.$store.dispatch('frontpage/fetch');
    },
    computed: mapGetters([
      'collection'
    ])
  }
</script>

<style>
</style>
