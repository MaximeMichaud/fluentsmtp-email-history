<?php
/**
 * My Emails list template.
 *
 * @package FluentSMTP_Email_History
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$page         = isset( $_GET['email_page'] ) ? absint( wp_unslash( $_GET['email_page'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$per_page     = 20;

$result       = fluentsmtp_email_history_get_emails( $current_user->user_email, $page, $per_page );
$emails       = $result['emails'];
$total        = $result['total'];
$total_pages  = $result['total_pages'];
$current_page = $result['current_page'];
?>

<div class="woocommerce-my-emails">
	<h2><?php esc_html_e( 'My Emails', 'fluentsmtp-email-history' ); ?></h2>

	<?php if ( empty( $emails ) ) : ?>
		<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
			<?php esc_html_e( 'No emails found.', 'fluentsmtp-email-history' ); ?>
		</div>
	<?php else : ?>

		<p class="woocommerce-my-emails-total">
			<?php
			printf(
				/* translators: %s: number of emails */
				esc_html( _n( '%s email found', '%s emails found', $total, 'fluentsmtp-email-history' ) ),
				'<strong>' . esc_html( number_format_i18n( $total ) ) . '</strong>'
			);
			?>
		</p>

		<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
			<thead>
				<tr>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date">
						<span class="nobr"><?php esc_html_e( 'Date', 'fluentsmtp-email-history' ); ?></span>
					</th>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-subject">
						<span class="nobr"><?php esc_html_e( 'Subject', 'fluentsmtp-email-history' ); ?></span>
					</th>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status">
						<span class="nobr"><?php esc_html_e( 'Status', 'fluentsmtp-email-history' ); ?></span>
					</th>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions">
						<span class="nobr"><?php esc_html_e( 'Actions', 'fluentsmtp-email-history' ); ?></span>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ( $emails as $email ) : ?>
					<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $email['status'] ); ?> order">

						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date" data-title="<?php esc_attr_e( 'Date', 'fluentsmtp-email-history' ); ?>">
							<time datetime="<?php echo esc_attr( $email['created_at'] ); ?>">
								<?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $email['created_at'] ) ) ); ?>
							</time>
						</td>

						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-subject" data-title="<?php esc_attr_e( 'Subject', 'fluentsmtp-email-history' ); ?>">
							<strong><?php echo wp_kses_post( $email['subject'] ); ?></strong>
							<?php if ( ! empty( $email['from'] ) ) : ?>
								<br>
								<small class="woocommerce-email-from">
									<?php esc_html_e( 'From:', 'fluentsmtp-email-history' ); ?>
									<?php echo esc_html( $email['from'] ); ?>
								</small>
							<?php endif; ?>
						</td>

						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status" data-title="<?php esc_attr_e( 'Status', 'fluentsmtp-email-history' ); ?>">
							<?php echo fluentsmtp_email_history_status_badge( $email['status'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</td>

						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions" data-title="<?php esc_attr_e( 'Actions', 'fluentsmtp-email-history' ); ?>">
							<a href="<?php echo esc_url( add_query_arg( 'view', $email['id'], wc_get_endpoint_url( 'emails' ) ) ); ?>" class="woocommerce-button button view">
								<?php esc_html_e( 'View', 'fluentsmtp-email-history' ); ?>
							</a>
						</td>

					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ( $total_pages > 1 ) : ?>
			<nav class="woocommerce-pagination">
				<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'base'      => add_query_arg( 'email_page', '%#%' ),
							'format'    => '',
							'current'   => $current_page,
							'total'     => $total_pages,
							'prev_text' => '&larr;',
							'next_text' => '&rarr;',
							'type'      => 'list',
							'end_size'  => 3,
							'mid_size'  => 3,
						)
					)
				);
				?>
			</nav>
		<?php endif; ?>

	<?php endif; ?>
</div>

<style>
.wc-email-status { display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
.wc-email-status-success { background-color: #c6efce; color: #006100; }
.wc-email-status-error { background-color: #ffc7ce; color: #9c0006; }
.wc-email-status-pending { background-color: #fff4ce; color: #9c6500; }
.woocommerce-email-from { color: #666; font-size: 13px; }
</style>
