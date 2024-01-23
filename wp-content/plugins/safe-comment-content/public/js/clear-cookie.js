/**
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co | https://itsmeit.biz
 */

(function ($) {
    'use strict';

    $(function () {
        var end_point = $.trim(clear_cookie_vars.end_point),
            current_url = window.location.href;

        if (!current_url.endsWith("/"+end_point)) {
            document.cookie = "pr_hr" + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        }
    });
})(jQuery);
