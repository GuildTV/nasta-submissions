<!DOCTYPE html>
<html>
<head>
</head>
<body>
  <h1>File upload has been accepted for {{ $upload->category->name }} - {{ $upload->constraint->name }}</h1>

  <p>Using file: {{ $filename }}</p>

  <p>You can submit a new file before the deadline <a href="{{ route('station.submission', $upload->category) }}">here</a>
</body>
</html>