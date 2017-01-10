Hi {{ $user->name }},

A reminder of the deadlines coming up.

@foreach ($groupedCategories as $date => $group)
{{ \Carbon\Carbon::parse($date)->format("l jS") }}:
@foreach ($group as $cat)
  * {{ $cat->name }}
@endforeach
@endforeach

You can login to view your entries at {{ route('station.categories') }}

Regards,
The Asset Acquisitions Team
