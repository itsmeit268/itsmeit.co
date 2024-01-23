(function ($) {
    $(function () {
        var windowWidth = $(window).width();
        // var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        // var isTablet = /iPad/i.test(navigator.userAgent);
        var blog_wrap = $('.blog-wrap');
        var copyright_menu = $('#copyright-menu');

        function _moveFooterLogo() {
            var footerLogo = $('.footer-logo');
            var footerQuoteAbout = $('.footer-quote-about');
            if (windowWidth > 700 && footerLogo.length && footerQuoteAbout.length) {
                footerLogo.prependTo(footerQuoteAbout);
            }
        }

        function _moveFormTip() {
            if ($('.wpcf7-not-valid-tip').length) {
                $(".wpcf7-not-valid-tip").prependTo(".mc4wp-form-fields");
            }
        }

        function _fix_header_mobile_elm() {
            var header_mobile = $('#header-mobile');
            if (windowWidth > 700 && header_mobile.length) {
                header_mobile.remove();
            }
        }

        function _select_language() {
            var $languageSelectorClone = $('.language-selector').clone();
            if (windowWidth > 700) {
                $languageSelectorClone.insertBefore('.footer-social-list-title');
            } else {
                $languageSelectorClone.insertBefore('.footer-copyright');
            }

            var $dropdownButton = $('.language-selector-dropdown');
            var $dropdownMenu = $('.dropdown-menu');
            var $dropdownItem = $('.dropdown-item');

            $dropdownButton.on('click', function () {
                $dropdownMenu.toggle();
            });

            $dropdownItem.on('click', function (e) {
                e.preventDefault();
                $dropdownMenu.hide();
                $dropdownButton.html(`${$(this).text().trim()} <i class="fas fa-caret-down"></i>`);
                window.location.href = $(this).attr('value');
            });
        }

        function _show_level() {
            var level_name = $('.show-level');
            var data = {
                action: 'show_level_callback'
            };
            $.ajax({
                type: 'POST',
                url: ims.ajax_url,
                data: data,
                success: function(response) {

                    if (level_name.text() !== response.data.level) {
                        level_name.text(' ('+response.data.level+')');
                    }
                    if (response.data.level === 'FREE') {
                        level_name.css('color', '#00b38f');
                    }
                    if (response.data.level === 'PREMIUM') {
                        level_name.css('color', '#0c8b1d');
                    }
                    if (response.data.level === 'VIP') {
                        level_name.css('color', '#ff0000');
                    }
                }
            });
        }

        if (blog_wrap.length) {
            blog_wrap.css ('margin-top', '0');
        }

        if (copyright_menu.length && windowWidth < 700) {
            copyright_menu.remove();
        }

        _moveFooterLogo();
        _moveFormTip();
        _fix_header_mobile_elm();
        _select_language();
        _show_level();

    });
})(jQuery);