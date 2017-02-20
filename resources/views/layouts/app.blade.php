<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="Submit your entries to NaSTA Awards 2017.">

    <link rel="icon" href="/images/icons/favicon.ico">

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        @if (App::isDownForMaintenance() || (isset($name) && isset($code) && $name == "Internal Server Error" && $code == 500))
                            <!-- Nuke navbar in maintenance or exception mode -->
                        @elseif (Auth::guest())
                            <!--
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    Help <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ route("help.rules") }}">Rules</a></li>
                                    <li><a href="{{ route("help.video-format") }}">Video Format</a></li>
                                    <li><a href="{{ route("help.contact") }}">Contact Us</a></li>
                                </ul>
                            </li>
                            -->
                            <li><a href="{{ route('help') }}">Help</a></li>

                            <li><a href="{{ url('/login') }}">Login</a></li>
                        @else
                            @if (Auth::user()->type == "admin")
                                <li><a href="{{ route("admin.dashboard") }}">Dashboard</a></li>

                            @elseif (Auth::user()->type == "judge")

                            @else
                                <li><a <?=(@View::getSections()['page_selected']=="categories"?' class="active"':'')?> href="{{ route("station.categories") }}">Categories</a></li>
                                <li><a <?=(@View::getSections()['page_selected']=="files"?' class="active"':'')?> href="{{ route("station.files") }}">Files</a></li>
                                <!--<li><a <?=(@View::getSections()['page_selected']=="results"?' class="active"':'')?> href="{{ route("station.results") }}">Results</a></li>-->
                            @endif

                            <!--
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    Help <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ route("help.rules") }}">Rules</a></li>
                                    <li><a href="{{ route("help.video-format") }}">Video Format</a></li>
                                    <li><a href="{{ route("help.contact") }}">Contact Us</a></li>
                                </ul>
                            </li>
                            -->
                            
                            <li><a href="{{ route('help') }}">Help</a></li>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ route("station.settings") }}">Settings</a></li>
                                    <li>
                                        <a href="{{ url('/logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <section class="page-content">
            @yield('content')
        </section>

        <footer class="footer">
            <div class="footer__content" role="presentation">
                <p>&copy; The NaSTA Conference and Awards Weekend 2017</p>
            </div>
        </footer>
    </div>

    @yield('modals')

    <!-- Scripts -->
    <script src="{{ elixir('js/app.js') }}"></script>

    <script type="text/javascript">
        @yield('js')
    </script>
</body>
</html>
