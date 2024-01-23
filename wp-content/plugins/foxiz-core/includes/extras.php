<?php
/** Don't load directly */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'rb_get_covid_data' ) ) {
	/**
	 * @param string $country
	 *
	 * @return int[]|mixed
	 */
	function rb_get_covid_data( $country = 'all' ) {

		$data = get_transient( 'rb_covid_' . $country );

		if ( ! empty( $data['confirmed'] ) || ! empty( $data['deaths'] ) ) {
			return $data;
		} else {
			delete_transient( 'rb_covid_' . $country );
		}

		$data = [
			'confirmed' => 0,
			'deaths'    => 0,
		];

		$params = [
			'sslverify' => true,
			'timeout'   => 100,
		];

		if ( 'all' === $country ) {
			$response = wp_remote_get( 'https://disease.sh/v3/covid-19/all?yesterday=false&allowNull=true', $params );
		} else {
			$response = wp_remote_get( 'https://disease.sh/v3/covid-19/countries/' . esc_attr( $country ) . '?yesterday=true&strict=false', $params );
		}

		if ( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
			$response = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! empty( $response->cases ) ) {
				$data['confirmed'] = $response->cases;
			}
			if ( ! empty( $response->deaths ) ) {
				$data['deaths'] = $response->deaths;
			}
		} else {
			$data = [
				'confirmed' => - 1,
				'deaths'    => - 1,
			];
		}

		set_transient( 'rb_covid_' . $country, $data, 43200 );

		return $data;
	}
}

if ( ! function_exists( 'foxiz_render_covid_data' ) ) {
	/**
	 * @param $attrs
	 *
	 * @return false|string
	 */
	function foxiz_render_covid_data( $attrs ) {

		$settings = shortcode_atts( [
			'country_code'    => '',
			'country_name'    => '',
			'title_tag'       => '',
			'icon'            => '',
			'confirmed_label' => foxiz_html__( 'Confirmed', 'foxiz-core' ),
			'death_label'     => foxiz_html__( 'Death', 'foxiz-core' ),
			'confirmed'       => '',
			'deaths'          => '',
			'date'            => '1',
		], $attrs );

		if ( empty( $settings['country_code'] ) ) {
			$settings['country_code'] = 'all';
		}

		if ( empty( $settings['title_tag'] ) ) {
			$settings['title_tag'] = 'h3';
		}

		$data = rb_get_covid_data( trim( $settings['country_code'] ) );

		if ( - 1 === $data['confirmed'] ) {
			$data['confirmed'] = $settings['confirmed'];
			$data['deaths']    = $settings['deaths'];
		}

		ob_start();
		?>
		<div class="block-covid-data">
			<div class="data-inner">
				<?php if ( ! empty( $settings['country_name'] ) ) {
					echo '<div class="country-name"><' . esc_attr( $settings['title_tag'] ) . '>' . esc_html( $settings['country_name'] ) . '</' . esc_attr( $settings['title_tag'] ) . '></div>';
				} ?>
				<div class="data-item data-confirmed">
					<p class="description-text">
						<span class="data-item-icon"><?php foxiz_render_svg( 'chart' ); ?></span><?php echo esc_html( $settings['confirmed_label'] ); ?>
					</p>
					<p class="data-item-value h5"><?php echo foxiz_pretty_number( $data['confirmed'] ); ?></p>
				</div>
				<div class="data-item data-death">
					<p class="description-text">
						<span class="data-item-icon"><?php foxiz_render_svg( 'chart' ); ?></span><?php echo esc_html( $settings['death_label'] ); ?>
					</p>
					<p class="data-item-value h5"><?php echo foxiz_pretty_number( $data['deaths'] ); ?></p>
				</div>
				<?php if ( ! empty( $settings['icon'] ) ) {
					foxiz_render_svg( 'virus' );
				} ?>
			</div>
		</div>
		<?php return ob_get_clean();
	}
}

if ( ! function_exists( 'foxiz_render_pricing_plan' ) ) {
	/**
	 * @param $settings
	 *
	 * @return string
	 */
	function foxiz_render_pricing_plan( $settings ) {

		$output = '';

		if ( empty( $settings['box_style'] ) ) {
			$settings['box_style'] = 'shadow';
		}

		$classes = 'plan-box is-box-' . $settings['box_style'];
		if ( ! empty( $settings['color_scheme'] ) ) {
			$classes .= ' light-scheme';
		}

		$output .= '<div class="' . esc_attr( $classes ) . '"><div class="plan-inner">';
		$output .= '<div class="plan-header">';
		if ( ! empty( $settings['title'] ) ) {
			$output .= '<h2 class="plan-heading">' . wp_kses( $settings['title'], 'foxiz' ) . '</h2>';
		}
		if ( ! empty( $settings['description'] ) ) {
			$output .= '<p class="plan-description">' . wp_kses( $settings['description'], 'foxiz' ) . '</p>';
		}
		$output .= '</div>';

		if ( ! empty( $settings['price'] ) ) {
			$output .= '<div class="plan-price-wrap h6">';
			if ( ! empty( $settings['unit'] ) ) {
				$output .= '<span class="plan-price-unit">' . esc_html( $settings['unit'] ) . '</span>';
			}
			$output .= '<span class="plan-price">' . esc_html( $settings['price'] ) . '</span>';
			if ( ! empty( $settings['tenure'] ) ) {
				$output .= '<span class="plan-tenure">' . esc_html( $settings['tenure'] ) . '</span>';
			}
			$output .= '</div>';
		}

		if ( is_array( $settings['features'] ) ) {
			$output .= '<div class="plan-features">';
			foreach ( $settings['features'] as $feature ) {
				if ( ! empty( $feature['feature'] ) ) {
					$output .= '<span class="plan-feature">' . esc_html( $feature['feature'] ) . '</span>';
				}
			}
			$output .= '</div>';
		}
		$output .= '<div class="plan-button-wrap">';
		if ( ! empty( $settings['shortcode'] ) ) {
			$output .= do_shortcode( $settings['shortcode'] );
		} elseif ( ! empty( $settings['register_button'] ) && class_exists( 'SwpmSettings' ) ) {
			$output .= '<a class="button" href="' . SwpmSettings::get_instance()->get_value( 'registration-page-url' ) . '">' . esc_html( $settings['register_button'] ) . '</a>';
		}
		$output .= '</div>';

		$output .= '</div></div>';

		return $output;
	}
}