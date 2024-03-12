<template>
    <v-container>
        <v-row justify="center">
            <v-col cols="12">
                <h1>Invite Friends</h1>
                <p>Hand out invite codes to your friends! Invites are active for a week before being deactivated.
                </p>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12">
                <div class="">
                    <a target="_blank" :href="link">{{ link }}</a>
                    <br />
                    <v-btn @click="generateInviteLink()" class="mt-2">Generate Invite Link</v-btn>
                </div>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import http from '@/services/api/Client'

export default {
    name: 'InviteView',
    components: {
    },
    data: () => ({
        link: null,
    }),
    methods: {
        generateInviteLink() {
            http.get('/invite/generate')
                .then(response => {
                    this.link = response.data.link
                })
                .catch(error => {
                    console.log(error)
                })
        }
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
