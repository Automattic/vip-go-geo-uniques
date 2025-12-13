<?php
/**
 * Unit tests bootstrap file.
 *
 * @package Automattic\VIPGoGeoUniques
 */

declare( strict_types=1 );

$vendor_dir = dirname( dirname( __DIR__ ) ) . '/vendor';

// Load Brain Monkey bootstrap from yoast/wp-test-utils.
require_once $vendor_dir . '/yoast/wp-test-utils/src/BrainMonkey/bootstrap.php';

// Load Composer autoloader.
require_once $vendor_dir . '/autoload.php';

// Stub add_action before loading the plugin file since the constructor uses it.
if ( ! function_exists( 'add_action' ) ) {
	/**
	 * Stub for add_action to allow loading the plugin.
	 *
	 * @param string   $hook_name Hook name.
	 * @param callable $callback  Callback function.
	 * @param int      $priority  Priority.
	 * @param int      $accepted_args Number of accepted arguments.
	 * @return true
	 */
	function add_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

// Load the plugin file - we need the class definitions.
require_once dirname( dirname( __DIR__ ) ) . '/vip-go-geo-uniques.php';

// Load the base test case.
require_once __DIR__ . '/TestCase.php';
