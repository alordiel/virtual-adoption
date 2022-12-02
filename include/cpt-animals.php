<?php
/**
 * This file creates the post type for the sheltered animals
 * It includes the taxonomies and tags for them as well
 */

//Creating a post type for sheltered animals
add_action( 'init', 'ars_sheltered_animals' );
function ars_sheltered_animals() {
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
    'public'              => true,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'show_in_nav_menus'   => true,
    'show_in_menu'        => true,
    'query_var'           => true,
    'rewrite'             => array( 'slug' => 'sheltered-animal' ),
    'capability_type'     => 'post',
    'exclude_from_search' => true,
    'has_archive'         => true,
    'hierarchical'        => false,
    'menu_position'       => null,
    'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' )
  );
  register_post_type( 'sheltered-animal', $args );
}

add_action( 'init', 'sheltered_animal_taxonomy' );
function sheltered_animal_taxonomy() {
  register_taxonomy(
    'kind-of-animal',
    'sheltered-animal',
    array(
      'hierarchical'      => true,
      'public'            => true,
      'label'             => 'Kind of animal',
      'query_var'         => true,
      'rewrite'           => array( 'slug' => 'kind-of-animal', 'hierarchical' => true ),
      'show_ui'           => true,
      'show_admin_column' => true
    )
  );

  register_taxonomy(
    'animal-tags',
    'sheltered-animal',
    array(
      'hierarchical'      => false,
      'public'            => false,
      'query_var'         => true,
      'label'             => 'Tags',
      'rewrite'           => array( 'slug' => 'animal-tags', 'hierarchical' => false ),
      'show_ui'           => true,
      'show_admin_column' => true
    )
  );
}
