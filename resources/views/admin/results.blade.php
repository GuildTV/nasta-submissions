@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">

      <div class="panel panel-default">
        <div class="panel-heading">Results</div>

        <div class="panel-body">
          <table class="table" id="files-table">
            <thead>
              <th>Station</th>
              <th>Progress</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($categories as $category)
<?php
  $countComplete = $category->entries->filter(function($v){return $v->canBeJudged() && $v->result != null;})->count();
  $count = $category->entries->filter(function($v){return $v->canBeJudged();})->count();
  $percentStr = round($countComplete/$count*100, 0)."%";
?>
              <tr>
                <td>{{ $category->name }}</td>
                <td>{{ $category->result == null ? $percentStr : "yes" }}</td>
                <td>
                  <a class="btn btn-primary" href="{{ route('admin.results.view', $category) }}">View</button>
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
