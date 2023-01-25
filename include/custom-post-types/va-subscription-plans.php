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
function va_meta_info_about_subscription() {
	add_meta_box(
		'subscription-plan',
		__( 'Subscription plan details', 'virtual-adoption' ),
		'va_subscription_plan_details',
		'va-subscription-plan',
		'normal'
	);
}

add_action( 'add_meta_boxes', 'va_meta_info_about_subscription' );

/**
 * Function to display the details of the subscription in the admin
 *
 * @param WP_Post $post
 *
 * @return void
 */
function va_subscription_plan_details( WP_Post $post ) {
	$plan_meta         = get_post_meta( $post->ID );
	$plan_costs        = (float) $plan_meta['plan_const'];
	$currency          = $plan_meta['currency'];
	$paypal_product_id = $plan_meta['paypal_product_id'];
	$paypal_plan_id    = $plan_meta['paypal_plan_id'];
	?>
	<div>
		<p>
			<label name="plan-cost"><?php _e( 'Plan cost', 'virtual-adoption' ) ?>:
				<input type="number" readonly value="<?php echo $plan_costs; ?>">
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
		<p>
			<label><?php _e( 'PayPal product ID', 'virtual-adoption' ); ?>:
				<input type="text" readonly value="<?php echo $paypal_product_id; ?>">
			</label>
		</p>
		<p>
			<label><?php _e( 'PayPal subscription plan ID', 'virtual-adoption' ); ?>
				<input type="text" readonly value="<?php echo $paypal_plan_id; ?>">
			</label>
		</p>
	</div>
	<?php
}
