<?php
/** Add facebook chat*/
//add_action('wp_footer', 'add_facebook_script_footer');
function add_facebook_script_footer()
{
    ?>
    <!-- Messenger Chat Plugin Code -->
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
                    window.fbAsyncInit = function () {
                        FB.init({
                            xfbml: true,
                            version: 'v15.0'
                        });
                    };
                    (function (d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0];
                        if (d.getElementById(id)) return;
                        js = d.createElement(s);
                        js.id = id;
                        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));
                }
            }, false);
        });
    </script>
    <?php
}

//   add_shortcode('add_facebook_script_like_share', 'add_facebook_script_like_share');
function add_facebook_script_like_share()
{
    global $with, $height;
    if (!wp_is_mobile() && is_single()) {
        $with = 370;
    } else {
        $with = '';
    }
    ?>
    <?php if (is_single() || is_category()) : ?>
    <div class="block-h heading-layout-5 heading-facebook-like-page" style="display: none">
        <div class="heading-inner">
            <h4 class="heading-title" style="margin-bottom: 15px;"><span>Follow Us on Socials</span></h4></div>
    </div>
<?php endif; ?>
    <?php if (is_single() || is_category()) : ?>
    <div class="elementor-element elementor-widget elementor-widget-shortcode content-facebook-like-page"
    style="text-align: center;margin-bottom:10px;display: none">
    <div class="elementor-widget-container">
<?php endif; ?>
    <div id="fb-root"></div>
    <div class="fb-page" data-href="https://www.facebook.com/itsmeit.biz" data-tabs="events"
         data-width="<?= $with ?>" data-height="" data-small-header="false"
         data-adapt-container-width="true" data-hide-cover="false"
         data-show-facepile="true">
        <blockquote cite="https://www.facebook.com/itsmeit.biz" class="fb-xfbml-parse-ignore">
            <a href="https://www.facebook.com/itsmeit.biz">itsmeit.biz</a></blockquote>
    </div>
    <?php if (is_single() || is_category()) : ?>
    </div>
    </div>
<?php endif;
}

/**
 * Google analytics
 */
add_action('wp_head', 'add_google_analytics_script_head');
function add_google_analytics_script_head()
{
    $manager = current_user_can('manage_options');
    $IP = array('127.0.0.1', '116.101.118.75');
    if (!$manager && !in_array($_SERVER['REMOTE_ADDR'], $IP) && $_SERVER['HTTP_HOST'] != 'localhost') {
        ?>
        <!-- Google tag (gtag.js) -->
        <script>
            window.addEventListener('load', function () {
                var is_analytics_load = 0;
                window.addEventListener('scroll', function () {
                    if (is_analytics_load == 0) {
                        is_analytics_load = 1;

                        window.dataLayer = window.dataLayer || [];

                        function gtag() {dataLayer.push(arguments);}

                        gtag('js', new Date());
                        gtag('config', 'G-YKVNLMPXLB');

                        var ele = document.createElement('script');
                        ele.async = true;
                        ele.defer = true;
                        ele.src = 'https://www.googletagmanager.com/gtag/js?id=G-YKVNLMPXLB';
                        var sc = document.getElementsByTagName('script')[0];
                        sc.parentNode.insertBefore(ele, sc);
                    }
                }, false);
            });
        </script>
        <?php
    }
}

//   add_filter( 'the_content', 'add_fb_comment_after_content' );
function add_fb_comment_after_content($content) {
    global $post;
    if (is_single() && $post->ID == '14049') {
        ?>
        <div id="fb-root"></div>
        <script>
            window.addEventListener('load', function () {
                var is_analytics_load = 0;
                window.addEventListener('scroll', function () {
                    if (is_analytics_load == 0) {
                        is_analytics_load = 1;

                        var ele = document.createElement('script');
                        ele.async = true;
                        ele.defer = true;
                        ele.crossorigin = 'anonymous';
                        ele.nonce = 'LeGWcQZO';
                        ele.src = 'https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v15.0&appId=1019721838751823&autoLogAppEvents=1';
                        var sc = document.getElementsByTagName('script')[0];
                        sc.parentNode.insertBefore(ele, sc);
                    }
                }, false);
            });
        </script>
        <?php
        $htmlAfterContent = '<div class="fb-comments" data-href="'. get_permalink($post->ID) .'" data-width="100%" data-numposts="5"></div>';
        return $content.$htmlAfterContent;
    }
    return $content ;
}
   