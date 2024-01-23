<?php
/**
 * Plugin Name: Entry Excerpt Foxiz
 * Plugin URI: https://itsmeit.co
 * Description: Phát triển bởi itsmeit.co
 * Author: itsmeit.co
 * Author URI: https://itsmeit.co
 */

if ( ! function_exists( 'foxiz_entry_excerpt' ) ) {
    /**
     * @param array $settings
     *
     * @return false
     */
    function foxiz_entry_excerpt( $settings = array() ) {

        $classes = 'entry-summary';
        if ( ! empty( $settings['hide_excerpt'] ) ) {
            switch ( $settings['hide_excerpt'] ) {
                case 'mobile' :
                    $classes .= ' mobile-hide';
                    break;
                case 'tablet' :
                    $classes .= ' tablet-hide';
                    break;
                case 'all' :
                    $classes .= ' mobile-hide tablet-hide';
                    break;
            }
        }

        if ( ! empty( $settings['excerpt_source'] ) && 'moretag' === $settings['excerpt_source'] ) :
            $classes .= ' entry-content rbct'; ?>
            <p class="<?php echo esc_attr( $classes ); ?>"><?php the_content( '' ); ?></p>
        <?php else :
            if ( empty( $settings['excerpt_length'] ) || 0 > $settings['excerpt_length'] ) {
                return false;
            }
            if ( ! empty( $settings['excerpt_source'] ) && 'tagline' === $settings['excerpt_source'] && rb_get_meta( 'tagline' ) ) :
                $tagline = wp_trim_words( rb_get_meta( 'tagline' ), intval( $settings['excerpt_length'] ), '<span class="summary-dot">&hellip;</span>' ); ?>
                <p class="<?php echo esc_attr( $classes ); ?>"><?php echo wp_kses( $tagline, 'foxiz' ); ?></p>
            <?php else :
                $excerpt = get_post_field( 'post_excerpt', get_the_ID() );
                if (strpos($excerpt, '<table>') !== false || strpos($excerpt, '<tbody>') !== false) {
                    $excerpt = '';
                }
                if ( ! empty( $excerpt ) ) {
                    $output = wp_trim_words( $excerpt, intval( $settings['excerpt_length'] ), '<span class="summary-dot">&hellip;</span>' );
                }
                if ( empty( $output ) ) {

                    if ( 'page' === get_post_type() && get_post_meta( get_the_ID(), '_elementor_data', true ) ) {
                        return false;
                    }

                    $output = get_the_content( '' );
                    $output = strip_shortcodes( $output );
                    $output = excerpt_remove_blocks( $output );
                    $output = preg_replace( "~(?:\[/?)[^/\]]+/?\]~s", '', $output );
                    $output = str_replace( ']]>', ']]&gt;', $output );
                    $output = wp_strip_all_tags( $output );
                    $output = wp_trim_words( $output, intval( $settings['excerpt_length'] ), '<span class="summary-dot">&hellip;</span>' );
                }
                if ( empty( $output ) ) {
                    return false;
                }
                ?><p class="<?php echo esc_attr( $classes ); ?>"><?php echo wp_kses( $output, 'foxiz' ); ?></p>
            <?php endif;
        endif;
    }
}