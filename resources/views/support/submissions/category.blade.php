@extends('layouts.app')

@section('js')
$.fn.dataTable.moment( 'ddd, MMM D, YYYY h:mm A' ); // Thu, Dec 25, 1975 2:15 PM

$('#submissions-table').DataTable({
  paging: false,
});
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">Submissions for the {{ $category->name }} award</div>

        <div class="panel-body">

          <table class="table" id="submissions-table">
            <thead>
              <th>Station</th>
              <th>Status</th>
              <th>No. of files</th>
              <th data-type="moment-ddd, MMM D, YYYY h:mm A">Updated At</th>
              <th>Rule break</th>
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
                <td>{{ $entry == null ? "" : ($entry->rule_break == null ? "pending" : $entry->rule_break->result) }}</td>
                <td>
                  @if ($entry != null)
                  <a href="{{ route('support.submissions.view', [$station, $category]) }}">View</a>
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
