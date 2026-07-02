=== WooCommerce Business ID ===
Contributors: nicw
Tags: woocommerce, business id, tax, gst, vat, abn
Requires at least: 6.4
Tested up to: 6.6
Requires PHP: 8.0
Requires Plugins: woocommerce
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds configurable business ID fields to WooCommerce settings, displays them in emails, and provides a template helper for custom placements.

== Description ==

WooCommerce Business ID adds jurisdiction-neutral Business ID fields to WooCommerce settings.

Merchants can set a Business ID description, such as `ABN`, `VAT ID`, or `GST number`, and a Business ID value, such as `51 824 753 556`. When the Business ID is present, WooCommerce emails include the value in this format:

`ABN: 51 824 753 556`

By default the line is appended to the email footer. To control where it appears, add the `{business_id}` token to WooCommerce > Settings > Emails > Footer text. The token is replaced with the formatted Business ID line (HTML or plain text to match the email). When the token is present it is used in place and the line is not also appended; when the Business ID is blank the token is removed.

== Template helper ==

The plugin provides a public helper callback so themes and custom templates can place the configured Business ID wherever a WordPress or WooCommerce action runs.

Add the Business ID to any template hook:

`add_action( 'woocommerce_after_shop_loop', 'woocommerce_business_id_output' );`

Print the Business ID directly in a template:

`woocommerce_business_id_output();`

Print via the plugin-provided action:

`do_action( 'woocommerce_business_id_template_output' );`

Get the formatted output without echoing it:

`$business_id = woocommerce_business_id_get_output();`

Get plain-text output for advanced use:

`$business_id = woocommerce_business_id_get_output( array( 'format' => 'plain_text' ) );`

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/woocommerce-business-id`.
2. Activate the plugin through the WordPress Plugins screen.
3. Go to WooCommerce > Settings > General and configure the Business ID fields.

== Frequently Asked Questions ==

= Does this validate ABN, VAT ID, GST number, or other jurisdiction-specific IDs? =

No. Version 1 sanitizes the stored values for safe output but does not validate jurisdiction-specific identifier rules.

== Changelog ==

= 1.0.0 =

* Initial stable release.
* Add configurable Business ID and Business ID description fields to WooCommerce > Settings > General.
* Display the Business ID in outgoing HTML and plain-text WooCommerce emails.
* Add a `{business_id}` footer text token so merchants can control placement in the email footer.
* Provide a public template helper (`woocommerce_business_id_output()` and related) for custom placements.

= 0.1.0 =

* Initial development version.
