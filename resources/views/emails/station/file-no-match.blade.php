<!DOCTYPE html>
<html>
<head>
</head>
<body>
  <p>Hi {{ $user->name }},</p>

  <p>We have received your file '{{ $file->name }}' but the automated system was unable to match it to an entry.</p>

  <p>You can link this file to a entry <a href="{{ route('station.files') }}">here</a> or wait for the acquisitions team to do it for you.</p>

  <p>Regards,<br/ >The Asset Acquisitions Team</p>
</body>
</html>