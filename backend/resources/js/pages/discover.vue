<template>
	<div class="container-fluid">
		<div class="row" style="margin-top:10px;" v-if="items.length >= 1">
			<!-- <div class="col-lg-6">
				<b-alert show variant="primary"><button class="btn btn-primary" onclick="location.reload()">Refresh</button> We found some music you may like based on your collection. Click refresh to shuffle.</b-alert>
			</div> -->
		</div>

		<div class="row mt-2" v-if="items.length >= 1">

			<div v-for="item in items" :key="item.id" class="col-lg-3 col-md-6 col-sm-12">
		        <youtube-card 
		          :spotifyId="item.source_id" 
		          :video-id="item.index" 
		          :title="item.title" 
		          :thumbnail="item.thumbnail"
				  :collected="item.collected">
		        </youtube-card>
      		</div>
		</div>

		<div id="noItemsAlert" class="row" v-if="items.length <= 0" style="margin-top:10px; display: none;">
			<div class="col">
				<b-alert show variant="warning">You need a larger collection before we can discover new music for you!</b-alert>
			</div>
		</div>

		<control-bar></control-bar>
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
				}).catch(() => {
					$('#noItemsAlert').show();
				})
			}
		}
	};
</script>

<style>
</style>