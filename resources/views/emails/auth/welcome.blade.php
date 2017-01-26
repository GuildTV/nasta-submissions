@extends('layouts.email')

@section('content')
  <h2>Hi {{ $user->name }},</h2>

  <p>Welcome to the NaSTA Awards {{ \Config::get('nasta.year') }}.</p>

  <p>Your username is '{{ $user->username }}' and you can activate you account <a href="{{ route('auth.reset', $token) }}">here</a>.</p>

  <p>The link above expires in 1 week, if required you can get a new link <a href="{{ route('auth.forgot') }}">here</a>.</p>

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection
