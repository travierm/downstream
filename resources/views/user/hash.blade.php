@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
  <div class="alert alert-success" role="alert">
    <h4 class="alert-heading">You're Registered!</h4>
    <p>We've generated a unique hash for you. Use it to login to this account in the future!</p>
    <a href="" class="alert-link">{{$hash}}</a>
    <hr>
    <p class="mb-0">We use hashes instead of usernames to keep things interesting. You'll be able to set a display name for your friends to see soon!</p>
  </div>

  <button class="btn btn-outline-success">Import Songs</button>
</div>
@endsection
