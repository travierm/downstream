import _ from "lodash"

const _DEBUG = true

function dd(data) {
    if (_DEBUG) {
        console.log(data)
    }
}

class YoutubePlayerManager {
    constructor() {
        this.volume = 100
        this.guidIndex = []
        this.registeredCards = []
        this.currentPlayingGuid = false
    }

    getPlayingCardId() {
        return this.currentPlayingGuid
    }

    getPlayingCard() {
        if (!this.currentPlayingGuid) {
            return false
        }

        return this.findCardByGuid(this.currentPlayingGuid)
    }

    getNextCard(currentGuid) {
        this.findCardByGuid(this.getNextGuid(currentGuid))
    }

    getNextGuid(currentGuid) {
        const guidIndexLength = this.guidIndex.length - 1
        const currentIndex = this.findIndexByGuid(currentGuid)

        const nextIndex = currentIndex >= guidIndexLength ? 0 : currentIndex + 1

        return this.guidIndex[nextIndex]
    }

    findIndexByGuid(guid) {
        return this.guidIndex.indexOf(guid)
    }

    findCardByGuid(guid) {

        if(!guid) {
            throw Error('guid no passed')
        }

        return _.find(this.registeredCards, { guid })
    }

    registerCardPlayer(player) {
        this.registeredCards.push(player)
    }

    setGuidIndex(index) {
        this.guidIndex = index
        this.registeredCards = []
        this.currentPlayingGuid = false
    }

    getVolume() {
        return this.volume
    }

    setVolume(value) {
        this.volume = value

        const playingVideo = getPlayingCard()

        if (playingVideo) {
            playingVideo.setVolume(value)
        }
    }

    // Large Methods
    playNextCard() {
        if (this.guidIndex.length <= 0) {
            console.error("Can not play next card since guidIndex is empty")
            return
        }

        let nextCard = this.getNextCard(this.currentPlayingGuid)

        nextCard.setVolume(volume)
        nextCard.play()
    }

    stopPlayingCard(guid) {
        let playingCard = this.findCardByGuid(guid)

        if (playingCard) {
            playingCard.stop()
        }
    }

    triggerPlayEvent(guid) {
        const previousPlayingGuid = _.clone(this.currentPlayingGuid)

        if (previousPlayingGuid) {
            // Stop playing previous card because a new one would like to play
            this.stopPlayingCard(previousPlayingGuid)
        }

        // Update current playing card id
        this.currentPlayingGuid = guid
    }
}

const $manager = new YoutubePlayerManager()

export default $manager
