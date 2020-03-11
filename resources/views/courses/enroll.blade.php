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
<title>Yoken | Enroll</title>
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
    <form method="post" action="/enroll">
        <div class="card-panel light-green lighten-5">
            @include('partials.errors')
            <h6>Confirm enrollment:</h6>
            <hr>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Course name:</strong>
                </div>
                <div class="col m6 s6">
                    {{$course->name}}
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Institute:</strong>
                </div>
                <div class="col m6 s6">
                    {{$course->institute->name}}
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Select batch</strong>
                </div>
                <div class="col m6 s6">
                    <select name="batch">
                        <option disabled selected>--Choose Batch--</option>
                    @foreach($course->batches as $batch)
                        <option value="{{$batch->id}}">{{$batch->commence_date}}</option>
                    @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Payment option:</strong>
                </div>
                <div class="col m6 s6">
                    <select name="paymentOption">
                        <option disabled selected>--Choose payment option--</option>
                        <option value="ot">One time (Total fees: &#8377;{{ $course->fees - ($course->fees * $course->discount)/100 }})</option>
                        @foreach($course->installments as $installment)
                            <option value="{{$installment->id}}">{{$installment->frequency}} installments (Total fees: &#8377;{{array_sum(explode(';',$installment->amounts))}})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Payment type:</strong>
                </div>
                <div class="col m6 s6">
                    <select name="paymentType" id="paymentType">
                        <option disabled selected>--Choose payment option--</option>
                        <option value="0">Offline Payment</option>
                        <option value="1">Online Payment ( Instamojo )</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Institute promo code:</strong>
                </div>
                <div class="col m6 s6">
                    <div class="input-field col m5">
                        <input type="text" name="ins_promo_code" id="ins-promo-code" style="margin-bottom: 0">
                        <label for="ins-promo-code">Promo Code</label>
                        <span class="red-text" style="font-size: 11px;" id="ins-promo-message"></span>
                    </div>
                    <div class="input-field col m5">
                        <a class="btn-flat waves-effect" id="ins-promo-apply">Apply</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Yoken promo code:</strong>
                </div>
                <div class="col m6 s6">
                    <div class="input-field col m5">
                        <input type="text" name="promo_code" id="yok-promo-code" style="margin-bottom: 0">
                        <label for="yok-promo-code">Promo Code</label>
                        <span class="red-text" style="font-size: 11px;" id="yok-promo-message"></span>
                    </div>
                    <div class="input-field col m5">
                        <a class="btn-flat waves-effect" id="yok-promo-apply">Apply</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col m6 s6">
                    <strong>Payable amount:</strong>
                </div>
                <div class="col m6 s6" id="course-final-fees">
                    &#8377; {{ $course->fees - ($course->fees * $course->discount)/100 }}
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
                <button class="btn waves-effect"type="submit" value="Enroll">Enroll now</button>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#yok-promo-apply').click(function() {
            var formData = new FormData();
            formData.append('code',$('#yok-promo-code').val());
            formData.append('courseId', '{{$course->id}}');
            formData.append('paymentOption',$('select[name=paymentOption]').val());
            formData.append('couponBy','');
            var params="";
            for (var pair of formData.entries()) {
                params += pair[0] +"="+ pair[1] + "&";
            }
            $.post({
                url: '/apply-coupon',
                data: params,
                processData: false,
                success: function(data){
                    $("#course-final-fees").html('&#8377; ' + data.message);
                },
                error: function(response){
                    alert(JSON.parse(response.responseText).message);
                }
            });
        });
        $('#ins-promo-apply').click(function() {
            var formData = new FormData();
            formData.append('code',$('#ins-promo-code').val());
            formData.append('courseId', '{{$course->id}}');
            formData.append('paymentOption',$('select[name=paymentOption]').val());
            formData.append('couponBy','');
            var params="";
            for (var pair of formData.entries()) {
                params += pair[0] +"="+ pair[1] + "&";
            }
            $.post({
                url: '/apply-coupon',
                data: params,
                processData: false,
                success: function(data){
                    $("#course-final-fees").html('&#8377; ' + data.message);
                },
                error: function(response){
                    alert(JSON.parse(response.responseText).message);
                }
            });
        });
        $("select[name='paymentOption']").change(function(){
            if(this.value == 'ot') {
                $("#paymentType").closest('div.row').show();
                $("#paymentType option").prop("selected", false);
                $("#paymentType option[value='1']").prop("selected", true);
            }
            else {
                $("#paymentType").closest('div.row').hide();
                $("#paymentType option").prop("selected", false);
                $("#paymentType option[value='0']").prop("selected", true);
            }
        });
    });
</script>
</html>
