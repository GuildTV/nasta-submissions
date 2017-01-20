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
        <div class="panel-heading">Submissions for '{{ $station->name }}'</div>

        <div class="panel-body">

          <table class="table" id="submissions-table">
            <thead>
              <th>Category</th>
              <th>Status</th>
              <th>No. of files</th>
              <th>Updated At</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($categories as $cat)
<?php 
  $entry = $entries->first(function($v, $k) use ($cat) { return $v->category_id == $cat->id; }); 
  $msg = $entry == null ? " - " : ($entry->submitted ? ($entry->isLate($cat) ? "Late" : "Submitted") : "Draft");
?>
              <tr>
                <td>{{ $cat->name }}</td>
                <td>{{ $msg }}</td>
                <td>{{ $entry == null ? "" : count($entry->uploadedFiles) }}</td>
                <td>{{ $entry == null ? "" : $entry->updated_at->toDayDateTimeString() }}</td>
                <td>
                  @if ($entry != null)
                  <a href="{{ route('admin.submissions.view', [$station, $cat]) }}">View</a>
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
