<?php

$displayOpt = "visibility:visible;";
$height = "height:auto;";
$padding = "padding-bottom:40px;";
$pricing_views = isset($pricing_views) ? sanitize_file_name($pricing_views) : '';
if ($pricing_views == "horizontal_view") {
	$pricing_style_views = $pricing_horizontal_view;
} else {
	$pricing_style_views = $pricing_vertical_view;
}


$lpyearmonthswitch = lp_theme_option('listingpro_month_year_plans');

/* dev for 2.0 */
$listingpro_plans_cats_pos = 'pricingplan';
$paidMode = lp_paid_mode_status();
if ($paidMode == true) {
	$listingpro_pre_plans_cats = lp_theme_option('listingpro_plans_cats');
	if ($listingpro_pre_plans_cats == "yes") {
	}
}


//Standard and Exclusive buttons switch
$showSwither = "yes";
if ($showSwither == "yes") {
	//for categories list to hide show.
	$displayOpt = "visibility:hidden;";
	$height = "height:0px;";
	$padding = "padding-bottom:0px;";

?>
	<div class="lp-standerd-exlusiv-outer padding-bottom-40">
		<div class="lp-standerd-exlusiv">
			<button type="button" class="btn standardbutto lp_plans_switcher_btn isactive" data-val="<?php echo esc_attr('standard'); ?>"><i class="fa fa-certificate" aria-hidden="true"></i><?php echo esc_html__('Standard', 'listingpro-plugin'); ?></button>

			<button type="button" class="btn exclusivebutto lp_plans_switcher_btn" data-val="<?php echo esc_attr('exclusive'); ?>"><i class="fa fa-hand-paper-o" aria-hidden="true"></i><?php echo esc_html__('Exclusive', 'listingpro-plugin'); ?></button>
		</div>
	</div>

<?php
}

// showing cats in dropdown add listing button
if ($listingpro_plans_cats_pos == "dropdown") {
	$outputCat = null;

	$outputCat .= '<div class="col-md-10 col-md-offset-1 padding-bottom-40 ' . $pricing_views . ' clearfix">';

	if ($title_subtitle_show == 'show_hide') {
		$outputCat .= '<div class="page-header">
				<h3>' . $title . '</h3>
				<p>' . $subtitle . '</p>
			</div>';
	}

	if (isset($_POST['lp_cat_plan_submit'])) {
		$lp_s_cat = $_POST['lp-slected-plan-cat'];
		if (!empty($lp_s_cat)) {
			/* code goes here */
			$args = null;
			$args = array(
				'post_type' => 'price_plan',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'meta_query' => array(
					array(
						'key' => 'lp_selected_cats',
						'value' => $lp_s_cat,
						'compare' => 'LIKE',
					),
				),
			);


			$cat_Plan_Query = null;
			$gridNumber = 0;
			$cat_Plan_Query = new WP_Query($args);
			if ($cat_Plan_Query->have_posts()) {
				while ($cat_Plan_Query->have_posts()) {
					$cat_Plan_Query->the_post();
					if ($pricing_views == "vertical_view_v2") {
						$gridNumber++;
					}
					ob_start();
					get_template_part("templates/pricing/loop/" . $pricing_plan_style);
					$outputCat .= ob_get_contents();
					ob_end_clean();
					ob_flush();
					if ($gridNumber % 3 == 0) {
						$outputCat .= '<div class="clearfix"></div>';
					}
				} //END WHILE
				wp_reset_postdata();
			} else {
				$outputCat .= '<div class="lp-no-plan-found"><p>' . esc_html__('No plan associated with this category', 'listingpro-plugin') . '</p></div>';
			}
		}
	}
	$outputCat .= '</div>';
	echo $outputCat;
}

//by showing cats on plans page
elseif ($listingpro_plans_cats_pos == "pricingplan") {

	$outputCat = null;
	$outputCat .= '
		<div class="col-md-aa col-md-offset-a padding-bottom-40 ' . $pricing_views . ' lp_cats_on_plan_wrap clearfix" data-style="' . $pricing_style_views . '" id="select_style" style="' . $displayOpt . ' ' . $height . ' ' . $padding . '">
			';
	$lp_only_plan_based_cats = ('listingpro_show_cats_have_plans');
	$lp_parent_cats = lp_get_all_cats_array(true);

	// Version 2,3,4 category
	if ($pricing_views == 'vertical_view' && $pricing_style_views == 'vertical_view_3' || $pricing_views == 'vertical_view' && $pricing_style_views == 'vertical_view_4' ||  $pricing_views == 'vertical_view' && $pricing_style_views == 'vertical_view_5') {

		if (!empty($lp_parent_cats)) {
			$outputCat .= '<form id="plan-page-cat-form" action = "" method="POST">
								<ul class="lp-price-cats-with-icon-v2">
								<div class="lp_category_list_slide">';

			foreach ($lp_parent_cats as $singlePCatID => $singlePcatName) {
				$is_active_plan = lp_plan_is_published($singlePCatID);
				if (!empty($is_active_plan)) {
					if ($lp_parent_cats == "yes") {
						$lp_attached_plans = get_term_meta($singlePCatID, 'lp_attached_plans', true);

						if (!empty($lp_attached_plans)) {
							$outputCat .= '<label class="clearfix">
										   <input type="radio" class="lp-slected-plan-cat" name="lp-slected-plan-cat" value="' . $singlePCatID . '">
										   <span> ' . $singlePcatName . '</span>
										</label>';
						}
					} else {

						$outputCat .= '<li><label class="clearfix">
										   <input type="radio" class="lp-slected-plan-cat" name="lp-slected-plan-cat" value="' . $singlePCatID . '">';

						$outputCat .= '<span> ' . $singlePcatName . '</span>
										</label></li>';
					}
				}
			}
			$outputCat .= '</div></ul></form>';
		}
	}

	// Version 5 category

	elseif ($pricing_views == 'vertical_view' && $pricing_style_views == 'vertical_view_6') {

		if (!empty($lp_parent_cats)) {
			$outputCat .= '<form id="plan-page-cat-form" action = "" method="POST">
								<ul class="lp-price-cats-with-icon">
								<div class="lp_category_list_slide">';

			foreach ($lp_parent_cats as $singlePCatID => $singlePcatName) {
				$is_active_plan = lp_plan_is_published($singlePCatID);
				if (!empty($is_active_plan)) {
					if ($lp_parent_cats == "yes") {
						$lp_attached_plans = get_term_meta($singlePCatID, 'lp_attached_plans', true);

						if (!empty($lp_attached_plans)) {
							$outputCat .= '<label class="clearfix">
										   <input type="radio" class="lp-slected-plan-cat" name="lp-slected-plan-cat" value="' . $singlePCatID . '">
										   <span> ' . $singlePcatName . '</span>
										</label>';
						}
					} else {

						$outputCat .= '<li><div class="category_image_thumbnail">
									';
						$outputCat .= '<label class="clearfix">
									<div class="category_image_thumbnail_overlay"></div>
										   <input type="radio" class="lp-slected-plan-cat" name="lp-slected-plan-cat" value="' . $singlePCatID . '">';
						$outputCat .= '<span> ' . $singlePcatName . '</span>';
						$category_image = listing_get_tax_meta($singlePCatID, 'category', 'banner');

						if (!empty($category_image)) {
							$outputCat .= '<img class="image_thumbnail_section" alt="image" src="' . $category_image . '"/>';
						} else {
							$img_thumb_url = listingpro_icons_url('plans_thumbnail_section');
							$outputCat .= '<img class="image_thumbnail_section" alt="image" src="' . $img_thumb_url . '">';
						}

						$outputCat .= '</label></div></li>';
					}
				}
			}
			$outputCat .= '</div></ul></form>';
		}
	}

	// Version 6 category

	elseif ($pricing_views == 'vertical_view' && $pricing_style_views == 'vertical_view_7' || $pricing_views == 'vertical_view' && $pricing_style_views == 'vertical_view_8') {

		if (!empty($lp_parent_cats)) {
			$outputCat .= '<form id="plan-page-cat-form" action = "" method="POST">
					<div class="category_custom_dropdown">			
					<select class="select2" id="category_dropdown">
					<option>CHOOSE CATEGORY</option>
					';

			foreach ($lp_parent_cats as $singlePCatID => $singlePcatName) {
				$is_active_plan = lp_plan_is_published($singlePCatID);
				if (!empty($is_active_plan)) {
					if ($lp_parent_cats == "yes") {
						$lp_attached_plans = get_term_meta($singlePCatID, 'lp_attached_plans', true);

						if (!empty($lp_attached_plans)) {
							$outputCat .= '<label class="clearfix">
										   <input type="radio" class="lp-slected-plan-cat" name="lp-slected-plan-cat" value="' . $singlePCatID . '">
										   <span> ' . $singlePcatName . '</span>
										</label>';
						}
					} else {

						$outputCat .= '<option class="lp-slected-plan-cat" name="lp-slected-plan-cat" value="' . $singlePCatID . '">' . $singlePcatName . '</option>';
					}
				}
			}
			$outputCat .= '</select></div></form>';
		}
	} else {
		// Version 1 category and version 8.
		if (!empty($lp_parent_cats)) {
			$outputCat .= '<form id="plan-page-cat-form" action = "" method="POST">
								<ul class="lp-price-cats-with-icon">
								<div class="lp_category_list_slide">';

			foreach ($lp_parent_cats as $singlePCatID => $singlePcatName) {

				$is_active_plan = lp_plan_is_published($singlePCatID);
				if (!empty($is_active_plan)) {

					if ($lp_parent_cats == "yes") {
						$lp_attached_plans = get_term_meta($singlePCatID, 'lp_attached_plans', true);

						if (!empty($lp_attached_plans)) {
							$outputCat .= '<label class="clearfix">
										   <input type="radio" class="lp-slected-plan-cat" name="lp-slected-plan-cat" value="' . $singlePCatID . '">
										   <span> ' . $singlePcatName . '</span>
										</label>';
						}
					} else {

						$outputCat .= '<li><label class="clearfix">
										   <input type="radio" class="lp-slected-plan-cat" name="lp-slected-plan-cat" value="' . $singlePCatID . '">';

						$category_image = listing_get_tax_meta($singlePCatID, 'category', 'image');
						$outputCat .= '<span>';
						if (!empty($category_image)) {
							if (hasFontAwesomeIconClass($category_icon)) {
								$outputCat .=  '<i class="icons-banner-cat ' . $category_image . '"></i>';
							} else {
								$outputCat .= '<img class="icons-banner-cat" alt="image" src="' . $category_image . '"/>';
							}
							$outputCat .= '<img class="icons-banner-cat" alt="image" src="' . $category_image . '"/>';
						} else {

							$icon_bann_url = listingpro_icons_url('icon_banner_cat');
							$outputCat .= '<img class="icons-banner-cat" alt="image" src="' . $icon_bann_url . '">';
						}

						$outputCat .= $singlePcatName . '</span>
										</label></li>';
					}
				}
			}
			$outputCat .= '</div></ul></form>';
		}
	}

	$outputCat .= '</div>';

	// Monthly Yearly Switch
	$isMontlyFilter = lp_plan_has_monthyear_duration('default', 'default', 'NO EXIST');

	if (
		$pricing_views == 'vertical_view' && $pricing_style_views == 'vertical_view_4' ||
		$pricing_views == 'vertical_view' && $pricing_style_views == 'vertical_view_5' ||
		$pricing_views == 'vertical_view' && $pricing_style_views == 'vertical_view_7' ||
		$pricing_views == 'vertical_view' && $pricing_style_views == 'vertical_view_8'
	) {

		if ($lpyearmonthswitch == "yes") {
			$outputCat .= '<div class="col-md-aa col-md-offset-a clearfix">
					<div class="outer_switch_month_year lpmonthyearswitcher"><ul class="switch_month_year">
					<li><span class="active_switch">' . esc_html__('Monthly', 'listingpro-plugin') . '</span></li>
					<li><span class="lp_show_hide_plans">' . esc_html__('Annually', 'listingpro-plugin') . '</span></li>
					</ul></div>
					</div>';
		}
	} else {

		if ($lpyearmonthswitch == "yes") {
			$outputCat .= '<div class="lp-montly-annualy-text clearfix">
					<div class="lp_button_switcher lpmonthyearswitcher">
					<span class="lp-monthly-side">' . esc_html__('Monthly', 'listingpro-plugin') . '</span>
					<a href="#" class="switch-fields lp_show_hide_plans"></a><span class="lp-year-side">' . esc_html__('Annually', 'listingpro-plugin') . '</span>
					</div>
					
					</div>';
		}
	}

	// Default View pricing plan and ajax response

	if ($pricing_style_views == 'horizontal_view_2' || $pricing_style_views == 'horizontal_view_1') {
		$outputCat .= '<div class="col-md-aa col-md-offset-a clearfix lp_plan_result_section lp-horizontial-specific" style="border:0px;">
		<div id="cats-selected-plans" class="selected_horizontial_plans_v1"></div>
		</div>';
	} elseif ($pricing_style_views == 'vertical_view_1' || $pricing_style_views == 'vertical_view_3' || $pricing_style_views == 'vertical_view_4' || $pricing_style_views == 'vertical_view_5') {

		$outputCat .= '<div class="col-md-aa col-md-offset-a row-centered clearfix lp_plan_result_section" style="border:0px;" data-style="' . $pricing_style_views . '">
			<div class="slider_pricing_Plan">
			<div id="cats-selected-plans" class="selected_plans_v2" style="border:0px;"></div>
			</div>
			</div>';
	} else {

		$outputCat .= '<div class="col-md-aa col-md-offset-a row-centered clearfix lp_plan_result_section" data-style="' . $pricing_style_views . '">
			<div class="slider_pricing_Plan">
			<div id="cats-selected-plans" class="selected_plans_v2"></div>
			</div>
			</div>';
	}

	echo $outputCat;
}
?>