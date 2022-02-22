import _ from 'lodash'
import YTPlayer from 'yt-player'
import Cache from '../services/Cache'

const _DEBUG = true

class YoutubePlayerManager {
  constructor() {
    this.localCache = new Cache(true)
    this.localCache.setStoragePrefix('player_manager_')

    this.volume = this.localCache.get('volume', 100)

    // All guids available to the manager
    this.guidIndex = []
    // A map of guid to video information
    this.guidVideoMap = {}
    // Queued guids used when repeating a video
    this.guidQueue = []

    this.registeredCards = []
    this.currentPlayingGuid = false

    this.videoPlayerInstance = false
  }

  getPlayingGuid() {
    return this.currentPlayingGuid
  }

  getNextGuid(currentGuid) {
    const guidIndexLength = this.guidIndex.length - 1
    const currentIndex = this.findIndexByGuid(currentGuid)

    // if we can find our current index just start over
    if (currentIndex == undefined) {
      return 0
    }

    const nextIndex = currentIndex >= guidIndexLength ? 0 : currentIndex + 1

    return this.guidIndex[nextIndex]
  }

  findIndexByGuid(guid) {
    return this.guidIndex.indexOf(guid)
  }

  findVideoByGuid(guid) {
    return this.guidVideoMap[guid]
  }

  setGuidIndex(index) {
    this.guidIndex = index
  }

  updateGuidVideoMap(mapData) {
    this.guidVideoMap = { ...this.guidVideoMap, ...mapData }
  }

  getVolume() {
    return this.volume
  }

  setVolume(value) {
    this.volume = value
    this.localCache.set('volume', value)

    if (this.videoPlayerInstance) {
      this.videoPlayerInstance.setVolume(value)
    }
  }

  // Large Methods
  removeGuid(guid) {
    _.remove(this.guidIndex, guid)
  }

  // Add a guid to be queued to play next
  queueNextGuid(guid) {
    if (typeof guid === 'string') {
      this.guidQueue.push(guid)
    }
  }

  playNext() {
    if (this.guidIndex.length <= 0) {
      throw Error('Can not play next card since guidIndex is empty')
    }

    let nextGuid
    if (this.guidQueue.length >= 1) {
      const guid = this.guidQueue.shift()

      // We have songs queued up to play
      nextGuid = guid
    } else {
      nextGuid = this.getNextGuid(this.currentPlayingGuid)
    }

    if (!nextGuid) {
      throw Error(
        'Failed to find nextCard after guid ' + this.currentPlayingGuid
      )
    }

    this.playGuid(nextGuid)
  }

  hideVideoElement() {
    var element = document.getElementById('downstream-video-container')
    element.style.visibility = 'hidden'
  }

  showVideoElement() {
    var element = document.getElementById('downstream-video-container')
    element.style.visibility = 'visible'
  }

  stopPlayingGuid() {
    if (this.videoPlayerInstance) {
      this.videoPlayerInstance.stop()
      this.videoPlayerInstance.destroy()

      //this.hideVideoElement()
    }
  }

  loadVideo(videoId) {
    const options = {
      volume: 5,
      fullscreen: true,
      playsinline: true,
      controls: false,
      height: '350px',
      width: '350px',
    }

    let videoPlayerInstance
    try {
      this.showVideoElement()
      videoPlayerInstance = new YTPlayer('#downstream-video-container', options)

      videoPlayerInstance.setVolume(this.volume)
      videoPlayerInstance.load(videoId)
      videoPlayerInstance.play()

      videoPlayerInstance.on('ended', () => {
        this.playNext()
      })
    } catch (error) {
      console.log(error)
    }

    this.videoPlayerInstance = videoPlayerInstance
  }

  playGuid(guid) {
    const guidVideo = this.findVideoByGuid(guid)
    const previousPlayingGuid = _.clone(this.currentPlayingGuid)

    if (previousPlayingGuid) {
      // Stop playing previous card because a new one would like to play
      this.stopPlayingGuid(guid)
    }

    // Update current playing card id
    this.currentPlayingGuid = guid

    this.loadVideo(guidVideo.videoId)
  }
}

const $manager = new YoutubePlayerManager()

window.$manager = $manager

export default $manager
