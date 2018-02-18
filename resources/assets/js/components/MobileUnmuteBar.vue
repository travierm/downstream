<template>
    <div v-if="isMobile" class="row">
      <div class="col-lg-12">
        <button @click="unmute" class="btn btn-success">Unmute</button>
      </div>
    </div>
</template>

<script>
  import { mapActions } from 'vuex';

  export default {
    data: () => ({
      madeAction:false,
      isMobile: window._isMobile
    }),
    computed: {
    },
    methods: {
      unmute() {
        this.$store.dispatch('video/unmute');
      }
    },
  };
</script>

<style scoped>
</style>
