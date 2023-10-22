<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Generates and outputs theme options' generated styleshets
 *
 * @action Before the template: us_before_template:templates/css-theme-options
 * @action After the template: us_after_template:templates/css-theme-options
 */

$with_shop = class_exists( 'woocommerce' );
$cols_via_grid = ( us_get_option( 'live_builder' ) AND us_get_option( 'grid_columns_layout' ) );
$responsive_states = us_get_responsive_states();

// Add filter to remove protocols from URLs for better compatibility with caching plugins and services
if ( ! us_get_option( 'keep_url_protocol' ) ) {
	add_filter( 'clean_url', 'us_remove_url_protocol', 10 );
}

// Helper function to determine if CSS asset is used
if ( ! function_exists( 'us_is_asset_used' ) ) {
	function us_is_asset_used( $asset_name ) {
		$_assets = us_get_option( 'assets' );
		if (
			us_get_option( 'optimize_assets', FALSE )
			AND isset( $_assets[ $asset_name ] )
			AND $_assets[ $asset_name ] == 0
		) {
			return FALSE;
		}
		return TRUE;
	}
}

/* GLOBAL CSS VARS
   ====================================================================================================== */

echo ':root {';
$color_options = us_config( 'theme-options.colors.fields' );
foreach( $color_options as $color_option => $color_option_params ) {

	// Do not output empty color values
	if ( us_get_color( $color_option, TRUE, FALSE ) === '' ) {
		continue;
	}

	// Do not output variables without "color" prefix in its names
	if ( strpos( $color_option, 'color' ) !== 0 ) {
		continue;
	}

	echo '--' . str_replace( '_', '-', $color_option ) . ': ' . us_get_color( $color_option, FALSE, FALSE ) . ';';

	// Add separate values from color pickers that support gradients
	if ( ! empty( $color_option_params['with_gradient'] ) ) {
		echo '--' . str_replace( '_', '-', $color_option ) . '-grad: ' . us_get_color( $color_option, TRUE, FALSE ) . ';';
	}
}

// Add CSS VARS, needed to simplify CSS values globally
echo '--color-content-primary-faded:' . us_hex2rgba( us_get_color( '_content_primary', FALSE, FALSE ), 0.15 ) . ';';
echo '--box-shadow: 0 5px 15px rgba(0,0,0,.15);';
echo '--box-shadow-up: 0 -5px 15px rgba(0,0,0,.15);';
echo '--site-content-width: ' . us_get_option( 'site_content_width' ) . ';';
echo '--inputs-font-size: ' . us_get_option( 'input_fields' )[0]['font_size'] . ';';
echo '--inputs-height: ' . us_get_option( 'input_fields' )[0]['height'] . ';';
echo '--inputs-padding: ' . us_get_option( 'input_fields' )[0]['padding'] . ';';
echo '--inputs-border-width: ' . us_get_option( 'input_fields' )[0]['border_width'] . ';';
echo '--inputs-text-color: ' . us_get_color( us_get_option( 'input_fields' )[0]['color_text'] ) . ';';

// Add global FONTS as CSS VARS
foreach ( array( 'body', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $font_var ) {

	// Get only font-name, because the value also contains the font-weight
	$font_name = strstr( us_get_option( $font_var . '_font_family' ), '|', TRUE );

	// For 'get_h1' value get the Heading 1 font name
	if ( $font_name === 'get_h1' ) {
		$font_name = strstr( us_get_option( 'h1_font_family' ), '|', TRUE );
	}

	// Exclude "No font specified" value
	if ( $font_name !== 'none' ) {
		$fallback_font_family = NULL;
		if ( is_array( us_config( 'google-fonts.' . $font_name, NULL ) ) ) {
			$fallback_font_family = us_config( 'google-fonts.' . $font_name . '.fallback', 'sans-serif' );
		}
		if ( strpos( $font_name, ',' ) === FALSE ) {
			$font_name = '"' . $font_name . '"';
		}
		if ( $fallback_font_family ) {
			$font_name .= ', ' . $fallback_font_family;
		}
		echo '--font-' . $font_var . ': ' . $font_name . ';';
	}
}
echo '}';

/* DARK COLORS CSS VARS
   ====================================================================================================== */
$dark_theme = us_get_option( 'dark_theme', 'none' );
if ( $dark_theme !== 'none' ) {
	$color_schemes = us_get_color_schemes();

	echo '@media (prefers-color-scheme: dark) {';
	echo ':root {';
	foreach( $color_schemes[ $dark_theme ]['values'] as $color_schemes_option => $color_value ) {
		echo '--' . str_replace( '_', '-', $color_schemes_option ) . ': ' . us_gradient2hex( $color_value ) . ';';

		// Add separate values from color pickers that support gradients
		foreach( $color_options as $color_option => $color_option_params ) {
			if ( ! empty( $color_option_params['with_gradient'] ) AND $color_option === $color_schemes_option ) {
				echo '--' . str_replace( '_', '-', $color_schemes_option ) . '-grad: ' . $color_value . ';';
			}
		}

		if ( $color_schemes_option === 'color_content_primary' ) {
			echo '--color-content-primary-faded:' . us_hex2rgba( us_gradient2hex( $color_value ), 0.15 ) . ';';
		}
	}
	echo '}';
	echo '}';
}

/* Specific styles for gradient Headings */
if ( strpos( us_get_color( '_content_heading', TRUE ), 'grad' ) !== FALSE ) {
echo 'h1, h2, h3, h4, h5, h6 {';
echo 'background: var(--color-content-heading-grad);';
echo '-webkit-background-clip: text;';
echo 'color: transparent;';
echo '}';
}

/* Specific styles for gradient Headings in Alternate Content colors */
if ( strpos( us_get_color( '_alt_content_heading', TRUE ), 'grad' ) !== FALSE ) {
echo '.l-section.color_alternate h1,';
echo '.l-section.color_alternate h2,';
echo '.l-section.color_alternate h3,';
echo '.l-section.color_alternate h4,';
echo '.l-section.color_alternate h5,';
echo '.l-section.color_alternate h6 {';
echo 'background: var(--color-alt-content-heading-grad);';
echo '-webkit-background-clip: text;';
echo 'color: transparent;';
echo '}';
}

/* Specific styles for gradient Headings in Footer colors */
if ( strpos( us_get_color( '_footer_heading', TRUE ), 'grad' ) !== FALSE ) {
echo '.l-section.color_footer-top h1,';
echo '.l-section.color_footer-top h2,';
echo '.l-section.color_footer-top h3,';
echo '.l-section.color_footer-top h4,';
echo '.l-section.color_footer-top h5,';
echo '.l-section.color_footer-top h6 {';
echo 'background: var(--color-footer-heading-grad);';
echo '-webkit-background-clip: text;';
echo 'color: transparent;';
echo '}';
}
if ( strpos( us_get_color( '_subfooter_heading', TRUE ), 'grad' ) !== FALSE ) {
echo '.l-section.color_footer-bottom h1,';
echo '.l-section.color_footer-bottom h2,';
echo '.l-section.color_footer-bottom h3,';
echo '.l-section.color_footer-bottom h4,';
echo '.l-section.color_footer-bottom h5,';
echo '.l-section.color_footer-bottom h6 {';
echo 'background: var(--color-subfooter-heading-grad);';
echo '-webkit-background-clip: text;';
echo 'color: transparent;';
echo '}';
}

// Headings 1-6 separate colors
for ( $i = 1; $i <= 6; $i ++ ) {
	if ( $h_color = us_get_color( us_get_option( 'h' . $i . '_color' ), TRUE ) ) {
		$h_color_important = us_get_option( 'h' . $i . '_color_override' ) ? '!important' : '';

		echo 'h' . $i . '{';
		if ( strpos( $h_color, 'grad' ) !== FALSE ) {
			echo 'background:' . $h_color . ';';
			echo '-webkit-background-clip: text;';
			echo 'color: transparent;';
		} else {
			echo 'color:' . $h_color . $h_color_important . ';';
		}
		echo '}';
	}
}

/* Specific styles for gradient icons in IconBoxes and Counters */
if ( us_is_asset_used( 'iconbox' ) OR us_is_asset_used( 'counter' ) ) {
	if ( strpos( us_get_color( '_content_primary', TRUE ), 'grad' ) !== FALSE ) {
		echo '.w-counter.color_primary .w-counter-value,';
		echo '.w-iconbox.color_primary.style_default .w-iconbox-icon i:not(.fad) {';
		echo 'background: var(--color-content-primary-grad);';
		echo '-webkit-background-clip: text;';
		echo 'color: transparent;';
		echo '}';
	}
	if ( strpos( us_get_color( '_content_secondary', TRUE ), 'grad' ) !== FALSE ) {
		echo '.w-counter.color_secondary .w-counter-value,';
		echo '.w-iconbox.color_secondary.style_default .w-iconbox-icon i:not(.fad) {';
		echo 'background: var(--color-content-secondary-grad);';
		echo '-webkit-background-clip: text;';
		echo 'color: transparent;';
		echo '}';
	}
}

// Add colors styles for Block Editor (Gutenberg), if it's enabled on the frontend
if ( us_get_option( 'block_editor' ) ) {
	$predefined_colors = array(
		'color_content_primary',
		'color_content_secondary',
		'color_content_heading',
		'color_content_text',
		'color_content_faded',
		'color_content_border',
		'color_content_bg_alt',
		'color_content_bg',
	);
	foreach ( $predefined_colors as $color ) {
		$color_name = str_replace( 'color_', '', $color );
		$color_name = str_replace( '_', '-', $color_name );

		echo '.has-' . $color_name . '-color {';
		echo 'color: var(--color-' . $color_name . ');';
		echo '}';

		// Gradients are possible for background
		echo '.has-' . $color_name . '-background-color {';
		echo 'background: var(--color-' . $color_name . '-grad);';
		echo '}';
	}
}

/* Typography
  =============================================================================================================================== */

// Global Text
$css = 'html,';
$css .= '.l-header .widget,';
$css .= '.menu-item-object-us_page_block {';
$css .= 'font-family: var(--font-body);';

// Set font-weight based on the first checkbox in font selection in Theme Options > Typography
// TODO: remove this in #2183
$body_font_value = us_get_option( 'body_font_family' );
if ( $body_font_value !== 'none' ) {
	$css .= 'font-weight:' . substr( strstr( $body_font_value, '|' ), 1, 3 ) . ';';
}

$css .= 'font-size:' . us_get_option( 'body_fontsize' ) . ';';
$css .= 'line-height:' . us_get_option( 'body_lineheight' ) . ';';
$css .= '}';

// Uploaded Fonts
$uploaded_fonts = us_get_option( 'uploaded_fonts', array() );
if ( is_array( $uploaded_fonts ) AND count( $uploaded_fonts ) > 0 ) {
	foreach ( $uploaded_fonts as $uploaded_font ) {
		$files = explode( ',', $uploaded_font['files'] );
		$urls = array();
		foreach ( $files as $file ) {
			if ( $url = wp_get_attachment_url( $file ) ) {
				$urls[] = 'url(' . esc_url( $url ) . ') format("' . pathinfo( $url, PATHINFO_EXTENSION ) . '")';
			}
		}
		if ( count( $urls ) ) {
			$css .= '@font-face {';
			$css .= 'font-display: ' . us_get_option( 'font_display', 'swap' ) . ';';
			$css .= 'font-style: ' . ( ! empty( $uploaded_font['italic'] ) ? 'italic' : 'normal' ) . ';';
			$css .= 'font-family:"' . strip_tags( $uploaded_font['name'] ) . '";';
			$css .= 'font-weight:' . $uploaded_font['weight'] . ';';
			$css .= 'src:' . implode( ', ', $urls ) . ';';
			$css .= '}';
		}
	}
}

// Headings h1-h6
for ( $i = 1; $i <= 6; $i ++ ) {
	if ( $i == 4 ) { // set to some elements styles as <h4>
		if ( $with_shop ) {
			$css .= '.woocommerce-Reviews-title,';
		}
		$css .= '.widgettitle, h' . $i . '{';
	} else {
		$css .= 'h' . $i . '{';
	}
	$css .= 'font-family: var(--font-h' . $i . ');';
	$css .= 'font-weight:' . us_get_option( 'h' . $i . '_fontweight' ) . ';';
	$css .= 'font-size:' . us_get_option( 'h' . $i . '_fontsize' ) . ';';
	$css .= 'line-height:' . us_get_option( 'h' . $i . '_lineheight' ) . ';';
	$css .= 'letter-spacing:' . us_get_option( 'h' . $i . '_letterspacing' ) . ';';
	$css .= 'margin-bottom:' . us_get_option( 'h' . $i . '_bottom_indent' ) . ';';
	if ( us_get_option( 'h' . $i . '_texttransform', 'none' ) !== 'none' ) {
		$css .= 'text-transform:' . us_get_option( 'h' . $i . '_texttransform' ) . ';';
	}
	if ( ! empty( us_get_option( 'h' . $i . '_fontstyle' ) ) ) {
		$css .= 'font-style: italic;';
	}
	$css .= '}';
}

// Sizes on Mobiles
$css .= '@media (max-width: ' . (int) us_get_option( 'mobiles_breakpoint' ) . 'px) {';
$css .= 'html {';
if ( $body_fontsize_mobile = us_get_option( 'body_fontsize_mobile' ) ) {
	$css .= 'font-size:' . $body_fontsize_mobile . ';';
}
$css .= 'line-height:' . us_get_option( 'body_lineheight_mobile' ) . ';';
$css .= '}';

// Headings h1-h6
for ( $i = 1; $i <= 6; $i ++ ) {
	if ( $h_fontsize_mobile = us_get_option( 'h' . $i . '_fontsize_mobile' ) ) {

		if ( $i == 4 ) { // set to some elements styles as <h4>
			if ( $with_shop ) {
				$css .= '.woocommerce-Reviews-title,';
			}
			$css .= '.widgettitle, h' . $i . '{';
		} else {
			$css .= 'h' . $i . '{';
		}
		$css .= 'font-size:' . $h_fontsize_mobile . ';';
		$css .= '}';
		$css .= 'h' . $i . '.vc_custom_heading:not([class*="us_custom_"]) {';
		$css .= 'font-size:' . $h_fontsize_mobile . ' !important;';
		$css .= '}';
	}
}
$css .= '}';
echo strip_tags( $css );



/* Site Layout
  =============================================================================================================================== */

// Save rem units in pixels, used in @media calculations
if ( strpos( us_get_option( 'body_fontsize' ), 'px' ) === 2 ) {
	$rem_in_px = (int) us_get_option( 'body_fontsize' );
} else {
	$rem_in_px = 16;
}

// Generate body background value
$body_bg_image = '';
$body_bg_color = us_get_color( us_get_option( 'color_body_bg' ), TRUE );

// Add image properties when image is set
if ( $body_bg_image_id = us_get_option( 'body_bg_image' ) AND $body_bg_image_url = wp_get_attachment_image_url( $body_bg_image_id, 'full' ) ) {
	$body_bg_image .= 'url(' . $body_bg_image_url . ') ';
	$body_bg_image .= us_get_option( 'body_bg_image_position' );
	if ( us_get_option( 'body_bg_image_size' ) != 'initial' ) {
		$body_bg_image .= '/' . us_get_option( 'body_bg_image_size' );
	}
	$body_bg_image .= ' ';
	$body_bg_image .= us_get_option( 'body_bg_image_repeat' );
	if ( ! us_get_option( 'body_bg_image_attachment', 0 ) ) {
		$body_bg_image .= ' fixed';
	}

	// If the color value contains gradient, add comma for correct appearance
	if ( strpos( $body_bg_color, 'grad' ) !== FALSE ) {
		$body_bg_image .= ',';
	}
}
?>
body {
	background: <?= esc_attr( $body_bg_image . ' ' . $body_bg_color ) ?>;
	}
.l-canvas.type_boxed,
.l-canvas.type_boxed .l-subheader,
.l-canvas.type_boxed ~ .l-footer {
	max-width: <?= us_get_option( 'site_canvas_width' ) ?>;
	}
.l-subheader-h,
.l-section-h,
.l-main .aligncenter,
.w-tabs-section-content-h {
	max-width: <?= us_get_option( 'site_content_width' ) ?>;
	}
.post-password-form {
	max-width: calc(<?= us_get_option( 'site_content_width' ) ?> + 5rem);
	}

/* Limit width for centered images */
@media screen and (max-width: <?= ( (int) us_get_option( 'site_content_width' ) + $rem_in_px * 5 ) ?>px) {
.l-main .aligncenter {
	max-width: calc(100vw - 5rem);
	}
}

<?php if ( us_get_option( 'row_height' ) == 'custom' ) { ?>
.l-section.height_custom {
	padding-top: <?= us_get_option( 'row_height_custom' ) ?>;
	padding-bottom: <?= us_get_option( 'row_height_custom' ) ?>;
	}
<?php } ?>

<?php if ( ! in_array( us_get_option( 'text_bottom_indent' ), array( '0', '0rem', '0px' ) ) ) { ?>
.wpb_text_column:not(:last-child) {
	margin-bottom: <?= us_get_option( 'text_bottom_indent' ) ?>;
	}
<?php } ?>

<?php if ( us_get_option( 'enable_sidebar_titlebar' ) ) { ?>
.l-sidebar {
	width: <?= us_get_option( 'sidebar_width' ) ?>;
	}
.l-content {
	width: <?= 95 - (float) us_get_option( 'sidebar_width' ) ?>%;
	}
<?php } ?>

/* Footer Reveal Effect */
<?php if ( us_get_option( 'footer_reveal' ) ) { ?>
@media (min-width: <?= us_get_option( 'columns_stacking_width' ) ?>) {
body.footer_reveal .l-canvas {
	position: relative;
	z-index: 1;
	}
body.footer_reveal .l-footer {
	position: fixed;
	bottom: 0;
	}
body.footer_reveal .l-canvas.type_boxed ~ .l-footer {
	left: 0;
	right: 0;
	}
}
<?php } ?>

/* HIDE ELEMENTS ON RESPONSIVE STATES */
@media (min-width:<?= ( $responsive_states['laptops']['breakpoint'] + 1 ) ?>px) {
body.usb_preview .hide_on_default {
	opacity: 0.25 !important;
	}
.vc_hidden-lg,
body:not(.usb_preview) .hide_on_default {
	display: none !important;
	}
}
@media (min-width:<?= ( $responsive_states['tablets']['breakpoint'] + 1 ) ?>px) and (max-width:<?= $responsive_states['laptops']['breakpoint'] ?>px) {
body.usb_preview .hide_on_laptops {
	opacity: 0.25 !important;
	}
.vc_hidden-md,
body:not(.usb_preview) .hide_on_laptops {
	display: none !important;
	}
}
@media (min-width:<?= ( $responsive_states['mobiles']['breakpoint'] + 1 ) ?>px) and (max-width:<?= $responsive_states['tablets']['breakpoint'] ?>px) {
body.usb_preview .hide_on_tablets {
	opacity: 0.25 !important;
	}
.vc_hidden-sm,
body:not(.usb_preview) .hide_on_tablets {
	display: none !important;
	}
}
@media (max-width:<?= $responsive_states['mobiles']['breakpoint'] ?>px) {
body.usb_preview .hide_on_mobiles {
	opacity: 0.25 !important;
	}
.vc_hidden-xs,
body:not(.usb_preview) .hide_on_mobiles {
	display: none !important;
	}
}

<?php
// CSS FLEX columns layout (legacy from WPBakery)
if ( us_is_asset_used( 'columns' ) AND ! $cols_via_grid ) {

	// Define the side for margins offset
	$_offset = is_rtl() ? 'right' : 'left';
	?>
	@media (max-width:<?= $responsive_states['mobiles']['breakpoint'] ?>px) {
	.g-cols.type_default > div[class*="vc_col-xs-"] {
		margin-top: 1rem;
		margin-bottom: 1rem;
		}
	.g-cols > div:not([class*="vc_col-xs-"]) {
		width: 100%;
		margin: 0 0 1.5rem;
		}
	.g-cols.reversed > div:last-of-type {
		order: -1;
		}
	.g-cols.type_boxes > div,
	.g-cols.reversed > div:first-child,
	.g-cols:not(.reversed) > div:last-child,
	.g-cols > div.has_bg_color {
		margin-bottom: 0;
		}
	.vc_col-xs-1 { width: 8.3333%; }
	.vc_col-xs-2 { width: 16.6666%; }
	.vc_col-xs-1\/5 { width: 20%; }
	.vc_col-xs-3 { width: 25%; }
	.vc_col-xs-4 { width: 33.3333%; }
	.vc_col-xs-2\/5 { width: 40%; }
	.vc_col-xs-5 { width: 41.6666%; }
	.vc_col-xs-6 { width: 50%; }
	.vc_col-xs-7 { width: 58.3333%; }
	.vc_col-xs-3\/5 { width: 60%; }
	.vc_col-xs-8 { width: 66.6666%; }
	.vc_col-xs-9 { width: 75%; }
	.vc_col-xs-4\/5 { width: 80%; }
	.vc_col-xs-10 { width: 83.3333%; }
	.vc_col-xs-11 { width: 91.6666%; }
	.vc_col-xs-12 { width: 100%; }
	.vc_col-xs-offset-0 { margin-<?= $_offset ?>: 0; }
	.vc_col-xs-offset-1 { margin-<?= $_offset ?>: 8.3333%; }
	.vc_col-xs-offset-2 { margin-<?= $_offset ?>: 16.6666%; }
	.vc_col-xs-offset-1\/5 { margin-<?= $_offset ?>: 20%; }
	.vc_col-xs-offset-3 { margin-<?= $_offset ?>: 25%; }
	.vc_col-xs-offset-4 { margin-<?= $_offset ?>: 33.3333%; }
	.vc_col-xs-offset-2\/5 { margin-<?= $_offset ?>: 40%; }
	.vc_col-xs-offset-5 { margin-<?= $_offset ?>: 41.6666%; }
	.vc_col-xs-offset-6 { margin-<?= $_offset ?>: 50%; }
	.vc_col-xs-offset-7 { margin-<?= $_offset ?>: 58.3333%; }
	.vc_col-xs-offset-3\/5 { margin-<?= $_offset ?>: 60%; }
	.vc_col-xs-offset-8 { margin-<?= $_offset ?>: 66.6666%; }
	.vc_col-xs-offset-9 { margin-<?= $_offset ?>: 75%; }
	.vc_col-xs-offset-4\/5 { margin-<?= $_offset ?>: 80%; }
	.vc_col-xs-offset-10 { margin-<?= $_offset ?>: 83.3333%; }
	.vc_col-xs-offset-11 { margin-<?= $_offset ?>: 91.6666%; }
	.vc_col-xs-offset-12 { margin-<?= $_offset ?>: 100%; }
	}
	@media (min-width:<?= ( $responsive_states['mobiles']['breakpoint'] + 1 ) ?>px) {
	.vc_col-sm-1 { width: 8.3333%; }
	.vc_col-sm-2 { width: 16.6666%; }
	.vc_col-sm-1\/5 { width: 20%; }
	.vc_col-sm-3 { width: 25%; }
	.vc_col-sm-4 { width: 33.3333%; }
	.vc_col-sm-2\/5 { width: 40%; }
	.vc_col-sm-5 { width: 41.6666%; }
	.vc_col-sm-6 { width: 50%; }
	.vc_col-sm-7 { width: 58.3333%; }
	.vc_col-sm-3\/5 { width: 60%; }
	.vc_col-sm-8 { width: 66.6666%; }
	.vc_col-sm-9 { width: 75%; }
	.vc_col-sm-4\/5 { width: 80%; }
	.vc_col-sm-10 { width: 83.3333%; }
	.vc_col-sm-11 { width: 91.6666%; }
	.vc_col-sm-12 { width: 100%; }
	.vc_col-sm-offset-0 { margin-<?= $_offset ?>: 0; }
	.vc_col-sm-offset-1 { margin-<?= $_offset ?>: 8.3333%; }
	.vc_col-sm-offset-2 { margin-<?= $_offset ?>: 16.6666%; }
	.vc_col-sm-offset-1\/5 { margin-<?= $_offset ?>: 20%; }
	.vc_col-sm-offset-3 { margin-<?= $_offset ?>: 25%; }
	.vc_col-sm-offset-4 { margin-<?= $_offset ?>: 33.3333%; }
	.vc_col-sm-offset-2\/5 { margin-<?= $_offset ?>: 40%; }
	.vc_col-sm-offset-5 { margin-<?= $_offset ?>: 41.6666%; }
	.vc_col-sm-offset-6 { margin-<?= $_offset ?>: 50%; }
	.vc_col-sm-offset-7 { margin-<?= $_offset ?>: 58.3333%; }
	.vc_col-sm-offset-3\/5 { margin-<?= $_offset ?>: 60%; }
	.vc_col-sm-offset-8 { margin-<?= $_offset ?>: 66.6666%; }
	.vc_col-sm-offset-9 { margin-<?= $_offset ?>: 75%; }
	.vc_col-sm-offset-4\/5 { margin-<?= $_offset ?>: 80%; }
	.vc_col-sm-offset-10 { margin-<?= $_offset ?>: 83.3333%; }
	.vc_col-sm-offset-11 { margin-<?= $_offset ?>: 91.6666%; }
	.vc_col-sm-offset-12 { margin-<?= $_offset ?>: 100%; }
	}
	@media (min-width:<?= ( $responsive_states['tablets']['breakpoint'] + 1 ) ?>px) {
	.vc_col-md-1 { width: 8.3333%; }
	.vc_col-md-2 { width: 16.6666%; }
	.vc_col-md-1\/5 { width: 20%; }
	.vc_col-md-3 { width: 25%; }
	.vc_col-md-4 { width: 33.3333%; }
	.vc_col-md-2\/5 { width: 40%; }
	.vc_col-md-5 { width: 41.6666%; }
	.vc_col-md-6 { width: 50%; }
	.vc_col-md-7 { width: 58.3333%; }
	.vc_col-md-3\/5 { width: 60%; }
	.vc_col-md-8 { width: 66.6666%; }
	.vc_col-md-9 { width: 75%; }
	.vc_col-md-4\/5 { width: 80%; }
	.vc_col-md-10 { width: 83.3333%; }
	.vc_col-md-11 { width: 91.6666%; }
	.vc_col-md-12 { width: 100%; }
	.vc_col-md-offset-0 { margin-<?= $_offset ?>: 0; }
	.vc_col-md-offset-1 { margin-<?= $_offset ?>: 8.3333%; }
	.vc_col-md-offset-2 { margin-<?= $_offset ?>: 16.6666%; }
	.vc_col-md-offset-1\/5 { margin-<?= $_offset ?>: 20%; }
	.vc_col-md-offset-3 { margin-<?= $_offset ?>: 25%; }
	.vc_col-md-offset-4 { margin-<?= $_offset ?>: 33.3333%; }
	.vc_col-md-offset-2\/5 { margin-<?= $_offset ?>: 40%; }
	.vc_col-md-offset-5 { margin-<?= $_offset ?>: 41.6666%; }
	.vc_col-md-offset-6 { margin-<?= $_offset ?>: 50%; }
	.vc_col-md-offset-7 { margin-<?= $_offset ?>: 58.3333%; }
	.vc_col-md-offset-3\/5 { margin-<?= $_offset ?>: 60%; }
	.vc_col-md-offset-8 { margin-<?= $_offset ?>: 66.6666%; }
	.vc_col-md-offset-9 { margin-<?= $_offset ?>: 75%; }
	.vc_col-md-offset-4\/5 { margin-<?= $_offset ?>: 80%; }
	.vc_col-md-offset-10 { margin-<?= $_offset ?>: 83.3333%; }
	.vc_col-md-offset-11 { margin-<?= $_offset ?>: 91.6666%; }
	.vc_col-md-offset-12 { margin-<?= $_offset ?>: 100%; }
	}
	@media (min-width:<?= ( $responsive_states['laptops']['breakpoint'] + 1 ) ?>px) {
	.vc_col-lg-1 { width: 8.3333%; }
	.vc_col-lg-2 { width: 16.6666%; }
	.vc_col-lg-1\/5 { width: 20%; }
	.vc_col-lg-3 { width: 25%; }
	.vc_col-lg-4 { width: 33.3333%; }
	.vc_col-lg-2\/5 { width: 40%; }
	.vc_col-lg-5 { width: 41.6666%; }
	.vc_col-lg-6 { width: 50%; }
	.vc_col-lg-7 { width: 58.3333%; }
	.vc_col-lg-3\/5 { width: 60%; }
	.vc_col-lg-8 { width: 66.6666%; }
	.vc_col-lg-9 { width: 75%; }
	.vc_col-lg-4\/5 { width: 80%; }
	.vc_col-lg-10 { width: 83.3333%; }
	.vc_col-lg-11 { width: 91.6666%; }
	.vc_col-lg-12 { width: 100%; }
	.vc_col-lg-offset-0 { margin-<?= $_offset ?>: 0; }
	.vc_col-lg-offset-1 { margin-<?= $_offset ?>: 8.3333%; }
	.vc_col-lg-offset-2 { margin-<?= $_offset ?>: 16.6666%; }
	.vc_col-lg-offset-1\/5 { margin-<?= $_offset ?>: 20%; }
	.vc_col-lg-offset-3 { margin-<?= $_offset ?>: 25%; }
	.vc_col-lg-offset-4 { margin-<?= $_offset ?>: 33.3333%; }
	.vc_col-lg-offset-2\/5 { margin-<?= $_offset ?>: 40%; }
	.vc_col-lg-offset-5 { margin-<?= $_offset ?>: 41.6666%; }
	.vc_col-lg-offset-6 { margin-<?= $_offset ?>: 50%; }
	.vc_col-lg-offset-7 { margin-<?= $_offset ?>: 58.3333%; }
	.vc_col-lg-offset-3\/5 { margin-<?= $_offset ?>: 60%; }
	.vc_col-lg-offset-8 { margin-<?= $_offset ?>: 66.6666%; }
	.vc_col-lg-offset-9 { margin-<?= $_offset ?>: 75%; }
	.vc_col-lg-offset-4\/5 { margin-<?= $_offset ?>: 80%; }
	.vc_col-lg-offset-10 { margin-<?= $_offset ?>: 83.3333%; }
	.vc_col-lg-offset-11 { margin-<?= $_offset ?>: 91.6666%; }
	.vc_col-lg-offset-12 { margin-<?= $_offset ?>: 100%; }
	}
	@media (min-width:<?= ( $responsive_states['mobiles']['breakpoint'] + 1 ) ?>px) and (max-width:<?= $responsive_states['tablets']['breakpoint'] ?>px) {
	.g-cols.via_flex.type_default > div[class*="vc_col-md-"],
	.g-cols.via_flex.type_default > div[class*="vc_col-lg-"] {
		margin-top: 1rem;
		margin-bottom: 1rem;
		}
	}
	@media (min-width:<?= ( $responsive_states['tablets']['breakpoint'] + 1 ) ?>px) and (max-width:<?= $responsive_states['laptops']['breakpoint'] ?>px) {
	.g-cols.via_flex.type_default > div[class*="vc_col-lg-"] {
		margin-top: 1rem;
		margin-bottom: 1rem;
		}
	}
<?php }

// CSS GRID columns layout
if ( us_is_asset_used( 'columns' ) AND $cols_via_grid ) {
	foreach ( $responsive_states as $state => $data ) {
		if ( $state == 'default' ) {
			continue;
		}
		?>
		@media (max-width: <?= $data['breakpoint'] ?>px) {
			.g-cols.<?= $state ?>-cols_1 {
				grid-template-columns: 100%;
				}
			.g-cols.<?= $state ?>-cols_1.reversed > div:last-of-type {
				order: -1;
				}
			.g-cols.<?= $state ?>-cols_2 {
				grid-template-columns: repeat(2, 1fr);
				}
			.g-cols.<?= $state ?>-cols_3 {
				grid-template-columns: repeat(3, 1fr);
				}
			.g-cols.<?= $state ?>-cols_4 {
				grid-template-columns: repeat(4, 1fr);
				}
			.g-cols.<?= $state ?>-cols_5 {
				grid-template-columns: repeat(5, 1fr);
				}
			.g-cols.<?= $state ?>-cols_6 {
				grid-template-columns: repeat(6, 1fr);
				}
			.g-cols.<?= $state ?>-cols_1-2 {
				grid-template-columns: 1fr 2fr;
				}
			.g-cols.<?= $state ?>-cols_2-1 {
				grid-template-columns: 2fr 1fr;
				}
			.g-cols.<?= $state ?>-cols_2-3 {
				grid-template-columns: 2fr 3fr;
				}
			.g-cols.<?= $state ?>-cols_3-2 {
				grid-template-columns: 3fr 2fr;
				}
			.g-cols.<?= $state ?>-cols_1-3 {
				grid-template-columns: 1fr 3fr;
				}
			.g-cols.<?= $state ?>-cols_3-1 {
				grid-template-columns: 3fr 1fr;
				}
			.g-cols.<?= $state ?>-cols_1-4 {
				grid-template-columns: 1fr 4fr;
				}
			.g-cols.<?= $state ?>-cols_4-1 {
				grid-template-columns: 4fr 1fr;
				}
			.g-cols.<?= $state ?>-cols_1-5 {
				grid-template-columns: 1fr 5fr;
				}
			.g-cols.<?= $state ?>-cols_5-1 {
				grid-template-columns: 5fr 1fr;
				}
			.g-cols.<?= $state ?>-cols_1-2-1 {
				grid-template-columns: 1fr 2fr 1fr;
				}
			.g-cols.<?= $state ?>-cols_1-3-1 {
				grid-template-columns: 1fr 3fr 1fr;
				}
			.g-cols.<?= $state ?>-cols_1-4-1 {
				grid-template-columns: 1fr 4fr 1fr;
				}
			<?php if ( $state == 'mobiles' ) { ?>
			.g-cols:not([style*="grid-gap"]) {
				grid-gap: 1.5rem;
				}
			<?php } ?>
		}
		<?php
	}
}
?>

/* COLUMNS STACKING WIDTH */
@media (max-width: <?= ( (int) us_get_option( 'columns_stacking_width' ) - 1 ) ?>px) {
.l-canvas {
	overflow: hidden;
	}
.g-cols.stacking_default.reversed > div:last-of-type {
	order: -1;
	}
.g-cols.stacking_default.via_flex > div:not([class*=" vc_col-"]) {
	width: 100%;
	margin: 0 0 1.5rem;
	}
.g-cols.stacking_default.via_grid.mobiles-cols_1 {
	grid-template-columns: 100%;
	}
.g-cols.stacking_default.via_flex.type_boxes > div,
.g-cols.stacking_default.via_flex.reversed > div:first-child,
.g-cols.stacking_default.via_flex:not(.reversed) > div:last-child,
.g-cols.stacking_default.via_flex > div.has_bg_color {
	margin-bottom: 0;
	}
.g-cols.stacking_default.via_flex.type_default > .wpb_column.stretched {
	margin-left: -1rem;
	margin-right: -1rem;
	}
.g-cols.stacking_default.via_grid.mobiles-cols_1 > .wpb_column.stretched,
.g-cols.stacking_default.via_flex.type_boxes > .wpb_column.stretched {
	margin-left: -2.5rem;
	margin-right: -2.5rem;
	width: auto;
	}
.vc_column-inner.type_sticky > .wpb_wrapper,
.vc_column_container.type_sticky > .vc_column-inner {
	top: 0 !important;
	}
}

@media (min-width: <?= us_get_option( 'columns_stacking_width' ) ?>) {
body:not(.rtl) .l-section.for_sidebar.at_left > div > .l-sidebar,
.rtl .l-section.for_sidebar.at_right > div > .l-sidebar {
	order: -1;
	}
.vc_column_container.type_sticky > .vc_column-inner,
.vc_column-inner.type_sticky > .wpb_wrapper {
	position: -webkit-sticky;
	position: sticky;
	}
.l-section.type_sticky {
	position: -webkit-sticky;
	position: sticky;
	top: 0;
	z-index: 11;
	transform: translateZ(0); /* render fix for webkit browsers */
	transition: top 0.3s cubic-bezier(.78,.13,.15,.86) 0.1s;
	}
.admin-bar .l-section.type_sticky {
	top: 32px;
	}
.l-section.type_sticky > .l-section-h {
	transition: padding-top 0.3s;
	}
.header_hor .l-header.pos_fixed:not(.down) ~ .l-main .l-section.type_sticky:not(:first-of-type) {
	top: var(--header-sticky-height);
	}
.admin-bar.header_hor .l-header.pos_fixed:not(.down) ~ .l-main .l-section.type_sticky:not(:first-of-type) {
	top: calc( var(--header-sticky-height) + 32px );
	}
.header_hor .l-header.pos_fixed.sticky:not(.down) ~ .l-main .l-section.type_sticky:first-of-type > .l-section-h {
	padding-top: var(--header-sticky-height);
	}
}

/* EMULATE INDENTS TO THE SCREEN EDGES */
<?php
// Calculate Vertical Header width on the "desktops" state
if ( us_get_header_option( 'orientation' ) === 'ver' ) {
	if ( strpos( us_get_header_option( 'width' ), 'px' ) !== FALSE ) {
		$header_width_px = (int) us_get_header_option( 'width' );
	} else {
		$header_width_px = (int) us_get_header_option( 'width' ) * $rem_in_px;
	}
} else {
	$header_width_px = 0;
}
?>
@media screen and (min-width: <?= ( (int) us_get_option( 'site_content_width' ) + $rem_in_px * 5 + $header_width_px ) ?>px) {
.g-cols.via_flex.type_default > .wpb_column.stretched:first-of-type {
	margin-left: calc( var(--site-content-width) / 2 + <?= $header_width_px ?>px / 2 + 1.5rem - 50vw);
	}
.g-cols.via_flex.type_default > .wpb_column.stretched:last-of-type {
	margin-right: calc( var(--site-content-width) / 2 + <?= $header_width_px ?>px / 2 + 1.5rem - 50vw);
	}
.l-main .alignfull, /* Full width for Gutenberg blocks */
.w-separator.width_screen,
.g-cols.via_grid > .wpb_column.stretched:first-of-type,
.g-cols.via_flex.type_boxes > .wpb_column.stretched:first-of-type {
	margin-left: calc( var(--site-content-width) / 2 + <?= $header_width_px ?>px / 2 - 50vw );
	}
.l-main .alignfull, /* Full width for Gutenberg blocks */
.w-separator.width_screen,
.g-cols.via_grid > .wpb_column.stretched:last-of-type,
.g-cols.via_flex.type_boxes > .wpb_column.stretched:last-of-type {
	margin-right: calc( var(--site-content-width) / 2 + <?= $header_width_px ?>px / 2 - 50vw );
	}
}

<?php if ( us_is_asset_used( 'forms' ) ) {
	?>
	@media (max-width: <?= (int) us_get_option( 'mobiles_breakpoint' ) ?>px) {
	.w-form-row.for_submit .w-btn {
		font-size: var(--btn-size-mobiles) !important;
		}
	}
	<?php
}

if ( us_get_option( 'keyboard_accessibility' ) ) { ?>
a:focus,
button:focus,
input[type="checkbox"]:focus + i,
input[type="submit"]:focus {
	outline: 2px dotted var(--color-content-primary);
	}
<?php } else { ?>
a,
button,
input[type="submit"],
.ui-slider-handle {
	outline: none !important;
	}
<?php } ?>

/* "Back to top" and Vertical Header opening buttons */
<?php if ( us_get_option( 'back_to_top' ) AND ! us_get_option( 'back_to_top_style' ) ) { ?>
.w-toplink,
<?php } ?>
.w-header-show {
	background: <?php echo us_get_color( us_get_option( 'back_to_top_color' ), TRUE ) ?>;
	}
<?php if ( us_get_option( 'back_to_top' ) AND ! us_get_option( 'back_to_top_style' ) ) { ?>
.no-touch .w-toplink.active:hover,
<?php } ?>
.no-touch .w-header-show:hover {
	background: var(--color-content-primary-grad);
	}
<?php



/* BUTTONS STYLES
   ====================================================================================================== */
if ( us_is_asset_used( 'buttons' ) AND $btn_styles = us_get_option( 'buttons' ) ) {

    // Remove transition if the FIRST style has a gradient in its background (gradients don't support transition)
	if (
		strpos( $btn_styles[0]['color_bg'], 'grad' ) !== FALSE
		OR strpos( $btn_styles[0]['color_bg_hover'], 'grad' ) !== FALSE
	) {
		echo 'button[type="submit"], input[type="submit"] { transition: none; }';
	}

	// Generate Buttons Styles
	foreach ( $btn_styles as $key => $btn_style ) {

		// Set the FIRST style for non-editable button elements
		if ( $key === 0 ) {
			echo 'button[type="submit"]:not(.w-btn),';
			echo 'input[type="submit"]:not(.w-btn),';
		}
		if ( $with_shop AND us_get_option( 'shop_secondary_btn_style' ) == $btn_style['id'] ) {
			echo '.woocommerce .button, .woocommerce .actions .button,';
		}
		if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
			echo '.woocommerce .button.alt, .woocommerce .button.checkout, .woocommerce .button.add_to_cart_button,';
		}
		echo '.us-nav-style_' . $btn_style['id'] . ' > *,';
		echo '.navstyle_' . $btn_style['id'] . ' > .owl-nav div,';
		echo '.us-btn-style_' . $btn_style['id'] . '{';
		if ( ! empty( $btn_style['font'] ) ) {
			echo sprintf( 'font-family:%s;', us_get_font_family( $btn_style['font'] ) );
		}
		if ( isset( $btn_style['font_size'] ) ) {
			echo 'font-size:' . $btn_style['font_size'] . ';';
		}
		if ( isset( $btn_style['line_height'] ) ) {
			echo 'line-height:' . $btn_style['line_height'] . '!important;';
		}

		// Fallback for var type
		if ( is_array( $btn_style['text_style'] ) ) {
			$btn_style['text_style'] = implode( ',', $btn_style['text_style'] );
		}

		echo 'font-weight:' . $btn_style['font_weight'] . ';';
		echo 'font-style:' . ( strpos( $btn_style['text_style'], 'italic' ) !== FALSE ? 'italic' : 'normal' ) . ';';
		echo 'text-transform:' . ( strpos( $btn_style['text_style'], 'uppercase' ) !== FALSE ? 'uppercase' : 'none' ) . ';';
		echo 'letter-spacing:' . $btn_style['letter_spacing'] . ';';
		echo 'border-radius:' . $btn_style['border_radius'] . ';';
		echo 'padding:' . $btn_style['height'] . ' ' . $btn_style['width'] . ';';
		echo 'background:' . ( ! empty( us_get_color( $btn_style['color_bg'], /* Gradient */ TRUE ) )
			? us_get_color( $btn_style['color_bg'], /* Gradient */ TRUE )
			: 'transparent' ) . ';';
		if ( ! empty( $btn_style['color_border'] ) ) {
			$border_color = us_get_color( $btn_style['color_border'], /* Gradient*/ TRUE );
			if ( strpos( $border_color, 'grad' ) !== FALSE ) {
				echo 'border-image:' . $border_color . ' 1;';
			} else {
				echo 'border-color:' . $border_color . ';';
			}
		} else {
			echo 'border-color: transparent;';
		}
		if ( ! empty( $btn_style['color_text'] ) ) {
			echo 'color:' . us_get_color( $btn_style['color_text'] ) . '!important;';
		}

		// Shadow
		if ( ! empty( $btn_style['color_shadow'] ) ) {
			$_inset = ! empty( $btn_style['shadow_inset'] ) ? 'inset' : '';
			$_offset_h = ! empty( $btn_style['shadow_offset_h'] ) ? $btn_style['shadow_offset_h'] : '0';
			$_offset_v = ! empty( $btn_style['shadow_offset_v'] ) ? $btn_style['shadow_offset_v'] : '0';
			$_blur = ! empty( $btn_style['shadow_blur'] ) ? $btn_style['shadow_blur'] : '0';
			$_spread = ! empty( $btn_style['shadow_spread'] ) ? $btn_style['shadow_spread'] : '0';
			echo sprintf(
				'box-shadow: %s %s %s %s %s %s;',
				$_inset,
				$_offset_h,
				$_offset_v,
				$_blur,
				$_spread,
				us_get_color( $btn_style['color_shadow'] )
			);
		}
		echo '}';

		// Border imitation
		if ( $key === 0 ) {
			echo 'button[type="submit"]:not(.w-btn):before,';
			echo 'input[type="submit"]:not(.w-btn),';
		}
		if ( $with_shop AND us_get_option( 'shop_secondary_btn_style' ) == $btn_style['id'] ) {
			echo '.woocommerce .button:before, .woocommerce .actions .button:before,';
		}
		if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
			echo '.woocommerce .button.alt:before, .woocommerce .button.checkout:before, .woocommerce .button.add_to_cart_button:before,';
		}
		echo '.us-nav-style_' . $btn_style['id'] . ' > *:before,';
		echo '.navstyle_' . $btn_style['id'] . ' > .owl-nav div:before,';
		echo '.us-btn-style_' . $btn_style['id'] . ':before {';
		echo 'border-width:' . $btn_style['border_width'] . ';';
		echo '}';

		// HOVER STATE
		if ( $key === 0 ) {
			echo '.no-touch button[type="submit"]:not(.w-btn):hover,';
			echo '.no-touch input[type="submit"]:not(.w-btn):hover,';
		}
		if ( $with_shop AND us_get_option( 'shop_secondary_btn_style' ) == $btn_style['id'] ) {
			echo '.no-touch .woocommerce .button:hover, .no-touch .woocommerce .actions .button:hover,';
		}
		if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
			echo '.no-touch .woocommerce .button.alt:hover, .no-touch .woocommerce .button.checkout:hover, .no-touch .woocommerce .button.add_to_cart_button:hover,';
		}
		echo '.us-nav-style_' . $btn_style['id'] . ' > span.current,';
		echo '.no-touch .us-nav-style_' . $btn_style['id'] . ' > a:hover,';
		echo '.no-touch .navstyle_' . $btn_style['id'] . ' > .owl-nav div:hover,';
		echo '.no-touch .us-btn-style_' . $btn_style['id'] . ':hover {';

		// Shadow on Hover
		if ( ! empty( $btn_style['color_shadow_hover'] ) ) {
			$_inset = ! empty( $btn_style['shadow_hover_inset'] ) ? 'inset' : '';
			$_offset_h = ! empty( $btn_style['shadow_hover_offset_h'] ) ? $btn_style['shadow_hover_offset_h'] : '0';
			$_offset_v = ! empty( $btn_style['shadow_hover_offset_v'] ) ? $btn_style['shadow_hover_offset_v'] : '0';
			$_blur = ! empty( $btn_style['shadow_hover_blur'] ) ? $btn_style['shadow_hover_blur'] : '0';
			$_spread = ! empty( $btn_style['shadow_hover_spread'] ) ? $btn_style['shadow_hover_spread'] : '0';
			echo sprintf(
				'box-shadow: %s %s %s %s %s %s;',
				$_inset,
				$_offset_h,
				$_offset_v,
				$_blur,
				$_spread,
				us_get_color( $btn_style['color_shadow_hover'] )
			);
		}

		echo 'background:' . (
			! empty( $btn_style['color_bg_hover'] )
				? us_get_color( $btn_style['color_bg_hover'], /* Gradient */ TRUE )
				: 'transparent'
			) . ';';
		if ( ! empty( $btn_style['color_border_hover'] ) ) {
			$border_color = us_get_color( $btn_style['color_border_hover'], /* Gradient */ TRUE );
			if ( strpos( $border_color, 'grad' ) !== FALSE ) {
				echo 'border-image:' . $border_color . ' 1;';
			} else {
				echo 'border-color:' . $border_color . ';';
			}
		} else {
			echo 'border-color: transparent;';
		}
		if ( ! empty( $btn_style['color_text_hover'] ) ) {
			echo 'color:' . us_get_color( $btn_style['color_text_hover'] ) . '!important;';
		}
		echo '}';

		// Add min-width for Pagination to make correct circles or squares
		if ( isset( $btn_style['line_height'] ) ) {
			$btn_line_height = strpos( $btn_style['line_height'], 'px' ) !== FALSE ? $btn_style['line_height'] : $btn_style['line_height'] . 'em';
		} else {
			$btn_line_height = '1.2em';
		}
		echo '.us-nav-style_' . $btn_style['id'] . ' > *{';
		echo 'min-width:calc(' . $btn_line_height . ' + 2 * ' . $btn_style['height'] . ');';
		echo '}';

		// Check if the button background has a gradient
		$has_gradient = FALSE;
		if (
			strpos( us_get_color( $btn_style['color_bg'], /* Gradient */ TRUE ), 'grad' ) !== FALSE
			OR strpos( us_get_color( $btn_style['color_bg_hover'], /* Gradient */ TRUE ), 'grad' ) !== FALSE
		) {
			$has_gradient = TRUE;
		}

		// Extra layer for "Slide" hover type OR for gradient backgrounds (cause gradients don't support transition)
		if ( ( isset( $btn_style['hover'] ) AND $btn_style['hover'] == 'slide' ) OR $has_gradient ) {

			if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
				echo '.woocommerce .button.add_to_cart_button,';
			}
			echo '.us-btn-style_' . $btn_style['id'] . '{';
			echo 'overflow: hidden;';
			echo '-webkit-transform: translateZ(0);'; // fix for Safari
			echo '}';

			if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
				echo '.no-touch .woocommerce .button.add_to_cart_button > *,';
			}
			echo '.us-btn-style_' . $btn_style['id'] . ' > * {';
			echo 'position: relative;';
			echo 'z-index: 1;';
			echo '}';

			if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
				echo '.no-touch .woocommerce .button.add_to_cart_button:hover,';
			}
			echo '.no-touch .us-btn-style_' . $btn_style['id'] . ':hover {';
			if ( ! empty( us_get_color( $btn_style['color_bg'], /* Gradient */ TRUE ) ) AND ! empty( $btn_style['color_bg_hover'] ) ) {
				echo 'background:' . us_get_color( $btn_style['color_bg'], /* Gradient */ TRUE ) . ';';
			} else {
				echo 'background: transparent;';
			}
			echo '}';

			if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
				echo '.no-touch .woocommerce .button.add_to_cart_button:after,';
			}
			echo '.no-touch .us-btn-style_' . $btn_style['id'] . ':after {';
			echo 'content: ""; position: absolute; top: 0; left: 0; right: 0;';
			if ( $btn_style['hover'] == 'slide' ) {
				echo 'height: 0; transition: height 0.3s;';
			} else {
				echo 'bottom: 0; opacity: 0; transition: opacity 0.3s;';
			}
			echo 'background:' . (
				! empty( $btn_style['color_bg_hover'] )
					? us_get_color( $btn_style['color_bg_hover'], /* Gradient */ TRUE )
					: 'transparent'
				) . ';';
			echo '}';

			if ( $with_shop AND us_get_option( 'shop_primary_btn_style' ) == $btn_style['id'] ) {
				echo '.no-touch .woocommerce .button.add_to_cart_button:hover:after,';
			}
			echo '.no-touch .us-btn-style_' . $btn_style['id'] . ':hover:after {';
			if ( $btn_style['hover'] == 'slide' ) {
				echo 'height: 100%;';
			} else {
				echo 'opacity: 1;';
			}
			echo '}';
		}
	}
}

/* FIELDS STYLE
   ====================================================================================================== */
foreach( us_get_option( 'input_fields' ) as $input_fields ) {

	// Check if the fields has default colors to override them in Rows with other Color Style
	if ( empty( $input_fields['color_bg'] ) OR $input_fields['color_bg'] === 'transparent' ) {
		$_fields_have_no_bg_color = TRUE;
	}
	if ( $input_fields['color_bg'] == '_content_bg_alt' ) {
		$_fields_have_alt_bg_color = TRUE;
	}
	if ( $input_fields['color_border'] == '_content_border' ) {
		$_fields_have_border_color = TRUE;
	}
	if ( $input_fields['color_text'] == '_content_text' ) {
		$_fields_have_text_color = TRUE;
	}

	// Default styles
	echo '.w-filter.state_desktop.style_drop_default .w-filter-item-title,';
	echo '.select2-selection,';
	echo 'select,';
	echo 'textarea,';
	echo 'input:not([type="submit"]),';
	echo '.w-form-checkbox,';
	echo '.w-form-radio {';
	if ( ! empty( $input_fields['font'] ) ) {
		echo sprintf( 'font-family:%s;', us_get_font_family( $input_fields['font'] ) );
	}
	echo sprintf( 'font-weight:%s;', $input_fields['font_weight'] );
	echo sprintf( 'letter-spacing:%s;', $input_fields['letter_spacing'] );
	echo sprintf( 'border-radius:%s;', $input_fields['border_radius'] );

	if ( ! empty( $input_fields['color_bg'] ) ) {
		echo sprintf( 'background:%s;', us_get_color( $input_fields['color_bg'], /* Gradient */ TRUE ) );
	}
	if ( ! empty( $input_fields['color_border'] ) ) {
		echo sprintf( 'border-color:%s;', us_get_color( $input_fields['color_border'] ) );
	}
	if ( ! empty( $input_fields['color_text'] ) ) {
		echo sprintf( 'color:%s;', us_get_color( $input_fields['color_text'] ) );
	}
	if ( ! empty( $input_fields['color_shadow'] ) ) {
		$_shadow_inset = ! empty( $input_fields['shadow_inset'] ) ? 'inset' : '';
		echo sprintf(
			'box-shadow: %s %s %s %s %s %s;',
			$input_fields['shadow_offset_h'],
			$input_fields['shadow_offset_v'],
			$input_fields['shadow_blur'],
			$input_fields['shadow_spread'],
			us_get_color( $input_fields['color_shadow'] ),
			$_shadow_inset
		);
	}
	echo '}';

	// On Focus styles
	echo '.w-filter.state_desktop.style_drop_default .w-filter-item-title:focus,';
	echo '.select2-container--open .select2-selection,';
	echo 'select:focus,';
	echo 'textarea:focus,';
	echo 'input:not([type="submit"]):focus,';
	echo 'input:focus + .w-form-checkbox,';
	echo 'input:focus + .w-form-radio {';

	if ( ! empty( $input_fields['color_bg_focus'] ) ) {
		echo sprintf( 'background:%s !important;', us_get_color( $input_fields['color_bg_focus'], /* Gradient */ TRUE ) );
	}
	if ( ! empty( $input_fields['color_border_focus'] ) ) {
		echo sprintf( 'border-color:%s !important;', us_get_color( $input_fields['color_border_focus'] ) );
	}
	if ( ! empty( $input_fields['color_text_focus'] ) ) {
		echo sprintf( 'color:%s !important;', us_get_color( $input_fields['color_text_focus'] ) );
	}
	if ( ! empty( $input_fields['color_shadow'] ) OR ! empty( $input_fields['color_shadow_focus'] )	) {

		$_shadow_focus_color = ! empty( $input_fields['color_shadow_focus'] )
			? us_get_color( $input_fields['color_shadow_focus'] )
			: us_get_color( $input_fields['color_shadow'] );
		$_shadow_focus_inset = ! empty( $input_fields['shadow_focus_inset'] ) ? 'inset' : '';

		echo sprintf(
			'box-shadow: %s %s %s %s %s %s;',
			$input_fields['shadow_focus_offset_h'],
			$input_fields['shadow_focus_offset_v'],
			$input_fields['shadow_focus_blur'],
			$input_fields['shadow_focus_spread'],
			$_shadow_focus_color,
			$_shadow_focus_inset
		);
	}
	echo '}';

	if ( ! empty( $input_fields['color_text_focus'] ) ) {
		echo '.w-form-row.focused .w-form-row-field > i {';
		echo sprintf( 'color:%s;', us_get_color( $input_fields['color_text_focus'] ) );
		echo '}';
	}

	// For form label separately
	echo '.w-form-row.move_label .w-form-row-label {';
	echo sprintf( 'font-size:%s;', $input_fields['font_size'] );
	echo sprintf( 'top: calc(%s/2 + %s - 0.7em);', $input_fields['height'], $input_fields['border_width'] );
	echo sprintf( 'margin: 0 %s;', $input_fields['padding'] );
	if ( ! empty( $_fields_have_no_bg_color ) ) {
		echo 'background: var(--color-content-bg-grad);';
	} else {
		echo sprintf( 'background-color:%s;', us_get_color( $input_fields['color_bg'] ) );
	}
	if ( ! empty( $input_fields['color_text'] ) ) {
		echo sprintf( 'color:%s;', us_get_color( $input_fields['color_text'] ) );
	}
	echo '}';
	echo '.w-form-row.with_icon.move_label .w-form-row-label {';
	echo sprintf( 'margin-%s: calc(1.6em + %s);', ( is_rtl() ? 'right' : 'left' ), $input_fields['padding'] );
	echo '}';
}

// Add specific input fields styles for sections with other color styles
if ( ! empty( $_fields_have_no_bg_color ) ) { ?>
.color_alternate .w-form-row.move_label .w-form-row-label {
	background: var(--color-alt-content-bg-grad);
	}
.color_footer-top .w-form-row.move_label .w-form-row-label {
	background: var(--color-subfooter-bg-grad);
	}
.color_footer-bottom .w-form-row.move_label .w-form-row-label {
	background: var(--color-footer-bg-grad);
	}
<?php }

if ( ! empty( $_fields_have_alt_bg_color ) ) { ?>
.color_alternate input:not([type="submit"]),
.color_alternate textarea,
.color_alternate select,
.color_alternate .w-form-checkbox,
.color_alternate .w-form-radio,
.color_alternate .move_label .w-form-row-label {
	background: var(--color-alt-content-bg-alt-grad);
	}
.color_footer-top input:not([type="submit"]),
.color_footer-top textarea,
.color_footer-top select,
.color_footer-top .w-form-checkbox,
.color_footer-top .w-form-radio,
.color_footer-top .w-form-row.move_label .w-form-row-label {
	background: var(--color-subfooter-bg-alt-grad);
	}
.color_footer-bottom input:not([type="submit"]),
.color_footer-bottom textarea,
.color_footer-bottom select,
.color_footer-bottom .w-form-checkbox,
.color_footer-bottom .w-form-radio,
.color_footer-bottom .w-form-row.move_label .w-form-row-label {
	background: var(--color-footer-bg-alt-grad);
	}
<?php }

if ( ! empty( $_fields_have_border_color ) ) { ?>
.color_alternate input:not([type="submit"]),
.color_alternate textarea,
.color_alternate select,
.color_alternate .w-form-checkbox,
.color_alternate .w-form-radio {
	border-color: var(--color-alt-content-border);
	}
.color_footer-top input:not([type="submit"]),
.color_footer-top textarea,
.color_footer-top select,
.color_footer-top .w-form-checkbox,
.color_footer-top .w-form-radio {
	border-color: var(--color-subfooter-border);
	}
.color_footer-bottom input:not([type="submit"]),
.color_footer-bottom textarea,
.color_footer-bottom select,
.color_footer-bottom .w-form-checkbox,
.color_footer-bottom .w-form-radio {
	border-color: var(--color-footer-border);
	}
<?php }

if ( ! empty( $_fields_have_text_color ) ) { ?>
.color_alternate input:not([type="submit"]),
.color_alternate textarea,
.color_alternate select,
.color_alternate .w-form-checkbox,
.color_alternate .w-form-radio,
.color_alternate .w-form-row-field > i,
.color_alternate .w-form-row-field:after,
.color_alternate .widget_search form:after,
.color_footer-top input:not([type="submit"]),
.color_footer-top textarea,
.color_footer-top select,
.color_footer-top .w-form-checkbox,
.color_footer-top .w-form-radio,
.color_footer-top .w-form-row-field > i,
.color_footer-top .w-form-row-field:after,
.color_footer-top .widget_search form:after,
.color_footer-bottom input:not([type="submit"]),
.color_footer-bottom textarea,
.color_footer-bottom select,
.color_footer-bottom .w-form-checkbox,
.color_footer-bottom .w-form-radio,
.color_footer-bottom .w-form-row-field > i,
.color_footer-bottom .w-form-row-field:after,
.color_footer-bottom .widget_search form:after {
	color: inherit;
	}
<?php }

// Output styles for fallback icons
if ( us_get_option( 'optimize_assets', FALSE ) ) {
?>
.fa-angle-down:before { content: "\f107" }
.fa-angle-left:before { content: "\f104" }
.fa-angle-right:before { content: "\f105" }
.fa-angle-up:before { content: "\f106" }
.fa-bars:before { content: "\f0c9" }
.fa-check:before { content: "\f00c" }
.fa-comments:before { content: "\f086" }
.fa-copy:before { content: "\f0c5" }
.fa-envelope:before { content: "\f0e0" }
.fa-map-marker-alt:before { content: "\f3c5" }
.fa-mobile:before { content: "\f10b" }
.fa-phone:before { content: "\f095" }
.fa-play:before { content: "\f04b" }
.fa-quote-left:before { content: "\f10d" }
.fa-search-plus:before { content: "\f00e" }
.fa-search:before { content: "\f002" }
.fa-shopping-cart:before { content: "\f07a" }
.fa-star:before { content: "\f005" }
.fa-tags:before { content: "\f02c" }
.fa-times:before { content: "\f00d" }
<?php }

// Output styles with absolute URLs
global $us_template_directory_uri;
?>
.style_phone6-1 > * {
	background-image: url(<?= esc_url( $us_template_directory_uri ) ?>/img/phone-6-black-real.png);
	}
.style_phone6-2 > * {
	background-image: url(<?= esc_url( $us_template_directory_uri ) ?>/img/phone-6-white-real.png);
	}
.style_phone6-3 > * {
	background-image: url(<?= esc_url( $us_template_directory_uri ) ?>/img/phone-6-black-flat.png);
	}
.style_phone6-4 > * {
	background-image: url(<?= esc_url( $us_template_directory_uri ) ?>/img/phone-6-white-flat.png);
	}

<?php if ( us_is_asset_used( 'lmaps' ) ) { ?>
/* Default icon Leaflet URLs */
.leaflet-default-icon-path {
	background-image: url(<?= esc_url( $us_template_directory_uri ) ?>/common/css/vendor/images/marker-icon.png);
	}
<?php }

/* WooCommerce Product gallery settings
  =============================================================================================================================== */
if ( $with_shop AND us_get_option( 'product_gallery' ) == 'slider' ) {

	if ( us_get_option( 'product_gallery_thumbs_pos' ) == 'bottom' ) {
		$cols = (int) us_get_option( 'product_gallery_thumbs_cols', 4 );
		echo '.woocommerce-product-gallery--columns-' . $cols . ' li { width:' . sprintf( '%0.3f', 100 / $cols ) . '%; }';
	} else {
		echo '.woocommerce-product-gallery { display: flex;	}';
		echo '.woocommerce-product-gallery ol {	display: block; order: -1; }';
		echo '.woocommerce-product-gallery ol > li { width:' . us_get_option( 'product_gallery_thumbs_width', '6rem' ) . '; }';
	}

	// Gaps between thumbnails
	if ( $gap_half = (int) us_get_option( 'product_gallery_thumbs_gap', 0 ) / 2 ) {
		if ( us_get_option( 'product_gallery_thumbs_pos' ) == 'bottom' ) {
			echo '.woocommerce-product-gallery ol { margin:' . $gap_half . 'px -' . $gap_half . 'px 0; }';
		} else {
			echo '.woocommerce-product-gallery ol { margin: -' . $gap_half . 'px ' . $gap_half . 'px -' . $gap_half . 'px -' . $gap_half . 'px; }';
			echo '.rtl .woocommerce-product-gallery ol { margin: -' . $gap_half . 'px -' . $gap_half . 'px -' . $gap_half . 'px ' . $gap_half . 'px; }';
		}
		echo '.woocommerce-product-gallery ol > li { padding:' . $gap_half . 'px; }';
	}
}

/* Menu Dropdown Settings
  =============================================================================================================================== */
global $wpdb;

$wpdb_query = 'SELECT posts.ID as ID, meta.meta_value as value FROM ' . $wpdb->posts . ' posts ';
$wpdb_query .= 'RIGHT JOIN ' . $wpdb->postmeta . ' meta on (posts.id = meta.post_id AND meta.meta_key = "us_mega_menu_settings")';
$wpdb_query .= ' WHERE post_type = "nav_menu_item"';
$results = $wpdb->get_results( $wpdb_query, ARRAY_A );

foreach( $results as $result ) {

	$menu_item_id = $result['ID'];
	$settings = unserialize( $result['value'] );
	$dropdown_css_props = '';

	if ( ! isset( $settings['drop_to'] ) ) {

		// Fallback condition for theme versions prior to 6.2 (instead of migration)
		if ( isset( $settings['direction'] ) ) {
			$settings['drop_to'] = ( $settings['direction'] ) ? 'left' : 'right';
		} else {
			$settings['drop_to'] = 'right';
		}
	}

	// Full Width
	if ( $settings['width'] == 'full' ) {
		$dropdown_css_props .= 'left: 0; right: 0;';
		$dropdown_css_props .= 'transform-origin: 50% 0;';

		// Auto or Custom Width
	} else {

		// Center
		if ( $settings['drop_to'] == 'center' ) {
			$dropdown_css_props .= 'left: 50%; right: auto;';

			// Need margin-left for correct centering based on custom width divided by two
			if ( $settings['width'] == 'custom' AND preg_match( '~^(\d*\.?\d*)(.*)$~', $settings['custom_width'], $matches ) ) {
				$dropdown_css_props .= 'margin-left: -' . ( $matches[1] / 2 ) . $matches[2] . ';';
			} else {
				$dropdown_css_props .= 'margin-left: -6rem;';
			}

			// Left
		} elseif ( $settings['drop_to'] == 'left' ) {
			if ( is_rtl() ) {
				$dropdown_css_props .= 'left: 0; right: auto; transform-origin: 0 0;';
			} else {
				$dropdown_css_props .= 'left: auto; right: 0; transform-origin: 100% 0;';
			}
		}
	}

	$dropdown_bg_color = us_get_color( $settings['color_bg'], /* Gradient */ TRUE );
	$dropdown_bg_image = '';

	// Add image properties when image is set
	if ( $dropdown_bg_image_url = wp_get_attachment_image_url( $settings['bg_image'], 'full' ) ) {
		$dropdown_bg_image .= 'url(' . $dropdown_bg_image_url . ') ';
		$dropdown_bg_image .= $settings['bg_image_position'];
		if ( $settings['bg_image_size'] != 'initial' ) {
			$dropdown_bg_image .= '/' . $settings['bg_image_size'];
		}
		$dropdown_bg_image .= ' ';
		$dropdown_bg_image .= $settings['bg_image_repeat'];

		// If the color value contains gradient, add comma for correct appearance
		if ( strpos( $dropdown_bg_color, 'grad' ) !== FALSE ) {
			$dropdown_bg_image .= ',';
		}
	}

	// Output single combined background value
	if ( $dropdown_bg_image != '' OR $dropdown_bg_color != '' ) {
		$dropdown_css_props .= 'background:' . $dropdown_bg_image . ' ' . $dropdown_bg_color . ';';
	}

	if ( $settings['color_text'] != '' ) {
		$dropdown_css_props .= 'color:' . us_get_color( $settings['color_text'] ) . ';';
	}
	if ( $settings['width'] == 'custom' ) {
		$dropdown_css_props .= 'width:' . $settings['custom_width'] . ';';
	}

	// Stretch background to the screen edges
	if ( $settings['width'] == 'full' AND isset( $settings['stretch'] ) AND $settings['stretch'] ) {
		$dropdown_css_props .= 'margin: 0 -50vw;';
		$dropdown_css_props .= 'padding:' . $settings['padding'] . ' 50vw;';
	} elseif ( (int) $settings['padding'] != 0 ) {
		$dropdown_css_props .= 'padding:' . $settings['padding'] . ';';
	}

	// Output dropdown CSS if it's not empty
	if ( ! empty( $dropdown_css_props ) ) {
		echo '.header_hor .w-nav.type_desktop .menu-item-' . $menu_item_id . ' .w-nav-list.level_2 {';
		echo strip_tags( $dropdown_css_props );
		echo '}';
	}

	// Make menu item static in 2 cases
	if ( $settings['width'] == 'full' OR ( isset( $settings['drop_from'] ) AND $settings['drop_from'] == 'header' ) ) {
		echo '.header_hor .w-nav.type_desktop .menu-item-' . $menu_item_id . ' { position: static; }';
	}

}

// Remove filter for protocols removal from URLs for better compatibility with caching plugins and services
if ( ! us_get_option( 'keep_url_protocol', 1 ) ) {
	remove_filter( 'clean_url', 'us_remove_url_protocol', 10 );
}
