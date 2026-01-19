=== Email History for FluentSMTP ===
Contributors: maximemichaud
Tags: fluentsmtp, email history, woocommerce, my account, email logs
Requires at least: 6.2
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 8.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display sent emails history in WooCommerce My Account for customers. Requires FluentSMTP with email logging enabled.

== Description ==

Email History for FluentSMTP adds a "My Emails" tab to WooCommerce My Account page, allowing customers to view their email history directly from their account.

This plugin reads the email logs from FluentSMTP and displays only the emails sent to the logged-in customer.

= Features =

* Adds "My Emails" tab to WooCommerce My Account
* Displays email history with subject, date, and status
* View full email content
* Pagination support
* Multi-language support (English, French)
* HPOS compatible

= Requirements =

* WordPress 6.2+
* WooCommerce 8.0+
* FluentSMTP with email logging enabled
* PHP 8.1+

== Installation ==

1. Make sure FluentSMTP is installed and email logging is enabled
2. Upload the plugin files to `/wp-content/plugins/fluentsmtp-email-history/`
3. Activate the plugin through the 'Plugins' screen in WordPress
4. Go to WooCommerce My Account - you should see the "My Emails" tab

== Frequently Asked Questions ==

= Does this plugin require FluentSMTP? =

Yes, this plugin reads email logs from FluentSMTP's database table. Without FluentSMTP with logging enabled, there will be no emails to display.

= Is customer data secure? =

Yes, customers can only view emails sent to their own email address. The plugin checks the logged-in user's email against the recipient field.

= Can I customize the templates? =

Yes, copy the templates from `plugins/fluentsmtp-email-history/templates/` to `yourtheme/woocommerce/` and modify them.

== Screenshots ==

1. My Emails tab in WooCommerce My Account
2. Email list view with pagination
3. Single email view

== Changelog ==

= 1.0.0 =
* Initial release
* My Emails tab in WooCommerce My Account
* Email list with pagination
* Single email view
* French translation included

== Upgrade Notice ==

= 1.0.0 =
Initial release.
