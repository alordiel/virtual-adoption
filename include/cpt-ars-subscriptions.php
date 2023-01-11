<?php
add_action( 'init', 'ars_subscription_post_type' );
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


function ars_custom_post_status_for_subscriptions() {
	register_post_status( 'ars-active', array(
		'label'                     => 'Active',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>' ),
	) );
	register_post_status( 'ars-onhold', array(
		'label'                     => 'On hold',
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'On hold <span class="count">(%s)</span>', 'On hold <span class="count">(%s)</span>' ),
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


add_action( 'admin_footer-post.php', 'ars_append_post_status_list' );
function ars_append_post_status_list() {
	global $post;
	$options  = '';
	$label    = '';
	if ( $post->post_type === 'ars-subscription' ) {
		if ( $post->post_status === 'ars-onhold' ) {
			$options .= '<option value="ars-onhold" selected=\"selected\">On hold</option>';
			$label    = ' On hold';
		} else {
			$options .= '<option value="ars-onhold">On hold</option>';
		}

		if ( $post->post_status === 'ars-active' ) {
			$options .= '<option value="ars-active" selected=\"selected\">Active</option>';
			$label    = ' Active';
		} else {
			$options .= '<option value="ars-active">Active</option>';
		}

		if ( $post->post_status === 'ars-cancelled' ) {
			$options .= '<option value="ars-cancelled" selected=\"selected\">Cancelled</option>';
			$label    = ' Cancelled';
		} else {
			$options .= '<option value="ars-cancelled">Cancelled</option>';
		}
		echo '
          <script>
          jQuery(document).ready(function($){
              $("select#post_status").html(\'' . $options . '\');
			  document.getElementById("post-status-display").innerText = "' . $label . '";
              document.getElementById("save-post").remove();
          });
          </script>
          ';
	}
}


function ars_display_archive_state( $states ) {
	global $post;
	$arg = get_query_var( 'post_status' );
	if ( $arg !== 'archive' ) {
		if ( $post->post_status === 'ars-onhold' ) {
			return array( 'On hold' );
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
