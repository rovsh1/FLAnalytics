@extends('layouts.app')

@section('template_title')
  Showing Rbac roles
@endsection

@section('template_linked_css')
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
    <style type="text/css" media="screen">
        .roles-table {
            border: 0;
        }
        .roles-table tr td:first-child {
            padding-left: 15px;
        }
        .roles-table tr td:last-child {
            padding-right: 15px;
        }
        .roles-table.table-responsive,
        .roles-table.table-responsive table {
            margin-bottom: 0;
        }

    </style>
@endsection

@section('content')
    <div class="">
        <div class="">

            @include('users.partials.form-status')
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            Showing All Roles
                            <a href="/rbac/roles/create" class="btn btn-default btn-sm pull-right">
                                <i class="fa fa-fw fa-role-plus" aria-hidden="true"></i>
                                Create New Role
                            </a>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="table-responsive roles-table">
                            <table class="table table-striped table-condensed data-table">
                                <thead>
                                    <tr>
                                        <td>Name</td>
                                        <td class="hidden-xs">slug</td>
                                        <td colspan="3" align="center">Actions</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($models as $model)
                                        <tr>
                                            <td>{{$model->name}}</td>
                                            <td class="hidden-xs">{{$model->slug}}</td>
                                            <td>
                                                {!! Form::open(array('url' => 'rbac/roles/' . $model->id, 'class' => '')) !!}
                                                    {!! Form::hidden('_method', 'DELETE') !!}
                                                    {!! Form::button('<i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> Delete<span><span class="hidden-xs hidden-sm"> this</span><span class="hidden-xs"> role</span>', array('class' => 'btn btn-danger btn-sm','type' => 'submit', 'style' =>'width: 100%;' ,'data-toggle' => 'modal', 'data-target' => '#confirmDelete', 'data-title' => 'Delete role', 'data-message' => 'Are you sure you want to delete this role ?')) !!}
                                                {!! Form::close() !!}
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-success btn-block" href="{{ URL::to('rbac/roles/' . $model->id) }}">
                                                    <i class="fa fa-eye fa-fw" aria-hidden="true"></i> <span>Show</span> <span class="hidden-sm hidden-xs">this</span> <span class="hidden-xs">role</span>
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-sm btn-info btn-block" href="{{ URL::to('rbac/roles/' . $model->id . '/edit') }}">
                                                    <i class="fa fa-pencil fa-fw" aria-hidden="true"></i> <span>Edit</span> <span class="hidden-sm hidden-xs">this</span> <span class="hidden-xs">role</span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
