<?php
/**
 * This file creates the post type for the sheltered animals
 * It includes the taxonomies and tags for them as well
 */

//Creating a post type for sheltered animals
add_action( 'init', 'va_sheltered_animals' );
function va_sheltered_animals() {
	$labels = array(
		'name'               => _x( 'Sheltered animals', 'post type general name', 'virtual-adoption' ),
		'singular_name'      => _x( 'Sheltered animal', 'post type singular name', 'virtual-adoption' ),
		'menu_name'          => _x( 'Sheltered animals', 'admin menu', 'virtual-adoption' ),
		'name_admin_bar'     => _x( 'Sheltered animal', 'add new on admin bar', 'virtual-adoption' ),
		'add_new'            => _x( 'Add New', 'Sheltered animal', 'virtual-adoption' ),
		'add_new_item'       => __( 'Add New', 'virtual-adoption' ),
		'new_item'           => __( 'New sheltered animal', 'virtual-adoption' ),
		'edit_item'          => __( 'Edit sheltered animal', 'virtual-adoption' ),
		'view_item'          => __( 'View sheltered animal', 'virtual-adoption' ),
		'all_items'          => __( 'All sheltered animals', 'virtual-adoption' ),
		'search_items'       => __( 'Search sheltered animals', 'virtual-adoption' ),
		'parent_item_colon'  => __( 'Parent sheltered animals:', 'virtual-adoption' ),
		'not_found'          => __( 'No sheltered animal found.', 'virtual-adoption' ),
		'not_found_in_trash' => __( 'No sheltered animal found in Trash.', 'virtual-adoption' )
	);

	$args = array(
		'labels'              => $labels,
		'description'         => __( 'Description.', 'virtual-adoption' ),
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_nav_menus'   => true,
		'show_in_menu'        => true,
		'query_var'           => true,
		'rewrite'             => array( 'slug' => 'sheltered-animal' ),
		'capability_type'     => 'post',
		'exclude_from_search' => false,
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


/**
 * Register meta box(es).
 */
function va_register_meta_boxes() {
	add_meta_box(
		'sheltered-animal-meta',
		__( 'Animal\'s details', 'virtual-adoption' ),
		'va_sheltered_animal_details',
		'sheltered-animal',
		'side'
	);
}

add_action( 'add_meta_boxes', 'va_register_meta_boxes' );

/**
 * Meta box display callback.
 *
 * @param WP_Post $post Current post object.
 */
function va_sheltered_animal_details( WP_Post $post ) {
	$age           = (int) get_post_meta( $post->ID, 'animals-age', true );
	$sheltered_for = (int) get_post_meta( $post->ID, 'sheltered-years', true );
	$sex           = get_post_meta( $post->ID, 'animals-sex', true );
	?>
	<p>
		<label>
			<?php _e( 'Age of the animal (years)', 'virtual-adoption' ); ?>
			<input type="text" value="<?php echo $age; ?>" name="animals-age">
		</label>
	</p>
	<p>
		<label>
			<?php _e( 'Years spent in the shelter', 'virtual-adoption' ); ?>
			<input type="text" name="sheltered-years" value="<?php echo $sheltered_for ?>">
		</label>
	</p>
	<p>
		<label>
			<?php _e( 'Sex of the animal', 'virtual-adoption' ); ?> <br>
			<select name="animals-sex">
				<option value=""></option>
				<option value="male" <?php echo $sex === 'male' ? 'selected="selected"' : ''; ?>>
					<?php _e( 'Male', 'virtual-adoption' ) ?>
				</option>
				<option value="female" <?php echo $sex === 'female' ? 'selected="selected"' : ''; ?>>
					<?php _e( 'Female', 'virtual-adoption' ) ?>
				</option>
			</select>
		</label>
	</p>
	<?php
	wp_nonce_field( 'ars-shelter-animal-meta', 'ars-power-dog' );
}

/**
 * Save meta boxes content for the sheltered animals.
 *
 * @param int $post_id Post ID
 */
function va_sheltered_animal_save_meta( int $post_id ) {

	$nonce_name = $_POST['ars-power-dog'] ?? '';
	if ( ! wp_verify_nonce( $nonce_name, 'ars-shelter-animal-meta' ) ) {
		return;
	}

	if ( ! empty( $_POST['animals-sex'] ) && in_array( $_POST['animals-sex'], [ 'male', 'female' ] ) ) {
		update_post_meta( $post_id, 'animals-sex', $_POST['animals-sex'] );
	} else {
		delete_post_meta( $post_id, 'animals-sex' );
	}

	if ( ! empty( $_POST['animals-age'] ) ) {
		update_post_meta( $post_id, 'animals-age', $_POST['animals-age'] );
	} else {
		delete_post_meta( $post_id, 'animals-age' );
	}

	if ( ! empty( $_POST['sheltered-years'] ) ) {
		update_post_meta( $post_id, 'sheltered-years', $_POST['sheltered-years'] );
	} else {
		delete_post_meta( $post_id, 'sheltered-years' );
	}

}

add_action( 'save_post', 'va_sheltered_animal_save_meta' );
