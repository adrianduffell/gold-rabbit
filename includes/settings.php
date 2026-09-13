<?php
/**
 * Settings functions.
 *
 * @package OutletPro
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

namespace OutletPro;

defined( 'ABSPATH' ) || exit;

/**
 * WordPress option key used to store the outlet message.
 *
 * @internal
 */
const OUTLET_MESSAGE_OPTION = 'outletpro_message';

/**
 * WordPress option key used to store the badge label text.
 *
 * @internal
 */
const OUTLET_BADGE_LABEL_OPTION = 'outletpro_badge_label';

/**
 * WordPress option key used to store the badge text color.
 *
 * @internal
 */
const OUTLET_BADGE_TEXT_COLOR_OPTION = 'outletpro_badge_text_color';

/**
 * WordPress option key used to store the badge background color.
 *
 * @internal
 */
const OUTLET_BADGE_BG_COLOR_OPTION = 'outletpro_badge_bg_color';

/**
 * WordPress option key used to store the badge background gradient.
 *
 * @internal
 */
const OUTLET_BADGE_BG_GRADIENT_OPTION = 'outletpro_badge_bg_gradient';

/**
 * WordPress option key used to store the badge border radius.
 *
 * @internal
 */
const OUTLET_BADGE_BORDER_RADIUS_OPTION = 'outletpro_badge_border_radius';

/**
 * WordPress option key used to store the badge border color.
 *
 * @internal
 */
const OUTLET_BADGE_BORDER_COLOR_OPTION = 'outletpro_badge_border_color';

/**
 * WordPress option key used to store the badge border style.
 *
 * @internal
 */
const OUTLET_BADGE_BORDER_STYLE_OPTION = 'outletpro_badge_border_style';

/**
 * WordPress option key used to store the badge border width.
 *
 * @internal
 */
const OUTLET_BADGE_BORDER_WIDTH_OPTION = 'outletpro_badge_border_width';

/**
 * WordPress option key used to store the badge font weight.
 *
 * @internal
 */
const OUTLET_BADGE_FONT_WEIGHT_OPTION = 'outletpro_badge_font_weight';

/**
 * WordPress option key used to store the badge scale.
 *
 * @internal
 */
const OUTLET_BADGE_SCALE_OPTION = 'outletpro_badge_scale';

/**
 * WordPress option key used to store the badge density.
 *
 * @internal
 */
const OUTLET_BADGE_DENSITY_OPTION = 'outletpro_badge_density';

/**
 * Sanitize a CSS property value, rejecting values that contain CSS block delimiters or
 * values that fail sanitize_text_field().
 *
 * Intentionally light-weight as CSS is broad and evolving.
 *
 * Accepts int and float in addition to string so that callers may pass numeric
 * CSS values (e.g. a unitless scale factor) without first converting them.
 * The numeric value is converted to its string representation before the
 * remaining sanitization steps run.
 *
 * @internal
 *
 * @param mixed $value The CSS property value to sanitize. Accepts string, int, or float.
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
 */
function sanitize_css_value( $value ): string {
	if ( is_int( $value ) || ( is_float( $value ) && is_finite( $value ) ) ) {
		// Convert numeric types to string for the CSS pipeline.
		$value = (string) $value;
	}

	if ( ! is_string( $value ) ) {
		return '';
	}

	$value = sanitize_text_field( $value );

	if (
		false !== strpos( $value, ';' ) ||
		false !== strpos( $value, '{' ) ||
		false !== strpos( $value, '}' )
	) {
		return '';
	}

	return $value;
}

/**
 * Sanitize an unsigned integer value.
 *
 * Expects an integer > 0, or null, passed as an int, string, or float.
 * All other values return null.
 *
 * Fractional floats are normalized to int.
 *
 * @internal
 *
 * @param mixed $value The value to sanitize.
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
 */
function sanitize_unsigned_integer( $value ): ?int {
	if ( is_null( $value ) ) {
		return null;
	}

	if ( ! is_scalar( $value ) ) {
		return null;
	}

	if ( $value < 0 ) {
		return null;
	}

	if ( is_int( $value ) ) {
		return $value;
	}

	if ( is_string( $value ) && ctype_digit( $value ) ) {
		return (int) $value;
	}

	if ( is_float( $value ) && is_finite( $value ) ) {
		return (int) $value;
	}

	return null;
}

/**
 * Check whether the settings screen is enabled.
 *
 * @internal
 */
function settings_screen_enabled(): bool {
	return (bool) apply_filters( 'outletpro_settings_screen_enabled', false );
}

/**
 * Helper to initialize settings.
 *
 * @internal
 */
function init_settings(): void {
	register_outlet_badge_label_setting();
	register_outlet_badge_text_color_setting();
	register_outlet_badge_bg_color_setting();
	register_outlet_badge_bg_gradient_setting();
	register_outlet_badge_border_color_setting();
	register_outlet_badge_border_style_setting();
	register_outlet_badge_border_width_setting();
	register_outlet_badge_border_radius_setting();
	register_outlet_badge_font_weight_setting();
	register_outlet_badge_scale_setting();
	register_outlet_badge_density_setting();
	register_outlet_message_setting();
}

/**
 * Seed option values with defaults.
 *
 * Uses add_option() so that existing values are never overwritten. This
 * preserves the default at the time of installation even if defaults change
 * in future versions.
 *
 * @internal
 */
function seed_settings(): void {
	add_option( OUTLET_BADGE_LABEL_OPTION, __( 'Authentic', 'outletpro' ) );
	add_option( OUTLET_BADGE_TEXT_COLOR_OPTION, '#111111' );
	add_option( OUTLET_BADGE_BG_COLOR_OPTION, '' );
	add_option(
		OUTLET_BADGE_BG_GRADIENT_OPTION,
		'linear-gradient(90deg,rgb(211,175,55) 0%,rgb(237, 223, 174) 70%,rgb(233,215,154) 100%)'
	);
	add_option( OUTLET_BADGE_BORDER_COLOR_OPTION, '' );
	add_option( OUTLET_BADGE_BORDER_STYLE_OPTION, 'none' );
	add_option( OUTLET_BADGE_BORDER_WIDTH_OPTION, '0' );
	add_option( OUTLET_BADGE_BORDER_RADIUS_OPTION, '2px' );
	add_option( OUTLET_BADGE_FONT_WEIGHT_OPTION, '600' );
	add_option( OUTLET_BADGE_SCALE_OPTION, 166 );
	add_option( OUTLET_BADGE_DENSITY_OPTION, 50 );
	add_option( OUTLET_MESSAGE_OPTION, __( 'Our product images are not AI-generated', 'outletpro' ) );
}

/**
 * Register the outlet badge label setting.
 *
 * @internal
 */
function register_outlet_badge_label_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_BADGE_LABEL_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Outlet badge label', 'outletpro' ),
			'description'       => __( 'Store-wide outlet badge label.', 'outletpro' ),
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the outlet badge text color setting.
 *
 * @since 1.0.0
 */
function register_outlet_badge_text_color_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_BADGE_TEXT_COLOR_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Outlet badge text color', 'outletpro' ),
			'description'       => __( 'Store-wide outlet badge text color.', 'outletpro' ),
			'default'           => '',
			'sanitize_callback' => 'sanitize_hex_color',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the outlet badge background color setting.
 *
 * @since 1.0.0
 */
function register_outlet_badge_bg_color_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_BADGE_BG_COLOR_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Outlet badge background color', 'outletpro' ),
			'description'       => __( 'Store-wide outlet badge background color.', 'outletpro' ),
			'default'           => '',
			'sanitize_callback' => 'sanitize_hex_color',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the outlet badge background gradient setting.
 *
 * @internal
 */
function register_outlet_badge_bg_gradient_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_BADGE_BG_GRADIENT_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Outlet badge background gradient', 'outletpro' ),
			'description'       => __( 'Store-wide outlet badge background gradient.', 'outletpro' ),
			'default'           => '',
			'sanitize_callback' => 'OutletPro\sanitize_css_value',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the outlet badge border radius setting.
 *
 * @internal
 */
function register_outlet_badge_border_radius_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_BADGE_BORDER_RADIUS_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Outlet badge border radius', 'outletpro' ),
			'description'       => __( 'Store-wide outlet badge border radius.', 'outletpro' ),
			'default'           => '',
			'sanitize_callback' => 'OutletPro\sanitize_css_value',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the outlet badge border color setting.
 *
 * @internal
 */
function register_outlet_badge_border_color_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_BADGE_BORDER_COLOR_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Outlet badge border color', 'outletpro' ),
			'description'       => __( 'Store-wide outlet badge border color.', 'outletpro' ),
			'default'           => '',
			'sanitize_callback' => 'OutletPro\sanitize_css_value',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the outlet badge border style setting.
 *
 * @internal
 */
function register_outlet_badge_border_style_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_BADGE_BORDER_STYLE_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Outlet badge border style', 'outletpro' ),
			'description'       => __( 'Store-wide outlet badge border style.', 'outletpro' ),
			'default'           => '',
			'sanitize_callback' => 'OutletPro\sanitize_css_value',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the outlet badge border width setting.
 *
 * @internal
 */
function register_outlet_badge_border_width_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_BADGE_BORDER_WIDTH_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Outlet badge border width', 'outletpro' ),
			'description'       => __( 'Store-wide outlet badge border width.', 'outletpro' ),
			'default'           => '',
			'sanitize_callback' => 'OutletPro\sanitize_css_value',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the outlet badge font weight setting.
 *
 * @internal
 */
function register_outlet_badge_font_weight_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_BADGE_FONT_WEIGHT_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Outlet badge font weight', 'outletpro' ),
			'description'       => __( 'Store-wide outlet badge font weight.', 'outletpro' ),
			'default'           => '',
			'sanitize_callback' => 'OutletPro\sanitize_css_value',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}

/**
 * Register the outlet badge scale setting.
 *
 * @internal
 */
function register_outlet_badge_scale_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_BADGE_SCALE_OPTION,
		array(
			'type'              => 'integer',
			'label'             => __( 'Outlet badge scale', 'outletpro' ),
			'description'       => __( 'Percentage size of the outlet badge relative to the surrounding text cap-height.', 'outletpro' ),
			'default'           => null,
			'sanitize_callback' => 'OutletPro\sanitize_unsigned_integer',
			'show_in_rest'      => array(
				'schema' => array(
					'type'    => 'integer',
					'minimum' => 0,
				),
			),
		)
	);
}

/**
 * Register the outlet badge density setting.
 *
 * @internal
 */
function register_outlet_badge_density_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_BADGE_DENSITY_OPTION,
		array(
			'type'              => 'integer',
			'label'             => __( 'Outlet badge density', 'outletpro' ),
			'description'       => __( 'Controls the ratio between font size and padding for the badge. A lower density results in more whitespace. A Higher density results in a larger font.', 'outletpro' ),
			'default'           => null,
			'sanitize_callback' => 'OutletPro\sanitize_unsigned_integer',
			'show_in_rest'      => array(
				'schema' => array(
					'type'    => 'integer',
					'minimum' => 0,
					'maximum' => 100,
				),
			),
		)
	);
}

/**
 * Register the outlet message setting.
 *
 * @internal
 */
function register_outlet_message_setting(): void {
	register_setting(
		'outletpro',
		OUTLET_MESSAGE_OPTION,
		array(
			'type'              => 'string',
			'label'             => __( 'Outlet message', 'outletpro' ),
			'description'       => __( 'Message displayed on product pages.', 'outletpro' ),
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => array(
				'schema' => array(
					'type' => 'string',
				),
			),
		)
	);
}
