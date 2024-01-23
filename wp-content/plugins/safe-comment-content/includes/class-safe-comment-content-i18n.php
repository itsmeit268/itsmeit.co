<?php

/**
 * @link       https://itsmeit.co
 * @package    Safe_Comment_Content
 * @subpackage Safe_Comment_Content/public
 * @author     itsmeit | Technology Blogs <admin@itsmeit.co>
 */
class Safe_Comment_Content_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'safe-comment-content',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
