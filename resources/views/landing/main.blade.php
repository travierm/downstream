@extends('layouts.master')

@section('content')
    <div class="container mt-4">
        <section class="jumbotron text-center mb-2 px-0">
            <div class="row justify-content-md-center mobile-br">
                <div class="col-lg-12 mt-2 px-0">

                    <div class="feature-col">
                        <i class="fas fa-compact-disc"></i><span class="ml-2">Collect</span>
                    </div>

                    <div class="feature-col">
                        <i class="ml-3 fas fa-search"></i><span class="ml-2">Discover</span>
                    </div>

                    <div class="feature-col">
                        <i class="ml-3 fas fa-play"></i><span class="ml-2">Play</span>
                    </div>

                    <p class="service-tagline pt-2 mb-2">A music service designed to help you collect and discover whatever
                        type of music you enjoy listening to.</p>
                </div>
            </div>
        </section>

        @if (Auth::guest())
            <div class="row justify-content-md-center">
                <div class="col-lg-2 col-sm-6 mt-2">
                    <a href="/register" class="btn btn-primary btn-block">Register</a>
                </div>
            </div>
        @endif

        @if ($items)
            <div class="row">
                @foreach ($items as $item)
                    <div class="col-md-4">
                        <x-media-card :video-id="$item->index" :thumbnail="$item->meta->thumbnail" :title="$item->meta->title" />
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
