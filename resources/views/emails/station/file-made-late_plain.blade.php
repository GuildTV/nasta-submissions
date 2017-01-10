Hi {{ $user->name }},

We have received your file '{{ $file->name }}' and it has made your entry for the {{ $category->name }} award to be marked as late.
If we have matched this to the wrong entry, please let us know and we shall correct that for you.

You can view your full list of entries at {{ route('station.categories') }}

Regards,
The Asset Acquisitions Team