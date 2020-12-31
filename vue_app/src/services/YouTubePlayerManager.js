import _ from 'lodash';

const _DEBUG = false;

let guidIndex = []
let registeredCards = [];
let currentPlayingGuid = false

function dd(data) {
    if(_DEBUG) {
        console.log(data);
    }
}

function getNextCard(playingGuid) {
    const currentIndex = findIndexByGuid(playingGuid)
    const nextIndex = currentIndex >= guidIndex.length - 1 ? 0 : currentIndex + 1
    const nextGuid = guidIndex[nextIndex]

    return findCardByGuid(nextGuid)
}

function findCardByGuid(guid) {
    return _.find(registeredCards, { guid })
}

function findIndexByGuid(guid) {
    return guidIndex.indexOf(guid)
}

function stopPlayingCard(guid) {
    let playingCard = findCardByGuid(guid)

    if (playingCard) {
        playingCard.stop()
    }
}

export function getPlayingCardId() {
    return currentPlayingGuid;
}

export function registerCardPlayer(player) {
    registeredCards.push(player);

    //dd("Registered player from cardId " + player.cardId + " for videoId " + player.videoId)
}

export function setGuidIndex(index) {
    guidIndex = index;
}

export function playNextCard() {
    if(guidIndex.length <= 0) {
        console.error("Can not play next card since guidIndex is empty")
        return
    }

    let nextCard = getNextCard(currentPlayingGuid);

    dd("PLAYING " + nextCard.guid)

    nextCard.play(true)
}

export function playEvent(guid) {
    const previousPlayingGuid = _.clone(currentPlayingGuid);

    
    if (previousPlayingGuid) {
        // Stop playing previous card because a new one would like to play
        dd("STOP PLAYING EVENT: Previous Playing Card:" + previousPlayingGuid)
        stopPlayingCard(previousPlayingGuid)
    }

    // Update current playing card id
    currentPlayingGuid = guid   
}

export function pauseEvent(cardId) {

}

export function stopEvent(cardId) {

}