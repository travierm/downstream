<template>
    <v-tooltip location="top">
        <template v-slot:activator="{ props }">
            <v-btn v-bind="props" icon color="light-blue-accent-3" @click="autoFixItem">
                <v-icon>{{ mdiAutoFix }}</v-icon>
            </v-btn>-
        </template>
        <span>Fix a broken video</span>
    </v-tooltip>
</template>

<script>
import { mdiAutoFix } from '@mdi/js'
import CollectionService from '@/services/api/CollectionService'
import { getAutofixByMediaId } from '@/services/api/MediaService'

export default {
    name: 'AutoFixAction',
    props: {
        mediaId: {
            type: Number,
            default: 0,
            required: false,
        },
    },
    data() {
        return {
            mdiAutoFix,
        }
    },
    methods: {
        autoFixItem() {
           getAutofixByMediaId(this.mediaId).then((response) => {
                console.log(response)
            })
        },
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped></style>
