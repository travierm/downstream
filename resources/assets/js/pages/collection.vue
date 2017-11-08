<template>
  <div class="container-fluid pushFromTop">
    <div class="row">
        <div class="col-lg-3">
          <h1>Here 6</h1>
        </div>
    </div>
  </div>
</template>

<script>
  export default {
    data:() => {
      return {
        collection:[]
      }
    },
    mounted() {
      this.$store.dispatch('fetchCollection');
    }
  }
</script>

<style>
</style>
