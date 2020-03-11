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
	<link rel="stylesheet" type="text/css" href="/css/jquery.bxslider.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
</head>
<body>
	@include('partials.nav')
	<section class="hero-section green">
		<div class="row">
			<h3 class="center-align welcome-text"><span class="white-text">Welcome to Yoken Online</span></h3>
			<h5 class="center-align support-welcome-text"><span class="white-text">Learn your favourite course <span class="rotate-text">from the best institute, in Bhubaneswar, at lowest price, for a better future</span></span></h5>
		</div>

		<div class="container">
			<form action="/browse-courses" method="GET">
				{{csrf_field()}}
				<div class="search-container">
					<input type="text" id="search-field" placeholder="Search your Yoken course.." name="q" id="course">
					<button id="search-button">Search</button>
				</div>
			</form>
		</div>
	</section>
	<br>
	<section class="about-section center-align">
		<div id="about-us" class="container section scrollspy left-align">
			<h5 class="grey-text center-align text-darken-4">Courses for all</h5>
			<br>
			<div class="row">
				<div class="col m4 s12">
					<div class="card hoverable category-card" style="cursor:pointer" onclick="$(this).find('form').submit();">
						<div class="card-image center-align">
							<img class="responsive-img category-image" src="/img/cs.jpg">
							<span class="card-title">Computer Science & Engineering</span>
						</div>
						<form action="/browse-courses" style="display: none;">
							<input type="hidden" name="categories[]" value="1">
						</form>
					</div>
				</div>
				<div class="col m4 s12">
					<div class="card hoverable category-card" style="cursor:pointer" onclick="$(this).find('form').submit();">
						<div class="card-image center-align">
							<img class="responsive-img category-image" src="/img/it.jpg">
							<span class="card-title">Information Technology</span>
						</div>
						<form action="/browse-courses" style="display: none;">
							<input type="hidden" name="categories[]" value="11">
						</form>
					</div>
				</div>
				<div class="col m4 s12">
					<div class="card hoverable category-card" style="cursor:pointer" onclick="$(this).find('form').submit();">
						<div class="card-image center-align">
							<img class="responsive-img category-image" src="/img/ee.jpg">
							<span class="card-title">Electrical Engineering</span>
						</div>
						<form action="/browse-courses" style="display: none;">
							<input type="hidden" name="categories[]" value="2">
						</form>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col m4 s12">
					<div class="card hoverable category-card" style="cursor:pointer" onclick="$(this).find('form').submit();">
						<div class="card-image center-align">
							<img class="responsive-img category-image" src="/img/civil.jpg">
							<span class="card-title">Civil Engineering</span>
						</div>
						<form action="/browse-courses" style="display: none;">
							<input type="hidden" name="categories[]" value="3">
						</form>
					</div>
				</div>
				<div class="col m4 s12">
					<div class="card hoverable category-card" style="cursor:pointer" onclick="$(this).find('form').submit();">
						<div class="card-image center-align">
							<img class="responsive-img category-image" src="/img/Mech.jpg">
							<span class="card-title">Mechanical Engineering</span>
						</div>
						<form action="/browse-courses" style="display: none;">
							<input type="hidden" name="categories[]" value="5">
						</form>
					</div>
				</div>
				<div class="col m4 s12">
					<div class="card hoverable category-card" style="cursor:pointer" onclick="$(this).find('form').submit();">
						<div class="card-image center-align">
							<img class="responsive-img category-image" src="/img/banking.jpg">
							<span class="card-title">Banking, IAS and others</span>
						</div>
						<form action="/browse-courses" style="display: none;">
							<input type="hidden" name="categories[]" value="10">
						</form>
					</div>
				</div>
			</div>
		</div>
		
	</section>
	<section class="container about-section">
		<h5 class="center-align">How it works</h5>
		<br>
		<ul class="bxslider">
			<li><img src="/img/hiw-1.png"/></li>
			<li><img src="/img/hiw-2.png" /></li>
			<li><img src="/img/hiw-3.png" /></li>
		</ul>
	</section>
	<div class="divider"></div>
	<section class="our-partners">
		<h5 class="center-align">Our Partners</h5>
		<br>
		<br>
		<marquee behavior="scroll" direction="">
			<img class="client-logo" src="/img/clients/jt.png" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/arifin.png" alt="Yoken client">
			<img class="client-logo" src="/img/clients/attitude-bs.png" alt="Yoken client">
			<img class="client-logo" src="/img/clients/cadd.jpeg" alt="Yoken client">
			<img class="client-logo" src="/img/clients/disha.jpg" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/vj.jpg" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/english-mania.png" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/frameboxx.jpg" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/hint.jpg" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/hitech.png" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/inno-dust.jpg" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/jifsa.jpg" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/lcc.png" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/photo.jpg" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/sysoft.png" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/talent-sigma.jpg" alt="Yoken Client">
			<img class="client-logo" src="/img/clients/train-guru.png" alt="Yoken Client">
		</marquee>
	</section>
	@include('partials.footer')
	<script src="/js/morphext.min.js"></script>
	<script src="/js/jquery.bxslider.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
	<script type="text/javascript">
		 $(document).ready(function() {
			if(window.location.pathname == '/' && window.location.search.indexOf('?m=1') != -1) {
				swal("Success!", "You have been successfully enrolled!", "success");
			 }
			if(window.location.pathname == '/' && window.location.search.indexOf('?m=2') != -1) {
				swal("Failed!", "Your payment has failed :(\nContact support team for help.", "error");
			 }
             $('.carousel').carousel();
             $('.carousel.carousel-slider').carousel({
                 fullWidth: true,
				 dist: 0
             });
		    $('.scrollspy').scrollSpy({
		    	scrollOffset: 90
		    });
		  });
         $(".rotate-text").Morphext({
             animation: "fadeInDown",
             separator: ",",
             speed: 3000,
			 complete: function() {
			 }
         });
         $('.bxslider').bxSlider({
             mode: 'horizontal',
             default: true,
			 auto: true,
		 });

	</script>
</body>
</html>
