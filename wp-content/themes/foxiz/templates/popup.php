<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'foxiz_get_privacy' ) ) {
	/**
	 * @param string $text
	 * @param string $classes
	 *
	 * @return string
	 */
	function foxiz_get_privacy( $text = '', $classes = '' ) {

		$class_name = 'privacy-bar';
		if ( ! empty( $classes ) ) {
			$class_name .= ' ' . $classes;
		}

		$output = '<aside id="rb-privacy" class="' . esc_attr( $class_name ) . '">';
		$output .= '<div class="privacy-inner">';
		$output .= '<div class="privacy-content">';
		$output .= wp_kses( $text, 'foxiz' );
		$output .= '</div>';
		$output .= '<div class="privacy-dismiss">';
		$output .= '<a id="privacy-trigger" href="#" class="privacy-dismiss-btn is-btn"><span>' . foxiz_html__( 'Accept', 'foxiz' ) . '</span></a>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</aside>';

		return $output;
	}
}

if ( ! function_exists( 'foxiz_render_privacy' ) ) {
	/**
	 * @return false
	 */
	function foxiz_render_privacy() {

		if ( foxiz_is_amp() || empty( foxiz_get_option( 'privacy_bar' ) ) || empty( foxiz_get_option( 'privacy_text' ) ) ) {
			return false;
		}

		$text = foxiz_get_option( 'privacy_text' );

		$class_name = 'privacy-top';
		if ( ! empty( foxiz_get_option( 'privacy_position' ) ) ) {
			$class_name = 'privacy-' . foxiz_get_option( 'privacy_position' );
		}

		if ( ! empty( foxiz_get_option( 'privacy_width' ) ) && 'wide' === foxiz_get_option( 'privacy_width' ) ) {
			$class_name .= ' privacy-wide';
		}

		echo foxiz_get_privacy( $text, $class_name );
	}
}

if ( ! function_exists( 'foxiz_popup_newsletter' ) ) {
	function foxiz_popup_newsletter() {

		echo foxiz_get_popup_newsletter();
	}
}

if ( ! function_exists( 'foxiz_get_popup_newsletter' ) ) {
	/**
	 * @return false|string
	 */
	function foxiz_get_popup_newsletter() {

		if ( ! foxiz_get_option( 'newsletter_popup' ) || foxiz_is_amp() ) {
			return false;
		}

		$title       = foxiz_get_option( 'newsletter_title' );
		$description = foxiz_get_option( 'newsletter_description' );
		$shortcode   = foxiz_get_option( 'newsletter_shortcode' );
		$footer      = foxiz_get_option( 'newsletter_footer' );
		$footer_url  = foxiz_get_option( 'newsletter_footer_url' );
		$cover       = foxiz_get_option( 'newsletter_cover' );
		$display     = foxiz_get_option( 'newsletter_popup_display' );
		$offset      = foxiz_get_option( 'newsletter_popup_offset' );
		$delay       = foxiz_get_option( 'newsletter_popup_delay' );
		$expired     = foxiz_get_option( 'newsletter_popup_expired' );

		$output = '<aside id="rb-popup-newsletter" class="popup-newsletter mfp-animation mfp-hide light-scheme" data-display="' . esc_attr( $display ) . '" data-delay="' . absint( $delay ) . '" data-expired="' . absint( $expired ) . '" data-offset="' . absint( $offset ) . '">';
		$output .= '<div class="popup-newsletter-inner">';
		$output .= '<div class="popup-newsletter-cover">';
		$output .= '<div class="popup-newsletter-cover-holder">';
		if ( ! empty( $cover['url'] ) ) {
			$cover_size = foxiz_get_image_size( $cover['url'] );
			$output     .= '<img loading="lazy" decoding="async" class="popup-newsletter-img" src="' . esc_url( $cover['url'] ) . '" alt="' . ( ! empty( $cover['alt'] ) ? esc_attr( $cover['alt'] ) : '' ) . '" ';
			if ( ! empty( $cover_size[3] ) ) {
				$output .= $cover_size[3];
			}
			$output .= '/>';
		}
		$output .= '</div></div>';
		$output .= '<div class="popup-newsletter-content">';
		$output .= '<div class="popup-newsletter-header">';
		$output .= '<h6 class="popup-newsletter-heading h1">' . esc_html( $title ) . '<span class="popup-newsletter-icon"><i class="rbi rbi-plane"></i></span></h6>';
		$output .= '<p class="popup-newsletter-description">' . wp_kses( $description, 'foxiz' ) . '</p>';
		$output .= '';
		$output .= '</div>';
		$output .= '<div class="popup-newsletter-shortcode">';
		if ( do_shortcode( $shortcode ) ) {
			$output .= do_shortcode( $shortcode );
		} elseif ( current_user_can( 'manage_options' ) ) {
			$output .= '<p class="rb-error">' . esc_html__( 'The short code is incorrect or empty form. Please check the setting again!', 'foxiz' ) . '</p>';
		}
		$output .= '</div>';
		if ( ! empty( $footer ) ) {
			$output .= '<div class="popup-newsletter-footer">';
			if ( ! empty( $footer_url ) ) {
				$output .= '<a class="is-meta" href="' . esc_url( $footer_url ) . '">' . esc_html( $footer ) . '</a>';
			} else {
				$output .= '<span class="is-meta">' . esc_html( $footer ) . '</span>';
			}
			$output .= '</div>';
		}
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</aside>';

		return $output;
	}
}

if ( ! function_exists( 'foxiz_render_popup_login_form' ) ) {
	/**
	 * @return false
	 */
	function foxiz_render_popup_login_form() {

		$settings = foxiz_get_option();

		if ( ! empty( $settings['disable_login_popup'] ) || is_user_logged_in() || foxiz_is_amp() ) {
			return false;
		}

		$classes = 'user-login-form';
		$args    = [ 'form_id' => 'popup-form' ];

		if ( get_option( 'users_can_register', false ) ) {
			$classes .= ' can-register';
			if ( empty( $settings['login_register'] ) ) {
				$settings['login_register'] = wp_registration_url();
			}
		}

		if ( empty( $settings['login_forget'] ) ) {
			$settings['login_forget'] = wp_lostpassword_url();;
		}
		if ( ! empty( $settings['login_redirect'] ) ) {
			$args['redirect'] = $settings['login_redirect'];
		} else {
			$args['redirect'] = foxiz_get_current_permalink();
		} ?>
		<div id="rb-user-popup-form" class="rb-user-popup-form mfp-animation mfp-hide">
			<div class="logo-popup-outer">
				<div class="logo-popup">
					<div class="login-popup-header">
						<?php if ( ! empty( $settings['header_login_logo']['url'] ) ) : ?>
							<div class="logo-popup-logo">
								<?php if ( ! empty( $settings['header_login_dark_logo']['url'] ) ) : ?>
									<img loading="lazy" decoding="async" data-mode="default" src="<?php echo esc_url( $settings['header_login_logo']['url'] ); ?>" alt="<?php echo esc_attr( $settings['header_login_logo']['alt'] ) ?>" height="<?php echo esc_attr( $settings['header_login_logo']['height'] ) ?>" width="<?php echo esc_attr( $settings['header_login_logo']['width'] ) ?>"/>
									<img loading="lazy" decoding="async" data-mode="dark" src="<?php echo esc_url( $settings['header_login_dark_logo']['url'] ); ?>" alt="<?php echo esc_attr( $settings['header_login_dark_logo']['alt'] ) ?>" height="<?php echo esc_attr( $settings['header_login_dark_logo']['height'] ) ?>" width="<?php echo esc_attr( $settings['header_login_dark_logo']['width'] ) ?>"/>
								<?php else : ?>
									<img loading="lazy" decoding="async" src="<?php echo esc_url( $settings['header_login_logo']['url'] ); ?>" alt="<?php echo ! empty( $settings['header_login_logo']['alt'] ) ? esc_attr( $settings['header_login_logo']['alt'] ) : ''; ?>" height="<?php echo esc_attr( $settings['header_login_logo']['height'] ) ?>" width="<?php echo esc_attr( $settings['header_login_logo']['width'] ) ?>"/>
								<?php endif; ?>
							</div>
						<?php endif;
						if ( ! empty( $settings['header_login_heading'] ) ) : ?>
							<span class="logo-popup-heading h3"><?php echo esc_html( $settings['header_login_heading'] ); ?></span>
						<?php endif;
						if ( ! empty( $settings['header_login_description'] ) ) : ?>
							<p class="logo-popup-description is-meta"><?php echo esc_html( $settings['header_login_description'] ); ?></p>
						<?php endif; ?>
					</div>
					<div class="<?php echo esc_attr( $classes ); ?>"><?php wp_login_form( $args ); ?>
						<div class="login-form-footer is-meta">
							<?php if ( ! empty( $settings['login_register'] ) ) {
								printf( '<a href="%s">%s</a>', esc_url( $settings['login_register'] ), foxiz_html__( 'Register', 'foxiz' ) );
							} ?>
							<a href="<?php echo esc_url( $settings['login_forget'] ); ?>"><?php foxiz_html_e( 'Lost your password?', 'foxiz' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php }
}


if ( ! function_exists( 'foxiz_adblock_popup' ) ) {
	function foxiz_adblock_popup() {

		echo foxiz_get_adblock_popup();
	}
}

if ( ! function_exists( 'foxiz_get_adblock_popup' ) ) {
	/**
	 * @return false|string
	 */
	function foxiz_get_adblock_popup() {

		if ( ! foxiz_get_option( 'adblock_detector' ) ) {
			return false;
		}

		$title       = foxiz_get_option( 'adblock_title' );
		$description = foxiz_get_option( 'adblock_description' );

		$output = '<div id="rb-checktag"><div id="google_ads_iframe_checktag" class="adbanner rb-adbanner"><img loading="lazy" decoding="async" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" alt="adbanner"/></div></div>';
		$output .= '<aside id="rb-site-access" class="site-access-popup mfp-animation mfp-hide light-scheme">';
		$output .= '<div class="site-access-inner">';
		$output .= '<div class="site-access-image"><i class="rbi rbi-lock"></i></div>';
		if ( ! empty( $title ) ) {
			$output .= '<div class="site-access-title h2">' . esc_html( $title ) . '</div>';
		}
		if ( ! empty( $description ) ) {
			$output .= '<div class="site-access-description">' . wp_kses( $description, 'foxiz' ) . '</div>';
		}
		$output .= '<div class="site-access-btn"><a class="is-btn" href="' . foxiz_get_current_permalink() . '">' . foxiz_html__( 'Okay, I\'ll Whitelist' ) . '</a>' . '</div>';
		$output .= '</div>';
		$output .= '</aside>';

		return $output;
	}
}

if ( ! function_exists( 'foxiz_footer_slide_up' ) ) {
	function foxiz_footer_slide_up() {

		echo foxiz_get_footer_slide_up();
	}
}

if ( ! function_exists( 'foxiz_get_footer_slide_up' ) ) {
	/**
	 * @return false|string
	 */
	function foxiz_get_footer_slide_up() {

		if ( ! foxiz_get_option( 'footer_slide_up' ) || ! foxiz_get_option( 'slide_up_shortcode' ) || foxiz_is_amp() ) {
			return false;
		}

		$delay   = foxiz_get_option( 'slide_up_delay' );
		$expired = foxiz_get_option( 'slide_up_expired' );

		if ( empty( $expired ) ) {
			$expired = 1;
		} elseif ( '-1' === (string) $expired ) {
			$expired = 0;
		}

		if ( empty( $delay ) ) {
			$delay = 2000;
		}
		$output = '<aside id="footer-slideup" class="f-slideup" data-delay="' . intval( $delay ) . '" data-expired="' . intval( $expired ) . '">';
		$output .= '<a href="#" class="slideup-toggle"><i class="rbi rbi-angle-up"></i></a>';
		$output .= '<div class="slideup-inner">';
		$output .= do_shortcode( foxiz_get_option( 'slide_up_shortcode' ) );
		$output .= '</div>';
		$output .= '</aside>';

		return $output;
	}
}