<template>
  <div>
    <v-alert
      class="mb-0"
      v-for="(message, index) in messages"
      text
      :type="message.type"
      :key="index"
      >{{ message.message }}</v-alert
    >
  </div>
</template>

<script>
export default {
  name: 'AlertList',
  props: {
    /*string: {
            type: String,
            required: true,
        },*/
  },
  components: {},
  data: () => {
    return {
      messages: [],
    }
  },
  methods: {
    clear() {
      this.messages = []
    },
    create(type, message) {
      this.messages.push({
        type,
        message,
      })
    },
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
