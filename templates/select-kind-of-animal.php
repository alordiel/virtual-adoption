<?php
$selected      = 'all';
$site_url      = site_url();
$category_link = [
	'dogs' => $site_url . '/kind-of-animal/dogs',
	'horses' => $site_url . '/kind-of-animal/horses',
	'cats' => $site_url . '/kind-of-animal/cats',
	'other' => $site_url . '/kind-of-animal/farm-animals',
	'all' => $site_url . '/kind-of-animal/',
];

?>
<div class="list-of-kind-of-animals">
	<h3>Select category</h3>
	<div class="kind-of-animal-logo <?php echo $selected === 'dog' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['dogs'] ?>" title="<?php _e( 'View all dogs', 'ars-sheltered-animals' ); ?>">
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/dog.png"
				 alt="<?php _e( 'Dogs', 'ars-sheltered-animals' ) ?>">
			<?php _e( 'Dogs', 'ars-sheltered-animals' ) ?>
		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'horses' ? 'selected-logo' : ''; ?>">
		<a href="<?php echo $category_link['horses'] ?>" title="<?php _e( 'View all horses', 'ars-sheltered-animals' ); ?>">
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
		<a href="<?php echo $category_link['all'] ?>" title="<?php _e( 'View all animals', 'ars-sheltered-animals' ); ?>">
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/all.png"
				 alt="<?php _e( 'All', 'ars-sheltered-animals' ) ?>">
			<?php _e( 'All', 'ars-sheltered-animals' ) ?>

		</a>
	</div>

</div>
