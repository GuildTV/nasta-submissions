@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">

      <div class="panel panel-default">
        <div class="panel-heading">All Rule break errors</div>

        <div class="panel-body">

          <table class="table">
            <thead>
              <th>Error</th>
              <th>Total</th>
              <th>Pending</th>
            </thead>
            <tbody>
@foreach ($errors as $name=>$error)
              <tr>
                <td>{{ $name }}</td>
                <td>{{ $error['total'] }}</td>
                <td>{{ $error['pending'] }}</td>
              </tr>
@endforeach
            </tbody>
          </table>
  
        </div>   
      </div>

      <div class="panel panel-default">
        <div class="panel-heading">All Rule break warnings</div>

        <div class="panel-body">

          <table class="table">
            <thead>
              <th>Warning</th>
              <th>Total</th>
              <th>Pending</th>
            </thead>
            <tbody>
@foreach ($warnings as $name=>$warn)
              <tr>
                <td>{{ $name }}</td>
                <td>{{ $warn['total'] }}</td>
                <td>{{ $warn['pending'] }}</td>
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
