<?php

function ars_subscription_post_type() {
	$labels = array(
		'name'               => _x( 'Animal subscriptions', 'post type general name', 'ars-virtual-donations' ),
		'singular_name'      => _x( 'Animal subscription', 'post type singular name', 'ars-virtual-donations' ),
		'menu_name'          => _x( 'Animal subscriptions', 'admin menu', 'ars-virtual-donations' ),
		'name_admin_bar'     => _x( 'Animal subscription', 'add new on admin bar', 'ars-virtual-donations' ),
		'add_new'            => _x( 'Add New Animal subscription', 'Animal subscription', 'ars-virtual-donations' ),
		'add_new_item'       => __( 'Add New Animal subscription', 'ars-virtual-donations' ),
		'new_item'           => __( 'New animal subscription', 'ars-virtual-donations' ),
		'edit_item'          => __( 'Edit animal subscription', 'ars-virtual-donations' ),
		'view_item'          => __( 'View animal subscription', 'ars-virtual-donations' ),
		'all_items'          => __( 'All animal subscriptions', 'ars-virtual-donations' ),
		'search_items'       => __( 'Search animal subscriptions', 'ars-virtual-donations' ),
		'parent_item_colon'  => __( 'Parent animal subscriptions:', 'ars-virtual-donations' ),
		'not_found'          => __( 'No animal subscription found.', 'ars-virtual-donations' ),
		'not_found_in_trash' => __( 'No animal subscription found in Trash.', 'ars-virtual-donations' )
	);

	$args = array(
		'labels'              => $labels,
		'description'         => __( 'Description.', 'ars-virtual-donations' ),
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_nav_menus'   => false,
		'show_in_menu'        => true,
		'query_var'           => false,
		'rewrite'             => array( 'slug' => 'ars-subscription' ),
		'capability_type'     => 'post',
		'exclude_from_search' => true,
		'has_archive'         => false,
		'hierarchical'        => false,
		'menu_position'       => null,
		'supports'            => array( 'title' )
	);
	register_post_type( 'ars-subscription', $args );
}

add_action( 'init', 'ars_subscription_post_type' );


/**
 * Registers 3 extra post types used for the ars-subscription post type
 * ars-pending - marks all the subscriptions that are pending payment or some other issue to be resolved
 * ars-active - active subscription that renews every month
 * ars-cancelled - is an old subscription that was cancelled
 *
 * @return void
 */
function ars_custom_post_status_for_subscriptions() {
	register_post_status( 'ars-active', array(
		'label'                     => 'Active',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'ars-pending', array(
		'label'                     => 'Pending',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'ars-cancelled', array(
		'label'                     => 'Cancelled',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>' ),
	) );
}

add_action( 'init', 'ars_custom_post_status_for_subscriptions' );


/**
 * Handles the custom post statuses for the admin for the subscriptions post type
 *
 * @return void
 */
function ars_append_post_status_list() {
	global $post;
	$options = '';
	$label   = '';
	if ( $post->post_type === 'ars-subscription' ) {
		if ( $post->post_status === 'ars-pending' ) {
			$options .= '<option value="ars-pending" selected=\"selected\">Pending</option>';
			$label   = ' Pending';
		} else {
			$options .= '<option value="ars-pending">Pending</option>';
		}

		if ( $post->post_status === 'ars-active' ) {
			$options .= '<option value="ars-active" selected=\"selected\">Active</option>';
			$label   = ' Active';
		} else {
			$options .= '<option value="ars-active">Active</option>';
		}

		if ( $post->post_status === 'ars-cancelled' ) {
			$options .= '<option value="ars-cancelled" selected=\"selected\">Cancelled</option>';
			$label   = ' Cancelled';
		} else {
			$options .= '<option value="ars-cancelled">Cancelled</option>';
		}
		echo '
          <script>
          jQuery(document).ready(function(){
              document.getElementById("post_status").innerHTML = \'' . $options . '\'; // Replaces the standard post statuses
			  document.getElementById("post-status-display").innerText = "' . $label . '";
              document.getElementById("save-post").remove(); // removes the "Save draft" button
          });
          </script>
          ';
	}
}

add_action( 'admin_footer-post.php', 'ars_append_post_status_list' );


/**
 * Displays the post status in admin next to the post title for the subscriptions
 *
 * @param $states
 *
 * @return mixed|string[]
 */
function ars_display_archive_state( $states ) {
	global $post;
	$arg = get_query_var( 'post_status' );
	if ( $arg !== 'archive' ) {
		if ( $post->post_status === 'ars-pending' ) {
			return array( 'Pending' );
		}
		if ( $post->post_status === 'ars-active' ) {
			return array( 'Active' );
		}
		if ( $post->post_status === 'ars-cancelled' ) {
			return array( 'Cancelled' );
		}
	}

	return $states;
}

add_filter( 'display_post_states', 'ars_display_archive_state' );

/**
 * This filter will remove the Quick Edit link from the admin list of all post with post type ars-subscription
 *
 * @param array $actions
 * @param WP_Post $post
 *
 * @return array
 */
function remove_quick_edit( array $actions, WP_Post $post ): array {
	if ( $post->post_type === 'ars-subscription' ) {
		unset( $actions['inline hide-if-no-js'] );
	}

	return $actions;
}

add_filter( 'post_row_actions', 'remove_quick_edit', 10, 2 );


/**
 * Check if post status of the ars-subscription is changed
 * It will look only for the case when the subscription is changed from "Pending" to "Active"
 * On that change it will send an email with success message
 *
 * @param string $new_status New post status.
 * @param string $old_status Old post status.
 * @param WP_Post $post Post object.
 */
function ars_change_of_subscription_post_status( string $new_status, string $old_status, WP_Post $post ) {
	if ( $old_status === $new_status ) {
		return;
	}

	global $wpdb;
	$wpdb->update(
		$wpdb->prefix . 'ars_subscriptions',
		[ 'status' => $new_status ],
		[ 'post_id' => $post->ID ],
		[ '%d' ],
		[ '%d' ]
	);

	// In case the admin is cancelling the subscription
	if ($new_status === 'ars-cancelled') {
		//TODO Cancel recurring payment
	}

	ars_send_confirmation_email( $post );
}

add_action( 'transition_post_status', 'ars_change_of_subscription_post_status', 10, 3 );



/**
 * Hook to the deletion of subscription post and also delete the ars_subscription entry
 *
 * @param int $post_id
 *
 * @return void
 */
function ars_on_deleting_subscription_post( int $post_id ) {
	global $wpdb;
	$wpdb->delete(
		$wpdb->prefix . 'ars_subscriptions',
		[ 'post_id' => $post_id ]
	);
}

add_action( 'delete_post', 'ars_on_deleting_subscription_post', 10, 2 );


/**
 * Hook on moving a post subscription's entry into the trash. Updates the status of the ars-subscriptions entry to "cancelled".
 *
 * @param int $post_id
 *
 * @return void
 */
function ars_change_status_when_post_is_trashed( int $post_id) {
	global $wpdb;
	$wpdb->update(
		$wpdb->prefix . 'ars_subscriptions',
		[ 'status' => 'ars-cancelled'],
		[ 'post_id' => $post_id ]
	);
}
add_action('wp_trash_post', 'ars_change_status_when_post_is_trashed');
