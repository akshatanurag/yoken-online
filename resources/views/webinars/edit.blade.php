@extends('admin.layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="/css/jquery.timepicker.css">
@endsection
@section('main-content')
    @include('partials.errors')
    <form action="{{route('webinar.update', ['webinar'=>$webinar->id])}}" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="input-field col m6 s12">
                <input type="text" name="name" id="name" value="{{$webinar->name}}">
                <label for="name">Webinar name</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6 s12">
                <textarea id="description" name="description" class="materialize-textarea" data-length="120">{{$webinar->description}}</textarea>
                <label for="description">Short description</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field file-field col m6 s12">
                <div class="btn">
                    <span>File</span>
                    <input type="file" name="webinar_pic">
                </div>
                <div class="file-path-wrapper">
                    <input class="file-path validate" type="text">
                </div>
            </div>
            <div class="col m2">
                <img style="height: 70px" class="responsive-img" src="/{{ str_replace(storage_path() . '/app/public', 'storage', $webinar->image_url) }}" alt="">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3 s12">
                <strong>Start time:</strong>
                <input type="date" name="start_date" class="date-field" id="starts-at" placeholder="Start date" value="{{date('Y-m-d', strtotime(str_replace('/', '-', $webinar->starts_at)))}}">
            </div>
            <div class="input-field col m3 s12">
                <strong>&nbsp;</strong>
                <input type="text" name="start_time" id="starts-at" placeholder="Start time" class="time-field" value="{{date('H:i:s', strtotime(str_replace('/', '-', $webinar->starts_at)))}}">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3 s12">
                <strong>End time:</strong>
                <input type="date" name="end_date" class="date-field" id="ends-at" placeholder="End date" value="{{date('Y-m-d', strtotime(str_replace('/', '-', $webinar->ends_at)))}}">
            </div>
            <div class="input-field col m3 s12">
                <strong>&nbsp;</strong>
                <input type="text" name="end_time" id="ends-at" placeholder="End time" class="time-field" value="{{date('H:i:s', strtotime(str_replace('/', '-', $webinar->ends_at)))}}">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6 s12">
                <input type="number" name="fees" id="fees" step="0.1" value="{{$webinar->fees}}">
                <label for="fees">Webinar fees</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6 s12">
                <input type="number" name="discount" id="discount" step="0.1" value="{{$webinar->discount}}">
                <label for="discount">Webinar discount</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6 s12">
                <input type="text" name="room_url" id="room-url" value="{{$webinar->room_url}}">
                <label for="room-url">Webinar embed code</label>
            </div>
        </div>
        {{csrf_field()}}
        <button class="btn waves-effect" type="submit"> Update Webinar </button>
        <br>
        <br>
    </form>
@endsection
@section('footer')
    <script src="/js/jquery.timepicker.min.js"></script>
    <script>
        $('.date-field').pickadate({
            format: 'yyyy-mm-dd',
        });
        $(document).ready(function () {
            $('.time-field').timepicker();
        });
    </script>
@endsection
