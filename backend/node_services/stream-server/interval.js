let timer = require('./services/timer');


let queue = new timer();

queue.start(5)
queue.registerEndCallback(() => {
    queue.start(5)
})


