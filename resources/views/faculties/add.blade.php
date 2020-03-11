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
    @include('partials.errors')
    <form action="{{route('faculty.store')}}" id="add-faculty-form" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="row">
            <div class="input-field col m6">
                <input type="text" name="name" placeholder="Enter faculty name..">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6">
                <textarea class="materialize-textarea" placeholder="Enter faculty bio.." name="description"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6">
                <input type="number" name="experience" placeholder="Enter experience..">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6">
                <input type="text" name="speciality" placeholder="Enter speciality..">
            </div>
        </div>
        <div class="row">
            <div class="file-field input-field col m6">
                <div class="btn">
                    <span>File</span>
                    <input type="file" name="faculty_image" id="file">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path course-file-path validate" type="text" placeholder="Upload faculty image">
                </div>
            </div>
        </div>
        <br>
        <div id="views"></div>
        <br>
        <a class="btn waves-effect" id="confirm-add">Add faculty</a>
        <br><br>
        <input type="hidden" name="cropped_x">
        <input type="hidden" name="cropped_y">
        <input type="hidden" name="courseId" value="{{$courseId}}">
        <input type="hidden" name="cropped_h">
        <input type="hidden" name="cropped_w">
    </form>
@endsection

@section('footer')
    <script src="/js/faculty-add.js"></script>
    <script src="/js/jquery.Jcrop.min.js"></script>
@endsection