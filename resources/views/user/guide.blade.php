@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
    <div class="row">
        <div class="col-lg-12">
            <h1>User Guide</h1>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-12">
            <h3>
                Search
                <small class="text-muted">Adding things to your collection</small>
                <hr>
            </h3>

            <p class="guide-text">Use the search bar above to find items for your Collection.</p>
            <p class="guide-text">After you find an item you like, you may play it directly or click the <button class="btn btn-outline-success">Collect</button> on the top of right of the card.  </p>
        </div>
    </div>

    <!-- <div class="row">
        <div class="col-lg-12">
            <h3>
                Collection
                <small class="text-muted">Playing the things you find</small>
                <hr>
            </h3>
            <p>Now that you collected an item you can replay it anytime from your collection. 
                    <br />
                <router-link class="btn btn-outline-primary" to="/collection">Collection Link</router-link>
                 <br />  <br />
                <h5>What is your Collection?</h5>
                Your collection contains every item you collected while using Downstream.<br />
                <br />
                <h5>Listen again and again!</h5>
                It is also the best place to play your collected items!<br />
                Your collection is ordered by most recent items collected first and will automatically play next item to the right after a track is done.
                <br /><br />
                <h5>Remove an item</h5>
                You may click <button class="btn btn-success">Collected</button> to remove an item from your Collection.
            </p>
        </div>
    </div> -->

    <div class="row mt-2">
        <div class="col-lg-12">
            <h3>
                Example Video Card
                <small class="text-muted">Click the thumbnail to play!</small>
                <hr>
            </h3>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
                <h3>How to use the Card</h3>
                <ul class="list">
                    <li>Click the cards image to play its content.</li>
                    <li>Click again to pause the content.</li>
                    <li>Click Collect to add the item to your Collection.</li>
                    <li>Click Collected to remove an item from your Collection.</li>
                </ul>

                <h3>More Information</h3>
                <ul class="list">
                        <li>It works and plays everywhere!</li>
                        <li>After a card is finished playing it will play its neighbor </li>
                        <li>The Control bar will appear at the bottom of certain pages like Collection. <br /> Use it to play, pause and skip to other items.</li>
                    </ul>
        </div>
        <div class="col-lg-6 mx-auto">
            <youtube-card 
            :media-id="474" 
            title="Triple One - Six Speed"
            video-id="IJkVHwQQF50"
            thumbnail="https://i.ytimg.com/vi/IJkVHwQQF50/sddefault.jpg">
            </youtube-player-card>
        </div>
    </div>
</div>
@endsection
