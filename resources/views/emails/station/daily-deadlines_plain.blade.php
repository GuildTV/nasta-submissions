Hi {{ $user->name }},

Awards Categories that close today:-

@foreach ($groupedCategories as $date => $group)
@foreach ($group as $cat)
  {{ $cat->name }}
@endforeach
@endforeach

Regards,
The Submissions Team
