<?php

if ( is_post_type_archive( 'sheltered-animal' ) ) {
	$selected = 'all';
} else {
	global $wp;
	$request_args = explode('/', $wp->request);
	$selected = end($request_args);
}
$terms = get_terms( 'kind-of-animal' );

?>
<div class="list-of-kind-of-animals">
	<h3>Select category</h3>

	<?php if ( ! empty( $terms ) ) : ?>
		<?php foreach ( $terms as $term ) : ?>

			<?php
			$image_id  = get_term_meta( $term->term_id, 'featured-image', true );
			$image_url = '';
			if ( ! empty( $image_id ) ) {
				$image_url = wp_get_attachment_image_url( $image_id, 'medium' );
			}
			?>

			<div class="kind-of-animal-logo <?php echo $selected === $term->slug ? 'selected-logo' : ''; ?>">
				<a href="<?php echo get_term_link( $term->term_id ); ?>">
					<?php if ( ! empty( $image_url ) ): ?>
						<img src="<?php echo $image_url; ?>" alt="<?php echo $term->name; ?>">
					<?php endif; ?>
					<?php echo $term->name; ?>
				</a>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<div class="kind-of-animal-logo <?php echo $selected === 'all' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo get_post_type_archive_link( 'sheltered-animal' ) ?>"
		   title="<?php _e( 'View all', 'virtual-adoption' ); ?>">
			<img src="<?php echo VA_URL; ?>/assets/images/animal-logos/all.png"
				 alt="<?php _e( 'All', 'virtual-adoption' ) ?>">
			<?php _e( 'All', 'virtual-adoption' ) ?>

		</a>
	</div>

</div>
