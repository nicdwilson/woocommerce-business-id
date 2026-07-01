<?php
/**
 * Integration tests for WooCommerce General settings hooks.
 *
 * @package WooCommerceBusinessId\Tests\Integration
 */

namespace WooCommerceBusinessId\Tests\Integration;

use PHPUnit\Framework\TestCase;
use WooCommerceBusinessId\Admin\General_Settings;

/**
 * Tests Business ID settings through WooCommerce-style filters.
 *
 * @covers \WooCommerceBusinessId\Admin\General_Settings
 */
class General_Settings_Integration_Test extends TestCase {

	/**
	 * Register settings hooks before each test.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['woocommerce_business_id_test_actions'] = array();
		$GLOBALS['woocommerce_business_id_test_options'] = array();

		( new General_Settings() )->register_hooks();
	}

	/**
	 * Verify WooCommerce's General settings filter receives both Business ID fields.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_general_settings_filter_registers_business_id_fields(): void {
		$settings = array(
			array(
				'id'   => 'woocommerce_store_address',
				'type' => 'text',
			),
			array(
				'id'   => 'woocommerce_store_postcode',
				'type' => 'text',
			),
			array(
				'id'   => 'store_address',
				'type' => 'sectionend',
			),
		);

		$result = \apply_filters( 'woocommerce_get_settings_general', $settings );

		$this->assertSame( General_Settings::OPTION_DESCRIPTION, $result[2]['id'] );
		$this->assertSame( General_Settings::OPTION_BUSINESS_ID, $result[3]['id'] );
		$this->assertSame( 'Business ID description', $result[2]['title'] );
		$this->assertStringContainsString( 'ABN', $result[2]['desc'] );
		$this->assertStringContainsString( 'VAT ID', $result[2]['desc'] );
		$this->assertStringContainsString( 'GST number', $result[2]['desc'] );
		$this->assertSame( '50', $result[2]['custom_attributes']['maxlength'] );
		$this->assertSame( '100', $result[3]['custom_attributes']['maxlength'] );
	}

	/**
	 * Verify description values are sanitized and saved through the WooCommerce settings filter.
	 *
	 * @since 0.1.0
	 *
	 * @dataProvider description_save_provider
	 *
	 * @param mixed  $raw      Raw submitted value.
	 * @param string $expected Expected stored value.
	 *
	 * @return void
	 */
	public function test_description_values_save_through_sanitize_filter( mixed $raw, string $expected ): void {
		$sanitized = \apply_filters(
			'woocommerce_admin_settings_sanitize_option_' . General_Settings::OPTION_DESCRIPTION,
			$raw,
			array( 'id' => General_Settings::OPTION_DESCRIPTION ),
			$raw
		);

		\update_option( General_Settings::OPTION_DESCRIPTION, $sanitized );

		$this->assertSame( $expected, \get_option( General_Settings::OPTION_DESCRIPTION ) );
	}

	/**
	 * Verify business ID values are sanitized and saved through the WooCommerce settings filter.
	 *
	 * @since 0.1.0
	 *
	 * @dataProvider business_id_save_provider
	 *
	 * @param mixed  $raw      Raw submitted value.
	 * @param string $expected Expected stored value.
	 *
	 * @return void
	 */
	public function test_business_id_values_save_through_sanitize_filter( mixed $raw, string $expected ): void {
		$sanitized = \apply_filters(
			'woocommerce_admin_settings_sanitize_option_' . General_Settings::OPTION_BUSINESS_ID,
			$raw,
			array( 'id' => General_Settings::OPTION_BUSINESS_ID ),
			$raw
		);

		\update_option( General_Settings::OPTION_BUSINESS_ID, $sanitized );

		$this->assertSame( $expected, \get_option( General_Settings::OPTION_BUSINESS_ID ) );
	}

	/**
	 * Data provider for description save cases.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, array{0:mixed, 1:string}>
	 */
	public function description_save_provider(): array {
		return array(
			'ABN'           => array( 'ABN', 'ABN' ),
			'VAT ID'        => array( 'VAT ID', 'VAT ID' ),
			'GST number'    => array( 'GST number', 'GST number' ),
			'trailing colon'=> array( 'ABN:', 'ABN' ),
			'HTML input'    => array( 'ABN <script>alert(1)</script>', 'ABN alert(1)' ),
			'blank value'   => array( '   ', '' ),
		);
	}

	/**
	 * Data provider for business ID save cases.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, array{0:mixed, 1:string}>
	 */
	public function business_id_save_provider(): array {
		return array(
			'ABN-style spacing'       => array( '51 824 753 556', '51 824 753 556' ),
			'VAT-style alphanumeric' => array( 'GB123456789', 'GB123456789' ),
			'hyphenated ID'          => array( '12-345-678', '12-345-678' ),
			'common punctuation'     => array( 'ID 123/456.78_A', 'ID 123/456.78_A' ),
			'HTML input'             => array( '51 <script>alert(1)</script>', '51 alert1' ),
			'blank value'            => array( '   ', '' ),
		);
	}
}
