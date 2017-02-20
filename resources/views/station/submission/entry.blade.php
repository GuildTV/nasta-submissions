<div class="col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">Entry</div>

			<div class="row">
				<div class="col-md-12">
					<form id="entryform" class="form-horizontal" onsubmit="return false">

						<input type="hidden" id="entrycategory" value="{{ $category->id }}">

						@if ($entry->submitted)
						<div id="alert_holder" class="row">
							<div class="alert col-sm-10 col-sm-offset-1 {{ $entry->isLate($category) ? "alert-warning" : "alert-success" }}" id="update_status">
								<p>You submitted your entry on the {{ $entry->updated_at->format("jS F \\a\\t H:i") }}</p>
								@if ($entry->isLate($category))
									<p class="late-upload">Your entry has been marked as late!!</p>
								@endif
								<p><a href="{{ route('station.categories') }}">Back</a> to categories list</p>
							</div>
						</div>
						@elseif (\Session::has('entry.edit'))
						<div id="alert_holder" class="row">
							<div class="alert alert-warning col-sm-10 col-sm-offset-1">
								<p>{{ \Session::get('entry.edit') }}</p>
							</div>
						</div>
						@elseif (\Session::has('entry.save'))
						<div id="alert_holder" class="row">
							<div class="alert alert-success col-sm-10 col-sm-offset-1">
								<p>{{ \Session::get('entry.save') }}</p>
							</div>
						</div>
						@endif

						<div class="form-group">
							<label for="entryname" class="col-sm-2 control-label">Entry Name</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" id="entryname" name="entryname" maxlength="255" placeholder="Entry Name" {{ $readonly ? "disabled='disabled'" : "" }} value="{{ $entry->name }}">
							</div>
						</div>
						
						<div class="form-group">
							<label for="entrydescription" class="col-sm-2 control-label">Description</label>
							<div class="col-sm-9">
								<textarea class="form-control" id="entrydescription" name="entrydescription" {{ $readonly ? "disabled='disabled'" : "" }} rows="5">{{ $entry->description }}</textarea>
							</div>
						</div>

						<div class="form-group">
							<label for="files" class="col-sm-2 control-label">Files</label>
							<div class="col-sm-9">

								@if ($readonly)
									<p class="entry-closed">Entry is closed</p>
								@else
									<button class="btn btn-primary" data-url="{{ route("station.entry.upload", $category) }}" data-filename="{{ $filename }}"
										onclick="StationEntry.ShowUpload(this); return false">Upload Files</button>
									
									<br />
									<br />
								@endif

								<div id="too_many_files_holder" class="row {{ $entry->uploadedFiles->count() <= $category->constraints->count() ? "hidden" : "" }}">
									<div class="alert alert-danger col-sm-12">
										<p>You have too many files for this entry. Only {{ $category->constraints->count() }} files are expected.</p>
									</div>
								</div>
								<table class="table" id="files-table">
									<thead>
										<th>Filename</th>
										<th>Uploaded At</th>
										<th>Status</th>
										<th id="countdown-holder" class="center">&nbsp;</th>
									</thead>
									<tbody id="file-table-body">
										@foreach ($entry->uploadedFiles as $file)
										<?php
											$errors = [];
											if ($file->rule_break != null){
												$errs = json_decode($file->rule_break->errors, true);
												foreach ($errs as $err){
													$errors[] = trans("rule_break.error." . $err);
												}
											}
										?>
										<tr>
											<td>{{ $file->name }}</td>
											<td>{{ $file->uploaded_at != null ? $file->uploaded_at->toDayDateTimeString() : " - " }}</td>
											<td>{{ $file->rule_break == null ? "pending" : ($file->rule_break->errors == "[]" ? "ok" : "fail") }}</td>
											<td>
												<button class="btn btn-info btn-small" data-id="{{ $file->id }}" data-name="{{ $file->name }}" 
													data-type="{{ $file->metadata ? "video" : "other" }}" data-url="{{ route('station.files.download', $file) }}"
													data-errors="{{ json_encode($errors) }}"
													onclick="window.StationCommon.ViewFile(this); return false">View</button>
											@if (!$readonly)
												<button class="btn btn-danger btn-small" data-id="{{ $file->id }}" onclick="window.StationEntry.DeleteFile(this); return false">Delete</button>
											@endif
											</td>
										</tr>
										@endforeach
									</tbody>
								 </table>

								<hr>
								<p>Note: files may a few minutes to show here. If it does not show up, <a href="{{ route("station.files") }}" target="_new">Click here</a> to view all of your uploaded files</p>
							</div>
						</div>


						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-9">
								<div class="checkbox">
									<label>
										<input id="entryrules" name="entryrules" type="checkbox" {{ $readonly ? "disabled='disabled'" : "" }} {{ $entry->rules ? "checked=\"checked\"" : "" }}> I agree to the <a target="_new" href="{{ route("help") }}">rules governing the NaSTA Awards {{ Config::get('nasta.year') }}</a>
									</label>
								</div>
							</div>
						</div>
											
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-9">
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