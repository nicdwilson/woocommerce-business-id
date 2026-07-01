<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package WooCommerceBusinessId\Tests
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir && ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

$woocommerce_business_id_autoload = dirname( __DIR__ ) . '/vendor/autoload.php';

if ( file_exists( $woocommerce_business_id_autoload ) ) {
	require_once $woocommerce_business_id_autoload;
}

if ( $_tests_dir ) {
	require_once $_tests_dir . '/includes/functions.php';

	tests_add_filter(
		'muplugins_loaded',
		static function (): void {
			require dirname( __DIR__ ) . '/woocommerce-business-id.php';
		}
	);

	require $_tests_dir . '/includes/bootstrap.php';
	return;
}

if ( ! defined( 'WOOCOMMERCE_BUSINESS_ID_BASENAME' ) ) {
	define( 'WOOCOMMERCE_BUSINESS_ID_BASENAME', 'woocommerce-business-id/woocommerce-business-id.php' );
}

require_once dirname( __DIR__ ) . '/src/class-autoloader.php';

$woocommerce_business_id_classmap        = require dirname( __DIR__ ) . '/src/autoload-classmap.php';
$woocommerce_business_id_test_autoloader = new WooCommerceBusinessId\Autoloader(
	dirname( __DIR__ ),
	$woocommerce_business_id_classmap
);
$woocommerce_business_id_test_autoloader->register();

require_once dirname( __DIR__ ) . '/src/functions.php';

if ( ! function_exists( '__' ) ) {
	/**
	 * Minimal translation shim for isolated unit tests.
	 *
	 * @param string $text Text to translate.
	 *
	 * @return string
	 */
	function __( string $text ): string {
		return $text;
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	/**
	 * Minimal escaped translation shim for isolated unit tests.
	 *
	 * @param string $text Text to translate.
	 *
	 * @return string
	 */
	function esc_html__( string $text ): string {
		return esc_html( $text );
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	/**
	 * Minimal sanitize_text_field shim for isolated unit tests.
	 *
	 * @param mixed $value Value to sanitize.
	 *
	 * @return string
	 */
	function sanitize_text_field( mixed $value ): string {
		if ( ! is_scalar( $value ) ) {
			return '';
		}

		$value = (string) $value;
		$value = preg_replace( '/<[^>]*>/', '', $value );

		if ( null === $value ) {
			return '';
		}

		$value = preg_replace( '/[\r\n\t ]+/', ' ', $value );

		if ( null === $value ) {
			return '';
		}

		$value = preg_replace( '/%[A-Fa-f0-9]{2}/', '', $value );

		if ( null === $value ) {
			return '';
		}

		return trim( $value );
	}
}

if ( ! function_exists( 'get_option' ) ) {
	/**
	 * Minimal get_option shim for isolated unit tests.
	 *
	 * @param string $option  Option name.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	function get_option( string $option, mixed $default = false ): mixed {
		return $GLOBALS['woocommerce_business_id_test_options'][ $option ] ?? $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	/**
	 * Minimal update_option shim for isolated unit tests.
	 *
	 * @param string $option Option name.
	 * @param mixed  $value  Option value.
	 *
	 * @return true
	 */
	function update_option( string $option, mixed $value ): bool {
		$GLOBALS['woocommerce_business_id_test_options'][ $option ] = $value;

		return true;
	}
}

if ( ! function_exists( 'delete_option' ) ) {
	/**
	 * Minimal delete_option shim for isolated unit tests.
	 *
	 * @param string $option Option name.
	 *
	 * @return true
	 */
	function delete_option( string $option ): bool {
		unset( $GLOBALS['woocommerce_business_id_test_options'][ $option ] );

		return true;
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	/**
	 * Minimal esc_html shim for isolated unit tests.
	 *
	 * @param mixed $text Text to escape.
	 *
	 * @return string
	 */
	function esc_html( mixed $text ): string {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	/**
	 * Minimal wp_strip_all_tags shim for isolated unit tests.
	 *
	 * @param string $text          Text to strip.
	 * @param bool   $remove_breaks Whether to remove line breaks.
	 *
	 * @return string
	 */
	function wp_strip_all_tags( string $text, bool $remove_breaks = false ): string {
		$text = strip_tags( $text );

		if ( $remove_breaks ) {
			$text = preg_replace( '/[\r\n\t ]+/', ' ', $text );

			if ( null === $text ) {
				return '';
			}
		}

		return trim( $text );
	}
}

if ( ! function_exists( 'is_admin' ) ) {
	/**
	 * Minimal is_admin shim for isolated unit tests.
	 *
	 * @return bool
	 */
	function is_admin(): bool {
		return (bool) ( $GLOBALS['woocommerce_business_id_test_is_admin'] ?? false );
	}
}

if ( ! function_exists( 'load_plugin_textdomain' ) ) {
	/**
	 * Minimal load_plugin_textdomain shim for isolated unit tests.
	 *
	 * @param string       $domain          Text domain.
	 * @param bool         $deprecated      Deprecated argument.
	 * @param string|false $plugin_rel_path Plugin relative language path.
	 *
	 * @return true
	 */
	function load_plugin_textdomain(
		string $domain,
		bool $deprecated = false,
		string|false $plugin_rel_path = false
	): bool {
		unset( $domain, $deprecated, $plugin_rel_path );

		return true;
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	/**
	 * Minimal wp_kses_post shim for isolated unit tests.
	 *
	 * @param mixed $text Text to filter.
	 *
	 * @return string
	 */
	function wp_kses_post( mixed $text ): string {
		return (string) $text;
	}
}

if ( ! function_exists( 'add_action' ) ) {
	/**
	 * Minimal add_action shim for isolated unit tests.
	 *
	 * @param string   $hook_name     Hook name.
	 * @param callable $callback      Hook callback.
	 * @param int      $priority      Hook priority.
	 * @param int      $accepted_args Accepted argument count.
	 *
	 * @return true
	 */
	function add_action( string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1 ): bool {
		$GLOBALS['woocommerce_business_id_test_actions'][ $hook_name ][ $priority ][] = array(
			'callback'      => $callback,
			'accepted_args' => $accepted_args,
		);

		return true;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	/**
	 * Minimal add_filter shim for isolated unit tests.
	 *
	 * @param string   $hook_name     Hook name.
	 * @param callable $callback      Hook callback.
	 * @param int      $priority      Hook priority.
	 * @param int      $accepted_args Accepted argument count.
	 *
	 * @return true
	 */
	function add_filter( string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1 ): bool {
		return add_action( $hook_name, $callback, $priority, $accepted_args );
	}
}

if ( ! function_exists( 'apply_filters' ) ) {
	/**
	 * Minimal apply_filters shim for isolated unit tests.
	 *
	 * @param string $hook_name Hook name.
	 * @param mixed  $value     Filter value.
	 * @param mixed  ...$args   Additional filter arguments.
	 *
	 * @return mixed
	 */
	function apply_filters( string $hook_name, mixed $value, mixed ...$args ): mixed {
		if ( empty( $GLOBALS['woocommerce_business_id_test_actions'][ $hook_name ] ) ) {
			return $value;
		}

		$callbacks_by_priority = $GLOBALS['woocommerce_business_id_test_actions'][ $hook_name ];
		ksort( $callbacks_by_priority );

		foreach ( $callbacks_by_priority as $callbacks ) {
			foreach ( $callbacks as $callback ) {
				$filter_args = array_merge( array( $value ), $args );
				$value       = call_user_func_array(
					$callback['callback'],
					array_slice( $filter_args, 0, (int) $callback['accepted_args'] )
				);
			}
		}

		return $value;
	}
}

if ( ! function_exists( 'do_action' ) ) {
	/**
	 * Minimal do_action shim for isolated unit tests.
	 *
	 * @param string $hook_name Hook name.
	 * @param mixed  ...$args   Hook arguments.
	 *
	 * @return void
	 */
	function do_action( string $hook_name, mixed ...$args ): void {
		if ( empty( $GLOBALS['woocommerce_business_id_test_actions'][ $hook_name ] ) ) {
			return;
		}

		$callbacks_by_priority = $GLOBALS['woocommerce_business_id_test_actions'][ $hook_name ];
		ksort( $callbacks_by_priority );

		foreach ( $callbacks_by_priority as $callbacks ) {
			foreach ( $callbacks as $callback ) {
				call_user_func_array(
					$callback['callback'],
					array_slice( $args, 0, (int) $callback['accepted_args'] )
				);
			}
		}
	}
}
