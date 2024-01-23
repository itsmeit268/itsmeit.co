<?php
/**
 * @link       https://itsmeit.co/tao-trang-chuyen-huong-link-download-wordpress.html
 * @author     itsmeit <itsmeit.biz@gmail.com>
 * Website     https://itsmeit.co
 */
?>

<?php
$post_id            = get_the_ID();
$view_link          = get_permalink($post_id);
$settings           = get_option('preplink_setting');
$advertising        = get_option('preplink_advertising');
$faqSetting         = get_option('preplink_faq');
$endpointSetting    = get_option('preplink_endpoint');
$post_title         = get_the_title($post_id) ? get_the_title($post_id) : get_post_field('post_title', $post_id);
$excerpt            = get_the_excerpt();
$prepLinkText       = isset($_COOKIE['prep_title']) ? $_COOKIE['prep_title'] : '';
$prepLinkURL        = isset($_COOKIE['prep_request']) ? $_COOKIE['prep_request'] : '';
$baseUrl            = str_replace('https://', '', !empty(home_url()) ? home_url() : get_bloginfo('url'));
$time_conf          = !empty($endpointSetting['countdown_endpoint']) ? (int) $endpointSetting['countdown_endpoint'] : 15;

$os                 = get_post_meta($post_id, 'os', true);
$version            = get_post_meta($post_id, 'version', true);
$link_no_login      = get_post_meta($post_id, 'link_no_login', true);
$link_is_login      = get_post_meta($post_id, 'link_is_login', true);
$os_version         = get_post_meta($post_id, 'os_version', true);
$app_file_name      = get_post_meta($post_id, 'app_file_name', true);
$app_file_size      = get_post_meta($post_id, 'app_file_size', true);

add_action('wp_print_scripts', function () {
    wp_dequeue_script('fixedtoc-js');
    wp_dequeue_script('fixedtoc-js-js-extra');
    wp_dequeue_script( 'enlighterjs' );
    wp_dequeue_style('enlighterjs');
});

function remove_enlighterjs_script() {
    wp_dequeue_script('fixedtoc-js');
    wp_dequeue_script('fixedtoc-js-js-extra');
    wp_dequeue_script( 'enlighterjs' );
    wp_dequeue_style('enlighterjs');
}
add_action( 'wp_enqueue_scripts', 'remove_enlighterjs_script', 10 );

if (!empty($settings['preplink_custom_style'])) {
    echo "<style>{$settings['preplink_custom_style']}</style>";
}
?>
<?php if (file_exists(get_template_directory() . '/header.php')) get_header(); ?>

<div class="single-page without-sidebar sticky-sidebar" id="prep-link-single-page" data-url="<?= $prepLinkURL ?>"  style="max-width: 890px; margin: 0 auto;">
    <div class="p-file">
        <div class="section">
            <div class="p-file-back">
                <a href="<?= esc_attr($view_link)?>"><svg width="48" height="20"><use xlink:href="#i__back"></use></svg></a>
            </div>
            <h1 class="p-file-title title"><?= esc_html($post_title);?></h1>

            <div class="p-file-cont section">
                <?php if (empty($prepLinkURL) || empty($prepLinkText)) : ?>
                    <?php if (isset($advertising['pr_ad_6']) && (int)$advertising['pr_ad_6'] == 1 && !empty($advertising['pr_ad_code_6'])): ?>
                        <div class="preplink-ads preplink-ads-6" style="margin: 0 25px;">
                            <?= $advertising['pr_ad_code_6'] ?>
                        </div>
                    <?php endif; ?>
                    <div class="session-expired">
                        <p><?= __('Your session has ended, please click', 'prep-link')?>&nbsp;<a href="<?= $view_link ?>"><span style="color: #0a4ad0;"><?= __('here', 'prep-link')?></span></a>&nbsp;<?= __('and do it again.', 'prep-link')?></p>
                        <p><?= __('If the issue persists, try clearing your cookies or browser history and attempt again.', 'prep-link') ?></p>
                    </div>
                    <?php if ( isset($advertising['pr_ad_7']) && (int)$advertising['pr_ad_7'] == 1 && !empty($advertising['pr_ad_code_7'])): ?>
                        <div class="preplink-ads preplink-ads-7">
                            <?= $advertising['pr_ad_code_7'] ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>

                    <?php if ( isset($advertising['preplink_advertising_1']) && (int)$advertising['preplink_advertising_1'] == 1 && !empty($advertising['preplink_advertising_code_1'])): ?>
                        <div class="preplink-ads preplink-ads-1">
                            <?= $advertising['preplink_advertising_code_1'] ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!$os_version && !$os && !function_exists('savvymobi_get_app_image') && has_post_thumbnail()):?>
                        <div class="s-feat-outer">
                            <div class="featured-image">
                                <img src="<?= get_the_post_thumbnail_url($post_id, 'full'); ?>" class="prep-thumbnail" alt="<?= $post_title ?>" title="<?= $post_title ?>">
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="p-file-data section">
                            <div class="p-file-data-l">
                                <i class="c-svg"><svg width="24" height="24"><use xlink:href="#i__android"></use></svg></i>
                                <span class="c-blue fw-b"><?= esc_html__($os); ?></span>
                                <span> <?= esc_html($os_version)?></span>
                            </div>
                            <div class="page_file-img">
                                <?= function_exists('savvymobi_get_app_image') ? savvymobi_get_app_image($post_id, 116, 116): ''; ?>
                            </div>
                            <div class="p-file-data-r">
                                <i class="c-svg">
                                    <svg class="bi bi-phone-flip" fill="currentColor" width="20" height="20" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11 1H5a1 1 0 0 0-1 1v6a.5.5 0 0 1-1 0V2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v6a.5.5 0 0 1-1 0V2a1 1 0 0 0-1-1Zm1 13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-2a.5.5 0 0 0-1 0v2a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-2a.5.5 0 0 0-1 0v2ZM1.713 7.954a.5.5 0 1 0-.419-.908c-.347.16-.654.348-.882.57C.184 7.842 0 8.139 0 8.5c0 .546.408.94.823 1.201.44.278 1.043.51 1.745.696C3.978 10.773 5.898 11 8 11c.099 0 .197 0 .294-.002l-1.148 1.148a.5.5 0 0 0 .708.708l2-2a.5.5 0 0 0 0-.708l-2-2a.5.5 0 1 0-.708.708l1.145 1.144L8 10c-2.04 0-3.87-.221-5.174-.569-.656-.175-1.151-.374-1.47-.575C1.012 8.639 1 8.506 1 8.5c0-.003 0-.059.112-.17.115-.112.31-.242.6-.376Zm12.993-.908a.5.5 0 0 0-.419.908c.292.134.486.264.6.377.113.11.113.166.113.169 0 .003 0 .065-.13.187-.132.122-.352.26-.677.4-.645.28-1.596.523-2.763.687a.5.5 0 0 0 .14.99c1.212-.17 2.26-.43 3.02-.758.38-.164.713-.357.96-.587.246-.229.45-.537.45-.919 0-.362-.184-.66-.412-.883-.228-.223-.535-.411-.882-.571ZM7.5 2a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1h-1Z" fill-rule="evenodd"/>
                                    </svg>
                                </i>
                                <span class="c-blue fw-b">Version</span>
                                <span><?= esc_html($version)?></span>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if ( isset($advertising['preplink_advertising_2']) && (int)$advertising['preplink_advertising_2'] == 1 && !empty($advertising['preplink_advertising_code_2'])): ?>
                        <div class="preplink-ads preplink-ads-2">
                            <?= $advertising['preplink_advertising_code_2'] ?>
                        </div>
                    <?php endif; ?>

                    <div class="p-file-hide" id="buttondw">
                        <div class="p-file-timer" style="display:none;">
                            <span class="p-file-timer-sec fw-b" id="preplink-timer-link" data-time="<?= $time_conf ?>"><?= $time_conf ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 72" width="72" height="72">
                                <defs>
                                    <linearGradient id="timer-gradient" x1="-512.07" y1="-2048.22" x2="-511.51" y2="-2048.45" gradientTransform="matrix(64, 0, 0, -64, 32808.44, -131050.69)" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#656ED6" stop-opacity="0"></stop>
                                        <stop offset="1" stop-color="#39C1E0"></stop>
                                    </linearGradient>
                                    <linearGradient id="timer-gradient-2" x1="-511.51" y1="-2048.25" x2="-512.51" y2="-2048.25" gradientTransform="matrix(64, 0, 0, -64, 32804.44, -131055.69)" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#C867F4"></stop>
                                        <stop offset="1" stop-color="#E53D2D" stop-opacity="0"></stop>
                                    </linearGradient>
                                    <linearGradient id="timer-gradient-3" x1="-511.51" y1="-2048.25" x2="-512.51" y2="-2048.25" gradientTransform="matrix(64, 0, 0, -64, 32800.44, -131047.69)" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="#FC7352" stop-opacity="0"></stop>
                                        <stop offset="1" stop-color="#F0B835"></stop>
                                    </linearGradient>
                                </defs>
                                <circle fill="url(#timer-gradient)" cx="40" cy="37" r="32"></circle>
                                <circle fill="url(#timer-gradient-2)" cx="36" cy="32" r="32"></circle>
                                <circle fill="url(#timer-gradient-3)" cx="32" cy="40" r="32"></circle>
                            </svg>
                        </div>
                        <div class="p-file-timer-btn" style="opacity:0;pointer-events:none;visibility:hidden;">
                            <a class="btn blue-style preplink-btn-link">
                                <?= $app_file_name && $app_file_size ? $app_file_name.' '.$app_file_size : $prepLinkText; ?>
                            </a>
                            <?php
                            $link_download_data = get_post_meta($post_id, 'link-download-metabox', true);
                            $total = (int) $settings['preplink_number_field_lists']? : 5;

                            if (!empty($link_download_data)) {
                                for ($i = 1; $i <= $total; $i++) {
                                    $file_name_key = 'file_name-' . $i;
                                    $link_no_login_key = 'link_no_login-' . $i;
                                    $link_is_login_key = 'link_is_login-' . $i;
                                    $size_key = 'size-' . $i;

                                    if (isset($link_download_data[$link_no_login_key])) {
                                        $file_name = $link_download_data[$file_name_key];
                                        $link_no_login = $link_download_data[$link_no_login_key];
                                        $link_is_login = $link_download_data[$link_is_login_key];
                                        $size = $link_download_data[$size_key];

                                        if ($link_no_login) {
                                            ?>
                                            <a class="btn blue-style preplink-btn-link" data-url="<?= esc_html(base64_encode($link_no_login))?>">
                                                <?= esc_html($file_name . ' ' . $size) ?>
                                            </a>
                                            <?php
                                        }
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <?php if ( isset($advertising['preplink_advertising_3']) && (int)$advertising['preplink_advertising_3'] == 1 && !empty($advertising['preplink_advertising_code_3'])): ?>
                        <div class="preplink-ads preplink-ads-3">
                            <?= $advertising['preplink_advertising_code_3'] ?>
                        </div>
                    <?php endif; ?>
                <?php endif;?>
            </div>

            <?php if (!empty($prepLinkURL) || !empty($prepLinkText) && isset($faqSetting['faq_enabled']) &&
                $faqSetting['faq_enabled'] == 1 && isset($faqSetting['faq_description'])) : ?>
                <div class="faq-download">
                    <h3 class="faq-title"><?= !empty($faqSetting['faq_title']) ? $faqSetting['faq_title'] : 'FAQ' ?></h3>
                    <?= $faqSetting['faq_description']; ?>
                </div>
                <?php if ( isset($advertising['preplink_advertising_4']) && (int)$advertising['preplink_advertising_4'] == 1 && !empty($advertising['preplink_advertising_code_4'])): ?>
                    <div class="preplink-ads preplink-ads-4">
                        <?= $advertising['preplink_advertising_code_4'] ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($prepLinkURL) || !empty($prepLinkText)) : ?>
            <div class="section section-newgames">
                <h3 class="section-title"><?= __('Recommended for you', 'prep-link')?></h3>
                <div class="list-app one-row">
                    <?php
                    $categories = get_the_category();
                    $category_ids = array();
                    foreach ($categories as $category) {
                        $category_ids[] = $category->term_id;
                    }

                    $args = array(
                        'category__in' => $category_ids,
                        'post__not_in' => array(get_the_ID()),
                        'posts_per_page' => !empty($settings['preplink_related_number']) ? $settings['preplink_related_number'] : 4, // Lấy 10 bài viết
                        'orderby' => 'rand',
                        'order' => 'DESC'
                    );

                    $related_posts = get_posts($args);
                    if ($related_posts) {
                        foreach ($related_posts as $post) {
                            setup_postdata($post); ?>
                            <div class="app">
                                <a class="app-cont" href="<?= get_permalink($post); ?>">
                                    <figure class="app-img">
                                        <?php
                                        if (function_exists('savvymobi_get_app_image')) {
                                            savvymobi_get_app_image($post_id, 116, 116);
                                        } else {
                                            if (has_post_thumbnail()){
                                                echo get_the_post_thumbnail($post, 'thumbnail');
                                            }
                                        }
                                        ?>
                                    </figure>
                                    <span class="app-title"><?= $post->post_title?></span>
                                </a>
                            </div>
                            <?php } ?>
                        <?php
                        wp_reset_postdata();
                    } ?>
                </div>
            </div>
            <?php if ( isset($advertising['pr_ad_5']) && (int)$advertising['pr_ad_5'] == 1 && !empty($advertising['pr_ad_code_5'])): ?>
                <div class="preplink-ads preplink-ads-5">
                    <?= $advertising['pr_ad_code_5'] ?>
                </div>
            <?php endif; ?>
        <?php
        if (file_exists(get_template_directory() . '/comments.php') && (int)$endpointSetting['preplink_comment'] == 1) { ?>
            <div class="comment"><?php comments_template(); ?></div>
        <?php } ?>
        <?php endif;?>
    </div>
    <svg aria-hidden="true" style="display:none;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <defs>
            <symbol id="i__back" viewBox="0 0 48 20">
                <path fill="currentColor" d="M46.27,9.24a1,1,0,0,0-1.41.1A10.71,10.71,0,0,1,36.78,13h-.16a10.78,10.78,0,0,1-8.1-3.66A12.83,12.83,0,0,0,18.85,5C14.94,5,10.73,7.42,6,12.4V5A1,1,0,0,0,4,5V15a1,1,0,0,0,1,1H15a1,1,0,0,0,0-2H7.24c4.42-4.71,8.22-7,11.62-7A10.71,10.71,0,0,1,27,10.66,12.81,12.81,0,0,0,36.61,15h.18a12.7,12.7,0,0,0,9.58-4.35A1,1,0,0,0,46.27,9.24Z"></path>
            </symbol>
        </defs>
    </svg>
<?php if (file_exists(get_template_directory() . '/footer.php')) get_footer(); ?>