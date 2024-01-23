<?php
/**
 * Plugin Name: footer menu multiple language
 * Plugin URI: https://itsmeit.co
 * Description: Phát triển bởi itsmeit.co
 * Author: itsmeit.co
 * Author URI: https://itsmeit.co
 */

if ( ! function_exists( 'foxiz_get_footer_copyright' ) ) {
    function foxiz_get_footer_copyright() {

        $copyright = foxiz_get_option( 'copyright' );
        $menu      = foxiz_get_option( 'footer_menu' );
        $current_language = pll_current_language();

        if ($current_language === 'vi') {
            $menu = '1229';
        }

        if ( foxiz_is_amp() ) {
            $social = foxiz_get_option( 'amp_footer_social' );
            $logo   = foxiz_get_option( 'amp_footer_logo' );

            /** unset copyright */
            if ( ! foxiz_get_option( 'amp_copyright' ) ) {
                $copyright = $menu = false;
            }
        } else {
            $social    = foxiz_get_option( 'footer_social' );
            $logo      = foxiz_get_option( 'footer_logo' );
            $dark_logo = foxiz_get_option( 'dark_footer_logo' );
        }

        ob_start();
        if ( ! empty( $logo['url'] ) || ! empty( $social ) ) : ?>
            <div class="bottom-footer-section">
                <?php if ( ! empty( $logo['url'] ) ) : ?>
                    <a class="footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>">
                        <?php if ( empty( $dark_logo['url'] ) ) {
                            $dark_logo = $logo;
                        }
                        echo foxiz_get_logo_html( $logo, false, 'logo-default', 'default', 'lazy' );
                        echo foxiz_get_logo_html( $dark_logo, false, 'logo-dark', 'dark', 'lazy' );
                        ?>
                    </a>
                <?php endif;
                if ( ! empty( $social ) ) : ?>
                    <div class="footer-social-list">
                        <span class="footer-social-list-title h6"><?php foxiz_html_e( 'Follow US', 'foxiz' ); ?></span>
                        <?php echo foxiz_get_social_list( foxiz_get_option() ); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif;
        if ( ! empty( $copyright ) || ! empty( $menu ) ) : ?>
            <div class="copyright-inner">
                <?php
                if ( ! empty( $copyright ) ) {
                    echo '<div class="copyright">' . wp_kses( $copyright, 'foxiz' ) . '</div>';
                }
                if ( ! empty( $menu ) && is_nav_menu( $menu ) ) {
                    wp_nav_menu( [
                        'menu'        => $menu,
                        'menu_id'     => 'copyright-menu',
                        'menu_class'  => 'copyright-menu',
                        'container'   => false,
                        'depth'       => 1,
                        'echo'        => true,
                        'fallback_cb' => '__return_false',
                    ] );
                } ?>
            </div>
        <?php endif;

        return ob_get_clean();
    }
}