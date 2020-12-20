import $ from 'jquery';
import YTPlayer from 'yt-player';
import { registerCardPlayer, playNextCard, playEvent } from '../includes/YouTubePlayerManager';

export default class YouTubeCardPlayer {
    constructor(videoId, cardId) {

        this.videoId = videoId;
        this.cardId = cardId;

        this._player = false;

        this.onPlayCallbacks = [];
        this.eventCallbacks = [];

        registerCardPlayer(this);
    }

    applyEventCallbacks() {
        this.eventCallbacks.forEach((event) => {
            this._player.on(event.eventType, event.callback);
        })
    }

    registerEventCallback(eventType, callback) {
        if(eventType == 'play') {
            this.onPlayCallbacks.push(callback);
            return
        }

        if(this._player) {
            this._player.on(eventType, callback);
        }else{
            this.eventCallbacks.push({
                eventType,
                callback
            });
        }
    }

    handlePlayerEvents() {
        this._player.on('ended', () => {
            this._player.seek(0);
            this._player.pause();

            console.log("ENDED EVENT!");

            playNextCard()
        })
    }

    loadVideo() {
        const cardId = "#" + this.cardId;
        const options = {
            volume: 5,
            fullscreen: true,
            playsinline: true,
            height: $(`${cardId}_media`).height(),
            width: $(`${cardId}_media`).width()
        };

        this._player = new YTPlayer(cardId, options);
        this._player.load(this.videoId);

        this.applyEventCallbacks();
        this.handlePlayerEvents()
    }

    stop() {
        this._player.pause();
        this._player.seek(0);

        this._player.emit('stopped_by_manager');
    }

    play(triggerEvent = false) {
        if (!this._player) {
            this.loadVideo();
        }

        if(triggerEvent) {
            // Let the player know we started playing
            playEvent(this.cardId)
        }
        
        this.onPlayCallbacks.forEach((callback) => {
            callback();
        });

        this._player.setVolume(100);
        this._player.play();
    }
}