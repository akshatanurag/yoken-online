@extends('institute.layout')
@section('header')
@endsection
@section('main-content')
    @include('partials.errors')
    <p><strong>Enter installment details:</strong></p>
    <form action="{{route('installment.store')}}" method="post">
        <div class="field-container">
            <div class="row">
                <div class="col m6 s12">
                    <select name="frequency">
                        <option disabled selected>--Select payment frequency in months -- </option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                        <option value="11">11</option>
                        <option value="12">12</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="installment-template row" hidden>
            <div class="input-field col m5">
                <input type="number" name="amounts[]" placeholder="Enter amount.." class="amount" disabled>
            </div>
            <div class="input-field col m5">
                <input type="number" name="durations[]" placeholder="Should be paid in (months).." class="duration" disabled>
            </div>
        </div>
        <input type="hidden" name="courseId" value="{{$courseId}}">
        {{csrf_field()}}
        <input class="btn waves-effect" type="submit" value="Add installment">
    </form>
@endsection
@section('footer')
    <script>
        $(document).ready(function() {
            $('select').material_select();
            $('select').change(function () {
                $('.installment-item').remove();
                var val = $(this).val();
                for (var i = 0; i < val; i++) {
                    var clone = $('.installment-template').clone();
                    clone.removeClass('installment-template');
                    clone.addClass('installment-item');
                    clone.find('input').prop('disabled', false);
                    clone.prop('hidden', false);
                    if(i == 0) {
                        clone.find('.duration').val('0');
                        clone.find('.duration').prop('readonly', true);
                    }
                    $('.field-container').append(clone);
                }
            });
            $('.datepicker').pickadate({
                format: 'dd/mm/yyyy'
            });
            $('input[type=submit]').click(function(e) {
                e.preventDefault();
                var submittable = true;
                $('input').each(function() {
                    if($(this).prop('disabled') == false && $(this).val() == '') {
                        console.log($(this).attr('name'));
                        alert('Please fill out all the fields.');
                        submittable = false;
                        return false;
                    }
                });
                if(submittable)
                    $('form').submit();
            });
        });
    </script>
@endsection