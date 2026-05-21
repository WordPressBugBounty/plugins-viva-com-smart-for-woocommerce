# Viva.com | Smart Checkout for WooCommerce

**Contributors:** vivawalletplugins  
**Tags:** woocommerce, payments, viva, sepa, apple pay, google pay, bancontact, iris, ideal, giropay, p24, smart checkout  
**Requires at least:** 6.5
**Tested up to:** 6.9
**Requires PHP:** 7.4
**Stable tag:** 1.1.0
**License:** GPLv2
**License URI:** [https://www.gnu.org/licenses/old-licenses/lgpl-2.0.html](https://www.gnu.org/licenses/old-licenses/lgpl-2.0.html)


Take secure online payments on your WooCommerce store with Viva.com Smart Checkout.

---

## Description

Viva.com | Smart Checkout extends WooCommerce by providing a seamless and secure payment gateway. Accept online payments with a modern checkout experience, and multiple payment methods.

### Key Features

- **Seamless Checkout** – Provide a frictionless payment experience optimized for conversion.
- **Multiple Payment Methods** – Accept credit/debit cards, digital wallets, and local payment options.
- **WooCommerce Integration** – Fully compatible with WooCommerce’s payment flow.
- **Localized Experience** – Support for multiple languages.

---

## Important: Webhook security & IP allowlisting

Webhook security relies on **allowlisting Viva.com's published source IP addresses at your server or firewall level**. If your hosting environment, WAF, or security plugin blocks Viva.com's outbound notifications, order statuses will not be updated and payments may appear stuck.

Before going live, make sure your infrastructure allows inbound traffic from the IPs published by Viva.com. The current list and instructions are maintained at:

[developer.viva.com — Whitelist the Viva addresses](https://developer.viva.com/webhooks-for-payments/#whitelist-the-viva-addresses)

This requirement is independent of the plugin — it applies at the server/firewall layer and must be configured by your hosting provider or system administrator.

---

## Frequently Asked Questions

### What payment methods does Viva.com Smart Checkout support?
Viva.com Smart Checkout supports major credit and debit cards, digital wallets like Apple Pay and Google Pay, and various local payment methods depending on your region.


### Can I test transactions before going live?
Yes, Viva.com provides a demo mode for testing transactions before enabling live payments.

### Where can I find documentation?
Full setup and configuration details are available at [Viva.com Documentation](https://developer.viva.com/plugins/).

### Where can I get support?
For support, visit our [Help Center](https://help.viva.com/en/) or reach out via the WooCommerce plugin support forum.

---

## Screenshots

1. Viva.com Smart Checkout with multiple payment options.
2. Smooth and secure checkout process integrated with WooCommerce.
3. Admin settings for configuring Viva.com Smart Checkout.

---

## Changelog

## 1.1.0 - 2026-05-21
- Add dynamic descriptor feature
- Logo update
- Optimize payment transaction handling
- Compatibility with woocommerce latest version
- Update viva.com sdk version
- Admin notices refactor
- Add notice prompting merchants to whitelist Viva.com IPs
- Conditional render notice on thank you page
- Minimum WooCommerce version raised to 9.2 — stores on WC < 9.2 must update WooCommerce before upgrading this plugin
- Minimum PHP version raised to 7.4
- Security: credential fields (client ID, client secret) now masked in admin settings
- Security: REST webhook endpoint now validates EventTypeId and EventData before processing

## 1.0.2 - 2025-05-15
- Add currency to transaction api calls
## 1.0.1 - 2025-03-27
- Add brand color picker option for smart checkout
- Fix double request to viva during configuration
### 1.0.0 - 2025-02-05
- Initial release.  