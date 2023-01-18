<?php
/**
 * Template name: VirtualAdopt -  Donation Checkout
 */
get_header();
$post_id = 0;
if ( ! empty( $_GET['aid'] ) ) {
	$post_id = va_decode_id( $_GET['aid'] );
}
?>
	<div class="sheltered-animals-archive">
		<?php
		if ( $post_id !== 0 ) {
			include_once( VA_ABS . '/templates/parts/checkout-form.php' );
		} else {
			$animals = get_posts( [
				'post_type'        => 'sheltered-animal',
				'posts_per_page'   => 9,
				'suppress_filters' => false,
			] );
			if ( empty( $animals ) ) {
				include_once( VA_ABS . '/templates/parts/no-animals-found.php' );
			} else {
				?>
				<div class="intro-text">
					<h2>Select an animal to sponsor</h2>
					<p>It couldn't be easier to sponsor a dog in our care, or to gift a sponsorship to a friend or loved
						as little as 1.25 lv. per week* (5.00 BGN per month).</p>
				</div>
				<div class="list-of-animals">
					<?php
					$va_settings = get_option( 'va-settings' );
					$sponsor_link = get_permalink( $va_settings['page']['checkout'] );
					$adopted_animals = va_get_list_of_adopted_animals();
					foreach ( $animals as $animal ) {
						// don't show already adopted animals
						if ($adopted_animals !== [] && in_array($animal->ID, $adopted_animals)) {
							continue;
						}
						$post_id = $animal->ID;
						$age           = get_post_meta( $post_id, 'animals-age', true );
						$sheltered_for = get_post_meta( $post_id, 'sheltered-years', true );
						$animal_link   = get_the_permalink( $post_id );
						$image         = get_the_post_thumbnail_url( $post_id, 'medium' );
						$the_title     = $animal->post_title;
						include( VA_ABS . '/templates/parts/animal-card.php');
					}
					?>
				</div>
				<div class="blue-button-wrap">
					<a href="<?php echo get_post_type_archive_link( 'sheltered-animal' ) ?>" class="blue-button">
						View all animals
					</a>
				</div>
				<?php
			}
		}
		?>
	</div>
<?php
get_footer();
