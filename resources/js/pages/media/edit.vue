<template>
  <div class="container pushFromTop">

      <h3>Edit Media</h3>
      
      <div class="row" v-if="formPostResults == 'success'">
        <div class="col-lg-6">
           <b-alert variant="success" show>Media information updated!</b-alert>
        </div>
      </div>

      <div class="row" v-if="formPostResults == 'failed'">
        <div class="col-lg-6">
           <b-alert variant="danger" show>Failed to update Media information.</b-alert>
        </div>
      </div>

      <div class="row">
          <div class="col-lg-6">
            <b-form @submit="postUpdateMedia">
                <b-form-group id="title" label="Title" label-for="title" description="">
                    <b-form-input v-model="inputs.title" id="title" type="text" required placeholder="" />
                </b-form-group>

                <b-form-group id="thumbnail" label="Thumbnail" label-for="thumbnail" description="">
                    <b-form-input v-model="inputs.thumbnail" id="thumbnail" type="text" required placeholder="" />
                </b-form-group>

                <!--<b-form-group id="artist" label="Artist" label-for="artist" description="">
                    <b-form-input v-model="inputs.artist" id="artist" type="text" required placeholder="" />
                </b-form-group>

                <b-form-group id="album" label="Album" label-for="album" description="">
                    <b-form-input v-model="inputs.album" id="album" type="text" required placeholder="" />
                </b-form-group>-->

                <button class="btn btn-primary float-right">Save</button>
            </b-form>

            <div v-if="isAdmin">
              <h4>Raw Data:</h4>
              <raw-data-view :show="true" :data="mediaItem" />
            </div>
          </div>

          <div class="col-lg-6" v-if="mediaItem">
            <youtube-card :title="inputs.title" :videoId="mediaItem.index" :thumbnail="inputs.thumbnail"></youtube-card>
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
        formPostResults:false,
        mediaItem:false
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
          if(!resp.data) {
            console.error("Failed to get media data");
            return;
          }

          this.mediaItem = resp.data;
          this.mediaItem.meta = JSON.parse(this.mediaItem.meta);

          this.updateInputsToItem();
        })
      },
      postUpdateMedia(events) {
        events.preventDefault();

        const data = {
          mediaId: this.mediaItem.id,
          title: this.inputs.title,
          thumbnail: this.inputs.thumbnail
        };

        axios.post('/api/media/update', data)
          .then(() => {
            this.getMediaItem(this.mediaItem.id);
            this.formPostResults = 'success';
          })
          .catch(() => {
            this.formPostResults = 'failed';
          });
      },
      updateInputsToItem() {
        this.inputs = {
          title: this.mediaItem.meta.title,
          thumbnail: this.mediaItem.meta.thumbnail
        };
      }
    }
  };
</script>

<style scroped>
</style>
