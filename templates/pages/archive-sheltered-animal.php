<?php
get_header();

$va_settings  = get_option( 'va-settings' );
$sponsor_link = get_permalink( $va_settings['page']['checkout'] );
?>
	<div class="va-container">
		<div class="sheltered-animals-archive">
			<div class="intro-text">
				<h2><?php _e( 'Sponsor animals from "The Farm"', 'virtual-adoptions' ) ?></h2>
				<p><?php _e( 'You could sponsor an animal in our care, or to gift a sponsorship to a friend or loved one for
				as little as 5.00 EUR per month.', 'virtual-adoptions' ) ?></p>
				<p><?php _e( 'With more than 120 animals in our care at any one time, your support helps us gives every single one the
				very best care possible. While most of them find a loving new home within about six weeks, some need a
				lot of care and love from us, as no one want to adopt them. Your support for these animals can make the world of
				difference. ', 'virtual-adoptions' ) ?></p>
			</div>

			<?php
			if ( ! empty( $va_settings['general']['enable-categories'] ) && $va_settings['general']['enable-categories'] === 'on' ) {
				require_once( VA_ABS . '/templates/parts/select-kind-of-animal.php' );
			}
			?>

			<h3 class="text-center"><?php _e( 'Choose your sponsor animal', 'virtual-adoptions' ) ?></h3>

			<div class="list-of-animals">

				<?php if ( have_posts() ) :
					$adopted_animals = va_get_list_of_adopted_animals();
					while ( have_posts() ) : the_post();
						$post_id       = get_the_ID();
						$the_title     = get_the_title();
						$age           = get_post_meta( $post_id, 'animals-age', true );
						$sheltered_for = get_post_meta( $post_id, 'sheltered-years', true );
						$animal_link   = get_the_permalink();
						$image         = get_the_post_thumbnail_url( $post_id, 'medium' );
						include( VA_ABS . '/templates/parts/animal-card.php' );
					endwhile;
					the_posts_pagination( array(
						'mid_size'  => 2,
						'prev_text' => __( 'Previous Page', 'virtual-adoptions' ),
						'next_text' => __( 'Next Page', 'virtual-adoptions' ),
					) );
				else:
					include_once( VA_ABS . '/templates/parts/no-animals-found.php' );
				endif;
				?>
			</div>

			<?php include_once( VA_ABS . '/templates/parts/how-it-works.php' ); ?>

		</div>
	</div>
<?php
get_footer();
