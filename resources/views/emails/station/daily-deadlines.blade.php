<!DOCTYPE html>
<html>
<head>
</head>
<body>
  <p>Hi {{ $user->name }},</p>

  <p>A reminder of the deadlines coming up</p>

  @foreach ($groupedCategories as $date => $group)
    <h3>{{ \Carbon\Carbon::parse($date)->format("l jS") }}:</h3>

    <ul>
    @foreach ($group as $cat)
      <li>{{ $cat->name }}</li>
    @endforeach
    </ul>

  @endforeach

  <p>You can login to view your entries <a href="{{ route('station.categories') }}">here</a>.</p>

  <p>Regards,<br/ >The Asset Acquisitions Team</p>
</body>
</html>