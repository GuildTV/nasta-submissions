@extends('layouts.email')

@section('content')
  <h2>Hi {{ $user->name }},</h2>  

  <p>We have received your file '{{ $file->name }}' but the automated system was unable to match it to an entry.

  <p>You can link this file to a entry at <a href="{{ route('station.files') }}">here</a> or wait for the submissions team to do it for you.</p>

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection
