@extends('layouts.app')

@section('js')

window.StationSettings.BindValidator();

@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel panel-default">
        <div class="panel-heading">Settings</div>

        <div class="row">
          <div class="col-md-11 col-md-offset-1">
            <form id="entryform" class="form-horizontal" onsubmit="return false">

              <div class="form-group">
                <label for="username" class="col-sm-3 control-label">Name</label>
                <div class="col-sm-9">
                  <p>{{ $user->name }}</p>
                </div>
              </div>

              <div class="form-group">
                <label for="useremail" class="col-sm-3 control-label">Email</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="useremail" name="useremail" maxlength="255" value="{{ $user->email }}" />
                </div>
              </div>

              <div class="form-group">
                <label for="userpassword" class="col-sm-3 control-label">New Password</label>
                <div class="col-sm-9">
                  <input type="password" class="form-control" id="userpassword" name="userpassword" maxlength="100" />
                </div>
              </div>
              <div class="form-group">
                <label for="userpassword" class="col-sm-3 control-label">Confirm Password</label>
                <div class="col-sm-9">
                  <input type="password" class="form-control" id="userpassword_confirm" name="userpassword_confirm" maxlength="100" />
                </div>
              </div>

            <div class="form-group">
              <div class="col-sm-offset-7 col-sm-2">
                <button type="submit" class="btn btn-success" id="usersave">Save</button>
              </div>
            </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
