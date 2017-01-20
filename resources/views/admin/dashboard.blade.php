@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">Stations</div>

        <div class="panel-body">
          <div class="center">
            <a class="btn btn-default" href="{{ route("admin.submissions") }}">Submissions</a>
            <a class="btn btn-default" href="">Unmatched files</a>
            <a class="btn btn-default" href="">Users</a>
          </div>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">Service Auth</div>

        <div class="panel-body">
          <div class="center">
            <a class="btn btn-default" href="{{ route("admin.dropboxauth") }}">Dropbox</a>
            <a class="btn btn-default" href="{{ route("admin.googleauth") }}">Google</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
