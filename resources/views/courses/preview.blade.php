<?php
/**
 * Created by: Amandeep.
 *For: YokenOnline
 * Date: 3/16/17
 * Time: 10:41 PM
 */
?>
        <!DOCTYPE html>
<html>
<head>
    <title>Yoken | Course</title>
    <meta name="viewport" content="width=device-width">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/materialize.css">
    <style type="text/css">
        nav {
            background-color: #fff;
        }
        .nav-wrapper {
            padding: 0 30px;
        }
        nav .brand-logo img {
            margin-top: 10px;
            max-height: 40px;
        }
        h6 {
            font-size: 1.24rem
        }
        #faculty-image {
            height: 120px;
            width: 120px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col m8 s12">
            <h5 id="title-course-name">{{$course->name}}</h5>
            <br>
            <h6><strong>Course overview</strong></h6>
            <p id="course-description">
                {{$course->description}}
            </p>
        </div>
        <div class="col m4 s12">
            <div class="card">
                <div class="card-image">
                    <img src="{{$course->pic_link}}">
                </div>
                <div class="card-content">
                    <div class="row">
                        <div class="col m6 s6"><strong>Offered By</strong></div>
                        <div class="col m6 s6">{{$course->institute->name}}</div>
                    </div>
                    <div class="row">
                        <div class="col m6 s6"><strong>Duration</strong></div>
                        <div class="col m6 s6">{{$course->duration}} {{$course->duration_type}}</div>
                    </div>
                    <div class="row">
                        <div class="col m6 s6"><strong>Demo classes</strong></div>
                        <div class="col m6 s6">{{$course->demo_classes}}</div>
                    </div>
                    <div class="row">
                        <div class="col m6 s6"><strong>Fees</strong></div>
                        <div class="col m6 s6">
                            <span>&#8377;{{ $course->fees - ($course->fees * $course->discount)/100 }}</span>
                            <span class="grey-text text-darken-1"><del>&#x20b9;{{$course->fees}}</del></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col m6 s6"><strong>Batches commencing on:</strong></div>
                        <div class="col m6 s6">
                            @foreach($course->batches()->get() as $batch)
                                {{$batch->commence_date}}
                                <br>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <ul class="tabs">
        <li class="tab col s3"><a class="active" href="#syllabus-tab">Syllabus</a></li>
        <li class="tab col s3"><a href="#faculty-tab">Faculty</a></li>
        <li class="tab col s3"><a href="#faq-tab">FAQs</a></li>
    </ul>
    <br>
    <div id="syllabus-tab" class="col s12">
        <p id="course-syllabus">
            {{$course->syllabus}}
        </p>
    </div>
    <div id="faculty-tab" class="col s12">
        <div class="row">
            <div class="col m12 s12">
                <ul class="collapsible" data-collapsible="accordion">
                    {{$active = false}}
                    @foreach($course->faculties as $faculty)
                        <li>
                            @if(!$active)
                                <div class="collapsible-header active">
                                    @else
                                        <div class="collapsible-header">
                                            {{  $active=true }}
                                            @endif
                                            <i class="material-icons">assignment_ind</i>
                                            <strong>{{$faculty->name}}</strong>, {{$faculty->speciality}}
                                        </div>
                                        <div class="collapsible-body">
                                            <div class="row">
                                                <div class="col s2">
                                                    <?php
                                                    $url = str_replace(storage_path() . '/app/public',url('/') . '/storage',$faculty->pic_link);
                                                    ?>
                                                    <img src="{{$url}}" alt="" class="circle responsive-img">
                                                </div>
                                                <div class="col s10">
                                                    <div class="black-text">
                                                        {{$faculty->description}}
                                                    </div>
                                                    <br>
                                                    <div><strong>{{$faculty->experience}} years of experience</strong></div>
                                                </div>
                                            </div>
                                        </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="row">
            <p id="faculty-experience">
            </p>
        </div>
    </div>
    <div id="faq-tab" class="col s12">
        <?php $i=1;?>
        @foreach($course->faqs()->get() as $faq)
            <p><strong>{{$i.'. ' . $faq->question}}</strong></p>
            <p>{{$faq->answer}}</p>
            <?php $i++;?>
            <br>
        @endforeach
    </div>
    <div class="divider"></div>
    <br>
    <h6><strong>About {{$course->institute->name}}</strong></h6>
    <br>
    <div class="row">
        <div class="col m4">
            <?php
            $url = str_replace(storage_path() . '/app/public',url('/') . '/storage', $course->institute->logo_file);
            ?>
            <img class="responsive-img" src="/storage/{{$url}}">
        </div>
        <div class="col m8">
            <p id="course-description">
                {{$course->institute->description}}
            </p>
        </div>
    </div>

</div>

<script src="/js/jquery-3.1.1.min.js"></script>
<script src="/js/materialize.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        Materialize.updateTextFields();
        $(".button-collapse").sideNav();
        $(".dropdown-button").dropdown();
        $('.scrollspy').scrollSpy({
            scrollOffset: 90
        });
    });
</script>
</body>
