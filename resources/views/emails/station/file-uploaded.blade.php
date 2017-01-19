@extends('layouts.email')

@section('content')
  <h2>Hi {{ $user->name }},</h2>  

  <p>'{{ $file->name }}' has successfully been added to your entry for the {{ $file->category->name }} award.</p>

  @if ($entry != null && !$entry->submitted)
  <p>Remember that you still need to submit your entry before the deadline.</p>
  @endif

  @if ($file->category != null)
  <p>You can still view or edit your entry <a href="{{ route('station.entry', $file->category) }}">here</a></p>
  @else
  <p>You can link this file to a entry <a href="{{ route('station.files') }}">here</a></p>
  @endif

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection
