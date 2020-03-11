@extends('institute.layout')
@section('header')
    <link rel="stylesheet" type="text/css" href="/css/jquery.timepicker.css">
@endsection
@section('main-content')
    @include('partials.errors')
    <p><strong>Enter batch details:</strong></p>
    <form action="{{route('batch.update.store')}}" method="post">
        <div class="row">
            <div class="input-field col m5">
                <input type="number" name="no_of_seats" placeholder="Enter no. of seats.." value="{{$batch->no_of_seats}}">
            </div>
            <div class="input-field col 5">
                <input type="date" class="datepicker" placeholder="Enter commence_date.." name="commence_date" value="{{$batch->commence_date}}">
            </div>
        </div>
        <?php
            $days = explode(';', $batch->days);
            $timings = explode(';', $batch->timings);
            $i = 0;
        ?>
        <div class="row">
            <div class="input-field col m3">
                @if(in_array('M', $days))
                    <input checked type="checkbox" name="days[]" id="monday-check" value="M">
                @else
                    <input type="checkbox" name="days[]" id="monday-check" value="M">
                @endif
                <label for="monday-check">Mon</label>
            </div>
            <div class="col m4">
                @if(in_array('M', $days))
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing.." value="{{$timings[$i]}}">
                    <?php $i++;?>
                @else
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing..">
                @endif
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                @if(in_array('T', $days))
                    <input checked type="checkbox" name="days[]" id="tuesday-check" value="T">
                @else
                    <input type="checkbox" name="days[]" id="tuesday-check" value="T">
                @endif
                <label for="tuesday-check">Tue</label>
            </div>
            <div class="col m4">
                @if(in_array('T', $days))
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing.." value="{{$timings[$i]}}">
                    <?php $i++;?>
                @else
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing..">
                @endif
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                @if(in_array('W', $days))
                    <input checked type="checkbox" name="days[]" id="wednesday-check" value="W">
                @else
                    <input type="checkbox" name="days[]" id="wednesday-check" value="W">
                @endif
                <label for="wednesday-check">Wed</label>
            </div>
            <div class="col m4">
                @if(in_array('W', $days))
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing.." value="{{$timings[$i]}}">
                    <?php $i++;?>
                @else
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing..">
                @endif
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                @if(in_array('Th', $days))
                    <input checked type="checkbox" name="days[]" id="thursday-check" value="Th">
                @else
                    <input type="checkbox" name="days[]" id="thursday-check" value="Th">
                @endif
                <label for="thursday-check">Thur</label>
            </div>
            <div class="col m4">
                @if(in_array('Th', $days))
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing.." value="{{$timings[$i]}}">
                    <?php $i++;?>
                @else
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing..">
                @endif
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                @if(in_array('F', $days))
                    <input checked type="checkbox" name="days[]" id="friday-check" value="F">
                @else
                    <input type="checkbox" name="days[]" id="friday-check" value="F">
                @endif
                <label for="friday-check">Fri</label>
            </div>
            <div class="col m4">
                @if(in_array('F', $days))
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing.." value="{{$timings[$i]}}">
                    <?php $i++;?>
                @else
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing..">
                @endif
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                @if(in_array('S', $days))
                    <input checked type="checkbox" name="days[]" id="saturday-check" value="S">
                @else
                    <input type="checkbox" name="days[]" id="saturday-check" value="S">
                @endif
                <label for="saturday-check">Sat</label>
            </div>
            <div class="col m4">
                @if(in_array('S', $days))
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing.." value="{{$timings[$i]}}">
                    <?php $i++;?>
                @else
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing..">
                @endif
            </div>
        </div>
        <div class="row">
            <div class="input-field col m3">
                @if(in_array('Su', $days))
                    <input checked type="checkbox" name="days[]" id="sunday-check" value="Su">
                @else
                    <input type="checkbox" name="days[]" id="sunday-check" value="Su">
                @endif
                <label for="sunday-check">Sun</label>
            </div>
            <div class="col m4">
                @if(in_array('Su', $days))
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing.." value="{{$timings[$i]}}">
                    <?php $i++;?>
                @else
                    <input type="text" name="timings[]" placeholder="Enter timing.." class="time-field" placeholder="Enter timing..">
                @endif
            </div>
        </div>
        <input type="submit" class="btn waves-effect" value="Update batch">
        <br>
        <br>
        <input type="hidden" name="batchId" value="{{$batch->id}}">
        {{csrf_field()}}
    </form>
@endsection
@section('footer')
    <script src="/js/jquery.timepicker.min.js"></script>
    <script>
        $(document).ready(function(){
            $('input[type=checkbox').each(function(){
                if($(this).is(':checked')) {
                    $(this).parent().siblings('div').find('.time-field').prop('disabled',false);
                }
                else $(this).parent().siblings('div').find('.time-field').prop('disabled',true);
            });
        });
        $('.time-field').prop('disabled',true);
        $('input[type=checkbox]').change(function() {
            if($(this).is(':checked')) {
                $(this).parent().siblings('div').find('.time-field').prop('disabled',false);
            }
            else $(this).parent().siblings('div').find('.time-field').prop('disabled',true);
        });
        var $input = $('.datepicker').pickadate({
            format: 'dd/mm/yyyy',
        });
        var picker = $input.pickadate('picker');
        picker.set('select', '{{$batch->commence_date}}', { format: 'dd/mm/yyyy' });
        $('.time-field').timepicker();
    </script>
@endsection