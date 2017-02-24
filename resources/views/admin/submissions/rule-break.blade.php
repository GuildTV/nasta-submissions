@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">{{ $entry->station->name }}'s submission for the {{ $entry->category->name }} award</div>

        <div class="row">
          <div class="col-md-12">
            <form class="form-horizontal" onsubmit="window.AdminRuleBreak.SubmitEntry(this);return false">

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label"></label>
                <div class="col-sm-10">
                  <a href="{{ route('admin.submissions.view', [ $entry->station, $entry->category ]) }}" class="btn btn-info">View Submission</a>
                </div>
              </div>


                @if($entry->rule_break == null)

                 <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Rule Break</label>
                  <div class="col-sm-10">
                    <p>pending</p>
                    
                    <a href="{{ route('admin.rule-break.entry-check', $entry) }}" class="btn btn-warning pull-right">Re-run</a>
                  </div>
                </div>

                @else

                <input type="hidden" class="entryid" value="{{ $entry->id }}" />

                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Rule Break</label>
                  <div class="col-sm-10">
                    <select class="form-control" name="result" id="result">
                    <?php $res = $entry->rule_break->result; ?>
                      @if ($res != "pending" && $res != "accepted" && $res != "rejected")
                        <option value="{{ $res }}" selected='selected'>{{ $res }}</option>
                      @endif

                      <option value="pending" {!! $res == "pending" ? "selected='selected'" : "" !!}>pending</option>
                      <option value="accepted" {!! $res == "accepted" ? "selected='selected'" : "" !!}>accepted</option>
                      <option value="rejected" {!! $res == "rejected" ? "selected='selected'" : "" !!}>rejected</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Notes</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" id="notes" name="notes" rows="5">{{ $entry->rule_break->notes }}</textarea>
                    <p></p>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-9">
                    <button type="submit" class="btn btn-success" id="save">Save</button>
                    <a href="{{ route('admin.rule-break.entry-check', $entry) }}" class="btn btn-warning pull-right">Re-run</a>
                  </div>
                </div>

                <hr />

                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Warnings</label>
                  <div class="col-sm-10">
                    <?php $warnings = json_decode($entry->rule_break->warnings, true); ?>
                    @if (count($warnings) == 0)
                      <p> - </p>
                    @else
                      <ul>
                        <?php
                          foreach ($warnings as $msg){
                            $split = explode("=", $msg);
                            if (count($split) == 2){
                              echo "<li>" . trans("rule_break.warning." . $split[0], [ "id" => $split[1] ]) . "</li>";
                            } else {
                              echo "<li>" . trans("rule_break.warning." . $msg) . "</li>";
                            }
                          }
                        ?>
                      </ul>
                    @endif
                  </div>
                </div>

                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Failures</label>
                  <div class="col-sm-10">
                    <?php $errors = json_decode($entry->rule_break->errors, true); ?>
                    @if (count($errors) == 0)
                      <p> - </p>
                    @else
                      <ul>
                        <?php
                          foreach ($errors as $msg){
                            $split = explode("=", $msg);
                            if (count($split) == 2){
                              echo "<li>" . trans("rule_break.error." . $split[0], [ "id" => $split[1] ]) . "</li>";
                            } else {
                              echo "<li>" . trans("rule_break.error." . $msg) . "</li>";
                            }
                          }
                        ?>
                      </ul>
                    @endif
                  </div>
                </div>

              @endif

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Updated At</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" disabled='disabled' value="{{ $entry->updated_at->toDayDateTimeString() }}">
                </div>
              </div>

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Late</label>
                <div class="col-sm-10">
                  <input type="checkbox" disabled='disabled' {{ $entry->isLate() ? "checked=\"checked\"" : "" }}>
                </div>
              </div>


            </form>
          </div>
        </div>
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">Files</div>

        <div class="row">
          <div class="col-md-12">
            @foreach ($files as $file)
              <form class="form-horizontal" onsubmit="window.AdminRuleBreak.SubmitFile(this);return false">
                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label"></label>
                  <div class="col-sm-10">
                    <h4>
                      {{ $file->name }} - (#{{ $file->id }})

                      <button class="btn btn-info pull-right" data-id="{{ $file->id }}" data-name="{{ $file->name }}" 
                        data-type="{{ $file->metadata ? "video" : "other" }}" data-url="{{ route('admin.submissions.file.download', $file) }}"
                        onclick="window.StationCommon.ViewFile(this); return false">View</button>
                    </h4>
                  </div>
                </div>

                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Constraint</label>
                  <div class="col-sm-10">
                    <p>{{ isset($constraint_map[$file->id]) ? $constraint_map[$file->id] : " - " }}</p>
                  </div>
                </div>

                @if ($file->hasReplacement())
                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Replaced</label>
                  <div class="col-sm-10">
                    <p>By #{{ $file->replacement_id }}</p>
                  </div>
                </div>
                @endif

                @if($file->rule_break == null)

                 <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Rule Break</label>
                  <div class="col-sm-10">
                    <p>pending</p>
                    <a href="{{ route('admin.rule-break.file-check', [ $entry, $file ]) }}" class="btn btn-warning pull-right">Re-run</a>
                  </div>
                </div>

                @else

                <input type="hidden" class="fileid" value="{{ $file->id }}" />

                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Rule Break</label>
                  <div class="col-sm-10">
                    <select class="form-control" name="result" id="result">
                    <?php $res = $file->rule_break->result; ?>
                      @if ($res != "pending" && $res != "accepted" && $res != "rejected")
                        <option value="{{ $res }}" selected='selected'>{{ $res }}</option>
                      @endif

                      <option value="pending" {!! $res == "pending" ? "selected='selected'" : "" !!}>pending</option>
                      <option value="accepted" {!! $res == "accepted" ? "selected='selected'" : "" !!}>accepted</option>
                      <option value="rejected" {!! $res == "rejected" ? "selected='selected'" : "" !!}>rejected</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Notes</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" id="notes" name="notes" rows="5">{{ $file->rule_break->notes }}</textarea>
                    <p></p>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-9">
                    <button type="submit" class="btn btn-success" id="save">Save</button>
                    <a href="{{ route('admin.rule-break.file-check', [ $entry, $file ]) }}" class="btn btn-warning pull-right">Re-run</a>
                  </div>
                </div>

                <hr />

                @if ($file->rule_break->errors != "[]]")
                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Transcode</label>
                  <div class="col-sm-10">
                    @if ($file->transcode != null)
                    <p>pending</p>
                    @else
                    <button class="btn btn-warning" data-id="{{ $file->id }}" data-profile="fix_audio" onclick="window.AdminRuleBreak.StartTranscode(this);return false">Fix audio</button>
                    <button class="btn btn-warning" data-id="{{ $file->id }}" data-profile="1080p" onclick="window.AdminRuleBreak.StartTranscode(this);return false">1080P</button>
                    <button class="btn btn-warning" data-id="{{ $file->id }}" data-profile="720p" onclick="window.AdminRuleBreak.StartTranscode(this);return false">720P</button>
                    <button class="btn btn-warning" data-id="{{ $file->id }}" data-profile="sd" onclick="window.AdminRuleBreak.StartTranscode(this);return false">SD</button>
                    @endif
                  </div>
                </div>
                @endif

                <hr />

                  <div class="form-group">
                    <label for="entryname" class="col-sm-2 control-label">Length</label>
                    <div class="col-sm-10">
                      <p>{{ $file->rule_break->length }}</p>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="entryname" class="col-sm-2 control-label">Warnings</label>
                    <div class="col-sm-10">
                      <?php $warnings = json_decode($file->rule_break->warnings, true); ?>
                      @if (count($warnings) == 0)
                        <p> - </p>
                      @else
                        <ul>
                        <?php
                          foreach ($warnings as $msg){
                            $split = explode("=", $msg);
                            if (count($split) == 2){
                              echo "<li>" . trans("rule_break.warning." . $split[0], [ "id" => $split[1] ]) . "</li>";
                            } else {
                              echo "<li>" . trans("rule_break.warning." . $msg) . "</li>";
                            }
                          }
                        ?>
                        </ul>
                      @endif
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="entryname" class="col-sm-2 control-label">Failures</label>
                    <div class="col-sm-10">
                      <?php $errors = json_decode($file->rule_break->errors, true); ?>
                      @if (count($errors) == 0)
                        <p> - </p>
                      @else
                        <ul>
                        <?php
                          foreach ($errors as $msg){
                            $split = explode("=", $msg);
                            if (count($split) == 2){
                              echo "<li>" . trans("rule_break.error." . $split[0], [ "id" => $split[1] ]) . "</li>";
                            } else {
                              echo "<li>" . trans("rule_break.error." . $msg) . "</li>";
                            }
                          }
                        ?>
                        </ul>
                      @endif
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="entryname" class="col-sm-2 control-label">Metadata</label>
                    <div class="col-sm-10">
                      <pre>{{ json_encode(json_decode($file->rule_break->metadata), JSON_PRETTY_PRINT) }}</pre>
                    </div>
                  </div>

                @endif

                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Uploaded At</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" disabled='disabled' value="{{ $file->uploaded_at->toDayDateTimeString() }}">
                  </div>
                </div>

                <hr />
              </form>

            @endforeach
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

@section('modals')

  @include('station.submission.view-modal')

@endsection