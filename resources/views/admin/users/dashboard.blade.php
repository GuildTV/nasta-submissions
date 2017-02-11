@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

      <div class="panel panel-default">
        <div class="panel-heading">Users</div>

        <div class="panel-body">

          <table class="table" id="files-table">
            <thead>
              <th>Name</th>
              <th>Type</th>
              <th>Last Login</th>
              <th>&nbsp;</th>
            </thead>
            <tbody>
@foreach ($users as $user)
              <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->type }}</td>
                <td>{{ $user->last_login_at == null ? "-" : $user->last_login_at->toDayDateTimeString() }}</td>
                <td>
                  <a class="btn btn-primary" href="{{ route('admin.users.view', $user) }}">View</button>
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
