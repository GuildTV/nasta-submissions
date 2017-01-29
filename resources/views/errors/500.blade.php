@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <h3>An unexpected error occured. The submissions team have been notified!</h3>
        </div>
    </div>
    <br/>
    <br/>
    @include('errors.smpte')
</div>
@endsection