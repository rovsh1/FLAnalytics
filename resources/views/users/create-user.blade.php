@extends('layouts.app')

@section('template_title')
  Create New User
@endsection

@section('template_linked_css')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
@endsection

@section('content')
  @if (\Session::has('errors'))
    @foreach ($errors->all() as $error)
      <div class="alert alert-success">
        <p>{{ $error }}</p>
      </div>
    @endforeach

  @endif
  <section class="personal_cabinet">
    <div class="container">
      <div class="row">
        <h2 class="col-lg-12">Create New User</h2>
        <a href="/users" class="btn btn-info btn-xs pull-right">
          <i class="fa fa-fw fa-mail-reply" aria-hidden="true"></i>
          <span class="hidden-sm hidden-xs">Back to </span><span class="hidden-xs">Users</span>
        </a>
        @include('users.partials.form-status')
        {!! Form::open(array('action' => 'Auth\UsersController@store', 'method' => 'POST', 'role' => 'form','files' => true)) !!}

        {!! csrf_field() !!}

        <div class="personal_information col-lg-12">
          <div class="site_info_add">
            <div class="personal_block_info col-lg-8 col-lg-offset-1">

              <h3>Main information</h3>
              <div class="row">
                <div class="name col-lg-4">
                  Name
                </div>
                <div class="input_block col-lg-8">
                  {!! Form::text('name', old('name'), array('id' => 'user-name', 'class' => 'form-control', 'placeholder' =>'Your name')) !!}

                </div>
              </div>
              <div class="row">
                <div class="name col-lg-4">
                  Email
                </div>
                <div class="input_block col-lg-8">
                  {!! Form::text('email', old('email'), array('id' => 'email', 'class' => 'form-control', 'placeholder' => "example@mail.com")) !!}

                </div>
              </div>
              <h3 class="change_pass">Password</h3>
              <div class="row form-group">
                <div class="name col-lg-4">
                  New password
                </div>
                <div class="input_block col-lg-8">
                  {!! Form::password('password', array('id' => 'user-new-pass', 'class' => 'form-control ', 'placeholder' => '')) !!}

                </div>
              </div>
              <div class="row form-group">
                <div class="name col-lg-4">
                  Repeat password
                </div>
                <div class="input_block col-lg-8">
                  {!! Form::password('password_confirmation', array('id' => 'user-new-pass-repeat', 'class' => 'form-control', 'placeholder' =>'')) !!}
                  <span>Min password length 6 symbols</span>
                </div>
              </div>
              @ifUserIs('admin')
              <div class="row form-group">
                <div class="name col-lg-4">
                  Select role
                </div>
                <div class="col-lg-8">
                  <select name="role[]" id="roles" class="form-control" multiple="multiple">
                    <?php foreach($allroles as $key => $role):?>
                    <option value="{{ $key }}">{{ $role }}</option>
                    <?php endforeach?>
                  </select>
                </div>
              </div>
              @endif

            </div>
          </div>
        </div>
        <div class="row form-group">
          <div class="col-md-8 col-offset-4">
            {!! Form::button( 'save', array('class' => 'btn btn-success btn-flat margin-bottom-1 pull-right','type' => 'submit', )) !!}
          </div>
        </div>

        {!! Form::close() !!}

      </div>
    </div>
  </section>
  <script src="/vendor/laravel-filemanager/js/lfm.js"></script>
  <script>
    var options = {
      filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
      filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{ csrf_token() }}',
      filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
      filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{ csrf_token() }}'
    };

    $('#main_image').filemanager('image');
  </script>
@endsection

@section('template_scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
  <script>
    $(document).ready(function(){
      $('#roles').select2({
        placeholder : 'Please select role',
        tags: true
      });
    });
  </script>
@endsection