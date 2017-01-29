@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-8 col-md-offset-2">
      <div class="panel panel-default">
        <div class="panel-heading">Help</div>

        <div class="panel-body">
          <p class="center">
            <a class="btn" href="{{ \Config::get('nasta.assets.guide') }}">Submissions Guide</a>
          </p>

          <h4 class="center">Lineup</h4>
          <p class="center">
            <a class="btn" href="{{ \Config::get('nasta.assets.lineup.example') }}">Example</a>
            <a class="btn" href="{{ \Config::get('nasta.assets.lineup.bg') }}">Background</a>
            <a class="btn" href="{{ \Config::get('nasta.assets.lineup.overlay') }}">Overlay</a>
          </p>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
