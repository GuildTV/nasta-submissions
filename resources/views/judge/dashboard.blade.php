@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

@foreach ($categories as $category)
      <div class="panel panel-default">
        <div class="panel-heading">Entries for {{ $category->name }}</div>

        <div class="panel-body">
          <div class="row card-container">
            
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
                <td>{{ $entry->station->name }}</td>
                <td>{{ $entry->name }}</td>
                <td>{{ $entry->result ? $entry->result->score : "-" }}</td>
                <td>
                  <a href="{{ route('judge.view', [$entry]) }}">View</a>
                </td>
              </tr>
@endforeach
            </tbody>
          </table>

          </div>
        </div>
      </div>
    </div>
@endforeach

  </div>
</div>
@endsection
