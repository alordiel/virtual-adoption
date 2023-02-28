<?php
// loads templates name in page attributes
function va_register_template_pages( $templates ) {
	$templates['va-donation-checkout.php']  = 'VirtualAdoption - Donation Checkout page';
	$templates['va-thank-you-donation.php'] = 'VirtualAdoption - Thank you page';
	$templates['va-my-subscriptions.php']   = 'VirtualAdoption - My subscriptions page';
	$templates['va-login-page.php']         = 'VirtualAdoption - Login page';

	return $templates;
}

add_filter( 'theme_page_templates', 'va_register_template_pages' );

// loads page templates
function va_add_templates_pages( $template ) {
	$template_slug = get_page_template_slug();
	if ( $template_slug === 'va-donation-checkout.php' ) {
		$template = VA_ABS . '/templates/pages/donation-checkout.php';
	} elseif ( $template_slug === 'va-thank-you-donation.php' ) {
		$template = VA_ABS . '/templates/pages/thank-you-donation.php';
	} elseif ( $template_slug === 'va-my-subscriptions.php' ) {
		$template = VA_ABS . '/templates/pages/my-subscriptions.php';
	} elseif ( $template_slug === 'va-login-page.php' ) {
		$template = VA_ABS . '/templates/pages/login-page.php';
	}

	return $template;
}

add_filter( 'page_template', 'va_add_templates_pages' );


/* Remove the "Dashboard" from the admin menu for non-admin users */
function va_wp_remove_dashboard_for_non_admins () {
    global $current_user, $menu, $submenu;
    get_currentuserinfo();

    if( ! in_array( 'administrator', $current_user->roles ) ) {
        reset( $menu );
        $page = key( $menu );
        while( ( __( 'Dashboard' ) != $menu[$page][0] ) && next( $menu ) ) {
            $page = key( $menu );
        }
        if( __( 'Dashboard' ) == $menu[$page][0] ) {
            unset( $menu[$page] );
        }
        reset($menu);
        $page = key($menu);
        while ( ! $current_user->has_cap( $menu[$page][1] ) && next( $menu ) ) {
            $page = key( $menu );
        }
        if ( preg_match( '#wp-admin/?(index.php)?$#', $_SERVER['REQUEST_URI'] ) &&
            ( 'index.php' != $menu[$page][2] ) ) {
                wp_redirect( get_option( 'siteurl' ) . '/wp-admin/edit.php');
        }
    }
}
add_action('admin_menu', 'va_wp_remove_dashboard_for_non_admins');
