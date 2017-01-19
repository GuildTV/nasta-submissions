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

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection
