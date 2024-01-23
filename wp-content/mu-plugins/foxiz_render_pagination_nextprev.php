<?php
/**
 * Plugin Name: Social Pagination Nextprev
 * Plugin URI: https://itsmeit.co
 * Description: Phát triển bởi itsmeit.co
 * Author: itsmeit.co
 * Author URI: https://itsmeit.co
 */

if ( ! function_exists( 'foxiz_render_pagination_nextprev' ) ) {
    /**
     * @param null $_query
     *
     * @return false
     */
    function foxiz_render_pagination_nextprev( $_query = null ) {

        if ( empty( $_query ) || ! is_object( $_query ) ) {
            global $wp_query;
            $_query = $wp_query;
        }
        if ( $_query->max_num_pages < 2 ) {
            return false;
        } ?>
        <div class="pagination-wrap pagination-nextprev">
            <a href="#" aria-label="<?php foxiz_html_e( 'Previous', 'foxiz' ); ?>" class="pagination-trigger ajax-prev is-disable" data-type="prev"><i class="rbi rbi-angle-left" aria-hidden="true"></i><span><?php foxiz_html_e( 'Previous', 'foxiz' ); ?></span></a>
            <a href="#" aria-label="<?php foxiz_html_e( 'Next', 'foxiz' ); ?>" class="pagination-trigger ajax-next" data-type="next"><span><?php foxiz_html_e( 'Next', 'foxiz' ); ?></span><i class="rbi rbi-angle-right" aria-hidden="true"></i></a>
        </div>
        <?php
    }
}
