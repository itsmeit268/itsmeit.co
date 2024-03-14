/**
 * @author     itsmeit <buivanloi.2010@gmail.com>
 * Website     https://itsmeit.co/
 */

(function ($) {
    'use strict';

    $(function () {
        var $progress = $('#endpoint-progress'),
            time_cnf = parseInt(prep_template.countdown_endpoint),
            auto_direct = parseInt(prep_template.endpoint_direct),
            page_elm = $('#prep-request-page'),
            preUrlGo = page_elm.data('request'),
            t2_timer = $('#preplink-timer-link'),
            href_modify = prep_template.modify_conf,
            require_vip = $('.not-vip-download'),
            is_login = prep_template.is_user_logged_in;

        /**
         * @param url
         * @returns {*}
         */
        function href_restore(url) {
            if (url.includes(atob(href_modify.mstr)) || url.includes(atob(href_modify.sfix))) {
                return url.replace(href_modify.pfix, '').replace(atob(href_modify.mstr), '').replace(atob(href_modify.sfix), '');
            }
            return url.replace(href_modify.pfix, '').replace(href_modify.mstr, '').replace(href_modify.sfix, '');
        }

        function redirect_link() {
            $('.preplink-btn-link,.list-preplink-btn-link').on('click', function (e) {
                e.preventDefault();
                var self = $(this);
                const _href = window.atob(href_restore(self.data('request')) || href_restore(preUrlGo));
                const point = self.data('point');

                if (!is_login || point === 0) {
                    window.location.href = _href;
                } else {
                    const lang = prep_template._language;
                    $.magnificPopup.open({
                        items: {
                            src: '<div class="white-popup">' +
                                '<p>' + (lang === 'en' ? 'Are you sure you want to redeem' : 'Bạn có chắc muốn đổi') +
                                ' <span id="points-to-redeem">' + point + '</span> ' + (lang === 'en' ? 'points to download the file?' : 'điểm để tải tập tin?') +
                                '</p>' +
                                '<button id="confirm" style="">'+(lang === 'en' ? 'Yes' : 'OK')+'</button><button id="cancel" style="">'+ (lang === 'en' ? 'Cancel' : 'Hủy')+'</button>' +
                                '</div>',
                            type: 'inline'
                        },
                        callbacks: {
                            open: function() {
                                $('#confirm').on('click', function() {
                                    $.ajax({
                                        url: prep_template._ajax_url,
                                        type: 'post',
                                        data: {
                                            point: point,
                                            href: _href,
                                            action: 'update_point_download',
                                            title:  self.text()
                                        },
                                        success: function (response) {
                                            if (response.success) {
                                                window.location.href = _href;
                                            }
                                        }
                                    });
                                });

                                $('#cancel').on('click', function() {
                                    $('.mfp-close').trigger('click');
                                });
                            }
                        }
                    });

                }
            });
        }

        function scrollToProgressElm() {
            $('.clickable,.prep-title').on('click', function () {
                if (time_cnf === 0) {
                    window.location.href = preUrlGo.atob(href_restore(preUrlGo));
                    return;
                }
                $progress.trigger('click');
                $('html, body').animate({
                    scrollTop: $progress.offset().top - 150
                }, 100);
            });
        }

        /**
         * Chức năng xử lý sự kiện click để download/nhận liên kết */
        function progressRunning() {
            if (time_cnf > 0) {
                var isProgressRunning = false;
                $progress.on('click', function (e) {
                    e.preventDefault();
                    var $progress = $(this);
                    $progress.show();

                    if (isProgressRunning) {
                        return;
                    }
                    isProgressRunning = true;

                    const $counter = $('.counter');
                    const startTime = new Date().getTime();
                    const totalTime = time_cnf * 1000;
                    let isCountdownFinished = false;

                    function updateProgress() {
                        const currentTime = new Date().getTime();
                        const timeRemaining = totalTime - (currentTime - startTime);

                        if (timeRemaining <= 200) {
                            $counter.html('');
                            $('.prep-btn-download').appendTo($counter).fadeIn(1000);

                            if ($('.list-link-redirect,.list-server-download').length) {
                                $('.list-server-download').fadeIn(1000);

                                $progress.fadeOut(100);
                                if (require_vip.length) {
                                    page_elm.removeAttr('data-request');
                                    require_vip.fadeIn(1000);
                                }
                            }

                            clearInterval(interval);
                            isCountdownFinished = true;
                            isProgressRunning = false;
                            $progress.off('click');
                            if (auto_direct) {
                                window.location.href = window.atob(href_restore(preUrlGo));
                            }
                        } else if (!isCountdownFinished) {
                            const percent = Math.floor((1 - timeRemaining / totalTime) * 100);
                            $('.bar').css('width', percent + '%');
                            $counter.html(percent + '%');
                        }

                        if (isCountdownFinished) {
                            $('.bar').css('width', '100%');
                        }
                    }

                    let interval = setInterval(updateProgress, 10);
                    setTimeout(() => clearInterval(interval), totalTime);

                    $counter.on('click', function (e) {
                        if (!isCountdownFinished) {
                            e.preventDefault();
                        } else {
                            window.location.href = window.atob(href_restore(preUrlGo));
                        }
                    });
                });

                /**
                 * Countdown
                 */
                if (t2_timer.length) {
                    var data_time = t2_timer.attr('data-time');

                    function countdown(sec) {
                        sec--;
                        if (sec > 0) {
                            t2_timer.html('' + sec + '');
                            setTimeout(function () {
                                countdown(sec);
                            }, 1200);
                        } else {
                            $("#buttondw").addClass('del-timer');
                            if (require_vip.length) {
                                page_elm.removeAttr('data-request');
                                require_vip.fadeIn(1000);
                            }
                            if (auto_direct) {
                                var request_link = href_restore(preUrlGo);
                                window.location.href = window.atob(request_link);
                            }
                        }
                    }

                    countdown(data_time);
                }
            }
        }

        progressRunning();
        redirect_link();
        scrollToProgressElm();
    });
})(jQuery);
