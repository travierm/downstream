import _ from 'lodash';

const _DEBUG = false;

let volume = 100;
let guidIndex = []
let registeredCards = [];
let currentPlayingGuid = false

function dd(data) {
    if(_DEBUG) {
        console.log(data);
    }
}

function getNextCard(currentGuid) {
    return findCardByGuid(getNextGuid(currentGuid))
}

export function getNextGuid(currentGuid) {
    const guidIndexLength = guidIndex.length - 1;
    const currentIndex = findIndexByGuid(currentGuid)
    const nextIndex = currentIndex >= guidIndexLength ? 0 : currentIndex + 1

    return guidIndex[nextIndex]
}

export function findCardByGuid(guid) {
    return _.find(registeredCards, { guid })
}

export function findIndexByGuid(guid) {
    return guidIndex.indexOf(guid)
}

export function setGuidIndex(index) {
    guidIndex = index
    registeredCards = []
    currentPlayingGuid = false

    dd(index)
}

export function getPlayingCard() {
    if(!currentPlayingGuid) {
        return false;
    }

    return findCardByGuid(currentPlayingGuid)
}

export function getPlayingCardId() {
    return currentPlayingGuid
}

export function getVolume() {
    return volume
}

export function setVolume(value) {
    volume = value;

    const playingVideo = getPlayingCard();

    if(playingVideo) {
        playingVideo.setVolume(value)
    }
}

export function registerCardPlayer(player) {
    registeredCards.push(player);

    //dd("Registered player from cardId " + player.cardId + " for videoId " + player.videoId)
}

export function playNextCard() {
    dd('PLAY NEXT CARD event called')
    if(guidIndex.length <= 0) {
        console.error("Can not play next card since guidIndex is empty")
        return
    }

    let nextCard = getNextCard(currentPlayingGuid);

    dd("PLAYING " + nextCard.guid)
    nextCard.setVolume(volume)
    nextCard.play()
}

function stopPlayingCard(guid) {
    let playingCard = findCardByGuid(guid)

    if (playingCard) {
        playingCard.stop()
    }
}

export function triggerPlayEvent(guid) {
    dd("trigger playevent " + guid)
    dd(registeredCards)

    const previousPlayingGuid = _.clone(currentPlayingGuid);
    
    if (previousPlayingGuid) {
        // Stop playing previous card because a new one would like to play
        dd("STOP PLAYING EVENT: Previous Playing Card:" + previousPlayingGuid)
        stopPlayingCard(previousPlayingGuid)
    }

    // Update current playing card id
    currentPlayingGuid = guid   
}