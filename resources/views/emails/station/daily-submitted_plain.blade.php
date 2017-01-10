Hi {{ $user->name }},

@if (count($entries) == 0)
You did not make an entries for today's deadlines. Not to worry, there are other awards you can enter on other days!
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
      echo " files";

    if ($entry->isLate())
      echo " (LATE ENTRY)";

  } else {
    echo "No entry";
  }

?>
@endforeach


You can view your full list of entries at {{ route('station.categories') }}

Regards,
The Asset Acquisitions Team
