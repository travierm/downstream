@extends('layouts.master')
@section('content')
    <div class="container-fluid pushFromTop">
        <div class="row">
            @foreach ($items as $item)
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <x-media-card :video-id="$item->index" :thumbnail="$item->thumbnail" :title="$item->title" />
                </div>
            @endforeach
        </div>
    </div>
@endsection
