@extends('layouts.app')

@section('js')
window.OpenCategories = [
  @foreach ($categories as $cat)
    <?php $name = $cat->canEditSubmissions() ? ($cat->closing_at->gt(\Carbon\Carbon::now()) ? $cat->name : ($cat->name . " (Closed)")) : ($cat->name . " (Cut-off)"); ?>
    {
      text: "{{ $name }}",
      value: "{{ $cat->id }}"
    },
  @endforeach
];
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">{{ $file->station->name }}'s file '{{ $file->name }}' {{ $file->category != null ? "for the " . $file->category->name . " award" : "" }}</div>

        <div class="row">
          <div class="col-md-12">
            <form class="form-horizontal" onsubmit="return false">

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ $file->name }}" />
                </div>
              </div>
              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Category</label>
                <div class="col-sm-10">
                  @if ($file->category != null)
                    <input type="text" class="form-control" disabled='disabled' value="{{ $file->category->name }}" />
                    <a class="btn btn-primary" href="{{ route('admin.submissions.view', [ $file->station, $file->category ]) }}">View</a>
                  @endif
                  <button class="btn btn-primary" onclick="window.AdminSubmissionFiles.Link(this)" data-id="{{ $file->id }}" data-name="{{ $file->name }}">Link</button>
                </div>
              </div>
              
              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Dropbox account</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ $file->account_id }}" />
                </div>
              </div>
              
              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Dropbox path</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ $file->path }}" />
                </div>
              </div>
              
              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Local path</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ $file->path_local != null ? Config::get("nasta.local_entries_path") . $file->path_local : "" }}" />
                </div>
              </div>
              
              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Size</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ \App\Helpers\StringHelper::formatBytes($file->size) }}" />
                </div>
              </div>

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Updated At</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ $file->uploaded_at->toDayDateTimeString() }}">
                </div>
              </div>

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Late</label>
                <div class="col-sm-10">
                  <input type="checkbox" disabled='disabled' {{ $file->isLate() ? "checked=\"checked\"" : "" }}>
                </div>
              </div>

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Rule Break</label>
                <div class="col-sm-10">
                  <p>
                    {{ $file->rule_break == null ? "pending" : $file->rule_break->result }}
                    @if($entry != null && $file->rule_break != null)
                      <a href="{{ route('admin.submissions.rule-break', $entry) }}" class="btn btn-info pull-right">View</a>
                    @endif
                  </p>
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                  <a class="btn btn-info" href="{{ route('admin.submissions.file.download', $file) }}" target="_blank">Download</a>
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                  <a class="btn btn-info" href="{{ route('admin.submissions.file.metadata', $file) }}" target="_blank">Update video metadata</a>
                  Note: can take a few minutes
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>

      @if ($file->metadata != null) <!-- if video -->
      <div class="panel panel-default">
        <div class="panel-heading">Video</div>

        <div class="row">
          <div class="col-md-12">
            <form class="form-horizontal" onsubmit="return false">

              <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                  <script type="text/javascript" src="https://cdn.jsdelivr.net/clappr/latest/clappr.min.js"></script>
                  <div id="player"></div>
                  <script>
                    var player = new Clappr.Player({
                      width: 590,
                      mimeType: "video/mp4",
                      source: "{{ route('admin.submissions.file.download', $file) }}", 
                      parentId: "#player"
                    });
                  </script>
                </div>
              </div>
              
              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Resolution</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ $file->metadata->width }}x{{ $file->metadata->height }}" />
                </div>
              </div>
              
              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Duration</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ \App\Helpers\StringHelper::formatDuration($file->metadata->duration/1000) }}" />
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
      @endif

    </div>
  </div>
</div>
@endsection
