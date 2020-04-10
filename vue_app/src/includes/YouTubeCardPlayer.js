import YTPlayer from 'yt-player';
import $ from 'jquery';

export default class YouTubeCardPlayer {
    constructor(videoID, elementId) {
        this.videoID = videoID;
        this.elementId = elementId;

        this._player = false;
    }

    loadVideo() {
        const elementId = "#" + this.elementId;
        const options = {
            volume: 5,
            fullscreen: true,
            playsinline: true,
            height: $(`#${this.elementId}_media`).height(),
            width: $(`#${this.elementId}_media`).width()
        };

        this._player = new YTPlayer(elementId, options);
        this._player.load(this.videoID);
    }

    play() {
        if (!this._player) {
            this.loadVideo();
        }

        this._player.setVolume(100);
        this._player.play();
    }
}