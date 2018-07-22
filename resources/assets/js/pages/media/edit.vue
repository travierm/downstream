<template>
  <div class="container pushFromTop">
      <div class="row">
          <div class="col-lg-6">
            <h1>{{$route.params.id}}</h1>
            <b-form>
                <b-form-group id="title"
                                label="Title"
                                label-for="title"
                                description="Media Title">

                    <b-form-input id="exampleInput1"
                                type="text"
                                required
                                placeholder="Media title">
                    </b-form-input>
                </b-form-group>
            </b-form>
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
      }
    },
    computed: {
    },
    mounted() {
    },
    methods: {

    }
  };
</script>

<style scroped>
</style>
