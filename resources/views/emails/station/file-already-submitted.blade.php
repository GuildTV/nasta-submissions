@extends('layouts.email')

@section('content')
  <h2>Hi {{ $user->name }},</h2>

  <p>A new file '{{ $file->name }}' has been added to your already submitted entry for the {{ $category->name }} award.</p>

  <p>You can view your entry <a href="{{ route('station.entry', $category) }}">here</a>.</p>

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection