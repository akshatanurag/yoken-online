(function() {

	//enroll();
	//console.log(grecaptcha.getResponse());
	document.querySelector('#promo-apply').addEventListener('click',function() {
		if($('#promo-code').val() == '') {
			$('#promo-message').html('Promo code cannot be empty!');
		}
		else checkPromo('YOKEN');
	});
	document.querySelector('#ins-promo-apply').addEventListener('click',function() {
		if($('#ins-promo-code').val() == '') {
			$('#ins-promo-message').html('Promo code cannot be empty!');
		}
		checkPromo(getInstituteId());
	});
	document.querySelector('#enroll-button').addEventListener('click',function() {
		enroll();
	});
    $('input[name=payment_option').on('click',function() {
        var id = $(this).val();
        $.post('php/EnrollStudent.php',{id:id},function(resp) {
        	if(typeof resp != 'undefined'
				&& typeof resp.message != 'undefined'
				&& typeof resp.message.amount != 'undefined'){
                $("#payable-amount").html(resp.message.amount);
			}
			else {
				console.log(typeof resp);
				console.log(typeof resp.message);
				console.log(typeof resp.message.amount);
				Materialize.toast('Error occurred. Please refresh and try again.',3000);
			}
        });

    });
}());
function checkPromo(promoBy) {
	$('#promo-message').html('');
	var xhr = new XMLHttpRequest();
	var params = window.location.search;
	params = params.replace('?','');
	if(promoBy == "YOKEN")
		params += '&promo_code='+$('#promo-code').val();
	else 
		params += '&promo_code='+$('#ins-promo-code').val();
	params += '&instituteId='+getInstituteId();
	params += '&promo_by=' + promoBy;
	console.log(params);
	//console.log(params);
	xhr.open('POST','php/PromoHandler.php',true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.send(params);
	xhr.onreadystatechange = function() {
	    if(xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
	    	var resp = JSON.parse(this.responseText);
	    	if((typeof resp.message.payableAmount)!= 'undefined') {
	    		document.querySelector('#payable-amount').innerHTML = "\u20B9"+resp.message.payableAmount;
	    		if(promoBy == "YOKEN"){
	    			document.querySelector('#promo-apply').className += ' disabled';
	    			document.querySelector('#promo-code').setAttribute('disabled',true);
	    		}
	    		else {
	    			document.querySelector('#ins-promo-apply').className += ' disabled';
	    			document.querySelector('#ins-promo-code').setAttribute('disabled',true);
	    		}
	    	}
	    	console.log(this.responseText);
	    	Materialize.toast(resp.message.message,3000);
	    }
	}
}


function enroll() {
	var xhr = new XMLHttpRequest();
	var params = window.location.search;
	params = params.replace('?','');
	//console.log(params);
	var selected_batch = document.querySelector('input[name=batch_id]:checked').value;
    var selected_installment = document.querySelector('input[name=payment_option]:checked').value;
	params += '&batch_id='+selected_batch;
	params += '&installment_id='+selected_installment;
    console.log(params);
	xhr.open('POST','php/EnrollStudent.php',true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	xhr.onreadystatechange = function() {
	    if(xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
	    	console.log(this.responseText);
	    	var resp = JSON.parse(this.responseText);
	    	Materialize.toast(resp.message.message);
	    	if(resp.message.code == 1) {
	    		document.querySelector('#enroll-button').className += ' disabled';
	    		document.querySelector('#payment-button').className = 'waves-effect waves-light btn';
	    		window.location = "payment.php";
	    	}
	    }
	}
	xhr.send(params);
	if(typeof grecaptcha != "undefined") {
		var response = grecaptcha.getResponse();
		params += '&g-response='+response;
	}
}