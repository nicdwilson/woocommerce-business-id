# WooCommerce Business ID Roadmap

## Project Goal

Build a small WooCommerce extension that lets a merchant store their business identification number, define the description used for that number, and automatically display both in outgoing WooCommerce emails.

The fields are jurisdiction-neutral. In Australia the description may be `ABN`; in other regions it may be `VAT ID`, `GST number`, `Tax registration number`, `Company registration number`, `EIN`, or another locally required label.

## Scope

### In scope

- Add two merchant-facing settings to `WooCommerce > Settings > General`.
- Store the business ID and business ID description as WordPress options owned by this plugin.
- Sanitize and escape both values safely.
- Display the configured description and business ID in every WooCommerce email when the business ID is present.
- Place the displayed line below the store/company address area where WooCommerce email templates make that possible.
- Support HTML and plain-text WooCommerce emails.
- Include tests for settings registration, sanitization, saved options behavior, and email rendering.

### Out of scope

- Generating PDF invoices.
- Creating a dedicated invoice numbering system.
- Validating jurisdiction-specific identifiers such as ABNs, VAT IDs, or GST numbers.
- Adding checkout, cart, product, order, or customer fields.
- Storing order-specific business identifier snapshots in version 1.
- Guaranteeing legal tax invoice compliance across all jurisdictions.

## Product Decisions

- Plugin name: WooCommerce Business ID.
- Plugin slug: `woocommerce-business-id`.
- Text domain: `woocommerce-business-id`.
- PHP namespace: `WooCommerceBusinessId`.
- Plugin header description: `Adds configurable business ID fields to WooCommerce settings, displays them in emails, and provides a template helper for custom placements.`
- Business ID option name: `woocommerce_business_id_number`.
- Business ID description option name: `woocommerce_business_id_description`.
- Default business ID description: `Business ID`.
- Email display format: `{business ID description}: {business ID}`.
- Empty business ID behavior: render nothing.
- Empty business ID description behavior: use the default `Business ID` description.
- Deactivation behavior: preserve both saved options.
- Uninstall behavior: delete both saved options.

## Technical Approach

### Coding standards, namespaces, and autoloading

Follow WordPress PHP Coding Standards for production PHP files:

- Production class names use capitalized words separated by underscores, for example `General_Settings`.
- Production class files use lowercase hyphenated file names with `class-` prepended, for example `class-general-settings.php`.
- Production source directories use lowercase names, for example `src/admin/`.
- Each production class lives in its own file.
- Public and protected methods use `snake_case`.
- Test class files are the exception: PHPUnit test files should reflect the test class name exactly.

Use namespaces for all plugin classes, following WordPress and WooCommerce extension guidance to keep the plugin isolated from other extensions:

- Root namespace: `WooCommerceBusinessId`.
- Admin classes: `WooCommerceBusinessId\Admin`.
- Email classes: `WooCommerceBusinessId\Email`.
- Utility classes: `WooCommerceBusinessId\Utilities`.
- Do not use `Automattic\WooCommerce` or other vendor-owned namespaces.
- Continue prefixing non-namespaced constructs such as option names, hooks, constants, and text domains because namespaces do not apply to those identifiers.

Use a classmap-backed autoloader because WordPress class-file naming does not match PSR-4 file-name expectations.

Target implementation:

- Main plugin file manually requires `src/class-autoloader.php`.
- `Autoloader` registers itself with `spl_autoload_register()`.
- The autoloader reads a map from `src/autoload-classmap.php`.
- The map keys are fully qualified class names.
- The map values are plugin-relative file paths.
- The autoloader only loads classes within the `WooCommerceBusinessId` namespace.
- Missing files should fail gracefully by returning without requiring anything.
- `composer.json` may still manage development tools, but production autoloading must not depend on PSR-4.

Initial class map:

```php
<?php
return array(
	'WooCommerceBusinessId\\Autoloader' => 'src/class-autoloader.php',
	'WooCommerceBusinessId\\Plugin' => 'src/class-plugin.php',
	'WooCommerceBusinessId\\Admin\\General_Settings' => 'src/admin/class-general-settings.php',
	'WooCommerceBusinessId\\Email\\Business_ID_Renderer' => 'src/email/class-business-id-renderer.php',
	'WooCommerceBusinessId\\Template\\Template_Tags' => 'src/template/class-template-tags.php',
	'WooCommerceBusinessId\\Utilities\\Business_ID_Sanitizer' =>
		'src/utilities/class-business-id-sanitizer.php',
);
```

Global helper functions are not autoloadable by class name. The main plugin file must manually require `src/functions.php` after registering the autoloader and before plugin initialization.

### Admin settings

Use the WooCommerce Settings API to inject two fields into the existing General settings page instead of creating a separate admin page.

Target implementation:

- Register both fields through the WooCommerce General settings filter.
- Add a `Business ID description` text field.
- Add a `Business ID` text field.
- Set the default Business ID description to `Business ID`.
- For the description field, use helper text explaining that this is the label shown before the identifier in emails, for example `ABN`, `VAT ID`, or `GST number`.
- For the business ID field, use helper text explaining that this is the identifier value shown after the description in emails, for example `51 824 753 556`.
- Rely on WooCommerce's settings save flow for nonce and capability checks.
- Add plugin-specific sanitizers for both options.
- Trim whitespace from both values.
- Strip HTML and control characters from both values.
- For the description, remove a trailing colon if the merchant enters one so emails do not render a double colon.
- For the description, enforce a reasonable maximum length, for example 50 characters.
- For the business ID, allow common identifier characters: letters, numbers, spaces, hyphens, periods, slashes, and underscores.
- For the business ID, enforce a reasonable maximum length, for example 100 characters.
- Store an empty string when the merchant clears either field.

### Email output

Create a dedicated email renderer responsible for reading, formatting, and escaping the stored description and business ID.

Target behavior:

- If `woocommerce_business_id_number` is empty, do not change email output.
- If the business ID is present, render a line in the format `{business ID description}: {business ID}`.
- Example output: `ABN: 51 824 753 556`.
- If `woocommerce_business_id_description` is empty, use the default description `Business ID`.
- Escape the description and value at output time.
- Render HTML emails with simple paragraph or line-break markup that matches WooCommerce email styling.
- Render plain-text emails as a simple text line.
- Apply the output to all WooCommerce email types, including admin, customer, order, and invoice emails.

Implementation note:

- During implementation, verify the exact target hook against the supported WooCommerce versions.
- Preferred path is to append to the WooCommerce email footer text flow if it places the value below the store/company address in both HTML and plain-text emails.
- If the target WooCommerce template separates store address and footer text, use the closest stable WooCommerce email hook and document the placement tradeoff.

### Template helper API

Provide a public helper API so developers can add the configured business ID output to any WordPress, WooCommerce, or theme template hook without instantiating plugin classes.

Target implementation:

- Add `src/functions.php` for global, prefixed helper functions.
- Add `src/template/class-template-tags.php` for the namespaced implementation used by those functions.
- Define `woocommerce_business_id_get_output( array $args = array() ): string`.
- Define `woocommerce_business_id_output( mixed ...$hook_args ): void`.
- Register `woocommerce_business_id_output()` to the plugin-owned action `woocommerce_business_id_template_output`.
- The helper output must reuse the same formatting and escaping rules as email output.
- If the Business ID is blank, the helper must return or echo nothing.
- The helper must default to HTML output because template action hooks usually render HTML.
- The helper must support a plain-text mode through `woocommerce_business_id_get_output( array( 'format' => 'plain_text' ) )` for advanced use.

Primary examples to document:

```php
// Add the business ID to any template hook.
add_action( 'woocommerce_after_shop_loop', 'woocommerce_business_id_output' );
```

```php
// Print the business ID directly in a template.
woocommerce_business_id_output();
```

```php
// Print via the plugin-provided action.
do_action( 'woocommerce_business_id_template_output' );
```

```php
// Get the formatted output without echoing it.
$business_id = woocommerce_business_id_get_output();
```

Documentation requirements:

- The plugin header description must mention that a template helper is available.
- `readme.txt` must include a `Template helper` section with the examples above.
- `README.md` must include the same examples for developers.
- Inline PHPDoc must document both helper functions and the `woocommerce_business_id_template_output` action.
- The helper API must be treated as public and backwards-compatible after version 1.0.0.

### Compatibility

- Minimum PHP version: 8.0.
- Minimum WordPress version: 6.4.
- Minimum WooCommerce version: 8.5.
- Declare HPOS compatibility even though this plugin does not read or write order data.
- No Cart and Checkout Blocks integration is required because the plugin does not alter cart or checkout behavior.
- No Product Block Editor integration is required because the plugin does not alter product editing.
- No custom database tables are required.
- No external API integrations are required.
- No scheduled actions or background processing are required.

## Proposed File Structure

```text
woocommerce-business-id/
|-- woocommerce-business-id.php
|-- uninstall.php
|-- composer.json
|-- phpcs.xml.dist
|-- phpstan.neon.dist
|-- phpunit.xml.dist
|-- package.json
|-- README.md
|-- readme.txt
|-- documentation/
|   `-- roadmap.md
|-- src/
|   |-- autoload-classmap.php
|   |-- class-autoloader.php
|   |-- class-plugin.php
|   |-- functions.php
|   |-- admin/
|   |   `-- class-general-settings.php
|   |-- email/
|   |   `-- class-business-id-renderer.php
|   |-- template/
|   |   `-- class-template-tags.php
|   `-- utilities/
|       `-- class-business-id-sanitizer.php
|-- tests/
|   |-- Unit/
|   |   `-- Business_ID_Sanitizer_Test.php
|   |-- Integration/
|   |   |-- General_Settings_Test.php
|   |   |-- Email_Renderer_Test.php
|   |   `-- Template_Helper_Test.php
|   `-- bootstrap.php
`-- languages/
```

## Milestones

### Milestone 1: Project foundation

Deliverables:

- Main plugin file with valid WordPress/WooCommerce headers.
- Namespaced classmap autoloader using WordPress class-file naming.
- Bootstrap class.
- Public template helper functions loaded from `src/functions.php`.
- WooCommerce dependency check.
- HPOS compatibility declaration.
- Uninstall handler that removes `woocommerce_business_id_number` and `woocommerce_business_id_description`.
- Coding standards, PHPUnit, PHPStan, and basic CI configuration.

Acceptance criteria:

- Plugin activates only when WooCommerce is available.
- Plugin classes autoload through the class map without manual `require_once` chains.
- Global helper functions load once and are protected with `function_exists()` checks.
- Production class files use `class-name-of-class.php` naming and classes use `Name_Of_Class` naming.
- Plugin activation does not create custom tables or order metadata.
- Plugin deactivation leaves merchant data intact.
- Plugin uninstall removes only this plugin's options.

### Milestone 2: WooCommerce General setting

Deliverables:

- Business ID description and Business ID fields appear in `WooCommerce > Settings > General`.
- Merchant can save, update, and clear both values.
- Sanitizer handles common real-world descriptions and identifiers without applying jurisdiction-specific rules.
- All setting labels and descriptions are translatable.

Acceptance criteria:

- Saving `ABN` as the description stores `ABN`.
- Saving `51 824 753 556` stores `51 824 753 556`.
- Saving `ABN:` as the description stores `ABN`.
- Saving `ABN <script>alert(1)</script>` as the description stores a safe text value without executable markup.
- Saving `51 <script>alert(1)</script>` as the business ID stores a safe text value without executable markup.
- Clearing either field stores an empty value.
- Users without `manage_woocommerce` cannot save either setting through the normal WooCommerce settings flow.

### Milestone 3: Email rendering

Deliverables:

- Business ID line is added to WooCommerce emails when a business ID value is configured.
- The line uses the exact format `{business ID description}: {business ID}`.
- No email output changes when the business ID field is blank.
- HTML and plain-text email output are both supported.
- Renderer has one clear public method for formatted output and small private helpers for value lookup and escaping.

Acceptance criteria:

- Customer processing order email includes the configured description and Business ID, for example `ABN: 51 824 753 556`.
- Customer invoice email includes the configured description and Business ID.
- Admin new order email includes the configured description and Business ID.
- Plain-text email includes the configured description and Business ID as text.
- If the business ID description is blank and the business ID is present, email output uses `Business ID` as the description.
- Email output never includes unescaped merchant-provided HTML.

### Milestone 4: Template helper API

Deliverables:

- `woocommerce_business_id_get_output()` returns formatted output without echoing it.
- `woocommerce_business_id_output()` echoes formatted output and can be attached to any template action.
- `woocommerce_business_id_template_output` action is registered to output the business ID.
- Helper output shares the same description, Business ID, escaping, and blank-value behavior as email output.
- Plugin header description, `readme.txt`, and `README.md` document the helper API with copy-pasteable examples.

Acceptance criteria:

- `add_action( 'woocommerce_after_shop_loop', 'woocommerce_business_id_output' );` prints the configured output at that hook.
- `do_action( 'woocommerce_business_id_template_output' );` prints the configured output.
- `woocommerce_business_id_get_output()` returns HTML output when the Business ID is configured.
- `woocommerce_business_id_get_output( array( 'format' => 'plain_text' ) )` returns plain-text output.
- All helper paths return or echo nothing when the Business ID is blank.
- Helper output never includes unescaped merchant-provided HTML.

### Milestone 5: Test coverage and quality gates

Deliverables:

- Unit tests for sanitizer behavior.
- Integration tests for WooCommerce settings registration and saved options behavior.
- Integration tests for email rendering when the Business ID is present, absent, and present with a blank description.
- Integration tests for helper function output and the plugin-owned helper action.
- Static analysis configuration.
- Coding standards configuration.

Acceptance criteria:

- `composer test` or equivalent runs PHPUnit.
- `vendor/bin/phpcs` passes.
- PHPCS enforces WordPress class-file naming for production source files.
- `vendor/bin/phpstan analyse` passes at the configured level.
- Tests cover common descriptions and identifiers including `ABN`, `VAT ID`, `GST number`, ABN-style spacing, VAT-style alphanumerics, hyphenated IDs, blank values, trailing colons, and HTML input.
- Tests verify `woocommerce_business_id_output()`, `woocommerce_business_id_get_output()`, and `woocommerce_business_id_template_output`.

### Milestone 6: Manual QA and release packaging

Deliverables:

- Manual QA checklist.
- WordPress.org-style `readme.txt` with a template helper section.
- Installation, usage, and developer helper notes in `README.md`.
- Release zip build command.

Acceptance criteria:

- Tested on a local WooCommerce store with HTML emails enabled.
- Tested with plain-text emails enabled.
- Tested with HPOS enabled.
- Tested with a custom WooCommerce email template if one is available.
- No PHP warnings or notices in debug mode.
- Release artifact excludes development-only files where appropriate.

## Manual QA Checklist

- Activate the plugin with WooCommerce active.
- Confirm no top-level admin menu is added.
- Open `WooCommerce > Settings > General`.
- Confirm the Business ID description and Business ID fields appear in a logical location near store/business address settings.
- Save `ABN` as the Business ID description.
- Save an ABN-like Business ID value, for example `51 824 753 556`.
- Trigger a customer processing order email.
- Trigger a customer invoice email.
- Trigger an admin new order email.
- Confirm `ABN: 51 824 753 556` appears once in each email.
- Confirm clearing the Business ID removes the line from future emails.
- Confirm clearing the Business ID description while keeping the Business ID uses `Business ID: 51 824 753 556`.
- Confirm HTML entered into either field is displayed as harmless text or removed by sanitization.
- Add `add_action( 'woocommerce_after_shop_loop', 'woocommerce_business_id_output' );` in a test snippet and confirm the output appears at that template hook.
- Call `do_action( 'woocommerce_business_id_template_output' );` in a test template and confirm the output appears.
- Enable plain-text emails and repeat email rendering checks.
- Enable HPOS and repeat the email checks.

## Risks and Mitigations

### Email placement differs by template

WooCommerce email templates and custom theme overrides may not expose a perfect hook immediately below the store/company address.

Mitigation:

- Use the most stable WooCommerce email hook or footer-text filter available for all email types.
- Document the exact placement.
- Add compatibility notes for stores using overridden email templates.

### Jurisdiction-specific validation could reject valid IDs

Business identifiers vary significantly by country.

Mitigation:

- Keep version 1 validation format-neutral.
- Sanitize for safety, not legal correctness.
- Consider optional jurisdiction-specific validation in a later release.

### Merchants may enter inconsistent descriptions

Some merchants may enter descriptions with punctuation or wording that produces awkward email output.

Mitigation:

- Provide clear helper text with examples such as `ABN`, `VAT ID`, and `GST number`.
- Strip trailing colons during sanitization.
- Use `Business ID` as a safe default when the description is blank.

### Public helper API creates compatibility obligations

Once documented, helper function names and output behavior become part of the public API.

Mitigation:

- Prefix global helper functions with `woocommerce_business_id_`.
- Keep helper output behavior stable after version 1.0.0.
- Add integration tests for helper functions and the helper action.
- Document any future changes in the changelog before release.

## Future Enhancements

- Optional country-specific examples or help text based on store country.
- Optional validation presets for common identifiers such as ABN, VAT ID, and GST number.
- Optional display in PDF invoice plugins through integration hooks.
- Optional REST API exposure for the saved store business identifier and description.
- Optional WP-CLI command to read or update the setting.
- Optional shortcode or block wrapper around the same helper output.

## Definition of Done

The plugin is ready for version 1 when:

- Merchants can save a Business ID description and Business ID in WooCommerce General settings.
- The description and value appear in outgoing WooCommerce emails when the Business ID is configured.
- The email line uses the format `{business ID description}: {business ID}`.
- The line does not appear when the Business ID is blank.
- Developers can attach `woocommerce_business_id_output()` to any template action to print the same output.
- The plugin-owned `woocommerce_business_id_template_output` action is documented and tested.
- The plugin header description, `readme.txt`, and `README.md` document the helper API.
- All dynamic output is escaped.
- All stored input is sanitized.
- Production class names, class-file names, and namespaces follow WordPress coding standards.
- The classmap autoloader loads all plugin classes without PSR-4 filename assumptions.
- HPOS compatibility is declared.
- Automated tests pass.
- Manual QA confirms HTML and plain-text email behavior.
- The release package contains only production-ready files.
