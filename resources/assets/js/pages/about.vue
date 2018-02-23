<template>
  <div class="container pushFromTop">
    <div class="row">
      <div class="col lead">
        <h2>Purpose - Discover, Collect, Play</h2>
        <p>We’ve harnessed the quality and scale of YouTube into full blown media service. You can search, collect and play any type of media found on YouTube through our site. Not only will you be collecting for yourself but you’ll also help other users discover great content.
Register on our site today and be apart of something unique!</p>

        <h2>Users: <span class="text-success">{{ userCountWithCommas }}</span></h2>
        <h2 style="padding-bottom: 10px;">Library Size: <span class="text-primary">{{ itemCountWithCommas }}</span> items</h2>

        <h2>Planned Features:</h2>
        <ul>
          <li>Playlists</li>
          <li>Friending/Following</li>
          <li>Better Mobile player</li>
          <li>Better Discovery and Frontpage mechanics</li>
          <li>Original Artist Content and Tools for Artists</li>
        </ul>  

        <h2>Development State: <span class="text-warning"> &Alpha;LPHA</span></h2>
        <p>We're at the very beginning of the development proccess. I have a few more big features planned and will continue development beyond depending on user growth and activity.</p>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <h3>Thanks for your interest in Downstream!</h3>
      </div>
    </div>
  </div>
</template>

<script>

  export default {
    data() {
      return {
        itemCount: 0,
        userCount: 0
      }
    },
    computed: {
      itemCountWithCommas() {
        return window._utils.numberWithCommas(this.itemCount);
      },
      userCountWithCommas() {
        return window._utils.numberWithCommas(this.userCount);
      }
    },
    mounted() {
      let self = this;
      axios.get('/api/stat/library/size').then((resp) => {
        self.itemCount = resp.data.count;
      });

      axios.get('/api/stat/user/count').then((resp) => {
        self.userCount = resp.data.count;
      });
    }
  };
</script>

<style>
</style>
