/**
 * timer-node
 * @copyright 2020 Eyas Ranjous <eyas.ranjous@gmail.com>
 * @license MIT
 */

/**
 * @class Timer
 */
class Timer {
    constructor(label) {
        this._label = label || '';
        this._isRunning = false;
        this._startTime = null;
        this._endTime = null;
        this.interval = false;
        this.callbacks = [];

    }

    clearEndCallbacks() {
        this.callbacks = [];
    }
    
    registerEndCallback(cb) {
        this.callbacks.push(cb);
    }

    /**
     * @public
     * starts the timer
     */
    start(duration = 100000) {
        this._startTime = process.hrtime();
        this._endTime = null;
        this._isRunning = true;

        this.interval = setInterval(() => {
            console.log("duration " + this.seconds())

            if(this.seconds() >= duration || this.isRunning == false || this._startTime == null) {
                
                this.stop();

            }else{
                this._endTime = process.hrtime(this._startTime)
            }
        }, 1000);
    }

    /**
     * @public
     * stops the timer
     */
    stop() {
        if (!this._isRunning) return;

        clearInterval(this.interval);
        
        this._endTime = process.hrtime(this._startTime);
        this._isRunning = false;

        this.callbacks.forEach((callback) => {
            callback()
        });

        this.clear()
    }

    /**
     * @public
     * clears the timer
     */
    clear() {
        this.clearEndCallbacks();
        this._isRunning = false;
        this._startTime = null;
        this._endTime = null;
    }

    /**
     * @public
     * checks if the timer is running
     * @returns {boolean}
     */
    isRunning() {
        return this._isRunning;
    }

    /**
     * @public
     * calculate the nano-seconds part of the time
     * @returns {number}
     */
    nanoseconds() {
        if (this._endTime === null) return null;

        return this._endTime[1] % 1000;
    }

    /**
     * @public
     * calculate the micro-seconds part of the time
     * @returns {number}
     */
    microseconds() {
        if (this._endTime === null) return null;

        return Math.floor(this._endTime[1] / 1000) % 1000;
    }

    /**
     * @public
     * calculate the milli-seconds part of the time
     * @returns {number}
     */
    milliseconds() {
        if (this._endTime === null) return null;

        return Math.floor(this._endTime[1] / 1000000);
    }

    /**
     * @public
     * calculate the seconds part of the time
     * @returns {number}
     */
    seconds() {
        if (this._endTime === null) return null;

        return this._endTime[0];
    }

    /**
     * @public
     * formats the recorded time using a template
     * @param {string} template
     * @returns {string}
     */
    format(template = '%lbl: %s s, %ms ms, %us us, %ns ns') {
        if (this._endTime === null) return null;

        return template
            .replace('%lbl', this._label)
            .replace('%s', this.seconds())
            .replace('%ms', this.milliseconds())
            .replace('%us', this.microseconds())
            .replace('%ns', this.nanoseconds());
    }
}

module.exports = Timer;