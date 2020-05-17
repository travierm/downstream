const io = require("socket.io")
const crypto = require("crypto");
const VideoQueue = require('./services/video-queue');
const server = io.listen(4444);

let connectedClients = new Map();

console.log("Started Streaming Server!");
let queue = new VideoQueue(server);

// event fired every time a new client connects:
server.on("connection", (socket) => {
    const sessionId = crypto.randomBytes(16).toString("hex");
    console.info(`Client connected [id=${socket.id}] sessionId=` + sessionId);
    // initialize this client's sequence number
    connectedClients.set(sessionId, socket);

    socket.on('queue_video', (video) => {
        console.info(video.vid + ' has been added to the queue');
        queue.pushVideo(video)
    });

    // when socket disconnects, remove it from the list:
    socket.on("disconnect", () => {
        connectedClients.delete(sessionId);
        console.info(`Client gone [id=${socket.id}] sessionId=` + sessionId);
    });
});

// sends each client its current sequence number
/*setInterval(() => {
    for (const [client, sequenceNumber] of sequenceNumberByClient.entries()) {
        client.emit("seq-num", sequenceNumber);
        sequenceNumberByClient.set(client, sequenceNumber + 1);
    }
}, 1000);*/