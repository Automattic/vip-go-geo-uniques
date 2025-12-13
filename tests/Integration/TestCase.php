<?php
/**
 * Base test case for integration tests.
 *
 * @package Automattic\VIPGoGeoUniques\Tests\Integration
 */

declare( strict_types=1 );

namespace Automattic\VIPGoGeoUniques\Tests\Integration;

use Yoast\WPTestUtils\WPIntegration\TestCase as WPIntegrationTestCase;

/**
 * Abstract base class for VIP Go Geo Uniques integration tests.
 */
abstract class TestCase extends WPIntegrationTestCase {

	/**
	 * Sets up test fixtures.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		// Reset the class static properties before each test.
		$this->reset_geo_uniques_class();
	}

	/**
	 * Cleans up after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		unset( $_SERVER['GEOIP_COUNTRY_CODE'] );
		parent::tearDown();
	}

	/**
	 * Resets the VIP_Go_Geo_Uniques class static properties.
	 *
	 * Uses reflection to access private static properties.
	 *
	 * @return void
	 */
	protected function reset_geo_uniques_class(): void {
		$reflection = new \ReflectionClass( \VIP_Go_Geo_Uniques::class );

		$default_location = $reflection->getProperty( 'default_location' );
		$default_location->setAccessible( true );
		$default_location->setValue( null, 'default' );

		$supported_locations = $reflection->getProperty( 'supported_locations' );
		$supported_locations->setAccessible( true );
		$supported_locations->setValue( null, [] );
	}
}
