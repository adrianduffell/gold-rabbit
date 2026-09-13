<?php
/**
 * Tests for register_outlet_badge_bg_gradient_setting().
 *
 * @package OutletPro
 * @copyright 2026 Adrian Duffell
 * @license GNU General Public License v2.0 or later
 */

use function OutletPro\register_outlet_badge_bg_gradient_setting;
use const OutletPro\OUTLET_BADGE_BG_GRADIENT_OPTION;

class Test_Register_Outlet_Badge_Bg_Gradient_Setting extends WP_UnitTestCase {

	public function test_registers_badge_bg_gradient_setting(): void {
		// Arrange.
		unregister_setting( 'outletpro', OUTLET_BADGE_BG_GRADIENT_OPTION );

		// Act.
		register_outlet_badge_bg_gradient_setting();

		// Assert.
		$settings = get_registered_settings();
		$this->assertArrayHasKey( OUTLET_BADGE_BG_GRADIENT_OPTION, $settings );
	}

	public function test_setting_type_is_string(): void {
		// Arrange.
		unregister_setting( 'outletpro', OUTLET_BADGE_BG_GRADIENT_OPTION );

		// Act.
		register_outlet_badge_bg_gradient_setting();

		// Assert.
		$settings = get_registered_settings();
		$this->assertSame( 'string', $settings[ OUTLET_BADGE_BG_GRADIENT_OPTION ]['type'] );
	}

	public function test_setting_is_shown_in_rest(): void {
		// Arrange.
		unregister_setting( 'outletpro', OUTLET_BADGE_BG_GRADIENT_OPTION );
		register_outlet_badge_bg_gradient_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request  = new WP_REST_Request( 'GET', '/wp/v2/settings' );
		$response = rest_do_request( $request );

		// Assert.
		$this->assertArrayHasKey( OUTLET_BADGE_BG_GRADIENT_OPTION, $response->get_data() );
	}

	public function test_setting_default_is_empty_string(): void {
		// Arrange.
		unregister_setting( 'outletpro', OUTLET_BADGE_BG_GRADIENT_OPTION );
		delete_option( OUTLET_BADGE_BG_GRADIENT_OPTION );
		register_outlet_badge_bg_gradient_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request  = new WP_REST_Request( 'GET', '/wp/v2/settings' );
		$response = rest_do_request( $request );
		$data     = $response->get_data();

		// Assert.
		$this->assertSame( '', $data[ OUTLET_BADGE_BG_GRADIENT_OPTION ] );
	}

	public function test_setting_can_be_updated_via_rest(): void {
		// Arrange.
		unregister_setting( 'outletpro', OUTLET_BADGE_BG_GRADIENT_OPTION );
		register_outlet_badge_bg_gradient_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$gradient = 'linear-gradient(red, blue)';

		// Act.
		$request = new WP_REST_Request( 'POST', '/wp/v2/settings' );
		$request->set_param( OUTLET_BADGE_BG_GRADIENT_OPTION, $gradient );
		$response = rest_do_request( $request );
		$data     = $response->get_data();

		// Assert.
		$this->assertSame( $gradient, $data[ OUTLET_BADGE_BG_GRADIENT_OPTION ] );
		$this->assertSame( $gradient, get_option( OUTLET_BADGE_BG_GRADIENT_OPTION ) );
	}

	public function test_setting_rejects_unsafe_css_via_rest(): void {
		// Arrange.
		unregister_setting( 'outletpro', OUTLET_BADGE_BG_GRADIENT_OPTION );
		register_outlet_badge_bg_gradient_setting();
		$user_id = $this->factory->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Act.
		$request = new WP_REST_Request( 'POST', '/wp/v2/settings' );
		$request->set_param( OUTLET_BADGE_BG_GRADIENT_OPTION, 'linear-gradient(red, blue); color: red' );
		$response = rest_do_request( $request );
		$data     = $response->get_data();

		// Assert.
		$this->assertSame( '', $data[ OUTLET_BADGE_BG_GRADIENT_OPTION ] );
		$this->assertSame( '', get_option( OUTLET_BADGE_BG_GRADIENT_OPTION ) );
	}
}
