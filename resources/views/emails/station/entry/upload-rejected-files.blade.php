<!DOCTYPE html>
<html>
<head>
</head>
<body>
  <h1>Uploaded files rejected for {{ $upload->category->name }} - {{ $upload->constraint->name }}</h1>

  <p>You can still submit a new file before the deadline <a href="{{ route('station.submission', $upload->category) }}">here</a>

  <p>The following files were rejected:</p>

  @foreach ($files as $name=>$group)
    <h2>{{ $name }}</h2>

    <ul>
    @foreach($group as $file)
      <li>{{ $file }}</li>
    @endforeach
    </ul>

  @endforeach
</body>
</html>