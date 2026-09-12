<?php
/**
 * Admin product list table functions.
 *
 * @package OutletPro
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

namespace OutletPro;

defined( 'ABSPATH' ) || exit;

/**
 * Helper to initialize admin product list table features.
 *
 * @internal
 */
function init_admin_product_list_table(): void {
	add_filter( 'woocommerce_admin_stock_html', 'OutletPro\add_badge_to_stock_html_hook', 10, 2 );
}

/**
 * Add the outlet badge to the stock HTML on the product admin list table.
 *
 * Fired by `woocommerce_admin_stock_html`.
 *
 * @internal WordPress filter hook
 * @phpcsSuppress SlevomatCodingStandard.TypeHints
 *
 * @param string|mixed $stock_html The stock HTML.
 * @param \WC_Product  $product    The product object.
 * @return string|mixed The modified stock HTML.
 */
function add_badge_to_stock_html_hook( $stock_html, $product ) {
	try {
		if ( ! is_outlet( $product ) ) {
			return $stock_html;
		}
	} catch ( \Throwable $e ) {
		return $stock_html;
	}
	$label = get_option( OUTLET_BADGE_LABEL_OPTION, '' );
	return $stock_html . '<div class="outletpro-admin-badge">' . esc_html( $label ) . '</div>';
}
