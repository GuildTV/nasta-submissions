@extends('layouts.email')

@section('content')
  <h2>Hi {{ $user->name }},</h2>  

  <p>We have received your file '{{ $file->name }}' and it has made your entry for the {{ $category->name }} award to be marked as late.</p>

  <p>If we have matched this to the wrong entry, please let us know and we look in to the issue.</p>

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection
