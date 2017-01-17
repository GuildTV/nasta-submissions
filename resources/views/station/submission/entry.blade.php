<div class="col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">Entry</div>

			<div class="row">
				<div class="col-md-12">
					<form id="entryform" class="form-horizontal" onsubmit="return false">

						<input type="hidden" id="entrycategory" value="{{ $category->id }}">

						@if ($entry->submitted)
						<div id="alert_holder">
							<div class="alert {{ $entry->isLate($category) ? "alert-warning" : "alert-success" }}" id="update_status">
								<p>You submitted your entry on the {{ $entry->updated_at->format("jS F \\a\\t H:i") }}</p>
								@if ($entry->isLate($category))
									<p class="late-upload">Your entry has been marked as  late!!</p>
									@if (!$closed)
									<p>It is still possible to remove offending files to clear the late status</p>
									@endif
								@endif
								<p><a href="{{ route('station.categories') }}">Back</a> to categories list</p>
							</div>
						</div>
						@endif

						<div class="form-group">
							<label for="entryname" class="col-sm-2 control-label">Entry Name</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="entryname" name="entryname" maxlength="255" placeholder="Entry Name" {{ $readonly ? "disabled='disabled'" : "" }} value="{{ $entry->name }}">
							</div>
						</div>
						
						<div class="form-group">
							<label for="entrydescription" class="col-sm-2 control-label">Description</label>
							<div class="col-sm-10">
								<textarea class="form-control" id="entrydescription" name="entrydescription" {{ $readonly ? "disabled='disabled'" : "" }} rows="5">{{ $entry->description }}</textarea>
							</div>
						</div>

						<div class="form-group">
							<label for="files" class="col-sm-2 control-label">Files</label>
							<div class="col-sm-10">

								@if ($readonly)
									<p class="entry-closed">Entry is closed</p>
								@else
									<button class="btn btn-primary" data-url="{{ route("station.entry.upload") }}" data-filename="{{ $filename }}"
										onclick="StationEntry.ShowUpload(this); return false">Upload Files</button>
									
									<br />
									<br />
								@endif

								<ul>
									@foreach ($entry->uploadedFiles as $file)
									<li class="{{ $file->isLate($category) ? "late-upload" : "" }}">{{ $file->name }} {{ $file->isLate($category) ? " - (Late)" : "" }}</li>
									@endforeach
								</ul>
								<hr>
								<p>Note: files may a few minutes to show here. If it does not show up, <a href="{{ route("station.files") }}" target="_new">Click here</a> to view all of your uploaded files</p>
							</div>
						</div>


						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<div class="checkbox">
									<label>
										<input id="entryrules" name="entryrules" type="checkbox" {{ $readonly ? "disabled='disabled'" : "" }} {{ $entry->rules ? "checked=\"checked\"" : "" }}> I agree to the <a target="_new" href="{{ route("rules") }}">rules governing the NaSTA Awards {{ Config::get('nasta.year') }}</a>
									</label>
								</div>
							</div>
						</div>
											
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<a href="{{ route("station.categories") }}" class="btn btn-danger">Back</a>

								@if ($closed)
								  <!-- No editing once closed -->
								@elseif ($readonly)
									<button type="submit" class="btn btn-success" id="entryedit">Edit Entry</button>
								@else
									<button type="submit" class="btn btn-primary">Save Draft</button>
									<button type="submit" class="btn btn-success" id="entrysubmit">Submit Entry</button>
								@endif
							</div>
						</div>

					</form>
				</div>
			</div>

		</div>
	</div>
</div>