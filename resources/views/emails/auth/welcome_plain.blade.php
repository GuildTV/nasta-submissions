Hi {{ $user->name }},

Welcome to the NaSTA Awards {{ \Config::get('nasta.year') }}.

Your username is '{{ $user->username }}' and you can activate you account at {{ route('auth.reset', $token) }}

The link above expires in 1 week, if required you can get a new link at {{ route('auth.forgot')  }}

Regards,
The Submissions Team
