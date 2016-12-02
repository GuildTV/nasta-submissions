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
								<input type="text" class="form-control" id="entryname" maxlength="255" placeholder="Entry Name" {{ $readonly ? "disabled='disabled'" : "" }} value="{{ $entry->name }}">
							</div>
						</div>
						
						<div class="form-group">
							<label for="entrydescription" class="col-sm-2 control-label">Description</label>
							<div class="col-sm-10">
								<textarea class="form-control" id="entrydescription" {{ $readonly ? "disabled='disabled'" : "" }} rows="5">{{ $entry->description }}</textarea>
							</div>
						</div>

						<div class="form-group">
							<label for="files" class="col-sm-2 control-label">Files</label>
							<div class="col-sm-10">

								@if ($readonly)
									<p>You cannot upload new files to your submitted entry</p>
								@else
									<a target="_new" class="btn btn-primary" onclick="StationEntry.OpenFolder()" href="{{ route("station.entry.upload", [$category]) }}">Open folder</a>
									<button class="btn btn-success" onclick="StationEntry.ReloadFileList(); return false">Refresh folder</button>

									<br />
									<br />
								@endif

								<iframe id="filelist" src="{{ route('station.submission.folderembed', $category) }}" data-src="{{ route('station.submission.folderembed', $category) }}"" style="width:100%; height:250px; border:1;"></iframe>
							</div>
						</div>


						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<div class="checkbox">
									<label>
										<input id="entryrules" type="checkbox" {{ $readonly ? "disabled='disabled'" : "" }} {{ $entry->rules ? "checked=\"checked\"" : "" }}> I agree to the <a target="_new" href="{{ route("rules") }}">rules governing the NaSTA Awards {{ Config::get('nasta.year') }}</a>
									</label>
								</div>
							</div>
						</div>
											
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								@if ($readonly)
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