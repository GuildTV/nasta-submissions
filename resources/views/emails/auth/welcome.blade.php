@extends('layouts.email')

@section('content')
  <h2>Hi {{ $user->name }},</h2>

  <p>Welcome to NaSTA Awards {{ \Config::get('nasta.year') }} Submissions!</p>

  <p>We've already generated a username for your station to login with:</p>
  <p><span style="font-weight:bold">{{ $user->username }}</span></p>

  <p>Before you can start submitting your entries, you'll need to set a password and activate your account <a href="{{ route('auth.reset', $token) }}">here</a>.</p>
  
  <p>If you have any questions about activation or the submissions process in general, please email us at <a href="mailto:submissions@nasta.tv">submissions@nasta.tv</a>.</p>

  <p>Good luck!</p>

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection
