<!--
Start with initial mediaId
Goto /api/theater to get a queue built
Play current media
Move to next media
-->
<template>
  <div class="container-fluid pushFromTop">
    <h1>Test Container</h1>
    <iframe width="100%" height="300" scrolling="no" frameborder="no" src=""></iframe>
  </div>
</template>
<script>
  import { mapGetters, mapActions } from 'vuex'
  import SoundCloudWidget from "soundcloud-widget";

  export default {
    data() {
      return {
      };
    },
    mounted() {
    let iframe = document.createElement("iframe");
    iframe.setAttribute('src', "https://soundcloud.com/conveyortheband/mane?in=soundcloud/sets/creator-clubhouse")
    document.body.appendChild(iframe);

    var widget = new SoundCloudWidget(iframe);
    },
    created() {

    },
    methods: {

    },
    computed: {
    }
  }
</script>

<style>
</style>
