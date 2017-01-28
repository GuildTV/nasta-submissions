@extends('layouts.app')

@section('js')

window.readonly = {{ $readonly ? "true" : "false" }};
window.StationEntry.BindValidator();

if (!window.readonly)
  window.StationEntry._RunClock();

@endsection

@section('content')
<div class="container">
	<div class="row">
		
	@include('station.submission.details')

	@include('station.submission.entry')		

	</div>
</div>
@endsection

@section('modals')

  @include('station.submission.view-modal')

@endsection