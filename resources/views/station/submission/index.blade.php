@extends('layouts.app')

@section('js')

window.StationEntry.BindValidator();

@endsection

@section('content')
<div class="container">
	<div class="row">
		
	@include('station.submission.details')

	@include('station.submission.entry')		

	</div>
</div>
@endsection
