@extends('layouts.app')

@section('js')
$('#stations-table').DataTable({
  paging: false,
  order: [[ 1, 'desc' ]],
});

$('#awards-table').DataTable({
  paging: false,
  order: [[ 1, 'desc' ]],
});
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">All Submissions</div>

        <div class="panel-body">
          <div class="center">
            <a class="btn btn-default" href="{{ route("admin.submissions.all") }}">All Submisions</a>
          </div>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">Submissions by Station</div>

        <div class="panel-body">
          <table class="table" id="stations-table">
            <thead>
              <th>Name</th>
              <th>No. of entries</th>
              <th>No. of submitted</th>
              <th>No. of late</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($users as $station)
              <tr>
                <td>{{ $station->name }}</td>
                <td>{{ $station->entries->count() }}</td>
                <td>{{ $station->entries->where('submitted', true)->count() }}</td>
                <td>{{ $station->entries->where('submitted', true)->filter(function($v, $k){ return $v->isLate(); })->count() }}</td>
                <td>
                  <a href="{{ route('admin.submissions.station', $station) }}">View</a>
                </td>
              </tr>
@endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">Submissions by Award</div>

        <div class="panel-body">
          <table class="table" id="awards-table">
            <thead>
              <th>Name</th>
              <th>No. of entries</th>
              <th>No. of submitted</th>
              <th>No. of late</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($categories as $cat)
              <tr>
                <td>{{ $cat->name }}</td>
                <td>{{ $cat->entries->count() }}</td>
                <td>{{ $cat->entries->where('submitted', true)->count() }}</td>
                <td>{{ $cat->entries->where('submitted', true)->filter(function($v, $k) use ($cat) { return $v->isLate($cat); })->count() }}</td>
                <td>
                  <a href="{{ route('admin.submissions.category', $cat) }}">View</a>
                </td>
              </tr>
@endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
