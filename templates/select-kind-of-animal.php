<?php
$selected = 'all';
$site_url = site_url();

if ( is_tax( 'kind-of-animal' )) {
	$selected = ars_get_the_current_selected_kind();
}
$settings = get_option('ars-settings');

$category_link = [
	'dogs'   => !empty($settings['animal-terms']['dogs'])  ? get_term_link($settings['animal-terms']['dogs']) : '',
	'horses' => !empty($settings['animal-terms']['horses'])  ? get_term_link($settings['animal-terms']['horses']) : '',
	'cats'   => !empty($settings['animal-terms']['cats'])  ? get_term_link($settings['animal-terms']['cats']) : '',
	'other'  => !empty($settings['animal-terms']['other'])  ? get_term_link($settings['animal-terms']['other']) : '',
	'all'    => get_post_type_archive_link('sheltered-animal'),
];

?>
<div class="list-of-kind-of-animals">
	<h3>Select category</h3>
	<div class="kind-of-animal-logo <?php echo $selected === 'dogs' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['dogs'] ?>" title="<?php _e( 'View all dogs', 'ars-sheltered-animals' ); ?>">
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/dog.png"
				 alt="<?php _e( 'Dogs', 'ars-sheltered-animals' ) ?>">
			<?php _e( 'Dogs', 'ars-sheltered-animals' ) ?>
		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'horses' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['horses'] ?>"
		   title="<?php _e( 'View all horses', 'ars-sheltered-animals' ); ?>">
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/horse.png"
				 alt="<?php _e( 'Horses', 'ars-sheltered-animals' ) ?>">
			<?php _e( 'Horses', 'ars-sheltered-animals' ) ?>

		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'cats' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['cats'] ?>" title="<?php _e( 'View all cats', 'ars-sheltered-animals' ); ?>">
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/cat.png"
				 alt="<?php _e( 'Cats', 'ars-sheltered-animals' ) ?>">
			<?php _e( 'Cats', 'ars-sheltered-animals' ) ?>
		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'other' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['other'] ?>"
		   title="<?php _e( 'View all farm animals', 'ars-sheltered-animals' ); ?>">
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/farm.png"
				 alt="<?php _e( 'Other', 'ars-sheltered-animals' ) ?>">
			<?php _e( 'Other', 'ars-sheltered-animals' ) ?>

		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'all' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['all'] ?>"
		   title="<?php _e( 'View all animals', 'ars-sheltered-animals' ); ?>">
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/all.png"
				 alt="<?php _e( 'All', 'ars-sheltered-animals' ) ?>">
			<?php _e( 'All', 'ars-sheltered-animals' ) ?>

		</a>
	</div>

</div>
