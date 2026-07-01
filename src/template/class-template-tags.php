<?php
/**
 * Template helper implementation.
 *
 * @package WooCommerceBusinessId
 * @since   0.1.0
 */

namespace WooCommerceBusinessId\Template;

use WooCommerceBusinessId\Email\Business_ID_Renderer;

defined( 'ABSPATH' ) || exit;

/**
 * Provides public template output helpers.
 *
 * @since 0.1.0
 */
final class Template_Tags {

	/**
	 * Get formatted business ID output.
	 *
	 * @since 0.1.0
	 *
	 * @param array<string, mixed> $args Output arguments. Accepts 'format' as 'html' or 'plain_text'.
	 *
	 * @return string
	 */
	public static function get_output( array $args = array() ): string {
		$format = isset( $args['format'] ) && \is_string( $args['format'] )
			? $args['format']
			: Business_ID_Renderer::FORMAT_HTML;

		return ( new Business_ID_Renderer() )->render( $format );
	}

	/**
	 * Echo formatted business ID output.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed ...$hook_args Arguments passed by the template action.
	 *
	 * @return void
	 */
	public static function output( mixed ...$hook_args ): void {
		unset( $hook_args );

		echo \wp_kses_post( self::get_output() );
	}
}
