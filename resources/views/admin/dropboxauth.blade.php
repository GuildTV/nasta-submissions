@extends('layouts.app')

@section('page_title', 'Dropbox Auth Manager')
@section('page_selected', 'admin')

@section('content')
<div class="container">
  <h1>
    <a class="back" href="/admin/home" title="Back"><i class="fa fa-arrow-left"></i></a>
    Dropbox Auth Manager
  </h1>
  <div class="col-lg-12">
    {{-- @include('partials.errors.admin-alert') --}}
    {{-- @include('partials.errors.basic') --}}

    <script type="text/javascript">
      function AddAccount(){
        bootbox.prompt("Give the account a name", function(result){
          if (result == null)
            return;

          window.location = "/admin/dropbox-auth/go?account=" + result;
        });
      }
    </script>
    <button class="btn btn-primary" onclick="AddAccount();">Add account</button>

    <table class="table table-striped table-bordered" id="shows_table">
      <thead>
        <th><p>Name</p></th>
        <th><p>Is Valid</p></th>
        <th><p>Enabled</p></th>
        <th><p>Used Space</p></th>
        <th><p>Total Space</p></th>
        <th><p>Re-auth</p></th>
      </thead>
@foreach($accounts as $acc)
      <tr>
        <td>{{ $acc['name'] }}</td>
        <td>{{ $acc['valid'] }}</td>
        <td>{{ $acc['enabled'] }}</td>
        <td>{{ $acc['usedSpace'] }} ({{ $acc['percentageUsed'] }})</td>
        <td>{{ $acc['totalSpace'] }}</td>
        <td><a href="/admin/dropbox-auth/go?account={{ $acc['name'] }}">Go</a></td>
      </tr>
@endforeach
    </table>
  </div>
</div>
@stop
