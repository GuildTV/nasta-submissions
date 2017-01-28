@extends('layouts.app')

@section('page_selected', 'files')

@section('js')
$('#files-table').DataTable({
  paging: false,
  searching: false,
  language: {
    emptyTable: "You have not uploaded any files!"
  }
});

window.OpenCategories = [
  @foreach ($categories as $cat)
    @if ($cat->canEditSubmissions())
      {
        text: "{{ $cat->name }}",
        value: "{{ $cat->id }}"
      },
    @endif
  @endforeach
];
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">Uploaded Files</div>

        <div class="panel-body">
        @if (\Session::has('files.link'))
          <div id="alert_holder">
            <div class="alert alert-success">
              <p>{{ \Session::get('files.link') }}</p>
            </div>
          </div>
        @elseif (\Session::has('files.delete'))
          <div id="alert_holder">
            <div class="alert alert-danger">
              <p>{{ \Session::get('files.delete') }}</p>
            </div>
          </div>
        @endif

          <table class="table" id="files-table">
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
                <td>
                  @if ($file->category == null)
                    <a class="btn btn-primary" data-id="{{ $file->id }}" data-name="{{ $file->name }}" onclick="window.StationFiles.Link(this)">Add to a category</button>
                  @else
                    {{ $file->category->name }}
                  @endif
                </td>
                <td>{{ $file->name }}</td>
                <td>{{ $file->uploaded_at->toDayDateTimeString() }}</td>
                <td class="{{ $file->isLate() ? "late-upload" : "submitted-upload" }}">{{ $file->isLate() ? "Yes" : "No" }}</td>
                <td>
                  <button class="btn btn-info" data-id="{{ $file->id }}" data-name="{{ $file->name }}" 
                    data-type="{{ $file->metadata ? "video" : "other" }}" data-url="{{ route('station.files.download', $file) }}"
                    onclick="window.StationCommon.ViewFile(this); return false">View</button>

                  @if ($file->category == null || $file->category->myEntry == null || ($file->category->canEditSubmissions() && !$file->category->myEntry->submitted))
                    <button class="btn btn-danger" data-id="{{ $file->id }}" onclick="window.StationFiles.Delete(this)">Delete</button>
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

@section('modals')

  @include('station.submission.view-modal')

@endsection