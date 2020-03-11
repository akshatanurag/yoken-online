<?php
/**
 * Created by: Amandeep.
 * For: YokenOnline
 * Date: 3/17/17
 * Time: 12:41 PM
 */
?>
<!DOCTYPE html>
<html>
<head>
<title>Yoken | Register for Webinar</title>
<meta name="viewport" content="width=device-width">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="/css/materialize.css">
<style type="text/css">
    nav {
    background-color: #fff;
			}
.nav-wrapper {
    padding: 0 30px;
			}
nav .brand-logo img {
    margin-top: 10px;
			    max-height: 40px;
			}
h6 {
    font-size: 1.24rem
			}
.enroll-form {
    padding-left: 40px;
				padding-right: 40px;
			}
.helper {
    cursor: pointer;
}
</style>
</head>
<body>
@include('partials/nav')
<br>
<div class="container">
    <form method="post" action="/webinar/register/{{$webinar->id}}">
        <div class="card-panel light-green lighten-5">
            @include('partials.errors')
            <h6>Confirm registration:</h6>
            <hr>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Webinar name:</strong>
                </div>
                <div class="col m6 s6">
                    {{$webinar->name}}
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Webinar Timings:</strong>
                </div>
                <div class="col m6 s6">
                    Starts : {{date("jS F, Y | H:iA", strtotime(str_replace('/', '-', $webinar->starts_at)))}}<br />
                    End : {{date("jS F, Y | H:iA", strtotime(str_replace('/', '-', $webinar->ends_at)))}}
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Payment type:</strong>
                </div>
                <div class="col m6 s6">
                    <select disabled name="paymentType" id="paymentType">
                        <option selected>Online Payment ( Instamojo )</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Payable amount:</strong>
                </div>
                <div class="col m6 s6">
                    &#8377; {{ $webinar->fees - ($webinar->fees * $webinar->discount)/100 }}
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Verify Captcha</strong>
                </div>
                <div class="col m6 s6">
                    {!!captcha_img('flat')!!}
                    <br>
                    <div class="input-field col m8">
                        <input id="captcha" type="text" name="captcha">
                        <label for="captcha">Type the above characters.</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <button class="btn waves-effect"type="submit" value="Enroll">Register now</button>
                {{csrf_field()}}
            </div>
        </div>
    </form>
</div>
</body>
<script src="/js/jquery-3.1.1.min.js"></script>
<script src="/js/materialize.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        Materialize.updateTextFields();
        $(".button-collapse").sideNav();
        $(".dropdown-button").dropdown();
        $('select').material_select();
    });
</script>
</html>
