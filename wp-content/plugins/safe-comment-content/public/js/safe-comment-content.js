(function( $ ) {
	'use strict';
	$(function () {
		var $comment_content = $('.comment-content,.wpd-comment-text'),
			post_url = window.location.href.replace(/#.*/, ''),
			end_point = $.trim(comment_vars.end_point),
			bloginfo_url = $.trim(comment_vars.bloginfo_url),
			expiration_time = new Date(Date.now() + 10 * 60 * 1000);

		function _set_cookie_page_direct(href) {
			__set_cookie_url("pr_hr", href);
		}

		function __set_cookie_url(name, value) {
			document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
			document.cookie = `${name}=${value}; expires=${expiration_time.toUTCString()}; path=/`;
		}

		function _endpoint() {
			if (post_url.indexOf('?') !== -1) {
				post_url = post_url.split('?')[0];
			}

			if (post_url.indexOf(".html") > -1 && post_url.includes('.html')) {
				post_url = post_url.match(/.*\.html/)[0] + '/';
			} else if (post_url.includes('/' + end_point + '/')) {
				post_url = post_url.replace('/' + end_point + '/', '');
			} else if (post_url.indexOf('.html') === -1 && !post_url.endsWith('/')) {
				post_url = post_url + '/';
			}
			return post_url + end_point;
		}

		function _link_click_process() {
			$comment_content.find('a').on('contextmenu', function (e) {
				var href = $(this).attr('href');
				var url = new URL(href);

				if (url.origin !== bloginfo_url) {
					e.preventDefault();
				}
			});

			$comment_content.find('a').on('click', function (e) {
				var href = $(this).attr('href');
				var url = new URL(href);

				if (url.origin !== bloginfo_url) {
					e.preventDefault();
					_set_cookie_page_direct(href);
					window.open(_endpoint(), '_blank');
				}
			});
		}

		_link_click_process();
	});
})( jQuery );
