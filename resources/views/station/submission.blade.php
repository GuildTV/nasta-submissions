@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">{{ $category->name }}</div>

				<div class="row">
					<div class="col-md-6">
						<dl class="dl-horizontal">
							<h3>Details</h3>

							<dt>Description</dt>
							<dd>{{ $category->description }}</dd>

							@if ($category->opening_at)
								<dt>Opening Date</dt>
								<dd>{{ $category->opening_at->toDayDateTimeString() }}</dd>
							@endif

							@if ($category->closing_at)
								<dt>Closing Date</dt> 
								<dd>{{ $category->closing_at->toDayDateTimeString() }}</dd>
							@endif
						</dl>
					</div>
					<div class="col-md-6">
						<dl class="dl-horizontal">
							<h3>Requirements</h3>

							@foreach ($category->constraints as $constraint)
								<dt>{{ $constraint->name }}</dt>
								<dd>{{ $constraint->description }}</dd>
							@endforeach
						</dl>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
