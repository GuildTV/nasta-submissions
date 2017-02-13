@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">{{ $station->name }}'s submission for the {{ $category->name }} award</div>

        <div class="row">
          <div class="col-md-12">
            <form id="entryform" class="form-horizontal" onsubmit="return false">

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ $entry->name }}">
                </div>
              </div>
              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Description</label>
                <div class="col-sm-10">
                  <textarea class="form-control" disabled='disabled' rows="5">{{ $entry->description }}</textarea>
                </div>
              </div>

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Rules</label>
                <div class="col-sm-10">
                  <input type="checkbox" disabled='disabled' {{ $entry->rules ? "checked=\"checked\"" : "" }}>
                </div>
              </div>

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Submitted</label>
                <div class="col-sm-10">
                  <input type="checkbox" disabled='disabled' {{ $entry->submitted ? "checked=\"checked\"" : "" }}>
                </div>
              </div>

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Updated At</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ $entry->updated_at->toDayDateTimeString() }}">
                </div>
              </div>

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Late</label>
                <div class="col-sm-10">
                  <input type="checkbox" disabled='disabled' {{ $entry->isLate($category) ? "checked=\"checked\"" : "" }}>
                </div>
              </div>

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Rule Break</label>
                <div class="col-sm-10">
                  <p>
                    {{ $entry->rule_break == null ? "pending" : $entry->rule_break->result }}
                    <a href="{{ route('admin.rule-break', $entry) }}" class="btn btn-info pull-right">View</a>
                  </p>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">Files</div>

        <div class="panel-body">
          <table class="table" id="files-table">
            <thead>
              <th>Filename</th>
              <th>Uploaded At</th>
              <th>Late</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($entry->uploadedFiles as $file)
              <tr>
                <td>{{ $file->name }}</td>
                <td>{{ $file->uploaded_at->toDayDateTimeString() }}</td>
                <td class="{{ $file->isLate($category) ? "late-upload" : "submitted-upload" }}">{{ $file->isLate($category) ? "Yes" : "No" }}</td>
                <td>
                  <a class="btn btn-primary" href="{{ route('admin.submissions.file', $file) }}">View</button>
                </td>
              </tr>
@endforeach
            </tbody>
          </table>

          <div class="filelog">
            @foreach ($entry->uploadedFileLog as $log)
            <p class="{{ $log->level }}">{{ $log->level }}: {{ $log->message }}</p>
            @endforeach
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
