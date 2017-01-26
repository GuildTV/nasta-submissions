Hi {{ $user->name }},

Welcome to NaSTA Awards {{ \Config::get('nasta.year') }} Submissions!

We've already generated a username for your station to login with:
  {{ $user->username }}

Before you can start submitting your entries, you'll need to set a password and activate your account at {{ route('auth.reset', $token) }}

If you have any questions about activation or the submissions process in general, please email us at submissions@nasta.tv

Good luck!

Regards,
The Submissions Team
