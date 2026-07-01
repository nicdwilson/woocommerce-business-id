<?php
/**
 * Plugin Name:       WooCommerce Business ID
 * Plugin URI:        https://github.com/nicw/woocommerce-business-id
 * Description:       Adds configurable business ID fields to WooCommerce settings, displays them in emails, and provides a template helper for custom placements.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Requires Plugins:  woocommerce
 * Author:            WooCommerce Growth Team / @nicw
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       woocommerce-business-id
 * Domain Path:       /languages
 * WC requires at least: 8.5
 *
 * @package WooCommerceBusinessId
 */

defined( 'ABSPATH' ) || exit;

define( 'WOOCOMMERCE_BUSINESS_ID_VERSION', '0.1.0' );
define( 'WOOCOMMERCE_BUSINESS_ID_FILE', __FILE__ );
define( 'WOOCOMMERCE_BUSINESS_ID_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOOCOMMERCE_BUSINESS_ID_URL', plugin_dir_url( __FILE__ ) );
define( 'WOOCOMMERCE_BUSINESS_ID_BASENAME', plugin_basename( __FILE__ ) );
define( 'WOOCOMMERCE_BUSINESS_ID_MINIMUM_WC_VERSION', '8.5' );

require_once WOOCOMMERCE_BUSINESS_ID_PATH . 'src/class-autoloader.php';

$woocommerce_business_id_classmap   = require WOOCOMMERCE_BUSINESS_ID_PATH . 'src/autoload-classmap.php';
$woocommerce_business_id_autoloader = new WooCommerceBusinessId\Autoloader(
	WOOCOMMERCE_BUSINESS_ID_PATH,
	$woocommerce_business_id_classmap
);
$woocommerce_business_id_autoloader->register();

require_once WOOCOMMERCE_BUSINESS_ID_PATH . 'src/functions.php';

/**
 * Declare WooCommerce feature compatibility.
 *
 * @since 0.1.0
 */
add_action(
	'before_woocommerce_init',
	static function (): void {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
				'custom_order_tables',
				__FILE__,
				true
			);
		}
	}
);

/**
 * Initialize the plugin after WooCommerce and other plugins are loaded.
 *
 * @since 0.1.0
 */
add_action(
	'plugins_loaded',
	static function (): void {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action(
				'admin_notices',
				static function (): void {
					echo '<div class="notice notice-error"><p>';
					echo esc_html__(
						'WooCommerce Business ID requires WooCommerce to be installed and active.',
						'woocommerce-business-id'
					);
					echo '</p></div>';
				}
			);
			return;
		}

		$woocommerce_business_id_wc_version = defined( 'WC_VERSION' ) ? (string) constant( 'WC_VERSION' ) : '0';

		if (
			version_compare(
				$woocommerce_business_id_wc_version,
				WOOCOMMERCE_BUSINESS_ID_MINIMUM_WC_VERSION,
				'<'
			)
		) {
			add_action(
				'admin_notices',
				static function (): void {
					echo '<div class="notice notice-error"><p>';
					printf(
						/* translators: %s: Minimum WooCommerce version. */
						esc_html__(
							'WooCommerce Business ID requires WooCommerce %s or higher.',
							'woocommerce-business-id'
						),
						esc_html( WOOCOMMERCE_BUSINESS_ID_MINIMUM_WC_VERSION )
					);
					echo '</p></div>';
				}
			);
			return;
		}

		WooCommerceBusinessId\Plugin::instance();
	}
);

register_activation_hook( __FILE__, array( WooCommerceBusinessId\Plugin::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( WooCommerceBusinessId\Plugin::class, 'deactivate' ) );
