<?php

function va_subscriptions_plans_post_type() {
	$labels = array(
		'name'               => _x( 'Subscription plans', 'post type general name', 'virtual-donations' ),
		'singular_name'      => _x( 'Subscription plan', 'post type singular name', 'virtual-donations' ),
		'menu_name'          => _x( 'Subscription plans', 'admin menu', 'virtual-donations' ),
		'name_admin_bar'     => _x( 'Subscription plan', 'add new on admin bar', 'virtual-donations' ),
		'add_new'            => _x( 'Add New Subscription plan', 'Subscription plan', 'virtual-donations' ),
		'add_new_item'       => __( 'Add New Subscription plan', 'virtual-donations' ),
		'new_item'           => __( 'New subscription plan', 'virtual-donations' ),
		'edit_item'          => __( 'Edit subscription plan', 'virtual-donations' ),
		'view_item'          => __( 'View subscription plan', 'virtual-donations' ),
		'all_items'          => __( 'All subscription plans', 'virtual-donations' ),
		'search_items'       => __( 'Search subscription plans', 'virtual-donations' ),
		'parent_item_colon'  => __( 'Parent subscription plans:', 'virtual-donations' ),
		'not_found'          => __( 'No subscription plan found.', 'virtual-donations' ),
		'not_found_in_trash' => __( 'No subscription plan found in Trash.', 'virtual-donations' )
	);

	$args = array(
		'labels'              => $labels,
		'description'         => __( 'Description.', 'virtual-donations' ),
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_nav_menus'   => false,
		'show_in_menu'        => true,
		'query_var'           => false,
		'rewrite'             => array( 'slug' => 'va-subscription-plan' ),
		'capability_type'     => 'post',
		'exclude_from_search' => true,
		'has_archive'         => false,
		'hierarchical'        => false,
		'menu_position'       => null,
		'supports'            => array( 'title' )
	);
	register_post_type( 'va-subscription-plan', $args );
}

add_action( 'init', 'va_subscriptions_plans_post_type' );

/**
 * Register meta box for subscription's details. There is no metadata that is being saved with this meta box.
 */
function va_meta_info_about_subscription_plans() {
	add_meta_box(
		'subscription-plan',
		__( 'Subscription plan details', 'virtual-adoption' ),
		'va_subscription_plan_details',
		'va-subscription-plan',
		'normal'
	);
}

add_action( 'add_meta_boxes', 'va_meta_info_about_subscription_plans' );

/**
 * Function to display the details of the subscription in the admin
 *
 * @param WP_Post $post
 *
 * @return void
 */
function va_subscription_plan_details( WP_Post $post ) {
	$plan_meta = get_post_meta( $post->ID );

	$plan_costs        = ! empty( $plan_meta['cost'] ) ? (float) $plan_meta['cost'][0] : '';
	$currency          = ! empty( $plan_meta['currency'] ) ? $plan_meta['currency'][0] : '';
	$paypal_product_id = ! empty( $plan_meta['paypal_product_id'] ) ? $plan_meta['paypal_product_id'][0] : '';
	$paypal_plan_id    = ! empty( $plan_meta['paypal_plan_id'] ) ? $plan_meta['paypal_plan_id'][0] : '';
	?>
	<div>
		<p>
			<label><?php _e( 'Plan cost', 'virtual-adoption' ) ?>:
				<input type="number" value="<?php echo $plan_costs; ?>" name="plan-cost">
			</label> <br>
			<label><?php _e( 'Currency', 'virtual-adoption' ) ?>:
				<select name="plan-currency">
					<option><?php _e( '-- Select currency --', 'virtual-adoption' ) ?></option>
					<option <?php echo $currency === 'eur' ? 'selected' : '' ?> value="eur">EUR</option>
					<option <?php echo $currency === 'usd' ? 'selected' : '' ?> value="usd">USD</option>
					<option <?php echo $currency === 'gbp' ? 'selected' : '' ?> value="gbp">GBP</option>
				</select>
			</label>
		</p>
		<br>
		<p>
			<label><?php _e( 'PayPal product ID', 'virtual-adoption' ); ?>:<br>
				<input type="text" readonly value="<?php echo $paypal_product_id; ?>">
			</label>
		</p>
		<p>
			<label><?php _e( 'PayPal subscription plan ID', 'virtual-adoption' ); ?>:<br>
				<input type="text" readonly value="<?php echo $paypal_plan_id; ?>">
			</label>
		</p>
	</div>
	<?php wp_nonce_field( 'va-subscription-plan-meta', 'va-power-dog' ); ?>
	<?php
}


function va_save_subscription_plan_meta( int $post_id, WP_Post $post ) {
	$nonce_name = $_POST['va-power-dog'] ?? '';
	if ( ! wp_verify_nonce( $nonce_name, 'va-subscription-plan-meta' ) ) {
		return;
	}

	if ( ! empty( $_POST['plan-currency'] ) && in_array( $_POST['plan-currency'], [ 'gbp', 'eur', 'usd' ] ) ) {
		update_post_meta( $post_id, 'currency', $_POST['plan-currency'] );
	} else {
		delete_post_meta( $post_id, 'currency' );
	}

	if ( ! empty( $_POST['plan-cost'] ) ) {
		update_post_meta( $post_id, 'cost', (float) $_POST['plan-cost'] );
	} else {
		delete_post_meta( $post_id, 'cost' );
	}
}

add_action( 'save_post_va-subscription-plan', 'va_save_subscription_plan_meta', 10, 2 );
