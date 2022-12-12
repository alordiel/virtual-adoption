<?php

add_action( 'admin_menu', 'ee_add_settings_page' );
function ee_add_settings_page() {
	add_submenu_page( 'options-general.php',
		__( 'ARS Settings', 'ears-virtual-donations' ),
		__( 'ARS Settings', 'ears-virtual-donations' ),
		'manage_options',
		'ars_settings',
		'ars_admin_settings_page' );
}

function ars_admin_settings_page() {
	?>

	<h1><?php _e( 'ARS sheltered animals settings', 'ars-virtual-donations' ) ?></h1>

	<?php

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'ears-virtual-donations' ) );
	}

	$ars_settings = get_option( 'ars-settings' );

	if ( isset( $_POST['dogs-term-id'] ) ) {
		if ( empty( $ars_settings ) ) {
			$ars_settings = [ 'animal-terms' ];
		}

		$ars_settings['animal-terms']['dogs']   = (int) $_POST['dogs-term-id'];
		$ars_settings['animal-terms']['cats']   = (int) $_POST['cats-term-id'];
		$ars_settings['animal-terms']['horses'] = (int) $_POST['horses-term-id'];
		$ars_settings['animal-terms']['other']  = (int) $_POST['farm-animals-term-id'];

		update_option( 'ars-settings', $ars_settings );
		echo '<div class="updated"><p><strong>' . __( 'Settings saved.', 'ears-virtual-donations' ) . '</strong></p></div>';
	}

	$terms = get_terms( [
		'taxonomy'   => 'kind-of-animal',
		'hide_empty' => false,
	] );

	$dogs_tax_id   = ! empty( $ars_settings['animal-terms']['dogs'] ) ? (int) $ars_settings['animal-terms']['dogs'] : 0;
	$cats_tax_id   = ! empty( $ars_settings['animal-terms']['cats'] ) ? (int) $ars_settings['animal-terms']['cats'] : 0;
	$horses_tax_id = ! empty( $ars_settings['animal-terms']['horses'] ) ? (int) $ars_settings['animal-terms']['horses'] : 0;
	$other_tax_id  = ! empty( $ars_settings['animal-terms']['other'] ) ? (int) $ars_settings['animal-terms']['other'] : 0;

	$dogs_options   = ars_get_selected_options_for_the_admin_settings( $dogs_tax_id, $terms );
	$cats_options   = ars_get_selected_options_for_the_admin_settings( $cats_tax_id, $terms );
	$horses_options = ars_get_selected_options_for_the_admin_settings( $horses_tax_id, $terms );
	$farm_options   = ars_get_selected_options_for_the_admin_settings( $other_tax_id, $terms );


	?>
	<form name="form1" method="post" action="">
		<p><?php _e( 'Set the categories for the animals', 'ars-virtual-donations' ) ?></p>
		<table class="form-table">
			<tbody>
			<tr>
				<th>
					<label for="dogs-term-id">
						<?php _e( "Dogs Category", "ars-virtual-donations" ); ?>
					</label>
				</th>
				<td>
					<select name="dogs-term-id" id="dogs-term-id">
						<?php echo $dogs_options ?>
					</select>
				</td>
			</tr>

			<tr class="form-field">
				<th>
					<label for="cats-term-id">
						<?php _e( "Cats Category", "ars-virtual-donations" ); ?>
					</label>
				</th>
				<td>
					<select name="cats-term-id" id="cats-term-id">
						<?php echo $cats_options ?>
					</select>
				</td>
			</tr>

			<tr class="form-field">
				<th>
					<label for="horses-term-id">
						<?php _e( "Horses Category", "ars-virtual-donations" ); ?>
					</label>
				</th>
				<td>
					<select name="horses-term-id" id="horses-term-id">
						<?php echo $horses_options ?>
					</select>
				</td>
			</tr>

			<tr class="form-field">
				<th>
					<label
						for="farm-animals-term-id">
						<?php _e( "Farm animals Category", "ars-virtual-donations" ); ?>
					</label>
				</th>
				<td>
					<select name="farm-animals-term-id" id="farm-animals-term-id">
						<?php echo $farm_options ?>
					</select>
				</td>
			</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" name="Submit" class="button-primary"
				   value="<?php esc_attr_e( 'Save Changes', 'ars-virtual-donations' ) ?>"/>
		</p>
	</form>

	<?php
}


function ars_get_selected_options_for_the_admin_settings( $search_term_id, $terms ): string {
	// build the select options for the categories
	$options = "<option value='0'>- select -</option>";
	foreach ( $terms as $term ) {
		$selected = ( $search_term_id !== 0 && $search_term_id === $term->term_id ) ? 'selected="selected"' : '';
		$options  .= "<option $selected value='$term->term_id'>$term->name</option>";
	}

	return $options;
}
