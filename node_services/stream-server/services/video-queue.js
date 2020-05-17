const crypto = require("crypto");

function createVideoQueueItem(video) {
    return {
        video
    }
}

module.exports = class VideoQueue {
    constructor() {
        this.id = crypto.randomBytes(16).toString("hex")
        this.queue = [];
    }

    pushVideo(video) {
        const queueItem = video

        this.queue.push(queueItem)
    }
}