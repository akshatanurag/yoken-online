@extends('user.layout')

@section('main-content')
    <div class="container" id="year-wise-sales">
        <div class="row">
            <h5>Profile</h5>
        </div>
        <form action="{{route('user.profile.edit')}}" method="POST" id="edit-user-form"  enctype="multipart/form-data">
            {{csrf_field()}}
            @if($errors->isEmpty() && isset($submitted_1))
                <div class="card-panel green lighten-3 green-text text-darken-2"><strong>Details Changed Successfully</strong></div>
            @endif
            <div class="row">
                <div class="col m12">
                    <div class="input-field">
                        <input id="user-name" type="text" name="name" value="{{$user->name}}">
                        <label for="user-name" data-error="Invalid name">Name</label>
                        @if ($errors->has('name'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12">
                    <div class="input-field">
                        <input id="user-email" type="email" name="email" value="{{$user->email}}">
                        <label for="user-email" data-error="Invalid email">Email</label>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12">
                    <div class="input-field">
                        <input id="user-phone" type="tel" name="phone" value="{{$user->phone}}">
                        <label for="user-phone" data-error="Invalid phone number">Phone Number</label>
                        @if ($errors->has('phone'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
            </div>
            <br>
            <input type="submit" id="confirm-edit" class="btn waves-effect" value="Update Profile" />
        </form>
        <br>
        <div class="row">
            <hr>
        </div>
        <div class="row">
            <h5>Password</h5>
        </div>
        <form action="{{route('user.profile.edit')}}?password" method="POST" id="edit-password-form"  enctype="multipart/form-data">
            {{csrf_field()}}
            @if($errors->isEmpty() && isset($submitted_2))
                <div class="card-panel green lighten-3 green-text text-darken-2"><strong>Password Changed Successfully</strong></div>
            @endif
            <div class="row">
                <div class="col m12">
                    <div class="input-field">
                        <input id="user-current-password" type="password" name="current-password" value="">
                        <label for="user-current-password" data-error="Invalid current password">Current Password</label>
                        @if ($errors->has('current-password'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('current-password') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12">
                    <div class="input-field">
                        <input id="user-password" type="password" name="password" value="">
                        <label for="user-password" data-error="Invalid password">New Password</label>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m12">
                    <div class="input-field">
                        <input id="user-confirm-password" type="password" name="password_confirmation" value="">
                        <label for="user-confirm-password" data-error="Doesn't match entered password">Confirm New Password</label>
                    </div>
                </div>
            </div>
            <br>
            <input type="submit" id="confirm-password" class="btn waves-effect" value="Update Password" />
        </form>
    </div>
@endsection

@section('footer')
@endsection
