@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading"></div>

        <div class="panel-body">
          <div class="center">
            <a class="btn btn-default" href="{{ route("support.submissions") }}">Submissions</a>
            <a class="btn btn-default" href="{{ route('support.submissions.files') }}">Uploaded files</a>
            <a class="btn btn-default" href="{{ route('admin.users') }}">Users</a>
            <a class="btn btn-default" href="{{ route('support.rule-break.errors') }}">Rule break errors</a>
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

      <div class="panel panel-default">
        <div class="panel-heading">Info</div>

        <div class="panel-body">
          <div class="center">
            <p>Revision: {{ $version }}</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
