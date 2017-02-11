@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">View User '{{ $user->name }}'</div>

        <div class="row">
          <div class="col-md-11 col-md-offset-1">
            <form id="entryform" class="form-horizontal" onsubmit="window.AdminUsers.Submit();return false">
              
              <input type="hidden" id="userid" name="userid" value="{{ $user->id }}" />

              <div class="form-group">
                <label for="username" class="col-sm-3 control-label">Name</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="username" name="username" maxlength="255" value="{{ $user->name }}" />
                </div>
              </div>

              <div class="form-group">
                <label for="usercompactname" class="col-sm-3 control-label">Compact Name</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="usercompactname" name="usercompactname" maxlength="255" value="{{ $user->compact_name }}" />
                </div>
              </div>

              <div class="form-group">
                <label for="userusername" class="col-sm-3 control-label">Username</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="userusername" name="userusername" maxlength="255" value="{{ $user->username }}" />
                </div>
              </div>

              <div class="form-group">
                <label for="useremail" class="col-sm-3 control-label">Email</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" id="useremail" name="useremail" maxlength="255" value="{{ $user->email }}" />
                </div>
              </div>

              <div class="form-group">
                <label for="userpassword" class="col-sm-3 control-label">New Password</label>
                <div class="col-sm-6">
                  <input type="password" class="form-control" id="userpassword" name="userpassword" maxlength="100" />
                </div>
              </div>
              <div class="form-group">
                <label for="userpassword" class="col-sm-3 control-label">Confirm Password</label>
                <div class="col-sm-6">
                  <input type="password" class="form-control" id="userpassword_confirm" name="userpassword_confirm" maxlength="100" />
                </div>
              </div>

              <div class="form-group">
                <label for="userusername" class="col-sm-3 control-label">Last Login</label>
                <div class="col-sm-6">
                  <input type="text" class="form-control" disabled="disabled" value="{{ $user->last_login_at == null ? "-" : $user->last_login_at->toDayDateTimeString() }}" />
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

      @if ($user->type == "station")
        @include("admin.users.view-station")

      @elseif ($user->type == "judge")
        @include("admin.users.view-judge")

      @elseif ($user->type == "admin")
        @include("admin.users.view-admin")

      @else
        <p>UNKNOWN USER TYPE</p>
      @endif

    </div>
  </div>
</div>
@endsection
