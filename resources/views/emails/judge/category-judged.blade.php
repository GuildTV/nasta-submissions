@extends('layouts.email')

@section('content')
  <h2>Hi,</h2>

  <p>{{ $category->name }} has been marked finalized.</p>

  <p>You can view the results <a href="{{ route('admin.results.view', $category) }}">here</a></p>

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection
