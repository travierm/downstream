@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
  <div class="row">
    @foreach($users as $user)
    <div class="col-lg-3 mr-4 mb-4">
        <!-- Card -->
        <div class="card" style="width: 18rem;">
            <div class="card-body">
                @if ($user->thumbnail)
                    <img class="card-img-top" src="{{ $user->thumbnail }}"/>
                @else
                    <img class="card-img-top" src="https://via.placeholder.com/640x480/000000?text=No Items"/>
                @endif

                
                <h5 class="card-title">{{ $user->display_name }}</h5>
                <p class="card-text">Collection Size: {{ $user->collectionSize }}</p>
                <a href="/user/{{ $user->hash }}/profile" class=" btn btn-outline-primary">See collection</a>
            </div>
        </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
