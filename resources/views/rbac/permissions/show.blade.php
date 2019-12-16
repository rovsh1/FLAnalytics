@extends('layouts.app')

@section('template_title')
  Showing Permission {{ $model->name }}
@endsection

@section('template_linked_css')
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
    <style type="text/css" media="screen">
      .permission-table {
          border: 0;
      }
      .user-table tr th {
          border: 0 !important;
      }
      .user-table tr th:first-child,
      .user-table tr td:first-child {
          padding-left: 15px;
      }
      .user-table tr th:last-child,
      .user-table tr td:last-child {
          padding-right: 15px;
      }
      .user-table .table-responsive,
      .user-table .table-responsive table {
          margin-bottom: 0;
          border-top: 0;
          border-left: 0;
          border-right: 0;
      }
    </style>
@endsection

@section('content')

  <div class="container">

    <div class="row">
      <div class="col-md-10 col-md-offset-1">

        @include('users.partials.form-status')

        <div class="panel panel-default">
          <div class="panel-heading">

            {{ $model->name }}'s Information

              <a href="/rbac/permissions" class="btn btn-info btn-xs pull-right">
                  <i class="fa fa-fw fa-mail-reply" aria-hidden="true"></i>
                  <span class="hidden-sm hidden-xs">Back to </span><span class="hidden-xs">Permissions</span>
              </a>

          </div>
          <div class="panel-body no-padding user-table">
            <table class="table table-borderless table-responsive">
                <thead>
                    <tr>
                      <th>Id</th>
                        <th>Name</th>
                        <th>Slug</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="vertical-align: middle;">
                            {{ $model->id }}
                        </td>
                        <td style="vertical-align: middle;">
                            {{ $model->name }}
                        </td>
                        <td style="vertical-align: middle;">
                            {{ $model->slug }}
                        </td>
                    </tr>
                </tbody>
            </table>
          </div>
          <div class="panel-footer">
            <div class="row">
              <div class="col-xs-6">
                <a href="/rbac/permissions/{{$model->id}}/edit" class="btn btn-small btn-info btn-block">
                  <i class="fa fa-pencil fa-fw" aria-hidden="true"></i> Edit<span class="hidden-xs hidden-sm"> this</span><span class="hidden-xs"> Permission</span>
                </a>
              </div>
              {!! Form::open(array('url' => 'rbac/permissions/' . $model->id, 'class' => 'col-xs-6')) !!}
                {!! Form::hidden('_method', 'DELETE') !!}
                {!! Form::button('<i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> Delete<span class="hidden-xs hidden-sm"> this</span><span class="hidden-xs"> permission</span>', array('class' => 'btn btn-danger btn-block btn-flat','type' => 'submit', 'data-toggle' => 'modal', 'data-target' => '#confirmDelete', 'data-title' => 'Delete permission', 'data-message' => 'Are you sure you want to delete this permission ?')) !!}
              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


@endsection
