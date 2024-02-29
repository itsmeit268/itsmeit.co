<?php

//add_action('wp_head', 'google_analytics_script');
//add_action('wp_head', 'google_adsense');
//add_action('wp_footer', 'add_facebook_script_footer');

function google_analytics_script(){
    $manager = current_user_can('manage_options');
    $IP = array('127.0.0.1', '127.0.1.1', 'localhost');
    if (!$manager && !in_array($_SERVER['REMOTE_ADDR'], $IP)) {
        ?>
        <script type="application/javascript">
            window.addEventListener('load', function () {
                var is_analytics_load = 0;
                window.addEventListener('scroll', function () {
                    if (is_analytics_load === 0) {
                        is_analytics_load = 1;
                        var analyticsElm = document.createElement('script');
                        analyticsElm.src = 'https://www.googletagmanager.com/gtag/js?id=G-YKVNLMPXLB';
                        var analytics_sc = document.getElementsByTagName('script')[0];
                        analytics_sc.parentNode.insertBefore(analyticsElm, analytics_sc);

                        window.dataLayer = window.dataLayer || [];
                        function gtag(){dataLayer.push(arguments);}
                        gtag('js', new Date());
                        gtag('config', 'G-YKVNLMPXLB');
                    }
                });
            });
        </script>
        <?php
    }
}

function google_adsense() {
    ?>
    <?php if (is_allow_show_ads() && aicp_can_see_ads()) { ?>
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3109927831594907" crossorigin="anonymous"></script>
        <?php } ?>
    <?php include_once get_theme_file_path('include/ezoic_shortcode.php'); ?>
<?php }

function add_facebook_script_footer() {
    ?>
    <!-- Messenger Plugin chat Code -->
    <div id="fb-root"></div>
    <div id="fb-customer-chat" class="fb-customerchat"></div>
    <script>
        window.addEventListener('load', function () {
            var is_facebook_load = 0
            var chatbox = document.getElementById('fb-customer-chat');
            chatbox.setAttribute("page_id", "111009301424827");
            chatbox.setAttribute("attribution", "biz_inbox");

            window.addEventListener('scroll', function () {
                if (is_facebook_load == 0) {
                    is_facebook_load = 1;
                    window.fbAsyncInit = function() {
                        FB.init({
                            xfbml   : true,
                            version : 'v17.0'
                        });
                    };

                    (function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s); js.id = id;
                        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));
                }
            }, false);
        });
    </script>
    <?php
}