@extends('layouts.app')

@section('template_title')
  Showing Role {{ $model->name }}
@endsection

@section('template_linked_css')
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css">
    <style type="text/css" media="screen">
      .role-table {
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

  <div class="">

    <div class="">
      <div class="col-md-10 col-md-offset-1">

        @include('users.partials.form-status')

        <div class="panel panel-default">
          <div class="panel-heading">

            {{ $model->name }}'s Information

              <a href="/rbac/roles" class="btn btn-info btn-xs pull-right">
                  <i class="fa fa-fw fa-mail-reply" aria-hidden="true"></i>
                  <span class="hidden-sm hidden-xs">Back to </span><span class="hidden-xs">Roles</span>
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
              @if($permissions != false)
                  <h4>Attached permissions</h4>
                  <table class="table table-borderless table-responsive">
                      <thead>
                      <tr>
                          <th>Id</th>
                          <th>Name</th>
                          <th>Slug</th>
                      </tr>
                      </thead>
                      <tbody>
                      @foreach ($permissions as $permission)
                          <tr>
                              <td style="vertical-align: middle;">
                                  {{ $permission->id }}
                              </td>
                              <td style="vertical-align: middle;">
                                  {{ $permission->name }}
                              </td>
                              <td style="vertical-align: middle;">
                                  {{ $permission->slug }}
                              </td>
                          </tr>
                      @endforeach
                      </tbody>
                  </table>
              @endif
              @if($permissiongroups != false)
                  <h4>Attached permission groups</h4>
                  <table class="table table-borderless table-responsive">
                      <thead>
                      <tr>
                          <th>Id</th>
                          <th>Name</th>
                          <th>module</th>
                      </tr>
                      </thead>
                      <tbody>
                      @foreach ($permissiongroups as $permissiongroup)
                          <tr>
                              <td style="vertical-align: middle;">
                                  {{ $permissiongroup->id }}
                              </td>
                              <td style="vertical-align: middle;">
                                  {{ $permissiongroup->name }}
                              </td>
                              <td style="vertical-align: middle;">
                                  {{ $permissiongroup->module }}
                              </td>
                          </tr>
                      @endforeach
                      </tbody>
                  </table>
              @endif
          </div>
          <div class="panel-footer">
            <div class="row">
              <div class="col-xs-6">
                <a href="/rbac/roles/{{$model->id}}/edit" class="btn btn-small btn-info btn-block">
                  <i class="fa fa-pencil fa-fw" aria-hidden="true"></i> Edit<span class="hidden-xs hidden-sm"> this</span><span class="hidden-xs"> Role</span>
                </a>
              </div>
              {!! Form::open(array('url' => 'rbac/roles/' . $model->id, 'class' => 'col-xs-6')) !!}
                {!! Form::hidden('_method', 'DELETE') !!}
                {!! Form::button('<i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> Delete<span class="hidden-xs hidden-sm"> this</span><span class="hidden-xs"> role</span>', array('class' => 'btn btn-danger btn-block btn-flat','type' => 'submit', 'data-toggle' => 'modal', 'data-target' => '#confirmDelete', 'data-title' => 'Delete role', 'data-message' => 'Are you sure you want to delete this role ?')) !!}
              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


@endsection
