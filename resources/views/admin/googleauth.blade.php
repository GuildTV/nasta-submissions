@extends('layouts.app')

@section('page_title', 'Google Auth Manager')
@section('page_selected', 'admin')

@section('content')
<div class="container">
  <h1>
    <a class="back" href="/admin/home" title="Back"><i class="fa fa-arrow-left"></i></a>
    Google Auth Manager
  </h1>
  <div class="col-lg-12">
    {{-- @include('partials.errors.admin-alert') --}}
    {{-- @include('partials.errors.basic') --}}

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
        <td><a href="/admin/google-auth/go?account={{ $acc['name'] }}">Go</a></td>
      </tr>
@endforeach
    </table>
  </div>
</div>
@stop
