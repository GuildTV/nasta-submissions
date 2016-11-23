@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Dashboard</div>

				<div class="panel-body">
					<table class="table">
						<thead>
							<th>Name</th>
							<th>Description</th>
							<th>Closing Date/Time</th>
							<th>Submission</th>
							<th>&nbsp;</th>
						</thead>
						<tbody>
@foreach ($categories as $cat)
							<tr>
								<td>{{ $cat->name }}</td>
								<td>{{ $cat->description }}</td>
								<td>{{ $cat->closing_at->toDayDateTimeString() }}</td>
								<td>???</td>
								<td>
									<a href="{{ route("station.submission", $cat->slug) }}">View</a>
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
