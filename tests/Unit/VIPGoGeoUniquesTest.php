<?php
/**
 * Unit tests for VIP_Go_Geo_Uniques class.
 *
 * @package Automattic\VIPGoGeoUniques\Tests\Unit
 */

declare( strict_types=1 );

namespace Automattic\VIPGoGeoUniques\Tests\Unit;

use VIP_Go_Geo_Uniques;

/**
 * Tests for VIP_Go_Geo_Uniques class static methods.
 *
 * @coversDefaultClass \VIP_Go_Geo_Uniques
 */
final class VIPGoGeoUniquesTest extends TestCase {

	/**
	 * Tests that get_default_location returns 'default' initially.
	 *
	 * @covers ::get_default_location
	 */
	public function test_get_default_location_returns_default_initially(): void {
		$this->assertSame( 'default', VIP_Go_Geo_Uniques::get_default_location() );
	}

	/**
	 * Tests that set_default_location changes the default location.
	 *
	 * @covers ::set_default_location
	 * @covers ::get_default_location
	 */
	public function test_set_default_location_changes_default(): void {
		VIP_Go_Geo_Uniques::set_default_location( 'US' );

		$this->assertSame( 'US', VIP_Go_Geo_Uniques::get_default_location() );
	}

	/**
	 * Tests that add_location registers a new location.
	 *
	 * @covers ::add_location
	 * @covers ::is_valid_location
	 */
	public function test_add_location_registers_location(): void {
		$result = VIP_Go_Geo_Uniques::add_location( 'US' );

		$this->assertTrue( $result );
		$this->assertTrue( VIP_Go_Geo_Uniques::is_valid_location( 'US' ) );
	}

	/**
	 * Tests that is_valid_location returns false for unregistered locations.
	 *
	 * @covers ::is_valid_location
	 */
	public function test_is_valid_location_returns_false_for_unregistered(): void {
		$this->assertFalse( VIP_Go_Geo_Uniques::is_valid_location( 'XX' ) );
	}

	/**
	 * Tests that is_valid_location returns true for registered locations.
	 *
	 * @covers ::is_valid_location
	 * @covers ::add_location
	 */
	public function test_is_valid_location_returns_true_for_registered(): void {
		VIP_Go_Geo_Uniques::add_location( 'GB' );

		$this->assertTrue( VIP_Go_Geo_Uniques::is_valid_location( 'GB' ) );
	}

	/**
	 * Tests that get_registered_locations returns empty array initially.
	 *
	 * @covers ::get_registered_locations
	 */
	public function test_get_registered_locations_returns_empty_initially(): void {
		$this->assertSame( [], VIP_Go_Geo_Uniques::get_registered_locations() );
	}

	/**
	 * Tests that get_registered_locations returns all added locations.
	 *
	 * @covers ::get_registered_locations
	 * @covers ::add_location
	 */
	public function test_get_registered_locations_returns_all_locations(): void {
		VIP_Go_Geo_Uniques::add_location( 'US' );
		VIP_Go_Geo_Uniques::add_location( 'GB' );
		VIP_Go_Geo_Uniques::add_location( 'CA' );

		$locations = VIP_Go_Geo_Uniques::get_registered_locations();

		$this->assertCount( 3, $locations );
		$this->assertContains( 'US', $locations );
		$this->assertContains( 'GB', $locations );
		$this->assertContains( 'CA', $locations );
	}

	/**
	 * Tests that get_country_code returns default when GEOIP_COUNTRY_CODE is not set.
	 *
	 * @covers ::get_country_code
	 * @covers ::get_default_location
	 */
	public function test_get_country_code_returns_default_when_not_set(): void {
		unset( $_SERVER['GEOIP_COUNTRY_CODE'] );

		$this->assertSame( 'default', VIP_Go_Geo_Uniques::get_country_code() );
	}

	/**
	 * Tests that get_country_code returns default when GEOIP_COUNTRY_CODE is empty.
	 *
	 * @covers ::get_country_code
	 * @covers ::get_default_location
	 */
	public function test_get_country_code_returns_default_when_empty(): void {
		$_SERVER['GEOIP_COUNTRY_CODE'] = '';

		$this->assertSame( 'default', VIP_Go_Geo_Uniques::get_country_code() );
	}

	/**
	 * Tests that get_country_code returns default when location is not registered.
	 *
	 * @covers ::get_country_code
	 * @covers ::get_default_location
	 * @covers ::is_valid_location
	 */
	public function test_get_country_code_returns_default_for_unregistered_location(): void {
		$_SERVER['GEOIP_COUNTRY_CODE'] = 'XX';

		$this->assertSame( 'default', VIP_Go_Geo_Uniques::get_country_code() );
	}

	/**
	 * Tests that get_country_code returns the country code when registered.
	 *
	 * @covers ::get_country_code
	 * @covers ::add_location
	 * @covers ::is_valid_location
	 */
	public function test_get_country_code_returns_code_when_registered(): void {
		VIP_Go_Geo_Uniques::add_location( 'US' );
		$_SERVER['GEOIP_COUNTRY_CODE'] = 'US';

		$this->assertSame( 'US', VIP_Go_Geo_Uniques::get_country_code() );
	}

	/**
	 * Tests that get_country_code uses the custom default location.
	 *
	 * @covers ::get_country_code
	 * @covers ::set_default_location
	 * @covers ::get_default_location
	 */
	public function test_get_country_code_uses_custom_default(): void {
		VIP_Go_Geo_Uniques::set_default_location( 'EU' );
		$_SERVER['GEOIP_COUNTRY_CODE'] = 'XX';

		$this->assertSame( 'EU', VIP_Go_Geo_Uniques::get_country_code() );
	}

	/**
	 * Tests that get_country_code sanitizes the input.
	 *
	 * @covers ::get_country_code
	 * @covers ::add_location
	 */
	public function test_get_country_code_sanitizes_input(): void {
		VIP_Go_Geo_Uniques::add_location( 'US' );
		$_SERVER['GEOIP_COUNTRY_CODE'] = '  US  ';

		$this->assertSame( 'US', VIP_Go_Geo_Uniques::get_country_code() );
	}

	/**
	 * Tests that duplicate locations can be added (no uniqueness check).
	 *
	 * @covers ::add_location
	 * @covers ::get_registered_locations
	 */
	public function test_add_location_allows_duplicates(): void {
		VIP_Go_Geo_Uniques::add_location( 'US' );
		VIP_Go_Geo_Uniques::add_location( 'US' );

		$locations = VIP_Go_Geo_Uniques::get_registered_locations();

		$this->assertCount( 2, $locations );
	}

	/**
	 * Tests the vip_geo_get_country_code wrapper function.
	 *
	 * @covers ::get_country_code
	 */
	public function test_vip_geo_get_country_code_wrapper(): void {
		VIP_Go_Geo_Uniques::add_location( 'GB' );
		$_SERVER['GEOIP_COUNTRY_CODE'] = 'GB';

		$this->assertSame( 'GB', vip_geo_get_country_code() );
	}

	/**
	 * Tests the vip_geo_set_default_location wrapper function.
	 *
	 * @covers ::set_default_location
	 */
	public function test_vip_geo_set_default_location_wrapper(): void {
		vip_geo_set_default_location( 'FR' );

		$this->assertSame( 'FR', VIP_Go_Geo_Uniques::get_default_location() );
	}

	/**
	 * Tests the vip_geo_add_location wrapper function.
	 *
	 * @covers ::add_location
	 */
	public function test_vip_geo_add_location_wrapper(): void {
		$result = vip_geo_add_location( 'DE' );

		$this->assertTrue( $result );
		$this->assertTrue( VIP_Go_Geo_Uniques::is_valid_location( 'DE' ) );
	}

	/**
	 * Cleanup after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		unset( $_SERVER['GEOIP_COUNTRY_CODE'] );
		parent::tearDown();
	}
}
