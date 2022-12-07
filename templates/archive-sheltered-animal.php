<?php
get_header();
?>
	<div class="sheltered-animals-archive">
		<div class="intro-text">
			<h2>Sponsor animals from "The Farm"</h2>
			<p>It couldn't be easier to sponsor a dog in our care, or to gift a sponsorship to a friend or loved one for
				as little as 1.25 lv. per week* (5.00 BGN per month).</p>
			<p>With around 100 animals in our care at any one time, your support helps us gives every single one the
				very best care possible. While most dogs find a loving new home within about six weeks, some dogs need a
				little bit of extra care and love from us. Your support for these animals can make the world of
				difference. </p>
			<h3>Choose your sponsor animal</h3>
		</div>

		<div class="list-of-animals">

			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					$post_id       = get_the_ID();
					$age           = (int) get_post_meta( $post_id, 'animals-age', true );
					$sheltered_for = (int) get_post_meta( $post_id, 'sheltered-years', true );
					?>
					<div class="animal-card" style="width: 18rem;">
						<?php
						the_post_thumbnail( 'large', [
							'class' => 'animal-card-image',
							'title' => get_the_title(),
						] );
						?>
						<div class="wave-cover">
							<div style="transform: rotate(0deg);">
								<svg viewBox="0 0 1920 230" xmlns="http://www.w3.org/2000/svg">
									<path
										d="M1920 0c-50.119 6.74-112.734 24.88-187.373 56.686-421.528 179.505-624.385 250.33-1571.311 42.354C107.818 87.295 53.9 78.197 0 71.457V300h1920V0Z"
										fill="#FFFFFF" fill-rule="evenodd"></path>
								</svg>
							</div>
						</div>
						<div class="animal-card-body">
							<h5 class="animal-card-title"><?php echo get_the_title(); ?></h5>
							<div class="animal-card-text">
								<div class="animal-additional-info">
									<svg viewBox="0 0 30 30" height="30px" width="30px"
										 xmlns="http://www.w3.org/2000/svg">
										<path
											d="M7.571 5.625A1.86 1.86 0 015.714 3.75c0-1.816 1.857-1.348 1.857-3.75.697 0 1.858 1.729 1.858 3.281 0 1.553-.827 2.344-1.858 2.344zm7.429 0a1.86 1.86 0 01-1.857-1.875C13.143 1.934 15 2.402 15 0c.696 0 1.857 1.729 1.857 3.281 0 1.553-.827 2.344-1.857 2.344zm7.429 0A1.86 1.86 0 0120.57 3.75c0-1.816 1.858-1.348 1.858-3.75.696 0 1.857 1.729 1.857 3.281 0 1.553-.827 2.344-1.857 2.344zM25.214 15h-1.857V6.562H21.5V15h-5.571V6.562H14.07V15H8.5V6.562H6.643V15H4.786C3.248 15 2 16.26 2 17.813V30h26V17.812C28 16.26 26.752 15 25.214 15zm.929 13.125H3.857v-4.221C4.8 23.348 5.246 22.5 6.338 22.5c1.622 0 1.814 1.875 4.338 1.875 2.487 0 2.74-1.875 4.324-1.875 1.634 0 1.81 1.875 4.338 1.875 2.516 0 2.714-1.875 4.338-1.875 1.073 0 1.523.848 2.467 1.404v4.221zm0-6.584c-.559-.462-1.231-.916-2.467-.916-2.52 0-2.717 1.875-4.338 1.875-1.607 0-1.825-1.875-4.338-1.875-2.486 0-2.74 1.875-4.324 1.875-1.633 0-1.81-1.875-4.338-1.875-1.245 0-1.92.456-2.48.917v-3.73c0-.516.416-.937.928-.937h20.428c.512 0 .929.42.929.938v3.728z"
											fill="#222222" fill-rule="nonzero"></path>
									</svg>
									<?php _e( 'Age', 'ars-sheltered-animals' ) ?>: <?php echo $age; ?>
								</div>
								<div class="animal-additional-info">
									<svg viewBox="0 0 30 30" height="30px" width="30px"
										 xmlns="http://www.w3.org/2000/svg">
										<path
											d="M28.754 14.815l-2.847-2.502V5.342a.78.78 0 00-.779-.782h-4.67a.78.78 0 00-.778.782v1.501l-4.158-3.65a.776.776 0 00-1.024 0L1.266 14.815a.784.784 0 00-.073 1.103.776.776 0 001.098.074l1.822-1.603v11.267A2.342 2.342 0 006.448 28h17.124a2.342 2.342 0 002.335-2.344V14.389l1.822 1.601a.776.776 0 001.078-.09.783.783 0 00-.053-1.085zM24.35 25.656a.78.78 0 01-.778.781H6.448a.78.78 0 01-.778-.781V13.022l9.34-8.204 9.34 8.204v12.634zm0-14.71L21.237 8.21V6.123h3.113v4.822zm-13.232 2.73v5.209c.003.718.582 1.3 1.297 1.302h5.188a1.304 1.304 0 001.299-1.302v-5.21a1.304 1.304 0 00-1.3-1.301h-5.187a1.303 1.303 0 00-1.297 1.302zm1.557.26h4.67v4.688h-4.67v-4.688z"
											stroke="#000" stroke-width="0.5" fill="#222222" fill-rule="nonzero"></path>
									</svg>
									<?php _e( 'Years in the shelter', 'ars-sheltered-animals' ) ?>
									: <?php echo $sheltered_for ?><br>
								</div>
							</div>
							<div class="sponsor-me-block">
								<button type="button" class="sponsor-me">Sponsor me</button>
							</div>
						</div>
					</div>
				<?php endwhile; ?>
			<?php else: ?>
				<div> No animals were found.</div>
			<?php endif; ?>
		</div>

		<?php include_once( 'how-it-works.php' ); ?>

	</div>
<?php
get_footer();
