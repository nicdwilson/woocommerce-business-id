<?php
/**
 * Tests for Business ID email rendering.
 *
 * @package WooCommerceBusinessId\Tests\Unit
 */

namespace WooCommerceBusinessId\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WooCommerceBusinessId\Email\Business_ID_Renderer;

/**
 * Tests Business ID email output.
 *
 * @covers \WooCommerceBusinessId\Email\Business_ID_Renderer
 */
class Business_ID_Renderer_Test extends TestCase {

	/**
	 * Reset test options before each test.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$GLOBALS['woocommerce_business_id_test_options'] = array();
	}

	/**
	 * Verify HTML email output includes escaped configured values.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_render_html_output(): void {
		$this->set_business_id_options( 'ABN', '51 824 753 556' );

		$this->assertSame(
			'<p class="woocommerce-business-id">ABN: 51 824 753 556</p>',
			( new Business_ID_Renderer() )->render()
		);
	}

	/**
	 * Verify plain-text email output includes no markup.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_render_plain_text_output(): void {
		$this->set_business_id_options( 'ABN', '51 824 753 556' );

		$this->assertSame(
			'ABN: 51 824 753 556',
			( new Business_ID_Renderer() )->render( Business_ID_Renderer::FORMAT_PLAIN_TEXT )
		);
	}

	/**
	 * Verify no output is rendered when the Business ID is blank.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_render_returns_empty_string_when_business_id_is_blank(): void {
		$this->set_business_id_options( 'ABN', '' );

		$this->assertSame( '', ( new Business_ID_Renderer() )->render() );
	}

	/**
	 * Verify blank descriptions fall back to the default label.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_render_uses_default_description_when_description_is_blank(): void {
		$this->set_business_id_options( '', '51 824 753 556' );

		$this->assertSame(
			'Business ID: 51 824 753 556',
			( new Business_ID_Renderer() )->render( Business_ID_Renderer::FORMAT_PLAIN_TEXT )
		);
	}

	/**
	 * Verify unsafe stored values are not rendered as executable markup.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_render_sanitizes_and_escapes_stored_values(): void {
		$this->set_business_id_options( 'ABN <script>alert(1)</script>', '51 <script>alert(1)</script>' );

		$output = ( new Business_ID_Renderer() )->render();

		$this->assertStringNotContainsString( '<script>', $output );
		$this->assertSame(
			'<p class="woocommerce-business-id">ABN alert(1): 51 alert1</p>',
			$output
		);
	}

	/**
	 * Verify HTML footer text receives HTML Business ID output.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_append_to_footer_text_uses_html_when_email_type_is_html(): void {
		$this->set_business_id_options( 'ABN', '51 824 753 556' );
		$email = new class() {
			/**
			 * Get the test email type.
			 *
			 * @return string
			 */
			public function get_email_type(): string {
				return 'html';
			}
		};

		$this->assertSame(
			'Footer text' . "\n\n" . '<p class="woocommerce-business-id">ABN: 51 824 753 556</p>',
			( new Business_ID_Renderer() )->append_to_footer_text( 'Footer text', $email )
		);
	}

	/**
	 * Verify plain footer text receives plain-text Business ID output.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_append_to_footer_text_uses_plain_text_when_email_is_not_provided(): void {
		$this->set_business_id_options( 'ABN', '51 824 753 556' );

		$this->assertSame(
			'Footer text' . "\n\n" . 'ABN: 51 824 753 556',
			( new Business_ID_Renderer() )->append_to_footer_text( 'Footer text' )
		);
	}

	/**
	 * Verify existing footer text is unchanged when the Business ID is blank.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function test_append_to_footer_text_preserves_footer_text_when_business_id_is_blank(): void {
		$this->set_business_id_options( 'ABN', '' );

		$this->assertSame(
			'Footer text',
			( new Business_ID_Renderer() )->append_to_footer_text( 'Footer text' )
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
