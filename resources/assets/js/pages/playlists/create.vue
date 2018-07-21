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

    <playlist-media-manager :items="previewItems"></playlist-media-manager>
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
