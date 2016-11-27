<div class="col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">Entry</div>

			<div class="row">
				<div class="col-md-12">
					<form id="entryform" class="form-horizontal" onsubmit="return false">

						<input type="hidden" id="entrycategory" value="{{ $category->id }}">

						<div class="form-group">
							<label for="entryname" class="col-sm-2 control-label">Entry Name</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="entryname" maxlength="255" placeholder="Entry Name" value="{{ $entry->name }}">
							</div>
						</div>
						
						<div class="form-group">
							<label for="entrydescription" class="col-sm-2 control-label">Description</label>
							<div class="col-sm-10">
								<textarea class="form-control" id="entrydescription" rows="5">{{ $entry->description }}</textarea>
							</div>
						</div>

						@foreach ($category->constraints as $constraint)

						<div class="form-group">
							<label for="file{{ $constraint->id }}" class="col-sm-2 control-label">{{ $constraint->name }}</label>
							<div class="col-sm-10">
								<input type="file" class="form-control" id="file{{ $constraint->id }}" >
								<a target="_new" href="{{ route("station.entry.upload", [$category, $constraint]) }}">Upload file</a>

								<!-- Add optional hash. optional for the user, and optional for the constraint -->

								<br />

								<pre id="log{{ $constraint->id }}"></pre>
							</div>
						</div>

						@endforeach

						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<div class="checkbox">
									<label>
										<input id="entryrules" type="checkbox" {{ $entry->rules ? "checked=\"checked\"" : "" }}> I agree to the rules governing the NaSTA Awards {{ Config::get('nasta.year') }}
									</label>
								</div>
							</div>
						</div>
											
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-primary">Save Draft</button>
								<button type="submit" class="btn btn-success" id="entrysubmit">Submit Entry</button>
							</div>
						</div>

					</form>
				</div>
			</div>

		</div>
	</div>
</div>