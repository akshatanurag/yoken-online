<!DOCTYPE html>
<html>
<head>
    <title>Yoken | Browse Webinars</title>
    <meta name="viewport" content="width=device-width">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/materialize.css">
    <style type="text/css">
        nav {
            background-color: #fff;
        }
        .nav-wrapper {
            padding: 0 30px;
        }
        nav .brand-logo img {
            margin-top: 15px;
            max-height: 40px;

        }
        #main-preloader {
            position: absolute;
            left: 50%;
            top: 50%;
        }
        .course-info {
            position: absolute;
            top: 10px;
            right: 20px;
        }
        .collapsible-body {
            padding: 0;
        }
        .collapsible-body p{
            padding: 1rem;
        }
        h6 {
            font-size: 1.24rem
        }

    </style>
</head>
<body>
@include('partials.nav')
@if(isset($webinar))
    {!! $webinar->room_url !!}
    <script type="text/javascript">
        setTimeout(function(){
            window.location = '/webinar-thank-you';
        }, {{ (strtotime(str_replace('/', '-', $webinar->ends_at)) - time()) * 1000 }});
    </script>
@else
    <br />
    <div class="container">
        <div class="card-panel red lighten-2">
            <span class="white-text">No live webinars currently.</span>
        </div>
    </div>
    @endif
<script src="/js/jquery-3.1.1.min.js"></script>
<script src="/js/materialize.mn.js"></script>
</body>
</html>
