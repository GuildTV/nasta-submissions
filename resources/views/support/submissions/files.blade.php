@extends('layouts.app')

@section('js')
$.fn.dataTable.moment( 'ddd, MMM D, YYYY h:mm A' ); // Thu, Dec 25, 1975 2:15 PM

$('#files-table').DataTable({
  paging: false,
  order: [[ 3, 'desc' ]],
  columnDefs: [ {
    targets: 1,
    render: $.fn.dataTable.render.ellipsis( 20 )
  }, {
    targets: 2,
    render: $.fn.dataTable.render.ellipsis( 40 )
  } ]
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
              <th data-type="moment-ddd, MMM D, YYYY h:mm A">Uploaded At</th>
              <th>Rule break</th>
              <th>Replaced</th>
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
                <td>{{ $file->replacement_id == null ? "-" : ("#" . $file->replacement_id) }}</td>
                <td>
                  <a class="btn btn-primary" href="{{ route('support.submissions.file', $file) }}">View</button>
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
