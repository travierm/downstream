<template>
    <button @click.prevent="handleClick()" class="btn btn-primary">Use Theme</button>
</template>

<script>

  export default {
    props: {
        theme: String,
        form: {
            type: String,
            default: "#settingsForm"
        }
    },
    methods: {
        handleClick() {
             $("#settingsForm").append('<input type="hidden" name="theme" value="' + this.theme + '" />');    
            $(this.formName).submit( function(e) {
                $("#settingsForm").append('<input type="hidden" name="theme" value="' + this.theme + '" />');    

                return false;
            });

            document.getElementById('settingsForm').submit();
        }
    }
  };
</script>

<style scoped>
</style>
