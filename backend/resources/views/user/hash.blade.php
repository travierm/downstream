@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
  <div class="alert alert-info" role="alert">
    <h3 class="alert-heading">Welcome!</h3>
    <h4 style="color:black;">{{$displayName}}</h4>
    <p>We've generated a unique hash for you..</p>
    <a href="" class="alert-link">{{$hash}}</a>
    <p class="mb-0">This hash will be used to identify you on the network. Your display name is used to help friends find your account!</p>
  </div>

  <a href="/" class="btn btn-outline-info">Frontpage</a>
  <router-link class="btn btn-outline-danger" to="/search">Search</router-link>
</div>
@endsection
