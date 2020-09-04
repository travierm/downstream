@extends('layouts.master')

@section('content')
<div class="container pushFromTop">
    <div class="row" style="margin-top: 10px;">
        <div class="col-lg-6">
            <form method="POST">
                @csrf

                <input placeholder="Filter Value" class="form-control" type="text" name="filter_value">
                <button class="btn btn-success">Create</button>
            </form>

        <table class="table">
            <tr>
                <th>Title</th>
            </tr>

            @foreach($filters as $value)
            <tr>
                <td>{{ $value }}</td>
            </tr>
            @endforeach
        </table>
        </div>
    </div>

    <div class="row" style="margin-top: 10px;">

    </div>
</div>
@endsection
