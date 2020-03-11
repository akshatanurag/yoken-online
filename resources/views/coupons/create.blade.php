@extends('institute.layout')
@section('header')
@endsection
@section('main-content')
    @include('partials.errors')
    <p>Enter coupon details</p>
    <form action="{{route('coupon.store')}}" method="post">
        <div class="row">
            <div class="input-field col m6">
                <input type="text" name="name" id="name">
                <label for="name">Coupon Name</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6">
                <select name="couponType">
                    <option value="" disabled selected>Choose your option</option>
                    <option value="PER">Percentage</option>
                    <option value="AMT">Amount</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6">
                <input type="number" name="couponValue" id="coupon-value">
                <label for="coupon-value">Promo value</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6">
                <input type="text" name="allowance" id="allowance">
                <label for="allowance">Allowed per user</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6">
                <select name="target">
                    <option value="" disabled selected>Choose your option</option>
                    <option value="ALL">All courses</option>
                    @foreach($courses as $course)
                        <option value="{{$course->id}}">{{$course->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6">
                <input type="date" class="datepicker" name="expireTimestamp" id="expire-timestamp">
                <label for="expire-timestamp">Expires on</label>
            </div>
        </div>
        <button type="submit" class="btn waves-effect">Add Promo</button>
        {{csrf_field()}}
    </form>
@endsection
@section('footer')
    <script>
        $(document).ready(function() {
            $('.button-collapse').sideNav({
                menuWidth: 240
            });
            $('select').material_select();
            $('.datepicker').pickadate({
                'format': 'dd-mm-yy'
            });
        });
    </script>
@endsection