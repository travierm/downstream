<template>
    <div>
        <button v-if="!isFollowing" @click.prevent="followUser()" class="btn btn-outline-primary">Follow</button>
        <button v-if="isFollowing" @click.prevent="unfollowUser()" class="btn btn-primary">Following</button>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        props: {
            following: {
                type: String,
                default: false,
                required: false
            },
            user: {
                type: Number,
                required: true
            }
        },
        methods: {
            followUser() {
                this.isFollowing = true;

                axios.get('/api/follow/' + this.user)
                    .then((resp) => {

                })
                .catch(() => {
                    this.isFollowing = false;
                });
            },
            unfollowUser() {
                this.isFollowing = false;
                
                axios.get('/api/unfollow/' + this.user)
                    .then((resp) => {

                })
                .catch(() => {
                    this.isFollowing = true;
                });
            }
        },
        data() {
            return {
                isFollowing: (this.following == true)
            }
        }
    };
</script>

<style scoped>
</style>
