<template>
	<div class="container-fluid">
		<div class="row" style="margin-top:10px;" v-if="items.length >= 1">
			<div class="col">
				<b-alert show variant="primary">This page is in beta. You may see types of media we didn't intent to show you.</b-alert>
			</div>
		</div>

		<div class="row" v-if="items.length >= 1">

			<div v-for="item in items" :key="item.id" class="col-lg-3 col-md-6 col-sm-12">
		        <video-player-card 
		          :spotifyId="item.source_id" 
		          :vid="item.index" 
		          :title="item.title" 
		          :thumbnail="item.thumbnail">
		        </video-player-card>
      		</div>
		</div>

		<div id="noItemsAlert" class="row" v-if="items.length <= 0" style="margin-top:10px; display: none;">
			<div class="col">
				<b-alert show variant="danger">You need to collect songs before we can make recommendations!</b-alert>
			</div>
		</div>

		<master-bar></master-bar>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				items: []
			}
		},
		created() {
			this.getDiscoverables();
		},
		methods: {
			getDiscoverables() {
				axios.post('/api/media/discoverables').then((resp) => {
					if(resp.data.items.length >= 1) {
						this.items = resp.data.items;
					}else{
						$('#noItemsAlert').show();
					}
				})
			}
		}
	};
</script>

<style>
</style>