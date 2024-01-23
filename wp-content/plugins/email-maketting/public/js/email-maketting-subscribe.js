(function ($) {
    'use strict';
    $(function () {
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

        var curent_url = window.location.href;
        var newsletter_request = $('.newsletter-request');

        function stripUrlToHtml(url) {
            var regex = /unsubscribe\.html/;
            var strippedUrl = url.match(/(.*\.html)(\?|#|$)/);

            if (regex.test(url)) {
                return 'new';
            } else {
                if (strippedUrl) {
                    return strippedUrl[1];
                } else {
                    var baseRegex = /^(.*\/)([^\/]*)\/?$/;
                    var baseStrippedUrl = url.match(baseRegex);
                    return baseStrippedUrl ? baseStrippedUrl[1] : null;
                }
            }
        }


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

        function render_html(parent, subscribe_section) {
            if (parent.length) {
                parent.html('Invalid email, please check again.');
                parent.css({'color': '#ff0000'});
            } else {
                subscribe_section.html('Invalid email, please check again.');
                subscribe_section.css({'color': '#ff0000'});
            }
        }

        function ajax_callback(parent, subscribe_section, email){
            $.ajax({
                url: email_subscribe_vars.subscribe_ajax_url,
                type: 'POST',
                data: {
                    action: 'subscribe_callback',
                    email: email,
                    post_url: stripUrlToHtml(curent_url)
                },

                beforeSend: function () {
                    $("#overlay").fadeIn();
                },

                success: function (response) {
                    if (parent.length) {
                        parent.html(response.data.message);
                        if (response.data.success) {
                            parent.css({'color': '#026c33'});
                        }
                    } else {
                        subscribe_section.html(response.data.message);
                        subscribe_section.css({'color': '#ff0000'});
                        if (response.data.success) {
                            subscribe_section.css({'color': '#026c33'});
                        }
                    }
                },
                complete: function () {
                    $("#overlay").fadeOut();
                },
                error: function (xhr, textStatus, errorThrown) {
                    $('.message').html('An error occurred, please try again another time.');
                    console.log(xhr);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            });
        }

        function _newsletter_request() {
            if (newsletter_request.length) {
                newsletter_request.on('click', function (e) {
                    e.preventDefault();
                    var $this = $(this);
                    var parent = $this.parents('.re_subscribe_form').find('.message');
                    var subscribe_section = $this.parents('.subscribe-section').find('.message');
                    var email = $this.parents('.subscribe-form').find('.subscribe-email').val();
                    var emailRegex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;

                    if (email === null || email === '' || !emailRegex.test(email)) {
                        render_html(parent, subscribe_section);
                        return false;
                    }

                    var domain = email.split('@')[1];

                    if (isValidDomain(domain)) {
                        _check_email_domain('https://'+domain, function(status){
                            if(status === 200){
                                ajax_callback(parent, subscribe_section, email);
                            } else {
                                render_html(parent, subscribe_section);
                                return false;
                            }
                        });
                    } else {
                        ajax_callback(parent, subscribe_section, email);
                    }
                });
            }
        }

        _newsletter_request();

    });
})(jQuery);
