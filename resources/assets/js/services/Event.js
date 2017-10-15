export default class Event {
  constructor() {
    this.didFire = false;
    this.listeners = [];
    //this.registerListener = this.registerListener.bind(this);
  }

  fire() {
    this.listeners.forEach((cb) => cb());
    this.didFire = true;
  }

  registerListener(cb) {
    if(this.didFire) {
      cb();
    }else{
      this.listeners.push(cb);
    }
  }
}
