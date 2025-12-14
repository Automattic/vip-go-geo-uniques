<?php
/**
 * Base test case for unit tests.
 *
 * @package Automattic\VIPGoGeoUniques\Tests\Unit
 */

declare( strict_types=1 );

namespace Automattic\VIPGoGeoUniques\Tests\Unit;

use Brain\Monkey;
use Yoast\WPTestUtils\BrainMonkey\YoastTestCase;

/**
 * Abstract base class for VIP Go Geo Uniques unit tests.
 */
abstract class TestCase extends YoastTestCase {

	/**
	 * Sets up test fixtures and stubs WordPress functions.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		// Stub WordPress functions used by the plugin.
		Monkey\Functions\stubs(
			[
				'sanitize_text_field' => static function ( $str ) {
					return trim( strip_tags( $str ) );
				},
				'is_admin'            => false,
			]
		);

		// Reset the class static properties before each test.
		$this->reset_geo_uniques_class();
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
