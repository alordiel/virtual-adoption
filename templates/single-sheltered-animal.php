<?php
get_header();
?>
<div class="shaltered-animal-container">
	<h1><?php echo the_title(); ?></h1>
	<div class="sinlge-animal-infobox"></div>
	<div class="single-animal-content">
		<?php the_content(); ?>
	</div>
</div>

<?php
get_footer();
