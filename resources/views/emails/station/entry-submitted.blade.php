@extends('layouts.email')

@section('content')
  <h2>Hi {{ $user->name }},</h2>  

  <p>We can confirm your entry '{{ $entry->name }}' for the {{ $entry->category->name }} award.</p>

  @if ($entry->isLate())
  <p style="color: #CF5252">Note: This entry has been marked as late</p>
  @endif

  <p>Remember that you can still edit this submission up until the deadline.</p>

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection
