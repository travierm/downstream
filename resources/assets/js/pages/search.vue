<template>
  <div class="container-fluid pushFromTop">

    <div class="row" v-if="!isMobile">
      <div class="col-lg-2">
        <master-bar></master-bar>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <img height="125" width="125" class="img-fluid" src="images/yt_logo_rgb_light.png"></img>
      </div>
    </div>

    <div class="row pushFromTop">
      <div class="col-lg-2">
        <div class="input-group">
          <input type="text" class="form-control" placeholder="Search for...">
          <span class="input-group-btn">
            <button class="btn btn-outline-danger" type="button">Query</button>
          </span>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
  export default {
    mounted() {
    },
    computed: {
      isMobile() {
        return window._isMobile;
      },
      videos() {
        return this.$store.getters['collection/videos'];
      },
    },
    methods: {
    },
  };
</script>

<style>
</style>
