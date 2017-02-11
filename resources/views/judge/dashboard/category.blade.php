<div class="panel panel-default">
  <div class="panel-heading">Entries for {{ $category->name }}</div>

  <div class="panel-body">
    @if ($category->isResultsReadOnly() && !$adminVersion)
    <div class="row">
      <div class="alert alert-warning col-sm-10 col-sm-offset-1">
        <p>You cannot edit your feedback as you have finalised the results.</p>
        <p>If you need to make a correction, please email {{ Config::get('nasta.judge_support_email') }}</p>
      </div>
    </div>
    @elseif (!$adminVersion)
    <div class="row">
      <div class="alert alert-info col-sm-10 col-sm-offset-1">
        <p>If you have any issues saving feedback or viewing any files, please email {{ Config::get('nasta.judge_support_email') }}</p>
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
<?php if (!$entry->canBeJudged(true)) continue; ?>
        <tr>
          <td style="white-space: nowrap;">{{ $entry->station->name }}</td>
          <td>{{ $entry->name }}</td>
          <td>{{ $entry->canBeJudged() && $entry->result ? $entry->result->score : "-" }}</td>
          <td>
            @if (!$entry->canBeJudged())
              pending
            @elseif (!$adminVersion)
            <a href="{{ route('judge.view', [$entry]) }}">View</a>
            @endif
          </td>
        </tr>
@endforeach
      </tbody>
    </table>

    <hr />
    @include("judge.dashboard.final")

  </div>
</div>