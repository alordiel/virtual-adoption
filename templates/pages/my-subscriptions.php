<?php
/**
 * Template name: ARS List of subscriptions
 */
get_header();
$user_id = wp_get_current_user();
?>
	<h2 xmlns="http://www.w3.org/1999/html">This is the list of your subscriptions</h2>

<?php
$subscriptions = get_posts( [
	'post_type'     => 'ars-subscription',
	'post_per_page' => - 1,
	'post_status'   => 'any',
	'post_author'   => $user_id
] );

if ( ! empty( $subscriptions ) ) {
	?>
	<table id="manage-my-subscriptions">
		<thead>
		<tr>
			<th><?php _e( 'Animal name', 'ars-virtual-donation' ) ?></th>
			<th><?php _e( 'Monthly donation', 'ars-virtual-donation' ) ?></th>
			<th><?php _e( 'Next due', 'ars-virtual-donation' ) ?></th>
			<th><?php _e( 'Status', 'ars-virtual-donation' ) ?></th>
			<th><?php _e( 'Actions', 'ars-virtual-donation' ) ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		global $wpdb;
		foreach ( $subscriptions as $subscription ) {
			$sql     = "SELECT * FROM {$wpdb->prefix}ars_subscriptions WHERE post_id = $subscription->ID";
			$details = $wpdb->get_row( $sql );
			if ( empty( $details ) ) {
				sprintf( __( 'We are missing details for subscription with ID %d', 'ars-virtual-donation' ), $subscription->ID );
				continue;
			}
			$post_id = $details->post_id;
			$animal  = get_post( $post_id );
			?>
			<tr class="row-<?php echo $post_id; ?>">
				<td><a href="<?php echo get_permalink( $animal->ID ) ?>"><?php echo $animal->post_title; ?></a></td>
				<td><?php echo $details->amount . ' ' . $details->currency ?></td>
				<td class="next-due-date"><?php echo $details->next_due ?> </td>
				<td class="subscription-status"><?php echo ars_get_verbose_subscription_status( $details->status ) ?></td>
				<td class="row-actions">
					<?php
					if ( $details->status === 'ars-active' ) {
						?>
						<button type="button" class="cancel-button" data-post-id="<?php echo $post_id ?>">
							<?php _e( 'Cancel', 'ars-virtual-donation' ) ?>
							<svg style="display:none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
								<path
									d="M304 48c0-26.5-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48s48-21.5 48-48zm0 416c0-26.5-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48s48-21.5 48-48zM48 304c26.5 0 48-21.5 48-48s-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48zm464-48c0-26.5-21.5-48-48-48s-48 21.5-48 48s21.5 48 48 48s48-21.5 48-48zM142.9 437c18.7-18.7 18.7-49.1 0-67.9s-49.1-18.7-67.9 0s-18.7 49.1 0 67.9s49.1 18.7 67.9 0zm0-294.2c18.7-18.7 18.7-49.1 0-67.9S93.7 56.2 75 75s-18.7 49.1 0 67.9s49.1 18.7 67.9 0zM369.1 437c18.7 18.7 49.1 18.7 67.9 0s18.7-49.1 0-67.9s-49.1-18.7-67.9 0s-18.7 49.1 0 67.9z"/>
							</svg>
						</button>
						<?php
					}
					?>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php wp_nonce_field( 'ars-taina', 'turbo-security' ); ?>
	<?php
} else {
	_e( 'No subscriptions found', 'ars-virtual-donation' );
}
?>

<?php
get_footer();
