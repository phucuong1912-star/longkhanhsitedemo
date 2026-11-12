<?php

/**
 * Template header part for app view.
 * templates/headers/header-app-view
 * @version 2.0
 */


$listing_mobile_view   =  lp_theme_option('single_listing_mobile_view');
if (!is_admin() && ($listing_mobile_view == 'app_view' || $listing_mobile_view == 'app_view2')) {

	$app_view_home  =   lp_theme_option('app_view_home');
	if (is_front_page()) {
		$front_page_id = get_option('page_on_front');
	}
	if (!empty($app_view_home) && !empty($front_page_id) && $app_view_home == $front_page_id) {
		$app_view_home = '';
	}
	if ($app_view_home != '' && !empty($app_view_home) && is_front_page()) {
		wp_redirect(get_permalink($app_view_home));
		exit;
	}
	/* call for root file */
	get_template_part('header-app-view');
}
