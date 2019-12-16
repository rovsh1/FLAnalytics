@extends('layouts.app')

@section('template_title')
  Editing User {{ $user->name }}
@endsection

@section('template_linked_css')
  <style type="text/css">
    .btn-save,
    .pw-change-container {
      display: none;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">

@endsection

@section('content')
  @if (\Session::has('success'))
    <div class="alert alert-success">
      <p>{{ \Session::get('success') }}</p>
    </div>
  @endif
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
        <h2 class="col-lg-12">Account</h2>
        {!! Form::model($user, array('action' => array('Auth\UsersController@update', $user->id), 'method' => 'PUT', 'files' => true)) !!}

        <div class="personal_information col-lg-12">
            <div class="site_info_add">

              <div class="personal_block_info col-lg-8 col-lg-offset-1">

                {!! csrf_field() !!}
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
                <div class="row row form-group">
                  <div class="name col-lg-4">
                    Select role
                  </div>
                  <div class="col-lg-8">
                    <select name="role[]" id="roles" class="form-control" multiple="multiple">
                      <?php foreach($allroles as $key => $role):?>
                        <option value="{{ $key }}" <?= (in_array($key, $roles)) ? 'selected' : ''?>>{{ $role }}</option>
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
            {!! Form::button('save', array('class' => 'save btn btn-success pull-right','type' => 'button', 'data-toggle' => 'modal', 'data-target' => '#confirmSave', 'data-title' => 'Сохранить', 'data-message' =>  'Are you sure want to save? ')) !!}
          </div>
        </div>


        {!! Form::close() !!}

      </div>
    </div>
  </section>

  @include('users.modals.modal-save')
  @include('users.modals.modal-delete')

@endsection

@section('template_scripts')

  @include('users.scripts.delete-modal-script')
  @include('users.scripts.save-modal-script')

  <script type="text/javascript">
    $('.btn-change-pw').click(function(event) {
      event.preventDefault();
      $('.pw-change-container').slideToggle(100);
      $(this).find('.fa').toggleClass('fa-times');
      $(this).find('.fa').toggleClass('fa-lock');
      $(this).find('span').toggleText('', 'Cancel');
    });
    $("input").keyup(function() {
      if(!$('input').val()){
          $(".btn-save").hide();
      }
      else {
          $(".btn-save").show();
      }
    });
  </script>
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