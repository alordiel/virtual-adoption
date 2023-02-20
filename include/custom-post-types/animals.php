<?php
/**
 * This file creates the post type for the sheltered animals
 * It includes the taxonomies and tags for them as well
 */

//Creating a post type for sheltered animals
add_action( 'init', 'va_sheltered_animals' );
function va_sheltered_animals() {
	$labels = array(
		'name'               => _x( 'Sheltered animals', 'post type general name', 'virtual-adoptions' ),
		'singular_name'      => _x( 'Sheltered animal', 'post type singular name', 'virtual-adoptions' ),
		'menu_name'          => _x( 'Sheltered animals', 'admin menu', 'virtual-adoptions' ),
		'name_admin_bar'     => _x( 'Sheltered animal', 'add new on admin bar', 'virtual-adoptions' ),
		'add_new'            => _x( 'Add New', 'Sheltered animal', 'virtual-adoptions' ),
		'add_new_item'       => __( 'Add New', 'virtual-adoptions' ),
		'new_item'           => __( 'New sheltered animal', 'virtual-adoptions' ),
		'edit_item'          => __( 'Edit sheltered animal', 'virtual-adoptions' ),
		'view_item'          => __( 'View sheltered animal', 'virtual-adoptions' ),
		'all_items'          => __( 'All sheltered animals', 'virtual-adoptions' ),
		'search_items'       => __( 'Search sheltered animals', 'virtual-adoptions' ),
		'parent_item_colon'  => __( 'Parent sheltered animals:', 'virtual-adoptions' ),
		'not_found'          => __( 'No sheltered animal found.', 'virtual-adoptions' ),
		'not_found_in_trash' => __( 'No sheltered animal found in Trash.', 'virtual-adoptions' )
	);

	$args = array(
		'labels'              => $labels,
		'description'         => __( 'Description.', 'virtual-adoptions' ),
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
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'page-attributes' )
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
		__( 'Animal\'s details', 'virtual-adoptions' ),
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
	$sheltered_for = get_post_meta( $post->ID, 'sheltered-years', true );
	$sex           = get_post_meta( $post->ID, 'animals-sex', true );
	?>
	<p>
		<label>
			<?php _e( 'Age of the animal (years)', 'virtual-adoptions' ); ?>
			<input type="text" value="<?php echo $age; ?>" name="animals-age">
		</label>
	</p>
	<p>
		<label>
			<?php _e( 'Years spent in the shelter', 'virtual-adoptions' ); ?>
			<input type="text" name="sheltered-years" value="<?php echo $sheltered_for ?>">
		</label>
	</p>
	<p>
		<label>
			<?php _e( 'Sex of the animal', 'virtual-adoptions' ); ?> <br>
			<select name="animals-sex">
				<option value=""></option>
				<option value="male" <?php echo $sex === 'male' ? 'selected="selected"' : ''; ?>>
					<?php _e( 'Male', 'virtual-adoptions' ) ?>
				</option>
				<option value="female" <?php echo $sex === 'female' ? 'selected="selected"' : ''; ?>>
					<?php _e( 'Female', 'virtual-adoptions' ) ?>
				</option>
			</select>
		</label>
	</p>
	<?php
	wp_nonce_field( 'va-shelter-animal-meta', 'va-power-dog' );
}

/**
 * Save meta boxes content for the sheltered animals.
 *
 * @param int $post_id Post ID
 */
function va_sheltered_animal_save_meta( int $post_id ) {

	$nonce_name = $_POST['va-power-dog'] ?? '';
	if ( ! wp_verify_nonce( $nonce_name, 'va-shelter-animal-meta' ) ) {
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

add_action( 'save_post_sheltered-animal', 'va_sheltered_animal_save_meta' );


/**
 * Register a field for uploading a featured image for the taxonomy
 *
 * @param $term
 *
 * @return void
 */
function va_kind_of_animal_edit_field( $term ) {
	$image_id  = get_term_meta( $term->term_id, 'featured-image', true );
	$image_url = '';
	if ( ! empty( $image_id ) ) {
		$image_url = wp_get_attachment_image_url( $image_id, 'medium' );
	}
	?>
	<tr class="form-field">
		<th scope="row">
			<label for="featured-image-id">
				<?php _e( 'Image', 'virtual-adoptions' ); ?>
				<input id="featured-image-id" name="featured-image-id" type="hidden" value="<?php echo $image_id; ?>">
			</label>
		</th>
		<td>
			<div style="display: flex;align-items: center;">
				<div style="display: flex;flex-direction: column;text-align: center;">
					<a id="featured-image-button" href="#" class="button button-primary" style="margin-bottom: 10px">
						<?php _e( 'Upload image', 'virtual-adoptions' ); ?>
					</a>
					<a id="remove-image-button" href="#" class="button button-secondary"
					   style="display:<?php echo empty( $image_url ) ? 'none' : 'block'; ?>">
						<?php _e( 'Remove image', 'virtual-adoptions' ); ?>
					</a>
				</div>
				<div style="margin-left: 20px">
					<img src="<?php echo $image_url; ?>" width="230" alt="featured-image-for-kind-of-animal"
						 id="featured-image-block"
						 style="display: <?php echo ! empty( $image_url ) ? 'block' : 'none'; ?>">
				</div>
			</div>
		</td>
	</tr>
	<?php
}

add_action( 'kind-of-animal_edit_form_fields', 'va_kind_of_animal_edit_field', 10, 2 );

/**
 * Saves the ID of the featured image (or delete it if it was removed)
 *
 * @param int $term_id
 *
 * @return void
 */
function va_save_kind_of_animal_featured_image( int $term_id ) {
	if ( isset( $_POST['featured-image-id'] ) ) {
		update_term_meta( $term_id, 'featured-image', (int) $_POST['featured-image-id'] );
	} else {
		delete_term_meta( $term_id, 'featured-image' );
	}
}

add_action( 'edited_kind-of-animal', 'va_save_kind_of_animal_featured_image', 10, 2 );
