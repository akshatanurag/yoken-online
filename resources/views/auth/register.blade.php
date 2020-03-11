<!DOCTYPE html>
<html>
<head>
    <title>Yoken | Home</title>
    <meta name="viewport" content="width=device-width">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/materialize.css">
    <style type="text/css">
        body {
            background: -webkit-linear-gradient(rgba(0,0,0,0.7),rgba(0,0,0,0.7)), url('/img/office.jpg');
            background-size: cover;
        }
        nav {
            background-color: #fff;
        }
        main {
            height: 100vh;
        }
        .nav-wrapper {
            padding: 0 30px;
        }
        .logo-main {
            margin-top: 10px;
            max-height: 80px;
        }
        .form-container {
            text-align: center;
        }
        @media only screen and (max-width : 992px) {
            .form-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<main>
    <div class="container">
        <div class="row">
            <div class="col m5 push-m3 s12">
                <div class="card-panel form-container">
                    <img src="img/yoken-logo.png" class="logo-main">
                    <form class="login-form" role="form" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}
                        <div class="input-field">
                            <input type="text" name="name" class="form-control" id="login-name"  value="{{ old('name') }}" required autofocus>
                            <label for="login-name">Name</label>
                            @if ($errors->has('name'))
                                <span class="red-text">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="input-field">
                            <input type="text" name="email" class="form-control" id="login-email"  value="{{ old('email') }}" required>
                            <label for="login-email">Email</label>
                            @if ($errors->has('email'))
                                <span class="red-text">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="input-field">
                            <input type="text" name="phone" class="form-control" id="login-phone"  value="{{ old('phone') }}" required>
                            <label for="login-phone">Phone</label>
                            @if ($errors->has('phone'))
                                <span class="red-text">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="input-field">
                            <input type="password" name="password" id="login-password" required>
                            <label for="login-password">Password</label>
                            @if ($errors->has('password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="input-field">
                            <input type="password" name="password_confirmation" id="login-password-confirmation" required>
                            <label for="login-password-confirmation">Confirm Password</label>
                        </div>
                        <div class="input-field" style="text-align: left">
                            <input type="checkbox" id="accept-terms" name="terms">
                            <label for="accept-terms">I agree to the <a target="_blank" href="/terms-and-conditions">terms and conditions</a></label>
                            <br>
                            <br>
                            @if ($errors->has('terms'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('terms') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <br>
                        <div class="input-field">
                            <button class="btn waves-effect" type="submit">Register</button>
                        </div>
                        <br>
                        <br>
                        <p class="center-align">Already a member? <a href="/login">Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</main>
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/materialize.min.js"></script>
</body>
</html>