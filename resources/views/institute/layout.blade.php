<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/materialize.css">
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    @yield('header')
    <style type="text/css">
        .top-nav {
        }
        header, footer {
            padding-left: 240px;
        }
        main {
            padding-left: 260px;
        }
        .brand-logo img {
            margin-top: 10px;
            max-height: 70px;
        }
        #page-title {
            font-size: 32px;
        }
        .nav-wrapper {
            padding-left: 20px;
        }
        #logo-container {
            padding: 20px 50px;
        }
        #year-wise-sales {

        }
        @media only screen and (max-width : 992px) {
            header, main, footer {
                padding-left: 0;
            }
            .nav-wrapper {
                padding-left: 0;
            }
        }
    </style>
</head>
<body>
<header>
    <nav class="top-nav white">
        <div class="nav-wrapper">
            <div class="container"><a href="#" data-activates="nav-mobile" class="button-collapse top-nav full hide-on-large-only">
                    <i class="material-icons">menu</i></a>
            </div>
            <span id="page-title">Dashboard</span>
        </div>
    </nav>
    @include('partials.institute-sidebar')
</header>
<br>
<main>
    @yield('main-content')
</main>
<script src="/js/jquery-3.1.1.min.js"></script>
<script src="/js/materialize.min.js"></script>
<script>
    $(document).ready(function() {
        $('.button-collapse').sideNav({
            menuWidth: 240
        });
    });
</script>
    @yield('footer')
</body>
</html>