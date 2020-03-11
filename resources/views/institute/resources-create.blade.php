@extends('institute.layout')
@section('header')
    <style>
        .logo-container {
            width: 200px;
        }
        .logo-container img{
            width: 100%
        }
    </style>
@endsection
@section('main-content')
    @include('partials.errors')
    <div class="row">
        <form class="col m7 s12" method="post" enctype="multipart/form-data" action="{{route('institute.resources-store')}}">
            <div class="row">
                <div class="col m12 s12">
                    <div class="input-field">
                        <input id="resource-name" type="text" class="validate" name="name" value="{{old('name')}}">
                        <label for="resource-name">Resource Name</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col m12 s12">
                    <textarea id="resource-description" class="materialize-textarea" name="description">{{old('description')}}</textarea>
                    <label for="resource-description">Resource Description</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field file-field col m12">
                    <div class="btn">
                        <span>Resource Image</span>
                        <input type="file" name="image">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col m12 s12">
                    <textarea id="resource-embed-code" class="materialize-textarea" name="embed_code">{{old('embed_code')}}</textarea>
                    <label for="resource-embed-code">Resource Embed Code</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col m12">
                    <strong>Expiry Date:</strong>
                    <input type="date" class="date-field" name="expiry_date" id="expiry-date" placeholder="Expiry Date" value="{{old('expiry')}}">
                </div>
            </div>
            <div class="row">
                <div class="input-field col m12 s12">
                    <select name="webinar_id" id="webinar-id">
                        <option value="">Select Webinar</option>
                        @foreach($webinars as $webinar)
                            @if(old('webinar_id')!=null && $webinar->id == old('webinar_id'))
                                <option value="{{$webinar->id}}" selected="selected">{{$webinar->name}}</option>
                            @else
                                <option value="{{$webinar->id}}">{{$webinar->name}}</option>
                            @endif
                        @endforeach
                    </select>
                    <label for="webinar-id">Resource Webinar</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col m12 s12">
                    <select name="course_id" id="course-id">
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            @if(old('course_id')!=null && $course->id == old('course_id'))
                                <option value="{{$course->id}}" selected="selected">{{$course->name}}</option>
                            @else
                                <option value="{{$course->id}}">{{$course->name}}</option>
                            @endif
                        @endforeach
                    </select>
                    <label for="course-id">Resource Course</label>
                </div>
            </div>
            <br>
            <div class="row">
                <input type="submit" name="submit" class="btn waves-effect" value="Update">
            </div>
            {{csrf_field()}}
        </form>
    </div>
@endsection

@section('footer')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.min.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="/js/jquery.timepicker.min.js"></script>
    <script>
        $('.date-field').pickadate({
            format: 'yyyy-mm-dd',
        });
        $('select').material_select();
    </script>
@endsection
