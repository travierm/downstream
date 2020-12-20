import _ from 'lodash';

const _DEBUG = false;

let playingCardId = false;
let registeredCards = [];

function dd(data) {
    if(_DEBUG) {
        console.log(data);
    }
}

function getNextCard(currentCardId) {
    let currentIndex = findCardIndexById(currentCardId)
    let nextIndex = (currentIndex >= (registeredCards.length - 1) ? 0 : currentIndex + 1);

    return registeredCards[nextIndex]
}

function findCardIndexById(cardId) {
    let index = _.findIndex(registeredCards, (card) => {
        return card.cardId == cardId
    })

    return index
}

function findCardById(cardId) {
    const index = findCardIndexById(cardId);

   return registeredCards[index]
}

function stopPlayingCard(cardId) {
    let playingCard = findCardById(cardId);

    if(playingCard) {
        playingCard.stop();
    }
}

export function getPlayingCardId() {
    return playingCardId;
}

export function registerCardPlayer(player) {
    registeredCards.push(player);

    dd("Registered player from cardId " + player.cardId + " for videoId " + player.videoId)
}


export function playNextCard() {
    let nextCard = getNextCard(playingCardId);

    nextCard.play(true)
}

export function playEvent(cardId) {
    const previousPlayingCardId = _.clone(playingCardId);

    console.log("PLAY EVENT: Previous Playing Card:" + previousPlayingCardId);
    if (previousPlayingCardId) {
        
        // Stop playing previous card because a new one would like to play
        stopPlayingCard(previousPlayingCardId)
    }

    // Update current playing card id
    playingCardId = cardId   
}

export function pauseEvent(cardId) {

}

export function stopEvent(cardId) {

}