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
        .course-logo {
            width: 250px;
            height: 250px;
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
<body class="grey lighten-3">
@include('partials.nav')

<br>
<div class="container">
<div class="row">
    <div class="col m12 s12">
        @if(empty($webinars->getCollection()->all()))
            <div class="card-panel red lighten-2"><span class="white-text">No webinars currently. We'll be back with our webinars shortly.</span></div>
        @else
            @foreach(array_chunk($webinars->getCollection()->all(),3) as $row)
                <div class="row">
                    @foreach($row as $webinar)
                        <div class="col m4 s12">
                            <div class="card sticky-action">
                                <div class="card-image waves-effect waves-block waves-light">
                                    <i class="material-icons course-info activator white-text">info_outline</i>
                                    <img class="activator course-logo" src="/{{ str_replace(storage_path() . '/app/public', 'storage', $webinar->image_url) }}">
                                    @if($webinar->fees == 0)
                                    <img style="width:75px; position: absolute; top:0" class="responsive-img" src="/img/free.png" alt="">
                                    @endif
                                </div>
                                <div class="card-content">
                                    <span class="card-title truncate activator grey-text text-darken-4 course-name">{{ $webinar->name }}</span>
                                </div>
                                <div class="card-action">
                                    <div class="row" style="margin-bottom: 0">
                                        <div class="col m6 s12">
                                            @if($webinar->fees != 0)
                                            <a class="grey-text text-darken-3 course-discounted-price">
                                                <strong>
                                                    &#8377;{{ $webinar->fees - ($webinar->fees * $webinar->discount)/100 }}
                                                </strong>
                                            </a>
                                            @endif    
                                            <br>
                                            @if($webinar->discount!=0)
                                            <a class="grey-text"><strong><del class="course-price">&#8377;{{ $webinar->fees }}</del></strong></a>
                                            @endif
                                        </div>
                                        <div class="col m6 s12">
                                            <div class="row">
                                                <a class="waves-effect waves-light btn" href="/webinar/register/{{$webinar->id}}">Register</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col m4 s12">
                                            <strong>Starts At</strong>
                                        </div>
                                        <div class="col m8 s12">
                                            <em>{{date("d/m/Y H:i", strtotime(str_replace('/', '-', $webinar->starts_at)))}}</em>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col m4 s12">
                                            <strong>Ends At</strong>
                                        </div>
                                        <div class="col m8 s12">
                                            <em>{{date("d/m/Y H:i", strtotime(str_replace('/', '-', $webinar->ends_at)))}}</em>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-reveal">
                                    <span class="card-title grey-text text-darken-4 course-title"><i class="material-icons right">close</i></span>
                                    <h6 class="grey-text text-darken-1">About the webinar:</h6>
                                    <p class="course-description">
                                        {{ $webinar->description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
        {{ $webinars->links() }}
        <div class="row center-align">
            <ul class="pagination" id="page-numbers">
            </ul>
        </div>
    </div>
</div>
</div>
@include('partials.footer')

</body>
</html>
