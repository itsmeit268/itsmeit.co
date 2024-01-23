(function( $ ) {
	'use strict';
	$(function () {
		var $contact_request = $('#submit-request');
		var excludedDomains = [
			'gmail.com',
			'yahoo.com',
			'icloud.com',
			'hotmail.com',
			'outlook.com',
			'protonmail.com',
			'zohomail.com',
			'yandex.com',
			'mail.ru',
			// Các dịch vụ email khác mà bạn muốn loại bỏ
		];

		function isValidDomain(domain) {
			return !excludedDomains.includes(domain.split('.').slice(-2).join('.').toLowerCase());
		}

		function _check_email_domain(url, callback){
			jQuery.ajax({
				url:      url,
				dataType: 'jsonp',
				type:     'GET',
				beforeSend: function () {
					$("#overlay").fadeIn();
				},
				complete:  function(xhr){
					$("#overlay").fadeOut();

					if(typeof callback === 'function') {
						callback.apply(this, [xhr.status]);
					}
				}
			});
		}

		function contact_send_request(){
			$contact_request.on('click', function (e) {

				var $this = $(this);
				var $parents = $this.parents('#form-contact');
				var full_name = $parents.find('#full-name').val();
				var email = $parents.find('#contact-email').val();
				var content = $parents.find('#contact-message').val();
				var subject = $parents.find('#contact-subject').val()
				var contact_response = $('.contact-response');


				if (full_name == '' || email == '' || content == '' || subject == '') {
					return;
				}

				e.preventDefault();
				var domain = email.split('@')[1];

				if (isValidDomain(domain)) {
					_check_email_domain('https://'+domain, function(status){
						if(status === 200){
							ajax_callback(full_name, email, subject, content, contact_response)
						} else {
							contact_response.html('<p style="color:red">Invalid email, please check again.</p>');
							return false;
						}
					});
				} else {
					ajax_callback(full_name, email, subject, content, contact_response)
				}
			});

		}

		function ajax_callback(full_name, email, subject, content, contact_response) {
			$.ajax({
				url: email_contact_vars.contact_ajax_url,
				type: 'POST',
				data: {
					action: 'contact_callback',
					full_name: full_name,
					email: email,
					subject: subject,
					content: content
				},
				beforeSend: function() {
					$("#overlay").fadeIn();
				},

				success: function(response) {
					contact_response.html('<p>' + response.data.message + '</p>');
					if (response.data.success === '1') {
						$('.contact-form').remove();
					} else {
						contact_response.css('color', 'red');
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
		}

		contact_send_request();
	});
})( jQuery );
