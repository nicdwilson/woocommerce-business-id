<?php
/**
 * Classmap autoloader for WooCommerce Business ID.
 *
 * @package WooCommerceBusinessId
 * @since   0.1.0
 */

namespace WooCommerceBusinessId;

defined( 'ABSPATH' ) || exit;

/**
 * Loads plugin classes from an explicit class map.
 *
 * @since 0.1.0
 */
final class Autoloader {

	/**
	 * Absolute plugin base path.
	 *
	 * @var string
	 */
	private string $base_path;

	/**
	 * Fully qualified class name to plugin-relative file path map.
	 *
	 * @var array<string, string>
	 */
	private array $classmap;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param string                $base_path Absolute plugin base path.
	 * @param array<string, string> $classmap  Fully qualified class name to file path map.
	 */
	public function __construct( string $base_path, array $classmap ) {
		$this->base_path = \rtrim( $base_path, '/\\' ) . DIRECTORY_SEPARATOR;
		$this->classmap  = $classmap;
	}

	/**
	 * Register this autoloader with PHP.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register(): void {
		\spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Load a plugin class if it exists in the class map.
	 *
	 * @since 0.1.0
	 *
	 * @param string $class_name Fully qualified class name.
	 *
	 * @return void
	 */
	public function autoload( string $class_name ): void {
		if ( 0 !== \strpos( $class_name, 'WooCommerceBusinessId\\' ) ) {
			return;
		}

		if ( ! isset( $this->classmap[ $class_name ] ) ) {
			return;
		}

		$file_path = $this->base_path . \ltrim( $this->classmap[ $class_name ], '/' );

		if ( \is_readable( $file_path ) ) {
			require_once $file_path;
		}
	}
}
