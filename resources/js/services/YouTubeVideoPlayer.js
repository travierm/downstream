import _ from 'lodash';
import YTPlayer from 'yt-player';
/*
	YouTube Video Player

	handles:
	registering videos
	preloading videos
	loading videos on play
*/

function createVideo(sessionId, videoId, options) {
	return { sessionId, videoId, options};
}

export default class YouTubeVideoPlayer {

	constructor() {
		this.videos = [];
		this.eventBacklog = [];
		this.volume = 75;

		this.playing = false;
	}

	resetState()  {
		//this.videos = [];
		this.eventBacklog = [];
		this.playing = false;
	}

	setVolume(volumeLevel) {
		this.volume = volumeLevel;
	}

	updatePlayerVolume(sessionId) {
		let video = this.findVideo(sessionId);

		if(video.player) {
			video.player.setVolume(this.volume);
		} 
	}

	getPlayerState() {
		return this.playing;
	}

	destroyVideo(sessionId) {
		let video = this.findVideo(sessionId);

		if(video.player) {
			video.player.destroy();
		}

		this.removeVideo(sessionId)
	}

	registerVideo(sessionId, videoId, options) {
		const newVideo = createVideo(sessionId, videoId, options);

		this.videos.push(newVideo);

		if(options.preload) {
			consl('doing preload');
			this.preloadVideo(sessionId);
		}

		//consl("registered new video " + sessionId);
	}

	preloadVideo(sessionId) {
		//video the video to preload
		let video = this.findVideo(sessionId);

		if(!video) {
			consl("Could not preload video " + sessionId);
			return false; 	
		}

		if(video.player) {
			//already preloaded
			return;
		}

		//override options
		video.options.autoplay = false;
		video.options.modestbranding = true;
		video.options.related = false;
		video.options.playsinline = true;
		video.options.fs = false;
		
		if(!video.options.height && $(`#${video.sessionId}_media`).height() != 0) 
    		video.options.height = $(`#${video.sessionId}_media`).height();

    	//create iframe yt player on element
    	video.player = new YTPlayer("#" + video.sessionId, video.options);
    	//load the iframe player
    	video.player.load(video.videoId);
		video.player.setVolume(this.volume);

    	if(video.options.mute) {
    		video.player.mute();
    	}

    	const videoEvents = this.eventBacklog[sessionId];
    	if(videoEvents) {
    		_.forEach(videoEvents, (event) => {
    			this.registerEvent(event.sessionId, event.eventType, event.callback);
    		})
    	}

    	//set player state
    	video.state = "loaded"
	}

	stopVideo(sessionId) {
		let video = this.findVideo(sessionId);

		if(video.player) {
			video.player.destroy();
			video.player = false;
		}
	}

	pauseVideo(sessionId) {
		let video = this.findVideo(sessionId);

		if(video.player) {
			this.playing = false;

			video.player.seek(0);
			video.player.pause();
		}
	}

	playVideo(sessionId) {

		let video = this.findVideo(sessionId);
		if(!video) {
			return false;
		}

		if(!video.player) { 
			//preload if not preloaded
			this.preloadVideo(sessionId);
		}

		this.playing = true;
		video.player.setVolume(this.volume);
		video.player.play();
		

		return true;
	}

	registerEvent(sessionId, eventType, callback) {
		const video = this.findVideo(sessionId);
		if(!video) {
			return;
		}

		if(!video.player) {
			this.backlogEvent(sessionId, {
				sessionId,
				eventType,
				callback
			});

			return;
		}

	 	if (_.isArray(eventType)) {
	        _.forEach(eventType, (type) => {
	          video.player.on(type, callback);
	        });
      	} else {
        	video.player.on(eventType, callback);
      	}
	}

	backlogEvent(sessionId, event) {
		if(!this.eventBacklog[sessionId]) {
			this.eventBacklog[sessionId] = [];
		}

		this.eventBacklog[sessionId].push(event);
	}

	//PRIVATE
	removeVideo(sessionId) {
		_.remove(this.videos, {
			sessionId: sessionId
		});
	}

	findVideo(sessionId) {
		const video = _.find(this.videos, { sessionId: sessionId});

		return video;
	}
}