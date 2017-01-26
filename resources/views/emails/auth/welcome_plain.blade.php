Hi {{ $user->name }},

Welcome to the NaSTA Awards {{ \Config::get('nasta.year') }}.

Your username is '{{ $user->username }}' and you can activate you account at {{ route('auth.reset', $token) }}

Regards,
The Submissions Team
