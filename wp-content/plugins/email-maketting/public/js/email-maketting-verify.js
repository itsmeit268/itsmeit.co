(function( $ ) {
	'use strict';
	$(function () {
		var urlParams = new URLSearchParams(window.location.search);
		var rqco = urlParams.get('rqco');
		var rqem = urlParams.get('rqem');
		var verify_email = $('.verify-email');

		function isBase64Encoded(value) {
			try {
				atob(value);
				return true;
			} catch (e) {
				return false;
			}
		}

		function send_ajax_verify(){
			if (isBase64Encoded(rqem)) {
				$.ajax({
					url: email_verify_vars.verify_ajax_url,
					type: 'POST',
					data: {
						action: 'verify_callback',
						verify_code: rqco,
						verify_email: atob(rqem)
					},
					beforeSend: function() {
						$("#overlay").fadeIn();
					},

					success: function(response) {
						verify_email.html(response.data.message);
						verify_email.css({'color': '#ff0000'});
						if (response.data.success === 'verified') {
							verify_email.css({'color': '#026c33'});
						}
						if (response.data.success === 'verify_null') {
							$('.re_subscribe_form').css({'display': 'block'});
						}
					},
					complete: function () {
						$("#overlay").fadeOut();
					},
					error: function(xhr, textStatus, errorThrown) {
						$('.verify-email').html('An error occurred, please try again another time.');
						console.log(xhr);
						console.log(textStatus);
						console.log(errorThrown);
					},
				});
			} else {
				$('.verify-email').html('Authentication failed. The link does not exist or has expired.\n');
			}
		}
		send_ajax_verify();
	});
})( jQuery );
