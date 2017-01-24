<div class="panel panel-default">
  <div class="panel-heading">Station Config</div>

  <div class="row">
    <div class="col-md-11 col-md-offset-1">

      <form class="form-horizontal" onsubmit="window.AdminUsers.SubmitDropbox();return false">

        <div class="form-group">
          <div class="col-sm-6 col-sm-offset-3">
          <a class="btn btn-info" href="{{ route('admin.submissions.station', $user) }}">View submissions</a>
          </div>
        </div>

        <legend>Dropbox Folder</legend>

        <div class="form-group">
          <label for="dropboxaccount" class="col-sm-3 control-label">Dropbox Account</label>
          <div class="col-sm-6">
            <select class="form-control" id="dropboxaccount" name="dropboxaccount">
            @foreach ($dropboxAccounts as $acc)
              <option value="{{ $acc->id }}" {{ $acc->id == $user->stationFolderOrNew()->account_id ? " selected=\"selected\"" : "" }}>{{ $acc->id }}</option>
            @endforeach
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="dropboxfolder" class="col-sm-3 control-label">Dropbox Folder</label>
          <div class="col-sm-6">
            <input type="text" class="form-control" id="dropboxfolder" name="dropboxfolder" maxlength="255" value="{{ $user->stationFolderOrNew()->folder_name }}" />
          </div>
        </div>

        <div class="form-group">
          <label for="dropboxurl" class="col-sm-3 control-label">Dropbox URL</label>
          <div class="col-sm-6">
            <input type="text" class="form-control" id="dropboxurl" name="dropboxurl" maxlength="255" value="{{ $user->stationFolderOrNew()->request_url }}" />
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-7 col-sm-2">
            <button type="submit" class="btn btn-success" id="usersave">Save</button>
          </div>
        </div>

      </form>
    </div>
  </div>

</div>