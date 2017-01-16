Hi {{ $user->name }},

Awards Categories that close today:-

@foreach ($groupedCategories as $date => $group)
@foreach ($group as $cat)
  * {{ $cat->name }}
@endforeach
@endforeach

You can view your full list of entries at {{ route('station.categories') }}

Regards,
The Asset Acquisitions Team
