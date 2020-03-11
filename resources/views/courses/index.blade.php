<!DOCTYPE html>
<html>
<head>
    <title>Yoken | Browse Courses</title>
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
        .filter-head {
            font-size: 0.90rem;
            text-transform: uppercase;
            font-weight: 500

        }
        .filter-card form {
            background: #fff;
        }
        .view-course-btn {
            //text-transform: none !important;
        }
        .promo-label {
            position: absolute;
            top: 10px;
            left: 20px;
            background: #fff;
            opacity: 0.7;
            color: #000;
            padding: 3px 10px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        .course-info {
            position: absolute;
            top: 10px;
            right: 20px;
        }
        @media only screen and (max-width : 992px) {
            .filter-card {
                position: fixed;
                transform: translateX(-700px);
                transition: transform 0.3s;
                z-index: 900;
                height: 85vh;
                overflow: auto;
            }
            .filter-card-show {
                display: block;
                transform: translateX(0px);
                overflow: scroll;
            }
        }
    </style>
</head>
<body>
@include('partials.nav')
<a class="btn white waves-effect hide-on-med-and-up" id="filter-in-btn" style="position: fixed; bottom:0;z-index: 999;margin:0;width:100%;text-align: center">
    <span class="grey-text text-darken-4">Filters</span>
</a>
<div class="preloader-wrapper" id="main-preloader">
    <div class="spinner-layer">
        <div class="circle-clipper left">
            <div class="circle"></div>
        </div>
        <div class="gap-patch">
            <div class="circle"></div>
        </div>
        <div class="circle-clipper right">
            <div class="circle"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col m3 s12 filter-card">
        <div class="card-panel light-green lighten-5">
            <h6>Filters</h6>
            <div class="divider"></div>
            <form action="" method="get">
                {{csrf_field()}}
                <input type="hidden" value="{{$keyword}}" name="q">
            <ul class="collapsible" data-collapse="accordion">
                <li>
                    <div class="collapsible-header active">
                    <p class="filter-head">Category</p>
                    </div>
                    <div class="collapsible-body">
                        @foreach($categories as $category)
                            <p>
                                @if(isset($selectedCategories) && in_array($category->id,$selectedCategories))
                                    <input type="checkbox" id="yoken-{{str_replace(" ","-",strtolower($category->name))}}"
                                           name="categories[]" checked value="{{$category->id}}"/>
                                @else
                                    <input type="checkbox" id="yoken-{{str_replace(" ","-",strtolower($category->name))}}"
                                           name="categories[]" value="{{$category->id}}"/>
                                @endif
                                <label for="yoken-{{str_replace(" ","-",strtolower($category->name))}}">
                                    {{$category->name}}
                                </label>
                            </p>
                        @endforeach
                    </div>
                </li>
                <li>
                    <div class="collapsible-header">
                        <p class="filter-head">Location</p>
                    </div>
                    <div class="collapsible-body">
                        @foreach($locations as $location)
                            <p>
                                @if(isset($selectedLocations) && in_array($location,$selectedLocations))
                                    <input type="checkbox" id="yoken-{{str_replace(" ","-",strtolower($location))}}"
                                           name="locations[]" checked value="{{$location}}"/>
                                @else
                                    <input type="checkbox" id="yoken-{{str_replace(" ","-",strtolower($location))}}"
                                           name="locations[]" value="{{$location}}"/>
                                @endif
                                <label for="yoken-{{str_replace(" ","-",strtolower($location))}}">
                                    {{$location}}
                                </label>
                            </p>
                        @endforeach
                    </div>
                </li>
            </ul>
                <input class="btn waves-effect" type="submit" value="Apply">
            </form>
        </div>
    </div>
    <div class="col m9 s12">
        @if($keyword!='')
        <header class="page-header">
            <h5>Results for query <span class="location-title green-text">{{ '"' . $keyword . '"' }}</span></h5>
        </header>
        @endif
        <form method="get" action="">
            {{csrf_field()}}
            <div class="row search-field">
                <div class="input-field col m5 s12">
                    <i class="material-icons tiny prefix">search</i>
                    <input type="text" name="q" id="course">
                    @if(!empty($selectedCategories))
                        @foreach($selectedCategories as $category)
                            <input type="hidden" name="categories[]" value="{{$category}}" />
                        @endforeach
                    @endif

                    @if(!empty($selectedLocations))
                        @foreach($selectedLocations as $location)
                            <input type="hidden" name="locations[]" value="{{$location}}" />
                        @endforeach
                    @endif
                    <label for="course">Search</label>
                </div>
            </div>
        </form>
        @if(empty($courses->all()))
            {!!
                '<div class="card-panel red lighten-2">
                    <span class="white-text">No results matching the requested query were found.<span>
                </div>'
            !!}
        @else
        @foreach(array_chunk($courses->all(),3) as $row)
            <div class="row">
            @foreach($row as $course)
                <div class="col m4">
                    <div class="card sticky-action">
                        <div class="card-image" style="cursor: pointer;">
                            <!--<span class="promo-label">Promo offer here</span>-->
                            <i class="material-icons course-info activator white-text">info_outline</i>
                            <img class="activator course-logo" src="/{{ str_replace(storage_path() . '/app/public', 'storage', $course->pic_link) }}">
                        </div>
                        <div class="card-content">
                            <span class="card-title truncate activator grey-text text-darken-4 course-name">{{ $course->name }}</span>
                            <p><a class="institute-name truncate">{{ $course->has('institute')?$course->institute->name : ''}}</a></p>
                        </div>
                        <div class="card-action">
                            <div class="row" style="margin-bottom: 0">
                                <div class="col m6 s6">
                                    <a class="grey-text text-darken-3 course-discounted-price">
                                        <strong>
                                            &#8377;{{ $course->fees - ($course->fees * $course->discount)/100 }}
                                        </strong>
                                    </a>
                                    <br>
                                    @if($course->discount > 0.0)
                                    <a class="grey-text"><strong><del class="course-price">&#8377;{{ $course->fees }}</del></strong></a>
                                    @endif
                                </div>

                                <div class="col m6 s6">
                                    <div class="row">
                                        <a class="btn-flat yellow darken-4 waves-effect view-course-btn" href="/view-course/{{$course->id}}"><span class="white-text">&nbsp;&nbsp;&nbsp;View&nbsp;&nbsp;&nbsp;</span></a>
                                    </div>
                                    <div class="row">
                                    <?php $batch_available = false ; ?>
                                    @foreach($course->batches()->get() as $batch)
                                        @if(strtotime(str_replace("/", "-", $batch->commence_date)) > time())
                                            <?php $batch_available = true ; ?>
                                        @endif
                                    @endforeach
                                    @if($batch_available)
                                        <a class="waves-effect waves-light btn" href="/enroll/{{$course->id}}">Enroll</a>
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        @endforeach
        @endif
        <div class="row center-align">
            <ul class="pagination" id="page-numbers">
                {{ $courses->appends(request()->only(['q', '_token', 'categories', 'locations']))->links() }}
            </ul>
        </div>
    </div>
</div>
@include('partials.footer')
<script src="/js/morphext.min.js"></script>
<script src="/js/wNumb.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#filter-in-btn').click(function() {
            $('body').toggleClass('body-disable');
            $('.filter-card').toggleClass('filter-card-show');
            $('.overlay-hidden').toggleClass('overlay-show');
        });
    });
    $(".card.sticky-action .card-image").click(function(ev){
        ev.preventDefault();
        this.parentElement.querySelector(".view-course-btn").click();
    })
</script>
</body>
</html>
