@extends('layouts.email')

@section('content')
  <h2>Hi {{ $user->name }},</h2>

  <p>You are receiving this email because we received a password reset request for your account.</p>

  <a href="{{ route('auth.reset', $token) }}">Reset Password</a>

  <p>If you did not request a password reset, no further action is required.</p>

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection
