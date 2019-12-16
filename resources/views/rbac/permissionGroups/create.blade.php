@extends('layouts.app')

@section('template_title')
  Create New PermissionGroup
@endsection
@section('template_linked_css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
@endsection

@section('content')
  @include('users.partials.form-status')
  <section class="personal_cabinet">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <a href="/rbac/permission-groups" class="btn btn-info btn-xs pull-left">
            <i class="fa fa-fw fa-mail-reply" aria-hidden="true"></i>
            <span class="hidden-sm hidden-xs">Back to </span><span class="hidden-xs">Permission Groups</span>
          </a>
        </div>
        <h2 class="col-lg-12">Create New Permission Group</h2>


      </div>
      <div class="row">
        {!! Form::open(array('action' => 'Auth\Rbac\PermissionGroupsController@store', 'method' => 'POST', 'permission-group' => 'form','files' => true)) !!}
        {!! csrf_field() !!}

        <div class="row">
          <div class="col-lg-6">
            {!! Form::text('name', old('name'), array('id' => 'permission-group-name', 'class' => 'form-control', 'placeholder' =>'Permission Group name')) !!}
          </div>
          <div class="col-lg-6">
            {!! Form::text('module', old('module'), array('id' => 'permission-group-slug', 'class' => 'form-control', 'placeholder' =>'Permission Group module')) !!}
          </div>

          @ifUserIs('admin')
          <div class="col-lg-12">
            <select name="permission[]" id="permissions" class="form-control" multiple="multiple">
                <?php foreach($allpermissions as $key => $permission):?>
              <option value="{{ $key }}">{{ $permission }}</option>
                <?php endforeach?>
            </select>
          </div>
          @endif
          <div class="col-lg-12">
            {!! Form::button( 'Create new permission group', array('class' => 'btn btn-success btn-flat margin-bottom-1 ','type' => 'submit', )) !!}
          </div>
        </div>
        {!! Form::close() !!}
      </div>



    </div>
  </section>
@endsection
@section('template_scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
  <script>
      $(document).ready(function(){
          $('#permissions').select2({
              placeholder : 'Please select permission',
              tags: true
          });
      });
  </script>
@endsection