<?php
/**
 * Tests for public template helper functions.
 *
 * @package WooCommerceBusinessId\Tests\Unit
 */

namespace WooCommerceBusinessId\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests global Business ID template helpers.
 *
 * @covers \woocommerce_business_id_get_output
 * @covers \woocommerce_business_id_output
 * @covers \WooCommerceBusinessId\Template\Template_Tags
 */
class Template_Helper_Test extends TestCase {

	/**
	 * Reset test options and actions before each test.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['woocommerce_business_id_test_options'] = array();
		$GLOBALS['woocommerce_business_id_test_actions'] = array();
	}

	/**
	 * Verify the helper returns HTML by default.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_get_output_returns_html_by_default(): void {
		$this->set_business_id_options( 'ABN', '51 824 753 556' );

		$this->assertSame(
			'<p class="woocommerce-business-id">ABN: 51 824 753 556</p>',
			\woocommerce_business_id_get_output()
		);
	}

	/**
	 * Verify the helper can return plain-text output.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_get_output_returns_plain_text_when_requested(): void {
		$this->set_business_id_options( 'ABN', '51 824 753 556' );

		$this->assertSame(
			'ABN: 51 824 753 556',
			\woocommerce_business_id_get_output( array( 'format' => 'plain_text' ) )
		);
	}

	/**
	 * Verify the helper returns no output when the Business ID is blank.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_get_output_returns_empty_string_when_business_id_is_blank(): void {
		$this->set_business_id_options( 'ABN', '' );

		$this->assertSame( '', \woocommerce_business_id_get_output() );
	}

	/**
	 * Verify the echo helper prints HTML output.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_output_echoes_html(): void {
		$this->set_business_id_options( 'ABN', '51 824 753 556' );

		ob_start();
		\woocommerce_business_id_output();
		$output = ob_get_clean();

		$this->assertSame( '<p class="woocommerce-business-id">ABN: 51 824 753 556</p>', $output );
	}

	/**
	 * Verify the documented plugin-owned action prints the helper output.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_template_output_action_prints_html(): void {
		$this->set_business_id_options( 'ABN', '51 824 753 556' );
		\add_action( 'woocommerce_business_id_template_output', 'woocommerce_business_id_output' );

		ob_start();
		\do_action( 'woocommerce_business_id_template_output' );
		$output = ob_get_clean();

		$this->assertSame( '<p class="woocommerce-business-id">ABN: 51 824 753 556</p>', $output );
	}

	/**
	 * Verify unsafe stored values are not rendered as markup.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_get_output_never_renders_unsafe_markup(): void {
		$this->set_business_id_options( 'ABN <script>alert(1)</script>', '51 <script>alert(1)</script>' );

		$output = \woocommerce_business_id_get_output();

		$this->assertStringNotContainsString( '<script>', $output );
		$this->assertSame(
			'<p class="woocommerce-business-id">ABN alert(1): 51 alert1</p>',
			$output
		);
	}

	/**
	 * Set Business ID options in the unit test option shim.
	 *
	 * @since 0.1.0
	 *
	 * @param string $description Business ID description.
	 * @param string $business_id Business ID.
	 *
	 * @return void
	 */
	private function set_business_id_options( string $description, string $business_id ): void {
		$GLOBALS['woocommerce_business_id_test_options'] = array(
			'woocommerce_business_id_description' => $description,
			'woocommerce_business_id_number'      => $business_id,
		);
	}
}
