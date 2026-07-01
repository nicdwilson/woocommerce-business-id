<?php
/**
 * Tests for WooCommerce General settings integration.
 *
 * @package WooCommerceBusinessId\Tests\Unit
 */

namespace WooCommerceBusinessId\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WooCommerceBusinessId\Admin\General_Settings;

/**
 * Tests Business ID WooCommerce settings registration.
 *
 * @covers \WooCommerceBusinessId\Admin\General_Settings
 */
class General_Settings_Test extends TestCase {

	/**
	 * Verify fields are inserted after the store postcode setting when present.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_add_settings_inserts_fields_after_store_postcode(): void {
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

		$result = ( new General_Settings() )->add_settings( $settings );

		$this->assertSame( General_Settings::OPTION_DESCRIPTION, $result[2]['id'] );
		$this->assertSame( General_Settings::OPTION_BUSINESS_ID, $result[3]['id'] );
		$this->assertSame( 'Business ID description', $result[2]['title'] );
		$this->assertSame( General_Settings::DEFAULT_DESCRIPTION, $result[2]['default'] );
		$this->assertSame( 'Business ID', $result[3]['title'] );
		$this->assertSame( '', $result[3]['default'] );
		$this->assertTrue( $result[2]['desc_tip'] );
		$this->assertTrue( $result[3]['desc_tip'] );
	}

	/**
	 * Verify fields fall back to insertion before the first section end.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_add_settings_falls_back_to_first_section_end(): void {
		$settings = array(
			array(
				'id'   => 'woocommerce_store_address',
				'type' => 'text',
			),
			array(
				'id'   => 'store_address',
				'type' => 'sectionend',
			),
		);

		$result = ( new General_Settings() )->add_settings( $settings );

		$this->assertSame( General_Settings::OPTION_DESCRIPTION, $result[1]['id'] );
		$this->assertSame( General_Settings::OPTION_BUSINESS_ID, $result[2]['id'] );
		$this->assertSame( 'sectionend', $result[3]['type'] );
	}

	/**
	 * Verify sanitizer callbacks use the raw value when WooCommerce provides it.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_sanitize_callbacks_use_raw_value_when_present(): void {
		$settings = new General_Settings();

		$this->assertSame( 'ABN', $settings->sanitize_description( 'Business ID', array(), 'ABN:' ) );
		$this->assertSame(
			'51 alert1',
			$settings->sanitize_business_id( '51', array(), '51 <script>alert(1)</script>' )
		);
	}
}
