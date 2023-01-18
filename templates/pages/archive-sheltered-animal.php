<?php
get_header();

$ars_settings = get_option( 'ars-settings' );
$sponsor_link = get_permalink( $ars_settings['checkout-page'] );
?>
	<div class="sheltered-animals-archive">
		<div class="intro-text">
			<h2>Sponsor animals from "The Farm"</h2>
			<p>It couldn't be easier to sponsor a dog in our care, or to gift a sponsorship to a friend or loved one for
				as little as 1.25 lv. per week* (5.00 BGN per month).</p>
			<p>With around 100 animals in our care at any one time, your support helps us gives every single one the
				very best care possible. While most dogs find a loving new home within about six weeks, some dogs need a
				little of extra care and love from us. Your support for these animals can make the world of
				difference. </p>
		</div>

		<?php require_once( ARSVD_ABS . '/templates/parts/select-kind-of-animal.php' ); ?>

		<h3 class="text-center">Choose your sponsor animal</h3>

		<div class="list-of-animals">

			<?php if ( have_posts() ) :
				$adopted_animals = ars_get_list_of_adopted_animals();
				while ( have_posts() ) : the_post();
					$post_id         = get_the_ID();
					$the_title       = get_the_title();
					$age             = get_post_meta( $post_id, 'animals-age', true );
					$sheltered_for   = get_post_meta( $post_id, 'sheltered-years', true );
					$animal_link     = get_the_permalink();
					$image           = get_the_post_thumbnail_url( $post_id, 'medium' );
					include( ARSVD_ABS . '/templates/parts/animal-card.php' );
				endwhile;
			else:
				include_once( ARSVD_ABS . '/templates/parts/no-animals-found.php' );
			endif;
			?>
		</div>

		<?php include_once( ARSVD_ABS . '/templates/parts/how-it-works.php' ); ?>

	</div>
<?php
get_footer();
