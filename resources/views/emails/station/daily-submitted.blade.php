@extends('layouts.email')

@section('content')
  <h2>Hi {{ $user->name }},</h2>

  @if (count($entries) == 0)
  <p>You did not enter any of the categories that close today.</p>
  @else
  <p>Your summary of todays deadlines:</p>
  @endif

  <ul class="clean">
  @foreach ($categories as $cat)
  <li>
  <?php
    echo $cat->name;
    echo ": ";

    if (isset($entries[$cat->id])) {
      $entry = $entries[$cat->id];
      echo $entry->name;
      echo " - ";
      echo $entry->uploadedFiles()->count();
      echo " file(s)";

      if ($entry->isLate())
        echo " - <span style='color: #CF5252'>(This entry was submitted late)</span>";

    } else {
      echo "No entry";
    }

  ?>
  </li>
  @endforeach
  </ul>

  <p>You can view your full list of entries <a href="{{ route('station.categories') }}">here</a>.</p>

  <h2>
    Regards,
    <br/ ><br/ >
    The Submissions Team
  </h2>
@endsection
