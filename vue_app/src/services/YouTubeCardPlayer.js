import $ from "jquery"
import YTPlayer from "yt-player"
import YoutubePlayerManager from "./YoutubePlayerManager"

export default class YouTubeCardPlayer {
    constructor(guid, videoId) {
        this.guid = guid
        this.videoId = videoId
        this._player = false

        this.onPlayCallbacks = []
        this.eventCallbacks = []

        YoutubePlayerManager.registerCardPlayer(this)
    }

    applyEventCallbacks() {
        this.eventCallbacks.forEach((event) => {
            this._player.on(event.eventType, event.callback)
        })
    }

    on(eventType, callback) {
        if (eventType == "play") {
            this.onPlayCallbacks.push(callback)
            return
        }

        if (this._player) {
            this._player.on(eventType, callback)
        } else {
            this.eventCallbacks.push({
                eventType,
                callback,
            })
        }
    }

    handlePlayerEvents() {
        this._player.on("ended", () => {
            this._player.seek(0)
            this._player.pause()

            YoutubePlayerManager.playNextCard()
        })
    }

    loadVideo() {
        const guid = "#" + this.guid
        const options = {
            volume: 5,
            fullscreen: true,
            playsinline: true,
            height: $(`${guid}_media`).height(),
            width: $(`${guid}_media`).width(),
        }

        this._player = new YTPlayer(guid, options)
        this._player.load(this.videoId)

        this.applyEventCallbacks()
        this.handlePlayerEvents()
    }

    stop() {
        if (!this._player) {
            console.error(
                "Trying to stop video that was never loaded " + this.guid
            )
            
            return
        }

        this._player.pause()
        this._player.seek(0)

        this._player.emit("stopped_by_manager")
    }

    setVolume(value = 100) {
        if(this._player) {
            this._player.setVolume(value)
        }else{
            console.error('Tried setting volume on player that was not loaded')
        }
    }

    play(triggerEvent = true) {
        if (!this._player) {
            this.loadVideo()
        }

        if (triggerEvent) {
            // Let the player know we started playing
            YoutubePlayerManager.triggerPlayEvent(this.guid)
        }

        this.onPlayCallbacks.forEach((callback) => {
            callback()
        })

        this._player.setVolume(YoutubePlayerManager.getVolume())
        this._player.play()
    }
}
