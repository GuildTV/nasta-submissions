@extends('layouts.app')

@section('js')
$('#submissions-table').DataTable({
  paging: false,
});
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">

      <div class="panel panel-default">
        <div class="panel-heading">All Submissions</div>

        <div class="panel-body">

          <table class="table" id="submissions-table">
            <thead>
              <th>Station</th>
              <th>Category</th>
              <th>Status</th>
              <th>No. of files</th>
              <th>Updated At</th>
              <th>Rule break</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($entries as $entry)
<?php 
  $msg = $entry == null ? " - " : ($entry->submitted ? ($entry->isLate() ? "Late" : "Submitted") : "Draft");
?>
              <tr>
                <td>{{ $entry->station->name }}</td>
                <td>{{ $entry->category->name }}</td>
                <td>{{ $msg }}</td>
                <td>{{ $entry == null ? "" : count($entry->uploadedFiles) }}</td>
                <td>{{ $entry == null ? "" : $entry->updated_at->toDayDateTimeString() }}</td>
                <td>{{ $entry->rule_break == null ? "pending" : $entry->rule_break->result }}</td>
                <td>
                  @if ($entry != null)
                  <a href="{{ route('admin.submissions.view', [$entry->station, $entry->category]) }}">View</a>
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
