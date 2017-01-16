Hi {{ $user->name }},

@if (count($entries) == 0)
You did not enter any of the categories that close today.
@else
Your summary of todays deadlines:
@endif

@foreach ($categories as $cat)
<?php
  echo " * ";
  echo $cat->name;
  echo ": ";

  if (isset($entries[$cat->id])) {
    $entry = $entries[$cat->id];
    echo $entry->name;
    echo " - ";
    echo $entry->uploadedFiles()->count();
    echo " file(s)";

    if ($entry->isLate())
      echo " - (This entry was submitted late)";

  } else {
    echo "No entry";
  }

?>
@endforeach


You can view your full list of entries at {{ route('station.categories') }}

Regards,
The Submissions Team
