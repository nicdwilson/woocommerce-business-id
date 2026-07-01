<?php
/**
 * Business ID sanitization helpers.
 *
 * @package WooCommerceBusinessId
 * @since   0.1.0
 */

namespace WooCommerceBusinessId\Utilities;

defined( 'ABSPATH' ) || exit;

/**
 * Sanitizes merchant-provided business ID settings.
 *
 * @since 0.1.0
 */
final class Business_ID_Sanitizer {

	/**
	 * Maximum stored description length.
	 *
	 * @var int
	 */
	private const MAX_DESCRIPTION_LENGTH = 50;

	/**
	 * Maximum stored business ID length.
	 *
	 * @var int
	 */
	private const MAX_BUSINESS_ID_LENGTH = 100;

	/**
	 * Sanitize the business ID description.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value Raw setting value.
	 *
	 * @return string
	 */
	public static function sanitize_description( mixed $value ): string {
		$value = self::normalize_value( $value );

		if ( '' === $value ) {
			return '';
		}

		$value = self::strip_control_characters( $value );
		$value = \sanitize_text_field( $value );
		$value = self::strip_control_characters( $value );
		$value = \rtrim( \trim( $value ), " \t\n\r\0\x0B:" );

		return \rtrim( self::limit_length( $value, self::MAX_DESCRIPTION_LENGTH ), " \t\n\r\0\x0B:" );
	}

	/**
	 * Sanitize the business ID value.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value Raw setting value.
	 *
	 * @return string
	 */
	public static function sanitize_business_id( mixed $value ): string {
		$value = self::normalize_value( $value );

		if ( '' === $value ) {
			return '';
		}

		$value    = self::strip_control_characters( $value );
		$value    = \sanitize_text_field( $value );
		$value    = self::strip_control_characters( $value );
		$filtered = \preg_replace( '/[^A-Za-z0-9 .\/_-]/', '', $value );

		if ( null === $filtered ) {
			return '';
		}

		$filtered = \preg_replace( '/ +/', ' ', $filtered );

		if ( null === $filtered ) {
			return '';
		}

		return self::limit_length( \trim( $filtered ), self::MAX_BUSINESS_ID_LENGTH );
	}

	/**
	 * Normalize a raw setting value to a string.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value Raw setting value.
	 *
	 * @return string
	 */
	private static function normalize_value( mixed $value ): string {
		if ( ! \is_scalar( $value ) ) {
			return '';
		}

		return \trim( (string) $value );
	}

	/**
	 * Strip ASCII control characters from a value.
	 *
	 * @since 0.1.0
	 *
	 * @param string $value Raw value.
	 *
	 * @return string
	 */
	private static function strip_control_characters( string $value ): string {
		$filtered = \preg_replace( '/[\x00-\x1F\x7F]/', '', $value );

		if ( null === $filtered ) {
			return '';
		}

		return $filtered;
	}

	/**
	 * Limit a string to a maximum character length.
	 *
	 * @since 0.1.0
	 *
	 * @param string $value      Value to limit.
	 * @param int    $max_length Maximum length.
	 *
	 * @return string
	 */
	private static function limit_length( string $value, int $max_length ): string {
		if ( \function_exists( 'mb_strlen' ) && \mb_strlen( $value ) <= $max_length ) {
			return $value;
		}

		if ( ! \function_exists( 'mb_strlen' ) && \strlen( $value ) <= $max_length ) {
			return $value;
		}

		if ( \function_exists( 'mb_substr' ) ) {
			return \mb_substr( $value, 0, $max_length );
		}

		return \substr( $value, 0, $max_length );
	}
}
