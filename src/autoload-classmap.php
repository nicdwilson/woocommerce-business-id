<?php
/**
 * Class map for WooCommerce Business ID.
 *
 * @package WooCommerceBusinessId
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

return array(
	'WooCommerceBusinessId\\Admin\\General_Settings'     => 'src/admin/class-general-settings.php',
	'WooCommerceBusinessId\\Email\\Business_ID_Renderer' => 'src/email/class-business-id-renderer.php',
	'WooCommerceBusinessId\\Plugin'                      => 'src/class-plugin.php',
	'WooCommerceBusinessId\\Template\\Template_Tags'     => 'src/template/class-template-tags.php',
	'WooCommerceBusinessId\\Utilities\\Business_ID_Sanitizer' =>
		'src/utilities/class-business-id-sanitizer.php',
);
