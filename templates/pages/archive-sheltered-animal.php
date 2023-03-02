<?php
get_header();

$va_settings  = get_option( 'va-settings' );
$sponsor_link = get_permalink( $va_settings['page']['checkout'] );
?>
	<div class="va-container">
		<div class="sheltered-animals-archive">
			<div class="intro-text">
				<?php
				if ( ! empty( $va_settings['page']['intro'] ) ) {
					$intro_post = get_post( $va_settings['page']['intro'] );
					echo apply_filters( 'the_content', $intro_post->post_content );
				};
				?>
			</div>

			<?php
			if ( ! empty( $va_settings['general']['enable-categories'] ) && $va_settings['general']['enable-categories'] === 'on' ) {
				require_once( VA_ABS . '/templates/parts/select-kind-of-animal.php' );
			}
			?>

			<?php if ( is_tax( 'kind-of-animal' ) ) { ?>
				<div class="va-term-description"> <?php echo term_description(); ?> </div>
			<?php } ?>

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
				else:
					include_once( VA_ABS . '/templates/parts/no-animals-found.php' );
				endif;
				?>
			</div>
			<div class="page-navigation">
				<?php
				the_posts_pagination( array(
					'mid_size'  => 2,
					'prev_text' => __( 'Previous Page', 'virtual-adoptions' ),
					'next_text' => __( 'Next Page', 'virtual-adoptions' ),
				) );
				?>
			</div>
			<?php include_once( VA_ABS . '/templates/parts/how-it-works.php' ); ?>

		</div>
	</div>
<?php
get_footer();
