<?php
/**
 * Plugin Name: Mobile login
 * Plugin URI: https://itsmeit.co
 * Description: Phát triển bởi itsmeit.co
 * Author: itsmeit.co
 * Author URI: https://itsmeit.co
 */

if ( ! function_exists( 'foxiz_mobile_collapse' ) ) {
    function foxiz_mobile_collapse( $settings = [] ) {

        $settings['ajax_search'] = false;
        $login_redirect          = foxiz_get_option( 'login_redirect' );
        $logout_redirect         = foxiz_get_option( 'logout_redirect' );

        if ( empty( $login_redirect ) ) {
            $login_redirect = foxiz_get_current_permalink();
        } ?>
        <div class="mobile-collapse">
            <div class="collapse-holder">
                <div class="collapse-inner">
                    <?php if ( foxiz_get_option( 'mobile_search_form' ) ) : ?>
                        <div class="mobile-search-form edge-padding"><?php foxiz_header_search_form( $settings ); ?></div>
                    <?php endif; ?>
                    <nav class="mobile-menu-wrap edge-padding">
                        <?php wp_nav_menu( [
                            'theme_location' => 'foxiz_mobile',
                            'menu_id'        => 'mobile-menu',
                            'menu_class'     => 'mobile-menu',
                            'container'      => false,
                            'depth'          => 2,
                            'echo'           => true,
                            'fallback_cb'    => 'foxiz_navigation_fallback',
                            'fallback_name'  => esc_html__( 'Mobile Menu', 'foxiz' ),
                        ] ); ?>
                    </nav>
                    <?php if ( ! empty( $settings['collapse_template'] ) ) {
                        echo '<div class="collapse-template">' . do_shortcode( trim( $settings['collapse_template'] ) ) . '</div>';
                    }
                    ?>
                    <div class="collapse-sections edge-padding">
                        <?php if ( ! empty( $settings['mobile_login'] ) && ! is_user_logged_in() && ! foxiz_is_amp() ) : ?>
                            <div class="mobile-login">
                                <span class="mobile-login-title h6"><?php
                                    if ( foxiz_get_option( 'mobile_login_label' ) ) {
                                        echo esc_html( foxiz_get_option( 'mobile_login_label' ) );
                                    } else {
                                        foxiz_html_e( 'Have an existing account?', 'foxiz' );
                                    } ?></span>
                                <a rel="nofollow noopener" href="<?php echo wp_login_url( $login_redirect ); ?>" class="login-toggle is-login is-btn"><?php foxiz_html_e( 'Sign In', 'foxiz' ); ?></a>
                            </div>
                        <?php else:
                            global $current_user; ?>
                            <div class="mobile-logout">
                                <span class="mobile-logout-title h6">You are logged in: <?= esc_html($current_user->display_name) ?>
                                <a rel="nofollow noopener" class="mobile-logout-url" style="color: #0b4aa2; font-weight: 600;" href="<?php echo wp_logout_url( $logout_redirect ); ?>"><?php foxiz_html_e( '(Logout)' ); ?></a>
                                </span>
                            </div>
                        <?php endif;
                        if ( ! empty( $settings['mobile_social'] ) ) : ?>
                            <div class="mobile-socials">
                                <span class="mobile-social-title h6"><?php foxiz_html_e( 'Follow US', 'foxiz' ); ?></span>
                                <?php echo foxiz_get_social_list( $settings ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ( ! empty( $settings['mobile_footer_menu'] ) || ! empty( $settings['mobile_copyright'] ) ) : ?>
                        <div class="collapse-footer">
                            <?php if ( ! empty( $settings['mobile_footer_menu'] ) ) : ?>
                                <div class="collapse-footer-menu"><?php
                                    wp_nav_menu( [
                                        'menu'        => $settings['mobile_footer_menu'],
                                        'menu_id'     => false,
                                        'container'   => false,
                                        'menu_class'  => 'collapse-footer-menu-inner',
                                        'depth'       => 1,
                                        'echo'        => true,
                                        'fallback_cb' => '__return_false',
                                    ] );
                                    ?></div>
                            <?php endif;
                            if ( ! empty( $settings['mobile_copyright'] ) ) : ?>
                                <div class="collapse-copyright"><?php echo wp_kses( $settings['mobile_copyright'], 'foxiz' ); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php }
}
