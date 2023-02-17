<?php
/**
 * Template name: VirtualAdopt -  List of subscriptions
 */

// If user is not logged in - redirect to login page
if ( ! is_user_logged_in() ) {
	$settings = get_option( 'va-settings' );
	wp_redirect( get_permalink( $settings['page']['login'] ) );
}

get_header();
$user_id = wp_get_current_user();
?>

<?php
$subscriptions = get_posts( [
	'post_type'     => 'va-subscription',
	'post_per_page' => - 1,
	'post_status'   => 'any',
	'post_author'   => $user_id
] );
?>
	<div class="va-container">
		<?php
		if ( ! empty( $subscriptions ) ) {
			?>
			<h5><?php _e( 'This is the list of your subscriptions', 'virtual-adoption' ); ?></h5>
			<div class="manage-my-subscriptions">
				<?php
				global $wpdb;
				$VA_paypal = new VA_PayPal();
				foreach ( $subscriptions as $subscription ) {
					$sql     = "SELECT * FROM {$wpdb->prefix}va_subscriptions WHERE post_id = $subscription->ID";
					$details = $wpdb->get_row( $sql );
					if ( empty( $details ) ) {
						echo sprintf( __( 'We are missing details for subscription with ID %d', 'virtual-adoption' ), $subscription->ID );
						echo '<br>';
						continue;
					}

					$post_id        = $details->post_id;
					$animal         = get_post( $details->sponsored_animal_id );
					$paypal_details = $VA_paypal->get_subscription_details( $details->paypal_id );
					$image          = get_the_post_thumbnail_url( $details->sponsored_animal_id, 'medium' );
					if ( $paypal_details['status'] === 'CANCELLED' ) {
						$next_due = '';
						$status   = __( 'Cancelled', 'virtual-adoptions' );
					} else {
						$next_due = $paypal_details['billing_info']['next_billing_time'];
						$status   = va_get_verbose_subscription_status( $details->status );
					}
					?>
					<div class="my-sponsored-animal-card card-id-<?php echo $post_id ?>">
						<div class="animal-card-image" style="background-image: url('<?php echo $image; ?>')"></div>
						<p><?php _e( 'Name', 'virtual-adoption' ) ?>:
							<a href="<?php echo get_permalink( $animal->ID ) ?>">
								<?php echo $animal->post_title; ?>
							</a>
						</p>
						<p>
							<?php echo __( 'Monthly donation', 'virtual-adoption' ) . ': ' . $details->amount . ' ' . $details->currency ?>
						</p>
						<?php if ( ! empty( $next_due ) ) : ?>
							<p class="next-due-date">
								<?php echo __( 'Next payment', 'virtual-adoption' ) . ': ' . $next_due ?>
							</p>
						<?php endif; ?>
						<p class="subscription-status">
							<?php echo __( 'Subscription status', 'virtual-adoption' ) . ': ' . $status ?>
						</p>
						<p class="card-actions">
							<?php
							if ( $details->status === 'va-active' && $paypal_details['status'] !== 'CANCELLED' ) {
								?>
								<a href="#" class="cancel-button" data-post-id="<?php echo $post_id ?>">
									<?php _e( 'Cancel subscription', 'virtual-adoption' ) ?>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
										<path
											d="M304 48c0-26.5-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48s48-21.5 48-48zm0 416c0-26.5-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48s48-21.5 48-48zM48 304c26.5 0 48-21.5 48-48s-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48zm464-48c0-26.5-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48s48-21.5 48-48zM142.9 437c18.7-18.7 18.7-49.1 0-67.9s-49.1-18.7-67.9 0s-18.7 49.1 0 67.9s49.1 18.7 67.9 0zm0-294.2c18.7-18.7 18.7-49.1 0-67.9S93.7 56.2 75 75s-18.7 49.1 0 67.9s49.1 18.7 67.9 0zM369.1 437c18.7 18.7 49.1 18.7 67.9 0s18.7-49.1 0-67.9s-49.1-18.7-67.9 0s-18.7 49.1 0 67.9z"/>
									</svg>
								</a>
								<?php
							} elseif ( $details->status === 'va-cancelled' || $paypal_details['status'] === 'CANCELLED' ) {
								$settings      = get_option( 'va-settings' );
								$encrypted_key = va_encode_id( $details->sponsored_animal_id );
								$re_adopt_link = get_permalink( $settings['page']['checkout'] ) . '?aid=' . $encrypted_key;
								?>
								<a href="<?php echo $re_adopt_link; ?>">
									<?php _e( 'Re-adopt', 'virtual-donations' ); ?>
								</a>
								<?php
							}
							?>
						</p>
					</div>
				<?php } ?>
			</div>
			<?php wp_nonce_field( 'va-taina', 'turbo-security' ); ?>
			<?php
		} else {
			$animal_archive_link = get_post_type_archive_link( 'sheltered-animal' );
			?>
			<h5>
				<?php echo sprintf( __( 'No subscriptions found. You can check our animals waiting for your sponsorship <a href="%s">here</a>.', 'virtual-adoption' ), $animal_archive_link ) ?>
			</h5>
			<?php
		}
		?>
	</div>
<?php
get_footer();
