<?php
/**
 * Plugin uninstall handler.
 *
 * @package WooCommerceBusinessId
 * @since   0.1.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'woocommerce_business_id_number' );
delete_option( 'woocommerce_business_id_description' );
