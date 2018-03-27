<template>
  <div class="container pushFromTop">
    <div class="row">
      <div class="col lead">
        <h2>Service Stats:</h2>
        <ul class="list-group mb-2">
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Total Users:</h6>
                <small class="text-muted">Number of active users.</small>
              </div>
              <span class="text-success">{{ userCountWithCommas }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Library Size</h6>
                <small class="text-muted">Number of items our users have imported into the network through search.</small>
              </div>
              <span class="text-primary">{{ itemCountWithCommas }}</span>
            </li>
          </ul>
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
