<?php
/**
 * Plugin Name: foxiz entry meta date
 * Plugin URI: https://itsmeit.co
 * Description: Phát triển bởi itsmeit.co
 * Author: itsmeit.co
 * Author URI: https://itsmeit.co
 */

if ( ! function_exists( 'foxiz_entry_meta_date' ) ) {
    /**
     * @param array $settings
     */
    function foxiz_entry_meta_date( $settings ) {

        $post_id = get_the_ID();
        $p_label = '';
        $s_label = '';
        $classes = [];

        if ( ! isset( $settings['human_time'] ) ) {
            $settings['human_time'] = foxiz_get_option( 'human_time' );
        }
        if ( ! empty( $settings['p_label_date'] ) ) {
            $p_label = $settings['p_label_date'];
        } elseif ( ! empty( $settings['has_date_label'] ) && empty( $settings['human_time'] ) ) {
            $p_label = foxiz_html__( 'Published', 'foxiz' ) . ' ';
        }

        if ( ! empty( $settings['s_label_date'] ) ) {
            $s_label = $settings['s_label_date'];
        }

        if ( ! empty( $settings['human_time'] ) ) {
            $date_string = sprintf( foxiz_html__( '%s ago', 'foxiz' ), human_time_diff( get_post_time( 'U', true, $post_id ) ) );
        } else {
            $date_string = get_the_date( null, $post_id );
        }

        $current_language = pll_current_language();
        if ($current_language === 'vi') {
            $date_string = sprintf( foxiz_html__( 'Ngày %s', 'foxiz' ), date_i18n( 'd/m/Y', get_post_time( 'U', true, $post_id ) ) );
        }

        $classes[] = 'meta-el meta-date';
        if ( ! empty( $settings['tablet_hide_meta'] ) && is_array( $settings['tablet_hide_meta'] ) && in_array( 'date', $settings['tablet_hide_meta'] ) ) {
            $classes[] = 'tablet-hide';
        }
        if ( ! empty( $settings['mobile_hide_meta'] ) && is_array( $settings['mobile_hide_meta'] ) && in_array( 'date', $settings['mobile_hide_meta'] ) ) {
            $classes[] = 'mobile-hide';
        }
        if ( ! empty( $settings['mobile_last'] ) && 'date' === $settings['mobile_last'] ) {
            $classes[] = 'mobile-last-meta';
        }
        if ( ! empty( $settings['tablet_last'] ) && 'date' === $settings['tablet_last'] ) {
            $classes[] = 'tablet-last-meta';
        }
        ?><span class="<?php echo join( ' ', $classes ); ?>">
        <?php if ( foxiz_get_option( 'meta_date_icon' ) ) {
            echo '<i class="rbi rbi-clock" aria-hidden="true"></i>';
        }
        ?>
        <time <?php if ( ! foxiz_get_option( 'force_modified_date' ) ) {
            echo 'class="date published"';
        } ?> datetime="<?php echo get_the_date( DATE_W3C, $post_id ); ?>"><?php echo esc_html( $p_label . $date_string . $s_label ); ?></time>
        </span><?php
    }
}

if ( ! function_exists( 'foxiz_entry_meta_updated' ) ) {
    /**
     * @param $settings
     */
    function foxiz_entry_meta_updated( $settings ) {

        $post_id = get_the_ID();
        $p_label = '';
        $s_label = '';
        $classes = [ 'meta-el meta-update' ];

        if ( ! isset( $settings['human_time'] ) ) {
            $settings['human_time'] = foxiz_get_option( 'human_time' );
        }
        if ( ! empty( $settings['p_label_update'] ) ) {
            $p_label = $settings['p_label_update'];
        } elseif ( ! empty( $settings['has_date_label'] ) && empty( $settings['human_time'] ) ) {
            $p_label = foxiz_html__( 'Last updated:', 'foxiz' ) . ' ';
        }
        if ( ! empty( $settings['s_label_date'] ) ) {
            $s_label = $settings['s_label_date'];
        }
        if ( ! empty( $settings['human_time'] ) ) {
            $date_string = sprintf( foxiz_html__( '%s ago', 'foxiz' ), human_time_diff( get_post_modified_time( 'U', true, $post_id ) ) );
            $classes[]   = 'human-format';
        } else {
            $date_string = get_the_modified_date( '', $post_id );
        }

        $current_language = pll_current_language();
        if ($current_language === 'vi') {
            $date_string = sprintf( foxiz_html__( 'Ngày %s', 'foxiz' ), date_i18n( 'd/m/Y', get_post_time( 'U', true, $post_id ) ) );
        }

        if ( ! empty( $settings['tablet_hide_meta'] ) && is_array( $settings['tablet_hide_meta'] ) && in_array( 'update', $settings['tablet_hide_meta'] ) ) {
            $classes[] = 'tablet-hide';
        }
        if ( ! empty( $settings['mobile_hide_meta'] ) && is_array( $settings['mobile_hide_meta'] ) && in_array( 'update', $settings['mobile_hide_meta'] ) ) {
            $classes[] = 'mobile-hide';
        }
        if ( ! empty( $settings['mobile_last'] ) && 'update' === $settings['mobile_last'] ) {
            $classes[] = 'mobile-last-meta';
        }
        if ( ! empty( $settings['tablet_last'] ) && 'update' === $settings['tablet_last'] ) {
            $classes[] = 'tablet-last-meta';
        }
        ?><span class="<?php echo join( ' ', $classes ); ?>">
        <?php if ( foxiz_get_option( 'meta_updated_icon' ) ) {
            echo '<i class="rbi rbi-time" aria-hidden="true"></i>';
        } ?>
        <time <?php if ( ! foxiz_get_option( 'force_modified_date' ) ) {
            echo 'class="updated"';
        } ?> datetime="<?php echo get_the_modified_date( DATE_W3C, $post_id ); ?>"><?php echo esc_html( $p_label . $date_string . $s_label ); ?></time>
        </span>
        <?php
    }
}