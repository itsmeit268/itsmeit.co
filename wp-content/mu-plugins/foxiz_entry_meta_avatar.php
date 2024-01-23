<?php
/**
 * Plugin Name: Entry Meta Avata
 * Plugin URI: https://itsmeit.biz
 * Description: Phát triển bởi itsmeit.biz
 * Author: itsmeit.biz
 * Author URI: https://itsmeit.biz
 */

if ( ! function_exists( 'foxiz_entry_meta_avatar' ) ) {
    /**
     * @param $settings
     */
    function foxiz_entry_meta_avatar( $settings ) {

        $post_id = get_the_ID();
        $classes = array();
        if ( empty( $settings['avatar_size'] ) ) {
            $settings['avatar_size'] = 44;
        }
        $classes[] = 'meta-el meta-avatar';
        if ( ! empty( $settings['tablet_hide_meta'] ) && is_array( $settings['tablet_hide_meta'] ) && in_array( 'avatar', $settings['tablet_hide_meta'] ) ) {
            $classes[] = 'tablet-hide';
        }
        if ( ! empty( $settings['mobile_hide_meta'] ) && is_array( $settings['mobile_hide_meta'] ) && in_array( 'avatar', $settings['mobile_hide_meta'] ) ) {
            $classes[] = 'mobile-hide';
        }
        if ( function_exists( 'get_post_authors' ) ) {
            $author_data = get_post_authors( $post_id );
            if ( is_array( $author_data ) && count( $author_data ) >= 1 ) {
                $classes[] = 'meta-el multiple-meta-avatar';
                ?>
                <span class="<?php echo implode( ' ', $classes ); ?>">
					<?php foreach ( $author_data as $author ) : ?><?php echo get_avatar( $author->ID, absint( $settings['avatar_size'] ), '', get_the_author_meta( 'display_name', $author->ID ) ); ?><?php endforeach; ?>
			    </span>
                <?php return;
            }
        }
        $author_id = get_post_field( 'post_author', $post_id ); ?>
        <a class="<?php echo implode( ' ', $classes ); ?>" href="<?php echo get_author_posts_url( $author_id ); ?>" aria-label="Meta Avata"><?php echo get_avatar( $author_id, absint( $settings['avatar_size'] ), '', get_the_author_meta( 'display_name', $author_id ) ); ?></a>
        <?php
    }
}