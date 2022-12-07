<?php
$selected = 'all';

?>
<div class="list-of-kind-of-animals">
	<div class="kind-of-animal-logo <?php echo $selected === 'dog' ? 'selected-logo' : ''; ?>">
		<a>
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/dog.png" alt="<?php _e('Dogs','ars-sheltered-animals')?>">
			<?php _e('Dogs','ars-sheltered-animals')?>
		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'horses' ? 'selected-logo' : ''; ?>">
		<a>
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/horse.png" alt="<?php _e('Horses','ars-sheltered-animals')?>">
			<?php _e('Horses','ars-sheltered-animals')?>

		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'cats' ? 'selected-logo' : ''; ?>">
		<a>
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/cat.png" alt="<?php _e('Cats','ars-sheltered-animals')?>">
			<?php _e('Cats','ars-sheltered-animals')?>
		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'other' ? 'selected-logo' : ''; ?>">
		<a>
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/farm.png" alt="<?php _e('Other','ars-sheltered-animals')?>">
			<?php _e('Other','ars-sheltered-animals')?>

		</a>
	</div>
	<div class="kind-of-animal-logo <?php echo $selected === 'all' ? 'selected-logo' : ''; ?>">
		<a>
			<img src="<?php echo ARSVD_URL; ?>/assets/images/animal-logos/all.png" alt="<?php _e('All','ars-sheltered-animals')?>">
			<?php _e('All','ars-sheltered-animals')?>

		</a>
	</div>

</div>
