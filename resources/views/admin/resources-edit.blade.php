@extends('admin.layout')
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
        <form class="col m7 s12" method="post" enctype="multipart/form-data" action="{{route('admin.resources-update', $resource->id)}}">
            <div class="row">
                <div class="col m12 s12">
                    <div class="input-field">
                        <input id="resource-name" type="text" class="validate" name="name" value="{{$resource->name}}">
                        <label for="resource-name">Resource Name</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col m12 s12">
                    <textarea id="resource-description" class="materialize-textarea" name="description">{{$resource->description}}</textarea>
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
                <div class="col m2">
                    <img style="height: 70px" class="responsive-img" src="/{{ str_replace(storage_path() . '/app/public', 'storage', $resource->image) }}" alt="">
                </div>
            </div>
            <div class="row">
                <div class="input-field col m12 s12">
                    <textarea id="resource-embed-code" class="materialize-textarea" name="embed_code">{{$resource->embed_code}}</textarea>
                    <label for="resource-embed-code">Resource Embed Code</label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col m12">
                    <strong>Expiry Date:</strong>
                    <input type="date" class="date-field" name="expiry_date" id="expiry-date" placeholder="Expiry Date" value="{{$resource->expiry}}">
                </div>
            </div>
            <div class="row">
                <div class="input-field col m12 s12">
                    <select name="webinar_id" id="webinar-id">
                        @if($resource->course_id == null)
                            <option value="" selected="selected">Select Webinar</option>
                        @else
                            <option value="">Select Webinar</option>
                        @endif
                        @foreach($webinars as $webinar)
                            @if($resource->webinar_id!=null && $webinar->id == $resource->webinar_id)
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
                        @if($resource->course_id == null)
                            <option value="" selected="selected">Select Course</option>
                        @else
                            <option value="">Select Course</option>
                        @endif
                        @foreach($courses as $course)
                            @if($resource->course_id!=null && $course->id == $resource->course_id)
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
