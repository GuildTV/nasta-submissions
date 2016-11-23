@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		
	@include('station.submission.details')

	@include('station.submission.submission')		

	</div>
</div>
@endsection
