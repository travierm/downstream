@extends('layouts.master')
@section('content')
    <div class="container-fluid pushFromTop">
        <div id="collection" class="row">
            @fragment('item-list')
                @foreach ($items as $item)
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <x-media-card :video-id="$item->index" :thumbnail="$item->thumbnail" :title="$item->title" />
                    </div>
                @endforeach
            @endfragment
        </div>

        <!-- Once the browser sees this element it will fetch the next results -->
        <div id="loader" style="min-height: 100px;"></div>
    </div>
@endsection
