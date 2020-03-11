@extends('institute.layout')
@section('header')
@endsection
@section('main-content')
@include('partials.errors')
@if(session()->has('password_changed'))
    <div class="row">
        <div class="col m6 s12">
            <div class="card-panel green lighten-1">
                <span class="white-text">
                    {{session()->get('password_changed')}}
                </span>
            </div>
        </div>
    </div>
@endif
    <form action="{{route('change.institute.password')}}" method="post">
        <div class="row">
            <div class="input-field col m6 s12">
                <input type="password" id="old-password" name="old_password">
                <label for="old-password">Old Password</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6 s12">
                <input type="password" id="new-password" name="new_password">
                <label for="new-password">New Password</label>
            </div>
        </div>
        <div class="row">
            <div class="input-field col m6 s12">
                <input type="password" id="confirm-password" name="new_password_confirmation">
                <label for="confirm-password">Confirm Password</label>
            </div>
        </div>
        <div class="row">
            <button class="btn waves-effect">Change</button>
        </div>
        {{csrf_field()}}
    </form>
@endsection

@section('footer')
@endsection