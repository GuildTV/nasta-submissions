@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Dashboard</div>

				<div class="panel-body">
					<div class="row card-container">
						<div class="card card-categories">
							<a href="{{ route("station.categories") }}">Categories</a>
						</div>
						<div class="card card-files">
							<a href="{{ route("station.files") }}">Uploaded Files</a>
						</div>
						{{-- <div class="card card-results">
							<a href="{{ route("station.results") }}">Results</a>
						</div> --}}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
