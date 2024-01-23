(function( $ ) {
	'use strict';
	$(function () {
		var request_sent = $('#unsubscribe-sent');
		if (request_sent.length) {
			request_sent.on('click',function(e) {
				e.preventDefault();
				$.ajax({
					url: email_maketting_vars.email_ajax_url,
					type: 'POST',
					data: {
						action: 'unsubscribe_callback',
						email: $(this).attr('data-email')
					},
					beforeSend: function() {
						$("#overlay").fadeIn();
					},
					success: function(response) {
						if (response.data.success) {
							$('.unsubscribe-request').html(response.data.success);
						}
						if (response.data.false) {
							$('.unsubscribe-page').html(response.data.false);
						}
					},
					error: function(xhr, textStatus, errorThrown) {
						console.log(xhr);
						console.log(textStatus);
						console.log(errorThrown);
					},
					complete: function () {
						$("#overlay").fadeOut();
					},
				});
			});
		}
	});
})( jQuery );
