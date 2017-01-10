<!DOCTYPE html>
<html>
<head>
</head>
<body>
  <p>Hi {{ $user->name }},</p>

  <p>This is to confirm your entry '{{ $entry->name }}' for the {{ $entry->category->name }} award.</p>

  @if ($entry->isLate())
  <p>Note: Your entry has been marked as late</p>
  @endif

  <p>Remember that you can still edit this submission up until the deadline. You can do so <a href="{{ route('station.entry', $entry->category) }}">here</a>.</p>

  <p>Regards,<br/ >The Asset Acquisitions Team</p>
</body>
</html>