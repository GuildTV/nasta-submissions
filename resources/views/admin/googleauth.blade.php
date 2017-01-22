@extends('layouts.app')

@section('page_title', 'Google Auth Manager')
@section('page_selected', 'admin')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">Google Auth Manager</div>

        <div class="panel-body">
          <table class="table table-striped table-bordered" id="shows_table">
            <thead>
              <th><p>Name</p></th>
              <th><p>Is Valid</p></th>
              <th><p>Enabled</p></th>
              <th><p>Used Space</p></th>
              <th><p>Used Space (Estimated)</p></th>
              <th><p>Total Space</p></th>
              <th><p>Re-auth</p></th>
            </thead>
@foreach($accounts as $acc)
            <tr>
              <td>{{ $acc['name'] }}</td>
              <td>{{ $acc['valid'] }}</td>
              <td>{{ $acc['enabled'] }}</td>
              <td>{{ $acc['usedSpace'] }} ({{ $acc['percentageUsed'] }})</td>
              <td>{{ $acc['usedSpaceEstimate'] }} ({{ $acc['percentageUsedEstimate'] }})</td>
              <td>{{ $acc['totalSpace'] }}</td>
              <td><a href="{{ route('admin.googleauth.go') }}?account={{ $acc['name'] }}">Go</a></td>
            </tr>
@endforeach
          </table>
         
        </div>
      </div>

    </div>
  </div>
</div>
@stop
