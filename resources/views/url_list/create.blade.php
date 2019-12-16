<!-- create.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create A Url</h2><br  />
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div><br />
    @endif
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <p>{{ \Session::get('success') }}</p>
        </div><br />
    @endif
    <form method="post" action="{{url('url-list')}}">

        {{csrf_field()}}
        <div class="row">
            <div class="form-group col-md-4">
                <label for="url">Name:</label>
                <input type="text" class="form-control" name="name">
            </div>
            <div class="form-group col-md-4">
                <label for="url">Url:</label>
                <input type="text" class="form-control" name="url">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4"></div>
            <div class="form-group col-md-4">
                <button type="submit" class="btn btn-success" style="margin-left:38px">Add Url</button>
            </div>
        </div>
</form>


</div>
@endsection