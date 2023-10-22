<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: woocommerce_notices
 */

$design_options_params = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Notices Box', 'us' ),
	'category' => 'WooCommerce',
	'icon' => 'fas fa-exclamation-triangle',
	'place_if' => class_exists( 'woocommerce' ),
	'params' => us_set_params_weight(
		array(
			'style' => array(
				'title' => us_translate( 'Style' ),
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
				),
				'std' => '1',
				'usb_preview' => array(
					'mod' => 'style',
				),
			),
		),

		$design_options_params
	),
);
