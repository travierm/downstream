<template>
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Playlist
        </button>

        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#">Create new Playlist</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">Kid Cudi</a>
            <a class="dropdown-item active" href="#">WRX Chill</a>
            <a class="dropdown-item" href="#">Grand Optimist</a>
        </div>
    </div>
</template>

<script>
  export default {
    name:'YouTubeCardPlayListButton',
    props: {
        /* name:{
            default: 'Test',
            type: String
        } */
    },
    data() {
      return {}
    },
    computed:{},
    methods: {}
  };
</script>

<style scoped>

</style>
