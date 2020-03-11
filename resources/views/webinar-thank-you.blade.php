<!DOCTYPE html>
<html>
<head>
    <title>Yoken | Home</title>
    <meta name="viewport" content="width=device-width">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/materialize.css">
    <link rel="stylesheet" type="text/css" href="/css/main.css">
</head>
<body>
@include('partials.nav')
<br>
<div class="container center-align">
    <img style="width:200px" src="/img/yoken-logo.png" alt="">
    <br><br><br>
    <?php if ($_GET["m"] == 1) : ?>
        <h4 class="center-align">Success!</h4>
        <br>
        <p><em>Your payment was successfully. We appreciate your participation in our live webinar.</em></p>
    <?php else : ?>
        <h4 class="center-align">Failed!</h4>
        <br>
        <p><em>Your payment has failed :(<br />Contact support team for help.</em></p>
    <?php endif; ?>
    <p><a href="/browse-courses">Browse courses</a> to find your favourite course today!</p>
    <br><br><br>
</div>
@include('partials.footer')