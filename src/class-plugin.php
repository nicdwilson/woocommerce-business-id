<?php
/**
 * Main plugin bootstrap.
 *
 * @package WooCommerceBusinessId
 * @since   0.1.0
 */

namespace WooCommerceBusinessId;

use WooCommerceBusinessId\Admin\General_Settings;
use WooCommerceBusinessId\Email\Business_ID_Renderer;

defined( 'ABSPATH' ) || exit;

/**
 * Initializes plugin hooks and lifecycle behavior.
 *
 * @since 0.1.0
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Get the plugin instance.
	 *
	 * @since 0.1.0
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Register plugin hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		\add_action( 'init', array( $this, 'load_textdomain' ) );

		/**
		 * Prints the configured Business ID for template integrations.
		 *
		 * Developers may call this action from any WordPress, WooCommerce, or theme template
		 * where the configured Business ID should appear.
		 *
		 * @since 0.1.0
		 */
		\add_action( 'woocommerce_business_id_template_output', 'woocommerce_business_id_output' );

		( new Business_ID_Renderer() )->register_hooks();

		if ( \is_admin() ) {
			( new General_Settings() )->register_hooks();
		}
	}

	/**
	 * Load plugin translations.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		\load_plugin_textdomain(
			'woocommerce-business-id',
			false,
			\dirname( WOOCOMMERCE_BUSINESS_ID_BASENAME ) . '/languages'
		);
	}

	/**
	 * Plugin activation callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function activate(): void {
		if ( \version_compare( PHP_VERSION, '8.0', '<' ) ) {
			\deactivate_plugins( WOOCOMMERCE_BUSINESS_ID_BASENAME );
			\wp_die(
				\esc_html__( 'WooCommerce Business ID requires PHP 8.0 or higher.', 'woocommerce-business-id' ),
				\esc_html__( 'Plugin activation error', 'woocommerce-business-id' ),
				array( 'back_link' => true )
			);
		}

		$woocommerce_class = 'WooCommerce';

		if ( ! \class_exists( $woocommerce_class ) ) {
			\deactivate_plugins( WOOCOMMERCE_BUSINESS_ID_BASENAME );
			\wp_die(
				\esc_html__(
					'WooCommerce Business ID requires WooCommerce to be installed and active.',
					'woocommerce-business-id'
				),
				\esc_html__( 'Plugin activation error', 'woocommerce-business-id' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Plugin deactivation callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		// No scheduled events or rewrite rules are registered in the foundation milestone.
	}

	/**
	 * Prevent cloning.
	 *
	 * @since 0.1.0
	 */
	private function __clone() {}

	/**
	 * Prevent unserialization.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 *
	 * @throws \RuntimeException When unserialization is attempted.
	 */
	public function __wakeup(): void {
		throw new \RuntimeException( 'Cannot unserialize singleton.' );
	}
}
