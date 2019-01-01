<template>
    <div>
        <button v-if="isAuthorized" class="btn btn-success disabled">Connected Accounts</button>
        <button v-if="isAuthorized" @click="disableAccess" class="btn btn-outline-danger">Disable Access</button>

        <button v-if="!isAuthorized" @click="getUrl" class="btn btn-outline-danger">Connect with Spotify</button>
    </div>
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
            var win = window.open(url);

            win.focus();
        },
        disableAccess() {
            axios.get('/api/spotify/disable').then((response) => {
                window.reload();
            });
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
