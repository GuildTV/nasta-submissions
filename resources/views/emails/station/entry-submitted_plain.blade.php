Hi {{ $user->name }},

We can confirm your entry '{{ $entry->name }}' for the {{ $entry->category->name }} award.
@if ($entry->isLate())
Note: This entry has been marked as late
@endif

Remember that you can still edit this submission up until the deadline.

You can view your full list of entries at {{ route('station.categories') }}

Regards,
The Asset Acquisitions Team