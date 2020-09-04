<template>
<div>
  <button @click="toggle" v-if="!showing">Show</button>
  <button @click="toggle" v-if="showing">Hide</button>
  <pre v-if="showing">
      <code>
          {{data}}
      </code>
  </pre>
</div>
</template>

<script>

  export default {
    props: {
        show:{
            default: true,
            type: Boolean
        },
        data: {
            default: {}
        }
    },
    data() {
      return {
          showing: this.show
      }
    },
    methods: {
        toggle() {
            this.showing = !this.showing;
        }
    }
  };
</script>

<style scroped>
</style>
