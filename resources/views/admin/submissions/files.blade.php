@extends('layouts.app')

@section('js')
$('#files-table').DataTable({
  paging: false,
});
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">

      <div class="panel panel-default">
        <div class="panel-heading">Uploaded files</div>

        <div class="panel-body">
          <a class="btn btn-default" href="">Export CSV</a>
          <table class="table" id="files-table">
            <thead>
              <th>Station</th>
              <th>Category</th>
              <th>Filename</th>
              <th>Uploaded At</th>
              <th>Rule break</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($files as $file)
              <tr>
                <td>{{ $file->station->name }}</td>
                <td>{{ $file->category != null ? $file->category->name : "" }}</td>
                <td>{{ $file->name }}</td>
                <td>{{ $file->uploaded_at->toDayDateTimeString() }}</td>
                <td>{{ $file->rule_break == null ? "pending" : $file->rule_break->result }}</td>
                <td>
                  <a class="btn btn-primary" href="{{ route('admin.submissions.file', $file) }}">View</button>
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
