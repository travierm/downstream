import _ from "lodash"
import Cache from '../services/Cache'

const _DEBUG = true

class YoutubePlayerManager {
    constructor() {
        this.localCache = new Cache(true)
        this.localCache.setStoragePrefix('player_manager_')

        this.volume = this.localCache.get('volume', 100)
        this.guidIndex = []
        this.guidQueue = []
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
        const nextGuid = this.getNextGuid(currentGuid)

        return this.findCardByGuid(nextGuid)
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
            throw Error('guid not passed')
        }

        return _.find(this.registeredCards, { guid })
    }

    registerCardPlayer(player) {
        if(this.findCardByGuid(player.guid)) {
            console.info("Stop duplicate card registration " + player.guid)
            return
        }

        this.registeredCards.push(player)
    }

    setGuidIndex(index) {
        // Stop playing current card so it doesn't get lost when the index is updated
        if(this.currentPlayingGuid) {
            this.stopPlayingCard(this.currentPlayingGuid);
        }

        this.guidIndex = index
        this.registeredCards = []
        this.currentPlayingGuid = false
    }

    getVolume() {
        return this.volume
    }

    setVolume(value) {
        this.volume = value
        this.localCache.set('volume', value)

        const playingVideo = this.getPlayingCard()

        if (playingVideo) {
            playingVideo.setVolume(value)
        }
    }

    // Large Methods
    removeCard(guid) {
        _.remove(this.guidIndex, guid)
        _.remove(this.registeredCards, {
            guid
        })
    }

    // Add a guid to be queued to play next
    queueNextCard(guid) {
        if(typeof guid === 'string') {
            this.guidQueue.push(guid)
        }
    }

    playNextCard() {
        if (this.guidIndex.length <= 0) {
            throw Error("Can not play next card since guidIndex is empty")
        }

        let nextCard = false
        if(this.guidQueue.length >= 1) {
            const guid = this.guidQueue.shift()

            // We have songs queued up to play
            nextCard = this.findCardByGuid(guid)
        }else{
            nextCard = this.getNextCard(this.currentPlayingGuid)
        }

        if(!nextCard) {
            throw Error('Failed to find nextCard after guid ' + this.currentPlayingGuid)
        }

        nextCard.setVolume(this.volume)
        nextCard.play()
    }

    stopPlayingCard(guid) {
        const playingCard = this.findCardByGuid(guid)

        if (playingCard) {
            playingCard.stop()
        }
    }

    triggerPlayEvent(guid) {
        // const previousPlayingGuid = _.clone(this.currentPlayingGuid)

        // if (previousPlayingGuid) {
            // Stop playing previous card because a new one would like to play
            // this.stopPlayingCard(previousPlayingGuid)
        // }

        // Update current playing card id
        this.currentPlayingGuid = guid
    }
}

const $manager = new YoutubePlayerManager()

window.$manager = $manager

export default $manager
