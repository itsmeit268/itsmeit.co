<?php

/***
 * @link       https://itsmeit.co
 * @since      1.0.0
 * @package    Email_Maketting
 * @subpackage Email_Maketting/public
 * @author     itsmeit.co <buivanloi.2010@gmail.com>
 */
class Email_Maketting_Public
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version){
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->settings    = get_option('email_marketing_settings');

        if (isset($this->settings['email_marketing_enable']) && $this->settings['email_marketing_enable']) {
            add_action( 'init', array($this, 'email_maketting_data'), 10, 0 );

            add_action( 'theme_page_templates', array($this, 'add_unsubscribe_template_to_select'), 10, 4 );
            add_action( 'page_template', array($this, 'add_unsubscribe_page_template'), 9 );

            add_action( 'theme_page_templates', array($this, 'add_verify_template_to_select'), 10, 4 );
            add_action( 'page_template', array($this, 'add_verify_page_template'), 9 );

            add_action( 'wp_ajax_unsubscribe_callback', array($this, 'unsubscribe_function') );
            add_action( 'wp_ajax_nopriv_unsubscribe_callback', array($this, 'unsubscribe_function') );

            add_action( 'wp_ajax_subscribe_callback', array($this, 'subscribe_function') );
            add_action( 'wp_ajax_nopriv_subscribe_callback', array($this, 'subscribe_function') );

            add_action( 'wp_ajax_verify_callback', array($this, 'verify_function') );
            add_action( 'wp_ajax_nopriv_verify_callback', array($this, 'verify_function') );

            add_action( 'wp_ajax_contact_callback', array($this, 'contact_function') );
            add_action( 'wp_ajax_nopriv_contact_callback', array($this, 'contact_function') );

            add_shortcode('subscribe_shortcode', array($this, 'subscribe_shortcode_function') );
            add_shortcode('contact_shortcode', array($this, 'contact_shortcode_function') );

            add_action( 'before_delete_post', array($this,'delete_post_id_email_marketting') );
            add_action( 'transition_post_status', array($this, 'check_post_public_or_update'), 10, 3  );

            add_action( 'wp_footer', array($this, 'ajax_loader') );
        }
    }

    function subscribe_shortcode_function(){
        ob_start( );
        include(plugin_dir_path(__FILE__) . 'template/subscribe_form.phtml' );
        return ob_get_clean( );
    }

    function contact_shortcode_function(){
        ob_start( );
        include(plugin_dir_path(__FILE__) . 'template/contact_form.phtml' );
        return ob_get_clean( );
    }

    function add_unsubscribe_page_template($page_template){
        if (get_page_template_slug() == 'unsubscribe.phtml') {
            $page_template = dirname(__FILE__) . '/unsubscribe.phtml';
        }
        return $page_template;
    }

    function add_unsubscribe_template_to_select($post_templates, $wp_theme, $post, $post_type){
        $post_templates['unsubscribe.phtml'] = __('Unsubscribe Email' );
        return $post_templates;
    }

    function add_verify_page_template($page_template){
        if (get_page_template_slug() == 'verify_email.phtml') {
            $page_template = dirname(__FILE__) . '/verify_email.phtml';
        }
        return $page_template;
    }

    function add_verify_template_to_select($post_templates, $wp_theme, $post, $post_type){
        $post_templates['verify_email.phtml'] = __('Verify Email' );
        return $post_templates;
    }

    public function enqueue_styles(){
        wp_enqueue_style('subscribe_email', plugin_dir_url(__FILE__) . 'css/email-maketting-subscribe.css', array(), $this->version, 'all' );
    }

    public function enqueue_scripts(){
        wp_enqueue_script('email_maketting_subscribe', plugin_dir_url(__FILE__) . 'js/email-maketting-subscribe.js', array('jquery'), $this->version, false );
        wp_localize_script('email_maketting_subscribe', 'email_subscribe_vars', array(
            'subscribe_ajax_url' => admin_url('admin-ajax.php')
        ) );

        if (isset($_GET['mus_rq'])) {
            wp_enqueue_script('email_maketting', plugin_dir_url(__FILE__) . 'js/email-maketting-unsubscribe.js', array('jquery'), $this->version, false );
            wp_localize_script('email_maketting', 'email_maketting_vars', array(
                'email_ajax_url' => admin_url('admin-ajax.php')
            ) );
        }

        if (isset($_GET['rqco'])) {
            wp_enqueue_script('email_verify', plugin_dir_url(__FILE__) . 'js/email-maketting-verify.js', array('jquery'), $this->version, false );
            wp_localize_script('email_verify', 'email_verify_vars', array(
                'verify_ajax_url' => admin_url('admin-ajax.php')
            ) );
        }

        if (!is_front_page() && is_page() && get_the_ID() == '10247') {
            wp_enqueue_script('email_maketting_contact', plugin_dir_url(__FILE__) . 'js/email-maketting-contact.js', array('jquery'), $this->version, false );
            wp_localize_script('email_maketting_contact', 'email_contact_vars', array(
                'contact_ajax_url' => admin_url('admin-ajax.php')
            ) );
        }
    }

    function ajax_loader(){
        require_once WP_PLUGIN_DIR . '/email-maketting/public/template/ajax_loader.phtml';
    }

    function get_category_by_post_id($post_id){
        $categories_ids = wp_get_post_categories($post_id);

        if ($categories_ids) {
            $highest_category_id = null;
            foreach ($categories_ids as $category_id) {
                $category = get_category($category_id);
                if (!$highest_category_id || $category->category_parent == 0) {
                    $highest_category_id = $category->cat_ID;
                }
            }
            if ($highest_category_id) {
                return $highest_category_id;
            }
        }
        return 0;
    }

    function get_category_by_product_id($product_id) {
        $taxonomy = 'product_cat';

        $categories = wp_get_post_terms($product_id, $taxonomy);

        if ($categories && !is_wp_error($categories)) {
            $highest_category_id = null;
            foreach ($categories as $category) {
                if (!$highest_category_id || $category->parent == 0) {
                    $highest_category_id = $category->term_id;
                }
            }
            if ($highest_category_id) {
                return $highest_category_id;
            }
        }

        return 0;
    }


    function check_is_product(){
        return (class_exists('WooCommerce', false) && is_singular('product'));
    }

    function save_email_marketing_data(){

        if (!is_admin() && !is_front_page() && is_singular('post') && is_user_logged_in() || $this->check_is_product()) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'email_marketing';
            $table_exists = $this->table_exists($table_name );

            if (!$table_exists) {
                return;
            }

            $user = wp_get_current_user( );
            $email = $user->user_email;

            if ($user->user_email == 'itsmeit.biz@gmail.com' || $user->user_email == 'buivanloi.2010@gmail.com') {
                return;
            }

            $exist_link = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM $table_name WHERE link = %s AND email = %s",
                    get_permalink(),
                    $email
                )
             );

            $category_id = $this->get_category_by_post_id(get_the_ID());
            if ($this->check_is_product()) {
                $category_id = $this->get_category_by_product_id(get_the_ID());
            }

            if (empty($exist_link)) {
                $data = array(
                    'name' => $user->display_name ?: 'friend',
                    'email' => $email,
                    'create_date' => current_time('mysql'),
                    'link' => get_permalink(),
                    'send_count' => '0',
                    'browser' => $_SERVER['HTTP_USER_AGENT'] ?: 'Unknown',
                    'allow' => 1,
                    'post_id' => get_the_ID(),
                    'category_id' => $category_id
                 );
                $wpdb->insert($table_name, $data );
            } else {
                $existing_browser = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT browser FROM $table_name WHERE email = %s AND link = %s",
                        $email,
                        get_permalink()
                    )
                 );

                $browser = $_SERVER['HTTP_USER_AGENT'] ?: 'Unknown';

                if ($existing_browser !== $browser) {
                    $wpdb->update(
                        $table_name,
                        array('browser' => $browser),
                        array('email' => $email, 'link' => get_permalink())
                     );
                }
            }
        }
    }

    function html_marketting_email($post_type, $post_id, $post_link, $name, $email){
        $template_path = WP_PLUGIN_DIR . '/email-maketting/public/template/marketing_email.html';
        $template_content = file_get_contents($template_path );

        $description = get_the_excerpt($post_id);
        $description_html = '<p style="line-height: 140%; padding:0 8px;text-align: center">'.$description.'</p>';
        if ($post_type === 'product') {
            $description_html = '<p style="line-height: 140%; padding:0 8px;text-align: left">'.$description.'</p>';
        }
        $template_content = str_replace('{{name}}', $name, $template_content );
        $template_content = str_replace('{{email}}', $email, $template_content );
        $template_content = str_replace('{{post_link}}', $post_link, $template_content );
        $template_content = str_replace('{{post_title}}', get_the_title($post_id), $template_content );
        $template_content = str_replace('{{description}}', $description_html, $template_content );
        $template_content = str_replace('{{site_url}}', get_bloginfo('url'), $template_content );
        $template_content = str_replace('{{dir_image}}', plugin_dir_url(__FILE__) . 'template/images', $template_content );
        return $template_content;
    }

    function html_subscribe_email($username, $email, $link){
        $template_path = WP_PLUGIN_DIR . '/email-maketting/public/template/subscribe_email.html';
        $template_content = file_get_contents($template_path );
        $template_content = str_replace('{{name}}', $username, $template_content );
        $template_content = str_replace('{{email}}', $email, $template_content );
        $template_content = str_replace('{{link}}', $link, $template_content );
        $template_content = str_replace('{{site_title}}', get_bloginfo('name'), $template_content );
        return $template_content;
    }

    function html_contact_email_admin($admin_name, $customer_name, $customer_email, $subject, $content){
        $template_path = WP_PLUGIN_DIR . '/email-maketting/public/template/contact_email_admin.html';
        $template_content = file_get_contents($template_path );
        $template_content = str_replace('{{admin_name}}', $admin_name, $template_content );
        $template_content = str_replace('{{customer_name}}', $customer_name, $template_content );
        $template_content = str_replace('{{email}}', $customer_email, $template_content );
        $template_content = str_replace('{{subject}}', $subject, $template_content );
        $template_content = str_replace('{{content}}', $content, $template_content );
        $template_content = str_replace('{{site_title}}', get_bloginfo('name'), $template_content );
        return $template_content;
    }

    function html_contact_email_customer($username){
        $template_path = WP_PLUGIN_DIR . '/email-maketting/public/template/contact_email_customer.html';
        $template_content = file_get_contents($template_path );
        $template_content = str_replace('{{name}}', $username, $template_content );
        $template_content = str_replace('{{site_title}}', get_bloginfo('name'), $template_content );
        return $template_content;
    }

    function email_maketting_data(){
        add_action( 'template_redirect', array($this, 'save_email_marketing_data')  );
    }

    function check_post_public_or_update(string $new_status, string $old_status, WP_Post $post ): void {
        $settings = $this->settings;

        if (isset($settings['disable_sendmail']) && $settings['disable_sendmail'] === '1') {
            return;
        }

        if ( is_admin() && wp_is_post_autosave( $post )){
            return;
        }

        if (($post->post_type !== 'post' || $post->post_type !== 'product') && $post->post_status !== 'publish') {
            return;
        }

        if (!empty( $_REQUEST['meta-box-loader'])) {
            return;
        }

        if ($old_status !== 'publish' && $new_status === 'publish' ) {
            $this->send_email_maketting_callback('publish_all', $post->ID, $post->post_type );
        } else if ($old_status === 'publish' && $new_status === 'publish' ) {
            $this->send_email_maketting_callback('update_one', $post->ID, $post->post_type );
        }
    }

    function send_email_maketting_callback($status, $post_id, $post_type){
        global $wpdb;
        $table_name = $wpdb->prefix . 'email_marketing';

        if ($this->table_exists($table_name)) {
            $post_link = get_permalink($post_id );
            $query = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE link = %s AND allow = 1",
                $post_link
            );

            if ($status == 'publish_all') {
                $category_id = $this->get_category_by_post_id($post_id);
                if ($post_type === 'product') {
                    $category_id = $this->get_category_by_product_id($post_id);
                }
                $query = $wpdb->prepare(
                    "SELECT DISTINCT name, email FROM $table_name WHERE allow = 1 AND category_id = %d",
                    $category_id
                );
            }

            $users = $wpdb->get_results($query);
            if (!empty($users)) {
                $headers = array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
                 );

                $relateds = get_posts(
                    array(
                        'post_type' => 'post',
                        'posts_per_page' => 5,
                        'post__not_in' => array($post_id),
                        'orderby' => 'rand',
                        'category__in' => wp_get_post_categories($post_id)
                    ) );

                $related_html = '';

                if ($post_type === 'product') {
                    $relateds = $this->get_related_products($post_id);
                }

                foreach ($relateds as $related) {
                    // Lấy giá gốc của sản phẩm
                    if ($post_type === 'product') {
                        $product = wc_get_product($related->ID);
                        $regular_price = $product->get_regular_price();
                        $sale_price = $product->get_sale_price();
                    } else {
                        $regular_price = '';
                        $sale_price = '';
                    }

                    $related_post_id = $related->ID;
                    $related_html .= $this->render_related_html(
                        $post_type,
                        $regular_price,
                        $sale_price,
                        get_permalink($related_post_id),
                        get_the_post_thumbnail_url($related_post_id, 'thumbnail'),
                        get_the_title($related_post_id),
                        get_the_excerpt($related_post_id)
                     );
                }

                foreach ($users as $user) {
                    $recipient_email = $user->email;
                    $subject = '[New] ' . get_the_title($post_id );

                    $html_content = $this->html_marketting_email($post_type, $post_id, $post_link, $user->name, $recipient_email );
                    $html_content = str_replace('{{related_title}}', __('YOU MIGHT ALSO LIKE'), $html_content );
                    $html_content = str_replace('{{related_posts}}', $related_html, $html_content );
                    $html_content = str_replace('{{email_customer}}', base64_encode($recipient_email), $html_content );

                    try {
                        $is_sent = wp_mail($recipient_email, $subject, $html_content, $headers );

                        if ($is_sent) {
                            $wpdb->update(
                                $table_name,
                                array('send_count' => $user->send_count + 1),
                                array('email' => $recipient_email)
                             );
                        }
                    } catch (Exception $e) {
                        error_log('Lỗi gửi email: ' . $e->getMessage() );
                    }
                }
            }
        }
    }

    function get_related_products($product_id) {
        $related_products = array();
        $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
        if ($product_categories && !is_wp_error($product_categories)) {
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 5,
                'post__not_in' => array($product_id),
                'orderby' => 'rand',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $product_categories,
                    ),
                ),
            );
            $related_products = get_posts($args);
        }

        return $related_products;
    }

    /**
     * @param $table_name
     * @return string|null
     */
    function table_exists($table_name){
        global $wpdb;
        $count_table = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = %s", $table_name) );
        $table_check = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name)) );
        if ($table_check === $table_name || $count_table > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $post_url
     * @param $thumbnail
     * @param $title
     * @param $description
     * @return string
     */
    function render_related_html($post_type, $regular_price, $sale_price ,$post_url, $thumbnail, $title, $description){
        $des_post = '<table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="font-size: 14px;">
                                <p style="color: #ced4d9; line-height: 15px; max-height: 30px;; overflow: hidden;">
                                    '.$description.'
                                </p>
                            </td>
                        </tr>
                    </table>
                    ';
        if ($post_type === 'product') {
            $des_product = '<p style="color: #ced4d9; font-weight: 600;font-size: 14px">';
            $des_product .= 'Price: ' . wc_price($regular_price);
            if ($sale_price) {
                $des_product .= '<p style="color: #eb1900; font-weight: 600;font-size: 14px">';
                $des_product .= 'Sales: ' . wc_price($sale_price);
                $des_product .= '</p>';
            }
            $des_product .= '</p>';
            $des_post = $des_product;
        }

        $html = '<tr>
                    <td class="v-container-padding-padding custom-td" align="center" style="padding: 0;font-family: Arial, sans-serif;">
                        <table style="width: 100%;">
                            <tr>
                                <td class="image" style="padding: 0 0 0 10px; width: 30%;">
                                    <a style="color: #333;text-decoration: none;" href="' . $post_url . '">
                                        <img style="width: 90%;height: 82px; object-fit: cover;padding-top:13px" src="' . $thumbnail . '" alt="' . $title . '">
                                    </a>
                                </td>
                                <td class="title_description" style="color: #333;text-align: left;padding: 0; width: 70%;">
                                    <h2 style="font-size: 15px; font-weight: 400;overflow: hidden; overflow: hidden;line-height: 1.2em; max-height: 2.4em;">
                                        <a style="color: #fff;text-decoration: none;" href="' . $post_url . '">' . $title . '</a>
                                    </h2>
                                     '.$des_post.'
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>';
        return $html;
    }

    function unsubscribe_function(){
        $response = [
            'false' => __('An error occurred, please try again another time.', 'email-maketting')
        ];

        if (isset($_REQUEST['email']) && $_REQUEST['email']) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'email_marketing';
            $table_exists = $this->table_exists($table_name );

            if ($table_exists) {
                $email = sanitize_email($_REQUEST['email'] );
                $query = $wpdb->prepare("SELECT * FROM $table_name WHERE email = %s AND allow = %d", $email, 1 );
                $result = $wpdb->get_row($query );

                if ($result !== null) {
                    $wpdb->update(
                        $table_name,
                        array('allow' => 0),
                        array('email' => $_REQUEST['email'])
                     );

                    $response = array(
                        'success' => __('You have unsubscribed from the newsletter.', 'email-maketting')
                     );
                }
            }
        }

        wp_send_json_success($response );
        wp_die( );
    }

    function subscribe_function(){
        $response = [
            'message' => __('An error occurred, please try again another time.', 'email-maketting')
        ];
        if (isset($_REQUEST['email']) && $_REQUEST['email']) {
            global $wpdb;
            $subscribe_table = $wpdb->prefix . 'email_subscribe';
            $marketting_table = $wpdb->prefix . 'email_marketing';

            if ($this->table_exists($subscribe_table) && $this->table_exists($marketting_table)) {
                $email = sanitize_email($_REQUEST['email'] );
                if (is_email($email)) {

                    $query_subscribe_email = $this->_query_data_by_email($wpdb, $subscribe_table, '*', $email );
                    $query_marketing_email = $this->_query_data_by_email($wpdb, $marketting_table, '*', $email );

                    if ($query_subscribe_email === null && $query_marketing_email === null) {

                        if (is_user_logged_in()) {
                            $username = wp_get_current_user()->display_name;
                        } else {
                            $username = strstr($email, '@', true );
                        }

                        $wpdb->insert($subscribe_table, array(
                            'name' => $username,
                            'email' => $email,
                            'post_url' => isset($_REQUEST['post_url']) ? $_REQUEST['post_url'] : '',
                            'verification_code' => wp_generate_password(10, false),
                            'create_date' => current_time('mysql')
                        ) );

                        $verification_code = $this->_query_data_by_email($wpdb, $subscribe_table, 'verification_code', $email );

                        $template_content = $this->html_subscribe_email(
                            $username,
                            $email,
                            get_bloginfo('url') . '/subscribe-verify/?rqco=' . $verification_code . '&rqem=' . base64_encode($email)
                         );

                        $subject = __('Subscribe to the newsletter from ' . get_bloginfo('name') );
                        wp_mail($email, $subject, $template_content, array(
                            'Content-Type: text/html; charset=UTF-8',
                            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
                        ) );
                        $response = [
                            'success' => 'success',
                            'message' => __('Registration request sent successfully! A verification link has been sent to your email address.', 'email-maketting')
                        ];

                    } elseif ($query_subscribe_email === null && !empty($query_marketing_email)) {
                        $wpdb->update(
                            $marketting_table,
                            array('allow' => 1),
                            array('email' => $email)
                         );
                        $response = ['message' => __('You have subscribed to receive new posts from ', 'email-maketting') . get_bloginfo('name')];
                    } elseif ($query_subscribe_email !== null && $query_marketing_email === null) {
                        $response = ['message' => __('You have previously registered, please check your email and verify.', 'email-maketting' )];
                    }
                } else {
                    $response = ['message' => __('Invalid email address, please check again.'. 'email-maketting')];
                }
            }
        } else {
            $response = ['message' => __('Please enter your email to subscribe.', 'email-maketting')];
        }

        wp_send_json_success($response );
        wp_die( );
    }

    /**
     * @param $wpdb
     * @param $table
     * @param $email
     * @return mixed
     */
    function _query_data_by_email($wpdb, $table, $row, $email){
        $query_date = $wpdb->prepare("SELECT $row FROM $table WHERE email = %s", $email );
        return $wpdb->get_var($query_date );
    }

    function get_curent_date(){
        $current_date = new DateTime( );
        $current_date->setTimezone(new DateTimeZone('GMT+7') );
        return $current_date->format('Y-m-d H:i:s' );
    }

    function verify_function(){
        $response = [
            'success' => 'error_request',
            'message' => __('An error occurred, please try again another time.', 'email-maketting')
        ];

        function get_category_id_from_url($category_url) {
            $pattern = '/\/([^\/]+)(?=\.html$)/';
            preg_match($pattern, $category_url, $matches);

            if (isset($matches[1])) {
                $category_slug = $matches[1];
                $category = get_term_by('slug', $category_slug, 'category');
                if ($category) {
                    return $category->term_id;
                }
            }
            return 0;
        }

        if (isset($_REQUEST['verify_code']) && $_REQUEST['verify_code'] && isset($_REQUEST['verify_email']) && $_REQUEST['verify_email']) {
            global $wpdb;
            $subscribe_table = $wpdb->prefix . 'email_subscribe';
            $marketing_table = $wpdb->prefix . 'email_marketing';

            if ($this->table_exists($subscribe_table) && $this->table_exists($subscribe_table)) {
                $email = sanitize_email($_REQUEST['verify_email'] );

                $create_date = $this->_query_data_by_email($wpdb, $subscribe_table, 'create_date', $email );
                $email_customer = $this->_query_data_by_email($wpdb, $marketing_table, 'email', $email );
                $verification_code = $this->_query_data_by_email($wpdb, $subscribe_table, 'verification_code', $email );
                $curent_url = $this->_query_data_by_email($wpdb, $subscribe_table, 'post_url', $email );

                if (empty($create_date) && !empty($email_customer)) {
                    $response = array(
                        'success' => 'verify_exits',
                        'message' => __('This email is already subscribed to our newsletter, you do not need to register again.', 'email-maketting')
                     );
                } else if (!empty($create_date) && empty($email_customer)) {

                    $current_time = $this->get_curent_date( );

                    $expiration_time = strtotime('+24 hours', strtotime($create_date) );
                    $expiration_time = date('Y-m-d H:i:s', $expiration_time );

                    if ($expiration_time <= $current_time) {

                        $response = array(
                            'success' => 'verify_expired',
                            'message' => __('Verification unsuccessful. The link does not exist or has expired.', 'email-maketting')
                         );
                        $wpdb->delete(
                            $subscribe_table,
                            array(
                                'email' => $email,
                                'verification_code' => $verification_code
                            )
                         );
                    } else {
                        if ($verification_code === $_REQUEST['verify_code']) {
                            if (url_to_postid($curent_url)) {
                                $category_id = $this->get_category_by_post_id(url_to_postid($curent_url));
                            } else {
                                $category_id = get_category_id_from_url($curent_url);
                            }
                            $wpdb->insert($marketing_table, array(
                                'name' => strstr($email, '@', true),
                                'email' => $email,
                                'create_date' => current_time('mysql'),
                                'link' => $curent_url ?: 'home',
                                'send_count' => 0,
                                'browser' => $_SERVER['HTTP_USER_AGENT'] ?: 'Unknown',
                                'allow' => 1,
                                'post_id' => url_to_postid($curent_url) ?: '0',
                                'category_id' => $category_id
                            ) );

                            $wpdb->delete(
                                $subscribe_table,
                                array(
                                    'email' => $email,
                                    'verification_code' => $verification_code
                                )
                             );

                            $response = array(
                                'success' => 'verified',
                                'message' => __('Email verification successful. Thank you for subscribing to our newsletter!', 'email-maketting')
                             );
                        } else {
                            $response = array(
                                'success' => 'verify_false',
                                'message' => __('Authentication unsuccessful. The link is not accepted.', 'email-maketting')
                             );
                        }
                    }
                } else if (empty($create_date) && empty($email_customer)) {
                    $response = array(
                        'success' => 'verify_null',
                        'message' => __('Verification unsuccessful. Please resubmit your registration.', 'email-maketting')
                     );
                }
            }
        } else {
            $response = [
                'success' => 'verify_no_code_or_email',
                'message' => __('Authentication failed, please try again later.', 'email-maketting')
            ];
        }

        wp_send_json_success($response );
        wp_die( );
    }

    function delete_post_id_email_marketting($post_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'email_marketing';
        $wpdb->delete(
            $table_name,
            array(
                'post_id' => $post_id
            )
         );
    }

    function contact_function(){
        $response = [
            'success' => '0',
            'message' => __('An error occurred, please try again another time.', 'email-maketting')
        ];

        if (isset($_REQUEST['full_name']) && $_REQUEST['full_name'] &&
            isset($_REQUEST['email']) && $_REQUEST['email'] && isset($_REQUEST['content']) && $_REQUEST['content']) {

            global $wpdb;
            $contact_table = $wpdb->prefix . 'email_contact';

            if ($this->table_exists($contact_table)) {
                $customer_name = $_REQUEST['full_name'];
                $customer_email = sanitize_email($_REQUEST['email'] );
                $admin_email = sanitize_email(get_option('admin_email'));
                $content = $_REQUEST['content'];
                $subject = $_REQUEST['subject'];

                $customer_subject = "Re: ". $subject;
                $template_content_customer = $this->html_contact_email_customer($customer_name);

                wp_mail($customer_email, $customer_subject, $template_content_customer, array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>'
                ) );

                $admin_name = strstr($admin_email, '@', true);
                $template_content_admin = $this->html_contact_email_admin($admin_name, $customer_name, $customer_email, $subject, $content);

                wp_mail($admin_email, $subject, $template_content_admin, array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>'
                ) );

                setcookie("contact-email-is-sent", "1", time() + 3600, "/");

                $wpdb->insert($contact_table, array(
                    'name' => $customer_name,
                    'subject' => $subject,
                    'content' => $content,
                    'email' => $customer_email,
                    'sent_count' => 0,
                    'create_date' => current_time('mysql'),
                ) );

                $response = [
                    'success' => '1',
                    'message' => __('Thank you for getting in touch. We have received your message and will respond to you as soon as possible.', 'email-maketting')
                ];
            }
        } else {
            $response = [
                'success' => '0',
                'message' => __('An error occurred, please try again later.', 'email-maketting')
            ];
        }

        wp_send_json_success($response );
        wp_die( );
    }
}
