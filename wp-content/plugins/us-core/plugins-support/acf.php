<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Advanced Custom Fields
 *
 * @link https://www.advancedcustomfields.com/
 */

if ( ! class_exists( 'ACF' ) ) {
	return;
}

// Register Google Maps API key
// https://www.advancedcustomfields.com/resources/google-map/
if ( ! function_exists( 'us_acf_google_map_api' ) ) {

	function us_acf_google_map_api($api)
	{
		// Get the Google Maps API key from the Theme Options
		$gmaps_api_key = trim(esc_attr(us_get_option('gmaps_api_key', '')));
		/*
		 * Set the API key for ACF only if it is not empty,
		 * to prevent possible erase of the same value set in other plugins
		 */
		if (!empty($gmaps_api_key)) {
			$api['key'] = $gmaps_api_key;
		}

		return $api;
	}

	add_filter( 'acf/fields/google_map/api', 'us_acf_google_map_api' );
}

/**
 * Removing custom plugin message for ACF Pro
 */
if ( ! function_exists( 'us_acf_pro_remove_update_message' ) ) {

	function us_acf_pro_remove_update_message() {
		if (
			function_exists( 'acf_get_setting' )
			AND $acf_basename = acf_get_setting( 'basename' )
		) {
			// Since action for plugin is added via class member function,
			// removing not one specific action but all actions for the ACF Pro plugin update message
			remove_all_actions( 'in_plugin_update_message-' . $acf_basename );
		}
	}

	add_action( 'init', 'us_acf_pro_remove_update_message', 30 );
}
