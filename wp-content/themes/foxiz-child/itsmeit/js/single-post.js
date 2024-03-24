(function ($) {
    $(function () {

        var windowWidth = $(window).width();

        function _magnificPopup(container, imageSelector) {
            var magnificItems = [];

            $(container + ' ' + imageSelector).each(function() {
                var imgSrc = $(this).attr('data-src') || $(this).attr('src');
                var modifiedImgSrc = imgSrc.replace(/-\d+x\d+/, '');

                magnificItems.push({
                    src: modifiedImgSrc,
                    title: '',
                    type: 'image'
                });
            });

            $(container).on('click', imageSelector, function() {
                var index = $(container + ' ' + imageSelector).index(this);
                var currentItems = magnificItems.slice(index).concat(magnificItems.slice(0, index));

                $.magnificPopup.open({
                    items: currentItems,
                    type: 'image',
                    tLoading: 'Loading image #%curr%...',
                    mainClass: 'mfp-img-mobile',
                    gallery: {
                        enabled: true,
                        navigateByImgClick: true,
                    },
                    image: {
                        tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
                        titleSrc: function(item) {
                            return item.title + '<small>by ' + $('.i-logo').attr('title') + '</small>';
                        }
                    },
                    index: 0
                });
            });
        }

        function _faqRankmath() {
            if ($("body").hasClass("single-post")) {
                var question = ".rank-math-question",
                    answer = ".rank-math-answer";
                // $('#rank-math-faq').prepend('<h3 class="rank-math-title">Frequently asked questions</h3>');
                $(question).on("click", function (event) {
                    if (!$(this).parent().find(answer).is(":visible")) {
                        $(question).removeClass("faq-active");
                        $(this).addClass("faq-active");
                        $(answer).hide();
                    } else {
                        $(this).removeClass("faq-active");
                    }
                    $(this).parent().find(answer).toggle(300);
                });
            }
        }

        function _removeTextShare() {
            var $share = $('.e-shared-sec');
            $share.find('a.icon-facebook').find('span').remove();
            $share.find('a.icon-twitter').find('span').remove();
        }

        function _fixElmEnlighterTheme_and_ads() {
            window.addEventListener('load', function () {
                var $enlighter_theme_custom = $('.enlighter-theme-custom>.enlighter-code>.enlighter'),
                    $godzilla_theme_itsmeit = $('.godzilla-theme-itsmeit>.enlighter-code>.enlighter'),
                    $godzilla_theme_hide_line_number = $('.godzilla-theme-hide-line-number>.enlighter-code>.enlighter'),
                    $power_shell_script_monokai = $('.power-shell-script-monokai-2>.enlighter-code>.enlighter');

                $enlighter_theme_custom.each(function () {
                    var $this = $(this),
                        div_count = $this.find('div').length;
                    if (div_count <= 2) {
                        $this.find('div:first').addClass('enlighter-theme-1');
                    } else if (div_count > 2) {
                        $this.find('div').addClass('enlighter-theme-2');
                    }
                });
                $godzilla_theme_itsmeit.each(function () {
                    var $this = $(this),
                        div_count = $this.find('div').length;
                    if (div_count <= 2) {
                        $this.find('div:first').addClass('godzilla-theme-1');
                    } else if (div_count > 2) {
                        $this.find('div').addClass('godzilla-theme-2');
                    }
                });
                $godzilla_theme_hide_line_number.each(function () {
                    var $this = $(this),
                        div_count = $this.find('div').length;
                    if (div_count <= 2) {
                        $this.find('div:first').addClass('godzilla-hide-line-1');
                    } else if (div_count > 2) {
                        $this.find('div').addClass('godzilla-hide-line-1');
                    }
                });
                $power_shell_script_monokai.each(function () {
                    var $this = $(this);
                    if ($this.find('div').length > 2) {
                        $this.find('div').not(':first').addClass('enlighter-theme-2');
                    }
                });

                document.addEventListener('copy', function(event) {
                    var target = event.target;
                    var displayName = ims.display_name;
                    if (target && (target.closest('.enlighter-default') !== null ||
                            target.closest('.enlighter-clipboard') ||
                            target.closest('.wp-block-code') ||
                            target.closest('#comments')) ||
                        [
                            'itsmeit',
                            'thuytt.92',
                            'thuttt.92',
                            'ドゥヤン.tl',
                            'duyanh.tl',
                            'Duyanh.tl',
                            'それは作られた',
                            '그것은 만들어졌다',
                            'มันทำ',
                            'minhanh2405',
                            'ミンハン2405',
                            'トゥイット.92',
                            'it\'s made',
                            'duyanh',
                            'loibv',
                            'ItsmeIT'
                        ].includes(displayName)
                    ) {
                        return;
                    }

                    var copiedText = window.getSelection().toString();
                    if (copiedText.length > 0) {
                        event.preventDefault();
                        var copiedData = copiedText + ' (Nguồn trích dẫn từ' + ' ' +document.URL + ')';
                        event.clipboardData.setData('text/plain', copiedData);
                    }
                });

                /**
                 * Fix show ads */
                if (windowWidth > 700) {
                    $('.is-layout-flex').each(function () {
                        var $codeBlockInside = $(this).find('.itsmeit-code-block');
                        if ($codeBlockInside.length) {
                            $codeBlockInside.each(function () {
                                var $codeBlock = $(this);
                                var $parentFlex = $codeBlock.parents('.is-layout-flex');
                                if ($parentFlex.length) {
                                    $codeBlock.detach().insertAfter($parentFlex);
                                }
                            });
                        }
                    });
                }
            });
        }

        function _remove_adstera_mb(){
            var elm = $('.advertising-adsterra');
            if (windowWidth < 700 && elm.length) {
                elm.remove();
            }
        }

        _removeTextShare();
        _faqRankmath();
        _fixElmEnlighterTheme_and_ads();
        _remove_adstera_mb();
        _magnificPopup('#ftwp-postcontent', 'figure.wp-block-image img,.aligncenter img');
        _magnificPopup('.entry-content', 'figure.wp-block-image img,.aligncenter img');
    });
})(jQuery);