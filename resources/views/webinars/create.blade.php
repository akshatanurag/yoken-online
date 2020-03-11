@extends('admin.layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="/css/jquery.timepicker.css">
@endsection
@section('main-content')
    @include('partials.errors')
    <form action="{{route('webinar.store')}}" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="input-field col m6 s12">
                <input type="text" name="name" id="name" value="{{old('name')}}">
                <label for="name">Webinar name</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6 s12">
                <textarea id="description" name="description" class="materialize-textarea" data-length="120">{{old('description')}}</textarea>
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
        </div>
        <div class="row">
            <div class="input-field col m3 s12">
                <strong>Start time:</strong>
                <input type="date" class="date-field" name="start_date" id="starts-at" placeholder="Start date" value="{{old('start_date')}}">
            </div>
            <div class="input-field col m3 s12">
                <strong>&nbsp;</strong>
                <input type="text" name="start_time" id="starts-at" placeholder="Start time" class="time-field" value="{{old('start_time')}}">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3 s12">
                <strong>End time:</strong>
                <input type="date" class="date-field" name="end_date" id="ends-at" placeholder="End date" value="{{old('end_date')}}">
            </div>
            <div class="input-field col m3 s12">
                <strong>&nbsp;</strong>
                <input type="text" name="end_time" id="ends-at" placeholder="End time" class="time-field" value="{{old('end_time')}}">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6 s12">
                <input type="number" name="fees" id="fees" step="0.1" value="{{old('fees')}}">
                <label for="fees">Webinar fees</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6 s12">
                <input type="number" name="discount" id="discount" step="0.1" value="{{old('discount')}}">
                <label for="discount">Webinar discount</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6 s12">
                <input type="text" name="room_url" id="room-url" value="{{old('room_url')}}">
                <label for="room-url">Webinar embed code</label>
            </div>
        </div>
        {{csrf_field()}}
        <button class="btn waves-effect" type="submit"> Add Webinar </button>
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
        $('.time-field').timepicker();
    </script>
@endsection