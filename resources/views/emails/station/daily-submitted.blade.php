<!DOCTYPE html>
<html>
<head>
</head>
<body>
  <p>Hi {{ $user->name }},</p>

  @if (count($entries) == 0)
  <p>You did not make an entries for today's deadlines. Not to worry, there are other awards you can enter on other days!</p>
  @else
  <p>Your summary of todays deadlines:</p>
  @endif

  <ul>
  @foreach ($categories as $cat)
  <li>
  <?php
    echo $cat->name;
    echo ": ";

    $entry = $entries[$cat->id];
    if ($entry) {
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
  </li>
  @endforeach
  </ul>

  <p>You can view your full list of entries <a href="{{ route('station.categories') }}">here</a>.</p>

  <p>Regards,<br/ >The Asset Acquisitions Team</p>
</body>
</html>
