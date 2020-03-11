@extends('admin.layout')
@section('header')
@endsection
@section('main-content')
    @include('partials.errors')
    <p>Enter coupon details</p>
    <form action="{{route('coupon.store-admin')}}" method="post">
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
                    <option value="" disabled selected>Choose coupon type</option>
                    <option value="ALL">Everywhere</option>
                    <option value="CTG">Category</option>
                    <option value="INS">Institute</option>
                    <option value="CRS">Course</option>
                    <option value="USR">User</option>
                </select>
            </div>
        </div>
        <div class="row hide" id="category-select">
            <div class="input-field col m6">
                <select name="target_value" disabled>
                    <option value="" disabled selected>Choose category</option>
                    @foreach($categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row hide" id="institute-select">
            <div class="input-field col m6">
                <select name="target_value" disabled>
                    <option value="" disabled selected>Choose Institute</option>
                    @foreach($institutes as $institute)
                        <option value="{{$institute->id}}">{{$institute->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row hide" id="course-select">
            <div class="input-field col m6">
                <select name="target_value" disabled>
                    <option value="" disabled selected>Choose Course</option>
                    @foreach($institutes as $institute)
                        <optgroup label="{{$institute->name}}">
                        @foreach($institute->courses as $course)
                            <option value="{{$course->id}}">{{$course->name}}</option>
                        @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row hide" id="user-select">
            <div class="input-field col m6">
                <select name="target_value" disabled>
                    <option value="" disabled selected>Choose User</option>
                    @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
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
            $("select[name='target']").change(function(){
                $("select[name='target_value']").prop("disabled", true);
                $("select[name='target_value']").closest("div.row").addClass("hide");
                console.log($(this).val());
                if($(this).val() == 'CTG') {
                    $("#category-select").removeClass("hide");
                    $("#category-select select").prop("disabled", false);
                }
                else if($(this).val() == 'INS') {
                    $("#institute-select").removeClass("hide");
                    $("#institute-select select").prop("disabled", false);
                }
                else if($(this).val() == 'CRS') {
                    $("#course-select").removeClass("hide");
                    $("#course-select select").prop("disabled", false);
                }
                else if($(this).val() == 'USR') {
                    $("#user-select").removeClass("hide");
                    $("#user-select select").prop("disabled", false);
                }
                $('select').material_select();
            });
            $('select').material_select();
        });
    </script>
@endsection