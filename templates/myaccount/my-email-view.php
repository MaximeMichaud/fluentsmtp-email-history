<?php
/**
 * Single email view template.
 *
 * @package WC_User_Emails
 * @var int $email_id Email ID passed from endpoint.
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$email_id     = isset( $email_id ) ? $email_id : 0;
$email        = wc_user_emails_get_single( $email_id, $current_user->user_email );

if ( ! $email ) {
	wc_print_notice( esc_html__( 'Email not found or you do not have access to this email.', 'wc-user-emails' ), 'error' );
	echo '<p><a href="' . esc_url( wc_get_endpoint_url( 'emails' ) ) . '" class="woocommerce-button button">&larr; ' . esc_html__( 'Back to list', 'wc-user-emails' ) . '</a></p>';
	return;
}
?>

<div class="woocommerce-my-email-view">

	<p class="woocommerce-my-email-back">
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'emails' ) ); ?>" class="woocommerce-button button">
			&larr; <?php esc_html_e( 'Back to list', 'wc-user-emails' ); ?>
		</a>
	</p>

	<div class="woocommerce-my-email-header">
		<h2><?php echo wp_kses_post( $email['subject'] ); ?></h2>

		<div class="woocommerce-my-email-meta">
			<div class="email-meta-row">
				<span class="meta-label"><?php esc_html_e( 'Status:', 'wc-user-emails' ); ?></span>
				<span class="meta-value"><?php echo wc_user_emails_status_badge( $email['status'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</div>

			<div class="email-meta-row">
				<span class="meta-label"><?php esc_html_e( 'Date:', 'wc-user-emails' ); ?></span>
				<span class="meta-value">
					<time datetime="<?php echo esc_attr( $email['created_at'] ); ?>">
						<?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $email['created_at'] ) ) ); ?>
					</time>
				</span>
			</div>

			<div class="email-meta-row">
				<span class="meta-label"><?php esc_html_e( 'From:', 'wc-user-emails' ); ?></span>
				<span class="meta-value"><?php echo esc_html( $email['from'] ); ?></span>
			</div>

			<div class="email-meta-row">
				<span class="meta-label"><?php esc_html_e( 'To:', 'wc-user-emails' ); ?></span>
				<span class="meta-value">
					<?php
					if ( is_array( $email['to'] ) ) {
						$recipients = array();
						foreach ( $email['to'] as $recipient ) {
							if ( is_array( $recipient ) && isset( $recipient['email'] ) ) {
								$name         = ! empty( $recipient['name'] ) ? $recipient['name'] . ' ' : '';
								$recipients[] = $name . '<' . $recipient['email'] . '>';
							} elseif ( is_string( $recipient ) ) {
								$recipients[] = $recipient;
							}
						}
						echo esc_html( implode( ', ', $recipients ) );
					} else {
						echo esc_html( $email['to'] );
					}
					?>
				</span>
			</div>

			<?php if ( ( isset( $email['retries'] ) && $email['retries'] > 0 ) || ( isset( $email['resent_count'] ) && $email['resent_count'] > 0 ) ) : ?>
				<div class="email-meta-row">
					<span class="meta-label"><?php esc_html_e( 'Attempts:', 'wc-user-emails' ); ?></span>
					<span class="meta-value">
						<?php
						$parts = array();
						if ( ! empty( $email['retries'] ) ) {
							/* translators: %d: number of retries */
							$parts[] = sprintf( esc_html__( 'Retries: %d', 'wc-user-emails' ), (int) $email['retries'] );
						}
						if ( ! empty( $email['resent_count'] ) ) {
							/* translators: %d: number of resends */
							$parts[] = sprintf( esc_html__( 'Resends: %d', 'wc-user-emails' ), (int) $email['resent_count'] );
						}
						echo esc_html( implode( ' | ', $parts ) );
						?>
					</span>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $email['attachments'] ) && is_array( $email['attachments'] ) ) : ?>
				<div class="email-meta-row">
					<span class="meta-label"><?php esc_html_e( 'Attachments:', 'wc-user-emails' ); ?></span>
					<span class="meta-value">
						<?php
						$attachment_names = array();
						foreach ( $email['attachments'] as $attachment ) {
							if ( is_array( $attachment ) && isset( $attachment[2] ) ) {
								$attachment_names[] = $attachment[2];
							} elseif ( is_array( $attachment ) && isset( $attachment[0] ) ) {
								$attachment_names[] = basename( $attachment[0] );
							}
						}
						echo esc_html( implode( ', ', $attachment_names ) );
						?>
					</span>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="woocommerce-my-email-body">
		<h3><?php esc_html_e( 'Email content', 'wc-user-emails' ); ?></h3>
		<div class="email-body-content">
			<?php
			if ( ! empty( $email['body'] ) ) {
				$is_html = ( wp_strip_all_tags( $email['body'] ) !== $email['body'] );

				if ( $is_html ) {
					echo '<iframe class="email-body-iframe" srcdoc="' . esc_attr( $email['body'] ) . '" sandbox="allow-same-origin"></iframe>';
				} else {
					echo '<div class="email-body-text">' . nl2br( esc_html( $email['body'] ) ) . '</div>';
				}
			} else {
				esc_html_e( 'No content available.', 'wc-user-emails' );
			}
			?>
		</div>
	</div>

	<?php if ( $email['status'] === 'failed' && ! empty( $email['response'] ) ) : ?>
		<div class="woocommerce-my-email-error">
			<h3><?php esc_html_e( 'Error details', 'wc-user-emails' ); ?></h3>
			<div class="email-error-content">
				<?php
				if ( is_array( $email['response'] ) ) {
					if ( isset( $email['response']['message'] ) ) {
						echo '<p><strong>' . esc_html__( 'Message:', 'wc-user-emails' ) . '</strong> ' . esc_html( $email['response']['message'] ) . '</p>';
					}
					if ( isset( $email['response']['code'] ) ) {
						echo '<p><strong>' . esc_html__( 'Code:', 'wc-user-emails' ) . '</strong> ' . esc_html( $email['response']['code'] ) . '</p>';
					}
				} else {
					echo '<p>' . esc_html( $email['response'] ) . '</p>';
				}
				?>
			</div>
		</div>
	<?php endif; ?>

	<p class="woocommerce-my-email-back">
		<a href="<?php echo esc_url( wc_get_endpoint_url( 'emails' ) ); ?>" class="woocommerce-button button">
			&larr; <?php esc_html_e( 'Back to list', 'wc-user-emails' ); ?>
		</a>
	</p>

</div>

<style>
.woocommerce-my-email-header { background: #f7f7f7; border: 1px solid #ddd; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
.woocommerce-my-email-header h2 { margin: 0 0 15px 0; font-size: 22px; }
.email-meta-row { display: flex; padding: 8px 0; border-bottom: 1px solid #e5e5e5; }
.email-meta-row:last-child { border-bottom: none; }
.email-meta-row .meta-label { font-weight: 600; color: #555; min-width: 140px; }
.email-meta-row .meta-value { color: #333; }
.woocommerce-my-email-body { background: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
.woocommerce-my-email-body h3 { margin: 0 0 15px 0; font-size: 18px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; }
.email-body-content { background: #fafafa; border: 1px solid #e5e5e5; border-radius: 3px; padding: 15px; min-height: 200px; }
.email-body-iframe { width: 100%; min-height: 400px; border: none; background: white; }
.email-body-text { font-family: monospace; font-size: 13px; line-height: 1.6; white-space: pre-wrap; }
.woocommerce-my-email-error { background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
.woocommerce-my-email-error h3 { margin: 0 0 15px 0; color: #856404; }
.wc-email-status { display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
.wc-email-status-success { background-color: #c6efce; color: #006100; }
.wc-email-status-error { background-color: #ffc7ce; color: #9c0006; }
.wc-email-status-pending { background-color: #fff4ce; color: #9c6500; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var iframe = document.querySelector('.email-body-iframe');
	if (iframe) {
		iframe.onload = function() {
			try {
				var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
				iframe.style.height = (iframeDoc.body.scrollHeight + 20) + 'px';
			} catch(e) {
				iframe.style.height = '500px';
			}
		};
	}
});
</script>
