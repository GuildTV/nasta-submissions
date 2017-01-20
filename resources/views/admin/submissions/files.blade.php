@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">Unmatched files</div>

        <div class="panel-body">
          <table class="table" id="files-table">
            <thead>
              <th>Station</th>
              <th>Filename</th>
              <th>Uploaded At</th>
              <th>Invalid</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($files as $file)
              <tr>
                <td>{{ $file->station->name }}</td>
                <td>{{ $file->name }}</td>
                <td>{{ $file->uploaded_at->toDayDateTimeString() }}</td>
                <td>-</td>
                <td>
                  <a class="btn btn-primary" >View</button>
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
