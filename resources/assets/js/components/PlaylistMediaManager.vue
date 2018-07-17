<template>
    <div class="container-fluid">
      <div class="row">
        <!-- Preview Item -->
        <div class="col-lg-2 mt-2 " v-for="item in previewItems" :key="item.id">
          <div class="card">
            
            <div class="media-container">
              <img class="img-fluid media-image" :src="item.meta.thumbnail"  />
              <p class="media-info ml-3">{{ item.meta.title }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
export default {
    props: {
        items: Array
    },
    data() {
        return {
            selected: []
        };
    }
}
</script>

<style scoped>

</style>