@extends('institute.layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="/css/jquery.timepicker.css">
@endsection
@section('main-content')
    @include('partials.errors')
    <p><strong>Enter batch details:</strong></p>
    <form action="{{route('batch.store')}}" method="post">
        <div class="row">
            <div class="input-field col m5">
                <input type="number" name="no_of_seats" placeholder="Enter no. of seats.." >
            </div>
            <div class="input-field col 5">
                <input type="date" class="datepicker" placeholder="Enter commence_date.." name="commence_date">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                <input type="checkbox" name="days[]" id="monday-check" value="M">
                <label for="monday-check">Mon</label>
            </div>
            <div class="col m4">
                <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing..">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                <input type="checkbox" name="days[]" id="tuesday-check" value="T">
                <label for="tuesday-check">Tue</label>
            </div>
            <div class="col m4">
                <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                <input type="checkbox" name="days[]" id="wednesday-check" value="W">
                <label for="wednesday-check">Wed</label>
            </div>
            <div class="col m4">
                <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                <input type="checkbox" name="days[]" id="thursday-check" value="Th">
                <label for="thursday-check">Thur</label>
            </div>
            <div class="col m4">
                <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                <input type="checkbox" name="days[]" id="friday-check" value="F">
                <label for="friday-check">Fri</label>
            </div>
            <div class="col m4">
                <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                <input type="checkbox" name="days[]" id="saturday-check" value="S">
                <label for="saturday-check">Sat</label>
            </div>
            <div class="col m4">
                <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field">
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                <input type="checkbox" name="days[]" id="sunday-check" value="Su">
                <label for="sunday-check">Sun</label>
            </div>
            <div class="col m4">
                <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field">
            </div>
        </div>
        <input type="submit" class="btn waves-effect" value="Add batch">
        <br>
        <br>
        <input type="hidden" name="courseId" value="{{$courseId}}">
        {{csrf_field()}}
    </form>
@endsection
@section('footer')
    <script src="/js/jquery.timepicker.min.js"></script>
    <script>
        $('.time-field').prop('disabled',true);
        $('input[type=checkbox]').change(function() {
            if($(this).is(':checked')) {
                $(this).parent().siblings('div').find('.time-field').prop('disabled',false);
            }
            else $(this).parent().siblings('div').find('.time-field').prop('disabled',true);
        });
        $('.datepicker').pickadate({
            format: 'dd/mm/yyyy',
        });
        $('.time-field').timepicker();
    </script>
@endsection