<?php
/**
 * Template name: ARS Donation Checkout
 */
get_header();
$post_id = 0;
if ( ! empty( $_GET['aid'] ) ) {
	$post_id = ars_decode_id( $_GET['aid'] );
}
?>
	<div class="sheltered-animals-archive">
		<?php
		if ( $post_id !== 0 ) {
			include_once 'checkout-form.php';
		} else {
			$animals = get_posts( [
				'post_type'        => 'sheltered-animal',
				'posts_per_page'   => 9,
				'suppress_filters' => false,
			] );
			if ( empty( $animals ) ) {
				include_once 'no-animals-found.php';
			} else {
				?>
				<div class="intro-tex">
					<h2>Select an animal to sponsor</h2>
					<p>It couldn't be easier to sponsor a dog in our care, or to gift a sponsorship to a friend or loved
						as little as 1.25 lv. per week* (5.00 BGN per month).</p>
				</div>
				<div class="list-of-animals">
					<?php
					$ars_settings = get_option( 'ars-settings' );
					$sponsor_link = get_permalink( $ars_settings['checkout-page'] );
					foreach ( $animals as $animal ) {
						$post_id = $animal->ID;
						dbga( $post_id );
						$age           = get_post_meta( $post_id, 'animals-age', true );
						$sheltered_for = get_post_meta( $post_id, 'sheltered-years', true );
						$animal_link   = get_the_permalink( $post_id );
						$image         = get_the_post_thumbnail_url( $post_id, 'medium' );
						$the_title     = $animal->post_title;
						include 'animal-card.php';
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
