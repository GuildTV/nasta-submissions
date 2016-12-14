@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">Uploaded Files</div>

        <div class="panel-body">
          <table class="table">
            <thead>
              <th>Category</th>
              <th>Name</th>
              <th>Uploaded At</th>
              <th>Late</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($files as $file)
              <tr>
                <td>{{ $file->category ? $file->category->name : " - " }}</td>
                <td>{{ $file->name }}</td>
                <td>{{ $file->uploaded_at->toDayDateTimeString() }}</td>
                <td>{{ $file->isLate() ? "Yes" : "No" }}</td>
                <td>
                  @if ($file->category == null || $file->category->canEditSubmissions())
                  <button class="btn btn-primary">Link</button>
                  <button class="btn btn-danger">Delete</button>
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
