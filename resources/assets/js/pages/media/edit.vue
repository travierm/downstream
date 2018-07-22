<template>
  <div class="container pushFromTop">

      <h3>Edit Media</h3>

      <div class="row" v-if="!isAdmin">
        <div class="col-lg-6">
           <b-alert variant="danger" show>You must be an Admin to edit Media items.</b-alert>
        </div>
      </div>

      <div class="row" v-if="isAdmin">
          <div class="col-lg-6">
            <b-form>
                <b-form-group id="title" label="Title" label-for="title" description="">
                    <b-form-input v-model="inputs.title" id="title" type="text" required placeholder="" />
                </b-form-group>

                <b-form-group id="artist" label="Artist" label-for="artist" description="">
                    <b-form-input v-model="inputs.artist" id="artist" type="text" required placeholder="" />
                </b-form-group>

                <b-form-group id="album" label="Album" label-for="album" description="">
                    <b-form-input v-model="inputs.album" id="album" type="text" required placeholder="" />
                </b-form-group>

                <button class="btn btn-primary float-right">Save</button>
            </b-form>

            <div>
              <h4>Raw Data:</h4>
              <raw-data-view :show="true" :data="mediaItem" />
            </div>
          </div>

          <div class="col-lg-6" v-if="mediaItem.meta">
            <youtube-card :title="inputs.title" :videoId="mediaItem.index" :thumbnail="mediaItem.meta.thumbnail"></youtube-card>
          </div>
      </div>
  </div>
</template>

<script>

  export default {
    props: {

    },

    data() {
      return {
        inputs: {
          title: "",
          artist: "",
          album: ""
        },
        mediaItem:{}
      }
    },
    computed: {
      isAdmin() {
        return this.$store.getters['user/isAdmin'];
      }
    },
    mounted() {
      this.getMediaItem(this.$route.params.id);
    },
    methods: {
      getMediaItem(id) {
        axios.get('/api/media/' + id).then((resp) => {
          this.mediaItem = resp.data;

          this.updateInputsToItem();
        })
      },
      postUpdateMedia() {
        const data = {
          mediaId: this.mediaItem.id,
          title: this.inputs.title,
          artist: this.inputs.artist,
          album: this.inputs.album
        }
        axios.post('/api/media/update', )
      },
      updateInputsToItem() {
        this.inputs = {
          title: this.mediaItem.meta.title,
          artist: this.mediaItem.artist,
          album: this.mediaItem.album
        };
      }
    }
  };
</script>

<style scroped>
</style>
