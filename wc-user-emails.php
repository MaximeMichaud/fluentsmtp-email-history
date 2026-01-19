<?php
/**
 * Plugin Name: Email History for FluentSMTP
 * Description: Display sent emails history in WooCommerce My Account for customers
 * Version: 1.0.0
 * Author: Maxime Michaud
 * Author URI: https://github.com/MaximeMichaud
 * Text Domain: wc-user-emails
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.1
 * WC requires at least: 8.0
 * WC tested up to: 9.5
 *
 * @package WC_User_Emails
 */

defined( 'ABSPATH' ) || exit;

define( 'WC_USER_EMAILS_VERSION', '1.0.0' );
define( 'WC_USER_EMAILS_PLUGIN_FILE', __FILE__ );
define( 'WC_USER_EMAILS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Declare HPOS compatibility.
 * This plugin doesn't interact with orders, only reads FluentSMTP email logs.
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * Main plugin class.
 */
final class WC_User_Emails {

	/**
	 * Single instance.
	 *
	 * @var WC_User_Emails|null
	 */
	private static ?WC_User_Emails $instance = null;

	/**
	 * Get instance.
	 *
	 * @return WC_User_Emails
	 */
	public static function instance(): WC_User_Emails {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'add_endpoint' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'add_menu_item' ) );
		add_action( 'woocommerce_account_emails_endpoint', array( $this, 'endpoint_content' ) );
		add_action( 'after_switch_theme', array( $this, 'flush_rewrite_rules' ) );
		register_activation_hook( WC_USER_EMAILS_PLUGIN_FILE, array( $this, 'activate' ) );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		$locale = determine_locale();
		$mofile = WC_USER_EMAILS_PLUGIN_DIR . 'languages/wc-user-emails-' . $locale . '.mo';

		// Try exact locale first (e.g., fr_FR).
		if ( file_exists( $mofile ) ) {
			load_textdomain( 'wc-user-emails', $mofile );
			return;
		}

		// Try short locale (e.g., fr).
		$short_locale = substr( $locale, 0, 2 );
		$mofile_short = WC_USER_EMAILS_PLUGIN_DIR . 'languages/wc-user-emails-' . $short_locale . '.mo';
		if ( file_exists( $mofile_short ) ) {
			load_textdomain( 'wc-user-emails', $mofile_short );
			return;
		}

		// Fallback to standard WordPress loading.
		load_plugin_textdomain( 'wc-user-emails', false, dirname( plugin_basename( WC_USER_EMAILS_PLUGIN_FILE ) ) . '/languages' );
	}

	/**
	 * Plugin activation.
	 *
	 * @return void
	 */
	public function activate(): void {
		$this->add_endpoint();
		flush_rewrite_rules();
	}

	/**
	 * Add rewrite endpoint.
	 *
	 * @return void
	 */
	public function add_endpoint(): void {
		add_rewrite_endpoint( 'emails', EP_ROOT | EP_PAGES );
	}

	/**
	 * Add menu item to My Account.
	 *
	 * @param array<string, string> $items Menu items.
	 * @return array<string, string>
	 */
	public function add_menu_item( array $items ): array {
		$logout = $items['customer-logout'] ?? '';
		unset( $items['customer-logout'] );

		$items['emails'] = esc_html__( 'My Emails', 'wc-user-emails' );

		if ( $logout ) {
			$items['customer-logout'] = $logout;
		}

		return $items;
	}

	/**
	 * Endpoint content.
	 *
	 * @return void
	 */
	public function endpoint_content(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$raw_view = isset( $_GET['view'] ) ? wp_unslash( $_GET['view'] ) : '';
		$raw_view = is_string( $raw_view ) ? $raw_view : ( is_numeric( $raw_view ) ? (string) $raw_view : '' );
		$view_id  = absint( sanitize_text_field( $raw_view ) );

		if ( $view_id > 0 ) {
			wc_get_template(
				'myaccount/my-email-view.php',
				array( 'email_id' => $view_id ),
				'',
				WC_USER_EMAILS_PLUGIN_DIR . 'templates/'
			);
		} else {
			wc_get_template(
				'myaccount/my-emails.php',
				array(),
				'',
				WC_USER_EMAILS_PLUGIN_DIR . 'templates/'
			);
		}
	}

	/**
	 * Flush rewrite rules.
	 *
	 * @return void
	 */
	public function flush_rewrite_rules(): void {
		$this->add_endpoint();
		flush_rewrite_rules();
	}
}

/**
 * Get user emails from FluentSMTP logs.
 *
 * @param string $user_email User email address.
 * @param int    $page       Page number.
 * @param int    $per_page   Results per page.
 * @return array{emails: list<array<string, mixed>>, total: int, total_pages: int, current_page: int}
 */
function wc_user_emails_get_emails( string $user_email, int $page = 1, int $per_page = 20 ): array {
	/** @var \wpdb $wpdb */
	global $wpdb;

	$empty_result = array(
		'emails'       => array(),
		'total'        => 0,
		'total_pages'  => 0,
		'current_page' => 1,
	);

	$table_name = $wpdb->prefix . 'fsmpt_email_logs';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$table_exists = $wpdb->get_var(
		$wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name )
	);

	if ( $table_exists !== $table_name ) {
		return $empty_result;
	}

	$offset = ( $page - 1 ) * $per_page;

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$emails = $wpdb->get_results(
		$wpdb->prepare(
			'SELECT * FROM %i WHERE `to` LIKE %s ORDER BY created_at DESC LIMIT %d OFFSET %d',
			$table_name,
			'%' . $wpdb->esc_like( $user_email ) . '%',
			$per_page,
			$offset
		),
		ARRAY_A
	);

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$total_raw = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT COUNT(*) FROM %i WHERE `to` LIKE %s',
			$table_name,
			'%' . $wpdb->esc_like( $user_email ) . '%'
		)
	);

	$total = is_numeric( $total_raw ) ? (int) $total_raw : 0;

	/** @var list<array<string, mixed>> $processed_emails */
	$processed_emails = array();

	if ( is_array( $emails ) ) {
		foreach ( $emails as $email ) {
			if ( is_array( $email ) ) {
				/** @var array<string, mixed> $email */
				$processed_emails[] = wc_user_emails_unserialize_fields( $email );
			}
		}
	}

	return array(
		'emails'       => $processed_emails,
		'total'        => $total,
		'total_pages'  => (int) ceil( $total / $per_page ),
		'current_page' => $page,
	);
}

/**
 * Get single email by ID.
 *
 * @param int    $email_id   Email ID.
 * @param string $user_email User email for security check.
 * @return array<string, mixed>|null
 */
function wc_user_emails_get_single( int $email_id, string $user_email ): ?array {
	/** @var \wpdb $wpdb */
	global $wpdb;

	$table_name = $wpdb->prefix . 'fsmpt_email_logs';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$email = $wpdb->get_row(
		$wpdb->prepare(
			'SELECT * FROM %i WHERE id = %d AND `to` LIKE %s LIMIT 1',
			$table_name,
			$email_id,
			'%' . $wpdb->esc_like( $user_email ) . '%'
		),
		ARRAY_A
	);

	if ( ! is_array( $email ) ) {
		return null;
	}

	/** @var array<string, mixed> $email */
	return wc_user_emails_unserialize_fields( $email );
}

/**
 * Unserialize email fields.
 *
 * @param array<string, mixed> $email Email data.
 * @return array<string, mixed>
 */
function wc_user_emails_unserialize_fields( array $email ): array {
	$serialized_fields = array( 'to', 'headers', 'attachments', 'response', 'extra' );

	foreach ( $serialized_fields as $field ) {
		if ( isset( $email[ $field ] ) && is_string( $email[ $field ] ) && is_serialized( $email[ $field ] ) ) {
			$email[ $field ] = maybe_unserialize( $email[ $field ] );
		}
	}

	return $email;
}

/**
 * Get status badge HTML.
 *
 * @param string $status Email status.
 * @return string
 */
function wc_user_emails_status_badge( string $status ): string {
	$badges = array(
		'sent'    => array(
			'label' => __( 'Sent', 'wc-user-emails' ),
			'class' => 'success',
		),
		'failed'  => array(
			'label' => __( 'Failed', 'wc-user-emails' ),
			'class' => 'error',
		),
		'pending' => array(
			'label' => __( 'Pending', 'wc-user-emails' ),
			'class' => 'pending',
		),
	);

	$badge = $badges[ $status ] ?? array(
		'label' => $status,
		'class' => 'default',
	);

	return sprintf(
		'<span class="wc-email-status wc-email-status-%s">%s</span>',
		esc_attr( $badge['class'] ),
		esc_html( $badge['label'] )
	);
}

/**
 * Initialize plugin.
 *
 * @return WC_User_Emails
 */
function wc_user_emails(): WC_User_Emails {
	return WC_User_Emails::instance();
}

// Initialize.
wc_user_emails();
