<?php
/**
 * Integration tests for WooCommerce email rendering hooks.
 *
 * @package WooCommerceBusinessId\Tests\Integration
 */

namespace WooCommerceBusinessId\Tests\Integration;

use PHPUnit\Framework\TestCase;
use WooCommerceBusinessId\Email\Business_ID_Renderer;

/**
 * Tests Business ID rendering through WooCommerce email filters.
 *
 * @covers \WooCommerceBusinessId\Email\Business_ID_Renderer
 */
class Email_Renderer_Integration_Test extends TestCase {

	/**
	 * Register email hooks before each test.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['woocommerce_business_id_test_actions'] = array();
		$GLOBALS['woocommerce_business_id_test_options'] = array();

		( new Business_ID_Renderer() )->register_hooks();
	}

	/**
	 * Verify common WooCommerce HTML email types receive the configured Business ID.
	 *
	 * @since 0.1.0
	 *
	 * @dataProvider html_email_provider
	 *
	 * @param string $email_id WooCommerce email ID.
	 *
	 * @return void
	 */
	public function test_footer_filter_appends_business_id_to_common_html_emails( string $email_id ): void {
		$this->set_business_id_options( 'ABN', '51 824 753 556' );

		$result = \apply_filters(
			'woocommerce_email_footer_text',
			'Footer text',
			$this->create_email( 'html', $email_id )
		);

		$this->assertSame(
			'Footer text' . "\n\n" . '<p class="woocommerce-business-id">ABN: 51 824 753 556</p>',
			$result
		);
	}

	/**
	 * Verify plain-text email footer output uses plain text.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_footer_filter_appends_plain_text_business_id_to_plain_emails(): void {
		$this->set_business_id_options( 'VAT ID', 'GB123456789' );

		$result = \apply_filters(
			'woocommerce_email_footer_text',
			'Footer text',
			$this->create_email( 'plain', 'customer_invoice' )
		);

		$this->assertSame( 'Footer text' . "\n\n" . 'VAT ID: GB123456789', $result );
	}

	/**
	 * Verify no email output changes when the Business ID is absent.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_footer_filter_preserves_footer_when_business_id_is_absent(): void {
		$this->set_business_id_options( 'ABN', '' );

		$result = \apply_filters(
			'woocommerce_email_footer_text',
			'Footer text',
			$this->create_email( 'html', 'new_order' )
		);

		$this->assertSame( 'Footer text', $result );
	}

	/**
	 * Verify blank descriptions use the default Business ID label.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_footer_filter_uses_default_description_when_description_is_blank(): void {
		$this->set_business_id_options( '', '51 824 753 556' );

		$result = \apply_filters(
			'woocommerce_email_footer_text',
			'Footer text',
			$this->create_email( 'html', 'customer_processing_order' )
		);

		$this->assertSame(
			'Footer text' . "\n\n" . '<p class="woocommerce-business-id">Business ID: 51 824 753 556</p>',
			$result
		);
	}

	/**
	 * Data provider for common WooCommerce email IDs.
	 *
	 * @since 0.1.0
	 *
	 * @return array<string, array{0:string}>
	 */
	public function html_email_provider(): array {
		return array(
			'customer processing order' => array( 'customer_processing_order' ),
			'customer invoice'          => array( 'customer_invoice' ),
			'admin new order'           => array( 'new_order' ),
		);
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
	 * Create a minimal WooCommerce email test double.
	 *
	 * @since 0.1.0
	 *
	 * @param string $email_type Email format.
	 * @param string $email_id   WooCommerce email ID.
	 *
	 * @return object
	 */
	private function create_email( string $email_type, string $email_id ): object {
		return new class( $email_type, $email_id ) {

			/**
			 * Email format.
			 *
			 * @var string
			 */
			private string $email_type;

			/**
			 * Email ID.
			 *
			 * @var string
			 */
			private string $email_id;

			/**
			 * Constructor.
			 *
			 * @param string $email_type Email format.
			 * @param string $email_id   Email ID.
			 */
			public function __construct( string $email_type, string $email_id ) {
				$this->email_type = $email_type;
				$this->email_id   = $email_id;
			}

			/**
			 * Get the email format.
			 *
			 * @return string
			 */
			public function get_email_type(): string {
				return $this->email_type;
			}

			/**
			 * Get the email ID.
			 *
			 * @return string
			 */
			public function get_id(): string {
				return $this->email_id;
			}
		};
	}
}
