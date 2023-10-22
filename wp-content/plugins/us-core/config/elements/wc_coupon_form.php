<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: wc_coupon_form
 */

$misc = us_config( 'elements_misc' );
$design_options_params = us_config( 'elements_design_options' );

return array(
	'title' => __( 'Coupon Form', 'us' ),
	'category' => 'WooCommerce',
	'icon' => 'fas fa-tags',
	'place_if' => class_exists( 'woocommerce' ),
	'params' => us_set_params_weight(

		// General section
		array(
			'placeholder' => array(
				'title' => __( 'Placeholder', 'us' ),
				'type' => 'text',
				'std' => us_translate( 'Coupon code', 'woocommerce' ),
				'usb_preview' => array(
					'attr' => 'placeholder',
					'elm' => 'input[type=text]',
				),
			),
			'btn_label' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => us_translate( 'Apply coupon', 'woocommerce' ),
				'usb_preview' => array(
					'attr' => 'text',
					'elm' => '.w-btn',
				),
			),
			'btn_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => us_get_btn_styles(),
				'std' => '1',
				'usb_preview' => array(
					'elm' => '.w-btn',
					'mod' => 'us-btn-style',
				),
			),
		),

		$design_options_params
	),
);
