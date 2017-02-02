@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">{{ $entry->station->name }}'s submission for the {{ $entry->category->name }} award</div>

        <div class="row">
          <div class="col-md-12">
            <form id="entryform" class="form-horizontal" onsubmit="return false">

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label"></label>
                <div class="col-sm-10">
                  <a href="{{ route('admin.submissions.view', [ $entry->station, $entry->category ]) }}" class="btn btn-info">View Submission</a>
                </div>
              </div>

              <div class="form-group">
                <label for="entryname" class="col-sm-2 control-label">Rule Break</label>
                <div class="col-sm-10">
                  <p>
                    {{ $entry->rule_break == null ? "pending" : $entry->rule_break->result }}

                    <a href="{{ route('admin.rule-break.entry-state', [ $entry, 'rejected' ]) }}" class="btn btn-primary pull-right">Reject</a>
                    <a href="{{ route('admin.rule-break.entry-state', [ $entry, 'accepted' ]) }}" class="btn btn-primary pull-right">Approve</a>
                    <a href="{{ route('admin.rule-break.entry-check', $entry) }}" class="btn btn-warning pull-right">Re-run</a>
                  </p>
                </div>
              </div>

              @if($entry->rule_break != null)

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
            <form id="entryform" class="form-horizontal" onsubmit="return false">

              @foreach ($files as $file)
                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label"></label>
                  <div class="col-sm-10">
                    <h4>{{ $file->name }} - (#{{ $file->id }})</h4>
                  </div>
                </div>

                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Constraint</label>
                  <div class="col-sm-10">
                    <p>{{ isset($constraint_map[$file->id]) ? $constraint_map[$file->id] : " - " }}</p>
                  </div>
                </div>

                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Rule Break</label>
                  <div class="col-sm-10">
                    <p>
                      {{ $file->rule_break == null ? "pending" : $file->rule_break->result }}

                      <a href="{{ route('admin.rule-break.file-state', [ $entry, 'rejected', $file ]) }}" class="btn btn-primary pull-right">Reject</a>
                      <a href="{{ route('admin.rule-break.file-state', [ $entry, 'accepted', $file ]) }}" class="btn btn-primary pull-right">Approve</a>
                      <a href="{{ route('admin.rule-break.file-check', [ $entry, $file ]) }}" class="btn btn-warning pull-right">Re-run</a>
                    </p>
                  </div>
                </div>

                @if($file->rule_break != null)

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

                @endif

                <div class="form-group">
                  <label for="entryname" class="col-sm-2 control-label">Uploaded At</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" disabled='disabled' value="{{ $file->uploaded_at->toDayDateTimeString() }}">
                  </div>
                </div>

                <hr />
              @endforeach

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
