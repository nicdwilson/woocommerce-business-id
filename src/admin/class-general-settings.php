<?php
/**
 * WooCommerce General settings integration.
 *
 * @package WooCommerceBusinessId
 * @since   0.1.0
 */

namespace WooCommerceBusinessId\Admin;

use WooCommerceBusinessId\Utilities\Business_ID_Sanitizer;

defined( 'ABSPATH' ) || exit;

/**
 * Adds Business ID fields to WooCommerce General settings.
 *
 * @since 0.1.0
 */
final class General_Settings {

	/**
	 * Business ID option name.
	 *
	 * @var string
	 */
	public const OPTION_BUSINESS_ID = 'woocommerce_business_id_number';

	/**
	 * Business ID description option name.
	 *
	 * @var string
	 */
	public const OPTION_DESCRIPTION = 'woocommerce_business_id_description';

	/**
	 * Default business ID description.
	 *
	 * @var string
	 */
	public const DEFAULT_DESCRIPTION = 'Business ID';

	/**
	 * Register WordPress hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		\add_filter( 'woocommerce_get_settings_general', array( $this, 'add_settings' ), 20 );
		\add_filter(
			'woocommerce_admin_settings_sanitize_option_' . self::OPTION_DESCRIPTION,
			array( $this, 'sanitize_description' ),
			10,
			3
		);
		\add_filter(
			'woocommerce_admin_settings_sanitize_option_' . self::OPTION_BUSINESS_ID,
			array( $this, 'sanitize_business_id' ),
			10,
			3
		);
	}

	/**
	 * Add the Business ID fields to WooCommerce General settings.
	 *
	 * @since 0.1.0
	 *
	 * @param array<int, array<string, mixed>> $settings WooCommerce settings.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function add_settings( array $settings ): array {
		$business_id_settings = $this->get_business_id_settings();

		foreach ( $settings as $index => $setting ) {
			if ( 'woocommerce_store_postcode' === ( $setting['id'] ?? '' ) ) {
				\array_splice( $settings, $index + 1, 0, $business_id_settings );

				return $settings;
			}
		}

		foreach ( $settings as $index => $setting ) {
			if ( 'sectionend' === ( $setting['type'] ?? '' ) ) {
				\array_splice( $settings, $index, 0, $business_id_settings );

				return $settings;
			}
		}

		return \array_merge( $settings, $business_id_settings );
	}

	/**
	 * Sanitize the business ID description before storage.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed                $value     Sanitized setting value from WooCommerce.
	 * @param array<string, mixed> $option    Option schema.
	 * @param mixed                $raw_value Raw setting value from WooCommerce.
	 *
	 * @return string
	 */
	public function sanitize_description( mixed $value, array $option = array(), mixed $raw_value = null ): string {
		unset( $option );

		return Business_ID_Sanitizer::sanitize_description( null === $raw_value ? $value : $raw_value );
	}

	/**
	 * Sanitize the business ID before storage.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed                $value     Sanitized setting value from WooCommerce.
	 * @param array<string, mixed> $option    Option schema.
	 * @param mixed                $raw_value Raw setting value from WooCommerce.
	 *
	 * @return string
	 */
	public function sanitize_business_id( mixed $value, array $option = array(), mixed $raw_value = null ): string {
		unset( $option );

		return Business_ID_Sanitizer::sanitize_business_id( null === $raw_value ? $value : $raw_value );
	}

	/**
	 * Get the Business ID settings fields.
	 *
	 * @since 0.1.0
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function get_business_id_settings(): array {
		return array(
			array(
				'title'             => \__( 'Business ID description', 'woocommerce-business-id' ),
				'desc'              => \__(
					'Shown before the business ID in emails. Examples: ABN, VAT ID, GST number.',
					'woocommerce-business-id'
				),
				'id'                => self::OPTION_DESCRIPTION,
				'type'              => 'text',
				'default'           => self::DEFAULT_DESCRIPTION,
				'desc_tip'          => true,
				'custom_attributes' => array(
					'maxlength' => '50',
				),
			),
			array(
				'title'             => \__( 'Business ID', 'woocommerce-business-id' ),
				'desc'              => \__(
					'Shown after the description in emails. Example: 51 824 753 556.',
					'woocommerce-business-id'
				),
				'id'                => self::OPTION_BUSINESS_ID,
				'type'              => 'text',
				'default'           => '',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'maxlength' => '100',
				),
			),
		);
	}
}
