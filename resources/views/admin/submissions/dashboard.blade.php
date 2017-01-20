@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">Station Submissions</div>

        <div class="panel-body">
          <div class="center">
@foreach ($users as $station)
            <a class="btn btn-default" href="{{ route("admin.submissions.station", $station) }}">{{ $station->name }}</a>
@endforeach
          </div>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">Category Submissions</div>

        <div class="panel-body">
          <div class="center">
@foreach ($categories as $cat)
            <a class="btn btn-default" href="{{ route("admin.submissions.category", $cat) }}">{{ $cat->name }}</a>
@endforeach
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
