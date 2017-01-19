@extends('layouts.email')

@section('content')
  <h2>Hi {{ $user->name }},</h2>

  <p>The Awards Categories that close today are:</p>

  @foreach ($groupedCategories as $date => $group)
    <ul class="clean">
      @foreach ($group as $cat)
        <li>{{ $cat->name }}</li>
      @endforeach
    </ul>

  @endforeach

  <p>You can view your full list of entries <a href="{{ route('station.categories') }}">here</a>.</p>

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection
