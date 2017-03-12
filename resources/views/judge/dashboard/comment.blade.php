@if ($entry != null)
  <legend>{{ $title }}</legend>

  <input type="hidden" id="{{ $prefix }}_id" value="{{ $entry->id }}" />

  <div class="form-group">
    <label class="col-sm-2 control-label">Entry</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" disabled='disabled' value="{{ $entry->name}} ({{ $entry->station->name }})">
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-2 control-label">Comment</label>
    <div class="col-sm-10">
      <input id="{{ $prefix }}_comment" name="{{ $prefix }}_comment" type="text" class="form-control" maxlength="200" 
        {!! $adminVersion || $category->isResultsReadOnly() ? "disabled='disabled'" : "placeholder=\"Please provide a short comment for the certificate\"" !!}
        value="{{ $category->result != null ? $category->result->getAttribute($prefix . '_comment') : "" }}">
    </div>
  </div>
@endif