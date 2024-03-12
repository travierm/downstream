import YTPlayer from 'yt-player'
import { getPlayerSizeByCategory } from './services/screenSizeService';

const videoContainerElement = document.getElementById('downstream-video-container')
const playerSize = getPlayerSizeByCategory('sm');

const player = new YTPlayer(videoContainerElement, {
  volume: 5,
  autoplay: true,
  fullscreen: true,
  playsinline: true,
  controls: false,
  height: playerSize.height,
  width: playerSize.width
})

player.on('error', (err) => {
  hideVideoContainer()

  throw err
})

window.playVideo = (videoId) => {
  showVideoContainer()

  player.load(videoId)
  player.setVolume(100)
}

export function showVideoContainer() {
  videoContainerElement.style.visibility = 'visible'
}

export function hideVideoContainer() {
  videoContainerElement.style.visibility = 'hidden'
}