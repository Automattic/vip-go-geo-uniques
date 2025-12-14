<?php
/**
 * PHPUnit bootstrap file for VIP Go Geo Uniques plugin tests.
 *
 * @package Automattic\VIPGoGeoUniques
 */

declare( strict_types=1 );

namespace Automattic\VIPGoGeoUniques\Tests;

use Yoast\WPTestUtils\WPIntegration;

$vendor_dir = dirname( __DIR__ ) . '/vendor';

require_once $vendor_dir . '/yoast/wp-test-utils/src/WPIntegration/bootstrap-functions.php';

// Check for a `--testsuite integration` or `--testsuite=integration` arg when calling phpunit,
// and use it to conditionally load up WordPress.
$argv_local     = $GLOBALS['argv'] ?? [];
$key            = (int) array_search( '--testsuite', $argv_local, true );
$is_integration = false;

// Check for --testsuite integration (two separate args).
if ( $key && isset( $argv_local[ $key + 1 ] ) && 'integration' === $argv_local[ $key + 1 ] ) {
	$is_integration = true;
}

// Check for --testsuite=integration (single arg with equals).
foreach ( $argv_local as $arg ) {
	if ( '--testsuite=integration' === $arg ) {
		$is_integration = true;
		break;
	}
}

if ( $is_integration ) {
	$_tests_dir = WPIntegration\get_path_to_wp_test_dir();

	if ( empty( $_tests_dir ) ) {
		echo 'ERROR: Could not find WordPress test library directory.' . PHP_EOL;
		echo 'Make sure wp-env is running: npm run wp-env start' . PHP_EOL;
		exit( 1 );
	}

	// Give access to tests_add_filter() function.
	require_once $_tests_dir . '/includes/functions.php';

	/**
	 * Manually load the plugin being tested.
	 */
	\tests_add_filter(
		'muplugins_loaded',
		function (): void {
			require dirname( __DIR__ ) . '/vip-go-geo-uniques.php';
		}
	);

	// Make sure the Composer autoload file has been generated.
	WPIntegration\check_composer_autoload_exists();

	// Start up the WP testing environment.
	require $_tests_dir . '/includes/bootstrap.php';

	/*
	 * Register the custom autoloader to overload the PHPUnit MockObject classes when running on PHP 8.
	 *
	 * This function has to be called _last_, after the WP test bootstrap to make sure it registers
	 * itself in FRONT of the Composer autoload (which also prepends itself to the autoload queue).
	 */
	WPIntegration\register_mockobject_autoloader();

	// Add custom test case.
	require __DIR__ . '/Integration/TestCase.php';
} else {
	// Unit tests: load Brain Monkey bootstrap.
	require_once $vendor_dir . '/yoast/wp-test-utils/src/BrainMonkey/bootstrap.php';

	// Load Composer autoloader.
	require_once $vendor_dir . '/autoload.php';

	// Stub add_action before loading the plugin file since the constructor uses it.
	if ( ! function_exists( 'add_action' ) ) {
		/**
		 * Stub for add_action to allow loading the plugin.
		 *
		 * @param string   $hook_name     Hook name.
		 * @param callable $callback      Callback function.
		 * @param int      $priority      Priority.
		 * @param int      $accepted_args Number of accepted arguments.
		 * @return true
		 */
		function add_action( $hook_name, $callback, $priority = 10, $accepted_args = 1 ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames
			return true;
		}
	}

	// Load the plugin file - we need the class definitions.
	require_once dirname( __DIR__ ) . '/vip-go-geo-uniques.php';

	// Load the base test case.
	require_once __DIR__ . '/Unit/TestCase.php';
}
