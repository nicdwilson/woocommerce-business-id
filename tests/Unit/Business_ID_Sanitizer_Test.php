<?php
/**
 * Tests for Business ID sanitization.
 *
 * @package WooCommerceBusinessId\Tests\Unit
 */

namespace WooCommerceBusinessId\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WooCommerceBusinessId\Utilities\Business_ID_Sanitizer;

/**
 * Tests Business ID setting sanitization.
 *
 * @covers \WooCommerceBusinessId\Utilities\Business_ID_Sanitizer
 */
class Business_ID_Sanitizer_Test extends TestCase {

	/**
	 * Verify description values are sanitized for storage.
	 *
	 * @since 0.1.0
	 *
	 * @dataProvider description_provider
	 *
	 * @param mixed  $value    Raw value.
	 * @param string $expected Expected sanitized value.
	 *
	 * @return void
	 */
	public function test_sanitize_description( mixed $value, string $expected ): void {
		$this->assertSame( $expected, Business_ID_Sanitizer::sanitize_description( $value ) );
	}

	/**
	 * Verify business ID values are sanitized for storage.
	 *
	 * @since 0.1.0
	 *
	 * @dataProvider business_id_provider
	 *
	 * @param mixed  $value    Raw value.
	 * @param string $expected Expected sanitized value.
	 *
	 * @return void
	 */
	public function test_sanitize_business_id( mixed $value, string $expected ): void {
		$this->assertSame( $expected, Business_ID_Sanitizer::sanitize_business_id( $value ) );
	}

	/**
	 * Description sanitizer data provider.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, array{0:mixed, 1:string}>
	 */
	public function description_provider(): array {
		return array(
			'plain ABN'       => array( 'ABN', 'ABN' ),
			'trailing colon'  => array( 'ABN:', 'ABN' ),
			'script markup'   => array( 'ABN <script>alert(1)</script>', 'ABN alert(1)' ),
			'empty string'    => array( '   ', '' ),
			'control chars'   => array( "A\x00B\nN", 'ABN' ),
			'non scalar'      => array( array( 'ABN' ), '' ),
			'maximum length'  => array( str_repeat( 'A', 60 ), str_repeat( 'A', 50 ) ),
			'colon after max' => array( str_repeat( 'A', 49 ) . ':Z', str_repeat( 'A', 49 ) ),
		);
	}

	/**
	 * Business ID sanitizer data provider.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, array{0:mixed, 1:string}>
	 */
	public function business_id_provider(): array {
		return array(
			'ABN spacing'         => array( '51 824 753 556', '51 824 753 556' ),
			'common punctuation'  => array( 'GB 123-456/789.01_A', 'GB 123-456/789.01_A' ),
			'script markup'       => array( '51 <script>alert(1)</script>', '51 alert1' ),
			'disallowed symbols'  => array( 'ID #123 + 45', 'ID 123 45' ),
			'empty string'        => array( '   ', '' ),
			'control chars'       => array( "51\x00753\n556", '51753556' ),
			'non scalar'          => array( array( '51 824 753 556' ), '' ),
			'maximum length'      => array( str_repeat( 'A', 120 ), str_repeat( 'A', 100 ) ),
		);
	}
}
