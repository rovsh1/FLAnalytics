<!-- index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <br />
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <p>{{ \Session::get('success') }}</p>
        </div><br />
    @endif
    <a href="{{action('UrlListController@create')}}" class="btn btn-success">Create</a>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Url</th>
                <th colspan="2">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($urls as $item)
                <tr>
                    <td>{{$item['id']}}</td>
                    <td>{{$item['name']}}</td>
                    <td>{{$item['url']}}</td>
                    <td><a href="{{action('UrlListController@edit', $item['id'])}}" class="btn btn-warning">Edit</a></td>
                    <td>
                        <form action="{{action('UrlListController@destroy', $item['id'])}}" method="post">
                            {{csrf_field()}}
                            <input name="_method" type="hidden" value="DELETE">
                            <button class="btn btn-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop