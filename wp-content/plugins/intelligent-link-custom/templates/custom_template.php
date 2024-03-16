<?php
/**
 * @author     itsmeit <buivanloi.2010@gmail.com>
 * Website     https://itsmeit.co/
 */

$prep_title         = isset($_COOKIE['prep_title']) ? $_COOKIE['prep_title'] : '';
$prep_request       = isset($_COOKIE['prep_request']) ? $_COOKIE['prep_request'] : '';

$settings           = get_option('preplink_setting');
$ads                = get_option('ads_code');
$endpoint_conf      = get_option('preplink_endpoint');
$meta_attr          = get_option('meta_attr');
$isMeta             = false;
$post_id            = get_the_ID();
$view_link          = get_permalink($post_id);
$post_title         = get_the_title($post_id) ? get_the_title($post_id) : $prep_title;

$baseUrl            = str_replace('https://', '', get_bloginfo('url'));
$file_format        = get_post_meta($post_id, 'file_format', true);
$require            = get_post_meta($post_id, 'require', true);
$os_version         = get_post_meta($post_id, 'os_version', true);
$file_version       = get_post_meta($post_id, 'file_version', true);
$file_name          = get_post_meta($post_id, 'file_name', true);
$download_meta      = base64_encode(get_bloginfo('url'));
$time_conf          = !empty($endpoint_conf['countdown_endpoint']) ? (int) $endpoint_conf['countdown_endpoint'] : 15;
$post_image         = !empty($endpoint_conf['preplink_image'] ? true: false);
$link_no_login      = get_post_meta($post_id, 'link_no_login', true);
$link_is_login      = get_post_meta($post_id, 'link_is_login', true);
$file_size          = get_post_meta($post_id, 'file_size', true);
$current_language   = pll_current_language();

if ($download_meta === $prep_request) {
    $isMeta = true;
}

set_no_index_page();

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

?>
<?php if (!empty($settings['preplink_custom_style'])) {
    echo "<style>{$settings['preplink_custom_style']}</style>";
} ?>

<?php file_exists(get_template_directory() . '/header.php') ? get_header() : wp_head(); ?>
<div class="igl-single-page" id="prep-request-page" data-request="<?= esc_attr($prep_request) ?>">
    <?= !empty($ads['ads_1']) && aicp_can_see_ads() && is_allow_show_ads_dl() ? '<div class="preplink-ads preplink-ads-1">' . $ads['ads_1'] . '</div>' : '' ?>
    <?php render_back_icon($view_link); ?>
    <header class="igl-header">
        <h1 class="s-title">
            <?php if ($isMeta) echo '<a class="a-title" href="'.esc_url($view_link).'">'.esc_html($post_title).'</a>' ?>
        </h1>
    </header>
    <div class="sv-small-container">
        <div class="prep-link-container">
            <div class="prep-content">
                <?php if (empty($prep_request) || empty($prep_title)) : ?>
                    <div class="session-expired">
                        <p><?= __('Your session has ended, please click', 'intelligent-link')?>&nbsp;<a class="session-end" href="<?= $view_link ?>"><span><?= __('here', 'intelligent-link')?></span></a>&nbsp;<?= __('and do it again.', 'intelligent-link')?></p>
                        <p><?= __('If the issue persists, please try clearing cookies or attempting with a different browser.', 'intelligent-link') ?></p>
                    </div>
                    <?= !empty($ads['ads_7']) && aicp_can_see_ads() && is_allow_show_ads_dl() ? '<div class="preplink-ads preplink-ads-2">' . $ads['ads_7'] . '</div>' : '' ?>
                <?php else: ?>
                    <?php if ($post_image && $isMeta && has_post_thumbnail()) : ?>
                        <div class="ilgl-feat-outer">
                            <div class="featured-image">
                                <img src="<?= get_the_post_thumbnail_url($post_id, 'full'); ?>" class="prep-thumbnail" alt="<?= $post_title ?>" title="<?= $post_title ?>">
                            </div>
                        </div>
                        <?= !empty($ads['ads_2']) && aicp_can_see_ads() && is_allow_show_ads_dl() ? '<div class="preplink-ads preplink-ads-2">' . $ads['ads_2'] . '</div>' : '' ?>
                    <?php endif;?>

                    <?php if (!empty($endpoint_conf['ep_mode'])&& $endpoint_conf['ep_mode'] == 'default' && $isMeta): ?>
                        <div class="download-list">
                            <div class="download-item-box">
                                <div class="download-item">
                                    <div class="left">
                                        <a class="a-title image" href="javascript:void(0)">
                                            <?= has_post_thumbnail() ? get_the_post_thumbnail($post_id, 'thumbnail') : '<img src="'. esc_url(plugin_dir_url(__DIR__) . 'images/check_icon.png').'"/>'; ?>
                                        </a>
                                        <div class="post-download">
                                            <p class="title prep-title"><?= $isMeta ? ($file_name) : $prep_title; ?></p>
                                            <p class="post-date"><?= __('Update:', 'intelligent-link') . ' ' . get_the_modified_date('d/m/Y') ?: get_the_date('d/m/Y'); ?></p>
                                        </div>
                                    </div>
                                    <div class="right">
                                        <div class="prep-link-download-btn">
                                            <div class="clickable prep-link-btn">
                                                <svg class="icon" fill="currentColor"
                                                     xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                     viewBox="0 0 24 24">
                                                    <path d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="endpoint-progress" id="endpoint-progress" style="display:none;">
                            <p class="counter">0%</p>
                            <div class="bar"></div>
                            <span class="prep-btn-download" style="display: none">
                            <svg class="icon" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path d="M504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-143.6-28.9L288 302.6V120c0-13.3-10.7-24-24-24h-16c-13.3 0-24 10.7-24 24v182.6l-72.4-75.5c-9.3-9.7-24.8-9.9-34.3-.4l-10.9 11c-9.4 9.4-9.4 24.6 0 33.9L239 404.3c9.4 9.4 24.6 9.4 33.9 0l132.7-132.7c9.4-9.4 9.4-24.6 0-33.9l-10.9-11c-9.5-9.5-25-9.3-34.3.4z"></path>
                            </svg>
                            <span class="text-down"><?= __('Download', 'intelligent-link');?></span>
                        </span>
                        </div>

                        <p class="download-success" style="color: rgb(36, 110, 17); padding: 5px 10px; border: 1px solid #dcdde7; border-radius: 5px; margin-bottom: 20px;display: none">
                            <?php if ($current_language === 'en') : ?>
                                You have successfully redeemed points. The file has been downloaded. Please check your email (including both your inbox and spam folder) for further information.
                            <?php else: ?>
                                Bạn vừa đổi điểm thành công, tệp tin đã được tải xuống, vui lòng kiểm tra email (bao gồm cả hộp thư đến hoặc các hộp thư spam) để nhận thêm thông tin.
                            <?php endif;?>
                        </p>
                        <div class="list-file-hide list-server-download" style="display: none">
                            <div class="ilgl-file-timer-btn">
                                <?php link_member_render($isMeta, $link_is_login, $link_no_login, $prep_request, $file_name, $file_size, $prep_title, $post_id, $meta_attr); ?>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="keyword-search">
                            <p><?= !empty($endpoint_conf['redirect_notice']) ? $endpoint_conf['redirect_notice']: ''?></p>
                        </div>
                        <div class="list-file-hide" id="buttondw">
                            <div class="ilgl-file-timer" style="display:none;">
                                <span class="ilgl-file-timer-sec fw-b" id="preplink-timer-link" data-time="<?= $time_conf ?>"><?= $time_conf ?></span>
                                <?php svg_render() ?>
                            </div>
                            <div class="ilgl-file-timer-btn" style="opacity:0;pointer-events:none;visibility:hidden;">
                                <?php link_member_render($isMeta, $link_is_login, $link_no_login, $prep_request, $file_name, $file_size, $prep_title, $post_id, $meta_attr); ?>
                            </div>
                        </div>
                    <?php endif;?>

                    <?= !empty($ads['ads_3']) && aicp_can_see_ads() && is_allow_show_ads_dl() ? '<div class="preplink-ads preplink-ads-3">' . $ads['ads_3'] . '</div>' : '' ?>

                    <?php $faq_conf = get_option('preplink_faq', []);if (!empty($faq_conf['faq_enabled']) && $faq_conf['faq_enabled'] == 1) : ?>
                        <?php if ($current_language === 'en') : ?>
                        <?php faq_render(); else: ?>
                        <style>
                            .accordion .accordion-item{border-bottom:1px solid #e5e5e5}.accordion .accordion-item button[aria-expanded=true]{border-bottom:1px solid #03b5d2}
                            .accordion button{position:relative;display:block;text-align:left;width:100%;padding:8px 0!important;color:#0c1521!important;font-size:16px;font-weight:400;border:none!important;background:0 0!important;outline:0;box-shadow:none!important;line-height:20px!important}
                            .accordion button:focus,.accordion button:hover{cursor:pointer;color:#03b5d2}.accordion button:focus::after,.accordion button:hover::after{cursor:pointer;color:#03b5d2;border:1px solid #03b5d2}
                            .accordion button .accordion-title{padding:7px 0}.accordion button .icon{display:inline-block;position:absolute;top:8px;right:0;width:20px;height:20px;border:1px solid;border-radius:20px}
                            .accordion button .icon::after,.accordion button .icon::before{display:block;position:absolute;content:'';background:currentColor}.accordion button .icon::before{top:8px;left:4px;width:10px;height:2px}
                            .accordion button .icon::after{top:5px;left:8px;width:2px;height:8px}.accordion button[aria-expanded=true]{color:#03b5d2}.accordion button[aria-expanded=true] .icon::after{width:0}
                            .accordion button[aria-expanded=true]+.accordion-content{opacity:1;max-height:100%;transition:.2s linear;will-change:opacity,max-height}
                            .accordion .accordion-content{opacity:0;max-height:0;overflow:hidden;transition:opacity .2s linear,max-height .2s linear;will-change:opacity,max-height}
                            .accordion .accordion-content p{font-size:1rem;font-weight:300;margin:10px 0;color:#010807}
                        </style>
                        <div class="faq-download">
                            <h3 class="faq-title">Các câu hỏi thường gặp?</h3>
                            <!--Frequently Asked Questions-->
                            <div class="accordion">
                                <div class="accordion-item">
                                    <button id="accordion-button-3" aria-expanded="false">
                                        <span class="accordion-title">Làm cách nào để tải xuống một tập tin?</span>
                                        <span class="icon" aria-hidden="true"></span>
                                    </button>
                                    <div class="accordion-content">
                                        <p>
                                            Chúng tôi ngăn chặn BOT spam, bạn cần xác nhận rằng bạn không phải là robot để có được liên kết.
                                            Sau khi xác nhận, bạn chỉ cần nhấp vào nút tải xuống và đợi vài giây để nó xuất hiện.
                                        </p>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <button id="accordion-button-1" aria-expanded="false">
                                        <span class="accordion-title">Làm cách nào để cài đặt?</span>
                                        <span class="icon" aria-hidden="true"></span>
                                    </button>
                                    <div class="accordion-content">
                                        <p>
                                            Mỗi ứng dụng hoặc tệp cài đặt đều có phương pháp cụ thể riêng. Chúng tôi cung cấp hướng dẫn cài đặt cụ thể cho từng phiên bản trong bài viết.
                                        </p>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <button id="accordion-button-2" aria-expanded="false">
                                        <span class="accordion-title">Làm thế nào để lấy mật khẩu giải nén?</span>
                                        <span class="icon" aria-hidden="true"></span>
                                    </button>
                                    <div class="accordion-content">
                                        <p>
                                            Chúng tôi đã bảo vệ tập tin bằng mật khẩu. Mật khẩu giải nén đã được đính kèm; vui lòng kiểm tra nó trong tệp zip.
                                        </p>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <button id="accordion-button-4" aria-expanded="false">
                                        <span class="accordion-title">File download có an toàn không?</span>
                                        <span class="icon" aria-hidden="true"></span>
                                    </button>
                                    <div class="accordion-content">
                                        <p>
                                            Mỗi bài đăng trên nền tảng của chúng tôi đều bao gồm hình ảnh cài đặt và hướng dẫn sử dụng.
                                            Tất cả các tệp đều trải qua quá trình kiểm tra kỹ lưỡng trên trang web trước khi được chia sẻ và được quét virus.
                                        </p>
                                    </div>
                                </div>
                                <div class="accordion-item">
                                    <button id="accordion-button-5" aria-expanded="false">
                                        <span class="accordion-title">Báo cáo lỗi hoặc yêu cầu cập nhật?</span>
                                        <span class="icon" aria-hidden="true"></span>
                                    </button>
                                    <div class="accordion-content">
                                        <p>
                                            Trong quá trình sử dụng nếu có vấn đề link tải hỏng, phiên bản cũ, yêu cầu cập nhật,… Bạn có thể gửi email cho chúng tôi trong phần liên hệ.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            jQuery(document).ready(function ($) {
                                if ($('.faq-download').length) {
                                    const items = $('.accordion button');
                                    function toggleAccordion() {
                                        const itemToggle = $(this).attr('aria-expanded');
                                        items.attr('aria-expanded', 'false');
                                        items.removeClass('faq-active');
                                        if (itemToggle == 'false') {
                                            $(this).addClass('faq-active');
                                            $(this).attr('aria-expanded', 'true');
                                            $(this).next('.content').slideDown(1000);
                                        } else {
                                            $(this).next('.content').slideUp(1000);
                                        }
                                    }
                                    items.click(toggleAccordion);
                                }
                            });
                        </script>
                        <?php endif;?>

                        <?= !empty($ads['ads_4']) && aicp_can_see_ads() && is_allow_show_ads_dl() ? '<div class="preplink-ads preplink-ads-4">' . $ads['ads_4'] . '</div>' : '' ?>
                    <?php endif; ?>

                    <div class="keyword-search">
                        <p>
                            <?= __('To search for a specific resource or content on the internet, you can visit', 'intelligent-link')?>
                            <a target="_blank" href="//www.google.com/search?q=<?=$prep_title.' '.$baseUrl?>"><?= __('google.com', 'intelligent-link')?></a>
                            <?= __('and enter your search query as:', 'intelligent-link')?>
                            <a target="_blank" href="//www.google.com/search?q=<?=$prep_title.' '.$baseUrl?>"><?= __('keyword +', 'intelligent-link') . ' '. $baseUrl?></a>
                        </p>
                    </div>

                    <?= !empty($ads['ads_5']) && aicp_can_see_ads() && is_allow_show_ads_dl() ? '<div class="preplink-ads preplink-ads-5">' . $ads['ads_5'] . '</div>' : '' ?>

                    <?php if ($isMeta && !empty($endpoint_conf['preplink_related_post']) && $endpoint_conf['preplink_related_post'] == 1): ?>
                        <?php ep_related_post($settings, $post_id) ?>
                        <?= !empty($ads['ads_6']) && aicp_can_see_ads() && is_allow_show_ads_dl() ? '<div class="preplink-ads preplink-ads-6">' . $ads['ads_6'] . '</div>' : '' ?>
                    <?php endif; ?>

                    <?php if (file_exists(get_template_directory() . '/comments.php') && !empty($endpoint_conf['preplink_comment']) && (int)$endpoint_conf['preplink_comment'] == 1 && $isMeta) { ?>
                        <div class="comment"><?php comments_template(); ?></div>
                    <?php } ?>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php if (file_exists(get_template_directory() . '/footer.php')) get_footer(); ?>
