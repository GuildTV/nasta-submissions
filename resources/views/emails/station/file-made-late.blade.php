<!DOCTYPE html>
<html>
<head>
</head>
<body>
  <p>Hi {{ $user->name }},</p>

  <p>We have received your file '{{ $file->name }}' and it has made your entry for the {{ $category->name }} award to be marked as late.</p>
  <p>If we have matched this to the wrong entry, please let us know and we shall correct that for you.</p>

  <p>You can login to view your entries <a href="{{ route('station.categories') }}">here</a>.</p>

  <p>Regards,<br/ >The Asset Acquisitions Team</p>
</body>
</html>