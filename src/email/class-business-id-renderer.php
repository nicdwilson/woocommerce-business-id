<?php
/**
 * Business ID email rendering.
 *
 * @package WooCommerceBusinessId
 * @since   0.1.0
 */

namespace WooCommerceBusinessId\Email;

use WooCommerceBusinessId\Utilities\Business_ID_Sanitizer;

defined( 'ABSPATH' ) || exit;

/**
 * Renders the configured Business ID for WooCommerce emails.
 *
 * @since 0.1.0
 */
final class Business_ID_Renderer {

	/**
	 * HTML output format.
	 *
	 * @var string
	 */
	public const FORMAT_HTML = 'html';

	/**
	 * Plain-text output format.
	 *
	 * @var string
	 */
	public const FORMAT_PLAIN_TEXT = 'plain_text';

	/**
	 * Business ID option name.
	 *
	 * @var string
	 */
	private const OPTION_BUSINESS_ID = 'woocommerce_business_id_number';

	/**
	 * Business ID description option name.
	 *
	 * @var string
	 */
	private const OPTION_DESCRIPTION = 'woocommerce_business_id_description';

	/**
	 * Default business ID description.
	 *
	 * @var string
	 */
	private const DEFAULT_DESCRIPTION = 'Business ID';

	/**
	 * Register WooCommerce email hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		\add_filter( 'woocommerce_email_footer_text', array( $this, 'append_to_footer_text' ), 20, 2 );
	}

	/**
	 * Render the configured Business ID.
	 *
	 * @since 0.1.0
	 *
	 * @param string $format Output format. Accepts 'html' or 'plain_text'.
	 *
	 * @return string
	 */
	public function render( string $format = self::FORMAT_HTML ): string {
		$business_id = $this->get_business_id();

		if ( '' === $business_id ) {
			return '';
		}

		$output = sprintf(
			'%s: %s',
			$this->get_description(),
			$business_id
		);

		if ( self::FORMAT_PLAIN_TEXT === $format ) {
			return \wp_strip_all_tags( $output, true );
		}

		return sprintf(
			'<p class="woocommerce-business-id">%s</p>',
			\esc_html( $output )
		);
	}

	/**
	 * Append the Business ID line to WooCommerce email footer text.
	 *
	 * WooCommerce uses this filter in HTML and plain-text order email templates.
	 * Current plain-text templates call the filter without passing the email object,
	 * so a missing email object is treated as plain-text output.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $footer_text Existing footer text.
	 * @param mixed $email       WooCommerce email object when the template provides it.
	 *
	 * @return string
	 */
	public function append_to_footer_text( mixed $footer_text, mixed $email = null ): string {
		$footer_text = \is_scalar( $footer_text ) ? (string) $footer_text : '';
		$output      = $this->render( $this->get_format_for_email( $email ) );

		if ( '' === $output ) {
			return $footer_text;
		}

		if ( '' === \trim( $footer_text ) ) {
			return $output;
		}

		return $footer_text . "\n\n" . $output;
	}

	/**
	 * Get the configured business ID.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	private function get_business_id(): string {
		$business_id = \get_option( self::OPTION_BUSINESS_ID, '' );

		return Business_ID_Sanitizer::sanitize_business_id( $business_id );
	}

	/**
	 * Get the configured business ID description.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	private function get_description(): string {
		$description = Business_ID_Sanitizer::sanitize_description(
			\get_option( self::OPTION_DESCRIPTION, self::DEFAULT_DESCRIPTION )
		);

		if ( '' === $description ) {
			return self::DEFAULT_DESCRIPTION;
		}

		return $description;
	}

	/**
	 * Determine the correct output format for a WooCommerce email object.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $email WooCommerce email object when available.
	 *
	 * @return string
	 */
	private function get_format_for_email( mixed $email ): string {
		if ( \is_object( $email ) && \method_exists( $email, 'get_email_type' ) ) {
			return 'plain' === $email->get_email_type() ? self::FORMAT_PLAIN_TEXT : self::FORMAT_HTML;
		}

		return self::FORMAT_PLAIN_TEXT;
	}
}
