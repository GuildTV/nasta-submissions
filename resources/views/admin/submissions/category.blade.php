@extends('layouts.app')

@section('js')
$('#submissions-table').DataTable({
  paging: false,
});
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">Submissions for '{{ $category->name }}'</div>

        <div class="panel-body">

          <table class="table" id="submissions-table">
            <thead>
              <th>Station</th>
              <th>Status</th>
              <th>No. of files</th>
              <th>Updated At</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($users as $station)
<?php 
  $entry = $entries->first(function($v, $k) use ($station) { return $v->station_id == $station->id; }); 
  $msg = $entry == null ? " - " : ($entry->submitted ? ($entry->isLate($category) ? "Late" : "Submitted") : "Draft");
?>
              <tr>
                <td>{{ $station->name }}</td>
                <td>{{ $msg }}</td>
                <td>{{ $entry == null ? "" : count($entry->uploadedFiles) }}</td>
                <td>{{ $entry == null ? "" : $entry->updated_at->toDayDateTimeString() }}</td>
                <td>
                  @if ($entry != null)
                  <a href="{{ route('admin.submissions.view', [$station, $category]) }}">View</a>
                  @endif
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
