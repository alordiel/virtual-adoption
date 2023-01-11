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
	<table>
		<thead>
		<tr>
			<th><?php _e( 'Animal name', 'ars-virtual-donation' ) ?></th>
			<th><?php _e( 'Next due', 'ars-virtual-donation' ) ?></th>
			<th><?php _e( 'Monthly donation', 'ars-virtual-donation' ) ?></th>
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
			$animal = get_post($details->sponsored_animal_id);
			?>
			<tr>
				<td><a  href="<?php echo get_permalink($animal->ID) ?>"><?php echo $animal->post_title; ?></a></td>
				<td><?php echo $details->next_due ?> </td>
				<td><?php echo $details->amount . ' '. $details->currency ?></td>
				<td><?php echo $details->status ?></td>
				<td></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php
} else {
	_e( 'No subscriptions found', 'ars-virtual-donation' );
}
?>

<?php
get_footer();
