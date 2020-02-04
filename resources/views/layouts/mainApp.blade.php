<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="{{ asset('js/app.js') }}"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">

</head>
<body>
<div id="app">
    <nav class="navbar navbar-tc navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse" aria-expanded="false">
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
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @guest
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <button type="button" class="btn btn-tc dropdown-toggle" data-toggle="dropdown"
                               aria-expanded="false" aria-haspopup="true" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Выход
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
                
            </div>
        </div>
    </nav>

    <div class="container" style="width: 99% !important">
        <div class="row">
            <div class="col-sm-3 left-menu">

                    <div class="collapse navbar-collapse" id="app-navbar-collapse">
                        <!-- Left Side Of Navbar -->
                        <ul class="nav navbar-nav">
                            &nbsp;
                        </ul>
        
                        <!-- Right Side Of Navbar -->
                        <ul class="nav navbar-nav navbar-left" style="width: 100%">
                            <?php
                            $admin = Auth::user()->adm;    
                            ?>

                            @if($admin == 1)
        
                            <li name="departments" style="width: 100%"><a href="{{ route('auth.departments.index') }}">Отделы исполнителя</a></li>

                            <li name="clients" style="width: 100%"><a href="{{ route('auth.clients.index') }}">Клиенты</a></li>
        
                            @endif
        
                            <li name="masters" style="width: 100%"><a href="{{ route('auth.masters.index') }}">Мастера</a></li>

                            <li name="inquiries" style="width: 100%"><a href="{{ route('auth.inquiries.index') }}">Запросы</a></li>

                        </ul>
                    </div>
            </div>
            <div class="col-sm-9 tabs-content">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    $(document).ready(function() {

        let pathname = window.location.pathname;

        switch(pathname.split('/')[1]) {
        case '':
            $( "li[name='news']" ).addClass( "active" );
            break;

        case 'news':
            $( "li[name='news']" ).addClass( "active" );
            break;

        case 'clients':
            $( "li[name='clients']" ).addClass( "active" );
            break;

        case 'cabinets':
            $( "li[name='cabinets']" ).addClass( "active" );
            break;

        case 'calendar':
            $( "li[name='calendar']" ).addClass( "active" );
            break;

        case 'notifications':
            $( "li[name='notifications']" ).addClass( "active" );
            break;
        }
    })
</script>
</body>
</html>
