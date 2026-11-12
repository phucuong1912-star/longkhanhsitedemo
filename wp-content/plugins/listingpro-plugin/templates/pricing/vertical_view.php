<?php
global $listingpro_options;
$listing_access_only_users = $listingpro_options['lp_allow_vistor_submit'];
$showAddListing = true;
if (isset($listing_access_only_users) && $listing_access_only_users == 1) {
	$showAddListing = false;
	if (is_user_logged_in()) {
		$showAddListing = true;
	}
}
if ($showAddListing == false) {
	wp_redirect(home_url());
	exit;
}
global $wpdb;

$lp_social_show;
$lp_social_show = $listingpro_options['listin_social_switch'];

$dbprefix = '';
$dbprefix = $wpdb->prefix;
$user_ID = '';
$user_ID = get_current_user_id();
$outputVert = null;
$results = null;
$table_name = $dbprefix . 'listing_orders';
$limitLefts = '';
$taxOn = $listingpro_options['lp_tax_swtich'];
$withtaxprice = false;
$lpyearmonthswitch = lp_theme_option('listingpro_month_year_plans');
$pricing_vertical_view = isset($pricing_vertical_view) ? sanitize_file_name($pricing_vertical_view) : '';
$allowed_statuses = array('horizontal_view_1', 'horizontal_view_2', 'vertical_view_1', 'vertical_view_2', 'vertical_view_3', 'vertical_view_4', 'vertical_view_5', 'vertical_view_6', 'vertical_view_7', 'vertical_view_8', 'vertical_view_9', 'claim_plans');
if ($pricing_views == "horizontal_view") {
	$pricing_style_views = $pricing_horizontal_view;
} else {
	$pricing_style_views = $pricing_vertical_view;
}
$switcherArgs = array();
$showSwitcher = false;
$lp_plans_cats = lp_theme_option('listingpro_plans_cats');
if ($lp_plans_cats != 'yes') {
	if ($lpyearmonthswitch == "yes") {
		$showSwitcher = true;
	}
} elseif ($lp_plans_cats == 'yes') {
	$switcherArgs['relation'] = 'AND';
}
if ($showSwitcher == true) {
	$outputVert .= '<div class="lp-montly-annualy-text clearfix">
			<div class="lp_button_switcher lpmonthyearswitcher">
			<span class="lp-monthly-side active">' . esc_html__('Monthly', 'listingpro-plugin') . '</span>
			<a href="#" class="switch-fields lp_show_hide_plans"></a><span class="lp-year-side">' . esc_html__('Annually', 'listingpro-plugin') . '</span>
			</div>
			</div>';
}


if ($lpyearmonthswitch == "yes") {
	$switcherArgs[] = array(
		'key' => 'plan_duration_type',
		'value' => 'monthly',
		'compare' => 'LIKE',
	);
}
if ($taxOn == "1") {
	$showtaxwithprice = $listingpro_options['lp_tax_with_plan_swtich'];
	if ($showtaxwithprice == "1") {
		$withtaxprice = true;
	}
}
$hide_plan_class = 'lp_hide_general_plans';
$borderclass = '';

// class added for border in views
if (
	$pricing_views == 'vertical_view' && $pricing_vertical_view == 'vertical_view_2' ||
	$pricing_views == 'vertical_view' && $pricing_vertical_view == 'vertical_view_6' ||
	$pricing_views == 'vertical_view' && $pricing_vertical_view == 'vertical_view_7' ||
	$pricing_views == 'vertical_view' && $pricing_vertical_view == 'vertical_view_8' ||
	$pricing_views == 'vertical_view' && $pricing_vertical_view == 'vertical_view_9'
) {

	$borderclass = 'lp-plan-view-border';
}

/* vertical view code */

$outputVert .= '
			<div class="page-inner-container ' . $hide_plan_class . ' ' . $borderclass . '" id="select_style"  data-style="' . $pricing_style_views . '">';
$args1 = array(
	'post_type' => 'price_plan',
	'posts_per_page' => -1,
	'post_status' => 'publish',
	'meta_query' => array(
		$switcherArgs,
	),
);
$query1 = new WP_Query($args1);
$count = $query1->found_posts;
$GLOBALS['plans_count'] = $count;
if ($query1->have_posts()) {
	while ($query1->have_posts()) {
		$query1->the_post();
		$listing_id = get_the_ID();
		$forListings = listing_get_metabox_by_ID('plan_for', $listing_id);
		if ($forListings != "claimonly") {
			$planfor = get_post_meta(get_the_ID(), 'plan_usge_for', true);
			if (empty($planfor) || $planfor == 'default') {
				if (in_array($pricing_vertical_view, $allowed_statuses, true)) {
					ob_start();
					include(LISTINGPRO_PLUGIN_PATH . "templates/pricing/loop/" . $pricing_vertical_view . '.php');
					$outputVert .= ob_get_contents();
					ob_end_clean();
					ob_flush();
				}
			}
		}
	}/* END WHILE */
	wp_reset_postdata();
} else {
	$outputVert .= '<p class="text-center">' . esc_html__('There is no Plan available right now.', 'listingpro-plugin') . '</p>';
}
$outputVert .= '
				
			</div>
				';

if ($lp_plans_cats != 'yes') {
	$outputVert .= '
				<div id="cats-selected-plans" class="selected_horizontial_plans_v1 lp_plan_result_section" data-style="' . $pricing_style_views . '"></div>';
}

echo $outputVert;
