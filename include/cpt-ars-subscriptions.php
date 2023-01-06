<?php
add_action( 'init', 'ars_subscription_post_type' );
function ars_subscription_post_type() {
	$labels = array(
		'name'               => _x( 'Sheltered animals', 'post type general name', 'ars-virtual-donations' ),
		'singular_name'      => _x( 'Sheltered animal', 'post type singular name', 'ars-virtual-donations' ),
		'menu_name'          => _x( 'Sheltered animals', 'admin menu', 'ars-virtual-donations' ),
		'name_admin_bar'     => _x( 'Sheltered animal', 'add new on admin bar', 'ars-virtual-donations' ),
		'add_new'            => _x( 'Add New', 'Sheltered animal', 'ars-virtual-donations' ),
		'add_new_item'       => __( 'Add New', 'ars-virtual-donations' ),
		'new_item'           => __( 'New sheltered animal', 'ars-virtual-donations' ),
		'edit_item'          => __( 'Edit sheltered animal', 'ars-virtual-donations' ),
		'view_item'          => __( 'View sheltered animal', 'ars-virtual-donations' ),
		'all_items'          => __( 'All sheltered animals', 'ars-virtual-donations' ),
		'search_items'       => __( 'Search sheltered animals', 'ars-virtual-donations' ),
		'parent_item_colon'  => __( 'Parent sheltered animals:', 'ars-virtual-donations' ),
		'not_found'          => __( 'No sheltered animal found.', 'ars-virtual-donations' ),
		'not_found_in_trash' => __( 'No sheltered animal found in Trash.', 'ars-virtual-donations' )
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
		'supports'            => array( 'title', 'editor', 'author' )
	);
	register_post_type( 'ars-subscription', $args );
}
