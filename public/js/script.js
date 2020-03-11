(function() {
	document.querySelector('#main-preloader').className += ' active';
	var app = {
		resultItemTemplate : document.querySelector('.result-item-template'),
		searchResults: document.querySelector('.search-results'),
		spinner: document.querySelector('.loader'),
		isSignedIn: false,
		userName: ''
	};

	function updateItem(data){
		var item = app.resultItemTemplate.cloneNode(true);
		item.removeAttribute('hidden');
		item.classList.remove('search-item-template');
		item.querySelector('.course-name').textContent = data.course_name;

		var more_button = document.createElement('i');
		more_button.className = 'material-icons right green-text text-darken-1';
		more_button.innerHTML = 'more_vert';
		item.querySelector('.course-name').appendChild(more_button);

		item.querySelector('.course-title').innerHTML = data.course_name;
		var close_button = document.createElement('i');
		close_button.className = 'material-icons right';
		close_button.innerHTML = 'close';
		item.querySelector('.course-title').appendChild(close_button);
		item.querySelector('.course-title').appendChild(close_button);
		item.querySelector('.course-description').textContent = data.course_description;
		item.querySelector('.institute-name').setAttribute('href','institute.php?id='+data.institute_id);
		item.querySelector('.course-logo').setAttribute ('src', data.course_pic_link);
		item.querySelector('.institute-name').textContent = data.name+", "+data.city;
		item.querySelector('.course-discounted-price').textContent = "\u20B9"+(data.fees - (data.fees * data.discount)/100).toString();
		item.querySelector('.course-price').textContent = "\u20B9 "+data.fees;
		item.querySelector('.enroll-button').setAttribute('href',"enroll.php?course_id="+data.id) ;
		item.querySelector('.read-more').setAttribute('href',"course.php?id="+data.id) ;
		app.searchResults.appendChild(item);
	}

	function makeRequest(filters) {

		var params = window.location.search;
		if((typeof filters)!=='undefined') {
			params+=filters;
			$('.filter-card').toggleClass('filter-card-show');
			$('body').toggleClass('body-disable');
			$('.overlay-hidden').removeClass('overlay-show');
		}
		var xhr = new XMLHttpRequest();
		//console.log(params);
		xhr.open("GET","php/CourseRequestController.php"+params,true);
		xhr.send();
		xhr.onreadystatechange = function(){
			if(this.readyState == 4 && this.status == 200){
				$('.search-results').empty();
				$('.pagination').empty();

				document.querySelector('#main-preloader').className = 'preloader-wrapper';
				console.log(this.responseText);
				var resp = JSON.parse(this.responseText);
				var message = resp.message;
				for(var object in message){
					var item = message[object];
					updateItem(item);
				}
				setPagination(resp.result_count,resp.current_page,resp.limit);
			}
		};
	}
	var priceSlider = document.getElementById('price-range');
	noUiSlider.create(priceSlider, {
		start: [0, 20000],
		connect: true,
		tooltips: true,
		step: 2000,
		range: {
			'min': 0,
			'max': 20000
		},
		format: wNumb({
			decimals: 0
		})
	});
	var nosSlider = document.getElementById('nos-range');
	noUiSlider.create(nosSlider, {
		start: [0, 10000],
		connect: true,
		step: 500,
		tooltips: true,
		range: {
			'min': 0,
			'max': 10000
		},
		format: wNumb({
			decimals: 0
		})
	});

	$('#filter-apply').click(function() {
		var minFees = priceSlider.noUiSlider.get()[0];
		var maxFees = priceSlider.noUiSlider.get()[1];

		var minNos = nosSlider.noUiSlider.get()[0];
		var maxNos = nosSlider.noUiSlider.get()[1];


		var filters = '&minFees='+minFees+'&maxFees='+maxFees
		+'&minNos='+minNos+'&maxNos='+maxNos
		+'&filters='+true;
		makeRequest(filters)
	});

	$('#change-query-button').click(function() {
		$('.page-header').fadeOut();
		$('.search-field').fadeIn();
		//$('.page-header').prop('hidden',true);
		//$('.search-field').prop('hidden',false);
	});
	$('#close-search-field').click(function() {
		$('.page-header').fadeIn();
		$('.search-field').fadeOut();
	});
	$('#search-submit').click(function() {
		if($('input[name=q').val() == '') {
			$('input[name=q').addClass('invalid');
		}
		else $('form').submit();
	});

	jQuery('.modallink').on('click', function (ev) {
		var lnk = this.getAttribute('href');
		ev.preventDefault();
		jQuery("#courseDetailsLink").src = lnk;
	});
	makeRequest();

}());

function get(name) {
   if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
      return decodeURIComponent(name[1]);
}

function setPagination(resultCount, currentPage, limit) {
	currentPage = parseInt(currentPage);
	var pageNumberList = document.querySelector('#page-numbers');
	var pageCount = (resultCount/limit) + 1;
	var leftArrow = document.createElement('li');
	var a = document.createElement('a');
	if(currentPage == 1) {
		leftArrow.className+='disabled';
	}
	else {
		var params = window.location.search;
		params = params.replace(new RegExp('(page=)[0-9]+'),'page='+parseInt(currentPage-1));
		a.setAttribute('href','browse-courses.php'+params);
	}
	var i = document.createElement('i');
	i.className += 'material-icons';
	i.innerHTML = 'chevron_left';
	a.appendChild(i);
	leftArrow.appendChild(a);
	pageNumberList.appendChild(leftArrow);
	var i;
	for(i=1;i<=pageCount;i++) {
		var childLi = document.createElement('li');
		if(i == currentPage) {
			childLi.className += 'active';
		}
		else childLi.className += 'waves-effect';
		var childA = document.createElement('a');
		var params = window.location.search;
		if(params.search(new RegExp('(page)')) != -1) {
			params = params.replace(new RegExp('(page=)[0-9]+'),'page='+i);
		}
		else params += '&page='+i;
		childA.setAttribute('href','browse-courses.php'+params);
		childA.innerHTML = i;
		childLi.appendChild(childA);
		pageNumberList.appendChild(childLi);
	}
	var rightArrow = document.createElement('li');
	a = document.createElement('a');
	//console.log(currentPage);
	if(currentPage == (i-1)) {
		rightArrow.className+='disabled';
	}
	else {
		var params = window.location.search;
		if(params.search(new RegExp('(page)')) != -1) {
			params = params.replace(new RegExp('(page=)[0-9]+'),'page='+(currentPage+1));
		}
		else params += '&page='+(currentPage+1);
		a.setAttribute('href','browse-courses.php'+params);
	}
	i = document.createElement('i');
	i.className += 'material-icons';
	i.innerHTML = 'chevron_right';
	a.appendChild(i);
	rightArrow.appendChild(a);
	pageNumberList.appendChild(rightArrow);

}