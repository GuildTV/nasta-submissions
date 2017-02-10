@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

@foreach ($categories as $category)
      <div class="panel panel-default">
        <div class="panel-heading">Entries for {{ $category->name }}</div>

        <div class="panel-body">
          @if ($category->isResultsReadOnly())

          <div class="row">
            <div class="alert alert-warning col-sm-10 col-sm-offset-1">
              <p>You cannot edit your feedback as you have finalised the results.</p>
              <p>If you need to make a correction, please email {{ Config::get('nasta.judge_support_email') }}</p>
            </div>
          </div>

          @endif

          <table class="table" id="entries-table">
            <thead>
              <th>Station</th>
              <th>Name</th>
              <th>Score</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($category->entries as $entry)
<?php if (!$entry->canBeJudged()) continue; ?>
              <tr>
                <td style="white-space: nowrap;">{{ $entry->station->name }}</td>
                <td>{{ $entry->name }}</td>
                <td>{{ $entry->result ? $entry->result->score : "-" }}</td>
                <td>
                  <a href="{{ route('judge.view', [$entry]) }}">View</a>
                </td>
              </tr>
@endforeach
            </tbody>
          </table>

          <hr />

<?php
  $missing_results = $category->entries->filter(function($v){ return $v->canBeJudged() && $v->result == null; })->count();
?>

          <h3>Final results</h3>

          <div class="row">
            <div class="col-md-12">
              <form class="form-horizontal finalizeform" id="finalize-{{ $category->id }}" onsubmit="window.JudgeDashboard.PromptSave(this);return false">

                <input type="hidden" id="category_id" value="{{ $category->id }}" />

                @if ($missing_results > 0)
                  <p>{{ $missing_results }} entries have not been scored yet. You must give all entries scores before they can finalized</p>

                @else
<?php
  $validEntries = $category->entries
    ->filter(function($v){ return $v->canBeJudged(); });
  $sortedEntries = $validEntries
    ->sortByDesc(function($v){ return $v->result->score; })
    ->groupBy(function($v){ return $v->result->score; })
    ->values();

  $invalidFirstOrSecondScores = false;
  if ($sortedEntries->count() >= 1) {
    $data = $sortedEntries[0];
    $invalidFirstOrSecondScores = $invalidFirstOrSecondScores || $data->count() != 1;
  }
  if ($sortedEntries->count() >= 2) {
    $data = $sortedEntries[1];
    $invalidFirstOrSecondScores = $invalidFirstOrSecondScores || $data->count() != 1;
  }
?>
                  @if ($invalidFirstOrSecondScores)
                    <p>Invalid score selections. Please ensure that the two highest scores are used only once.</p>
                  @else

                    @if ($validEntries->count() >= 1)
                      <?php $entry = $category->result != null ? $category->result->winner : $sortedEntries[0][0]; ?>
                      @if ($entry != null)
                      <legend>Winner</legend>

                      <input type="hidden" id="winner_id" value="{{ $entry->id }}" />

                      <div class="form-group">
                        <label class="col-sm-2 control-label">Entry</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" disabled='disabled' value="{{ $entry->name}} ({{ $entry->station->name }})">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-sm-2 control-label">Comment</label>
                        <div class="col-sm-10">
                          <input id="winner_comment" name="winner_comment" type="text" class="form-control" maxlength="200" 
                            {!! $category->isResultsReadOnly() ? "disabled='disabled'" : "placeholder=\"Please provide a short comment for the certificate\"" !!}
                            value="{{ $category->result != null ? $category->result->winner_comment : "" }}">
                        </div>
                      </div>
                      @endif
                    @endif

                    @if ($validEntries->count() >= 2)
                      <?php $entry = $category->result != null ? $category->result->commended : $sortedEntries[1][0]; ?>
                      @if ($entry != null)
                      <legend>Highly commended</legend>

                      <input type="hidden" id="commended_id" value="{{ $entry->id }}" />

                      <div class="form-group">
                        <label class="col-sm-2 control-label">Entry</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" disabled='disabled' value="{{ $entry->name}} ({{ $entry->station->name }})">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="col-sm-2 control-label">Comment</label>
                        <div class="col-sm-10">
                          <input id="commended_comment" name="commended_comment" type="text" class="form-control" maxlength="200" 
                            {!! $category->isResultsReadOnly() ? "disabled='disabled'" : "placeholder='Please provide a short comment for the certificate'" !!}
                            value="{{ $category->result != null ? $category->result->commended_comment : "" }}">
                        </div>
                      </div>
                      @endif
                    @endif

                      <hr />

                      @if (!$category->isResultsReadOnly())
                      <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                          <button class="btn btn-primary" type="submit">Finalize results</button>
                        </div>
                      </div>
                      @endif

                  @endif


                @endif

              </form>
            </div>
          </div>

        </div>
      </div>
@endforeach

    </div>
  </div>
</div>
@endsection
