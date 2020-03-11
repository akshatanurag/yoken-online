<!DOCTYPE html>
<html>
<head>
    <title>Yoken | Home</title>
    <meta name="viewport" content="width=device-width">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/materialize.css">
    <style type="text/css">
        body {
            background: -webkit-linear-gradient(rgba(0,0,0,0.7),rgba(0,0,0,0.7)), url('/img/institute.jpg');
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
        //width:40%;
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
<main style="display: flex;align-items: center;">
    <div class="container">
        <div class="row">
            <div class="col m5 push-m3 s12">
                <div class="card-panel form-container">
                    <img src="/img/yoken-logo.png" class="logo-main">
                    <div class="row">
                        <div class="col m12">
                            @foreach($errors->all() as $error)
                                <div class="card-panel red lighten-1" style="padding: 5px 2px">
                                    <span class="white-text">
                                      {{$error}}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <form class="login-form" role="form" method="POST" action="{{ route('institute.login.submit') }}">
                        {{ csrf_field() }}
                        <div class="input-field">
                            <input type="text" name="email" class="form-control" id="login-email"  value="{{ old('email') }}" required autofocus>
                            <label for="login-email">Email</label>
                            @if ($errors->has('email'))
                                <span class="red-text">
                                        <strong>{{ $errors->first('email') }}</strong>
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
                        <div class="input-field" style="text-align: left">
                            <input type="checkbox" id="remember-me" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember-me">Remember Me</label>
                        </div>
                        <br>
                        <div class="input-field">
                            <button class="btn waves-effect" type="submit">Login</button>
                        </div>
                        <br>
                        <!--
                        <a href="{{ route('password.request') }}" class="grey-text">
                            Forgot Your Password?
                        </a>
                        -->
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</main>
<script src="/js/jquery-3.1.1.min.js"></script>
<script src="/js/materialize.min.js"></script>
</body>
</html>
