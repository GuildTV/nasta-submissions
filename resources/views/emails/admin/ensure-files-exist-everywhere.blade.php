
{{ count($finalFailures) }} files with failures

@foreach($finalFailures as $file=>$issues)
{{ $file }} - {{ implode(", ", $issues) }}
@endforeach
