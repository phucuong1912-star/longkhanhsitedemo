<?php

/**
 * Adds a submenu page under settings for wizard
 */



/**
 * redirect to compatibility wizard
 */
if (!function_exists('lp_redirect_to_wizard')) {
	function lp_redirect_to_wizard()
	{

		$redURL = esc_url_raw(add_query_arg('page', 'lp-compatibility-wizard', admin_url('options-general.php')));

		$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

		if ($actual_link != $redURL) {

			if (wp_safe_redirect($redURL)) {
				exit;
			}
		}
	}
}

/**
 * admin init to call this function on theme activation
 */

if (!function_exists('lp_run_wizard_on_theme_update')) {
	function lp_run_wizard_on_theme_update()
	{
		global $pagenow;

		$is_compatible = get_option('lp_update_compatible');
		$lp_theme = get_option('stylesheet');
		$lp_themeOBJ = wp_get_theme();
		$lp_theme_ver = $lp_themeOBJ->get('Version');

		if (("index.php" == $pagenow || "themes.php" == $pagenow || "plugins.php" == $pagenow) && is_admin()) {
			if ($lp_theme == "listingpro" && $lp_theme_ver >= '2.5') {
				if (empty($is_compatible)) {
					$listings_check =   new WP_Query(
						array(
							'post_type' => 'listing',
							'post_status' => 'any',
							'posts_per_page' => 1
						)
					);
					$found_listings =   $listings_check->found_posts;
					if ($found_listings > 0) {
						lp_redirect_to_wizard();
					}
				}
			}
		}
	}
}
add_action('admin_init', 'lp_run_wizard_on_theme_update');




if (!function_exists('lp_register_compatiblity_wizard')) {
	function lp_register_compatiblity_wizard()
	{
		add_submenu_page(
			'options-general.php',
			__('Listingpro Compatibility Wizard', 'listingpro-plugin'),
			__('Listingpro Wizard', 'listingpro-plugin'),
			'manage_options',
			'lp-compatibility-wizard',
			'lp_compatiblity_page_callback'
		);
	}
}

/**
 * Display callback for wizard page
 */
if (!function_exists('lp_compatiblity_page_callback')) {
	function lp_compatiblity_page_callback()
	{
?>
		<div class="wrap lp-wizard-required">
			<div class="clearfix"></div>
			<div class="row marginbottom30">
				<div class="lp-back-wised-outer">
					<h1><?php _e('One-Time Backward Compatiblity Wizard', 'listingpro-plugin'); ?></h1>
					<div class="lp-back-wised-inner">
						<img alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/wised1.png'; ?>">
						<p><?php echo esc_html__('We have made some major changes in the latest version that requires a one-time compatiblity wizard to make sure everything is fine after the update.', 'listingpro-plugin'); ?></p>
						<button class="btn btn-default lp_compat_start" data-per-page="10" data-offset="0" data-count="0" type="button"><?php echo esc_html__('Start', 'listingpro-plugin'); ?></button>
						<a href="<?php echo admin_url(); ?>" class="btn btn-default lp_compat_start2" type="button"><?php echo esc_html__('Back To Dashboard', 'listingpro-plugin'); ?></a>
					</div>
				</div>
			</div>


			<div class="row">
				<div class="col-md-12">
					<div class="lp-comp-stats text-center" data-status=<?php echo esc_attr__('Complete', 'listingpro-plugin'); ?>>
						<p><span class="stats"></span></p>
					</div>
				</div>
			</div>
		</div>
<?php
	}
}

add_action('admin_menu', 'lp_register_compatiblity_wizard');

/**
 * enqueueing scripts
 */
if (!function_exists('lpwizard_admin_scripts')) {
	function lpwizard_admin_scripts()
	{
		global $post, $pagenow;
		if ($pagenow == "options-general.php") {
			//setting page
			if (isset($_GET['page'])) {
				if ($_GET['page'] == 'lp-compatibility-wizard') {
					//compatibilty wizard page
					wp_enqueue_style('bootstrapcss', get_template_directory_uri() . '/assets/lib/bootstrap/css/bootstrap.min.css');

					wp_enqueue_script('bootstrapadmin', get_template_directory_uri() . '/assets/lib/bootstrap/js/bootstrap.min.js', 'jquery', '', true);
				}
			}
		}
	}
}
add_action('admin_print_scripts', 'lpwizard_admin_scripts');


/**
 * ajax callback
 */
if (!function_exists('lp_setup_wizard')) {
	function lp_setup_wizard()
	{
		if (!check_ajax_referer('lp_admin_nonce_action', '_ajax_nonce', false)) {
			wp_send_json(array(
				'status'  => 'error',
				'message' => 'Invalid nonce'
			));
		}
		if (!current_user_can('edit_posts')) {
			wp_send_json(array(
				'status'  => 'error',
				'message' => 'You do not have permission to edit posts.'
			));
		}
		$ppp = isset($_POST['ppp']) ? (int) $_POST['ppp'] : 0;
		$offset = isset($_POST['offset']) ? (int) $_POST['offset'] : 0;
		$count = isset($_POST['count']) ? (int) $_POST['count'] : 0;
		$listingsArray = array();
		$count_listings = wp_count_posts($post_type = 'listing');
		$total = null;
		if (!empty($count_listings)) {
			$published_listing = $count_listings->publish;
			if (!empty($published_listing)) {
				$args = array(
					'post_type' => 'listing',
					'posts_per_page' => $ppp,
					'post_status' => 'any',
					'offset' => $offset
				);
				$the_query = new WP_Query($args);
				$total = $the_query->found_posts;
				if ($the_query->have_posts()) {

					while ($the_query->have_posts()) {
						$the_query->the_post();
						$listingsArray[get_the_ID()] = get_the_title();

						$claimed = listing_get_metabox_by_ID('claimed_section', get_the_ID());
						if ($claimed == 'claimed') {
							update_post_meta(get_the_ID(), 'claimed', 1);
						} else {
							update_post_meta(get_the_ID(), 'claimed', 0);
							listing_set_metabox('claimed_section', 'not_claimed', get_the_ID());
						}

						$planID = listing_get_metabox_by_ID('Plan_id', get_the_ID());
						if (!empty($planID) && $planID != 0 && $planID != 'none') {
							update_post_meta(get_the_ID(), 'plan_id', $planID);
						}
						if (!empty($planID) && $planID != 0 && $planID != 'none') {
							$plan_time  = get_post_meta($planID, 'plan_time', true);
							if (!empty($plan_time)) {
								listing_set_metabox('lp_purchase_days', $plan_time, get_the_ID());
							}
						}
						$listing_rate = get_post_meta(get_the_ID(), 'listing_rate', true);
						update_post_meta(get_the_ID(), 'listing_rate', $listing_rate);

						$listing_reviewed = get_post_meta(get_the_ID(), 'listing_reviewed', true);
						update_post_meta(get_the_ID(), 'listing_reviewed', $listing_reviewed);

						$listing_viewed = get_post_meta(get_the_ID(), 'post_views_count', true);
						update_post_meta(get_the_ID(), 'post_views_count', $listing_viewed);

						$count++;
					}
					wp_reset_postdata();
				}
			}
		}
		$offset = $ppp + $offset;

		$total = (int) $total;
		if ($total > 0 && $count == $total) {
			//compatibility complete
			update_option('lp_update_compatible', true);
		}
		$percentage = $total > 0 ? (($count / $total) * 100) : 0;

		wp_send_json(array(
			'offset'     => (int) $offset,
			'total'      => $total,
			'data'       => array_map('sanitize_text_field', $listingsArray),
			'percentage' => (float) $percentage,
			'count'      => (int) $count
		));
	}
}
add_action('wp_ajax_lp_setup_wizard', 'lp_setup_wizard');
