<?php
/**
 * Tests for render_outlet_message_callback().
 *
 * @package OutletPro
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function OutletPro\deinit_blocks;
use function OutletPro\init_blocks;
use function OutletPro\register_outlet_message_block;
use function OutletPro\render_outlet_message_callback;
use const OutletPro\OUTLET_MESSAGE_OPTION;

class Test_Render_Outlet_Message_Callback extends WP_UnitTestCase {

	public function test_returns_message_html_without_outlet_status(): void {
		// Arrange.
		deinit_blocks();
		register_outlet_message_block();
		update_option( OUTLET_MESSAGE_OPTION, 'All of our product images are real.' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'outletpro/outlet-message',
				'attrs'        => array(),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			),
			array( 'postId' => $product->get_id() )
		);

		// Act.
		$result = render_outlet_message_callback( array(), '', $block );

		// Assert.
		$this->assertStringContainsString( 'outletpro-message', $result );
		$this->assertStringContainsString( 'All of our product images are real.', $result );
	}

	public function test_returns_message_html_with_expected_markup(): void {
		// Arrange.
		deinit_blocks();
		register_outlet_message_block();
		update_option( OUTLET_MESSAGE_OPTION, 'Not eligible for change of mind returns' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'outletpro/outlet-message',
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
		$this->assertStringContainsString( 'outletpro-message', $result );
		$this->assertStringContainsString( 'Not eligible for change of mind returns', $result );

		// Cleanup.
		delete_option( OUTLET_MESSAGE_OPTION );
	}

	public function test_message_uses_global_message_option(): void {
		// Arrange.
		deinit_blocks();
		register_outlet_message_block();
		update_option( OUTLET_MESSAGE_OPTION, 'Final sale — no returns.' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'outletpro/outlet-message',
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
		$this->assertStringContainsString( 'Final sale — no returns.', $result );
		$this->assertStringNotContainsString( 'Not eligible for change of mind returns', $result );

		// Cleanup.
		delete_option( OUTLET_MESSAGE_OPTION );
	}

	public function test_message_is_registered_after_init_blocks(): void {
		// Arrange.
		deinit_blocks();

		// Act.
		init_blocks();

		// Assert.
		$this->assertTrue( \WP_Block_Type_Registry::get_instance()->is_registered( 'outletpro/outlet-message' ) );
	}

	public function test_empty_option_returns_empty_string(): void {
		// Arrange.
		deinit_blocks();
		register_outlet_message_block();
		update_option( OUTLET_MESSAGE_OPTION, '' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'outletpro/outlet-message',
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

		// Cleanup.
		delete_option( OUTLET_MESSAGE_OPTION );
	}

	public function test_missing_option_returns_empty_string(): void {
		// Arrange.
		deinit_blocks();
		register_outlet_message_block();
		delete_option( OUTLET_MESSAGE_OPTION );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'outletpro/outlet-message',
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

	public function test_message_is_wrapped_in_paragraph_tag(): void {
		// Arrange.
		deinit_blocks();
		register_outlet_message_block();
		update_option( OUTLET_MESSAGE_OPTION, 'Not eligible for change of mind returns' );
		$product = \WC_Helper_Product::create_simple_product();
		$block   = new WP_Block(
			array(
				'blockName'    => 'outletpro/outlet-message',
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
		$this->assertMatchesRegularExpression( '/<p\b[^>]*>.+<\/p>/', $result );

		// Cleanup.
		delete_option( OUTLET_MESSAGE_OPTION );
	}
}
