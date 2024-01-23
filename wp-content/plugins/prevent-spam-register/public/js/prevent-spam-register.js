(function( $ ) {
	'use strict';
	$(function () {

		var button = $('#wp-submit'),
			redirect = prevent_spam_vars.redirect,
			redirect_url = prevent_spam_vars.redirect_url,
			block_lists = prevent_spam_vars.block_list,
			exclude_lists = prevent_spam_vars.exclude_list;

		function block_list(domain) {
			var keys_array = block_lists.split('\r\n');
			return keys_array.includes(domain);
		}

		function exclude_list(domain) {
			var keys_array = exclude_lists.split('\r\n');
			return keys_array.includes(domain);
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
				},
				error: function (xhr, status, error) {
					$("#overlay").fadeOut();
					// console.error("Ajax request error: " + error);
				}
			});
		}

		function _ajax_loader_elm() {
			var html = '<div id="overlay" style="display: none">';
				html += '<div class="cv-spinner"><span class="spinner"></span></div>';
				html += '</div>';
			$('body').append(html);
		}

		function register_request() {
			if (button.length) {
				button.on('click', function (e) {
					var $this = $(this),
						login_elm = $this.parents('#login'),
						parents = $this.parents('form#registerform'),
						user_login = parents.find('input#user_login').val(),
						user_email = parents.find('input#user_email').val(),
						domain = user_email.split('@')[1];

					if (user_login === '' || user_email === '') {
						return;
					}

					if (!block_list(domain) && !exclude_list(domain)) {
						e.preventDefault();
						_ajax_loader_elm();
						_check_email_domain('https://'+ domain, function(status){
							if(status === 200){
								parents.off('submit');
								parents.submit();
							} else {
								if (redirect === '1') {
									window.location.href = redirect_url;
								} else {
									if (login_elm.length && login_elm.find('#login_error').length) {
										login_elm.find('#login_error').html('<strong>ERROR</strong>: Your email has been banned from registration.');
									} else {
										login_elm.find('.message.register').after('<div id="login_error"></div>');
										$('#login_error').html('<strong>ERROR</strong>: Your email has been banned from registration.');
									}
								}

								return false;
							}
						});
					}
				});
			}
		}

		register_request();
	});
})( jQuery );
