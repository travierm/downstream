import _ from 'lodash'
import YTPlayer from 'yt-player'

import Cache from '../services/Cache'
import AnalyticsService from './api/AnalyticsService'
import { getPlayerSizeByCategory } from './api/ScreenSizeService'

const _DEBUG = true

class YoutubePlayerManager {
  constructor() {
    this.localCache = new Cache(true)
    this.localCache.setStoragePrefix('player_manager_')

    this.volume = this.localCache.get('volume', 100)
    this.muted = false

    // All guids available to the manager
    this.guidIndex = []
    // A map of guid to video information
    this.guidVideoMap = {}
    // Queued guids used when repeating a video
    this.guidQueue = []

    this.registeredCards = []
    this.currentPlayingGuid = false

    this.videoPlayerInstance = false

    this.volumeChangeListener = []

    this.playerSize = getPlayerSizeByCategory('sm')
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

  setPlayerSize(width, height) {
    this.playerSize = {
      width,
      height,
    }

    if (this.videoPlayerInstance) {
      this.videoPlayerInstance._player.setSize(width, height)
    }
  }

  setVolume(value) {
    if (this.volume !== value) {
      this.volume = value
      this.localCache.set('volume', value)
    }

    if (this.videoPlayerInstance) {
      if (
        this.videoPlayerInstance.getVolume() !== value &&
        !this.getIsMuted()
      ) {
        this.videoPlayerInstance.setVolume(value)
      }
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

  loadVideo(videoId, seekTime = null) {
    const options = {
      volume: 5,
      autoplay: true,
      fullscreen: true,
      playsinline: true,
      controls: false,
      height: this.playerSize.height,
      width: this.playerSize.width,
    }

    let videoPlayerInstance
    try {
      this.showVideoElement()
      videoPlayerInstance = new YTPlayer('#downstream-video-container', options)

      videoPlayerInstance.setVolume(this.volume)
      videoPlayerInstance.load(videoId)
      if (seekTime) {
        videoPlayerInstance.seek(seekTime)
      }
      videoPlayerInstance.play()

      videoPlayerInstance.on('error', (err) => {
        throw err
      })

      let previousTimestamp = null
      videoPlayerInstance.on('timeupdate', (currentTimestamp) => {
        if (
          previousTimestamp !== null &&
          previousTimestamp * 0.65 > currentTimestamp
        ) {
          const currentMediaId = this.findVideoByGuid(
            this.currentPlayingGuid
          )?.id

          if (currentMediaId) {
            AnalyticsService.playedMedia(
              this.findVideoByGuid(this.currentPlayingGuid)?.id,
              'rewind'
            )

            console.log(
              'The video was rewinded more than 35% from last time update.'
            )
          }
        }
        previousTimestamp = currentTimestamp
      })

      videoPlayerInstance.on('ended', () => {
        this.playNext()
      })
    } catch (error) {
      throw error
    }

    this.videoPlayerInstance = videoPlayerInstance

    this.playerVolumeChangeWatcher()
  }

  playGuid(guid, seekTime = null, playType = 'autoplay') {
    const guidVideo = this.findVideoByGuid(guid)
    const previousPlayingGuid = _.clone(this.currentPlayingGuid)

    if (previousPlayingGuid) {
      // Stop playing previous card because a new one would like to play
      this.stopPlayingGuid(guid)
    }

    // Update current playing card id
    this.currentPlayingGuid = guid

    this.loadVideo(guidVideo.videoId, seekTime)
    AnalyticsService.playedMedia(guidVideo.id, playType)
  }

  onVolumeChange(listener) {
    if (typeof listener !== 'function') {
      console.error('onVolumeChange must be passed a function')
      return
    }

    this.volumeChangeListener.push(listener)
  }

  removeVolumeChangeListeners(listenerToRemove) {
    this.volumeChangeListener = this.volumeChangeListener.filter(
      (listener) => listener !== listenerToRemove
    )
  }

  playerVolumeChangeWatcher() {
    // if (this.volumeInterval) {
    //   clearInterval(this.volumeInterval)
    // }
    // this.volumeInterval = setInterval(() => {
    //   const appVolume = this.getVolume()
    //   const playerVolume = this.videoPlayerInstance.getVolume()
    //   if (this.getIsMuted() !== this.muted) {
    //     const cahceVolume = this.localCache.get('volume', 100)
    //     this.setVolume(cahceVolume)
    //     this.videoPlayerInstance.setVolume(cahceVolume)
    //     this.toggleMute()
    //   } else if (appVolume !== playerVolume && !this.getIsMuted()) {
    //     // When not muted and the player changes volume adjust the manager volume
    //     this.setVolume(playerVolume)
    //     this.volumeChangeListener.forEach((cb) => cb(playerVolume))
    //   } else if (
    //     this.getIsMuted() &&
    //     appVolume === playerVolume &&
    //     appVolume !== 0
    //   ) {
    //     // When the player is muted, the player will go to the last known volume that is not 0
    //     // So we override our volume in the manager to be 0 so it looks the same as the player
    //     this.setVolume(0)
    //     this.volumeChangeListener.forEach((cb) => cb(0))
    //   } else if (this.getIsMuted() && appVolume !== 0) {
    //     // The player is muted, and the app volume is going up, adjust the player volume and unmute
    //     this.videoPlayerInstance.setVolume(appVolume)
    //     this.toggleMute()
    //   }
    // }, 50)
  }

  getIsMuted() {
    if (
      !this.videoPlayerInstance ||
      !this.videoPlayerInstance._player ||
      !this.videoPlayerInstance._player.isMuted
    ) {
      return false
    }

    return this.videoPlayerInstance._player.isMuted()
  }

  toggleMute() {
    if (!this.videoPlayerInstance) {
      return
    }

    if (this.getIsMuted()) {
      this.videoPlayerInstance.unMute()
      this.muted = false
      return
    }

    this.muted = true
    this.videoPlayerInstance.mute()
  }
}

const $manager = new YoutubePlayerManager()

window.$manager = $manager

export default $manager
