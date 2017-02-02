Hi {{ $user->name }},

You are receiving this email because we received a password reset request for your account.

{{ route('auth.reset', $token) }}

If you did not request a password reset, no further action is required.


Regards,
The Submissions Team
