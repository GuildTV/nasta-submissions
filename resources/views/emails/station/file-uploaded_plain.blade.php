Hi {{ $user->name }},

'{{ $file->name }}' has successfully been added to your entry for the {{ $file->category->name }} award.

@if ($entry != null && !$entry->submitted)
Remember that you still need to submit your entry before the deadline
@endif

@if ($file->category != null)
You can still view or edit your entry at {{ route('station.entry', $file->category) }}
@else
You can link this file to a entry at {{ route('station.files') }}
@endif

Regards,
The Submissions Team
