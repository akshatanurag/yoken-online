@extends('institute.layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="/css/jquery.Jcrop.min.css">
    <style>
        .course-logo-container {
            position: relative;
            height: 200px;
            width: 200px;
        }
        .course-logo {
            position: absolute;
            width: 100%
        }

        .course-logo img {
            width: 100%
        }
        .logo-caption-container {
            position: absolute;
            text-align: center;
            width: 100%;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            -webkit-transition: background 0.3s;
            -moz-transition: background 0.3s;
            -ms-transition: background 0.3s;
            -o-transition: background 0.3s;
            transition: background 0.3s;
        }
        .logo-caption-container:hover{
            background: rgba(0,0,0,0.7);
        }
        .logo-caption-container label{
            color: #fff;
            cursor: pointer;
        }
        input[type=file] {
            display: none;
        }
        #views {
            width: 500px;
        }
        #views img {
            width: 100%;
        }
        .jcrop-thumb {
            bottom: 0px;
            right: -200px;
            border: 1px black solid;
        }
    </style>
@endsection
@section('main-content')
    <?php
            $url = str_replace(storage_path() . '/app/public',url('/') . '/storage',$course->pic_link);
    ?>
    <form action="{{route('course.edit')}}" method="POST" id="edit-course-form"  enctype="multipart/form-data">
        {{csrf_field()}}
        @if(session()->has('status'))
            <div class="card-panel">
                <span class="green-text">{{session()->get('status')}}</span>
            </div>
        @endif
        <p><strong>Course Details:</strong></p>
        @if(!$errors->isEmpty())
            <p><strong>Please resolve the following errors:</strong></p>
            <ul class="collection">
                @foreach($errors->all() as $error)
                    <li class="collection-item red-text text-lighten-1">{{$error}}</li>
                @endforeach
            </ul>
        @endif
        <div class="row">
            <div class="col m4">
                <div class="course-logo-container">
                    <div class="course-logo">
                        <img src="{{$url}}" alt="course-logo">
                    </div>
                    <div class="logo-caption-container">
                        <input type="file" id="course-image-upload" name="course_image">
                        <label for="course-image-upload" class="custom-file-upload">
                            Edit logo
                        </label>
                    </div>
                </div>
            </div>
            <div class="col m8">
                <div id="views"></div>
            </div>
        </div>
        <div class="row">
            <div class="col m6 s12">
                <div class="input-field">
                    <input id="course-name" type="text" name="name" value="{{$course->name}}">
                    <label for="course-name" data-error="Invalid course name">Course name</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s6">
                <textarea id="course-description" class="materialize-textarea" name="description">{{$course->description}}</textarea>
                <label for="course-description" data-error="Invalid description">Description</label>
            </div>
        </div>
        <div class="row">
            <div class="col m6 s12">
                <div class="input-field">
                    <select multiple name="category[]">
                        <option value="" disabled selected>Choose your option</option>
                        @foreach($categories as $category)
                            @if(in_array($category->id, $selectedCategories->all()))
                                <option selected value="{{$category->id}}">{{$category->name}}</option>
                            @else
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endif
                        @endforeach
                        ?>
                    </select>
                    <label>Recommended for</label>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col m6 s12">
                <div class="input-field">
                    <input id="course-demo-classes" type="number" class="validate" name="demo_classes"  value="{{$course->demo_classes}}">
                    <label for="course-demo-classes">Demo classes</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col m6 s12">
                <div class="input-field">
                    <input id="course-cpw" type="number" class="validate" name="classes_per_week" value="{{$course->classes_per_week}}" >
                    <label for="course-cpw">Classes per week</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col m6 s11">
                <div class="input-field">
                    <input id="course-fees" type="number" class="validate" name="one_time_fees"  value="{{$course->fees}}">
                    <label for="course-fees">Fees (in INR)</label>
                </div>
            </div>
            <div class="col m1 s1">
                <i class="tiny helper material-icons grey-text text-darken-1 tooltipped" data-position="bottom" data-delay="50" data-tooltip="Enter one time fees.">info_outline</i>
            </div>
        </div>
        <div class="row">
            <div class="col m6 s12">
                <div class="input-field">
                    <input id="course-discount" type="number" class="validate" name="discount"  value="{{$course->discount}}">
                    <label for="course-discount">Discount (%)</label>
                </div>
            </div>
            <div class="col m1 s1">
                <i class="tiny helper material-icons grey-text text-darken-1 tooltipped" data-position="bottom" data-delay="50" data-tooltip="Applicable only on one time payments.">info_outline</i>
            </div>
        </div>
        <div class="row">
            <div class="col m3 s12">
                <div class="input-field">
                    <input id="course-duration" type="number" class="validate" name="duration" step="0.1" value="{{$course->duration}}"">
                    <label for="course-duration">Tentative Duration</label>
                </div>
            </div>
            <div class="col m3 s12">
                <div class="input-field">
                    <select name="duration_type">
                        <option selected disabled>Select duration type</option>
                        @if($course->duration_type != null)
                            @if($course->duration_type == 'HOURS')
                                <option selected value="HOURS">Hours</option>
                                <option value="DAYS">Days</option>
                                <option value="MONTHS">Months</option>
                            @elseif($course->duration_type == 'DAYS')
                                <option selected value="DAYS">Days</option>
                                <option value="HOURS">Hours</option>
                                <option value="MONTHS">Months</option>
                            @elseif(($course->duration_type == 'MONTHS'))
                                <option selected value="MONTHS">Months</option>
                                <option value="DAYS">Days</option>
                                <option value="HOURS">Hours</option>
                             @else
                                <option value="MONTHS">Months</option>
                                <option value="DAYS">Days</option>
                                <option value="HOURS">Hours</option>
                            @endif
                        @else
                            <option value="MONTHS">Months</option>
                            <option value="DAYS">Days</option>
                            <option value="HOURS">Hours</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s6">
                <textarea id="course-syllabus" class="materialize-textarea" name="syllabus">{{$course->syllabus}}</textarea>
                <label for="course-syllabus">Syllabus</label>
            </div>
        </div>
        <br>
        <input type="hidden" name="id" value={{$course->id}}>
        <input type="hidden" name="cropped_x">
        <input type="hidden" name="cropped_y">
        <input type="hidden" name="cropped_h">
        <input type="hidden" name="cropped_w">
        <a id="confirm-edit" class="btn waves-effect">Update course</a>
        <br>
    </form>
    <br>
    <br>

@endsection
@section('footer')
    <script src="/js/course-edit.js"></script>
    <script src="/js/jquery.Jcrop.min.js"></script>
    <script>
        $(document).ready(function() {
            $('select').material_select();
        });
    </script>
@endsection