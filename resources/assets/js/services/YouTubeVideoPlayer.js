import _ from 'lodash';
import YTPlayer from 'yt-player';
/*
	YouTube Video Players

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

		this.playing = false;
	}

	getPlayerState() {
		return this.playing;
	}

	registerVideo(sessionId, videoId, options) {
		const newVideo = createVideo(sessionId, videoId, options);

		this.videos.push(newVideo);

		if(options.autoplay) {
			this.playVideo(sessionId);
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
    	video.options.height = $(`#${video.sessionId}_media`).height();

    	//create iframe yt player on element
    	video.player = new YTPlayer("#" + video.sessionId, video.options);
    	//load the iframe player
    	video.player.load(video.videoId);

    	const videoEvents = this.eventBacklog[sessionId];
    	if(videoEvents) {
    		_.forEach(videoEvents, (event) => {
    			this.registerEvent(event.sessionId, event.eventType, event.callback);
    		})

    		this.eventBacklog[sessionId] = [];
    	}

    	//set player state
    	video.state = "loaded"
	}

	pauseVideo(sessionId) {
		let video = this.findVideo(sessionId);

		if(video.player) {
			this.playing = false;

			video.player.pause();
		}
	}

	playVideo(sessionId) {
		let video = this.findVideo(sessionId);

		if(!video.player) { 
			//preload if not preloaded
			this.preloadVideo(sessionId);
		}

		this.playing = true;

		video.player.play();
		
		this.registerEvent(sessionId, 'ended', () => {
			video.player.destroy();
			video.player = false;
		});
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
	findVideo(sessionId) {
		const video = _.find(this.videos, { sessionId: sessionId});

		return video;
	}
}