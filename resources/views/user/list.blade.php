@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
  <div class="row">
    <div class="col-lg-12">
        <h3>Top Users</h3>
        <p>A list of our most active users with public accounts.</p>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Recent Art</th>
                    <th>Display Name</th>
                    <th>Collection Size</th>
                    <th>Follow</th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $user)
                <tr>
                <td class="text-center">
                        @if ($user->thumbnail)
                            <img width="80" class="img-fluid" src="{{ $user->thumbnail }}"/>
                        @else
                            <img height="300" width="300" class="img-fluid" src="https://via.placeholder.com/640x480/000000?text=No Items"/>
                        @endif
                    </td>
                    <td>{{ $user->display_name }}</td>
                    <td>{{ $user->collectionSize }}</td>
                <td><follow-button following="{{ $user->following }}" :user="{{ $user->id }}" ></follow-button></td>
                    <!-- <td><a href="/user/{{ $user->hash }}/profile" class="btn btn-outline-success">View collection</a></td> -->
                </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
    
  </div>
</div>
@endsection