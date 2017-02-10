@extends('layouts.app')

@section('js')
  window.JudgeScore.BindValidator();
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">{{ $entry->station->name }}'s entry for {{ $entry->category->name }} <a class="pull-left" href="{{ route('judge.dashboard') }}">Back</a></div>

        <div class="row">
          <div class="col-md-12">
            <form class="form-horizontal" id="scoreform" onsubmit="return false">

              @if ($readonly)

              <div class="row">
                <div class="alert alert-warning col-sm-10 col-sm-offset-1">
                  <p>You cannot edit your feedback as you have finalised the results.</p>
                  <p>If you need to make a correction, please email {{ Config::get('nasta.judge_support_email') }}</p>
                </div>
              </div>

              @endif

              <input type="hidden" id="entryid" value="{{ $entry->id }}" />

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Station</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ $entry->station->name }}">
                </div>
              </div>

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Entry Name</label>
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

              <hr />

              <div class="form-group">
                <label for="score" class="col-sm-2 control-label">Score</label>
                <div class="col-sm-10">
                  <select id="score" name="score" {{ $readonly ? "disabled='disabled'" : "" }} class="form-control">
                    <option> - </option>
                  <?php
                    $result = $entry->result != null ? $entry->result->score : -1;
                    for($i=0; $i<=20; $i++)
                      echo "<option " . ($i == $result ? "selected=\"selected\"" : "") . ">" . $i . "</option>";
                  ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="feedback" class="col-sm-2 control-label">Feedback</label>
                <div class="col-sm-10">
                  <textarea id="feedback" name="feedback" class="form-control" {{ $readonly ? "disabled='disabled'" : "" }} rows="5">{{ $entry->result != null ? $entry->result->feedback : "" }}</textarea>
                </div>
              </div>

              @if (!$readonly)

              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-2">
                  <button type="submit" class="btn btn-success" id="scoreave">Save</button>
                </div>
              </div>

              @endif

            </form>
          </div>
        </div>
      </div>

@foreach ($entry->uploadedFiles as $file)
<?php
  $constraint = null;
  if (isset($constraint_map[$file->id])) {
    $id = $constraint_map[$file->id];
    $constraint = \App\Database\Category\FileConstraint::find($id);
  }
?>
      <div class="panel panel-default">
        <div class="panel-heading">{{ ($constraint != null ? $constraint->name : "Extra") . " - " . $file->name }}</div>

        <div class="row">
          <div class="col-md-12">
            <form class="form-horizontal" onsubmit="return false">

              @if ($file->metadata)
              <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                  <script type="text/javascript" src="https://cdn.jsdelivr.net/clappr/latest/clappr.min.js"></script>
                  <div id="player"></div>
                  <script>
                    var player = new Clappr.Player({
                      width: 590,
                      mimeType: "video/mp4",
                      source: "{{ route('judge.download', $file) }}", 
                      parentId: "#player"
                    });
                  </script>
                </div>
              </div>
              
              <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                  <a class="btn btn-info" href="{{ route('judge.download', $file) }}" target="_blank">Download</a>
                </div>
              </div>
              
              @else

              <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                  <a class="btn btn-info" href="{{ route('judge.download', $file) }}" target="_blank">Download</a>
                  <p>This file must be downloaded to be viewed</p>
                </div>
              </div>

              @endif

            </form>
          </div>
        </div>
      </div>
@endforeach

    </div>
  </div>
</div>
@endsection
