<template>
    <v-container>
        <v-row justify="center">
            <v-col cols="12">
                <h1>Invite Friends</h1>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12">
                <div class="">
                    <a :href="inviteLink">{{ inviteLink }}</a>
                    <br />
                    <v-btn class="mt-2">Generate Invite Link</v-btn>
                </div>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>

export default {
    name: 'InviteView',
    components: {
    },
    data: () => ({
        invite_code: 'ASDFWEFF@',
    }),
    computed: {
        inviteLink() {
            return `https://downstream.us/register?invite=${this.invite_code}`
        }
    },
    methods: {},
    mounted() {

    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
