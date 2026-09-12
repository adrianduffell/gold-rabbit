<?php
/**
 * Tests for render_outlet_badge_callback().
 *
 * @package OutletPro
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function OutletPro\deinit_blocks;
use function OutletPro\init_blocks;
use function OutletPro\register_outlet_badge_block;
use function OutletPro\render_outlet_badge_callback;
use const OutletPro\OUTLET_BADGE_LABEL_OPTION;

class Test_Render_Outlet_Badge_Callback extends WP_UnitTestCase {

	public function test_returns_badge_html_for_product(): void {
		// Arrange.
		deinit_blocks();
		register_outlet_badge_block();
		update_option( OUTLET_BADGE_LABEL_OPTION, 'Clearance' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'outletpro/outlet-badge',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			),
			array( 'postId' => $product->get_id() )
		);

		// Act.
		$result = $block->render();

		// Assert.
		$this->assertStringContainsString( 'outletpro-badge', $result );
		$this->assertStringContainsString( 'Clearance', $result );
		$this->assertMatchesRegularExpression( '/<div[^>]+class="[^"]*outletpro-badge/', $result );
	}

	public function test_badge_uses_global_badge_label_option(): void {
		// Arrange.
		deinit_blocks();
		register_outlet_badge_block();
		update_option( OUTLET_BADGE_LABEL_OPTION, 'Sale' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'outletpro/outlet-badge',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			),
			array( 'postId' => $product->get_id() )
		);

		// Act.
		$result = $block->render();

		// Assert.
		$this->assertStringContainsString( 'Sale', $result );
		$this->assertStringNotContainsString( 'Clearance', $result );
	}

	public function test_returns_empty_string_when_post_id_is_zero(): void {
		// Arrange.
		deinit_blocks();
		register_outlet_badge_block();
		$block = new WP_Block(
			array(
				'blockName'    => 'outletpro/outlet-badge',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			),
			array()
		);

		// Act.
		$result = render_outlet_badge_callback( array(), '', $block );

		// Assert.
		$this->assertSame( '', $result );
	}

	public function test_badge_is_registered_after_init_blocks(): void {
		// Arrange.
		deinit_blocks();

		// Act.
		init_blocks();

		// Assert.
		$this->assertTrue( \WP_Block_Type_Registry::get_instance()->is_registered( 'outletpro/outlet-badge' ) );
	}

	public function test_returns_empty_string_when_label_is_empty(): void {
		// Arrange.
		deinit_blocks();
		register_outlet_badge_block();
		update_option( OUTLET_BADGE_LABEL_OPTION, '' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'outletpro/outlet-badge',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			),
			array( 'postId' => $product->get_id() )
		);

		// Act.
		$result = $block->render();

		// Assert.
		$this->assertSame( '', $result );
	}
}
