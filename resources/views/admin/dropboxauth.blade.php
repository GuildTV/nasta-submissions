@extends('layouts.app')

@section('page_title', 'Dropbox Auth Manager')
@section('page_selected', 'admin')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">Dropbox Auth Manager</div>

        <div class="panel-body">
          <script type="text/javascript">
            function AddAccount(){
              bootbox.prompt("Give the account a name", function(result){
                if (result == null)
                  return;

                window.location = "{{ route('admin.dropboxauth.go') }}?account=" + result;
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
              <td><a href="{{ route('admin.dropboxauth.go') }}?account={{ $acc['name'] }}">Go</a></td>
            </tr>
@endforeach
          </table>
         
        </div>
      </div>

    </div>
  </div>
</div>
@stop
