@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">{{ $file->station->name }}'s file '{{ $file->name }}' {{ $file->category != null ? "for the " . $file->category->name . " award" : "" }}</div>

        <div class="row">
          <div class="col-md-12">
            <form id="entryform" class="form-horizontal" onsubmit="return false">

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ $file->name }}" />
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
                  <input type="text" class="form-control" disabled='disabled' value="{{ $file->path_local }}" />
                </div>
              </div>
              
              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Size</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ \App\Helpers\StringHelper::formatBytes($file->size) }}" />
                </div>
              </div>

              

            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
