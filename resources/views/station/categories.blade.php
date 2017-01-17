@extends('layouts.app')
@section('page_selected', 'categories')

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12">

@foreach ($groupedCategories as $group)
			<div class="panel panel-default">
				<div class="panel-heading">Closing on {{ $group[0]->closing_at->format('l jS F \\a\\t H:i') }}</div>
				<div class="panel-body">
					<h3></h3>
					<table class="table">
						<thead>
							<th>Name</th>
							<th>Description</th>
							<th style="width: 120px;">Entry Status</th>
							<th>Submission</th>
						</thead>
						<tbody>
@foreach ($group as $cat)
<?php
	$entry = $cat->myEntry;
	$msg = $entry == null ? " - " : ($entry->submitted ? ($entry->isLate($cat) ? "Late Submission" : "Submitted") : "Draft");
	$class = $entry == null ? "" : ($entry->submitted ? ($entry->isLate($cat) ? "late-upload" : "submitted-upload") : "draft-upload");
?>
							<tr>
								<td>{{ $cat->name }}</td>
								<td><p class="cell-overflow">{{ $cat->description }}</p></td>
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
					<hr />
				</div>
			</div>
@endforeach
		</div>
	</div>
</div>
@endsection
