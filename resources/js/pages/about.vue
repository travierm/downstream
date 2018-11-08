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

            <li class="list-group-item d-flex justify-content-between lh-condensed">
              <div>
                <h6 class="my-0">Queue Size</h6>
                <small class="text-muted">Number of items in Global Queue (deletes items after 6 days).</small>
              </div>
              <span class="text-primary">{{ queueCountWithCommas }}</span>
            </li>
          </ul>
      </div>
    </div>
  </div>
</template>

<script>
  import { numberWithCommas } from '../services/Utils';

  export default {
    data() {
      return {
        itemCount: 0,
        userCount: 0,
        queueCount: 0,
      }
    },
    computed: {
      itemCountWithCommas() {
        return numberWithCommas(this.itemCount);
      },
      userCountWithCommas() {
        return numberWithCommas(this.userCount);
      },
      queueCountWithCommas() {
        return numberWithCommas(this.queueCount);
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

      axios.get('/api/stat/queue/count').then((resp) => {
        self.queueCount = resp.data.count;
      });
    }
  };
</script>

<style>
</style>
