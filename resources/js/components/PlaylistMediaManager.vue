<template>
    <div class="container-fluid">
      <div class="row">
        <!-- Preview Item -->
        <div class="col-lg-2 mt-2 " v-for="item in items" :key="item.id">
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
.media-container {
  position: relative;
} 
.media-container .media-info {
  position: absolute; 
  z-index: 1; 
  top: 0; 
  left: 0; 
  color: white; 
  margin-top: 10px;
}

.media-image:hover {
  -webkit-filter: brightness(25%);
  -webkit-transition: all 0.1s ease;
  -moz-transition: all 0.1s ease;
  -o-transition: all 0.1s ease;
  -ms-transition: all 5.5s ease;
  transition: all 0.1s ease;
}
</style>