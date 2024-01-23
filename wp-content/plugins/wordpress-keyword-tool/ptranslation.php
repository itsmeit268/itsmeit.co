<?php
function wp_keyword_tool_action_init()
{
	// Localization
	load_plugin_textdomain('wp_keyword_tool', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

// Add actions
add_action('init', 'wp_keyword_tool_action_init');