@extends('layouts.app')

@section('template_title')
  Edit New Permission
@endsection


@section('content')
  @include('users.partials.form-status')
  <section class="personal_cabinet">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <a href="/rbac/permissions" class="btn btn-info btn-xs pull-left">
            <i class="fa fa-fw fa-mail-reply" aria-hidden="true"></i>
            <span class="hidden-sm hidden-xs">Back to </span><span class="hidden-xs">Permissions</span>
          </a>
        </div>
        <h2 class="col-lg-12">Edit Permission {{$model->name}}</h2>

      </div>
      <div class="row">

        {!! Form::model($model, array('action' => array('Auth\Rbac\PermissionsController@update', $model->id), 'method' => 'PUT', 'files' => true)) !!}
        {!! csrf_field() !!}

        <div class="row">
          <div class="col-lg-4">
            {!! Form::text('name', old('name'), array('id' => 'permission-name', 'class' => 'form-control', 'placeholder' =>'Permission name')) !!}
          </div>
          <div class="col-lg-4">
            {!! Form::text('slug', old('slug'), array('id' => 'permission-slug', 'class' => 'form-control', 'placeholder' =>'Permission slug')) !!}
          </div>
          <div class="col-lg-4">
            {!! Form::button( 'Edit permission', array('class' => 'btn btn-success btn-flat margin-bottom-1 ','type' => 'submit', )) !!}
          </div>
        </div>
        {!! Form::close() !!}
      </div>
    </div>
  </section>
@endsection
