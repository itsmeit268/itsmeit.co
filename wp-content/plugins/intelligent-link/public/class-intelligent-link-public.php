<?php

/**
 * @author     itsmeit <buivanloi.2010@gmail.com>
 * Website     https://itsmeit.co/
 */

class Intelligent_Link_Public {

    public function __construct(){
        add_action('init', array($this, 'preplink_rewrite_endpoint'), 10, 0);
        add_action('wp_head', array($this, 'add_prep_custom_styles'), 10, 2);
        add_filter('the_content', array($this, 'render_meta_link_info'), 10);
        add_action('woocommerce_short_description', array($this,'render_meta_short_description'), 10);
        add_shortcode('link_shortcode', array($this,'link_shortcode_callback'));
    }

    public function enqueue_styles(){
        if (is_plugin_enable()){
            wp_enqueue_style('intelligent-link', plugin_dir_url(__FILE__) . 'css/intelligent-link'.(INTELLIGENT_LINK_DEV == 1 ? '': '.min').'.css', array(), INTELLIGENT_LINK_VERSION, 'all');
        }
    }

    public function enqueue_scripts() {
        if (is_plugin_enable()){
            wp_enqueue_script('wp-i18n', includes_url('/js/dist/i18n.js'), array('wp-element'), '1.0', true);
            wp_enqueue_script('intelligent-link', plugin_dir_url(__FILE__) . 'js/intelligent-link'.(INTELLIGENT_LINK_DEV == 1 ? '': '.min').'.js', array('jquery'), INTELLIGENT_LINK_VERSION, true);

            $href_vars = [];
            $href_vars = apply_filters('ilgl_href_vars', $href_vars);
            wp_localize_script('intelligent-link', 'href_vars', array_merge(
                [
                    'end_point'              => $this->endpoint_conf(),
                    'prep_url'               => $this->allow_domain(),
                    'pre_elm_exclude'        => $this->exclude_elm(),
                    'href_ex_elm'            => $this->href_ex_elm(),
                    'count_down'             => !empty(ilgl_settings()['preplink_countdown']) ? ilgl_settings()['preplink_countdown'] : 0,
                    'cookie_time'            => !empty(ilgl_settings()['cookie_time']) ? ilgl_settings()['cookie_time'] : 5,
                    'display_mode'           => !empty(ilgl_settings()['preplink_wait_text']) ? ilgl_settings()['preplink_wait_text'] : 'wait_time',
                    'wait_text'              => !empty(ilgl_settings()['wait_text_replace']) ? ilgl_settings()['wait_text_replace'] : 'please wait',
                    'auto_direct'            => !empty(ilgl_settings()['preplink_auto_direct']) ? ilgl_settings()['preplink_auto_direct'] : 0,
                    'modify_conf'            => modify_conf(),
                    'replace_text'           => [
                        'enable' => !empty(ilgl_settings()['replace_text_enable']) ? ilgl_settings()['replace_text_enable'] : 0,
                        'text'   => !empty(ilgl_settings()['replace_text']) ? ilgl_settings()['replace_text'] : 'link is ready',
                    ],
                    'meta_attr'       => [
                        'auto_direct' => !empty(ilgl_meta_option()['auto_direct']) ? ilgl_meta_option()['auto_direct'] : 0,
                        'time'        => isset(ilgl_meta_option()['time']) ? ilgl_meta_option()['time'] : 5,
                    ]
                ],
                $href_vars
            ));
        }
    }

    public function preplink_rewrite_endpoint(){
        if (is_plugin_enable()){
            add_rewrite_endpoint($this->endpoint_conf(), EP_PERMALINK | EP_PAGES | EP_ROOT | EP_CATEGORIES | EP_SEARCH);
            add_filter('template_include', [$this, 'intelligent_link_template_include']);
            if (INTELLIGENT_LINK_DEV == 1) {
                flush_rewrite_rules();
            }
        }
    }

    public function prep_head() {
        wp_enqueue_style('ilgl-template', plugin_dir_url(__FILE__) . 'css/template'.(INTELLIGENT_LINK_DEV == 1 ? '': '.min').'.css', [], INTELLIGENT_LINK_VERSION, 'all');
        wp_enqueue_script('ilgl-template', plugin_dir_url(__FILE__) . 'js/template'.(INTELLIGENT_LINK_DEV == 1 ? '': '.min').'.js', array('jquery'), INTELLIGENT_LINK_VERSION, false);

        $prep_template = [];
        $prep_template = apply_filters('ilgl_prep_template_vars', $prep_template);
        wp_localize_script('ilgl-template', 'prep_template', array_merge(
            [
                'modify_conf'         => modify_conf(),
                'countdown_endpoint'  => !empty(ep_settings()['countdown_endpoint']) ? ep_settings()['countdown_endpoint'] : 5,
                'endpoint_direct'     => !empty(ep_settings()['endpoint_auto_direct']) ? ep_settings()['endpoint_auto_direct'] : 0
            ],
            $prep_template
        ));
    }

    public function intelligent_link_template_include($template) {
        global $wp_query;


        $intelligent_link_template = apply_filters('intelligent_link_template', '');

        if (empty($intelligent_link_template)) {
            $intelligent_link_template = dirname( __FILE__ ) . '/templates/default.php';
        }

        include_once plugin_dir_path(INTELLIGENT_LINK_PLUGIN_FILE) . 'includes/class-intelligent-link-template.php';

        if (isset($wp_query->query_vars[$this->endpoint_conf()])) {
            $this->prep_head();
            if (is_singular('product')) {
                remove_all_actions( 'woocommerce_single_product_summary' );
                include_once $intelligent_link_template;
                exit;
            }

            return $intelligent_link_template;
        }

        $product_category = isset($wp_query->query_vars['product_cat']) ? $wp_query->query_vars['product_cat']: '';

        if ($product_category == $this->endpoint_conf()) {
            $this->prep_head();
            add_filter( 'pre_get_document_title', function ($title) {
                if (empty(get_the_title()) || empty($title)) {
                    return isset($_COOKIE['prep_title']) ? $_COOKIE['prep_title'] . ' – ' . get_bloginfo('name') : get_bloginfo('name');
                }
                return $title;
            });

            remove_all_actions('woocommerce_before_main_content');
            remove_all_actions('woocommerce_archive_description');
            remove_all_actions('woocommerce_before_shop_loop');
            remove_all_actions('woocommerce_shop_loop');
            remove_all_actions('woocommerce_after_shop_loop');
            remove_all_actions('woocommerce_sidebar');

            include_once $intelligent_link_template;
            exit;
        }


        return $template;
    }

    public function endpoint_conf(){
        $endpoint = '1';
        if (!empty(ep_settings()['endpoint'])) {
            $endpoint = preg_replace('/[^\p{L}a-zA-Z0-9_\-.]/u', '', trim(ep_settings()['endpoint']));
        }
        return $endpoint;
    }

    public function add_prep_custom_styles(){
        if (is_plugin_enable() && !empty(ilgl_settings()['preplink_custom_style'])) {
            ?>
            <style><?= ilgl_settings()['preplink_custom_style'] ?></style>
            <?php
        }
    }

    public function exclude_elm(){
        $excludeList = ilgl_settings()['preplink_excludes_element'];

        if (!empty($excludeList)) {
            $excludesArr = explode(',', $excludeList);
            $excludesArr = array_map('trim', $excludesArr);
            $excludesArr = array_merge($excludesArr, ['.prep-link-download-btn', '.prep-link-btn', '.keyword-search', '.comment', '.session-expired']);
            $excludesArr = array_unique($excludesArr);
            $excludeList = implode(',', $excludesArr);
        } else {
            $excludeList = '.prep-link-download-btn,.prep-link-btn,.keyword-search,.session-expired,.comment';
        }
        return $excludeList;
    }

    public function href_ex_elm(){
        return !empty(ilgl_settings()['href_exclude']) ? rtrim(ilgl_settings()['href_exclude'], ','): '';
    }

    public function allow_domain(){
        $allow_domain = '';
        $prepList = ilgl_settings()['preplink_url'];
        if (!empty($prepList)) {
            $prepArr = explode(',', $prepList);
            $prepArr = array_map('trim', $prepArr);

            $lastIndex = count($prepArr) - 1;
            if (empty($prepArr[$lastIndex])) {
                unset($prepArr[$lastIndex]);
            }
            $allow_domain = implode(',', $prepArr);
            $allow_domain = rtrim($allow_domain, ',');
        }
        return $allow_domain;
    }

    public function render_meta_short_description($content) {
        $file_name = get_post_meta(get_the_ID(), 'file_name', true);
        $link_no_login = get_post_meta(get_the_ID(), 'link_no_login', true);
        $link_is_login = get_post_meta(get_the_ID(), 'link_is_login', true);
        if ($file_name && $link_is_login && $link_no_login && is_plugin_enable()) {
            $after_description = isset(ilgl_meta_option()['product_elm'])? ilgl_meta_option()['product_elm'] == 'after_short_description': '';
            $html = $this->prep_link_html(ilgl_meta_option(), $file_name);
            if (!empty(get_the_excerpt()) && $after_description) {
                return $content. $html;
            }
        }

        return $content;
    }

    public function render_meta_link_info($content) {
        if (!is_admin() && is_plugin_enable() && !has_shortcode($content, 'link_shortcode')) {
            $file_name = get_post_meta(get_the_ID(), 'file_name', true);
            $link_no_login = get_post_meta(get_the_ID(), 'link_no_login', true);
            $link_is_login = get_post_meta(get_the_ID(), 'link_is_login', true);
            if ($file_name && $link_is_login && $link_no_login) {
                $product_elm_after_content = isset(ilgl_meta_option()['product_elm']) && ilgl_meta_option()['product_elm'] == 'after_product_content';
                $html = $this->prep_link_html(ilgl_meta_option(), $file_name);
                $is_post_or_product = is_singular('post') || (is_singular('product') && $product_elm_after_content);

                if ($is_post_or_product) {
                    $last_p = strrpos($content, '</p>');
                    if ($last_p !== false) {
                        $content = substr_replace($content, $html, $last_p + 4, 0);
                    } else {
                        $content .= $html;
                    }
                }
            }
        }

        return $content;
    }

    public function prep_link_html($meta_attr, $file_name) {
        $advertising = get_option('ads_code');
        $blog_url = base64_encode(get_bloginfo('url'));
        $display_mode = !empty(ilgl_settings()['preplink_wait_text']) ? ilgl_settings()['preplink_wait_text'] : 'wait_time';

        $html = '';
        if (isset($advertising['ads_7']) && !empty($advertising['ads_7'])) {
            $html = '<div class="preplink-ads preplink-ads-7 advertising-adsterra"><p style="margin-left: 10px;">– Advertising –</p>'.$advertising['ads_7'].'</div>';
        }
        $element = get_post_meta(get_the_ID(), 'render_element', true);
        $wrap = !empty($element['wrap_element']) ? $element['wrap_element'] : 'h3';
        $pre_fix = !empty($element['pre_fix']) ? $element['pre_fix'] .'&nbsp;' : 'Link download: ';

        $html .= '<' . $wrap . ' id="igl-download-now" class="igl-download-now"><b class="b-h-down">' . $pre_fix . '</b>';

        if (is_user_logged_in()) {
            $display_mode = 'progress';
        }
        if ($display_mode === 'progress') {
            $html .= '<div class="post-progress-bar">';
            $html .= '<span class="prep-request" data-id="' . $blog_url . '"><strong class="post-progress">' . $file_name . '</strong></span></div>';
        } else {
            $html .= '<span class="wrap-countdown">';
            $html .= '<span class="prep-request" data-id="' . $blog_url . '"><strong class="link-countdown">' . $file_name . '</strong></span></span>';
        }

        $html .= '</' . $wrap . '>';

        if (is_allow_show_ads() && aicp_can_see_ads()) {
            $html .= '
            <div class="aicp">
                <!-- Ezoic - after_start_vote - incontent_5 -->
                <div id="ezoic-pub-ad-placeholder-176">
                    <ins class="adsbygoogle"
                         style="display:block; text-align:center;"
                         data-ad-layout="in-article"
                         data-ad-format="fluid"
                         data-ad-client="ca-pub-3109927831594907"
                         data-ad-slot="4318488805"></ins>
                    <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                    </script>
                </div>
                <!-- End Ezoic - after_start_vote - incontent_5 -->
            </div>';
        }


        $show_list = !empty($meta_attr['show_list']) ? true: false;

        if ($show_list) {
            $list_link = get_post_meta(get_the_ID(), 'link-download-metabox', true);
            $settings = get_option('meta_attr', array());
            $total = (int) $settings['field_lists']? : 5;

            if (isset($list_link) && !empty($list_link) && is_array($list_link)) {
                $html .= '<div class="list-link-redirect" style="display: none">';
                for ($i = 1; $i <= $total; $i++) {
                    $file_name_key = 'file_name-' . $i;
                    $link_no_login_key = 'link_no_login-' . $i;
                    $link_is_login_key = 'link_is_login-' . $i;
                    $size_key = 'size-' . $i;

                    if (isset($list_link[$file_name_key]) && !empty($list_link[$link_no_login_key]) && isset($list_link[$link_is_login_key])) {
                        $file_name = $list_link[$file_name_key];
                        $size = $list_link[$size_key];
                        if (is_user_logged_in()) {
                            $html .= '<a href="' . esc_html(base64_encode($list_link[$link_is_login_key])) . '" class="preplink-btn-link list-preplink-btn-link">' . esc_html($file_name . ' ' . $size) . '</a>';
                        } else {
                            $html .= '<a href="' . esc_html(base64_encode($list_link[$link_no_login_key])) . '" class="preplink-btn-link list-preplink-btn-link">' . esc_html($file_name . ' ' . $size) . '</a>';
                        }

                    }
                }
                $html .= '</div>';
            }
        }

        return $html;
    }

    public function link_shortcode_callback() {
        $file_name = get_post_meta(get_the_ID(), 'file_name', true);
        $link_no_login = get_post_meta(get_the_ID(), 'link_no_login', true);
        $link_is_login = get_post_meta(get_the_ID(), 'link_is_login', true);
        if ($file_name && $link_is_login && $link_no_login) {
           $html = $this->prep_link_html(ilgl_meta_option(), $file_name);
        }
        return !empty($html) ? $html : '';
    }
}

