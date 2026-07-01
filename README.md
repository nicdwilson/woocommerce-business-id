# woocommerce-business-id

Adds configurable business ID fields to WooCommerce settings, displays them in emails, and provides a template helper for custom placements.

## Requirements

- WordPress 6.4 or later.
- WooCommerce 8.5 or later.
- PHP 8.0 or later.

## Installation

1. Upload the plugin directory to `wp-content/plugins/woocommerce-business-id`.
2. Activate `WooCommerce Business ID` from the WordPress Plugins screen.
3. Go to `WooCommerce > Settings > General`.
4. Set the `Business ID description` and `Business ID` fields.

## Settings

The plugin adds two fields to `WooCommerce > Settings > General`:

- `Business ID description`: the label shown before the identifier, such as `ABN`, `VAT ID`, or `GST number`.
- `Business ID`: the identifier value shown after the description, such as `51 824 753 556`.

When the Business ID is present, outgoing WooCommerce emails include it in this format:

```text
ABN: 51 824 753 556
```

## Template helper

The plugin provides a public helper callback so themes and custom templates can place the configured business ID wherever a WordPress or WooCommerce action runs.

```php
add_action( 'woocommerce_after_shop_loop', 'woocommerce_business_id_output' );
```

Templates can also print the output directly:

```php
woocommerce_business_id_output();
```

Or use the plugin-provided action:

```php
do_action( 'woocommerce_business_id_template_output' );
```

Get the formatted output without echoing it:

```php
$business_id = woocommerce_business_id_get_output();
```

Get plain-text output for advanced use:

```php
$business_id = woocommerce_business_id_get_output( array( 'format' => 'plain_text' ) );
```

## Development

Run the automated quality gates before packaging:

```bash
composer quality
```

The individual commands are:

```bash
vendor/bin/phpcs
php -d memory_limit=1G vendor/bin/phpstan analyse --memory-limit=1G --debug
vendor/bin/phpunit
```

Manual QA steps live in `documentation/manual-qa.md`.

## Release packaging

Build a production zip with:

```bash
composer build:zip
```

Or run the underlying script directly:

```bash
bash scripts/build-release-zip.sh
```

The zip is written to `dist/woocommerce-business-id-0.1.0.zip`. The package includes only the runtime plugin files: the main plugin file, uninstall handler, `src/`, `readme.txt`, `README.md`, and `LICENSE`.
