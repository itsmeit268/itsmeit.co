<?php
/*
Plugin Name: PMPro Customizations
Plugin URI: https://www.paidmembershipspro.com/wp/pmpro-customizations/
Description: Customizations for my Paid Memberships Pro Setup
Version: .1
Author: Paid Memberships Pro
Author URI: https://www.paidmembershipspro.com
*/

add_filter( 'pmpro_login_redirect', '__return_false' );

function pmpro_default_registration_level( $user_id ) {
    pmpro_changeMembershipLevel( 1, $user_id );
}

add_action( 'user_register', 'pmpro_default_registration_level' );

function show_level() {
    return '<span class="show-level" style="font-weight: 600;"></span>';
}

add_action('wp_ajax_show_level_callback', 'show_level_callback');
add_action('wp_ajax_nopriv_show_level_callback', 'show_level_callback');

function show_level_callback(){
    $response['level'] = get_level_name();
    wp_send_json_success($response);
    exit();
}

function get_user_id() {
    $current_user_id = wp_get_current_user()->ID;
    return $current_user_id ? : get_current_user_id();
}

function get_level_name(){
    $level_name = 'FREE';
    if (user_point() > 10 && user_point() < 50000) {
        $level_name = 'GOLD';
    } elseif (user_point() >= 50000 && user_point() < 100000) {
        $level_name = 'PREMIUM';
    } elseif(user_point() >= 100000) {
        $level_name = 'VIP';
    }
    return $level_name;
}

function user_point() {
    $user_point = get_user_meta(get_user_id(), 'wp_user_point', true);
    return !empty($user_point) ? (int)$user_point: 0;
}

function free_level() {
    if (get_level_name() == 'FREE') {
        return true;
    }
    return false;
}

function is_allow_show_ads() {
    $manager = current_user_can('manage_options');
    $IP = array('127.0.0.1', '127.0.1.1', 'localhost');
    if (!$manager && !in_array($_SERVER['REMOTE_ADDR'], $IP) && free_level()) {
        return true;
    }
    return false;
}

function link_member_render($isMeta, $link_is_login, $link_no_login, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id, $settings) {
    $point = get_post_meta($post_id, 'point_download', true);
    $file_point = !empty($point) ? (int) $point : 0;
    if (!$isMeta) : ?>
        <?php href_render($isMeta, $link_no_login, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id);
        if ($isMeta) list_member_link($post_id, $settings); ?>
    <?php else :
        if (get_level_name() == 'VIP'):?>
            <?php href_render($isMeta, $link_is_login, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id);
            if ($isMeta) list_member_link($post_id, $settings); ?>
        <?php elseif (get_level_name() !== 'VIP' && user_point() < $file_point): ?>
            <?php download_permission($file_point); ?>
        <?php elseif(get_level_name() !== 'VIP' && user_point() > $file_point):?>
            <?php href_render($isMeta, $link_is_login, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id);
            if ($isMeta) list_member_link($post_id, $settings); ?>
        <?php else: ?>
            <?php href_render($isMeta, $link_no_login, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id);
            if ($isMeta) list_member_link($post_id, $settings); ?>
        <?php endif;
    endif;
}

function href_render($isMeta, $link, $prepLinkURL, $file_name, $file_size, $prepLinkText, $post_id) {
    $point = get_post_meta($post_id, 'point_download', true);
    $file_point = !empty($point) ? (int) $point : 0;?>
    <a href="javascript:void(0)" data-request="<?php echo $isMeta ? esc_html(modify_href(base64_encode($link))) : esc_html($prepLinkURL); ?>"
       class="preplink-btn-link" data-point="<?= $file_point?>">
        <?php echo $isMeta ? ($file_name.' '.$file_size) : $prepLinkText; ?>
    </a>
<?php }

function list_member_link($post_id, $settings) {
    $list_link = get_post_meta($post_id, 'link-download-metabox', true);
    $total = (int) $settings['field_lists']? : 5;
    if (isset($list_link) && !empty($list_link) && is_array($list_link)) {
        $point = get_post_meta($post_id, 'point_download', true);
        $file_point = !empty($point) ? (int) $point : 0;
        ?>
        <div class="list-link-redirect" >
            <?php for ($i = 1; $i <= $total; $i++) {
                $file_name_key = 'file_name-' . $i;
                $link_no_login_key = 'link_no_login-' . $i;
                $link_is_login_key = 'link_is_login-' . $i;
                $size_key = 'size-' . $i;

                if (isset($list_link[$file_name_key]) && !empty($list_link[$link_no_login_key]) && $list_link[$link_is_login_key]) { ?>
                    <?php
                    $file_name = $list_link[$file_name_key];
                    $size = $list_link[$size_key]; ?>
                    <?php if (get_level_name() == 'FREE') :?>
                        <a href="javascript:void(0)" data-request="<?= esc_html(modify_list_href(base64_encode($list_link[$link_no_login_key])))?>"
                           class="preplink-btn-link list-preplink-btn-link" data-point="<?= $file_point?>"><?= esc_html($file_name . ' ' . $size) ?></a>
                    <?php else: ?>
                        <a href="javascript:void(0)" data-request="<?= esc_html(modify_list_href(base64_encode($list_link[$link_is_login_key])))?>"
                           class="preplink-btn-link list-preplink-btn-link" data-point="<?= $file_point?>"><?= esc_html($file_name . ' ' . $size) ?></a>
                    <?php endif;?>
                <?php }
            } ?>
        </div>
    <?php }
}

function download_permission($file_point) {
    $current_language = pll_current_language();
    $requireLevelText = ($current_language == 'en') ? 'You need' : 'Bạn cần';
    ?>
    <div class="not-vip-download" style="display: none">
        <p class="require-level">
            <?= $requireLevelText ?> <?= $file_point ?> <?= ($current_language == 'en') ? 'points to download this file.' : 'điểm để tải xuống tệp tin này.' ?>
            <a class="require-vip-download" href="<?= pmpro_url('levels'); ?>">
                <?= ($current_language == 'en') ? 'Click here' : 'bấm vào đây' ?>
            </a>
            <?= ($current_language == 'en') ? 'to earn additional points.' : 'để kiếm thêm điểm.' ?>
        </p>
    </div>
    <?php
}

add_action('wp_enqueue_scripts', 'member_script_callback');
function member_script_callback() {
    $current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($current_url, '/my-account/') !== false ||
        strpos($current_url, 'my-account.html') !== false ||
        strpos($current_url, 'user-login.html') !== false ||
        strpos($current_url, '/user/') !== false
    ) {
        wp_enqueue_style('member-style', plugin_dir_url(__FILE__). 'css/member-style.css', array(), FOXIZ_THEME_VERSION, 'all');
        wp_enqueue_script('member-checkout', plugin_dir_url(__FILE__). 'js/member-checkout.js', array('jquery'), FOXIZ_THEME_VERSION, true);
    }
    wp_enqueue_script('update-point', plugin_dir_url(__FILE__). 'js/update-point.js', array('jquery'), FOXIZ_THEME_VERSION, true);
    wp_localize_script('update-point', 'update_point', array(
        '_ajax_url' => admin_url('admin-ajax.php')
    ) );

    if (strpos($current_url, '/my-account/profile') !== false) {
        wp_enqueue_script('validate-profile', plugin_dir_url(__FILE__). 'js/validate-profile.js', array('jquery'), FOXIZ_THEME_VERSION, true);
    }
}

add_action('wp_ajax_update_user_points', 'update_user_points_callback');
add_action('wp_ajax_nopriv_update_user_points', 'update_user_points_callback');

function update_user_points_callback() {
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $point = '50';
        $current_point = get_user_meta($user_id, 'wp_user_point', true);
        if ($current_point) {
            $point += $current_point;
        }
        update_user_meta($user_id, 'wp_user_point', $point);
    }
    wp_die();
}

add_filter('pmpro_has_membership_access_filter', 'custom_pmpro_login_redirect', 10, 4);
function custom_pmpro_login_redirect($has_access, $mypost, $myuser, $post_membership_levels) {
    $current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($current_url, '/my-account/') && !is_user_logged_in() ) {
        $_SESSION['redirect_to'] = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        wp_redirect(get_bloginfo('url').'/user-login.html');
        exit();
    }

    return $has_access;
}

add_action('pmpro_after_checkout', 'point_calculation_after_checkout', 10, 2);
function point_calculation_after_checkout($user_id, $morder) {
    if ($morder->status === 'success') {
        $point_mappings = array(
            '1.99' => '2000',
            '4.99' => '5000',
            '9.99' => '10000',
            '19.99' => '20000',
            '49.99' => '50000',
            '99' => '100000'
        );

        if (isset($point_mappings[$morder->total])) {
            $current_point = get_user_meta($user_id, 'wp_user_point', true);
            if ($current_point) {
                $point_mappings[$morder->total] += $current_point;
            }
            update_user_meta($user_id, 'wp_user_point', $point_mappings[$morder->total]);
            pmpro_changeMembershipLevel( false, $user_id );
        }
    }
}

function updated_user_avatar_user_meta( $meta_id, $user_id, $meta_key, $meta_value ) {
    if ( 'user_avatar' === $meta_key ) {
        $filename      = $meta_value['fullpath'];

        // Check if the file has an allowed image format
        $allowed_image_formats = array( 'jpeg', 'jpg', 'png', 'gif', 'svg' );
        $filetype      = wp_check_filetype( basename( $filename ), null );

        if ( ! in_array( $filetype['ext'], $allowed_image_formats, true ) ) {
            return;
        }

        $attachment    = array(
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_status'    => 'inherit',
        );
        $attach_id     = wp_insert_attachment( $attachment, $filename );
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        update_user_meta( $user_id, 'wp_user_avatar', $attach_id );
    }
}

add_action( 'added_user_meta', 'updated_user_avatar_user_meta', 10, 4 );
add_action( 'updated_user_meta', 'updated_user_avatar_user_meta', 10, 4 );

function user_avatar_filter( $avatar, $id_or_email, $size, $default, $alt ) {
    $my_user = get_userdata( $id_or_email );
    if ( ! empty( $my_user ) ) {
        $avatar_id = get_user_meta( $my_user->ID, 'wp_user_avatar', true );
        if ( ! empty( $avatar_id ) ) {
            $avatar = wp_get_attachment_image_src( $avatar_id, array( $size, $size) );
            if ( is_array( $avatar ) && isset( $avatar[0] ) ) {
                $avatar = "<img alt='{$alt}' src='{$avatar[0]}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
            }
        }
    }
    return $avatar;
}
add_filter( 'get_avatar', 'user_avatar_filter', 20, 5 );

function pmprorh_init_user_profile() {
    if ( ! function_exists( 'pmprorh_add_registration_field' ) ) {
        return false;
    }

    $avatas   = array();
    $avatas[] = new PMProRH_Field(
        'user_avatar',
        'file',
        array(
            'label'     => '',
            'hint'      => 'Recommended size is 500 x 500 px',
            'profile'   => 'only',
            'preview'   => true,
            'addmember' => true,
            'allow_delete' => false,
        )
    );

    foreach ( $avatas as $avata ) {
        pmprorh_add_registration_field('checkout_boxes', $avata);
    }

    $fields = array();

    $fields[] = new PMProRH_Field(
        'website',
        'text',
        array(
            'label'         => 'Website',
            'hint'          => '',
            'profile'       => 'only',
            'addmember'     => true,
            'size' => '100'
        )
    );

    $fields[] = new PMProRH_Field(
        'interest',
        'text',
        array(
            'label'         => 'Interest',
            'hint'          => '',
            'profile'       => 'only',
            'addmember'     => true,
            'size' => '100'
        )
    );

    if (is_admin()) {
        $fields[] = new PMProRH_Field(
            'wp_user_point',
            'text',
            array(
                'label'         => 'User points',
                'hint'          => '',
                'profile'       => 'only',
                'addmember'     => true,
                'size' => '100'
            )
        );
    }

    $fields[] = new PMProRH_Field(
        'location',
        'text',
        array(
            'label'         => 'Location',
            'hint'          => '',
            'profile'       => 'only',
            'addmember'     => true,
            'size' => '100'
        )
    );

    $fields[] = new PMProRH_Field(
        'user_bio',
        'textarea',
        array(
            'label'         => 'Biography',
            'hint'          => '',
            'profile'       => 'only',
            'addmember'     => true,
            'rows'          => 4,
            'html'          => true,
            'maxlength'     => 350
        )
    );

    foreach ( $fields as $field ) {
        pmprorh_add_registration_field('user', $field);
    }
    return true;
}

add_action( 'init', 'pmprorh_init_user_profile' );

add_filter('template_include', 'intelldnt_link_template_include');

function intelldnt_link_template_include($template) {
    $manager = current_user_can('manage_options');
    if (!is_admin() && !$manager) {
        $current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if (strpos($current_url, '/user/')) {
            $template = dirname( __FILE__ ) . '/templates/user.php';;
        }
    }
    return $template;
}

function get_username_from_url() {
    if ( is_author() ) {
        $username = get_query_var( 'author_name' );
        if ( empty( $username ) ) {
            $username = get_query_var( 'author' );
        }
        return $username;
    }
    return false;
}

add_action( 'pmpro_personal_options_update', 'save_user_fields_in_profile' );

function save_user_fields_in_profile( $user_id ){
    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

    $profile_fields = pmpro_get_user_fields_for_profile($user_id);

    if(!empty($profile_fields)) {
        foreach($profile_fields as $field) {
            if( ! pmpro_is_field( $field ) ) {
                continue;
            }

            $field_name = $field->name;

            if (isset($_FILES[$field_name]['tmp_name']) && !empty($_FILES[$field_name]['tmp_name'])) {
                $file_info = $_FILES[$field_name];
                $allowed_image_formats = array('jpeg', 'jpg', 'png', 'gif', 'svg');
                $file_extension = pathinfo($file_info['name'], PATHINFO_EXTENSION);

                if (!in_array(strtolower($file_extension), $allowed_image_formats)) {
                    echo '<p>Allow image formats <span style="color:red">(jpeg, jpg, png, gif, svg).</span></p>';
                    call_user_func($field->save_function, $user_id, $field->name, array());
                    update_user_meta($user_id, 'wp_user_avatar', null);
                } else {
                    $user = get_userdata($user_id);
                    $upload_directory = wp_upload_dir();
                    $target_directory = $upload_directory['basedir'] . '/pmpro-register-helper/' . $user->user_login . '/';
                    $uploaded_file_name = pathinfo($file_info['name'], PATHINFO_FILENAME);

                    $files = scandir($target_directory);
                    $pattern = '/-\d+x\d+\.webp$|-\d+x\d+\.\w+$/';
                    $filename = preg_replace($pattern, '', $uploaded_file_name);
                    if (strpos($filename, ' ') !== false) {
                        $filename = str_replace(' ', '-', $filename);
                    }
                    foreach ($files as $file) {
                        if (strpos($file, $filename) === false) {
                            @unlink($target_directory . $file);
                        }
                    }
                }
            }
        }
    }
    return true;
}

