<?php
/**
 * Public template helper functions.
 *
 * @package WooCommerceBusinessId
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'woocommerce_business_id_get_output' ) ) {
	/**
	 * Get the formatted business ID output.
	 *
	 * Returns an empty string when no Business ID is configured. The output uses
	 * the same formatting and escaping rules as the plugin's WooCommerce email output.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $args Output arguments. Accepts 'format' as 'html' or 'plain_text'.
	 *
	 * @return string
	 */
	function woocommerce_business_id_get_output( array $args = array() ): string {
		return WooCommerceBusinessId\Template\Template_Tags::get_output( $args );
	}
}

if ( ! function_exists( 'woocommerce_business_id_output' ) ) {
	/**
	 * Echo the formatted business ID output.
	 *
	 * This callback can be attached to any WordPress or WooCommerce template action.
	 * It is also registered to the plugin-owned `woocommerce_business_id_template_output`
	 * action for templates that prefer calling `do_action()`.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed ...$hook_args Arguments passed by the template action.
	 *
	 * @return void
	 */
	function woocommerce_business_id_output( mixed ...$hook_args ): void {
		WooCommerceBusinessId\Template\Template_Tags::output( ...$hook_args );
	}
}
