<?php

/** Adsense advertisement_top_left */
add_shortcode('advertisement_top_left', 'advertisement_top_left');
function advertisement_top_left(){
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp">
            <?php if (!wp_is_mobile()) :?>
                <!-- Home top left -->
                <ins class="adsbygoogle"
                     style="display:inline-block;width:610px;height:180px"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="9742552366"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php else: ?>
                <!-- Home top left - mobile -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="1078181019"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php endif; ?>
        </div>
        <?php
    }
}

/** Adsense advertisement_after_more_latest_news*/
add_shortcode('advertisement_after_more_latest_news', 'advertisement_after_more_latest_news');
function advertisement_after_more_latest_news(){
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp">
            <?php if (!wp_is_mobile()) :?>
                <!-- Home After More Latest News -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="4994456865"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php else: ?>
                <!-- Home After More Latest News - mobile -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="7329014150"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php endif; ?>
        </div>
        <?php
    }
}

/** Adsense advertisement_after_more_latest_news_2*/
add_shortcode('advertisement_after_more_latest_news_2', 'advertisement_after_more_latest_news_2');
function advertisement_after_more_latest_news_2(){
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp">
            <?php if (!wp_is_mobile()) :?>
                <!-- Home After More Latest News 2 -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="9233084779"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php else: ?>
                <!-- Home After More Latest News 2 - mobile -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="4232077523"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php endif; ?>
        </div>
        <?php
    }
}

/** Adsense advertisement_after_more_latest_news_bottom*/
add_shortcode('advertisement_after_more_latest_news_bottom', 'advertisement_after_more_latest_news_bottom');
function advertisement_after_more_latest_news_bottom(){
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp" style="text-align: center;">
            <?php if (!wp_is_mobile()) :?>
                <!-- Home After More Latest News bottom -->
                <ins class="adsbygoogle"
                     style="display:inline-block;width:1200px;height:90px"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="1349929161"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php else: ?>
                <!-- Home After More Latest News bottom - mobile -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="4875037363"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php endif; ?>
        </div>
        <?php
    }
}

/** Adsense Must Read After */
add_shortcode('advertisement_home_must_read_after', 'advertisement_home_must_read_after');
function advertisement_home_must_read_after(){
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp" style="text-align: center;">
            <?php if (!wp_is_mobile()) :?>
                <!-- Home Must Read After -->
                <ins class="adsbygoogle"
                     style="display:inline-block;width:1200px;height:90px"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="2803407798"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php else:?>
                <!-- Home Must Read After - mobile -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="9889057873"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php endif; ?>
        </div>
        <?php
    }
}

/** Adsense More Latest News */
add_shortcode('advertisement_home_more_latest_news', 'advertisement_home_more_latest_news');
function advertisement_home_more_latest_news(){
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp" style="text-align: center;">
            <?php if (!wp_is_mobile()) :?>
                <!-- Home More Latest News -->
                <ins class="adsbygoogle"
                     style="display:inline-block;width:1200px;height:120px"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="6069819096"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php else: ?>
                <!-- Home More Latest News - mobile -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-8326801375483582"
                     data-ad-slot="5325667341"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            <?php endif; ?>
        </div>
        <?php
    }
}


/** Adsense sidebar */
add_shortcode('advertisement_sidebar', 'advertisement_sidebar');
function advertisement_sidebar(){
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp" style="margin-bottom: 15px">
            <!-- Sidebar top -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-8326801375483582"
                 data-ad-slot="9552648970"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        <?php
    }
}

add_shortcode('advertisement_sidebar_bottom', 'advertisement_sidebar_bottom');
function advertisement_sidebar_bottom(){
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp">
            <!-- Sidebar bottom -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-8326801375483582"
                 data-ad-slot="2578964948"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        <?php
    }
}

/** Adsense advertisement_category_slide */
add_shortcode('advertisement_category_slide', 'advertisement_category_slide');
function advertisement_category_slide(){
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp">
            <!-- Category -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:600px;height:170px"
                 data-ad-client="ca-pub-8326801375483582"
                 data-ad-slot="4977767385"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        <?php
    }
}

/** Adsense advertisement_category_sidebar */
add_shortcode('advertisement_category_sidebar', 'advertisement_category_sidebar');
function advertisement_category_sidebar(){
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp">
            <!-- Sidebar -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-8326801375483582"
                 data-ad-slot="6150414104"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        <?php
    }
}

/** Adsense advertisement_category_sidebar */
add_shortcode('advertisement_sidebar_after_home', 'advertisement_sidebar_after');
function advertisement_sidebar_after() {
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp">
            <!-- Sidebar after -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-8326801375483582"
                 data-ad-slot="9550355646"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        <?php
    }
}

/** Adsense advertisement_nguon_cap */
add_shortcode('advertisement_nguon_cap', 'advertisement_nguon_cap');
function advertisement_nguon_cap() {
    if (is_allow_show_ads() && aicp_can_see_ads() && free_level()) { // This part will show ads to your non-banned visitors
        ?>
        <div class="aicp">
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-format="autorelaxed"
                 data-ad-client="ca-pub-8326801375483582"
                 data-ad-slot="2763879343"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        <?php
    }
}