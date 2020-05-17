const crypto = require("crypto");

let timer = require("./timer");
let queueTimer = new timer();

module.exports = class VideoQueue {
    constructor(socketServer) {
        this.id = crypto.randomBytes(16).toString("hex")
        this.socketServer = socketServer

        this.queue = []
        this.state = {
            running: false,
            playingVideo: false
        }
    }

    handleStartingQueue() {
        if(this.state.running == false) {
            if(this.queue.length <= 2) {
                // dont start the queue until at least two videos are queued
                return;
            }

            this.nextVideo(); 
        }  
    }

    nextVideo() {
        console.log('nextVideo Triggered')
       
        const video = this.queue.shift()

        if(!video) {
            this.state.running = false;
            this.state.playingVideo = false;
            console.log("no video to play");
            return;
        }

        this.state.running = true;
        this.state.playingVideo = video;

        this.socketServer.emit('start_video', video)
        console.log(this.state.playingVideo.vid + " has started playing with a duration of " + this.state.playingVideo.duration);

        queueTimer.clear();

        setTimeout(() => {
            // Wait a few secs for the video to load before starting duration count
            queueTimer.start(video.duration);
            queueTimer.registerEndCallback(() => {
                // video stopped playing
                console.log(this.state.playingVideo.vid + " has reached its duration");

                if (this.queue.length >= 1) {
                    this.nextVideo();
                } else {
                    this.state.running = false;
                    console.log("queue is now empty")
                }
            })
        }, 3000)
    }

    pushVideo(video) {
        const queueItem = video

        this.queue.push(queueItem)

        
        this.handleStartingQueue();
    }
}