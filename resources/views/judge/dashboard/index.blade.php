@extends('layouts.app')

@section('js')
$.fn.dataTable.moment( 'ddd, MMM D, YYYY h:mm A' ); // Thu, Dec 25, 1975 2:15 PM

$('.entrytable').DataTable({
  paging: false,
  columnDefs: [ {
  targets: 3,
    orderable: false
  } ]
});
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10 col-md-offset-1">

@foreach ($categories as $category)
    @include('judge.dashboard.category')
@endforeach

    </div>
  </div>
</div>
@endsection
