<?php
/** Adsense sidebar */
add_shortcode('ads_sidebar_top', 'ads_sidebar_top');
function ads_sidebar_top(){
    if (is_allow_show_ads() && aicp_can_see_ads()) {
        ?>
        <div class="aicp">
            <!-- ads_sidebar_top -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-3109927831594907"
                     data-ad-slot="6368247041"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
        </div>
        <?php
    }
}

add_shortcode('post_side_bar_top', 'post_side_bar_top');
function post_side_bar_top(){
    if (is_allow_show_ads() && aicp_can_see_ads()) {
        ?>
        <div class="aicp" style="margin-bottom:15px">
            <!-- post_side_bar_top -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-3109927831594907"
                 data-ad-slot="8952718798"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
        <?php
    }
}

/** Adsense sidebar_bottom */
add_shortcode('sidebar_bottom', 'sidebar_bottom');
function sidebar_bottom(){
    if (is_allow_show_ads() && aicp_can_see_ads()) {
        ?>
        <div class="aicp">
        <!-- sidebar_bottom -->
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="ca-pub-3109927831594907"
                 data-ad-slot="2549192351"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        <?php
    }
}

/** Adsense advertisement_top_left */
add_shortcode('advertisement_top_left', 'advertisement_top_left');
function advertisement_top_left(){
    if (is_allow_show_ads() && aicp_can_see_ads()) {
        ?>
        <div class="aicp" style="max-width: 610px; max-height: 180px">
            <!-- Ezoic - advertisement_top_left - incontent_5 -->
            <div id="ezoic-pub-ad-placeholder-168"></div>
            <!-- End Ezoic - advertisement_top_left - incontent_5 -->
        </div>
        <?php
    }
}

/** Adsense advertisement_after_more_latest_news */
add_shortcode('advertisement_after_more_latest_news', 'advertisement_after_more_latest_news');
function advertisement_after_more_latest_news() {
    if (is_allow_show_ads() && aicp_can_see_ads()) {
        ?>
        <div class="aicp">
            <!-- advertisement_after_more_latest_news -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-3109927831594907"
                     data-ad-slot="2965044539"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
        </div>
        <?php
    }
}

/** Adsense advertisement_top_left */
add_shortcode('advertisement_after_more_latest_news_2', 'advertisement_after_more_latest_news_2');
function advertisement_after_more_latest_news_2(){
    if (is_allow_show_ads() && aicp_can_see_ads()) {
        ?>
        <div class="aicp">
            <!-- advertisement_after_more_latest_news_2 -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-3109927831594907"
                     data-ad-slot="2163098755"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
        </div>
        <?php
    }
}

/** Adsense advertisement_after_more_latest_news_bottom width:1200px;height:90px */
add_shortcode('advertisement_after_more_latest_news_bottom', 'advertisement_after_more_latest_news_bottom');
function advertisement_after_more_latest_news_bottom(){
    if (is_allow_show_ads() && aicp_can_see_ads()) {
        ?>
        <div class="aicp">
            <!-- advertisement_after_more_latest_news_bottom -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-3109927831594907"
                     data-ad-slot="3284608737"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
        </div>
        <?php
    }
}

/** Adsense advertisement_home_must_read_after width:1200px;height:90px */
add_shortcode('advertisement_home_must_read_after', 'advertisement_home_must_read_after');
function advertisement_home_must_read_after(){
    if (is_allow_show_ads() && aicp_can_see_ads()) {
        ?>
        <div class="aicp">
            <!-- advertisement_home_must_read_after -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-3109927831594907"
                     data-ad-slot="3093037049"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
        </div>
        <?php
    }
}

/** Adsense advertisement_home_more_latest_news width:1200px;height:120px */
add_shortcode('advertisement_home_more_latest_news', 'advertisement_home_more_latest_news');
function advertisement_home_more_latest_news(){
    if (is_allow_show_ads() && aicp_can_see_ads()) {
        ?>
        <div class="aicp">
            <!-- advertisement_home_more_latest_news -->
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-3109927831594907"
                     data-ad-slot="9466873709"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
        </div>
        <?php
    }
}

