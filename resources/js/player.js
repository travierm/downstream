import YTPlayer from 'yt-player'

window.playVideo = (videoId) => {

    const options = {
      volume: 5,
      autoplay: true,
      fullscreen: true,
      playsinline: true,
      controls: false,
      height: 500,
      width: 500
    }

    const player = new YTPlayer('#downstream-video-container', options)

        player.on('error', (err) => {
        throw err
      })

    console.log(videoId);
    player.load(videoId)
    player.setVolume(100)


    player.play()


}