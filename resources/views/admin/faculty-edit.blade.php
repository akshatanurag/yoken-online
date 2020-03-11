@extends('admin.layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="/css/jquery.Jcrop.min.css">
    <style>
        .faculty-logo-container {
            position: relative;
            height: 200px;
            width: 200px;
        }
        .faculty-logo {
            position: absolute;
            width: 100%
        }

        .faculty-logo img {
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
    @include('partials.errors')
    <?php
    $url = str_replace(storage_path() . '/app/public',url('/') . '/storage',$faculty->pic_link);
    ?>
    <form action="{{route('admin.update.faculty')}}" id="add-faculty-form" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="row">
            <div class="col m4">
                <div class="faculty-logo-container">
                    <div class="faculty-logo">
                        <img src="{{$url}}" alt="faculty-logo">
                    </div>
                    <div class="logo-caption-container">
                        <input type="file" id="file" name="faculty_image">
                        <label for="file" class="custom-file-upload">
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
            <div class="input-field col m6">
                <input type="text" name="name" placeholder="Enter faculty name.." value="{{$faculty->name}}">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6">
                <textarea class="materialize-textarea" placeholder="Enter faculty bio.." name="description">{{$faculty->description}}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6">
                <input type="number" name="experience" placeholder="Enter experience.." value="{{$faculty->experience}}">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6">
                <input type="text" name="speciality" placeholder="Enter speciality.." value="{{$faculty->speciality}}">
            </div>
        </div>
        <br>
        <br>
        <a class="btn waves-effect" id="confirm-add">Add faculty</a>
        <br><br>
        <input type="hidden" name="cropped_x">
        <input type="hidden" name="cropped_y">
        <input type="hidden" name="cropped_h">
        <input type="hidden" name="cropped_w">
        <input type="hidden" name="facultyId" value="{{$faculty->id}}">
    </form>
@endsection

@section('footer')
    <script src="/js/faculty-edit.js"></script>
    <script src="/js/jquery.Jcrop.min.js"></script>
@endsection