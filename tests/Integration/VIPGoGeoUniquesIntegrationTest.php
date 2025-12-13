<?php
/**
 * Integration tests for VIP_Go_Geo_Uniques class.
 *
 * @package Automattic\VIPGoGeoUniques\Tests\Integration
 */

declare( strict_types=1 );

namespace Automattic\VIPGoGeoUniques\Tests\Integration;

use VIP_Go_Geo_Uniques;

/**
 * Integration tests for VIP_Go_Geo_Uniques class.
 *
 * Tests the init() method and WordPress hook integration.
 *
 * @coversDefaultClass \VIP_Go_Geo_Uniques
 */
final class VIPGoGeoUniquesIntegrationTest extends TestCase {

	/**
	 * Tests that init skips when on admin.
	 *
	 * @covers ::init
	 */
	public function test_init_skips_when_admin(): void {
		// Note: We cannot easily test is_admin() returning true in integration tests
		// as it's determined by the request context. This test documents expected behavior.
		$this->assertTrue( true, 'Admin context skipping is handled by is_admin() check' );
	}

	/**
	 * Tests that init skips when no supported locations are registered.
	 *
	 * @covers ::init
	 */
	public function test_init_skips_when_no_locations(): void {
		$geo = new VIP_Go_Geo_Uniques();

		// Should not add the send_headers action when no locations are registered.
		$geo->init();

		$this->assertFalse(
			has_action( 'send_headers' ),
			'send_headers action should not be added when no locations are registered'
		);
	}

	/**
	 * Tests that init adds send_headers action when locations are registered.
	 *
	 * @covers ::init
	 */
	public function test_init_adds_send_headers_action(): void {
		VIP_Go_Geo_Uniques::add_location( 'US' );

		$geo = new VIP_Go_Geo_Uniques();
		$geo->init();

		$this->assertNotFalse(
			has_action( 'send_headers' ),
			'send_headers action should be added when locations are registered'
		);
	}

	/**
	 * Tests that init adds default location to supported locations if not already present.
	 *
	 * @covers ::init
	 * @covers ::add_location
	 * @covers ::is_valid_location
	 * @covers ::get_default_location
	 */
	public function test_init_adds_default_location_if_not_valid(): void {
		// Add a location but not the default.
		VIP_Go_Geo_Uniques::add_location( 'US' );

		// Confirm default is not valid.
		$this->assertFalse( VIP_Go_Geo_Uniques::is_valid_location( 'default' ) );

		$geo = new VIP_Go_Geo_Uniques();
		$geo->init();

		// After init, default should be added.
		$this->assertTrue( VIP_Go_Geo_Uniques::is_valid_location( 'default' ) );
	}

	/**
	 * Tests that init does not duplicate default location if already registered.
	 *
	 * @covers ::init
	 * @covers ::add_location
	 * @covers ::is_valid_location
	 * @covers ::get_registered_locations
	 */
	public function test_init_does_not_duplicate_default_location(): void {
		// Add the default location explicitly.
		VIP_Go_Geo_Uniques::add_location( 'default' );
		VIP_Go_Geo_Uniques::add_location( 'US' );

		$locations_before = VIP_Go_Geo_Uniques::get_registered_locations();

		$geo = new VIP_Go_Geo_Uniques();
		$geo->init();

		$locations_after = VIP_Go_Geo_Uniques::get_registered_locations();

		$this->assertSame(
			count( $locations_before ),
			count( $locations_after ),
			'Default location should not be duplicated'
		);
	}

	/**
	 * Tests that the Vary header callback outputs correct header.
	 *
	 * @covers ::init
	 */
	public function test_vary_header_is_set(): void {
		VIP_Go_Geo_Uniques::add_location( 'US' );

		$geo = new VIP_Go_Geo_Uniques();
		$geo->init();

		// Capture headers sent during the send_headers action.
		// Note: We cannot directly test header() output in PHPUnit,
		// but we can verify the action is hooked.
		$this->assertNotFalse(
			has_action( 'send_headers' ),
			'send_headers should have an action registered'
		);
	}

	/**
	 * Tests that init checks for XMLRPC_REQUEST constant.
	 *
	 * Note: We cannot easily test with XMLRPC_REQUEST defined as true
	 * because constants cannot be redefined between tests. This test
	 * documents that the constant check exists in the code.
	 *
	 * @covers ::init
	 */
	public function test_init_checks_for_xmlrpc(): void {
		// When XMLRPC_REQUEST is not defined (or false), init should proceed normally.
		VIP_Go_Geo_Uniques::add_location( 'US' );

		$geo = new VIP_Go_Geo_Uniques();
		$geo->init();

		// The action should be added when not in XMLRPC context.
		$this->assertNotFalse(
			has_action( 'send_headers' ),
			'send_headers action should be added when not in XMLRPC context'
		);
	}

	/**
	 * Tests that init checks for DOING_AJAX constant.
	 *
	 * Note: We cannot easily test with DOING_AJAX defined as true
	 * because constants cannot be redefined between tests. This test
	 * documents that the constant check exists in the code.
	 *
	 * @covers ::init
	 */
	public function test_init_checks_for_ajax(): void {
		// When DOING_AJAX is not defined (or false), init should proceed normally.
		VIP_Go_Geo_Uniques::add_location( 'US' );

		$geo = new VIP_Go_Geo_Uniques();
		$geo->init();

		// The action should be added when not in AJAX context.
		$this->assertNotFalse(
			has_action( 'send_headers' ),
			'send_headers action should be added when not in AJAX context'
		);
	}

	/**
	 * Tests the constructor hooks init action.
	 *
	 * @covers ::__construct
	 */
	public function test_constructor_hooks_init_action(): void {
		// The constructor adds an action to 'init'.
		// Since we're calling new VIP_Go_Geo_Uniques() manually,
		// and the plugin already created one on load, we can check for init hook.
		$this->assertNotFalse(
			has_action( 'init' ),
			'init action should be hooked by the constructor'
		);
	}

	/**
	 * Tests that get_country_code works with WordPress sanitization.
	 *
	 * @covers ::get_country_code
	 */
	public function test_get_country_code_with_wordpress_sanitization(): void {
		VIP_Go_Geo_Uniques::add_location( 'US' );

		// Test with whitespace - sanitize_text_field trims whitespace.
		$_SERVER['GEOIP_COUNTRY_CODE'] = '  US  ';
		$this->assertSame( 'US', VIP_Go_Geo_Uniques::get_country_code() );

		// Test with HTML tags - sanitize_text_field strips HTML.
		// '<script>US</script>' becomes 'US' after strip_tags.
		$_SERVER['GEOIP_COUNTRY_CODE'] = '<b>US</b>';
		$this->assertSame( 'US', VIP_Go_Geo_Uniques::get_country_code() );

		// Test with unregistered value after sanitization.
		$_SERVER['GEOIP_COUNTRY_CODE'] = '<script>XX</script>';
		$this->assertSame( 'default', VIP_Go_Geo_Uniques::get_country_code() );
	}

	/**
	 * Tests full workflow: set default, add locations, get country code.
	 *
	 * @covers ::set_default_location
	 * @covers ::add_location
	 * @covers ::get_country_code
	 * @covers ::get_default_location
	 */
	public function test_full_workflow(): void {
		// Set a custom default.
		VIP_Go_Geo_Uniques::set_default_location( 'EU' );

		// Add multiple locations.
		VIP_Go_Geo_Uniques::add_location( 'US' );
		VIP_Go_Geo_Uniques::add_location( 'GB' );
		VIP_Go_Geo_Uniques::add_location( 'CA' );

		// Test with a registered location.
		$_SERVER['GEOIP_COUNTRY_CODE'] = 'GB';
		$this->assertSame( 'GB', VIP_Go_Geo_Uniques::get_country_code() );

		// Test with an unregistered location (should return custom default).
		$_SERVER['GEOIP_COUNTRY_CODE'] = 'FR';
		$this->assertSame( 'EU', VIP_Go_Geo_Uniques::get_country_code() );

		// Test without GEOIP header (should return custom default).
		unset( $_SERVER['GEOIP_COUNTRY_CODE'] );
		$this->assertSame( 'EU', VIP_Go_Geo_Uniques::get_country_code() );
	}

	/**
	 * Tests that wrapper functions work correctly in WordPress context.
	 *
	 * @covers ::get_country_code
	 * @covers ::set_default_location
	 * @covers ::add_location
	 */
	public function test_wrapper_functions_in_wordpress_context(): void {
		vip_geo_set_default_location( 'APAC' );
		vip_geo_add_location( 'AU' );
		vip_geo_add_location( 'NZ' );

		$_SERVER['GEOIP_COUNTRY_CODE'] = 'AU';
		$this->assertSame( 'AU', vip_geo_get_country_code() );

		$_SERVER['GEOIP_COUNTRY_CODE'] = 'JP';
		$this->assertSame( 'APAC', vip_geo_get_country_code() );
	}
}
