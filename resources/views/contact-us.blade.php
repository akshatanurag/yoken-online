<!DOCTYPE html>
<html>
<head>
    <title>Yoken | Home</title>
    <meta name="viewport" content="width=device-width">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="//cdn.jsdelivr.net/animatecss/3.4.0/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/materialize.css">
    <link rel="stylesheet" type="text/css" href="/css/morphext.css">
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <style>
        .contact-para {
            font-size:18px;
        }
        section.about-section {
            padding: 60px 0;
        }
        .contact-head {
            font-size: 18px
        }
        .contact-body {
            font-size: 18px
        }
    </style>
</head>
<body>
@include('partials.nav')
<br>
<section class="container about-section">
    <br>
    <h5 class="center-align">Contact us</h5>
    <br>
    <div class="container">
        <p class="center contact-para">
            We would truly appreciate any comments or feedback. <br>
            Please use the following form to speak to us regarding our work, project collaborations or just to say hi.<br>
            Alternatively you may also reach out to us directly via email or phone.<br>
        </p>
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col m3 s12" style="border-right: 1px solid #ccc;">
            <p class="center-align contact-head"><strong>Address</strong></p>
            <p class="center-align contact-body">531, JMD Megapolis, Sohna Road, Gurgaon, India 122001</p>
        </div>
        <div class="col m3 s12" style="border-right: 1px solid #ccc;">
            <p class="center-align contact-head"><strong>Phone</strong></p>
            <p class="center-align contact-body">+917440080777 <br> +917440090777 </p>
        </div>
        <div class="col m3 s12" style="border-right: 1px solid #ccc;">
            <p class="center-align contact-head"><strong>Email</strong></p>
            <p class="center-align contact-body">contact@yokenonline.com</p>
        </div>
        <div class="col m3 s12">
            <p class="center-align contact-head"><strong>Follow us:</strong></p>
            <div class="center-align contact-body">
                <a href=""><img height="40" width="40" src="/img/facebook-circular-logo-black.svg" alt="facebook-link"></a>
            </div>
        </div>
    </div>
    <br>
    <br>

</section>
@include('partials.footer')