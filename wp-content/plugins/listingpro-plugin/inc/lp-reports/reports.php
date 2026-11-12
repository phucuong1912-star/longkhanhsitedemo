<?php
/*---------------------------------------------------
				adding reports page
----------------------------------------------------*/
if (!function_exists('listingpro_register_reports_page')) {

	function listingpro_register_reports_page()
	{
		//Get the reported listings
		$reportedLisings = get_option('lp_reported_listings');

		$ReportedLisints = array();
		if (strpos($reportedLisings, ',') !== false) {
			$ReportedLisints = explode(",", $reportedLisings);
		} else {
			$ReportedLisints[] = $reportedLisings;
		}

		$flagcount     = '';
		$listingcount  = 0;
		$reviewcount   = 0;
		$reports_query = new WP_Query(array(
			'post_type'      => 'listing',
			'post__in'       => $ReportedLisints,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		));

		if ($reports_query->have_posts()) {
			while ($reports_query->have_posts()) {
				$reports_query->the_post();
				$reportedCount = listing_get_metabox_by_ID('listing_reported', get_the_ID());

				if (!empty($reportedCount)) {
					if ($reportedCount > 0) {
						$listingcount = $reportedCount;
					} else {
						$listingcount = 0;
					}
				}
			}
		}



		//Get the reported reviews
		$reportedReviews = get_option('lp_reported_reviews');
		$Reported_review = array();
		if (strpos($reportedReviews, ',') !== false) {
			$Reported_review = explode(",", $reportedReviews);
		} else {
			$Reported_review[] = $reportedReviews;
		}
		$reports_query = new WP_Query(array(
			'post_type'      => 'lp-reviews',
			'post__in'       => $Reported_review,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		));

		if ($reports_query->have_posts()) {
			while ($reports_query->have_posts()) {
				$reports_query->the_post();
				$reportedreviewCount = listing_get_metabox_by_ID('review_reported', get_the_ID());

				if (!empty($reportedreviewCount)) {
					if ($reportedreviewCount > 0) {
						$reviewcount = $reportedreviewCount;
					} else {
						$reviewcount = 0;
					}
				}
			}
		}

		$style = '';
		$style1 = '';
		$have_unread_report_review = get_option('lp_unread_review_report');
		$have_unread_report = get_option('lp_unread_listing_report');

		if ($reviewcount > 0 && $have_unread_report_review > 0) {
			$style = 'position:relative;top:0px;left:10px;height:10px;width:10px;background-color:red;border-radius:50%;display:inline-block';
		} else if ($listingcount > 0 && $have_unread_report > 0) {
			$style = 'position:relative;top:0px;left:10px;height:10px;width:10px;background-color:red;border-radius:50%;display:inline-block';
		}

		if ($listingcount > 0 && $have_unread_report > 0) {
			$style1 = 'position:relative;top:0px;left:10px;height:10px;width:10px;background-color:red;border-radius:50%;display:inline-block';
		}

		if ($have_unread_report > 0) {
			$flagcount = 'Flags ';
			$flagcount .= '<span id="lp_flag_id" class="dot" style="' . $style . '"></span>';
		} else {
			$flagcount = 'Flag';
			$flagcount .= '<span id="lp_flag_id" class="dot" style="' . $style . '"></span>';
		}
		add_menu_page(__('Flags', 'listingpro-plugin'), $flagcount, 'manage_options', 'lp-flags', 'listingpro_flags_page', plugins_url('listingpro-plugin/images/flag.png'), 30);

		//Listing flag sub menu
		$flagcount_sub = '';
		if ($have_unread_report > 0) {
			$flagcount_sub = 'Listing Flags';
			if ($listingcount > 0) {
				$flagcount_sub .= '<span id="lp_flag_id" class="dot" style="' . $style1 . '"></span>';
			}
		} else {
			$flagcount_sub = 'Listing Flag';
			if ($listingcount > 0) {
				$flagcount_sub .= '<span id="lp_flag_id" class="dot" style="' . $style1 . '"></span>';
			}
		}
		add_submenu_page(
			'lp-flags',
			'Flags',
			$flagcount_sub,
			'manage_options',
			'lp-flags',
			'listingpro_flags_page'
		);
		wp_enqueue_style("panel_style", WP_PLUGIN_URL . "/listingpro-plugin/assets/css/custom-admin-pages.css", false, "1.0", "all");
	}
}
add_action('admin_menu', 'listingpro_register_reports_page');

/* ----------------------include listings reports---------------- */
include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/lp-reports/listings-reports.php');

/* ----------------------include reviews reports---------------- */
include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/lp-reports/reviews-reports.php');
