@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
  <div class="alert alert-success" role="alert">
    <h3 class="alert-heading">Welcome!</h3>
    <h4 style="color:black;">{{$displayName}}</h4>
    <p>We've generated a unique hash for you..</p>
    <a href="" class="alert-link">{{$hash}}</a>
    <p class="mb-0">This hash will be used to identify you on the network. Your display name is set to help your friends know what hash is who.</p>
  </div>

  <router-link class="btn btn-outline-success" to="/frontpage">Frontpage</router-link>
  <a href="/search" class="btn btn-outline-warning">Search</a>
</div>
@endsection
