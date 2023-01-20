<?php
/**
 * Add a new dashboard widget.
 */
function va_add_dashboard_widgets() {
	wp_add_dashboard_widget( 'dashboard_widget', 'Virtual Adoption Stats', 'dashboard_widget_function' );
}

add_action( 'wp_dashboard_setup', 'va_add_dashboard_widgets' );

/**
 * Output the contents of the dashboard widget
 */
function dashboard_widget_function() {
	global $wpdb;
	$subscriptions    = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}va_subscriptions" );
	$status_pending   = 0;
	$status_active    = 0;
	$status_cancelled = 0;
	$total_donations  = 0.00;
	foreach ( $subscriptions as $subscription ) {
		if ( $subscription->status === 'va-active' ) {
			$status_active ++;
			$amount = (float) $subscription->amount;
			if ( $subscription->currency === 'EUR' ) {
				$amount *= 1.958;
			}
			$total_donations += $amount;
		} elseif ( $subscription->status === 'va-pending' ) {
			$status_pending ++;
		} elseif ( $subscription->status === 'va-cancelled' ) {
			$status_cancelled ++;
		}
	}
	$total_donations   = number_format( $total_donations, 2, '.', ' ' );
	$uploads_directory = wp_upload_dir();
	$report_link_abs   = $uploads_directory['basedir'] . '/virtual-adoptions/monthly-report.csv';
	$report_link_url   = $uploads_directory['baseurl'] . '/virtual-adoptions/monthly-report.csv';
	?>
	<p><?php echo sprintf( __( 'Active subscriptions: %d', 'virtual-adoptions' ), $status_active ); ?></p>
	<p><?php echo sprintf( __( 'Pending subscriptions: %d', 'virtual-adoptions' ), $status_pending ); ?></p>
	<p><?php echo sprintf( __( 'Cancelled subscriptions: %d', 'virtual-adoptions' ), $status_cancelled ); ?></p>
	<p><?php echo sprintf( __( 'Total monthly donations: %s BGN', 'virtual-adoptions' ), $total_donations ); ?></p>
	<p id="report-link" style="display:<?php echo (file_exists($report_link_abs)) ? 'block' : 'none'; ?>">
		<a href="<?php echo $report_link_url; ?>" rel="noopener nofollow noreferrer" target="_blank">
			<?php _e( 'Download the last generated report', 'virtual-adoptions' ) ?>
		</a>
	</p>
	<p>
		<input type="button" name="va-donations-report" class="button-secondary" value="Generate report"
			   id="va-donations-report">
	</p>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			document.getElementById('va-donations-report').addEventListener('click', function () {
				jQuery.ajax({
					url: '/wp-admin/admin-ajax.php',
					data: {
						action: 'va_get_donations_report'
					},
					dataType: 'JSON',
					method: 'POST',
					success: (response) => {
						if (response.status === 0) {
							alert(response.message);
						} else {
							alert(response.message);
							const linkParagraph = document.getElementById('report-link');
							linkParagraph.style.display = 'block';
							linkParagraph.children[0].href = response.url;
						}
					},
					error: (error) => {
						alert(error.code + ' > ' + error.message);
					}
				});
			})
		})
	</script>
	<?php
}
