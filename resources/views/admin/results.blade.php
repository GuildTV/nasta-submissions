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
              <tr>
                <td>{{ $category->name }}</td>
                <td>{{ $category->result == null ? \App\Helpers\StringHelper::formatPercent(($category->entries->filter(function($v){return $v->result != null;})->count()/$category->entries->count())*100) : "yes" }}</td>
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
