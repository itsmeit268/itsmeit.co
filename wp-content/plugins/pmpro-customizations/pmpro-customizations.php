<?php
/*
Plugin Name: PMPro Customizations
Plugin URI: https://www.paidmembershipspro.com/wp/pmpro-customizations/
Description: Customizations for my Paid Memberships Pro Setup
Version: .1
Author: Paid Memberships Pro
Author URI: https://www.paidmembershipspro.com
*/

//Now start placing your customization code below this line

// Disables the pmpro redirect to levels page when user tries to register
add_filter( 'pmpro_login_redirect', '__return_false' );

function pmpro_default_registration_level( $user_id ) {
    // Give all members who register membership level 1
    pmpro_changeMembershipLevel( 1, $user_id );
}

add_action( 'user_register', 'pmpro_default_registration_level' );

function show_level() {
    return '<span class="show-level" style="font-weight: 600;"></span>';
}

add_action('wp_ajax_show_level_callback', 'show_level_callback');
add_action('wp_ajax_nopriv_show_level_callback', 'show_level_callback');

function show_level_callback(){
    $current_user = wp_get_current_user();
    $response['level'] = isset($current_user->membership_level->name) ? $current_user->membership_level->name : 'FREE';
    wp_send_json_success($response);
    exit();
}

function get_user_id() {
    $current_user_id = wp_get_current_user()->ID;
    return $current_user_id ? : get_current_user_id();
}

function last_member_order_success() {
    global $wpdb;

    $free_level = 1;
    $table_name = $wpdb->prefix . 'pmpro_membership_orders';
    // Lấy thông tin về đơn hàng mới nhất có trạng thái "success" và không phải là cấp độ "FREE"
    $query = $wpdb->prepare(
        "SELECT * FROM {$table_name} WHERE user_id = %d AND membership_id != %d AND status LIKE %s ORDER BY timestamp DESC LIMIT 1",
        get_user_id(),
        $free_level,
        'success'
    );
    $order = $wpdb->get_row($query);
    if (isset($order->status) && $order->status) {
        return $order->status;
    }

    return false;
}

function get_level_name(){
    $mylevels = pmpro_getMembershipLevelsForUser();

    $level_name = 'FREE';
    if (is_array($mylevels) && !empty($mylevels)) {
        foreach($mylevels as $level) {
            $level_name = $level->name;
        }
    }

    return $level_name;
}

function get_account_status() {
    $account_status = PMPro_Approvals::getUserApprovalStatus( get_user_id(), null, false ) ?? null;
    return $account_status;
}

function vip_level() {
    $order_success = last_member_order_success();

    if (get_level_name() !== 'VIP') {
        return false;
    }

    if (isset($order_success) && $order_success !== "success") {
        return false;
    }

    if (get_account_status() == 'Pending Approval for VIP' || get_account_status() == 'Đang chờ phê duyệt VIP') {
        return false;
    }

    return true;
}

function premium_level() {
    $order_success = last_member_order_success();

    if (get_level_name() !== 'PREMIUM') {
        return false;
    }


    if (isset($order_success) && $order_success !== "success") {
        return false;
    }

    if (get_account_status() == 'Pending Approval for PREMIUM' || get_account_status() == 'Đang chờ phê duyệt PREMIUM') {
        return false;
    }

    return true;
}

function free_level() {
    if (!vip_level() && !premium_level()) {
        return true;
    }
    return false;
}

function member_level($post_id) {
    $options = get_post_meta($post_id, 'member_level', true);
    $member_level = json_decode($options, true);

    $level_name = 'free';
    if (is_array($member_level) && !empty($member_level)) {
        foreach ($member_level as $level => $value) {
            if ($value === 'on')
                $level_name = $level;
        }
    }
    return $level_name;
}

function is_allow_show_ads() {
    $manager = current_user_can('manage_options');
    $IP = array('127.0.0.1', '127.0.1.1', 'localhost');
    if (!$manager && !in_array($_SERVER['REMOTE_ADDR'], $IP) && free_level()) {
        return true;
    }
    return false;
}

function account_status_render_html($post_id) {
    $account_status = get_account_status();
    $level = member_level($post_id);
    ?>
    <div class="not-vip-download" style="display: none">
        <?php if ($account_status == 'Pending Approval for VIP' || $account_status == 'Đang chờ phê duyệt VIP') : ?>
            <p class="require-level">Your account is <?= $account_status ?>,<a class="require-vip-download" href="<?= pmpro_url('levels'); ?>">click here</a> to check the status.</p>
        <?php elseif ($account_status == 'Pending Approval for PREMIUM' || $account_status == 'Đang chờ phê duyệt PREMIUM') :?>
            <p class="require-level">Your account is <?= $account_status ?>,<a class="require-vip-download" href="<?= pmpro_url('levels'); ?>">click here</a> to check the status.</p>
        <?php else: ?>
            <?php if ($level == 'vip') : ?>
                <p class="require-level">You need to be a <strong style="color:#ff0000;font-weight: 600;">VIP</strong> member to download this file,<a class="require-vip-download" href="<?= pmpro_url('levels'); ?>">click here</a> to register.</p>
            <?php elseif ($level == 'premium'):?>
                <p class="require-level">You need to purchase a <strong style="color:#ff3300;font-weight: 600;">PREMIUM</strong> membership to download this file,<a class="require-vip-download" href="<?= pmpro_url('levels'); ?>">click here</a> to register.</p>
            <?php endif;?>
        <?php endif; ?>
    </div>
    <?php
}

add_action('wp_enqueue_scripts', 'member_script_callback');
function member_script_callback() {
    $current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($current_url, '/my-account/') !== false ||
        strpos($current_url, 'my-account.html') !== false ||
        strpos($current_url, 'login.html') !== false
    ) {
        wp_enqueue_style('member-style', plugin_dir_url(__FILE__). 'css/member-style.css', array(), FOXIZ_THEME_VERSION, 'all');
        wp_enqueue_script('member-checkout', plugin_dir_url(__FILE__). 'js/member-checkout.js', array('jquery'), FOXIZ_THEME_VERSION, true);
    }
}

add_filter('pmpro_has_membership_access_filter', 'custom_pmpro_login_redirect', 10, 4);
function custom_pmpro_login_redirect($has_access, $mypost, $myuser, $post_membership_levels) {
    $current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($current_url, '/my-account/') && !is_user_logged_in() ) {
        $_SESSION['redirect_to'] = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        wp_redirect(get_bloginfo('url').'/admin_ma2405');
        exit();
    }

    return $has_access;
}

add_action('pmpro_after_checkout', 'approval_after_checkout', 10, 2);
function approval_after_checkout($user_id, $morder) {
    if ($morder->status === 'success') {
        PMPro_Approvals::approveMember( $user_id, $morder->membership_id, true );
        wp_cache_clean_cache( 'wp-cache-', true );
    }
}
