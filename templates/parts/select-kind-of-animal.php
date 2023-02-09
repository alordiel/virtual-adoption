<?php
$selected = 'all';
$site_url = site_url();
$settings = get_option('va-settings');

if ( is_tax( 'kind-of-animal' )) {
	$selected = va_get_the_current_selected_kind($settings);
}

$category_link = [
	'dogs'   => !empty($settings['animal-terms']['dogs'])  ? get_term_link($settings['animal-terms']['dogs']) : '',
	'horses' => !empty($settings['animal-terms']['horses'])  ? get_term_link($settings['animal-terms']['horses']) : '',
	'cats'   => !empty($settings['animal-terms']['cats'])  ? get_term_link($settings['animal-terms']['cats']) : '',
	'other'  => !empty($settings['animal-terms']['other'])  ? get_term_link($settings['animal-terms']['other']) : '',
	'all'    => get_post_type_archive_link('sheltered-animal'),
];

?>
<div class="list-of-kind-of-animals">
	<h3><?php _e('Select category','virtual-adoptions'); ?></h3>
	<div class="kind-of-animal-logo <?php echo $selected === 'dogs' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['dogs'] ?>" title="<?php _e( 'View all dogs', 'virtual-adoption' ); ?>">
			<img src="<?php echo VA_URL; ?>/assets/images/animal-logos/dog.png"
				 alt="<?php _e( 'Dogs', 'virtual-adoption' ) ?>">
			<?php _e( 'Dogs', 'virtual-adoption' ) ?>
		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'horses' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['horses'] ?>"
		   title="<?php _e( 'View all horses', 'virtual-adoption' ); ?>">
			<img src="<?php echo VA_URL; ?>/assets/images/animal-logos/horse.png"
				 alt="<?php _e( 'Horses', 'virtual-adoption' ) ?>">
			<?php _e( 'Horses', 'virtual-adoption' ) ?>

		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'cats' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['cats'] ?>" title="<?php _e( 'View all cats', 'virtual-adoption' ); ?>">
			<img src="<?php echo VA_URL; ?>/assets/images/animal-logos/cat.png"
				 alt="<?php _e( 'Cats', 'virtual-adoption' ) ?>">
			<?php _e( 'Cats', 'virtual-adoption' ) ?>
		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'farm-animals' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['other'] ?>"
		   title="<?php _e( 'View all farm animals', 'virtual-adoption' ); ?>">
			<img src="<?php echo VA_URL; ?>/assets/images/animal-logos/farm.png"
				 alt="<?php _e( 'Other', 'virtual-adoption' ) ?>">
			<?php _e( 'Other', 'virtual-adoption' ) ?>

		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'all' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['all'] ?>"
		   title="<?php _e( 'View all animals', 'virtual-adoption' ); ?>">
			<img src="<?php echo VA_URL; ?>/assets/images/animal-logos/all.png"
				 alt="<?php _e( 'All', 'virtual-adoption' ) ?>">
			<?php _e( 'All', 'virtual-adoption' ) ?>

		</a>
	</div>

</div>
