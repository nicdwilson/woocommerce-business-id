# Manual QA Checklist

Run this checklist on a local WooCommerce site before tagging a release.

## Environment

- WordPress 6.4 or later is installed.
- WooCommerce 8.5 or later is installed and active.
- WooCommerce Business ID is installed from the release zip.
- `WP_DEBUG` and `WP_DEBUG_LOG` are enabled.
- HPOS is enabled for at least one pass.

## Setup

- [ ] Activate the plugin with WooCommerce active.
- [ ] Confirm no top-level admin menu is added.
- [ ] Open `WooCommerce > Settings > General`.
- [ ] Confirm the Business ID description and Business ID fields appear near the store address fields.
- [ ] Save `ABN` as the Business ID description.
- [ ] Save `51 824 753 556` as the Business ID.
- [ ] Confirm both fields retain their saved values after the settings page reloads.

## HTML Emails

- [ ] Trigger a customer processing order email.
- [ ] Trigger a customer invoice email.
- [ ] Trigger an admin new order email.
- [ ] Confirm `ABN: 51 824 753 556` appears once in each email.
- [ ] Confirm the output appears below the store/company address area or in the closest stable WooCommerce footer placement.
- [ ] Confirm there are no PHP warnings or notices in the debug log.

## Plain-Text Emails

- [ ] Enable plain-text email output.
- [ ] Trigger a customer processing order email.
- [ ] Trigger a customer invoice email.
- [ ] Trigger an admin new order email.
- [ ] Confirm `ABN: 51 824 753 556` appears once as plain text.
- [ ] Confirm no HTML markup appears in the Business ID line.

## Settings Edge Cases

- [ ] Clear the Business ID value and save.
- [ ] Confirm future emails do not include a Business ID line.
- [ ] Set the Business ID value back to `51 824 753 556`.
- [ ] Clear the Business ID description and save.
- [ ] Confirm future emails use `Business ID: 51 824 753 556`.
- [ ] Save `ABN:` as the Business ID description.
- [ ] Confirm future emails use `ABN: 51 824 753 556`, not `ABN:: 51 824 753 556`.
- [ ] Enter HTML in both fields and save.
- [ ] Confirm future output does not render executable markup.

## Template Helper

- [ ] Add `add_action( 'woocommerce_after_shop_loop', 'woocommerce_business_id_output' );` in a test snippet.
- [ ] Confirm the Business ID appears at that template hook.
- [ ] Call `do_action( 'woocommerce_business_id_template_output' );` in a test template.
- [ ] Confirm the Business ID appears once.
- [ ] Confirm blank Business ID output prints nothing through both helper paths.

## HPOS

- [ ] Enable HPOS.
- [ ] Repeat the HTML email checks.
- [ ] Repeat the plain-text email checks.
- [ ] Confirm no HPOS compatibility warnings appear.

## Custom Template Override

- [ ] If a custom WooCommerce email template is available, repeat one HTML email check with the override active.
- [ ] Note the exact placement of the Business ID line.

## Release Result

- [ ] Manual QA passed.
- [ ] Any failed item is documented with reproduction steps.
- [ ] Debug log reviewed.
- [ ] Release zip installed cleanly on a fresh local site.
