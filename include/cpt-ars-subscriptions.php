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
		'supports'            => array( 'title', 'editor', 'author' )
	);
	register_post_type( 'ars-subscription', $args );
}
