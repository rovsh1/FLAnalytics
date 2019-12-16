@extends('layouts.app')

@section('template_title')
  Edit New Permission Groups
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
        <h2 class="col-lg-12">Edit Permission Group {{$model->name}}</h2>

      </div>
      <div class="row">

        {!! Form::model($model, array('action' => array('Auth\Rbac\PermissionGroupsController@update', $model->id), 'method' => 'PUT', 'files' => true)) !!}
        {!! csrf_field() !!}

        <div class="row">
          <div class="col-lg-6">
            {!! Form::text('name', old('name'), array('id' => 'permission-name', 'class' => 'form-control', 'placeholder' =>'Permission name')) !!}
          </div>
          <div class="col-lg-6">
            {!! Form::text('module', old('module'), array('id' => 'permission-group-slug', 'class' => 'form-control', 'placeholder' =>'Permission group slug')) !!}
          </div>

          @ifUserIs('admin')
          <div class="col-lg-12">
            <select name="permission[]" id="permissions" class="form-control" multiple="multiple">
                <?php foreach($allpermissions as $key => $permission):?>
              <option value="{{ $key }}" <?= (in_array($key, $permissions)) ? 'selected' : ''?>>{{ $permission }}</option>
                <?php endforeach?>
            </select>
          </div>
          @endif
          <div class="col-lg-12">
            {!! Form::button( 'Edit permission group', array('class' => 'btn btn-success btn-flat margin-bottom-1 ','type' => 'submit', )) !!}
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