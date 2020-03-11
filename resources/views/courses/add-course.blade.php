@extends('institute.layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="/css/jquery.Jcrop.min.css">
    <style>
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
    <form id="add-course-form" method="post" action="{{route('course.store')}}" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="row">
            <div class="col m6 s12">
                <div class="input-field">
                    <input id="course-name" type="text" name="name" value="{{old('name')}}">
                    <label for="course-name" data-error="Invalid course name">Course name</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s6">
                <textarea id="course-description" class="materialize-textarea" name="description">{{old('description')}}</textarea>
                <label for="course-description" data-error="Invalid description">Description</label>
            </div>
        </div>
        <div class="row">
            <div class="col m6 s12">
                <div class="input-field">
                    <select multiple name="category[]">
                        <option value="" disabled selected>Choose your option</option>
                        @foreach($categories as $category)
                            @if(old('category')!=null && in_array($category->id, old('category')))
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
        <div class="row">
            <div class="col m6 s12">
                <div class="file-field input-field">
                    <div class="btn">
                        <span>File</span>
                        <input type="file" name="course_image" id="file">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path course-file-path validate" type="text" placeholder="Upload course image">
                    </div>
                </div>
                <span><strong>Image size should not be greater than 5MB</strong></span>
            </div>
        </div>
        <br>
        <div id="views">
        </div>

        <br>
        <div class="row">
            <div class="col m6 s12">
                <div class="input-field">
                    <input id="course-demo-classes" type="number" class="validate" name="demo_classes"  value="{{old('demo_classes')}}">
                    <label for="course-demo-classes">Demo classes</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col m6 s12">
                <div class="input-field">
                    <input id="course-cpw" type="number" class="validate" name="classes_per_week" value="{{old('classes_per_week')}}" >
                    <label for="course-cpw">Classes per week</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col m6 s11">
                <div class="input-field">
                    <input id="course-fees" type="number" class="validate" name="one_time_fees"  value="{{old('one_time_fees')}}">
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
                    <input id="course-discount" type="number" class="validate" name="discount"  value="{{old('discount')}}">
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
                    <input id="course-duration" type="number" class="validate" name="duration" step="0.1" value="{{old('duration')}}">
                    <label for="course-duration">Tentative Duration</label>
                </div>
            </div>
            <div class="col m3 s12">
                <div class="input-field">
                    <select name="duration_type">
                        <option selected disabled>Select duration type</option>
                        @if(old('duration_type') != null)
                            @if(old('duration_type') == 'HOURS')
                                <option selected value="HOURS">Hours</option>
                                <option value="DAYS">Days</option>
                                <option value="MONTHS">Months</option>
                            @elseif(old('duration_type') == 'DAYS')
                                <option selected value="DAYS">Days</option>
                                <option value="HOURS">Hours</option>
                                <option value="MONTHS">Months</option>
                            @elseif((old('duration_type') == 'MONTHS'))
                                <option selected value="MONTHS">Months</option>
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
                <textarea id="course-syllabus" class="materialize-textarea" name="syllabus">{{old('syllabus')}}</textarea>
                <label for="course-syllabus">Syllabus</label>
            </div>
        </div>
        <br>
        <input type="hidden" name="cropped_x">
        <input type="hidden" name="cropped_y">
        <input type="hidden" name="cropped_h">
        <input type="hidden" name="cropped_w">
        <a id="confirm-add" value="Add course" class="btn waves-effect">Add course</a>
        <br>
        <br>
    </form>
    <div id="dialog-faculty-modal" class="modal">
        <div class="modal-content center-align">
            <h5>Course has been successfully added!</h5>
            <br>
            <br>
            <i class="material-icons large green-text">done</i>
            <br>
            <br>
            <a class="btn waves-effect">Proceed to faculty description</a>
            <br>
            <br>
        </div>
    </div>
@endsection
@section('footer')
    <script src="/js/course-add.js"></script>
    <script src="/js/jquery.Jcrop.min.js"></script>
    <script>
        $(document).ready(function() {
            $('select').material_select();
        });
    </script>
@endsection