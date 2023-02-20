<?php

function va_subscription_post_type() {
	$labels = array(
		'name'               => _x( 'Animal subscriptions', 'post type general name', 'virtual-donations' ),
		'singular_name'      => _x( 'Animal subscription', 'post type singular name', 'virtual-donations' ),
		'menu_name'          => _x( 'Animal subscriptions', 'admin menu', 'virtual-donations' ),
		'name_admin_bar'     => _x( 'Animal subscription', 'add new on admin bar', 'virtual-donations' ),
		'add_new'            => _x( 'Add New Animal subscription', 'Animal subscription', 'virtual-donations' ),
		'add_new_item'       => __( 'Add New Animal subscription', 'virtual-donations' ),
		'new_item'           => __( 'New animal subscription', 'virtual-donations' ),
		'edit_item'          => __( 'Edit animal subscription', 'virtual-donations' ),
		'view_item'          => __( 'View animal subscription', 'virtual-donations' ),
		'all_items'          => __( 'All animal subscriptions', 'virtual-donations' ),
		'search_items'       => __( 'Search animal subscriptions', 'virtual-donations' ),
		'parent_item_colon'  => __( 'Parent animal subscriptions:', 'virtual-donations' ),
		'not_found'          => __( 'No animal subscription found.', 'virtual-donations' ),
		'not_found_in_trash' => __( 'No animal subscription found in Trash.', 'virtual-donations' )
	);

	$args = array(
		'labels'              => $labels,
		'description'         => __( 'Description.', 'virtual-donations' ),
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_nav_menus'   => false,
		'show_in_menu'        => true,
		'query_var'           => false,
		'rewrite'             => array( 'slug' => 'va-subscription' ),
		'capability_type'     => 'post',
		'exclude_from_search' => true,
		'has_archive'         => false,
		'hierarchical'        => false,
		'menu_position'       => null,
		'supports'            => array( 'title' )
	);
	register_post_type( 'va-subscription', $args );
}

add_action( 'init', 'va_subscription_post_type' );


/**
 * Registers 3 extra post types used for the va-subscription post type
 * va-pending - marks all the subscriptions that are pending payment or some other issue to be resolved
 * va-active - active subscription that renews every month
 * va-cancelled - is an old subscription that was cancelled
 *
 * @return void
 */
function va_custom_post_status_for_subscriptions() {
	register_post_status( 'va-active', array(
		'label'                     => 'Active',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'va-pending', array(
		'label'                     => 'Pending',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'va-cancelled', array(
		'label'                     => 'Cancelled',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>' ),
	) );
}

add_action( 'init', 'va_custom_post_status_for_subscriptions' );


/**
 * Handles the custom post statuses for the admin for the subscriptions post type
 *
 * @return void
 */
function va_append_post_status_list() {
	global $post;
	$options = '';
	$label   = '';
	if ( $post->post_type === 'va-subscription' ) {
		if ( $post->post_status === 'va-pending' ) {
			$options .= '<option value="va-pending" selected=\"selected\">Pending</option>';
			$label   = ' Pending';
		} else {
			$options .= '<option value="va-pending">Pending</option>';
		}

		if ( $post->post_status === 'va-active' ) {
			$options .= '<option value="va-active" selected=\"selected\">Active</option>';
			$label   = ' Active';
		} else {
			$options .= '<option value="va-active">Active</option>';
		}

		if ( $post->post_status === 'va-cancelled' ) {
			$options .= '<option value="va-cancelled" selected=\"selected\">Cancelled</option>';
			$label   = ' Cancelled';
		} else {
			$options .= '<option value="va-cancelled">Cancelled</option>';
		}
		echo '
          <script>
          jQuery(document).ready(function(){
              document.getElementById("post_status").innerHTML = \'' . $options . '\'; // Replaces the standard post statuses
			  document.getElementById("post-status-display").innerText = "' . $label . '";
              document.getElementById("save-post").remove(); // removes the "Save draft" button
              // Check if button is "publish" or "update" (when it is "publish" we need to change it, else it will change the post type to "publish" and not to what we need
              const updateButton = document.getElementById("publish");
              if (updateButton.value === "Publish") {
				updateButton.value = "Update";
                updateButton.name = "save"
                document.getElementById("original_publish").value = "Update";
              }
          });
          </script>
          ';
	}
}

add_action( 'admin_footer-post.php', 'va_append_post_status_list' );


/**
 * Displays the post status in admin next to the post title for the subscriptions
 *
 * @param $states
 *
 * @return mixed|string[]
 */
function va_display_archive_state( $states ) {
	global $post;
	$arg = get_query_var( 'post_status' );
	if ( $arg !== 'archive' ) {
		if ( $post->post_status === 'va-pending' ) {
			return array( 'Pending' );
		}
		if ( $post->post_status === 'va-active' ) {
			return array( 'Active' );
		}
		if ( $post->post_status === 'va-cancelled' ) {
			return array( 'Cancelled' );
		}
	}

	return $states;
}

add_filter( 'display_post_states', 'va_display_archive_state' );

/**
 * This filter will remove the Quick Edit link from the admin list of all post with post type va-subscription
 *
 * @param array $actions
 * @param WP_Post $post
 *
 * @return array
 */
function remove_quick_edit( array $actions, WP_Post $post ): array {
	if ( $post->post_type === 'va-subscription' ) {
		unset( $actions['inline hide-if-no-js'] );
	}

	return $actions;
}

add_filter( 'post_row_actions', 'remove_quick_edit', 10, 2 );


/**
 * Check if post status of the va-subscription is changed
 * It will look only for the case when the subscription is changed from "Pending" to "Active"
 * On that change it will send an email with success message
 *
 * @param string $new_status New post status.
 * @param string $old_status Old post status.
 * @param WP_Post $post Post object.
 */
function va_change_of_subscription_post_status( string $new_status, string $old_status, WP_Post $post ) {
	if ( $old_status === $new_status || $post->post_type !== 'sheltered-animal' ) {
		return;
	}

	global $wpdb;
	$wpdb->update(
		$wpdb->prefix . 'va_subscriptions',
		[ 'status' => $new_status ],
		[ 'post_id' => $post->ID ],
		[ '%s' ],
		[ '%d' ]
	);

	$subscription = va_get_subscription_by_post_id( $post->ID );

	// In case the admin is cancelling the subscription
	if ( $new_status === 'va-cancelled' && $old_status === 'va-active' ) {
		va_paypal_cancel_subscription( $subscription, 'Subscription\'s status was changed' );
	}

	va_send_confirmation_email( $post );
}

add_action( 'transition_post_status', 'va_change_of_subscription_post_status', 10, 3 );


/**
 * Hook to the deletion of subscription post and also delete the va_subscription entry
 * This will not delete the entry from PayPal, but it should be already cancelled there
 *
 * @param int $post_id
 *
 * @return void
 */
function va_on_deleting_subscription_post( int $post_id ) {
	$post = get_post($post_id);
	if ($post->post_status !== 'va-subscription') {
		return;
	}

	global $wpdb;
	$wpdb->delete(
		$wpdb->prefix . 'va_subscriptions',
		[ 'post_id' => $post_id ]
	);
}

add_action( 'delete_post', 'va_on_deleting_subscription_post', 10, 2 );


/**
 * Hook on moving a post subscription's entry into the trash. Updates the status of the va-subscriptions entry to "cancelled".
 *
 * @param int $post_id
 *
 * @return void
 */
function va_change_status_when_post_is_trashed( int $post_id ) {
	// execute only if post type is for the subscriptions
	$post = get_post($post_id);
	if ($post->post_status !== 'va-subscription') {
		return;
	}

	global $wpdb;
	$subscription = va_get_subscription_by_post_id( $post_id );
	// Check if status is not already cancelled
	if ( $subscription['status'] === 'va-cancelled' ) {
		return;
	}

	// In case of active status we need to cancel the PayPal subscription
	if ( $subscription['status'] === 'va-active' ) {
		va_paypal_cancel_subscription( $subscription, 'Subscription was deleted' );
	}

	$wpdb->update(
		$wpdb->prefix . 'va_subscriptions',
		[ 'status' => 'va-cancelled' ],
		[ 'post_id' => $post_id ]
	);

}

add_action( 'wp_trash_post', 'va_change_status_when_post_is_trashed' );


/**
 * Register meta box for subscription's details. There is no metadata that is being saved with this meta box.
 */
function va_meta_info_about_subscription() {
	add_meta_box(
		'subscription-info',
		__( 'Subscription\'s details', 'virtual-adoption' ),
		'va_subscription_admin_details',
		'va-subscription',
		'normal'
	);
}

add_action( 'add_meta_boxes', 'va_meta_info_about_subscription' );

/**
 * Function to display the details of the subscription in the admin
 *
 * @param WP_Post $post
 *
 * @return void
 */
function va_subscription_admin_details( WP_Post $post ) {
	$subscription_details = va_get_subscription_by_post_id( $post->ID );
	$user                 = get_user_by( 'ID', $post->post_author );
	$supported_animal     = get_post($subscription_details['sponsored_animal_id']);
	$total                = (float) ($subscription_details['completed_cycles'] *  $subscription_details['amount'] );
	?>
	<div>
		<p>
			<strong><?php _e('Sponsor\'s name', 'virtual-adoption') ?>:</strong>
			<a href="/wp-admin/user-edit.php?user_id=<?php echo $user->ID  ?>">
				<?php echo $user->first_name . ' ' . $user->last_name; ?>
			</a>
		</p>
		<p>
			<strong><?php _e('Sponsor\'s email', 'virtual-adoption') ?>:</strong>
			<a href="mailto:<?php echo $user->user_email  ?>">
				<?php echo $user->user_email; ?>
			</a>
		</p>
		<p>
			<strong><?php _e('Sponsored animal', 'virtual-adoption') ?>:</strong>
			<a href="<?php echo get_permalink($supported_animal->ID);  ?>">
				<?php echo $supported_animal->post_title; ?>
			</a>
		</p>
		<p>
			<strong><?php _e('Start date', 'virtual-adoption') ?>:</strong>
			<?php echo $subscription_details['start_date']; ?>
		</p>
		<p>
			<strong><?php _e('Subscription amount', 'virtual-adoption') ?>:</strong>
			<?php echo (float) $subscription_details['amount'] . ' ' . $subscription_details['currency']?>
		</p>
		<p>
			<strong><?php _e('Number of monthly payments', 'virtual-adoption') ?>:</strong>
			<?php echo $subscription_details['completed_cycles']; ?>
		</p>
		<p>
			<strong><?php _e('Total ', 'virtual-adoption') ?>:</strong>
			<?php echo $total . ' ' . $subscription_details['currency']; ?>
		</p>
	</div>
	<?php
}
