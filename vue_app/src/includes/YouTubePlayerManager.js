import _ from 'lodash';

const _DEBUG = true;

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

    return registeredCards[nextIndex];
}

function findCardIndexById(cardId) {
    let index = _.findIndex(registeredCards, (card) => {
        return card.cardId == cardId
    })

    return (index ? index : false);
}

function findCardById(cardId) {
    const index = findCardIndexById(cardId);

   return (index ? registeredCards[index] : false);
}

export function registerCardPlayer(player) {
    registeredCards.push(player);

    dd("Registered player from cardId " + player.cardId + " for videoId " + player.videoId);
}


export function playNextCard() {
    let nextCard = getNextCard(playingCardId);

    nextCard.play();
}

export function playEvent(cardId) {
    playingCardId = cardId;
}

export function pauseEvent(cardId) {

}

export function stopEvent(cardId) {

}