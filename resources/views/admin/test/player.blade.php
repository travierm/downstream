@extends('layouts.master')

@section('content')
<div class="container-fluid pushFromTop">
    <h1>Player Test Ground</h1>
    <div class="row mt-2">
        <div class="col-lg-3">
            <youtube-card :collected="true"  video-id="{{ $media->index }}" title="{{ $media->getMeta()->title }}" thumbnail="{{$media->getMeta()->thumbnail}}"></video-player-card>
        </div>
    </div>
</div>
@endsection
