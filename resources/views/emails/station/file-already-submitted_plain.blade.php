Hi {{ $user->name }},


A new file '{{ $file->name }}' has been added to your already submitted entry for the {{ $category->name }} award.

You can view your entry at {{ route('station.entry', $category) }}

Regards,
The Submissions Team
