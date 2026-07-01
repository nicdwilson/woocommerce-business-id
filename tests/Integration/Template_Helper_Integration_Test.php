<?php
/**
 * Integration tests for public template helper hooks.
 *
 * @package WooCommerceBusinessId\Tests\Integration
 */

namespace WooCommerceBusinessId\Tests\Integration;

use PHPUnit\Framework\TestCase;
use WooCommerceBusinessId\Plugin;

/**
 * Tests public template helpers through WordPress-style actions.
 *
 * @covers \WooCommerceBusinessId\Plugin
 * @covers \WooCommerceBusinessId\Template\Template_Tags
 * @covers \woocommerce_business_id_get_output
 * @covers \woocommerce_business_id_output
 */
class Template_Helper_Integration_Test extends TestCase {

	/**
	 * Reset test hooks, options, and plugin singleton before each test.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['woocommerce_business_id_test_actions']  = array();
		$GLOBALS['woocommerce_business_id_test_options']  = array();
		$GLOBALS['woocommerce_business_id_test_is_admin'] = false;

		$this->reset_plugin_singleton();
	}

	/**
	 * Verify the plugin-owned template action prints the configured Business ID.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_plugin_owned_template_action_prints_business_id(): void {
		$this->set_business_id_options( 'GST number', '12-345-678' );

		Plugin::instance();

		ob_start();
		\do_action( 'woocommerce_business_id_template_output' );
		$output = ob_get_clean();

		$this->assertSame( '<p class="woocommerce-business-id">GST number: 12-345-678</p>', $output );
	}

	/**
	 * Verify the output callback can be attached to any template action.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_output_callback_can_be_attached_to_any_template_action(): void {
		$this->set_business_id_options( 'ABN', '51 824 753 556' );

		\add_action( 'example_template_action', 'woocommerce_business_id_output' );

		ob_start();
		\do_action( 'example_template_action', 'unused-template-argument' );
		$output = ob_get_clean();

		$this->assertSame( '<p class="woocommerce-business-id">ABN: 51 824 753 556</p>', $output );
	}

	/**
	 * Verify the helper can return plain text without echoing.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_get_output_returns_plain_text_without_echoing(): void {
		$this->set_business_id_options( 'VAT ID', 'GB123456789' );

		ob_start();
		$output = \woocommerce_business_id_get_output( array( 'format' => 'plain_text' ) );
		$echoed = ob_get_clean();

		$this->assertSame( '', $echoed );
		$this->assertSame( 'VAT ID: GB123456789', $output );
	}

	/**
	 * Verify helper actions print nothing when the Business ID is blank.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_template_action_prints_nothing_when_business_id_is_blank(): void {
		$this->set_business_id_options( 'ABN', '' );

		\add_action( 'example_template_action', 'woocommerce_business_id_output' );

		ob_start();
		\do_action( 'example_template_action' );
		$output = ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Set Business ID options in the test option shim.
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

	/**
	 * Reset the plugin singleton so each test can inspect hook registration.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	private function reset_plugin_singleton(): void {
		$reflection = new \ReflectionClass( Plugin::class );
		$property   = $reflection->getProperty( 'instance' );

		$property->setAccessible( true );
		$property->setValue( null, null );
	}
}
