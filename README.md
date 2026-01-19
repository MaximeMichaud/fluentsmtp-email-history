# Email History for FluentSMTP

[![WPCS](https://github.com/MaximeMichaud/fluentsmtp-email-history/actions/workflows/wpcs.yml/badge.svg)](https://github.com/MaximeMichaud/fluentsmtp-email-history/actions/workflows/wpcs.yml)
[![PHPStan](https://github.com/MaximeMichaud/fluentsmtp-email-history/actions/workflows/phpstan.yml/badge.svg)](https://github.com/MaximeMichaud/fluentsmtp-email-history/actions/workflows/phpstan.yml)
[![Psalm](https://github.com/MaximeMichaud/fluentsmtp-email-history/actions/workflows/psalm.yml/badge.svg)](https://github.com/MaximeMichaud/fluentsmtp-email-history/actions/workflows/psalm.yml)

Display sent emails history in WooCommerce My Account for customers.
Requires [FluentSMTP](https://wordpress.org/plugins/fluent-smtp/) plugin.

## Download

**[Download Latest Release](https://github.com/MaximeMichaud/fluentsmtp-email-history/releases/latest/download/fluentsmtp-email-history.zip)**

## Features

- View email history directly in WooCommerce My Account
- See email subject, date, and status
- View full email content
- Customers can only see their own emails

## Requirements

| Software     | Minimum Version | Tested Up To |
|--------------|-----------------|--------------|
| WordPress    | 6.2             | 6.9          |
| WooCommerce  | 8.0             | 9.5          |
| PHP          | 8.1             | 8.4          |
| FluentSMTP   | Required        | 2.2.95       |

> **Note:** PHP 8.4 and 8.5 pass all CI tests but have not been tested in production.

## Code Quality

This plugin follows strict code quality standards:

| Tool     | Level/Standard                    |
|----------|-----------------------------------|
| PHPCS    | WordPress Coding Standards (WPCS) |
| PHPStan  | Level 10                          |
| Psalm    | Level 8                           |

## Installation

1. [Download the latest release](https://github.com/MaximeMichaud/fluentsmtp-email-history/releases/latest/download/fluentsmtp-email-history.zip)
2. Upload the `fluentsmtp-email-history` folder to `/wp-content/plugins/`
3. Activate the plugin in **Plugins > Installed Plugins**
4. Make sure FluentSMTP is installed and activated

## Contributing

Contributions are welcome. Please submit issues or pull requests on GitHub.

## Contributors

- [MaximeMichaud](https://github.com/MaximeMichaud)

## License

This plugin is licensed under the [GNU General Public License v2.0 or later](https://www.gnu.org/licenses/gpl-2.0.html).
