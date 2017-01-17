@extends('layouts.app')
@section('page_selected', 'categories')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">Categories</div>

				<div class="panel-body">
					<table class="table">
						<thead>
							<th>Name</th>
							<th>Description</th>
							<th>Deadline</th>
							<th style="width: 120px;">Entry Status</th>
							<th>Submission</th>
						</thead>
						<tbody>
@foreach ($categories as $cat)
<?php
	$entry = $cat->myEntry;
	$msg = $entry == null ? " - " : ($entry->submitted ? ($entry->isLate() ? "Late Submission" : "Submitted") : "Draft");
	$class = $entry == null ? "" : ($entry->submitted ? ($entry->isLate() ? "late-upload" : "submitted-upload") : "draft-upload");
?>
							<tr>
								<td>{{ $cat->name }}</td>
								<td><p class="cell-overflow">{{ $cat->description }}</p></td>
								<td>{{ $cat->closing_at->toDayDateTimeString() }}</td>
								<td class="{{ $class }}">{{ $msg }}</td>
								<td>
								  @if ($cat->canEditSubmissions() || $entry != null)
								  	<a href="{{ route("station.entry", $cat) }}">View</a>
								  @endif
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
