@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
  <div class="row">
    <div class="col-lg-12">
        <h3>Users</h3>
        <p>A list of all user accounts.</p>
        <table id="userListTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Display Name</th>
                    <th>Collection Size</th>
                    <th>Last Collected Item Date</th>
                    <th>Account Created</th>
                </tr>
            </thead>

            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ ucfirst($user->display_name) }}</td>
                    <td>{{ ($user->collectionSize ? $user->collectionSize : 'No Collection') }}</td>
                    <td>{{ ($user->lastCollectedItemDate ? $user->lastCollectedItemDate : 'No Collection') }}</td>
                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
  </div>
</div>
@endsection