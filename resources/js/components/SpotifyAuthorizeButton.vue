<template>
    <button @click="getUrl" class="btn btn-outline-success">Authorize Spotify Import</button>
</template>

<script>
  
  export default {
    props:["authorized"],
    data() {
      return {
          isAuthorized: this.authorized
      }
    },
    methods: {
        openInNewTab(url) {
            console.log(url);
            var win = window.open(url);

            win.focus();
        },
        getUrl() {
            axios.get("/api/spotify/url").then((response) => {
                if(response.data) {
                    this.openInNewTab(response.data);
                }
            });
        }
    }
  };
</script>

<style scroped>
</style>
