Hi {{ $user->name }},

This is to confirm your entry '{{ $entry->name }}' for the {{ $entry->category->name }} award.

Remember that you can still edit this submission up until the deadline. You can do so at {{ route('station.entry', $entry->category) }}

Regards,
The Asset Acquisitions Team