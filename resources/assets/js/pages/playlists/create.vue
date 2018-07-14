<template>
  <div>
    <div class="container pushFromTop">
      <div class="row">
        <h1>Create Playlist</h1>
      </div>

      <div class="row">
        <div class="col-lg-8">
          <div class="form-group">
            <label for="exampleInputEmail1">Playlist Name</label>
            <input type="text" class="form-control" >
          </div>

          <div class="form-group">
            <button class="btn btn-outline-primary">Set Private</button>
            <small>This playlist is currently open to public viewing.</small>
          </div>

          <div class="form-group">
            <label>Search Collection</label>
            <input type="search" class="form-control" >
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <!-- Preview Item -->
        <div class="col-lg-2" v-for="item in previewItems" :key="item.id">
          <div class="card">
            
            <img class="img-fluid" :src="item.meta.thumbnail"  />
            <div class="col">
              <div class="col-sm-12">
                <p style="color:white;">{{ item.meta.title }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  export default {
    data() {
      return {
        previewItems: []
      }
    },
    computed: {},
    mounted() {
      this.getCollectionPreview();
    },
    methods: {
      getCollectionPreview() {
        axios.get('/api/playlists/collection/preview').then((resp, err) => {
          if (err) throw err;

          this.previewItems = resp.data;
        })
      }
    }
  };
</script>

<style>
</style>
