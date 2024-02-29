<?php

/**
 * @link       https://itsmeit.co
 * @package    Safe_Comment_Content
 * @subpackage Safe_Comment_Content/public
 * @author     itsmeit | Technology Blogs <admin@itsmeit.co>
 */
class Safe_Comment_Content_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'init', [$this, 'prep_link_rewrite_endpoint'], 10, 1);
		add_filter( 'preprocess_comment', [$this, 'safe_comment_content'], 10, 2 );
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/safe-comment-content.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		$endpoint = $this->endpoint_setting();
		wp_enqueue_script('clear-cookie', plugin_dir_url(__FILE__) . 'js/clear-cookie.js', array('jquery'), $this->version, false );
		wp_localize_script('clear-cookie', 'clear_cookie_vars', ['end_point'=> $endpoint]);

		if (!is_front_page() && is_singular('post')) {
			wp_enqueue_script( 'safe-comment-content', plugin_dir_url( __FILE__ ) . 'js/safe-comment-content.js', array('jquery'), $this->version, false );
			wp_localize_script('safe-comment-content', 'comment_vars', [
				'end_point'=> $endpoint,
				'bloginfo_url' => get_bloginfo('url')
			]);
		}
	}

	public function safe_comment_content($commentdata) {
        $manager = current_user_can('manage_options');
		$settings = get_option('scc_settings');
		$moderation_keys = $settings['scc_moderation'];
//		$moderation_keys = get_option('moderation_keys');
		if (!$moderation_keys || $manager) {
			return $commentdata;
		}

		$moderation_keys = preg_split('/\r\n|\r|\n|,/', $moderation_keys);

		if (isset($commentdata['comment_content'])) {
			$comment_content = $commentdata['comment_content'];
			foreach ($moderation_keys as $key) {
				$key_length = mb_strlen($key, 'UTF-8');
				if ($key_length > 2) {
					$encoded_key = mb_substr($key, 0, $key_length - 2, 'UTF-8') . '**';
					$comment_content = preg_replace('/\b' . preg_quote($key) . '\b/iu', $encoded_key, $comment_content);
				}
			}
			$commentdata['comment_content'] = $comment_content;
		}

		return $commentdata;
	}

	public function prep_link_rewrite_endpoint(){
		add_rewrite_endpoint($this->endpoint_setting(), EP_PERMALINK | EP_PAGES );

		add_filter('template_include', function($template) {
			global $wp_query;
			if (isset($wp_query->query_vars[$this->endpoint_setting()]) && is_singular('post')) {
				$this->set_robots_filter();
				return dirname( __FILE__ ) . '/templates/prep_link_template.php';
			}
			return $template;
		});
	}

	/**
	 * @return mixed|string
	 */
	public function endpoint_setting() {
		$settings = get_option('scc_settings', array());
		if (!empty($settings)) {
			return preg_replace('/[^\p{L}a-zA-Z0-9_\-.]/u', '', trim($settings['endpoint']));
		}
		return '1';
	}

	public function set_robots_filter() {
        if (!function_exists('aioseo' ) && !function_exists('wpseo_init' ) && !function_exists('rank_math' )) {
            $robots = array('noindex' => true, 'nofollow' => true, 'noarchive' => true, 'nosnippet' => true,);
            add_filter('wp_robots', function() use ($robots) {
                return $robots;
            });
        }

        $robots = array(
            'index' => 'noindex', 'follow' => 'nofollow',
            'archive' => 'noarchive', 'snippet' => 'nosnippet',
        );

        if (function_exists('rank_math' )){
            add_filter( 'rank_math/frontend/robots', function() use ($robots) {
                return $robots;
            });
        }

        if (function_exists('wpseo_init' )){
            add_filter( 'wpseo_robots', function() use ($robots) {
                return $robots;
            });
        }

        if (function_exists('aioseo' )){
            add_filter( 'aioseo_robots_meta', function() use ($robots) {
                return $robots;
            });
        }
	}
}
