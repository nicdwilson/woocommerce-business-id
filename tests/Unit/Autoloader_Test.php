<?php
/**
 * Tests for the classmap autoloader.
 *
 * @package WooCommerceBusinessId\Tests\Unit
 */

namespace WooCommerceBusinessId\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WooCommerceBusinessId\Autoloader;
use WooCommerceBusinessId\Plugin;

/**
 * Tests the classmap autoloader.
 *
 * @covers \WooCommerceBusinessId\Autoloader
 */
class Autoloader_Test extends TestCase {

	/**
	 * Verify a mapped plugin class can be loaded.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_autoload_loads_mapped_plugin_class(): void {
		$classmap   = require dirname( __DIR__, 2 ) . '/src/autoload-classmap.php';
		$autoloader = new Autoloader( dirname( __DIR__, 2 ), $classmap );

		$autoloader->autoload( Plugin::class );

		$this->assertTrue( class_exists( Plugin::class, false ) );
	}
}
