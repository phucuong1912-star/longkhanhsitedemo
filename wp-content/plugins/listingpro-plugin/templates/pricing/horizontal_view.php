<?php
global $listingpro_options;
global $wpdb;

$lp_social_show;
$lp_social_show = $listingpro_options['listin_social_switch'];
$dbprefix = '';
$dbprefix = $wpdb->prefix;
$user_ID = '';
$user_ID = get_current_user_id();
$output1 = null;
$results = null;
$table_name = $dbprefix . 'listing_orders';
$limitLefts = '';
$taxOn = $listingpro_options['lp_tax_swtich'];
$withtaxprice = false;
$lpyearmonthswitch = lp_theme_option('listingpro_month_year_plans');
$pricing_horizontal_view = isset($pricing_horizontal_view) ? sanitize_file_name($pricing_horizontal_view) : '';
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
	$output1 .= '<div class="lp-montly-annualy-text clearfix">
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
$horposiclass = '';
if ($pricing_views == 'horizontal_view' && $pricing_horizontal_view == 'horizontal_view_2') {

	$horposiclass = 'lp-horizontial-specific';
}

/* horizontal view */

$output1 .= '
				<div class="page-inner-container lp_plan_result_section ' . $hide_plan_class . ' ' . $horposiclass . '" id="select_style"  data-style="' . $pricing_style_views . '">';
$args = array(
	'post_type' => 'price_plan',
	'posts_per_page' => -1,
	'post_status' => 'publish',
	'meta_query' => array(
		$switcherArgs,
	),
);
$query = new WP_Query($args);
if ($query->have_posts()) {
	while ($query->have_posts()) {
		$query->the_post();
		$listing_id = get_the_ID();
		$forListings = listing_get_metabox_by_ID('plan_for', $listing_id);
		if ($forListings != "claimonly") {
			global $post;
			$planfor = get_post_meta(get_the_ID(), 'plan_usge_for', true);
			if (empty($planfor) || $planfor == 'default') {
				if (in_array($pricing_horizontal_view, $allowed_statuses, true)) {
					ob_start();
					include(LISTINGPRO_PLUGIN_PATH . "templates/pricing/loop/" . $pricing_horizontal_view . '.php');
					$output1 .= ob_get_contents();
					ob_end_clean();
					ob_flush();
				}
			}
		}
	}/* END WHILE */
	wp_reset_postdata();
} else {
	$output1 .= '<p class="text-center">' . esc_html__('There is no Plan available right now.', 'listingpro-plugin') . '</p>';
}


if ($lp_plans_cats != 'yes') {
	$output1 .= '
				<div id="cats-selected-plans" class="selected_horizontial_plans_v1 "></div>';
}

$output1 .= '
			</div> 
			';

echo $output1;
