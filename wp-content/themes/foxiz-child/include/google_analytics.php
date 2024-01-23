<?php

add_action('wp_head', 'add_script_ads_analytics');
add_action('wp_head', 'add_google_adsense_script');
add_action('wp_footer', 'add_facebook_script_footer');

function add_google_adsense_script(){
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { ?>
        <script type="application/javascript">
            window.addEventListener('load', function () {
                var is_adsense_load   = 0;
                window.addEventListener('scroll', function () {
                    if (is_adsense_load == 0) {
                        is_adsense_load = 1;
                        var adsenseElm = document.createElement('script');
                        adsenseElm.async = true;
                        adsenseElm.crossorigin = 'anonymous';
                        adsenseElm.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-8326801375483582'
                        var adsense_sc = document.getElementsByTagName('script')[0]
                        adsense_sc.parentNode.insertBefore(adsenseElm, adsense_sc);
                    }
                });
            });
        </script>
        <?php } ?>
    <?php include_once get_theme_file_path('include/adsense_shortcode.php');
}

function add_script_ads_analytics() {
    $manager = current_user_can('manage_options');
    $IP = array('127.0.0.1', 'localhost');

    if (!$manager && !in_array($_SERVER['REMOTE_ADDR'], $IP) && $_SERVER['HTTP_HOST'] != 'localhost') {
        ?>
        <script type="application/javascript">
            window.addEventListener('load', function () {
                var is_analytics_load = 0;
                window.addEventListener('scroll', function () {
                    if (is_analytics_load == 0) {
                        is_analytics_load = 1;

                        window.dataLayer = window.dataLayer || [];
                        function gtag(){dataLayer.push(arguments);}
                        gtag('js', new Date());
                        gtag('config', 'G-GSGH6TMJMT');

                        var analyticsElm = document.createElement('script');
                        analyticsElm.src = 'https://www.googletagmanager.com/gtag/js?id=G-GSGH6TMJMT';
                        var analytics_sc = document.getElementsByTagName('script')[0];
                        analytics_sc.parentNode.insertBefore(analyticsElm, analytics_sc);
                    }
                });
            });
        </script>
        <?php
    }
}

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
                        js.src = 'https://connect.facebook.net/en/sdk/xfbml.customerchat.js';
                        fjs.parentNode.insertBefore(js, fjs);
                    }(document, 'script', 'facebook-jssdk'));
                }
            }, false);
        });
    </script>
    <?php
}

function isBotDetected() {
    if ( preg_match('/abacho|accona|AddThis|AdsBot|ahoy|AhrefsBot|AISearchBot|alexa|altavista|anthill|appie|applebot|arale|araneo|AraybOt|ariadne|arks|aspseek|ATN_Worldwide|Atomz|baiduspider|baidu|bbot|bingbot|bing|Bjaaland|BlackWidow|BotLink|bot|boxseabot|bspider|calif|CCBot|ChinaClaw|christcrawler|CMC\/0\.01|combine|confuzzledbot|contaxe|CoolBot|cosmos|crawler|crawlpaper|crawl|curl|cusco|cyberspyder|cydralspider|dataprovider|digger|DIIbot|DotBot|downloadexpress|DragonBot|DuckDuckBot|dwcp|EasouSpider|ebiness|ecollector|elfinbot|esculapio|ESI|esther|eStyle|Ezooms|facebookexternalhit|facebook|facebot|fastcrawler|FatBot|FDSE|FELIX IDE|fetch|fido|find|Firefly|fouineur|Freecrawl|froogle|gammaSpider|gazz|gcreep|geona|Getterrobo-Plus|get|girafabot|golem|googlebot|\-google|grabber|GrabNet|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|HTTrack|ia_archiver|iajabot|IDBot|Informant|InfoSeek|InfoSpiders|INGRID\/0\.1|inktomi|inspectorwww|Internet Cruiser Robot|irobot|Iron33|JBot|jcrawler|Jeeves|jobo|KDD\-Explorer|KIT\-Fireball|ko_yappo_robot|label\-grabber|larbin|legs|libwww-perl|linkedin|Linkidator|linkwalker|Lockon|logo_gif_crawler|Lycos|m2e|majesticsEO|marvin|mattie|mediafox|mediapartners|MerzScope|MindCrawler|MJ12bot|mod_pagespeed|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|NationalDirectory|naverbot|NEC\-MeshExplorer|NetcraftSurveyAgent|NetScoop|NetSeer|newscan\-online|nil|none|Nutch|ObjectsSearch|Occam|openstat.ru\/Bot|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pingdom|pinterest|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|rambler|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Scrubby|Search\-AU|searchprocess|search|SemrushBot|Senrigan|seznambot|Shagseeker|sharp\-info\-agent|sift|SimBot|Site Valet|SiteSucker|skymob|SLCrawler\/2\.0|slurp|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|spider|suke|tach_bw|TechBOT|TechnoratiSnoop|templeton|teoma|titin|topiclink|twitterbot|twitter|UdmSearch|Ukonline|UnwindFetchor|URL_Spider_SQL|urlck|urlresolver|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|wapspider|WebBandit\/1\.0|webcatcher|WebCopier|WebFindBot|WebLeacher|WebMechanic|WebMoose|webquest|webreaper|webspider|webs|WebWalker|WebZip|wget|whowhere|winona|wlm|WOLP|woriobot|WWWC|XGET|xing|yahoo|YandexBot|YandexMobileBot|yandex|yeti|Zeus/i', $_SERVER['HTTP_USER_AGENT'])
    ) {
        return true;
    }
    return false;
}