<?php

/**
 * Listingpro Functions.
 *
 */

use Elementor\Plugin;
use Stripe\Stripe;
use Stripe\Subscription;

define('THEME_PATH', get_template_directory());
define('THEME_DIR', get_template_directory_uri());
define('STYLESHEET_PATH', get_stylesheet_directory());
define('STYLESHEET_DIR', get_stylesheet_directory_uri());
define('CRIDIO_API_URL', 'https://license.listingprowp.com/wp-json/verifier/v1/');
define('CRIDIO_FILES_URL', 'https://license.listingprowp.com/wp-content/plugins/lpverifier/core-files');


/* ============== Theme Setup ============ */

add_action('after_setup_theme', 'listingpro_theme_setup');
if (!function_exists('listingpro_theme_setup')) {
	function listingpro_theme_setup()
	{

		/* Text Domain */
		$domain = 'listingpro';
		$mofile = get_template_directory() . '/languages/' . determine_locale() . '.mo';
		if (file_exists($mofile)) {
			load_textdomain($domain, $mofile);
		}
		/* Theme supports */

		add_editor_style();
		add_theme_support('post-thumbnails');
		add_theme_support("custom-header");
		add_theme_support("custom-background");
		add_theme_support('automatic-feed-links');

		remove_post_type_support('page', 'thumbnail');

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list'
			)
		);

		// We are using three menu locations.
		register_nav_menus(array(
			'primary'         => esc_html__('Homepage Menu', 'listingpro'),
			'primary_inner'   => esc_html__('Inner Pages Menu', 'listingpro'),
			'top_menu'        => esc_html__('Top Bar Menu', 'listingpro'),
			'footer_menu' 	  => esc_html__('Footer Menu', 'listingpro'),
			'mobile_menu' 	  => esc_html__('Mobile Menu', 'listingpro'),
			'category_menu' 	  => esc_html__('Category Menu', 'listingpro'),
		));

		global $listingpro_options;
		$header_style   =   null;
		$header_style   =   lp_get_header_views();

		if (isset($listingpro_options['header_cats_partypro']) && $header_style == 'header_with_bigmenu') {
			register_nav_menu('pp_cat_menu', __('Partypro Category Menu', 'listingpro'));
		}

		/* Image sizes */
		add_image_size('listingpro-blog-grid', 372, 240, true); // (cropped)		
		add_image_size('listingpro-blog-grid2', 372, 400, true); // (cropped)		
		add_image_size('listingpro-blog-grid3', 672, 430, true); // (cropped)		
		add_image_size('listingpro-listing-grid', 272, 231, true); // (cropped)		
		add_image_size('listingpro-listing-gallery', 580, 408, true); // (cropped)		
		add_image_size('listingpro-list-thumb', 287, 190, true); // (cropped)		
		add_image_size('listingpro-author-thumb', 63, 63, true); // (cropped)		
		add_image_size('listingpro-gallery-thumb1', 458, 425, true); // (cropped)		
		add_image_size('listingpro-gallery-thumb2', 360, 198, true); // (cropped)		
		add_image_size('listingpro-gallery-thumb3', 263, 198, true); // (cropped)		
		add_image_size('listingpro-gallery-thumb4', 653, 199, true); // (cropped)

		add_image_size('listingpro-detail_gallery', 383, 454, true); // (cropped)

		add_image_size('listingpro-checkout-listing-thumb', 220, 80, true); // (cropped)	
		add_image_size('listingpro-review-gallery-thumb', 184, 135, true); // (cropped)
		add_image_size('listingpro-thumb4', 272, 300, true); // (cropped)

		//for location
		add_image_size('listingpro_location270_400', 270, 400, true); // (cropped)
		add_image_size('listingpro_location570_455', 570, 455, true); // (cropped)
		add_image_size('listingpro_location570_228', 570, 228, true); // (cropped)
		add_image_size('listingpro_location270_197', 270, 197, true); // (cropped)

		add_image_size('listingpro_cats270_213', 270, 213, true); // (cropped) 

		add_image_size('listingpro_cats270_150', 270, 150, true); // (cropped)
		add_image_size('listingpro_liststyle181_172', 190, 146, true); // (cropped)
		// v2
		add_image_size('lp-sidebar-thumb-v2', 84, 84, true);

		if (get_option('listingpro_activation_date') === false) {
			// Save the activation date to an option
			update_option('listingpro_activation_date', current_time('mysql'));
		}
	}
}

if (!isset($content_width)) {
	$content_width = 900;
}
/* ============== Dynamic options and Styling ============ */
require_once THEME_PATH . '/include/dynamic-options.php';

/* ============== Breadcrumb ============ */
require_once THEME_PATH . '/templates/breadcrumb.php';

/* ============== Blog Comments ============ */
require_once THEME_PATH . '/templates/blog-comments.php';

/* ============== Required Plugins ============ */
require_once THEME_PATH . "/include/plugins/install-plugin.php";

/* ============== icons ============ */
require_once THEME_PATH . "/include/icons.php";

/* ============== List confirmation ============ */
require_once THEME_PATH . "/include/list-confirmation.php";

/* ============== Login/Register ============ */
require_once THEME_PATH . "/include/login-register.php";

/* ============== Search Filter ============ */
require_once THEME_PATH . "/include/search-filter.php";

/* ============== Claim List ============ */
require_once THEME_PATH . "/include/single-ajax.php";

/* ============== Social Share ============ */
require_once THEME_PATH . "/include/social-share.php";

/* ============== Ratings ============ */
require_once THEME_PATH . "/include/reviews/ratings.php";

/* ============== Last Review ============ */
require_once THEME_PATH . "/include/reviews/last-review.php";

/* ============== Check Time status ============ */
require_once THEME_PATH . "/include/time-status.php";

/* ============== Banner Catss ============ */
require_once THEME_PATH . "/include/banner-cats.php";

/* ============== Fav Function ============ */
require_once THEME_PATH . "/include/favorite-function.php";

/* ============== Reviews Form ============ */
require_once THEME_PATH . "/include/reviews/reviews-form.php";

/* ============== all reviews ============ */
require_once THEME_PATH . "/include/reviews/all-reviews.php";

/* ============== review-submit ============ */
require_once THEME_PATH . "/include/reviews/review-submit.php";

/* ============== all reviews ============ */
require_once THEME_PATH . "/include/all-extra-fields.php";

/* ============== invoice function ============ */
require_once THEME_PATH . "/include/invoices/invoice-functions.php";

require_once THEME_PATH . "/include/invoices/invoice-modal.php";

/* ============== setup wizard =============== */
require_once THEME_PATH . "/include/setup/envato_setup.php";
//importer
require_once THEME_PATH . "/include/setup/importer/init.php";

/* ============== listing data db save ============ */
require_once THEME_PATH . "/include/listingdata_db_save.php";

/* ============== listing home map  ============ */
require_once THEME_PATH . "/include/home_map.php";

/* ============== login based file  ============ */
if (!is_user_logged_in()) {
	require_once THEME_PATH . "/include/lp-needlogin.php";
}

/* ============== login based file  ============ */
require_once THEME_PATH . "/include/function-filter.php";

/* ============== ListingPro Style Load ============ */
add_action('wp_enqueue_scripts', 'listingpro_style');
if (!function_exists('listingpro_style')) {
	function listingpro_style()
	{

		wp_enqueue_style('bootstrap', THEME_DIR . '/assets/lib/bootstrap/css/bootstrap.min.css');
		wp_enqueue_style('Magnific-Popup', THEME_DIR . '/assets/lib/Magnific-Popup-master/magnific-popup.css');
		wp_enqueue_style('popup-component', THEME_DIR . '/assets/lib/popup/css/component.css');
		wp_enqueue_style('Font-awesome', THEME_DIR . '/assets/lib/font-awesome/css/font-awesome.min.css');
		wp_enqueue_style('Mmenu', THEME_DIR . '/assets/lib/jquerym.menu/css/jquery.mmenu.all.css');
		wp_enqueue_style('MapBox', THEME_DIR . '/assets/css/mapbox.css');
		wp_enqueue_style('Chosen', THEME_DIR . '/assets/lib/chosen/chosen.css');
		wp_enqueue_style('bootstrap-datetimepicker-css', THEME_DIR . '/assets/css/bootstrap-datetimepicker.min.css');

		global $listingpro_options;
		$app_view_home  = isset($listingpro_options['app_view_home']) ? $listingpro_options['app_view_home'] : '';
		if (is_page($app_view_home) || is_singular('listing') || (is_front_page()) ||  is_tax('listing-category') || is_tax('features') || is_tax('location') || (is_search()  && isset($_GET['post_type'])  && $_GET['post_type'] == 'listing') || is_author()) {
			wp_enqueue_style('Slick-css', THEME_DIR . '/assets/lib/slick/slick.css');
			wp_enqueue_style('Slick-theme', THEME_DIR . '/assets/lib/slick/slick-theme.css');
			wp_enqueue_style('css-prettyphoto', THEME_DIR . '/assets/css/prettyphoto.css');
		}

		if (!is_front_page()) {
			wp_enqueue_style('jquery-ui', THEME_DIR . '/assets/css/jquery-ui.css');
		}
		wp_enqueue_style('icon8', THEME_DIR . '/assets/lib/icon8/styles.min.css');
		wp_enqueue_style('Color', THEME_DIR . '/assets/css/colors.css');
		wp_enqueue_style('custom-font', THEME_DIR . '/assets/css/font.css');
		wp_enqueue_style('fontawesome-all', THEME_DIR . '/assets/lib/fontawesome/css/all.css');
		wp_enqueue_style('Main', THEME_DIR . '/assets/css/main.css');
		wp_enqueue_style('Responsive', THEME_DIR . '/assets/css/responsive.css');
		/* by haroon */
		wp_enqueue_style('select2', THEME_DIR . '/assets/css/select2.css');
		/* end by haroon */
		/* for location */
		wp_enqueue_style('dynamiclocation', THEME_DIR . '/assets/css/city-autocomplete.css');
		wp_enqueue_style('lp-body-overlay', THEME_DIR . '/assets/css/common.loading.css');
		/* end for location */

		//if(is_archive()){
		wp_enqueue_style('bootstrapslider', THEME_DIR . '/assets/lib/bootstrap/css/bootstrap-slider.css');
		//}

		wp_enqueue_style('mourisjs', THEME_DIR . '/assets/css/morris.css');

		wp_enqueue_style('listingpro', STYLESHEET_DIR . '/style.css');
		$mobile_view = lp_theme_option('single_listing_mobile_view');
		if ($mobile_view == 'app_view2' && wp_is_mobile()) {
			wp_enqueue_style('app-view2-styles', THEME_DIR . '/assets/css/app-view2.css');
		}
		if (is_rtl()) {
			wp_enqueue_style('lp-rtl', THEME_DIR . '/assets/css/rtl.css');
		}

		wp_register_style('lp-classic-blog-grid', THEME_DIR . '/assets/css/classic/classic-blog-grid.css');
		wp_register_style('lp-classic-location-grid', THEME_DIR . '/assets/css/classic/classic-city-grid.css');
		wp_register_style('lp-classic-listings-grid', THEME_DIR . '/assets/css/classic/classic-loop.css');
		wp_register_style('lp-classic-footer', THEME_DIR . '/assets/css/classic/classic-footer.css');
		wp_register_style('lp-classic-archive', THEME_DIR . '/assets/css/classic/classic-archive.css');
		wp_register_style('lp-classic-promotional', THEME_DIR . '/assets/css/classic/classic-promotional.css');
		wp_register_style('lp-classic-reviews', THEME_DIR . '/assets/css/classic/classic-reviews.css');
		wp_register_style('lp-classic-call-to-action', THEME_DIR . '/assets/css/classic/classic-call-to-action.css');
	}
}

/* ============== ListingPro Script Load ============ */

add_action('wp_enqueue_scripts', 'listingpro_scripts');
if (!function_exists('listingpro_scripts')) {
	function listingpro_scripts()
	{


		global $listingpro_options;

		wp_enqueue_script('Mapbox', THEME_DIR . '/assets/js/mapbox.js', 'jquery', '', true);
		wp_enqueue_script('Mapbox-leaflet', THEME_DIR . '/assets/js/leaflet.markercluster.js', 'jquery', '', true);
		wp_enqueue_script('leaflet-google', THEME_DIR . '/assets/js/leafleft-google.js', 'jquery', '', true);

		//wp_enqueue_script('Build', THEME_DIR . '/assets/js/build.min.js', 'jquery', '', true);

		wp_enqueue_script('Chosen', THEME_DIR . '/assets/lib/chosen/chosen.jquery.js', 'jquery', '', true);

		wp_enqueue_script('bootstrap', THEME_DIR . '/assets/lib/bootstrap/js/bootstrap.min.js', 'jquery', '', true);

		wp_enqueue_script('Mmenu', THEME_DIR . '/assets/lib/jquerym.menu/js/jquery.mmenu.min.all.js', 'jquery', '', true);

		wp_enqueue_script('magnific-popup', THEME_DIR . '/assets/lib/Magnific-Popup-master/jquery.magnific-popup.min.js', 'jquery', '', true);

		wp_enqueue_script('select2', THEME_DIR . '/assets/js/select2.full.min.js', 'jquery', '', true);

		wp_enqueue_script('popup-classie', THEME_DIR . '/assets/lib/popup/js/classie.js', 'jquery', '', true);

		wp_enqueue_script('modalEffects', THEME_DIR . '/assets/lib/popup/js/modalEffects.js', 'jquery', '', true);
		wp_enqueue_script('2checkout', THEME_DIR . '/assets/js/2co.min.js', 'jquery', '', true);
		wp_enqueue_script('bootstrap-moment', THEME_DIR . '/assets/js/moment.js', 'jquery', '', true);
		wp_enqueue_script('bootstrap-datetimepicker', THEME_DIR . '/assets/js/bootstrap-datetimepicker.min.js', 'jquery', '', true);

		if (class_exists('Redux')) {
			$mapAPI = '';
			$mapAPI  = isset($listingpro_options['google_map_api']) ? $listingpro_options['google_map_api'] : '';
			if (empty($mapAPI)) {
				$mapAPI = 'AIzaSyDQIbsz2wFeL42Dp9KaL4o4cJKJu4r8Tvg';
			}
			wp_enqueue_script('mapsjs', 'https://maps.googleapis.com/maps/api/js?key=' . $mapAPI . '&libraries=places', 'jquery', '', false);
		}
		if (!is_front_page()) {
			wp_enqueue_script('pagination', THEME_DIR . '/assets/js/pagination.js', 'jquery', '', true);
		}
		/* IF ie9 */
		wp_enqueue_script('html5shim', 'https://html5shim.googlecode.com/svn/trunk/html5.js', array(), '1.0.0', true);
		wp_script_add_data('html5shim', 'conditional', 'lt IE 9');

		wp_enqueue_script('nicescroll', THEME_DIR . '/assets/js/jquery.nicescroll.min.js', 'jquery', '', true);
		wp_enqueue_script('chosen-jquery', THEME_DIR . '/assets/js/chosen.jquery.min.js', 'jquery', '', true);
		wp_enqueue_script('jquery-ui', THEME_DIR . '/assets/js/jquery-ui.js', 'jquery', '', true);
		if (is_page_template('template-dashboard.php')) {
			wp_enqueue_script('bootstrap-rating', THEME_DIR . '/assets/js/bootstrap-rating.js', 'jquery', '', true);
		}
		wp_enqueue_script('droppin', THEME_DIR . '/assets/js/drop-pin.js', 'jquery', '', true);
		if (is_singular('listing') || is_singular('events')) {
			wp_enqueue_script('singlemap', THEME_DIR . '/assets/js/singlepostmap.js', 'jquery', '', true);
		}
		if (is_singular('listing')) {
			wp_enqueue_script('socialshare', THEME_DIR . '/assets/js/social-share.js', 'jquery', '', true);
			wp_enqueue_script('jquery-prettyPhoto', THEME_DIR . '/assets/js/jquery.prettyPhoto.js', 'jquery', '', true);
			wp_enqueue_script('bootstrap-rating', THEME_DIR . '/assets/js/bootstrap-rating.js', 'jquery', '', true);
			wp_enqueue_script('Slick', THEME_DIR . '/assets/lib/slick/slick.min.js', 'jquery', '', true);
		}
		if (is_author()) {
			wp_enqueue_script('jquery-prettyPhoto', THEME_DIR . '/assets/js/jquery.prettyPhoto.js', 'jquery', '', true);
		}
		/* ==============start add by sajid ============ */
		global $listingpro_options;
		$app_view_home  = isset($listingpro_options['app_view_home']) ? $listingpro_options['app_view_home'] : '';
		if (is_page($app_view_home) || is_author() || is_tax('location') || (is_front_page()) || is_tax('listing-category') || is_tax('features') || (is_search()
			&& isset($_GET['post_type'])
			&& esc_html($_GET['post_type']) == 'listing'
		)) {
			wp_enqueue_script('Slick', THEME_DIR . '/assets/lib/slick/slick.min.js', 'jquery', '', true);
		}
		/* ==============end add by sajid ============ */
		wp_enqueue_script('dyn-location-js', THEME_DIR . '/assets/js/jquery.city-autocomplete.js', 'jquery', '', true);
		//if(is_archive()){
		wp_enqueue_script('bootstrapsliderjs', THEME_DIR . '/assets/lib/bootstrap/js/bootstrap-slider.js', 'jquery', '', true);
		//}


		wp_register_script('lp-icons-colors', THEME_DIR . '/assets/js/lp-iconcolor.js', 'jquery', '', true);
		wp_enqueue_script('lp-icons-colors');

		wp_register_script('lp-current-loc', THEME_DIR . '/assets/js/lp-gps.js', 'jquery', '', true);
		wp_enqueue_script('lp-current-loc');

		wp_enqueue_script('Pricing', THEME_DIR . '/assets/js/pricing.js', 'jquery', '', true);

		wp_register_script('raphelmin', THEME_DIR . '/assets/js/raphael-min.js', 'jquery', '', false);
		wp_enqueue_script('raphelmin');

		wp_register_script('morisjs', THEME_DIR . '/assets/js/morris.js', 'jquery', '', false);
		wp_enqueue_script('morisjs');

		wp_enqueue_script('Main', THEME_DIR . '/assets/js/main.js', 'jquery', '', true);

		//wp_enqueue_script('fontawesome', 'https://kit.fontawesome.com/abdbf0f360.js', 'jquery', '', true);

		if (is_singular('post') && comments_open()) {
			wp_enqueue_script('comment-reply');
		}

		if (is_rtl()) {
			wp_enqueue_script('lp-rtl-js', THEME_DIR . '/assets/js/rtl.js', 'jquery', '', true);
		}
	}
}

add_action('admin_enqueue_scripts', 'lp_pdffiles_include_action');
add_action('lp_pdf_enqueue_scripts', 'lp_pdffiles_include_action');
if (!function_exists('lp_pdffiles_include_action')) {
	function lp_pdffiles_include_action()
	{
		wp_register_script('lp-pdflib', THEME_DIR . '/assets/js/jspdf.min.js', 'jquery', '', true);
		wp_register_script('lp-pdffunc', THEME_DIR . '/assets/js/pdf-function.js', 'jquery', '', true);
		wp_enqueue_script('lp-pdflib');
		wp_enqueue_script('lp-pdffunc');
	}
}

/* ============== ListingPro Stripe JS ============ */
add_filter('wp_enqueue_scripts', 'listingpro_stripeJsfile', 0);
if (!function_exists('listingpro_stripeJsfile')) {
	function listingpro_stripeJsfile()
	{

		wp_enqueue_script('stripejs', THEME_DIR . '/assets/js/checkout.js', 'jquery', '', false);
	}
}

/* ============== ListingPro Options ============ */
add_action('after_setup_theme', 'listingpro_include_redux_options');
if (!function_exists('listingpro_include_redux_options')) {
	function listingpro_include_redux_options()
	{
		if (!isset($listingpro_options) && file_exists(dirname(__FILE__) . '/include/options-config.php')) {
			require_once(dirname(__FILE__) . '/include/options-config.php');
		}
	}
}

/* ============== ListingPro Load media ============ */
if (!function_exists('listingpro_load_media')) {
	function listingpro_load_media()
	{
		wp_enqueue_media();
	}
}
add_action('admin_enqueue_scripts', 'listingpro_load_media');

if (!function_exists('listingpro_admin_css')) {
	function listingpro_admin_css()
	{
		wp_enqueue_style('adminpages-css', THEME_DIR . '/assets/css/admin-style.css');
	}
}
add_action('admin_enqueue_scripts', 'listingpro_admin_css');

/* ============== ListingPro Author Contact meta ============ */
if (!function_exists('listingpro_author_meta')) {
	function listingpro_author_meta($contactmethods)
	{

		// Add telefone
		$contactmethods['phone'] = 'Phone';
		// add address
		$contactmethods['address'] = 'Adress 1st Line';
		$contactmethods['address2'] = 'Adress 2nd Line';
		$contactmethods['city'] = 'City';
		$contactmethods['zipcode'] = 'Zip Code';
		$contactmethods['state'] = 'State';
		$contactmethods['country'] = 'Country*';
		// add Social
		$contactmethods['facebook'] = 'Facebook';
		$contactmethods['google'] = 'Google';
		$contactmethods['linkedin'] = 'Linkedin';
		$contactmethods['instagram'] = 'Instagram';
		$contactmethods['twitter'] = 'Twitter';
		$contactmethods['pinterest'] = 'Pinterest';

		return $contactmethods;
	}
	add_filter('user_contactmethods', 'listingpro_author_meta', 10, 1);
}


/* ============== ListingPro User avatar URL ============ */

if (!function_exists('listingpro_get_avatar_url')) {
	function listingpro_get_avatar_url($author_id, $size)
	{
		$get_avatar = get_avatar($author_id, $size);
		preg_match("/src='(.*?)'/i", $get_avatar, $matches);
		if (!empty($matches)) {
			if (array_key_exists("1", $matches)) {
				return ($matches[1]);
			}
		}
	}
}

/* ============== ListingPro Author image ============ */

if (!function_exists('listingpro_author_image')) {

	function listingpro_author_image()
	{

		if (is_user_logged_in()) {

			$current_user = wp_get_current_user();

			$author_avatar_url = get_user_meta($current_user->ID, "listingpro_author_img_url", true);

			if (!empty($author_avatar_url)) {

				$avatar =  $author_avatar_url;
			} else {

				$avatar_url = listingpro_get_avatar_url($current_user->ID, $size = '94');
				$avatar =  $avatar_url;
			}
		}


		return $avatar;
	}
}

/* ============== ListingPro Single Author image ============ */

if (!function_exists('listingpro_single_author_image')) {

	function listingpro_single_author_image()
	{

		if (is_single()) {

			$author_avatar_url = get_user_meta(get_the_author_meta('ID'), "listingpro_author_img_url", true);

			if (!empty($author_avatar_url)) {

				$avatar =  $author_avatar_url;
			} else {

				$avatar_url = listingpro_get_avatar_url(get_the_author_meta('ID'), $size = '94');
				$avatar =  $avatar_url;
			}
		}


		return $avatar;
	}
}

/* ============== ListingPro Subscriber can upload media ============ */

if (!function_exists('listingpro_vcSetAsTheme')) {
	add_action('vc_before_init', 'listingpro_vcSetAsTheme');
	function listingpro_vcSetAsTheme()
	{
		vc_set_as_theme($disable_updater = false);
	}
}

/* ============== ListingPro Block admin acccess ============ */
if (!function_exists('listingpro_block_admin_access')) {

	add_action('init', 'listingpro_block_admin_access');

	function listingpro_block_admin_access()
	{
		if (is_user_logged_in()) {



			if (is_multisite()) {

				if (is_admin() && !current_user_can('administrator')  && isset($_GET['action']) != 'delete' && !(defined('DOING_AJAX') && DOING_AJAX)) {
					wp_die(esc_html__("You don't have permission to access this page.", "listingpro"));
					exit;
				}
			} else {

				if (is_admin() && current_user_can('subscriber')  && isset($_GET['action']) != 'delete' && !(defined('DOING_AJAX') && DOING_AJAX)) {
					wp_die(esc_html__("You don't have permission to access this page.", "listingpro"));
					exit;
				}
			}
		}
	}
}



/* ============== ListingPro Media Uploader ============ */

if (!function_exists('listingpro_add_media_upload_scripts')) {

	function listingpro_add_media_upload_scripts()
	{
		if (is_admin()) {
			return;
		}
		wp_enqueue_media();
	}
	//add_action('wp_enqueue_scripts', 'listingpro_add_media_upload_scripts');

}


/* ============== ListingPro Search Form ============ */

if (!function_exists('listingpro_search_form')) {

	function listingpro_search_form()
	{

		$form = '<form role="search" method="get" id="searchform" action="' . esc_url(home_url('/')) . '" >
			<div class="input">
				<i class="icon-search"></i><input class="" type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __('Type and hit enter', 'listingpro') . '">
			</div>
			</form>';

		return $form;
	}
}

add_filter('get_search_form', 'listingpro_search_form');


/* ============== ListingPro Favicon ============ */

if (!function_exists('listingpro_favicon')) {

	function listingpro_favicon()
	{
		global $listingpro_options;
		if (!function_exists('has_site_icon') || !has_site_icon()) {
			$theme_favicon  = isset($listingpro_options['theme_favicon']) ? $listingpro_options['theme_favicon'] : '';
			if ($theme_favicon != '') {

				echo '<link rel="shortcut icon" href="' . wp_kses_post($theme_favicon['url']) . '"/>';
			} else {
				echo '<link rel="shortcut icon" href="' . THEME_DIR . '/assets/img/favicon.ico"/>';
			}
		}
	}
}



/* ============== ListingPro Top bar menu ============ */

if (!function_exists('listingpro_top_bar_menu')) {

	function listingpro_top_bar_menu()
	{
		$defaults = array(
			'theme_location'  => 'top_menu',
			'menu'            => '',
			'container'       => 'false',
			'menu_class'      => 'lp-topbar-menu',
			'menu_id'         => '',
			'echo'            => true,
			'fallback_cb'     => '',
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		);
		if (has_nav_menu('top_menu')) {
			return wp_nav_menu($defaults);
		}
	}
}

/* ============== ListingPro Primary menu ============ */

if (!function_exists('listingpro_primary_menu')) {

	function listingpro_primary_menu()
	{
		$defaults = array(
			'theme_location'  => 'primary',
			'menu'            => '',
			'container'       => 'div',
			'menu_class'      => '',
			'menu_id'         => '',
			'echo'            => false,
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		);
		if (has_nav_menu('primary')) {
			return wp_nav_menu($defaults);
		}
	}
}


/* ============== ListingPro Inner pages menu ============ */

if (!function_exists('listingpro_inner_menu')) {

	function listingpro_inner_menu()
	{
		$defaults = array(
			'theme_location'  => 'primary_inner',
			'menu'            => '',
			'container'       => 'div',
			'menu_class'      => '',
			'menu_id'         => '',
			'echo'            => false,
			'items_wrap'      => '<ul id="%1$s" class="inner_menu %2$s">%3$s</ul>',
		);
		if (has_nav_menu('primary_inner')) {
			return wp_nav_menu($defaults);
		}
	}
}

/* ============== ListingPro Footer menu ============ */

if (!function_exists('listingpro_footer_menu')) {

	function listingpro_footer_menu()
	{
		$defaults = array(
			'theme_location'  => 'footer_menu',
			'menu'            => '',
			'container'       => 'false',
			'menu_class'      => 'footer-menu',
			'menu_id'         => '',
			'echo'            => true,
			'fallback_cb'     => '',
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		);

		if (has_nav_menu('footer_menu')) {
			return wp_nav_menu($defaults);
		}
	}
}


/* ============== ListingPro partypro category menu ============ */

if (!function_exists('listingpro_pp_cat_menu')) {

	function listingpro_pp_cat_menu()
	{
		$defaults = array(
			'theme_location'  => 'pp_cat_menu',
			'menu'            => '',
			'container'       => 'false',
			'menu_class'      => '',
			'menu_id'         => '',
			'echo'            => true,
			'fallback_cb'     => '',
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
			'walker' => new Nav_Bigmenu_Walker()
		);

		if (has_nav_menu('pp_cat_menu')) {
			return wp_nav_menu($defaults);
		}
	}
}

function lp_wp_title($title, $sep)
{
	global $paged, $page;

	if (is_feed()) {
		return $title;
	}

	$title .= get_bloginfo('name');

	$site_description = get_bloginfo('description', 'display');
	if ($site_description && (is_home() || is_front_page())) {
		$title = "$title $sep $site_description";
	}

	if ($paged >= 2 || $page >= 2) {
		$title = "$title $sep " . sprintf(__('Page %s', 'listingpro'), max($paged, $page));
	}

	if (isset($_GET['s']) && isset($_GET['select']) && $_GET['select'] != '') {
		$siteTitle = get_bloginfo();
		$title =  esc_html__("Search Results For", "listingpro") . ' "' . $_GET['select'] . '" - ' . $siteTitle;
	}

	return $title;
}
add_filter('wp_title', 'lp_wp_title', 10, 2);

class Nav_Bigmenu_Walker extends Walker_Nav_Menu
{

	public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
	{
		global $wp_query;
		$indent = ($depth) ? str_repeat("\t", $depth) : '';

		$class_names = $value = '';

		$classes = empty($item->classes) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		if ($depth == 0) {
			$classes[] = 'col-md-4';
		}

		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
		$class_names = ' class="' . esc_attr($class_names) . '"';

		$id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
		$id = strlen($id) ? ' id="' . esc_attr($id) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names . '>';

		$attributes  = !empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) . '"' : '';
		$attributes .= !empty($item->target)     ? ' target="' . esc_attr($item->target) . '"' : '';
		$attributes .= !empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn) . '"' : '';
		$attributes .= !empty($item->url)        ? ' href="'   . esc_attr($item->url) . '"' : '';
		$cat_icon   =   '';
		if ($item->type == 'taxonomy' && $item->object == 'listing-category') {
			$cat_icon = listing_get_tax_meta($item->object_id, 'category', 'image');
		}
		// Check our custom has_children property.
		if ($args->has_children) {
			$attributes .= ' class="menu parent"';
		}

		$item_output = $args->before;
		$item_output .= '<a' . $attributes . '>';
		if ($cat_icon != '' && $depth == 0) {
			if (hasFontAwesomeIconClass($cat_icon)) {
				$item_output .= '<i class="icon icons-search-cat ' . $cat_icon . '"></i>';
			} else {
				$item_output .= '<img alt="image" src="' . $cat_icon . '" />';
			}
		}
		$item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}

	public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
	{
		//new code 2.6.15
		if (empty($depth)) {
			$depth = 0;
		}
		//end new code 2.6.15

		$id_field = $this->db_fields['id'];
		if (is_object($args[0])) {
			$args[0]->has_children = !empty($children_elements[$element->$id_field]);
		}
		return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
	}
}


/* ==============start add by sajid ============ */
if (!function_exists('listingpro_footer_menu_app')) {

	function listingpro_footer_menu_app()
	{
		$defaults = array(
			'theme_location'  => 'footer_menu',
			'menu'            => '',
			'container'       => 'false',
			'menu_class'      => '',
			'menu_id'         => '',
			'echo'            => true,
			'fallback_cb'     => '',
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		);

		if (has_nav_menu('footer_menu')) {
			return wp_nav_menu($defaults);
		}
	}
}

/* ==============end add by sajid ============ */

/* ============== ListingPro Mobile menu ============ */

if (!function_exists('listingpro_mobile_menu')) {

	function listingpro_mobile_menu()
	{
		$defaults = array(
			'theme_location'  => 'mobile_menu',
			'menu'            => '',
			'container'       => 'false',
			'menu_class'      => 'mobile-menu',
			'menu_id'         => '',
			'echo'            => true,
			'fallback_cb'     => '',
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		);

		if (has_nav_menu('mobile_menu')) {
			return wp_nav_menu($defaults);
		}
	}
}

/* ============== ListingPro Default sidebar ============ */

if (!function_exists('listingpro_sidebar')) {

	function listingpro_sidebar()
	{
		global $listingpro_options;
		$footer_style = '';
		if (isset($listingpro_options['footer_style'])) {
			$footer_style = $listingpro_options['footer_style'];
		}

		register_sidebar(array(
			'name' => 'Default sidebar',
			'id' => 'default-sidebar',
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<div class="imo-widget-title-container"><h2 class="widget-title">',
			'after_title' => '</h2></div>',
		));
		register_sidebar(array(
			'name' => 'Listing Detail sidebar',
			'id' => 'listing_detail_sidebar',
			'before_widget' => '<div class="widget-box viewed-listing %2$s" id="%1$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2>',
			'after_title' => '</h2>',
		));
		register_sidebar(array(
			'name' => 'Listing Archive sidebar',
			'id' => 'listing_archive_sidebar',
			'before_widget' => '<div class="widget-box viewed-listing %2$s" id="%1$s">',
			'after_widget' => '</div>',
			'before_title' => '<h2>',
			'after_title' => '</h2>',
		));
		/* ============== shaoib start ============ */

		//if($footer_style == 'footer2'){

		global $listingpro_options;
		if (class_exists('Redux') && isset($listingpro_options) && !empty($listingpro_options)) {
			if (isset($listingpro_options) && !empty($listingpro_options)) {
				$grid = '';
				$fLayout = lp_theme_option('footer_layout');
				$grid = $fLayout != "" ? $fLayout : '2-2-2-2-2-2';
				if (!empty($grid)) {
					$i = 1;
					foreach (explode('-', $grid) as $g) {
						register_sidebar(array(
							'name' => esc_html__("Footer sidebar ", "listingpro") . $i,
							'id' => "footer-sidebar-$i",
							'description' => esc_html__('The footer sidebar widget area', 'listingpro'),
							'before_widget' => '<aside class="widget widgets %2$s" id="%1$s">',
							'after_widget' => '</aside>',
							'before_title' => '<div class="widget-title"><h2>',
							'after_title' => '</h2></div>',

						));
						$i++;
					}
				}
			}
		}
		/* ============== shoaib end ============ */
	}
}
add_action('widgets_init', 'listingpro_sidebar');

/* ============== ListingPro Primary Logo ============ */

if (!function_exists('listingpro_primary_logo')) {

	function listingpro_primary_logo()
	{

		global $listingpro_options;
		$lp_logo = isset($listingpro_options['primary_logo']) ? $listingpro_options['primary_logo'] : '';
		$lp_logo = $lp_logo['url'];
		if (!empty($lp_logo)) {
			echo '<img src="' . $lp_logo . '" alt="image" />';
		} else {
			echo get_option('blogname');
		}
	}
}


/* ============== ListingPro Seconday Logo ============ */

if (!function_exists('listingpro_secondary_logo')) {

	function listingpro_secondary_logo()
	{

		global $listingpro_options;
		$lp_logo2  = isset($listingpro_options['seconday_logo']) ? $listingpro_options['seconday_logo'] : '';
		$lp_logo2 = $lp_logo2['url'];
		if (!empty($lp_logo2)) {
			echo '<img src="' . $lp_logo2 . '" alt="image" />';
		} else {
			echo get_option('blogname');
		}
	}
}

/* ============== ListingPro URL Settings ============ */

if (!function_exists('listingpro_url')) {

	function listingpro_url($link)
	{
		global $listingpro_options;
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		if (class_exists('ListingproPlugin')) {
			$url = '';
			if ($link == 'add_listing_url_mode') {
				//$url = $listingpro_options[$link];
				$paidmode = $listingpro_options['enable_paid_submission'];
				if ($paidmode == "per_listing" || $paidmode == "membership") {
					$url = $listingpro_options['pricing-plan'];
				} else {
					$url = $listingpro_options['submit-listing'];
				}
			} else {
				if (isset($listingpro_options[$link])) {
					$url = $listingpro_options[$link];
				}
			}

			/* for wpml compatibility */
			// if (defined('ICL_LANGUAGE_CODE')) {
			// 	$url = $url . '?lang=' . ICL_LANGUAGE_CODE;
			// }
			$pageID = url_to_postid($url);
			if ($pageID) {
				$url = get_permalink($pageID);
			}

			return $url;
		} else {
			return false;
		}
	}
}

/* ============== ListingPro translation ============ */

if (!function_exists('listingpro_translation')) {

	function listingpro_translation($word)
	{


		return $word;
	}
}

/* ============== ListingPro filter page pagination ============ */

if (!function_exists('listingpro_load_more_filter')) {

	function listingpro_load_more_filter($my_query, $pageno = null, $sKeyword = '')
	{

		$output = '';
		$pages = '';
		$pages = $my_query->max_num_pages;
		$totalpages = $pages;
		$ajax_pagin_classes =   'pagination lp-filter-pagination-ajx';
		if (is_author()) {
			$ajax_pagin_classes =   '';
		}
		if (!empty($pages) && $pages > 1) {
			$output .= '<div class="lp-pagination ' . $ajax_pagin_classes . '">';
			$output .= '<ul class="page-numbers">';
			$n = 1;
			$flagAt = 7;
			$flagAt2 = 7;
			$flagOn = 0;
			while ($pages > 0) {

				if (isset($pageno) && !empty($pageno)) {

					if (!empty($totalpages) && $totalpages < 7) {
						if ($pageno == $n) {
							$output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers haspaglink current">' . $n . '</span></li>';
						} else {
							$output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers haspaglink">' . $n . '</span></li>';
						}
					} elseif (!empty($totalpages) && $totalpages > 6) {
						$flagOn = $pageno - 5;
						$flagOn2 = $pageno + 7;
						if ($pageno == $n) {
							$output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers haspaglink current">' . $n . '</span></li>';
						} else {
							if ($n <= 4) {
								$output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers haspaglink">' . $n . '</span></li>';
							} elseif ($n > 4 && $flagAt2 == 7) {
								$output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers haspaglink">' . $n . '</span></li>';
								$output .= '<li><span data-skeyword="' . $sKeyword . '"  class="page-numbers">...</span></li>';
								$flagAt2 = 1;
							} elseif ($n > 4  && $n >= $flagOn && $n < $flagOn2) {
								$output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers haspaglink">' . $n . '</span></li>';
							} elseif ($n == $totalpages) {
								$output .= '<li><span data-skeyword="' . $sKeyword . '" class="page-numbers">...</span></li>';
								$output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers haspaglink">' . $n . '</span></li>';
							}
						}
					}
				} else {

					if ($n == 1) {
						$output .= '<li><span data-pageurl="' . $n . '"  class="page-numbers  haspaglink current">' . $n . '</span></li>';
					} else if ($n < 7) {
						$output .= '<li><span data-pageurl="' . $n . '"  class="page-numbers haspaglink">' . $n . '</span></li>';
					} else if ($n > 7 && $pages > 7 && $flagAt == 7) {
						$output .= '<li><span  class="page-numbers">...</span></li>';
						$flagAt = 1;
					} else if ($n > 7 && $pages < 7 && $flagAt == 1) {
						$output .= '<li><span data-pageurl="' . $n . '"  class="page-numbers haspaglink">' . $n . '</span></li>';
					}
				}

				$pages--;
				$n++;
				$output .= '</li>';
			}
			$output .= '</ul>';
			$output .= '</div>';
		}


		return $output;
	}
}


/* ============== ListingPro Infinite load ============ */

if (!function_exists('listingpro_load_more')) {

	function listingpro_load_more($wp_query)
	{
		$pages = $wp_query->max_num_pages;
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		if (empty($pages)) {
			$pages = 1;
		}

		if (1 != $pages) {

			$big = 9999; // need an unlikely integer
			echo "
				<div class='lp-pagination pagination lp-filter-pagination'>";

			$pagination = paginate_links(
				array(
					'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
					'end_size' => 3,
					'mid_size' => 6,
					'format' => '?paged=%#%',
					'current' => max(1, get_query_var('paged')),
					'total' => $wp_query->max_num_pages,
					'type' => 'list',
					'prev_text' => __('&laquo;', 'listingpro'),
					'next_text' => __('&raquo;', 'listingpro'),
				)
			);
			echo wp_kses_post($pagination);
			echo "</div>";
		}
	}
}


/* ============== ListingPro Icon8 base64 Icons ============ */

if (!function_exists('listingpro_icons')) {

	function listingpro_icons($icons)
	{
		$colors = new listingproIcons();
		$icon = '';
		if ($icons != '') {
			$iconsrc = $colors->listingpro_icon($icons);
			$icon = '<img class="icon icons8-' . $icons . '" src="' . $iconsrc . '" alt="' . $icons . '">';
			return $icon;
		} else {
			return $icon;
		}
	}
}

/* ============== ListingPro Icon8 base64 Icons url ============ */

if (!function_exists('listingpro_icons_url')) {
	function listingpro_icons_url($icons)
	{
		$colors = new listingproIcons();
		$icon = '';
		if ($icons != '') {
			$iconsrc = $colors->listingpro_icon($icons);
			return $iconsrc;
		} else {
			return $iconsrc;
		}
	}
}


/* ============== ListingPro Search Filter ============ */

if (!function_exists('listingpro_searchFilter')) {


	function listingpro_searchFilter()
	{
		global $wp_post_types;
		$wp_post_types['page']->exclude_from_search = true;
	}
	add_action('init', 'listingpro_searchFilter');
}


/* ============== ListingPro Price Dynesty Text============ */

if (!function_exists('listingpro_price_dynesty_text')) {
	function listingpro_price_dynesty_text($postid)
	{
		$output = null;
		if (!empty($postid)) {
			$priceRange = listing_get_metabox_by_ID('price_status', $postid);
			//$listingptext = listing_get_metabox('list_price_text');
			$listingprice = listing_get_metabox_by_ID('list_price', $postid);
			if (!empty($priceRange) && !empty($listingprice)) {
				$output .= '
					<span class="element-price-range list-style-none">';
				$dollars = '';
				$tip = '';
				if ($priceRange == 'notsay') {
					$dollars = '';
					$tip = '';
				} elseif ($priceRange == 'inexpensive') {
					$dollars = '1';
					$tip = esc_html__('Inexpensive', 'listingpro');
				} elseif ($priceRange == 'moderate') {
					$dollars = '2';
					$tip = esc_html__('Moderate', 'listingpro');
				} elseif ($priceRange == 'pricey') {
					$dollars = '3';
					$tip = esc_html__('Pricey', 'listingpro');
				} elseif ($priceRange == 'ultra_high_end') {
					$dollars = '4';
					$tip = esc_html__('Ultra High End', 'listingpro');
				}
				global $listingpro_options;
				$lp_priceSymbol  = isset($listingpro_options['listing_pricerange_symbol']) ? $listingpro_options['listing_pricerange_symbol'] : '';
				if ($priceRange != 'notsay') {
					$output .= '<span class="grayscale simptip-position-top simptip-movable" data-tooltip="' . $tip . '">';
					for ($i = 0; $i < $dollars; $i++) {
						$output .= $lp_priceSymbol;
					}
					$output .= '</span>';
				}
				$output .= '
					</span>';
			}
		}
		return $output;
	}
}

/* ============== ListingPro Price Dynesty ============ */

if (!function_exists('listingpro_price_dynesty')) {
	function listingpro_price_dynesty($postid)
	{
		if (!empty($postid)) {
			$priceRange = listing_get_metabox_by_ID('price_status', $postid);
			$listingpTo = listing_get_metabox('list_price_to');
			$listingprice = listing_get_metabox_by_ID('list_price', $postid);
			if (($priceRange != 'notsay' && !empty($priceRange)) || !empty($listingpTo) || !empty($listingprice)) {
?>
				<div class="post-row price-range">
					<ul class="list-style-none post-price-row line-height-16">
						<?php if ($priceRange != 'notsay' && !empty($priceRange)) { ?>
							<li class="grayscale-dollar">
								<?php
								$dollars = '';
								$tip = '';
								if ($priceRange == 'notsay') {
									$dollars = '';
									$tip = '';
								} elseif ($priceRange == 'inexpensive') {
									$dollars = '1';
									$tip = esc_html__('Inexpensive', 'listingpro');
								} elseif ($priceRange == 'moderate') {
									$dollars = '2';
									$tip = esc_html__('Moderate', 'listingpro');
								} elseif ($priceRange == 'pricey') {
									$dollars = '3';
									$tip = esc_html__('Pricey', 'listingpro');
								} elseif ($priceRange == 'ultra_high_end') {
									$dollars = '4';
									$tip = esc_html__('Ultra High End', 'listingpro');
								}

								global $listingpro_options;
								$lp_priceSymbol  = isset($listingpro_options['listing_pricerange_symbol']) ? $listingpro_options['listing_pricerange_symbol'] : '';


								echo '<span class="simptip-position-top simptip-movable" data-tooltip="' . $tip . '">';
								echo '<span class="active">';
								for ($i = 0; $i < $dollars; $i++) {
									echo wp_kses_post($lp_priceSymbol);
								}
								echo '</span>';

								echo '<span class="grayscale">';
								$greyDollar = 4 - (float) $dollars;
								for ($i = 1; $i <= $greyDollar; $i++) {
									echo wp_kses_post($lp_priceSymbol);
								}
								echo '</span>';
								echo '</span>';

								?>
							</li>
						<?php
						}
						if (!empty($listingpTo) || !empty($listingprice)) {
						?>
							<li>
								<span class="post-rice">
									<span class="text">
										<?php echo esc_html__('Price Range', 'listingpro'); ?>
									</span>
									<?php

									if (!empty($listingprice)) {
										echo esc_html($listingprice);
									}
									if (!empty($listingpTo)) {
										echo ' - ';
										echo esc_html($listingpTo);
									}


									?>
								</span>
							</li>
						<?php
						}
						?>
					</ul>
				</div>
		<?php
			}
		}
	}
}

/* ============== ListingPro email and  callbacks ============ */
if (!function_exists('listingpro_mail_from')) {
	function listingpro_mail_from($old)
	{

		$mailFrom = null;
		if (class_exists('Redux')) {
			global $listingpro_options;
			$mailFrom = $listingpro_options['listingpro_general_email_address'];
		} else {
			$mailFrom = get_option('admin_email');
		}
		return $mailFrom;
	}
}

if (!function_exists('listingpro_mail_from_name')) {
	function listingpro_mail_from_name($old)
	{

		$mailFromName = null;
		if (class_exists('Redux')) {
			global $listingpro_options;
			$mailFromName = $listingpro_options['listingpro_general_email_from'];
		} else {
			$mailFromName = get_option('blogname');
		}
		return $mailFromName;
	}
}

/* ============== email html support ============ */
if (!function_exists('listingpro_set_content_type')) {
	add_filter('wp_mail_content_type', 'listingpro_set_content_type');
	function listingpro_set_content_type($content_type)
	{
		return 'text/html';
	}
}

/* ==================textarea to editor============= */

if (!function_exists('get_textarea_as_editor')) {
	function get_textarea_as_editor($editor_id, $editor_name, $pcontent)
	{
		$content = $pcontent;
		$settings = array(
			'wpautop' => true,
			'textarea_name' => $editor_name,
			'textarea_rows' => 8,
			'media_buttons' => false,
			'tinymce' => array(
				'theme_advanced_buttons1' => '',
				'theme_advanced_buttons2' => false,
				'theme_advanced_buttons3' => false,
				'theme_advanced_buttons4' => false,
			),
			'quicktags' => false,
		);
		ob_start();
		wp_editor($content, $editor_id, $settings);
		$output = ob_get_contents();
		ob_end_clean();
		ob_flush();
		return $output;
	}
}

/* ================= button in editor=========== */

add_filter('tiny_mce_before_init', 'lp_format_TinyMCE');
if (!function_exists('lp_format_TinyMCE')) {
	function lp_format_TinyMCE($in)
	{
		if (!is_admin()) {
			$in['toolbar'] = 'formatselect,|,bold,italic,underline,|,' .
				'bullist,numlist,blockquote,|,alignjustify' .
				',|,link,unlink,|' .
				',spellchecker,';
			$in['toolbar1'] = '';
			$in['toolbar2'] = '';
			return $in;
		} else {
			return $in;
		}
	}
}

/* ============== Listingpro term Exist ============ */

if (!function_exists('listingpro_term_exist')) {
	function listingpro_term_exist($name, $taxonomy)
	{
		$term = term_exists($name, $taxonomy);
		if (!empty($term)) {
			return $term;
		} else {
			return 0;
		}
	}
}

/* ============== Listingpro add new term ============ */

if (!function_exists('listingpro_insert_term')) {
	function listingpro_insert_term($name, $taxonomy)
	{
		if (!taxonomy_exists($taxonomy)) {
			return 0;
		} else {
			$term = term_exists($name, $taxonomy);
			if (!empty($term)) {
				return 0;
			} else {
				$loc = wp_insert_term($name, $taxonomy);
				if (is_wp_error($loc)) {
					return 0;
				} else {
					return $loc;
				}
			}
		}
	}
}
if (!function_exists('listingpro_get_campaigns_listing_sidebar')) {
	function listingpro_get_campaigns_listing_sidebar($campaign_type, $IDSonly, $taxQuery = array(), $searchQuery = array(), $priceQuery = array(), $s = null, $noOfListings = null, $posts_in = null)
	{
		$Clistingid =  '';
		if (is_singular('listing')) {
			global $post;
			$Clistingid = $post->ID;
		}
		global $listingpro_options;
		$listing_mobile_view = $listingpro_options['single_listing_mobile_view'];
		$adsType = array(
			'lp_random_ads',
			'lp_detail_page_ads',
			'lp_top_in_search_page_ads'
		);
		global $listingpro_options;
		$listing_style = '';
		$listing_style = $listingpro_options['listing_style'];
		$postNumber = '';
		if ($listing_style == '3' && !is_front_page()) {
			if (empty($noOfListings)) {
				$postNumber = 2;
			} else {
				$postNumber = $noOfListings;
			}
		} elseif ($listing_style == '4' && !is_front_page()) {
			if (empty($noOfListings)) {
				$postNumber = 2;
			} else {
				$postNumber = $noOfListings;
			}
		} else {
			if (empty($noOfListings)) {
				$postNumber = 3;
			} else {
				$postNumber = $noOfListings;
			}
		}
		if (!empty($campaign_type)) {
			if (in_array($campaign_type, $adsType, true)) {
				$TxQuery = array();
				if (!empty($taxQuery) && is_array($taxQuery)) {
					$TxQuery = $taxQuery;
				} elseif (!empty($searchQuery) && is_array($searchQuery)) {
					$TxQuery = $searchQuery;
				}
				$args_ads = array(
					'orderby' => 'rand',
					'post_type' => 'lp-ads',
					'post_status' => 'publish',
					'posts_per_page' => -1,
					//						'tax_query' => $TxQuery,
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'   => 'lp_listingpro_options',
							'value'  => 'lp_detail_page_ads',
							'compare' => 'LIKE',
						),
						//							$priceQuery
					),
				);
				$tehe_query = new WP_Query($args_ads);
				$adsidsArray = array();
				if ($tehe_query->have_posts()) {
					while ($tehe_query->have_posts()) {
						$tehe_query->the_post();
						//$idsArray[] = get_the_ID();
						$listing_ad_id = listing_get_metabox_by_ID('ads_listing', get_the_ID());
						$status = get_post_meta($listing_ad_id, 'campaign_status')[0];
						if ($status === 'active' && $listing_ad_id != $Clistingid) {
							$adsidsArray[] = $listing_ad_id;
						}
						wp_reset_postdata();
						if (count($adsidsArray) == $postNumber) {
							break;
						}
					}
				}
				$args = array(
					'orderby' => 'rand',
					'post_type' => 'listing',
					'post_status' => 'publish',
					'post__not_in'	=> array($Clistingid),
				);
				if (count($adsidsArray) > 0) {
					$args['post__in'] = $adsidsArray;
				} else {
					return;
				}
				$idsArray = array();
				$the_query = new WP_Query($args);
				if ($the_query->have_posts()) {
					while ($the_query->have_posts()) {
						$the_query->the_post();
						//$idsArray[] = get_the_ID();
						if (in_array(get_the_ID(), $adsidsArray)) {
							get_template_part('templates/details-page-ads');
						}
						wp_reset_postdata();
					}
				}
			}
		}
	}
}

/* ============== Listingpro compaigns ============ */
if (!function_exists('listingpro_get_campaigns_listing')) {
	function listingpro_get_campaigns_listing($campaign_type, $IDSonly, $taxQuery = array(), $searchQuery = array(), $priceQuery = array(), $s = null, $noOfListings = null, $posts_in = null)
	{
		global $lp_ad_ids;
		$Clistingid =   '';
		if (is_singular('listing')) {
			global $post;
			$Clistingid = $post->ID;
		}
		global $listingpro_options;
		$listing_mobile_view = $listingpro_options['single_listing_mobile_view'];

		$adsType = array(
			'lp_random_ads',
			'lp_detail_page_ads',
			'lp_top_in_search_page_ads'
		);

		global $listingpro_options;
		$listing_style = '';
		$listing_style = $listingpro_options['listing_style'];
		$postNumber = '';
		if ($listing_style == '3' && !is_front_page()) {
			if (empty($noOfListings)) {
				$postNumber = 2;
			} else {
				$postNumber = $noOfListings;
			}
		} elseif ($listing_style == '4' && !is_front_page()) {
			if (empty($noOfListings)) {
				$postNumber = 2;
			} else {
				$postNumber = $noOfListings;
			}
		} else {
			if (empty($noOfListings)) {
				$postNumber = 3;
			} else {
				$postNumber = $noOfListings;
			}
		}

		if (is_front_page()) {
			$listing_campaign_status = array(
				'key'     => 'lp_random_ads',
				'value'   => 'active',
				'compare' => '=',
			);
		} else if (is_singular('listing')) {
			$listing_campaign_status = array(
				'key'     => 'lp_detail_page_ads',
				'value'   => 'active',
				'compare' => '=',
			);
		} else {
			$listing_campaign_status = array(
				'key'     => 'lp_top_in_search_page_ads',
				'value'   => 'active',
				'compare' => '=',
			);
		}

		if (!empty($campaign_type)) {
			if (in_array($campaign_type, $adsType, true)) {

				$TxQuery = array();
				if (!empty($taxQuery) && is_array($taxQuery)) {
					$TxQuery = $taxQuery;
				} elseif (!empty($searchQuery) && is_array($searchQuery)) {
					$TxQuery = $searchQuery;
				}
				$args = array(
					'orderby' => 'rand',
					'post_type' => 'listing',
					'post_status' => 'publish',
					'posts_per_page' => $postNumber,
					'post__not_in'	=> array($Clistingid),
					'tax_query' => $TxQuery,
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'     => 'campaign_status',
							'value'   => array('active'),
							'compare' => 'IN',
						),
						$priceQuery,
						$listing_campaign_status
					),
				);
				if (!empty($s)) {
					$args = array(
						'orderby' => 'rand',
						'post_type' => 'listing',
						'post_status' => 'publish',
						's' => $s,
						'posts_per_page' => $postNumber,
						'tax_query' => $TxQuery,
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'     => 'campaign_status',
								'value'   => array('active'),
								'compare' => 'IN',
							),
							$priceQuery,
							$listing_campaign_status
						),
					);
				}

				if (!empty($posts_in)) {
					$args['post__in'] = $posts_in;
				}

				$idsArray = array();
				$lp_ad_ids = array();
				$the_query = new WP_Query($args);
				if ($the_query->have_posts()) {
					while ($the_query->have_posts()) {
						$the_query->the_post();
						$lp_ad_ids[] =  get_the_ID();
						if ($IDSonly == true) {
							$idsArray[] =  get_the_ID();
						} else {
							if (is_singular('listing')) {
								if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
									echo  '<div class="row app-view-ads lp-row-app">';
									get_template_part('mobile/listing-loop-app-view');
									echo '</div>';
								} else {
									get_template_part('templates/details-page-ads');
								}
							} elseif ((is_page()  || is_home() || is_singular('post')) &&  (is_active_sidebar('default-sidebar') || is_active_sidebar('listing_archive_sidebar'))) {
								get_template_part('templates/details-page-ads');
							} elseif (is_singular('post')) {
								get_template_part('templates/details-page-ads');
							} else {
								$listing_mobile_view = isset($listingpro_options['single_listing_mobile_view']) ? $listingpro_options['single_listing_mobile_view'] : '';
								if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
									get_template_part('mobile/listing-loop-app-view');
								} elseif ($listing_mobile_view == 'app_view2' && wp_is_mobile()) {
									get_template_part('mobile/listing-loop-app-view-adds');
								} else {
									if (isset($GLOBALS['sidebar_add_loop']) && $GLOBALS['sidebar_add_loop'] == 'yes') {
										get_template_part('templates/details-page-ads');
									} else {
										get_template_part('listing-loop');
									}
								}
							}
						}

						wp_reset_postdata();
					}
					if ($IDSonly == true) {
						if (!empty($idsArray)) {
							return $idsArray;
						}
					}
				}
			}
		}
	}
}

/* ============== Listingpro Sharing ============ */
if (!function_exists('listingpro_sharing')) {
	function listingpro_sharing()
	{
		?>
		<a class="reviews-quantity">
			<span class="reviews-stars">
				<i class="fa fa-share-alt"></i>
			</span>
			<?php echo esc_html__('Share', 'listingpro'); ?>
		</a>
		<div class="md-overlay hide"></div>
		<ul class="social-icons post-socials smenu">
			<li>
				<a href="<?php echo listingpro_social_sharing_buttons('facebook'); ?>" target="_blank">
					<!-- Facebook icon by Icons8 -->
					<i class="fa fa-facebook"></i>
				</a>
			</li>
			<li>
				<a href="<?php echo listingpro_social_sharing_buttons('twitter'); ?>" target="_blank">
					<!-- twitter icon by Icons8 -->
					<i class="fa-brands fa-square-x-twitter"></i>
				</a>
			</li>
			<li>
				<a href="<?php echo listingpro_social_sharing_buttons('linkedin'); ?>" target="_blank">
					<!-- linkedin icon by Icons8 -->
					<i class="fa fa-linkedin"></i>
				</a>
			</li>
			<li>
				<a href="<?php echo listingpro_social_sharing_buttons('pinterest'); ?>" target="_blank">
					<!-- pinterest icon by Icons8 -->
					<i class="fa fa-pinterest"></i>
				</a>
			</li>
			<li>
				<a href="<?php echo listingpro_social_sharing_buttons('reddit'); ?>" target="_blank">
					<!-- reddit icon by Icons8 -->
					<i class="fa fa-reddit"></i>
				</a>
			</li>
		</ul>
		<?php
	}
}

/* Post Views */

if (!function_exists('getPostViews')) {
	function getPostViews($postID)
	{
		$count_key = 'post_views_count';
		$count = get_post_meta($postID, $count_key, true);
		if ($count == '' || $count == '0') {
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '0');
			return esc_html__('0 View', 'listingpro');
		} else {
			if (!empty($count)) {
				if ($count == "1") {
					return $count . esc_html__(' View', 'listingpro');
				} else {
					return $count . esc_html__(' Views', 'listingpro');
				}
			} else {
				return $count . esc_html__('0 View', 'listingpro');
			}
		}
	}
}

// function to count views.
if (!function_exists('setPostViews')) {
	function setPostViews($postID)
	{
		$currID = get_current_user_id();
		$authorID = get_post_field('post_author', $postID);
		if ($authorID != $currID) {
			$count_key = 'post_views_count';
			$count = get_post_meta($postID, $count_key, true);
			if ($count == '') {
				$count = 0;
				delete_post_meta($postID, $count_key);
				add_post_meta($postID, $count_key, '0');
			} else {
				$count++;
				update_post_meta($postID, $count_key, $count);
			}
		} else {

			$count_key = 'post_views_count';
			$count = get_post_meta($postID, $count_key, true);
			if ($count == '' || empty($count)) {
				$count = 0;
				delete_post_meta($postID, $count_key);
				add_post_meta($postID, $count_key, '0');
			}
		}
	}
}

// function to get all post meta value by keys
if (!function_exists('getMetaValuesByKey')) {
	function getMetaValuesByKey($key)
	{
		global $wpdb;
		$metaVal = $wpdb->get_col("SELECT meta_value
		FROM $wpdb->postmeta WHERE meta_key = '$key'");
		return $metaVal;
	}
}

// function to get total views
if (!function_exists('getTotalPostsViews')) {
	function getTotalPostsViews()
	{
		$totalCount = 0;
		$totalArray = getMetaValuesByKey('post_views_count');
		if (!empty($totalArray) && is_array($totalArray)) {
			foreach ($totalArray as $count) {
				$totalCount = $totalCount + $count;
			}
		}
		return $totalCount;
	}
}

// function to get author listing total views
if (!function_exists('getAuthorPostsViews')) {
	function getAuthorPostsViews()
	{
		$count = 0;
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;

		$args = array(
			'post_type' => 'listing',
			'author' => $user_id,
			'post_status' => 'publish',
			'posts_per_page' => -1
		);
		$the_query = new WP_Query($args);
		if ($the_query->have_posts()) {
			while ($the_query->have_posts()) {
				$the_query->the_post();
				$n = get_post_meta(get_the_ID(), 'post_views_count', true);
				$count = $count + (int)$n;
			}
			wp_reset_postdata();
		}
		return $count;
	}
}

// function to get author listing total reviews
if (!function_exists('getAuthorTotalViews')) {
	function getAuthorTotalViews()
	{
		$count = 0;
		$review_ids = '';
		$result = array();
		$review_new = array();
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		$review_ids = array();

		$args = array(
			'post_type' => 'listing',
			'author' => $user_id,
			'post_status' => 'publish',
			'posts_per_page' => -1
		);
		$the_query = new WP_Query($args);
		if ($the_query->have_posts()) {
			while ($the_query->have_posts()) {
				$the_query->the_post();
				$key = 'reviews_ids';
				$review_idss = listing_get_metabox_by_ID($key, get_the_ID());

				if (!empty($review_idss)) {
					if (strpos($review_idss, ",") !== false) {
						$review_ids = explode(',', $review_idss);
						$result = array_merge($result, $review_ids);
					} else {
						$result[] = $review_idss;
					}
				}
			}
			wp_reset_postdata();
			$count = $count + count($result);
		}
		return $count;
	}
}

//function to get all reviews in array on author's posts
if (!function_exists('getAllReviewsArray')) {
	function getAllReviewsArray($submitted = false)
	{
		$review_ids = '';
		$result = array();
		$review_new = array();
		$review_idss = '';
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		if (!empty($submitted)) {
			/* submitted */
			$post_type = "lp-reviews";
		} else {
			/* received */
			$post_type = "lp-reviews";
			$result = array('0' => 0);
			$user_id = '';
		}

		$postid = array();

		$args = array(
			'post_type' => $post_type,
			'author' => $user_id,
			'post_status' => 'publish',
			'posts_per_page' => -1
		);
		$lp_the_query = null;
		$lp_the_query = new WP_Query($args);
		if ($lp_the_query->have_posts()) {
			while ($lp_the_query->have_posts()) {
				$lp_the_query->the_post();

				if (!empty($submitted)) {
					/* submitted */
					$result[get_the_ID()] = get_the_ID();
				} else {
					/* received */

					$listing_id = listing_get_metabox_by_ID('listing_id', get_the_ID());
					if (!empty($listing_id)) {
						$l_author_id = get_post_field('post_author', $listing_id);
						if ($l_author_id == get_current_user_id()) {
							$result[get_the_ID()] = get_the_ID();
						}
					}

					$key = 'reviews_ids';

					$review_idss = listing_get_metabox_by_ID($key, get_the_ID());

					if (!empty($review_idss)) {
						if (strpos($review_idss, ",") !== false) {
							$review_ids = explode(',', $review_idss);
							$result = array_merge($result, $review_ids);
						} else {
							$result[] = $review_idss;
						}
					}
				}
			}
			wp_reset_postdata();
		}
		return $result;
	}
}


/*========================================get ads invoices list============================================*/
//function to retreive invoices
if (!function_exists('get_ads_invoices_list')) {
	function get_ads_invoices_list($userid, $method, $status)
	{
		global $wpdb;
		$prefix = '';
		$prefix = $wpdb->prefix;
		$table_name = $prefix . 'listing_campaigns';

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {

			if (empty($userid)  && !empty($method) && !empty($status) && is_admin()) {
				//return on admin side only
				$results = $wpdb->get_results(
					$wpdb->prepare("SELECT * FROM {$prefix}listing_campaigns WHERE payment_method=%s AND status=%s ORDER BY main_id DESC", $method, $status)
				);
				return $results;
			} else if (!empty($userid) && isset($userid) && !empty($status)) {
				//return for all users by id

				$results = $wpdb->get_results(
					$wpdb->prepare("SELECT * FROM {$prefix}listing_campaigns WHERE user_id=%d AND status=%s ORDER BY main_id DESC", $userid, $status)
				);
				return $results;
			}
		}
	}
}

/*==============================get listing invoices==================================*/
//function to get invoices list
if (!function_exists('get_invoices_list')) {
	function get_invoices_list($userid, $method, $status)
	{
		global $wpdb;
		$prefix = '';
		$prefix = $wpdb->prefix;
		$table_name = $prefix . 'listing_orders';

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {

			if (empty($userid)  && !empty($method) && !empty($status) && is_admin()) {
				//return on admin side
				$results = $wpdb->get_results(
					$wpdb->prepare("SELECT * FROM {$prefix}listing_orders WHERE payment_method=%s AND status=%s ORDER BY main_id DESC", $method, $status)
				);
				return $results;
			} else if (!empty($userid) && isset($userid) && !empty($status) && !is_admin()) {
				//return on front side

				$results = $wpdb->get_results(
					$wpdb->prepare("SELECT * FROM {$prefix}listing_orders WHERE user_id=%d AND status=%s ORDER BY main_id DESC", $userid, $status)
				);
				return $results;
			}
			if (!empty($userid) && isset($userid) && empty($status) && !is_admin()) {
				//return on front side

				$results = $wpdb->get_results(
					$wpdb->prepare("SELECT * FROM {$prefix}listing_orders WHERE user_id=%d AND (status=%s OR status=%s OR status=%s) ORDER BY main_id DESC", $userid, 'pending', 'success', 'expired')
				);
				return $results;
			}
		}
	}
}

/*==============================delete post action==================================*/
// function to delete post action
if (!function_exists('lp_delete_any_post')) {
	add_action('before_delete_post', 'lp_delete_any_post');
	function lp_delete_any_post($postid)
	{
		global $post_type;

		if ($post_type == 'listing') {
			$listing_id = $postid;
			$campaignID = listing_get_metabox_by_ID('campaign_id', $listing_id);
			$get_reviews = listing_get_metabox_by_ID('reviews_ids', $listing_id);

			wp_delete_post($campaignID);
			if (!empty($get_reviews)) {
				$reviewsArray = array();
				if (strpos($get_reviews, ',') !== false) {
					$reviewsArray = explode(",", $get_reviews);
				} else {
					$reviewsArray[] = $get_reviews;
				}
				$args = array(
					'posts_per_page'      => -1,
					'post__in'            => $reviewsArray,
					'post_type' => 'lp-reviews',
				);
				$query = new WP_Query($args);
				if ($query->have_posts()) {
					while ($query->have_posts()) {
						$query->the_post();
						wp_delete_post(get_the_ID());
					}
				}
			}
		}
		// else if($post_type == 'lp-reviews'){

		// 	$review_id = $postid;
		// 	$action = 'delete';
		// 	$listing_id = listing_get_metabox_by_ID('listing_id', $postid);

		// 	listingpro_set_listing_ratings($review_id, $listing_id, '', $action);

		//     if(!empty($listing_id)){
		//         $total_reviewed = get_post_meta( $listing_id, 'listing_reviewed', true );
		//         if ( ! empty( $total_reviewed ) ) {
		//             $total_reviewed--;
		//             update_post_meta( $listing_id, 'listing_reviewed', $total_reviewed );
		//         }
		//     }

		// }
		else if ($post_type == 'lp-ads') {
			$listing_id = listing_get_metabox_by_ID('ads_listing', $postid);
			$ad_type = listing_get_metabox_by_ID('ad_type', $postid);
			if (!empty($ad_type) && is_array($ad_type)) {
				foreach ($ad_type as $type) {
					delete_post_meta($listing_id, $type);
				}
			}

			listing_delete_metabox('campaign_id', $listing_id);
			delete_post_meta($listing_id, 'campaign_status');
		}
	}
}

//=======================================================
//						Pagination
//=======================================================
if (!function_exists('listingpro_pagination')) {

	function listingpro_pagination($wp_query = array())
	{
		if (empty($wp_query)) {
			global $wp_query;
		}

		$pages = $wp_query->max_num_pages;
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		if (empty($pages)) {
			$pages = 1;
		}

		if (1 != $pages) {

			$big = 9999; // need an unlikely integer
			echo "
			<div class='lp-pagination pagination'>";
			$pagination = paginate_links(
				array(
					'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
					'end_size' => 3,
					'mid_size' => 6,
					'format' => '?paged=%#%',
					'current' => max(1, get_query_var('paged')),
					'total' => $wp_query->max_num_pages,
					'type' => 'list',
					'prev_text' => __('&laquo;', 'listingpro'),
					'next_text' => __('&raquo;', 'listingpro'),
				)
			);
			echo wp_kses_post($pagination);
			echo "</div>";
		}
	}
}

//=======================================================
//						Login Screen
//=======================================================
if (!function_exists('listingpro_login_screen')) {
	function listingpro_login_screen()
	{
		wp_enqueue_style('listable-custom-login', get_template_directory_uri() . '/assets/css/login-page.css');
		wp_enqueue_style('Font-awesome', THEME_DIR . '/assets/lib/font-awesome/css/font-awesome.min.css');
	}

	add_action('login_enqueue_scripts', 'listingpro_login_screen');
}
/*====================================================================================*/

/*====================================================================================*/
/* calculate average rate for listing */
if (!function_exists('lp_cal_listing_rate')) {
	function lp_cal_listing_rate($listing_id, $post_type = 'listing', $is_reviewcall = false)
	{

		global $listingpro_options;
		$reviewEnabled  = isset($listingpro_options['lp_review_switch']) ? $listingpro_options['lp_review_switch'] : '';
		if ($post_type == 'lp_review') {
			$rating = listing_get_metabox_by_ID('rating', $listing_id);
		} else {
			$rating = get_post_meta($listing_id, 'listing_rate', true);
		}
		$rating = apply_filters('lp_rating_number_format', $rating);
		$ratingRes = '';
		if (!empty($rating) && $rating > 0) {
			if ($rating < 1) {
				$ratingRes = '<span class="rate lp-rate-worst">' . $rating . '<sup>/ 5</sup></span>';
			} else if ($rating >= 1 && $rating < 2) {
				$ratingRes = '<span class="rate lp-rate-bad">' . $rating . '<sup>/ 5</sup></span>';
			} else if ($rating >= 2 && $rating < 3.5) {
				$ratingRes = '<span class="rate lp-rate-satisfactory">' . $rating . '<sup>/ 5</sup></span>';
			} else if ($rating >= 3.5 && $rating <= 5) {
				$ratingRes = '<span class="rate lp-rate-good">' . $rating . '<sup>/ 5</sup></span>';
			}
		} else {
			if (class_exists('ListingReviews')) {
				if (is_singular('listing')) {

					if ($is_reviewcall == true) {
						$ratingRes = '';
					} else {
						if (get_post_status($listing_id) != 'publish') {
							$ratingRes = '<span class="no-review">' . esc_html__("Rating only enabled on published listing", "listingpro") . '</span>';
						} else {
							if ($reviewEnabled == "1") {
								$ratingRes = '<span class="no-review">' . esc_html__("Be the first one to rate!", "listingpro") . '</span>';
							}
						}
					}
				} else {
					//$ratingRes = '<span class="no-review">'.esc_html__("0 Review", "listingpro").'</span>';
				}
			}
		}

		return $ratingRes;
	}
}


/* =============================================== cron-job for renew listing==================================== */
add_action('wp', 'lp_renew_listing_subcription');
if (!function_exists('lp_renew_listing_subcription')) {
	function lp_renew_listing_subcription()
	{
		wp_clear_scheduled_hook('lp_daily_renew_listing_subcription');
		if (!wp_next_scheduled('lp_daily_renew_listings_subcription')) {
			$timestamp = strtotime('23:30:00');
			wp_schedule_event($timestamp, 'daily', 'lp_daily_renew_listings_subcription');
		}
	}
}
add_action('lp_daily_renew_listings_subcription', 'lp_renew_this_listing');
if (!function_exists('lp_renew_this_listing')) {
	function lp_renew_this_listing()
	{
		global $wpdb, $listingpro_options;
		$listingpro_options = get_option('listingpro_options');
		include_once(LISTINGPRO_PLUGIN_PATH . "inc/stripe/stripe-php/init.php");
		$strip_sk = $listingpro_options['stripe_secrit_key'];
		Stripe::setApiKey($strip_sk);
		$users = get_users(array('fields' => array('ID')));
		foreach ($users as $user_id) {
			$user_id = $user_id->ID;
			$user_obj = get_user_by('id', $user_id);
			$user_login = $user_obj->user_login;
			$userSubscriptions = '';
			$userSubscriptions = get_user_meta($user_id, 'listingpro_user_sbscr', true);
			if (!empty($userSubscriptions) && count($userSubscriptions) > 0) {
				$subscription_exist = true;
				$n = 1;
				foreach ($userSubscriptions as $key => $subscription) {
					$subscr_id = $subscription['subscr_id'];
					$taxStatus = '';
					if (strpos($subscr_id, 'sub_') !== false && !isset($subscription['method'])) {
						/* stripe */
						try {
							$plan_id = $subscription['plan_id'];
							$subscr_id = $subscription['subscr_id'];
							$listing_id = $subscription['listing_id'];
							$subscrObj = Subscription::retrieve($subscr_id);

							$LpgetStatusListing = get_post_status($listing_id);
							if (empty($LpgetStatusListing)) {
								// expire subscription if listing is deleted
								$subscrObj->cancel();
								unset($userSubscriptions[$key]);
							} else {
								if ($subscrObj->status == 'canceled') {
									unset($userSubscriptions[$key]);
								}
							}

							$subscrID = $subscrObj->id;

							$plan_time = get_post_meta($plan_id, 'plan_time', true);
							$plan_time = (int)$plan_time;

							$isActive = true;
							$isCharged = true;
							$isValid = true;
							$Curdifernce = null;

							if (!empty($subscrObj)) {

								$subStatus = $subscrObj->plan->active;
								$subEnds = $subscrObj->current_period_end;

								if (!empty($subStatus)) {
									if (date('Y-m-d') == date('Y-m-d', $subEnds)) {
										// is active and today is renewal day
										$lpCurrentTime = current_time('mysql');
										$my_listing = array('ID' => $listing_id, 'post_date' => $lpCurrentTime, 'post_date_gmt' => get_gmt_from_date($lpCurrentTime), 'post_status' => 'publish');
										wp_update_post($my_listing);

										$table = 'listing_orders';

										$wherecond = 'post_id="' . $listing_id . '" AND status = "success" AND summary="recurring"';
										$recurringData = lp_get_data_from_db('listing_orders', '*', $wherecond);
										if (!empty($recurringData)) {
											$main_id = '';
											foreach ($recurringData as $data) {
												$main_id = $data->main_id;
												$date = date(get_option('date_format'));
												$data = array('date' => $date);
												$where = array('main_id' => $main_id);
												lp_update_data_in_db($table, $data, $where);
											}
										}
									}
								} else {
									// not active
									$my_listing = array('ID' => $listing_id, 'post_status' => 'expired');
									wp_update_post($my_listing);
								}
							}
						} catch (Exception $e) {
						}
					} else {
						/* paypal */
						$subscrObj = lp_retreive_recurring_profile($subscr_id);
						$plan_id = $subscription['plan_id'];
						$listing_id = $subscription['listing_id'];
						$LpgetStatusListing = get_post_status($listing_id);
						if (empty($LpgetStatusListing)) {
							// expire subscription if listing is deleted
							lp_cancel_recurring_profile($subscr_id);
							unset($userSubscriptions[$key]);
						}
						$plan_time = get_post_meta($plan_id, 'plan_time', true);
						$plan_time = (int)$plan_time;
						$isActive = true;
						$isCharged = true;
						$isValid = true;
						$Curdifernce = null;
						if (!empty($subscrObj)) {
							$subStatus = $subscrObj['STATUS'];
							$subEnds = strtotime($subscrObj['LASTPAYMENTDATE']);
							if (!empty($subStatus)) {
								if (date('Y-m-d') == date('Y-m-d', $subEnds)) {
									// is active and today is renewal day
									$lpCurrentTime = current_time('mysql');
									$my_listing = array('ID' => $listing_id, 'post_date' => $lpCurrentTime, 'post_date_gmt' => get_gmt_from_date($lpCurrentTime), 'post_status' => 'publish');
									wp_update_post($my_listing);
									$table = 'listing_orders';
									$wherecond = 'post_id="' . $listing_id . '" AND status = "success" AND summary="recurring"';
									$recurringData = lp_get_data_from_db('listing_orders', '*', $wherecond);
									if (!empty($recurringData)) {
										$main_id = '';
										foreach ($recurringData as $data) {
											$main_id = $data->main_id;
											$date = date(get_option('date_format'));
											$data = array('date' => $date);
											$where = array('main_id' => $main_id);
											lp_update_data_in_db($table, $data, $where);
										}
									}
								}
							} else {
								// not active
								$my_listing = array('ID' => $listing_id, 'post_status' => 'expired');
								wp_update_post($my_listing);
							}
						}
					}
				}
				if (!empty($userSubscriptions)) {
					update_user_meta($user_id, 'listingpro_user_sbscr', $userSubscriptions);
				} else {
					delete_user_meta($user_id, 'listingpro_user_sbscr');
				}
			}
		}
	}
}


/* =============================================== cron-job for listing==================================== */
add_action('wp', 'lp_expire_listings');
if (!function_exists('lp_expire_listings')) {
	function lp_expire_listings()
	{
		wp_clear_scheduled_hook('lp_daily_cron_listing');
		if (!wp_next_scheduled('lp_daily_cron_listings')) {
			$timestamp = strtotime('23:30:00');
			wp_schedule_event($timestamp, 'daily', 'lp_daily_cron_listings');
		}
	}
}
add_action('lp_daily_cron_listings', 'lp_expire_this_listing');
if (!function_exists('lp_expire_this_listing')) {
	function lp_expire_this_listing()
	{
		global $wpdb, $listingpro_options;

		$listingpro_options = get_option('listingpro_options');

		$dbprefix = $wpdb->prefix;
		$uid = get_current_user_id();
		$args = array(
			'post_type' => 'listing',
			'post_status' => 'publish',
			'posts_per_page' => 1000,
			'meta_query' => array(
				'key' => 'lp_listingpro_options',
				'value' => 'lp_purchase_days',
				'compare' => 'LIKE'
			),
		);

		$wp_query = null;
		$wp_query = new WP_Query($args);

		if ($wp_query->have_posts()) {
			while ($wp_query->have_posts()) : $wp_query->the_post();
				$listing_id = get_the_ID();
				$checkIfRecurriong = lp_listing_has_subscriptn($listing_id);
				$plan_id = listing_get_metabox_by_ID('Plan_id', $listing_id);
				$plan_price = listing_get_metabox_by_ID('plan_price', $listing_id);

				/* attach plan to expiry listing if on from theme option */
				$planAttach = lp_theme_option('lp_assignplanexpirybutton');
				$nplanid = '';
				if ($planAttach == "enable") {
					$nplanid = lp_theme_option('lp_plan_after_expire');
				}

				if (!empty($plan_id)) {
					$plan_duration  = listing_get_metabox_by_ID('lp_purchase_days', $listing_id);

					if (!empty($plan_duration) && empty($checkIfRecurriong)) {
						$sql = "UPDATE {$wpdb->posts} SET `post_status` = 'expired' WHERE (ID = '$listing_id' AND `post_type` = 'listing' AND `post_status` = 'publish')	AND DATEDIFF(NOW(), `post_date`) >= %d";
						$plan_duration = (int) $plan_duration;
						$res = $wpdb->query($wpdb->prepare($sql, $plan_duration));
						if ($res != false) {
							if (!empty($nplanid)) {
								//assign plan
								$time = current_time('mysql');
								listing_set_metabox('Plan_id', $nplanid, $listing_id);
								$plan_time = get_post_meta($nplanid, 'plan_time', true);
								listing_set_metabox('lp_purchase_days', $plan_time, $listing_id);
								$this_listing = array(
									'ID'           => $listing_id,
									'post_status'   => 'publish',
									'post_date'     => $time,
									'post_date_gmt' => get_gmt_from_date($time)
								);
								wp_update_post($this_listing);
							}

							if (!empty($plan_price) && is_numeric($plan_price)) {
								/* update in db table */
								$update_data = array('status' => 'in progress');
								$where = array('post_id' => $listing_id);
								$update_format = array('%s');
								$wpdb->update($dbprefix . 'listing_orders', $update_data, $where, $update_format);
								/* update in db table */
							}

							$campaign_status = get_post_meta($listing_id, 'campaign_status', true);
							if (!empty($campaign_status)) {
								delete_post_meta($listing_id, 'campaign_status');
							}
							$adID = listing_get_metabox_by_ID('campaign_id', $listing_id);
							if (!empty($adID)) {
								wp_delete_post($adID, true);
							}

							$post_author_id = get_post_field('post_author', $listing_id);
							$user = get_user_by('id', $post_author_id);
							$useremail = $user->user_email;
							$user_name = $user->user_login;

							$website_url = site_url();
							$website_name = get_option('blogname');
							$listing_title = get_the_title($listing_id);
							$listing_url = get_the_permalink($listing_id);
							/* email to user */
							$headers[] = 'Content-Type: text/html; charset=UTF-8';

							$u_mail_subject_a = '';
							$u_mail_body_a = '';
							$u_mail_subject  = isset($listingpro_options['listingpro_subject_listing_expired']) ? $listingpro_options['listingpro_subject_listing_expired'] : '';
							$u_mail_body  = isset($listingpro_options['listingpro_listing_expired']) ? $listingpro_options['listingpro_listing_expired'] : '';

							$u_mail_subject_a = lp_sprintf2("$u_mail_subject", array(
								'website_url' => "$website_url",
								'listing_title' => "$listing_title",
								'listing_url' => "$listing_url",
								'website_name' => "$website_name",
								'user_name' => "$user_name",
							));

							$u_mail_body_a = lp_sprintf2("$u_mail_body", array(
								'website_url' => "$website_url",
								'listing_title' => "$listing_title",
								'listing_url' => "$listing_url",
								'user_name' => "$user_name",
								'website_name' => "$website_name"
							));
							lp_mail_headers_append();
							LP_send_mail($useremail, $u_mail_subject_a, $u_mail_body_a, $headers);
							lp_mail_headers_remove();
						}
					}
				}
			endwhile;
		}
	}
}


/* =============================================== if lisitng has subscription========================= */

if (!function_exists('lp_listing_has_subscriptn')) {
	function lp_listing_has_subscriptn($listingid)
	{

		$resultCHeck = false;
		$users = get_users(array('fields' => array('ID')));
		foreach ($users as $user_ids) {
			$user_id = $user_ids->ID;
			$userSubscriptions = get_user_meta($user_id, 'listingpro_user_sbscr', true);

			if (!empty($userSubscriptions)) {
				foreach ($userSubscriptions as $subscription) {

					$listing_id = $subscription['listing_id'];
					if ($listingid == $listing_id) {
						$resultCHeck = true;
					}
				}
			}
		}

		return $resultCHeck;
	}
}


/* =============================================== cron-job for recurring email ==================================== */

add_action('wp', 'lp_payment_cron_alert_email');
if (!function_exists('lp_payment_cron_alert_email')) {
	function lp_payment_cron_alert_email()
	{
		wp_clear_scheduled_hook('lp_payments_cron_alets');
		if (!wp_next_scheduled('lp_payment_cron_alets')) {
			$timestamp = strtotime('23:30:00');
			wp_schedule_event($timestamp, 'daily', 'lp_payment_cron_alets');
		}
	}
}
add_action('lp_payment_cron_alets', 'lp_notify_payment_recurring');

if (!function_exists('lp_notify_payment_recurring')) {
	function lp_notify_payment_recurring()
	{
		global $wpdb;
		$listingpro_options = get_option('listingpro_options');
		$lp_nofify;
		if (isset($listingpro_options['lp_recurring_notification_before'])) {
			$lp_nofify = $listingpro_options['lp_recurring_notification_before'];
			$lp_nofify = trim($lp_nofify);
			$lp_nofify = (int)$lp_nofify;
		} else {
			$lp_nofify = 2;
		}
		$wherecond = 'status = "success" AND summary="recurring"';
		$recurringData = lp_get_data_from_db('listing_orders', '*', $wherecond);
		if (!empty($recurringData)) {
			foreach ($recurringData as $data) {
				$plan_id = $data->plan_id;
				$plan_id = trim($plan_id);
				$listing_id = $data->post_id;
				$listing_id = trim($listing_id);
				$user_id = $data->user_id;
				$user_id = trim($user_id);

				$plan_title = get_the_title($plan_id);
				$listing_title = get_the_title($listing_id);

				$plan_price = get_post_meta($plan_id, 'plan_price', true);
				$plan_time = get_post_meta($plan_id, 'plan_time', true);

				if (is_numeric($plan_time)) {
					$currentTime = date("Y-m-d");
					$publishedTime = get_the_time('Y-m-d', $listing_id);
					$currentTime = date_create($currentTime);
					$publishedTime = date_create($publishedTime);
					$interval = date_diff($currentTime, $publishedTime);
					/*2 days before plan end*/
					$plan_duration = $plan_time;
					$plan_time = (int)$plan_time - $lp_nofify;
					$daysDiff = $interval->days;
					if ($daysDiff == $plan_time) {

						$author_obj = get_user_by('id', $user_id);
						$author_email = $author_obj->user_email;

						$website_url = site_url();
						$website_name = get_option('blogname');
						$user_name = $author_obj->user_login;

						$headers[] = 'Content-Type: text/html; charset=UTF-8';

						/* user email */
						$subject  = isset($listingpro_options['listingpro_subject_recurring_payment']) ? $listingpro_options['listingpro_subject_recurring_payment'] : '';
						$mail_content  = isset($listingpro_options['listingpro_content_recurring_payment']) ? $listingpro_options['listingpro_content_recurring_payment'] : '';

						$formated_mail_content = lp_sprintf2("$mail_content", array(
							'website_url' => "$website_url",
							'website_name' => "$website_name",
							'user_name' => "$user_name",
							'listing_title' => "$listing_title",
							'plan_title' => "$plan_title",
							'plan_price' => "$plan_price",
							'plan_duration' => "$plan_duration",
							'notifybefore' => "$lp_nofify"
						));
						lp_mail_headers_append();
						LP_send_mail($author_email, $subject, $formated_mail_content, $headers);
						lp_mail_headers_remove();

						/* admin email */
						$admin_email = get_option('admin_email');

						$subjectadmin  = isset($listingpro_options['listingpro_subject_recurring_payment_admin']) ? $listingpro_options['listingpro_subject_recurring_payment_admin'] : '';
						$mail_content_admin  = isset($listingpro_options['listingpro_content_recurring_payment_admin']) ? $listingpro_options['listingpro_content_recurring_payment_admin'] : '';;

						$formated_mail_content_admin = lp_sprintf2("$mail_content_admin", array(
							'website_url' => "$website_url",
							'website_name' => "$website_name",
							'user_name' => "$user_name",
							'listing_title' => "$listing_title",
							'plan_title' => "$plan_title",
							'plan_price' => "$plan_price",
							'plan_duration' => "$plan_duration",
							'notifybefore' => "$lp_nofify"
						));
						lp_mail_headers_append();
						LP_send_mail($admin_email, $subjectadmin, $formated_mail_content_admin, $headers);
						lp_mail_headers_remove();
					}
				}
			}
		}
	}
}


/* =============================================== getClosestTimezone ==================================== */

if (!function_exists('getClosestTimezone')) {
	function getClosestTimezone($lat, $lng)
	{
		if (!empty($lat) && !empty($lng)) {
			$diffs = array();
			foreach (DateTimeZone::listIdentifiers() as $timezoneID) {
				$timezone = new DateTimeZone($timezoneID);
				$location = $timezone->getLocation();
				$tLat = $location['latitude'];
				$tLng = $location['longitude'];
				$diffLat = abs($lat - $tLat);
				$diffLng = abs($lng - $tLng);
				$diff = $diffLat + $diffLng;
				$diffs[$timezoneID] = $diff;
			}

			$timezone = array_keys($diffs, min($diffs));
			$timestamp = time();
			date_default_timezone_set($timezone[0]);
			$zones_GMT = date('O', $timestamp) / 100;
			return $zones_GMT;
		}
	}
}

/* js for invoice print */
if (!function_exists('lp_call_invoice_print_preview')) {
	function lp_call_invoice_print_preview()
	{
		wp_enqueue_script('lp-print-invoice', THEME_DIR . '/assets/js/jQuery.print.js', 'jquery', '', true);
	}
}
add_action('lp_enqueue_print_script', 'lp_call_invoice_print_preview');

/* check for receptcha */
if (!function_exists('lp_check_receptcha')) {
	function lp_check_receptcha($type)
	{

		global $listingpro_options;
		if (isset($listingpro_options['lp_recaptcha_switch'])) {
			if ($listingpro_options['lp_recaptcha_switch'] == 1) {

				if (isset($listingpro_options["$type"])) {
					if ($listingpro_options["$type"] == 1) {
						return true;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

/* check if package has purchased and has credit */
if (!function_exists('lp_check_package_has_credit')) {
	function lp_check_package_has_credit($plan_id)
	{
		global $listingpro_options, $wpdb;
		$dbprefix = '';
		$dbprefix = $wpdb->prefix;
		$user_ID = get_current_user_id();
		$plan_type = '';
		$plan_type = get_post_meta($plan_id, 'plan_package_type', true);
		$planPrice = get_post_meta($plan_id, 'plan_price', true);
		if (!empty($plan_type) && $plan_type == "Package") {
			$results = $wpdb->get_results("SELECT * FROM " . $dbprefix . "listing_orders WHERE user_id ='$user_ID' AND plan_id='$plan_id' AND status = 'success' AND plan_type='$plan_type'");
			if (!empty($results) && count($results) > 0 && !empty($planPrice)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}


/* get used listing in package*/
if (!function_exists('lp_get_used_listing_in_package')) {
	function lp_get_used_listing_in_package($plan_id)
	{
		global $listingpro_options, $wpdb;
		$used = 0;
		$dbprefix = '';
		$dbprefix = $wpdb->prefix;
		$user_ID = get_current_user_id();
		$plan_type = get_post_meta($plan_id, 'plan_package_type', true);
		if (!empty($plan_type) && $plan_type == "Package") {
			$results = $wpdb->get_results("SELECT * FROM " . $dbprefix . "listing_orders WHERE user_id ='$user_ID' AND plan_Id='$plan_id' AND plan_type='$plan_type' AND status = 'success'");
			if (!empty($results) && is_array($results)) {
				foreach ($results as $info) {
					$used = $info->used;
				}
			}
		}
		return $used;
	}
}

/* check if listing is purchased and pending*/
if (!function_exists('lp_if_listing_in_purchased_package')) {
	function lp_if_listing_in_purchased_package($plan_id, $listing_id)
	{
		global $wpdb;
		$postsIds = '';
		$postsIdsArray = array();
		$dbprefix = '';
		$dbprefix = $wpdb->prefix;
		$user_ID = get_current_user_id();
		$plan_type = get_post_meta($plan_id, 'plan_package_type', true);
		if (!empty($plan_type) && $plan_type == "Package") {
			$results = $wpdb->get_results("SELECT * FROM " . $dbprefix . "listing_orders WHERE user_id ='$user_ID' AND plan_Id='$plan_id' AND plan_type='$plan_type' AND (status = 'success' OR status = 'expired')");
			if (!empty($results) && count($results) > 0) {
				foreach ($results as $info) {
					$postsIds .= $info->post_id;
				}
			}
		}
		if (!empty($postsIds)) {
			$postsIdsArray = explode(",", $postsIds);
			if (in_array($listing_id, $postsIdsArray)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}


/* package update credit */
if (!function_exists('lp_update_credit_package')) {
	function lp_update_credit_package($listing_id, $plan_id = false)
	{
		global $listingpro_options, $wpdb;
		$listing_ids = '';
		$used = 0;
		$returnVal = false;
		$dbprefix = '';
		$dbprefix = $wpdb->prefix;
		$user_ID = get_current_user_id();
		if (empty($plan_id)) {
			$plan_id = listing_get_metabox_by_ID('Plan_id', $listing_id);
		}
		$plan_type = get_post_meta($plan_id, 'plan_package_type', true);
		$posts_allowed_in_plan = get_post_meta($plan_id, 'plan_text', true);
		if (!empty($plan_type) && $plan_type == "Package") {
			$packageHasCredit = lp_check_package_has_credit($plan_id);
			if (!empty($packageHasCredit) && $packageHasCredit == "1") {

				$results = $wpdb->get_results("SELECT * FROM " . $dbprefix . "listing_orders WHERE user_id ='$user_ID' AND plan_Id='$plan_id' AND plan_type='$plan_type' AND status = 'success'");
				if (!empty($results) && count($results) > 0) {
					foreach ($results as $info) {
						$used = $info->used;
						$listing_ids = $info->post_id;
					}
					if (!empty($listing_ids)) {
						$listing_ids = $listing_ids . ',' . $listing_id;
					} else {
						$listing_ids = $listing_id;
					}

					if ($used < $posts_allowed_in_plan) {
						$used++;
						$update_data = array('post_id' => $listing_ids, 'used' => $used);
						$where = array('user_id' => $user_ID, 'plan_id' => $plan_id, 'plan_type' => $plan_type, 'status' => 'success');
						$update_format = array('%s', '%s');
						$wpdb->update($dbprefix . 'listing_orders', $update_data, $where, $update_format);
						$returnVal = true;
					}

					if ($used == $posts_allowed_in_plan) {
						$update_data = array();
						$update_data = array('status' => 'expired');
						$where = array('user_id' => $user_ID, 'plan_id' => $plan_id, 'plan_type' => $plan_type, 'status' => 'success');
						$update_format = array('%s');
						$wpdb->update($dbprefix . 'listing_orders', $update_data, $where, $update_format);
					}
				}
			}
		}

		return $returnVal;
	}
}

/* change plan button */
if (!function_exists('listingpro_change_plan_button')) {
	function listingpro_change_plan_button($post, $listing_id = '')
	{
		global $listingpro_options;
		$buttonEnabled  = isset($listingpro_options['lp_listing_change_plan_option']) ? $listingpro_options['lp_listing_change_plan_option'] : '';
		if ($buttonEnabled == "enable") {
			$currency = listingpro_currency_sign();
			$buttonCode = '';
			$havePlan = "no";
			$planPrice = '';
			$listing_status = '';
			if (empty($listing_id)) {
				$listing_id = $post->ID;
				$listing_status =  get_post_status($listing_id);
				$plan_id = listing_get_metabox_by_ID('Plan_id', $listing_id);
				$planTitle = '';
				if (!empty($plan_id)) {
					$planTitle = get_the_title($plan_id);
					$planPrice = get_post_meta($plan_id, 'plan_price', true);
					if (!empty($planPrice)) {
						$planPrice = $currency . $planPrice;
					} else {
						$planPrice = esc_html__('Free', 'listingpro');
					}
					$planPrice .= '/<small>' . get_post_meta($plan_id, 'plan_package_type', true) . '</small>';
					$havePlan = "yes";
				} else {
					$planTitle = esc_html__('No Plan Assigned Yet', 'listingpro');
				}
				$buttonCode = '<a href="#" class="lp-review-btn btn-second-hover text-center lp-change-plan-btn" data-toggle="modal" data-target="#modal-packages" data-listingstatus="' . $listing_status . '" data-planprice="' . $planPrice . '"  data-haveplan="' . $havePlan . '" data-plantitle = "' . $planTitle . '" data-listingid="' . $listing_id . '" title="change"><i class="fa fa-paper-plane" aria-hidden="true"></i>' . esc_html__('Change Plan', 'listingpro') . '</a>';
			} else {
				$listing_id = $post->ID;
				$listing_status =  get_post_status($listing_id);
				$plan_id = listing_get_metabox_by_ID('Plan_id', $listing_id);
				$planTitle = '';
				if (!empty($plan_id)) {
					$planPrice = get_post_meta($plan_id, 'plan_price', true);
					if (!empty($planPrice)) {
						$planPrice = $currency . $planPrice;
					} else {
						$planPrice = esc_html__('Free', 'listingpro');
					}
					$planTitle = get_the_title($plan_id);
					$planpkgtype = '';
					$plantype = get_post_meta($plan_id, 'plan_package_type', true);
					if ($plantype == "Package") {
						$planpkgtype = esc_html__('Package', 'listingpro');
					} else {
						$planpkgtype = esc_html__('Pay Per Listing', 'listingpro');
					}
					$planPrice .= '/<small>' . $planpkgtype . '</small>';
					$havePlan = "yes";
				} else {
					$planTitle = esc_html__('No Plan Assigned Yet', 'listingpro');
				}
				$buttonCode = '<a href="#" class="lp-review-btn btn-second-hover text-center lp-change-plan-btn" data-toggle="modal" data-target="#modal-packages" data-listingstatus="' . $listing_status . '"  data-planprice="' . $planPrice . '"  data-haveplan="' . $havePlan . '" data-plantitle = "' . $planTitle . '" data-listingid="' . $listing_id . '" title="change"><i class="fa fa-paper-plane" aria-hidden="true"></i>' . esc_html__('Change Plan', 'listingpro') . '</a>';
			}


			global $listingpro_options;
			$paidmode  = isset($listingpro_options['enable_paid_submission']) ? $listingpro_options['enable_paid_submission'] : '';
			if (!empty($paidmode) && $paidmode == "yes") {
				return $buttonCode;
			} else {
				return;
			}
		}
	}
}

/* listingpro get payments status of listing */
if (!function_exists('lp_get_payment_status_column')) {
	function lp_get_payment_status_column($listing_id)
	{
		global $wpdb;
		$returnStatus = '';
		$table_name = $wpdb->prefix . 'listing_orders';
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
			$field_name = 'status';
			$prepared_statement = $wpdb->prepare("SELECT {$field_name} FROM {$table_name} WHERE  post_id = %d", $listing_id);
			$values = $wpdb->get_col($prepared_statement);
			$discounted = get_post_meta($listing_id, 'discounted', true);
			if (!empty($values)) {
				if ($values[0] == "success" && $discounted == '') {
					$returnStatus = esc_html__('Success', 'listingpro');
				} else if ($discounted == 'yes') {
					$returnStatus = esc_html__('Success (100% Discounted)', 'listingpro');
				} else {
					$plan_id = listing_get_metabox_by_ID('Plan_id', $listing_id);
					if (!empty($plan_id)) {
						$plan_price = get_post_meta($plan_id, 'plan_price', true);
						if (!empty($plan_price)) {
							$returnStatus = esc_html__('Pending', 'listingpro');
						} else {
							$returnStatus = esc_html__('Free', 'listingpro');
						}
					} else {
						$returnStatus = esc_html__('Free', 'listingpro');
					}
				}
			} else {
				$returnStatus = esc_html__('Free', 'listingpro');
			}
		}
		return $returnStatus;
	}
}

/* listingpro get payments status of listing by id */
if (!function_exists('lp_get_payment_status_by_ID')) {
	function lp_get_payment_status_by_ID($listing_id)
	{
		global $wpdb;
		$returnStatus = '';
		$table_name = $wpdb->prefix . 'listing_orders';
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
			$field_name = 'status';
			$prepared_statement = $wpdb->prepare("SELECT {$field_name} FROM {$table_name} WHERE  post_id = %d", $listing_id);
			$values = $wpdb->get_col($prepared_statement);
			if (!empty($values)) {
				if ($values[0] == "success") {
					$returnStatus = 'success';
				} else {
					$plan_id = listing_get_metabox_by_ID('Plan_id', $listing_id);
					if (!empty($plan_id)) {
						$plan_price = get_post_meta($plan_id, 'plan_price', true);
						if (!empty($plan_price)) {
							$returnStatus = 'pending';
						} else {
							$returnStatus = 'free';
						}
					} else {
						$returnStatus = 'free';
					}
				}
			} else {
				$returnStatus = 'free';
			}
		}
		return $returnStatus;
	}
}


/* lp count user campaign by id */
if (!function_exists('lp_count_user_campaigns')) {
	function lp_count_user_campaigns($userid)
	{
		$count = 0;
		$args = array(
			'post_type' => 'lp-ads',
			'posts_per_page' => -1,
			'post_status' => 'publish'
		);
		$the_query = new WP_Query($args);
		if ($the_query->have_posts()) {
			while ($the_query->have_posts()) {
				$the_query->the_post();
				$listingID = listing_get_metabox_by_ID('ads_listing', get_the_ID());
				$listing_author = get_post_field('post_author', $listingID);
				if ($userid == $listing_author) {
					$count++;
				}
			}
			wp_reset_postdata();
		}
		return ($count) ? $count : 0;
	}
}

/* count no.of post by user id */
if (!function_exists('count_user_posts_by_status')) {
	function count_user_posts_by_status($post_type = 'listing', $post_status = 'publish', $user_id = 0, $userListing = false)
	{
		global $wpdb;
		$count = 0;
		$where = "WHERE post_type = '$post_type' AND post_status = '$post_status' AND post_author = '$user_id'";
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts $where");
		return apply_filters('get_usernumposts', $count, $user_id, $post_type, true);
	}
}


if (!function_exists('reviews_sum_against_author_listings')) {
	function reviews_sum_against_author_listings($author, $listing_status)
	{
		if (empty($author)) {
			return $counter =   0;
		} else {
			if (empty($listing_status)) {
				$listing_status =   'publish';
			}
			$args = array(
				'post_type' => 'listing',
				'post_status' => $listing_status,
				'posts_per_page' => -1,
				'author' => $author,
			);

			$my_query = null;
			$my_query = new WP_Query($args);
			$count_reviews  =   array();
			if ($my_query->have_posts()) : while ($my_query->have_posts()) : $my_query->the_post();
					global $post;
					$review_idss = listing_get_metabox_by_ID('reviews_ids', $post->ID);
					if (!empty($review_idss)) {
						$review_ids = explode(",", $review_idss);
						$count_reviews[]  =   count($review_ids);
					}
				endwhile;
				wp_reset_postdata();
			endif;
			return array_sum($count_reviews);
		}
	}
}

/* check user reviews by user id and listing id */
if (!function_exists('lp_check_user_reviews_for_listing')) {
	function lp_check_user_reviews_for_listing($uid, $listing_id)
	{
		$returnVal = false;
		if (!empty($uid) && !empty($listing_id)) {

			$args = array(
				'post_type'  => 'lp-reviews',
				'post_status'	=> 'publish',
				'author' => $uid,
				'posts_per_page' => -1,

			);
			$query = new WP_Query($args);
			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
					$listingid = listing_get_metabox_by_ID('listing_id', get_the_ID());
					if ($listingid == $listing_id) {
						$returnVal = true;
					}
				}
				wp_reset_postdata();
			}
		} else {
			$returnVal = false;
		}
		return $returnVal;
	}
}

/* adding new user meta for new subscription */
if (!function_exists('lp_add_new_susbcription_meta')) {
	function lp_add_new_susbcription_meta($new_susbcription)
	{
		if (!empty($new_susbcription)) {
			$uid = get_current_user_id();
			$existing_subsc = get_user_meta($uid, 'listingpro_user_sbscr', true);
			if (!empty($existing_subsc)) {
				array_push($existing_subsc, $new_susbcription);
				update_user_meta($uid, 'listingpro_user_sbscr', $existing_subsc);
			} else {
				$new_subsc[] = $new_susbcription;
				update_user_meta($uid, 'listingpro_user_sbscr', $new_subsc);
			}
		}
	}
}

/* cancel subscription from stripe */
if (!function_exists('lp_cancel_stripe_subscription')) {
	function lp_cancel_stripe_subscription($listing_id, $plan_id)
	{
		if (!empty($plan_id) && !empty($listing_id)) {
			global $listingpro_options;
			include_once(LISTINGPRO_PLUGIN_PATH . "inc/stripe/stripe-php/init.php");
			$secritKey = $listingpro_options['stripe_secrit_key'];
			Stripe::setApiKey("$secritKey");

			$uid = get_current_user_id();
			$userSubscriptions = get_user_meta($uid, 'listingpro_user_sbscr', true);
			if (!empty($userSubscriptions)) {
				foreach ($userSubscriptions as $key => $subscriptions) {
					$subc_listing_id = $subscriptions['listing_id'];
					$subc_plan_id = $subscriptions['plan_id'];
					$subc_id = $subscriptions['subscr_id'];
					if (($subc_listing_id == $listing_id) && ($subc_plan_id == $plan_id)) {
						if (strpos($subc_id, 'sub_') !== false) {
							/* stripe */
							$subscription = Subscription::retrieve($subc_id);
							$subscription->cancel();
						} else {
							/* paypal */
							lp_cancel_recurring_profile($subc_id);
						}

						unset($userSubscriptions[$key]);
						break;
					}
				}
			}

			/* update metabox */
			if (!empty($userSubscriptions)) {
				update_user_meta($uid, 'listingpro_user_sbscr', $userSubscriptions);
			} else {
				delete_user_meta($uid, 'listingpro_user_sbscr');
			}
		}
	}
}


/* get distance between co-ordinates */
if (!function_exists('GetDrivingDistance')) {

	function GetDrivingDistance($latitudeFrom, $latitudeTo, $longitudeFrom, $longitudeTo, $unit)
	{

		$latitudeFrom   = (float) $latitudeFrom;
		$latitudeTo     = (float) $latitudeTo;
		$longitudeFrom  = (float) $longitudeFrom;
		$longitudeTo    = (float) $longitudeTo;
		$unit = strtoupper($unit);
		$theta = $longitudeFrom - $longitudeTo;
		$dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		if ($unit == "KM") {
			$distance = ($miles * 1.609344);
			$dist = round($distance, 1);
			return array('distance' => $dist);
		} else {
			$dist = round($miles, 1);
			return array('distance' => $dist);
		}
	}
}

/*====first change function as follow ======
/* get lat and long from address and set for listing */
if (!function_exists('lp_get_lat_long_from_address')) {
	function lp_get_lat_long_from_address($address, $listing_id)
	{
		$exLat = listing_get_metabox_by_ID('latitude', $listing_id);
		$exLong = listing_get_metabox_by_ID('longitude', $listing_id);
		$mapkey = lp_theme_option('google_map_api');
		if (empty($exLat) && empty($exLong)) {
			if (!empty($address) && !empty($listing_id)) {
				$address = urlencode($address);

				$url     = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&key=" . $mapkey;
				global $wp_filesystem;
				if (empty($wp_filesystem)) {
					require_once(ABSPATH . '/wp-admin/includes/file.php');
					WP_Filesystem();
				}
				$resp    = json_decode($wp_filesystem->get_contents($url), true);

				if ($resp['status'] === 'OK') {
					$formatted_address = ($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : '';
					$lat = ($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : '';
					$long = ($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : '';

					if (!empty($lat) && !empty($long)) {
						listing_set_metabox('latitude', $lat, $listing_id);
						listing_set_metabox('longitude', $long, $listing_id);
					}
				}
			}
		}
	}
}

/* hide activatio notice vc */
add_action('admin_head', 'lp_hide_vc_notification_css');
if (!function_exists('lp_hide_vc_notification_css')) {
	function lp_hide_vc_notification_css()
	{
		echo '<style>#vc_license-activation-notice { display: none !important; }</style>';
	}
}

/* ==============start add by sajid ============ */
add_filter('body_class', 'listing_view_class');
if (!function_exists('listing_view_class')) {
	function listing_view_class($classes)
	{
		global $listingpro_options;
		$listing_mobile_view  = isset($listingpro_options['single_listing_mobile_view']) ? $listingpro_options['single_listing_mobile_view'] : '';
		if (($listing_mobile_view == 'app_view' || $listing_mobile_view == 'app_view2') && wp_is_mobile()) {
			$classes[]  =   'listing-app-view';
		}
		if ($listing_mobile_view == 'app_view2' && wp_is_mobile()) {
			$classes[]  =   'listing-app-view-new';
		}
		$app_view_home  = isset($listingpro_options['app_view_home']) ? $listingpro_options['app_view_home'] : '';
		if (is_page($app_view_home) && ($listing_mobile_view == 'app_view' || $listing_mobile_view == 'app_view2') && wp_is_mobile()) {
			$classes[]  =   'app-view-home';
		}
		$listing_skeleton  = isset($listingpro_options['listing_views']) ? $listingpro_options['listing_views'] : '';
		$classes[]  =   'listing-skeleton-view-' . $listing_skeleton;
		return $classes;
	}
}

/* ========listingpro_footer_menu_app======== */

if (!function_exists('listingpro_footer_menu_app')) {
	function listingpro_footer_menu_app()
	{
		$defaults = array(
			'theme_location'  => 'footer_menu',
			'menu'            => '',
			'container'       => 'false',
			'menu_class'      => '',
			'menu_id'         => '',
			'echo'            => true,
			'fallback_cb'     => '',
			'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		);

		if (has_nav_menu('footer_menu')) {
			return wp_nav_menu($defaults);
		}
	}
}

/* ==============end add by sajid ============ */



/* ==============lp get free fields ============ */
if (!function_exists('listingpro_get_term_openfields')) {
	function listingpro_get_term_openfields($onbackend = false)
	{


		$lpAllCatIds = array();
		$lp_catterms = get_terms(array(
			'taxonomy' => 'listing-category',
			'hide_empty' => false,
		));

		if (!empty($lp_catterms)) {
			foreach ($lp_catterms as $term) {
				array_push($lpAllCatIds, $term->term_id);
			}
		}


		$output = null;
		$fieldIDs = array();

		$texQuery = array(
			'key' => 'lp_listingpro_options',
			'value' => $lpAllCatIds,
			'compare' => 'NOT IN'
		);



		$argss = array(
			'post_type'  => 'form-fields',
			'posts_per_page'  => -1,
			'meta_query' => array(
				$texQuery
			)
		);
		$the_queryy = null;
		$the_queryy = new WP_Query($argss);
		if ($the_queryy->have_posts()) {
			while ($the_queryy->have_posts()) {
				$the_queryy->the_post();
				$fID = get_the_ID();
				$yesString = 'Yes';
				$exclusiveCheck = listing_get_metabox_by_ID('exclusive_field', $fID);
				if (!empty($exclusiveCheck) && $exclusiveCheck == $yesString) {
					array_push($fieldIDs, get_the_ID());
				}
				wp_reset_postdata();
			}
		}
		if ($onbackend == false) {
			$output = listingpro_field_type($fieldIDs);
		} else {
			$output = $fieldIDs;
		}


		return $output;
	}
}
/* ============== /// ============ */

/* ==============  get post count of taxonomy term============ */
if (!function_exists('lp_count_postcount_taxonomy_term_byID')) {
	function lp_count_postcount_taxonomy_term_byID($post_type, $taxonomy, $termid)
	{
		$postcounts = 0;
		$termObj = get_term_by('id', $termid, "$taxonomy");
		if (!is_wp_error($termObj)) {
			$postcounts = $termObj->count;
		}
		$include_child = false;
		if (lp_theme_option('lp_children_in_tax') != "no") {
			$include_child = true;
		}
		$args = array(
			'post_type' => $post_type,
			'post_status' => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'field' => 'id',
					'terms' => $termid,
					'include_children' => $include_child,
				)
			),
		);
		$the_query = new WP_Query($args);
		$count = $the_query->found_posts;
		$postcounts = $count;

		return $postcounts;
	}
}

/* ============== is favourite or not only ============ */
if (!function_exists('listingpro_is_favourite_new')) {
	function listingpro_is_favourite_new($postid)
	{
		$return = null;
		if (is_user_logged_in()) {
			$uid = get_current_user_id();
			$favposts = get_user_meta($uid, 'lp_saved_user_posts', true);
			if (!is_array($favposts)) {
				$favposts   =   (array) $favposts;
			}
			if (in_array($postid, $favposts)) {
				$return =   'yes';
			}
		} else {
			$favposts = (isset($_COOKIE['newco'])) ? explode(',', (string) $_COOKIE['newco']) : array();
			$favposts = array_map('absint', $favposts); // Clean cookie input, it's user input!
			$return =   'no';
			if (in_array($postid, $favposts)) {
				$return =   'yes';
			}
		}
		return $return;
	}
}

/* ============== for mail sprintfto function============= */
if (!function_exists('lp_sprintf2')) {
	function lp_sprintf2($str = '', $vars = array(), $char = '%')
	{
		if (!$str) {
			return '';
		}
		if (count($vars) > 0) {
			foreach ($vars as $k => $v) {
				$str = str_replace($char . $k, $v, $str);
			}
		}

		return $str;
	}
}

/* ============== default featured image for listing ============= */
if (!function_exists('lp_default_featured_image_listing')) {
	function lp_default_featured_image_listing()
	{
		global $listingpro_options;
		$deafaultFeatImg = '';
		//if( isset($listingpro_options['lp_def_featured_image']) && !empty($listingpro_options['lp_def_featured_image']) ){

		//$deafaultFeatImgID = $listingpro_options['lp_def_featured_image']['id'];
		$deafaultFeatImgID = lp_theme_option_id('lp_def_featured_image');
		if (!empty($deafaultFeatImgID)) {
			$deafaultFeatImg = wp_get_attachment_image_src($deafaultFeatImgID, 'full', true);
			$deafaultFeatImg = $deafaultFeatImg[0];
		} else {
			$deafaultFeatImg = lp_theme_option_url('lp_def_featured_image');
		}
		//}
		return $deafaultFeatImg;
	}
}

/* ============== custom actions listingpro ============= */
if (!function_exists('lp_add_current_requests_query_args')) {
	add_filter('post_type_link', 'lp_add_current_requests_query_args', 90, 2);
	function lp_add_current_requests_query_args($permalink, $post)
	{
		global $lp_ad_ids;
		if (wp_doing_ajax()) {
			if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ajax_search_tags') {
				if (isset($post) && $post->post_type == 'listing') {
					if (!empty($lp_ad_ids)) {
						if (is_array($lp_ad_ids) && in_array($post->ID, $lp_ad_ids)) {
							return lp_add_current_requests_query_args_cb($permalink, $post, 'search_page');
						} else {
							return $permalink;
						}
					} else {
						return $permalink;
					}
				} else {
					return $permalink;
				}
			} else {
				return $permalink;
			}
		} else if (is_admin()) {
			return $permalink;
		} else {
			if (isset($post) && $post->post_type == 'listing') {

				if (is_front_page()) {
					return lp_add_current_requests_query_args_cb($permalink, $post, 'home_page');
				} else if (is_archive() || (isset($_GET['s']) && $_GET['s'] == 'home')) {
					if (!empty($lp_ad_ids)) {
						if (isset($lp_ad_ids) && in_array($post->ID, $lp_ad_ids)) {
							return lp_add_current_requests_query_args_cb($permalink, $post, 'search_page');
						} else {
							return $permalink;
						}
					} else {
						return $permalink;
					}
				} else if (is_single()) {
					return lp_add_current_requests_query_args_cb($permalink, $post, 'detail_page');
				} else {
					return lp_add_current_requests_query_args_cb($permalink, $post, 'home_page');
				}
			} else {
				return $permalink;
			}
		}
	}
}

if (!function_exists('lp_add_current_requests_query_args_cb')) {
	function lp_add_current_requests_query_args_cb($permalink, $post, $lp_ad_type = '')
	{

		$current_id             = get_current_user_id();
		$default_typeofcampaign = lp_theme_option('listingpro_ads_campaign_style');
		$adID                   = listing_get_metabox_by_ID('campaign_id', $post->ID);
		$typeofcampaign         = listing_get_metabox_by_ID('ads_mode', $adID);
		$listindUserID          = get_post_field('post_author', $post->ID);

		if (isset($typeofcampaign) && !empty($typeofcampaign)) {
			$typeofcampaign = 'ads' . $typeofcampaign;
		} else {
			$typeofcampaign = $default_typeofcampaign;
		}

		$front_page_adStatus   = get_post_meta($post->ID, 'lp_random_ads', true);
		$search_page_adStatus  = get_post_meta($post->ID, 'lp_top_in_search_page_ads', true);
		$detail_page_adStatus  = get_post_meta($post->ID, 'lp_detail_page_ads', true);
		$campaign_status       = get_post_meta($post->ID, 'campaign_status', true);

		if ($campaign_status == 'active' && $typeofcampaign == 'adsperclick' && $listindUserID != $current_id) {
			if ($lp_ad_type == 'home_page' && $front_page_adStatus == 'active') {
				return esc_url(add_query_arg(array('ad_type' => 'lp_random_ads_pc'), $permalink));
			} else if ($lp_ad_type == 'search_page' && $search_page_adStatus == 'active') {
				return esc_url(add_query_arg(array('ad_type' => 'lp_top_in_search_page_ads_pc'), $permalink));
			} else if ($lp_ad_type == 'detail_page' && $detail_page_adStatus == 'active') {
				return esc_url(add_query_arg(array('ad_type' => 'lp_detail_page_ads_pc'), $permalink));
			} else {
				return $permalink;
			}
		} else {
			return $permalink;
		}
	}
}

if (!function_exists('lp_count_clicks_for_listing_ads')) {
	add_action('template_redirect', 'lp_count_clicks_for_listing_ads');
	function lp_count_clicks_for_listing_ads($listing_id = '')
	{
		if (isset($_GET['ad_type']) && $_GET['ad_type'] != '') {
			global $wp, $post;
			$listing_id = $post->ID;
			$listingStatus = get_post_status($listing_id);
			$cid           = get_current_user_id();
			$listindUserID = get_post_field('post_author', $listing_id);

			if ($listingStatus == "expired" && $listindUserID != $cid) {
				wp_redirect(home_url());
				exit;
			}

			$adID = listing_get_metabox_by_ID('campaign_id', $listing_id);
			lp_count_clicks_for_campaigns($listing_id, $adID, $_GET['ad_type'], esc_url(home_url($wp->request)));
		}
	}
}

//add_action( 'template_redirect', 'listingpro_redirect_to_homepage' );
if (!function_exists('listingpro_redirect_to_homepage')) {
	function listingpro_redirect_to_homepage()
	{
		global $post;

		if (is_singular('listing')) {
			$cpostID = $post->ID;
			$adID = listing_get_metabox_by_ID('campaign_id', $cpostID);
			$typeofcampaign = listing_get_metabox_by_ID('ads_mode', $adID);
			if (empty($typeofcampaign)) {
				$typeofcampaign = lp_theme_option('listingpro_ads_campaign_style');
			} else {
				$typeofcampaign = 'ads' . $typeofcampaign;
			}
			if ($typeofcampaign == "adsperclick") {
				$refrer = wp_get_referer();
				listingpro_get_listing_referer($refrer, $adID);
			}
		}


		if (is_singular('listing')) {
			if (!empty($cpostID)) {
				$listingStatus = get_post_status($cpostID);
				$cid = get_current_user_id();
				$listindUserID = get_post_field('post_author', $post->ID);
				if ($listingStatus == "expired" && $listindUserID != $cid) {
					wp_redirect(home_url());
					exit;
				}
			}
		}
	}
}

/* ============ get image alt of featured image from post id ======= */
if (!function_exists('lp_get_the_post_thumbnail_alt')) {
	function lp_get_the_post_thumbnail_alt($post_id)
	{
		return get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
	}
}



add_image_size('listingpro_cats270_150', 270, 150, true); // (cropped)
/* ============== Version2 Functions ============ */
require_once THEME_PATH . "/include/functions-new.php";

/* ======================================= */
/* by dev for 2.0 */

if (!function_exists('lp_theme_option')) {
	function lp_theme_option($optionID)
	{
		global $listingpro_options;
		if (isset($listingpro_options["$optionID"])) {
			$optionValue = $listingpro_options["$optionID"];
			return $optionValue;
		} else {
			return false;
		}
	}
}

if (!function_exists('lp_paid_mode_status')) {
	function lp_paid_mode_status()
	{
		global $listingpro_options;
		$enable_paid_submission = lp_theme_option('enable_paid_submission');
		if ($enable_paid_submission == 'yes') {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('lp_get_parent_cats_array')) {
	function lp_get_parent_cats_array($onlyhavPlans = true)
	{

		$parentCatsArray = array();
		$parentCatTerms = get_terms('listing-category', array('parent' => 0, 'hide_empty' => false));
		if (!empty($parentCatTerms)) {
			foreach ($parentCatTerms as $catTerm) {
				$cat_id = $catTerm->term_id;
				if ($onlyhavPlans == true) {
					$lp_attached_plans = get_term_meta($cat_id, 'lp_attached_plans', true);
					if (!empty($lp_attached_plans)) {
						$parentCatsArray[$cat_id] = $catTerm->name;
					}
				} else {
					$parentCatsArray[$cat_id] = $catTerm->name;
				}
			}
		}
		return $parentCatsArray;
	}
}


if (!function_exists('lp_get_all_cats_array')) {
	function lp_get_all_cats_array($onlyhavPlans = true)
	{

		$parentCatsArray = array();
		$parentCatTerms = get_terms('listing-category', array('hide_empty' => false));
		if (!empty($parentCatTerms)) {
			foreach ($parentCatTerms as $catTerm) {
				$cat_id = $catTerm->term_id;
				if ($onlyhavPlans == true) {
					$lp_attached_plans = get_term_meta($cat_id, 'lp_attached_plans', true);
					if (!empty($lp_attached_plans)) {
						$parentCatsArray[$cat_id] = $catTerm->name;
					}
				} else {
					$parentCatsArray[$cat_id] = $catTerm->name;
				}
			}
		}
		return $parentCatsArray;
	}
}

if (!function_exists('lp_get_child_cats_of_parent')) {
	function lp_get_child_cats_of_parent($term_id, $taxonomy)
	{
		$childTermArray = array();
		$argsTermChild = array(
			'order' => 'ASC',
			'hide_empty' => false,
			'hierarchical' => false,
			'parent' => $term_id,

		);
		$childTerms = get_terms($taxonomy, $argsTermChild);
		if (!empty($childTerms) && !is_wp_error($childTerms)) {
			foreach ($childTerms as $singleChldTerm) {
				$childTermArray[$singleChldTerm->term_id] = $singleChldTerm->name;
			}
		}
		return $childTermArray;
	}
}

if (!function_exists('lp_get_term_field_by')) {
	function lp_get_term_field_by($term_param, $compare_by, $taxonomy, $returnField)
	{
		$returnFieldValue = '';
		$termData = get_term_by($compare_by, $term_param, $taxonomy);
		if (!empty($termData) && !is_wp_error($termData)) {
			if (!empty($returnField)) {
				$returnFieldValue = $termData->$returnField;
			}
		}
		return $returnFieldValue;
	}
}

/* ajax for plans */
add_action('wp_ajax_listingpro_select_plan_by_cat', 'listingpro_select_plan_by_cat');
add_action('wp_ajax_nopriv_listingpro_select_plan_by_cat', 'listingpro_select_plan_by_cat');
if (!function_exists('listingpro_select_plan_by_cat')) {
	function listingpro_select_plan_by_cat()
	{
		$catTermid = sanitize_text_field($_POST['term_id']);
		$pricing_style_views = sanitize_text_field($_POST['currentStyle']);
		$durationType = sanitize_text_field(stripcslashes($_POST['duration_type']));

		$durationArray = array();


		$planforArray = array();
		$plans_by_cat = lp_theme_option('listingpro_plans_cats');
		if ($plans_by_cat == 'yes') {
			$planforArray = array(
				'key' => 'plan_usge_for',
				'value' => 'default',
				'compare' => 'NOT LIKE',
			);
		}


		$allowed_statuses = array('horizontal_view_1', 'horizontal_view_2', 'vertical_view_1', 'vertical_view_2', 'vertical_view_3', 'vertical_view_4', 'vertical_view_5', 'vertical_view_6', 'vertical_view_7', 'vertical_view_8', 'vertical_view_9', 'claim_plans');
		$isMontlyFilter = false;
		/* for switcher */
		$args = null;
		$args = array(
			'post_type' => 'price_plan',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'lp_selected_cats',
					'value' => ':"' . $catTermid . '"',
					'compare' => 'LIKE',
				),
			),
		);
		$cat_Plan_Query = null;
		$cat_Plan_Query = new WP_Query($args);
		if ($cat_Plan_Query->have_posts()) {
			while ($cat_Plan_Query->have_posts()) {
				$cat_Plan_Query->the_post();
				$durationtype = get_post_meta(get_the_ID(), 'plan_duration_type', true);
				if ($durationtype == "monthly" || $durationtype == "yearly") {
					$isMontlyFilter = true;
				}
			}
		}
		/* end for switcher */


		if (!empty($durationType)) {
			$durationArray = array(
				'key' => 'plan_duration_type',
				'value' => $durationType,
				'compare' => 'LIKE',
			);
		}




		if (!empty($catTermid)) {
			/* code goes here */
			$output = null;
			$args = null;
			$args = array(
				'post_type' => 'price_plan',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'lp_selected_cats',
						'value' => $catTermid,
						'compare' => 'LIKE',
					),
					$durationArray,
					$planforArray,
				),
			);


			$cat_Plan_Query = null;
			$gridNumber = 0;
			$cat_Plan_Query = new WP_Query($args);
			$count = $cat_Plan_Query->found_posts;
			$GLOBALS['plans_count'] = $count;
			if ($cat_Plan_Query->have_posts()) {
				while ($cat_Plan_Query->have_posts()) {
					$cat_Plan_Query->the_post();
					$durationtype = get_post_meta(get_the_ID(), 'plan_duration_type', true);
					$gridNumber++;

					if (in_array($pricing_style_views, $allowed_statuses, true)) {
						ob_start();
						include(LISTINGPRO_PLUGIN_PATH . "templates/pricing/loop/" . $pricing_style_views . '.php');
						$output .= ob_get_contents();
						ob_end_clean();
						ob_flush();
					}
					if ($gridNumber % 3 == 0) {
						$output .= '<div class="clearfix"></div>';
					}
				} //END WHILE
				wp_reset_postdata();
				$returnData = array('response' => 'success', 'plans' => $output, 'switcher' => $isMontlyFilter);
			} else {
				$returnData = array('response' => 'success', 'plans' => esc_html__('Sorry! There is no plan associated with the category', 'listingpro'), 'switcher' => $isMontlyFilter);
			}
		}

		die(json_encode($returnData));
	}
}


/* ajax for general plans */
add_action('wp_ajax_listingpro_select_general_plans', 'listingpro_select_general_plans');
add_action('wp_ajax_nopriv_listingpro_select_general_plans', 'listingpro_select_general_plans');
if (!function_exists('listingpro_select_general_plans')) {
	function listingpro_select_general_plans()
	{
		/* code goes here */
		$metaQueryArray['relation'] = 'OR';
		$durationType = sanitize_text_field($_POST['duration_type']);
		$pricing_style_views = sanitize_file_name($_POST['currentStyle']);
		$lp_plans_cats = lp_theme_option('listingpro_plans_cats');
		if ($lp_plans_cats == 'yes') {
			$metaQueryArray['relation'] = 'AND';
		}
		$allowed_statuses = array('horizontal_view_1', 'horizontal_view_2', 'vertical_view_1', 'vertical_view_2', 'vertical_view_3', 'vertical_view_4', 'vertical_view_5', 'vertical_view_6', 'vertical_view_7', 'vertical_view_8', 'vertical_view_9', 'claim_plans');
		$durArrray = array();
		if (!empty($durationType)) {
			$metaQueryArray[] = array(
				'key' => 'plan_duration_type',
				'value' => $durationType,
				'compare' => 'LIKE',
			);
		}

		$outputt = null;
		$args = null;
		$args = array(
			'post_type' => 'price_plan',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'meta_query' => array(
				$metaQueryArray,
			),
		);


		$cat_Plan_Query = null;
		$gridNumber = 0;
		$cat_Plan_Query = new WP_Query($args);
		$count = $cat_Plan_Query->found_posts;
		$GLOBALS['plans_count'] = $count;
		if ($cat_Plan_Query->have_posts()) {
			while ($cat_Plan_Query->have_posts()) {
				$cat_Plan_Query->the_post();
				$planfor = get_post_meta(get_the_ID(), 'plan_usge_for', true);
				//New update 2.6.10
				$planforclaim = listing_get_metabox_by_ID('plan_for', get_the_ID());
				if ($planforclaim != 'claimonly') {
					if (empty($planfor) || $planfor == 'default') {
						if (in_array($pricing_style_views, $allowed_statuses, true)) {
							ob_start();
							include(LISTINGPRO_PLUGIN_PATH . "templates/pricing/loop/" . $pricing_style_views . '.php');
							$outputt .= ob_get_contents();
							ob_end_clean();
							ob_flush();
						}
					}
				}
				//End New update 2.6.10
			} //END WHILE
			wp_reset_postdata();
			$returnData = array('response' => 'success', 'plans' => $outputt);
		} else {
			$returnData = array('response' => 'success', 'plans' => esc_html__('Sorry! There is no general plan', 'listingpro'));
		}

		die(json_encode($returnData));
	}
}



/* ============= function for paid claim form================ */
if (!function_exists('lp_paid_claim_email_form')) {
	function lp_paid_claim_email_form()
	{
		$returnData = array();
		if (!check_ajax_referer('lp_admin_nonce_action', '_ajax_nonce', false)) {
			$returnData = array(
				'status'  => 'error',
				'message' => 'Invalid nonce'
			);
			exit(json_encode($returnData));
		}

		if (!current_user_can('edit_posts')) {
			$returnData = array(
				'status'  => 'error',
				'message' => 'You do not have permission to edit posts.'
			);
			exit(json_encode($returnData));
		}

		global $wp_rewrite;
		$htmlData = '';
		$checkoutURl = lp_theme_option('payment-checkout');
		$checkoutURl = get_permalink($checkoutURl);
		$listing_id = sanitize_text_field($_POST['listing_id']);
		$author_id = get_post_field('post_author', $listing_id);
		$author_obj = get_user_by('id', $author_id);
		$author_email = $author_obj->user_email;
		$claim_type = sanitize_text_field($_POST['claim_type']);
		$claim_plan = sanitize_text_field($_POST['claim_plan']);
		$claimer = sanitize_text_field($_POST['claimer']);

		$claimer_obj = get_user_by('id', $claimer);
		$claimer_email = $claimer_obj->user_email;
		$claimed_post = get_the_title($listing_id);


		$claim_post_ID = sanitize_text_field($_POST['claim_post_ID']);
		if ($wp_rewrite->permalink_structure == '') {
			$checkoutURl .= "&listing_id=$listing_id&claim_plan=$claim_plan&user_id=$claimer&claim_post=$claim_post_ID";
		} else {
			$checkoutURl .= "?listing_id=$listing_id&claim_plan=$claim_plan&user_id=$claimer&claim_post=$claim_post_ID";
		}

		/* encodeing url */
		if (!empty($checkoutURl)) {
			//$checkoutURl = urlencode($checkoutURl);
			//$checkoutURl = urldecode($checkoutURl);//need to be remove this line after test
		}

		$paidClaimData = esc_html__('To get Claim for listing, click of following link', 'listingpro') . '<br />';
		//$paidClaimData .= "<a href='$checkoutURl' target='_blank'>".esc_html__('Click Here', 'listingpro')."</a>";
		$paidClaimData .= $checkoutURl;

		if (!empty($claim_type) && $claim_type == "paidclaims") {
			$htmlData = '<tr id="lp_claim_email"><th><label>' . esc_html__('Load Claim Email', 'listingpro') . '</label></th><td>';
			$htmlData .= '<input type="email" id="to_claimer_email" name="to_claimer_email" placeholder="' . esc_attr__('john@gmail.com', 'listingpro') . '" value="' . esc_attr($claimer_email) . '">';
			$htmlData .= '<input type="hidden" id="claimer_id" name="claimer_id" value="' . esc_attr($claimer) . '">';
			$htmlData .= '<br />';
			$htmlData .= '<input type="text" value="' . esc_attr($claimed_post) . '" id="email_subject" name="email_subject" placeholder="' . esc_html__('Claim For Listing', 'listingpro') . '">';
			$htmlData .= '<br />';
			$htmlData .= '<textarea class="lp_claim_email" name="lp_claim_email">' . esc_attr($paidClaimData) . '</textarea>';
			$htmlData .= '<br />';
			$htmlData .= '<button type="submit" class="lp_trigger_paidclaim_email">' . esc_html__('Send Link to email', 'listingpro') . '</button>';
			$htmlData .= '<i style="display:none" class="lp-listing-spingg fa-li fa fa-spinner fa-spin"></i>';
			$htmlData .= '</td></tr>';
		}
		exit(json_encode($returnData = array('status' => 'success', 'htmlData' => $htmlData)));
	}
}

add_action('wp_ajax_lp_paid_claim_email_form', 'lp_paid_claim_email_form');

/* ======================send email for paid claim================== */
if (!function_exists('lp_paid_claim_email_send')) {
	function lp_paid_claim_email_send()
	{
		$returnData = array();
		$claimer_id = sanitize_text_field($_POST['claimer_id']);
		$to = '';

		if (isset($_POST['to_claimer_email'])) {
			$to = sanitize_email($_POST['to_claimer_email']);
		} else {
			$author_obj = get_user_by('id', $claimer_id);
			$to = $author_obj->user_email;
		}
		/* save in user meta */
		update_user_meta($claimer_id, 'email_for_claim', $to);
		$subject = sanitize_text_field($_POST['email_subject']);
		$body = sanitize_text_field($_POST['lp_claim_email']);
		$headers = array('Content-Type: text/html; charset=UTF-8');
		lp_mail_headers_append();
		$emailStatus = LP_send_mail($to, $subject, $body, $headers);
		lp_mail_headers_remove();
		if (!empty($emailStatus)) {
			$statusMsg = esc_html__('Email has sent', 'listingpro');
			$returnData = array(
				'status' => 'success',
				'msg' => $statusMsg
			);
		} else {
			$statusMsg = esc_html__('Problem in email sending', 'listingpro');
			$returnData = array(
				'status' => 'error',
				'msg' => $statusMsg
			);
		}

		exit(json_encode($returnData));
	}
}
add_action('wp_ajax_lp_paid_claim_email_send', 'lp_paid_claim_email_send');

/* ======================get filter addtional function ================== */
if (!function_exists('lp_get_extrafields_filter')) {
	function lp_get_extrafields_filter($fieltType = false, $catid = null, $countreturn = false)
	{
		$returnArray = array();
		$singleField = array();
		if (!empty($fieltType)) {
			$singleField = array($fieltType);
		} else {
			$singleField = array('check', 'checkbox', 'checkboxes', 'radio', 'select');
		}
		foreach ($singleField as $fieltType) {
			$args = array(
				'post_type' => 'form-fields',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key' => 'lp_field_filter_type',
						'value' => "$fieltType",
						'compare' => '='
					),
					array(
						'key' => 'lp_listingpro_options',
						'value' => 'displaytofilt',
						'compare' => 'LIKE'
					),

				),
			);

			$addition_fields_Query = new WP_Query($args);
			if ($addition_fields_Query->have_posts()) {
				while ($addition_fields_Query->have_posts()) {
					$addition_fields_Query->the_post();
					$isExclusive = listing_get_metabox_by_ID('exclusive_field',  get_the_ID());

					if ($isExclusive == "Yes") {
						$returnArray[get_the_ID()] = get_the_title();
					} else {
						$catsids = listing_get_metabox_by_ID('field-cat',  get_the_ID());
						if (!empty($catsids)) {
							if (!empty($catid)) {
								if (in_array($catid, $catsids)) {
									$returnArray[get_the_ID()] = get_the_title();
								}
							} else {
								//$returnArray[get_the_ID()] = get_the_title();
							}
						}
					}
				}
				wp_reset_postdata();
			}
		}
		if (!empty($countreturn)) {
			return count($returnArray);
		} else {
			return $returnArray;
		}
	}
}

/* ======================ajax post type search autocompelte ================== */
if (!function_exists('lp_ja_ajax_search_posttype')) {
	function lp_ja_ajax_search_posttype()
	{
		$query_args =   array(
			'post_type'     => sanitize_text_field(stripslashes($_POST['posttype'])),
			'post_status'   => 'publish',
			'posts_per_page' => 10,
			's'             => sanitize_text_field(stripslashes($_POST['search'])),
		);
		if (isset($_REQUEST) && $_POST['uniqueForEvents'] == 'yes') {
			$query_args['meta_query']   =   array(
				array(
					'key' => 'event_id',
					'compare' => 'NOT EXISTS'
				)
			);
		}
		$results = new WP_Query($query_args);
		$items = null;
		$items .= '<ul id="listing-list">';
		if (!empty($results->posts)) {
			foreach ($results->posts as $result) {
				$items .= '<li onClick="selectListing(' . $result->ID . ')">' . $result->post_title . '</li>';
			}
		} else {
		}
		$items .= '</ul>';
		wp_send_json_success($items);
	}
}
add_action('wp_ajax_search_posttype', 'lp_ja_ajax_search_posttype');
add_action('wp_ajax_nopriv_search_posttype', 'lp_ja_ajax_search_posttype');

/* ======================ajax pricing plan by month year ================== */
if (!function_exists('lp_filter_pricing_plans')) {
	function lp_filter_pricing_plans()
	{

		$catId = '';
		$planUsage = '';
		$catTaxArray = array();
		$catTax2Array = array();

		if (isset($_POST['planUsage'])) {
			$planUsage = sanitize_text_field(stripslashes($_POST['planUsage']));
		}

		if (isset($_POST['cat_id'])) {
			$catId = sanitize_text_field(stripslashes($_POST['cat_id']));
			if (!empty($catId)) {
				$catTaxArray = array(
					'key' => 'lp_selected_cats',
					'value' => $catId,
					'compare' => 'LIKE',
				);
			}
		}


		$durationType = sanitize_text_field(stripslashes($_POST['duration_type']));
		$relationParm = 'AND';
		if (empty($durationType) && empty($catTaxArray)) {
			$relationParm = 'OR';
		}


		$pricing_style_views = sanitize_file_name($_POST['currentStyle']);
		$allowed_statuses = array('horizontal_view_1', 'horizontal_view_2', 'vertical_view_1', 'vertical_view_2', 'vertical_view_3', 'vertical_view_4', 'vertical_view_5', 'vertical_view_6', 'vertical_view_7', 'vertical_view_8', 'vertical_view_9', 'claim_plans');
		$returnData = null;
		/* code goes here */
		$output = null;
		$args = null;
		$args = array(
			'post_type' => 'price_plan',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'meta_query' => array(
				'relation' => $relationParm,
				$catTaxArray,
				array(
					'key' => 'plan_duration_type',
					'value' => $durationType,
					'compare' => 'LIKE',
				),
				$catTax2Array,

			),
		);

		$cat_Plan_Query = null;
		$output = null;
		$gridNumber = 0;
		$cat_Plan_Query = new WP_Query($args);
		$count = $cat_Plan_Query->found_posts;
		$GLOBALS['plans_count'] = $count;
		if ($cat_Plan_Query->have_posts()) {
			while ($cat_Plan_Query->have_posts()) {
				$cat_Plan_Query->the_post();
				$showplan = true;
				$forListings = listing_get_metabox_by_ID('plan_for', get_the_ID());

				$planfor = get_post_meta(get_the_ID(), 'plan_usge_for', true);
				if (isset($_POST['cat_id'])) {
					if (!empty($_POST['cat_id'])) {
						if (!empty($planUsage)) {
							if ($planUsage != $forListings) {
								$showplan = false;
							}
						}
					} else {
						if (!empty($forListings)) {
							if ($planUsage != $forListings) {
								$showplan = false;
							}
						}
						if ($showplan == true && !empty($planfor)) {
							if ($planfor == 'default') {
								$showplan = true;
							} else {
								$showplan = false;
							}
						}
					}
				}
				if ($forListings == 'listingandclaim' || $forListings == 'listingonly') {
					$showplan   =   true;
				}
				if (!empty($showplan)) {
					if (in_array($pricing_style_views, $allowed_statuses, true)) {
						ob_start();
						include(LISTINGPRO_PLUGIN_PATH . "templates/pricing/loop/" . $pricing_style_views . '.php');
						$output .= ob_get_contents();
						ob_end_clean();
						ob_flush();
					}
				}
			} //END WHILE
			wp_reset_postdata();
			if (!empty($output)) {
				$returnData = array('response' => 'success', 'plans' => $output);
			} else {
				$returnData = array('response' => 'success', 'plans' => esc_html__('Sorry! There is no plan associated with the category', 'listingpro'));
			}
		} else {
			$returnData = array('response' => 'success', 'plans' => esc_html__('Sorry! There is no plan associated with the category', 'listingpro'));
		}

		exit(json_encode($returnData));
		//wp_send_json_success( $returnData );
	}
}
add_action('wp_ajax_filter_pricingplan', 'lp_filter_pricing_plans');
add_action('wp_ajax_nopriv_filter_pricingplan', 'lp_filter_pricing_plans');



/*---------------------------Fontawesome Icons For Pricing -----------------------*/

if (!function_exists('listingpro_fontawesome_icon')) {

	function listingpro_fontawesome_icon($icon)
	{
		$output = '';
		if ($icon == 'checked') {
			$output = '<i class="awesome_plan_icon_check fa fa-check-circle"></i>';
		} elseif ($icon == 'unchecked') {
			$output = '<i class="awesome_plan_icon_cross fa fa-times-circle"></i>';
		}
		return $output;
	}
}

/* ******************For coupon code*********************** */

if (!function_exists('lp_generate_coupon_code')) {
	function lp_generate_coupon_code()
	{
		$chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$res = "";
		for ($i = 0; $i < 10; $i++) {
			$res .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
		return $res;
	}
}
/* ******************For business hours translated*********************** */

if (!function_exists('lp_get_translated_day')) {
	function lp_get_translated_day($dayName)
	{
		return $dayName;
	}
}

if (!function_exists('lp_get_days_of_week')) {
	function lp_get_days_of_week($currentDate)
	{
		$weekArray = array();
		$currentDayStr = strtotime($currentDate);
		$currentDay = date("l", strtotime($currentDate));
		$StartDate = '';
		//$currentDay = 'Monday';
		switch ($currentDay) {

			case ('Monday'):
				$StartDate = strtotime($currentDate);
				break;

			case ('Tuesday'):
				$StartDate = strtotime($currentDate . "-1 day");
				break;

			case ('Wednesday'):
				$StartDate = strtotime($currentDate . "-2 day");
				break;

			case ('Thursday'):
				$StartDate = strtotime($currentDate . "-3 day");
				break;

			case ('Friday'):
				$StartDate = strtotime($currentDate . "-4 day");
				break;

			case ('Saturday'):
				$StartDate = strtotime($currentDate . "-5 day");
				break;

			case ('Sunday'):
				$StartDate = strtotime($currentDate . "-6 day");
				break;

			case ('Sunday'):
				$StartDate = strtotime($currentDate . "-7 day");
				break;
		}

		$start_date = date(get_option('date_format'), $StartDate);
		$weekArray = array(
			$StartDate,
			strtotime($start_date . "+1 day"),
			strtotime($start_date . "+2 day"),
			strtotime($start_date . "+3 day"),
			strtotime($start_date . "+4 day"),
			strtotime($start_date . "+5 day"),
			strtotime($start_date . "+6 day")
		);
		return $weekArray;
	}
}

/* ================================days of month ===================== */

if (!function_exists('lp_get_days_of_month')) {
	function lp_get_days_of_month($month, $year)
	{
		$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$dates_month = array();

		for ($i = 1; $i <= $num; $i++) {
			$mktime = mktime(0, 0, 0, $month, $i, $year);
			$date = date("Y-m-d", $mktime);
			$date = strtotime($date);
			$dates_month[$i] = $date;
		}

		return $dates_month;
	}
}

/* ========================= months of year =============================== */
if (!function_exists('lp_get_all_months')) {
	function lp_get_all_months()
	{
		$months = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
		return $months;
	}
}


/* ========================= lp set stats for chart =============================== */
if (!function_exists('lp_set_this_stats_for_chart')) {
	function lp_set_this_stats_for_chart($authorID, $listing_id, $type)
	{
		if ($type == "view") {
			$table = "listing_stats_views";
		} elseif ($type == "reviews") {
			$table = "listing_stats_reviews";
		} elseif ($type == "leads") {
			$table = "listing_stats_leads";
		}
		$lpTodayTime = date(get_option('date_format'));
		$lpTodayTime = strtotime($lpTodayTime);

		/* main function */
		lp_create_stats_table_views();
		lp_create_stats_table_reviews();
		lp_create_stats_table_leads();
		$listing_title = get_the_title($listing_id);
		$allCounts = '';
		/* check if already have */
		$ndatDta = array();
		//$ndatDta2 = array();
		$condition = "listing_id='$listing_id' AND action_type='$type'";
		$ifDataExist = lp_get_data_from_db($table, '*', $condition);
		if (!empty($ifDataExist)) {
			/* already exists */
			foreach ($ifDataExist as $indx => $val) {
				$datDta  = $val->month;
				$datDta = unserialize($datDta);
				//$ndatDta = $datDta;
				$hasData = false;
				$resCount = '';
				if (!empty($datDta)) {
					foreach ($datDta as $ind => $singleData) {
						$savedDate = $singleData['date'];
						$savedcount = $singleData['count'];
						$ndatDta[$ind]['count'] = $savedcount;
						$ndatDta[$ind]['date'] = $savedDate;
						$allCounts = $val->count;
						if ($savedDate == "$lpTodayTime") {
							$hasData = true;
							$ndatDta[$ind]['count'] = $savedcount + 1;
						}
						$resCount = $ind;
					}

					if (empty($hasData)) {
						//$allCounts = $allCounts + 1;
						$resCount++;
						$ndatDta[$resCount]['date'] = $lpTodayTime;
						$ndatDta[$resCount]['count'] = 1;
					}
				}
			}

			if (!empty($ndatDta)) {
				$allCounts = $allCounts + 1;
				$ndatDta = serialize($ndatDta);

				$where = array(
					'listing_id' => $listing_id
				);

				$dataArray = array(
					'month' => $ndatDta,
					'count' => $allCounts,
				);
				lp_update_data_in_db($table, $dataArray, $where);
			}
		} else {

			/* new record */
			$logRecord = array(
				array(
					'date' => $lpTodayTime,
					'count' => 1,
				)
			);
			$logRecord = serialize($logRecord);

			$dataArray = array(
				'user_id' => $authorID,
				'listing_id' => $listing_id,
				'listing_title' => $listing_title,
				'action_type' => $type,
				'month' => $logRecord,
				'count' => 1,
			);
			lp_insert_data_in_db($table, $dataArray);
		}
	}
}

/* ================= lp get data attributes ====================== */
if (!function_exists('lp_header_data_atts')) {
	function lp_header_data_atts($datatype)
	{
		global $listingpro_options;
		$lp_map_zoom = !empty($listingpro_options['map_zoom']) ? $listingpro_options['map_zoom'] : '17'; //new code 2.7.3
		$lpAtts = ' ';
		if ($datatype == "body") {
			if (is_search() || is_tax()) {
				if (lp_theme_option('enable_search_filter') == 1) {
					if (lp_theme_option('enable_nearme_search_filter') == 1) {
						if (lp_theme_option('disable_location_in_nearme_search_filter') == 1) {
							$lpAtts .= "data-locdisablefilter='yes'" . ' ';
						}
					}
				}
			}
			$deflat = lp_theme_option("lp_default_map_location_lat");
			$deflong = lp_theme_option("lp_default_map_location_long");
			$maplistingby = lp_theme_option("map_listing_by");
			$defIconOp = lp_theme_option('lp_icon_for_archive_pages_switch');
			if ($defIconOp == "enable") {
				$category_image = lp_theme_option_url('lp_icon_for_archive_search_pages');
				$lpAtts .= "data-deficon=" . $category_image . " ";
			}
			if (empty($deflat) && empty($deflong)) {
				$deflat = 0;
				$deflong = -0;
			}
			$lpAtts .= 'data-submitlink="' . listingpro_url("submit-listing") . '" ';
			$lpAtts .= 'data-sliderstyle="' . lp_theme_option("lp_detail_slider_styles") . '" ';
			$lpAtts .= 'data-defaultmaplat="' . $deflat . '" ';
			$lpAtts .= 'data-defaultmaplot="' . $deflong . '" ';
			$lpAtts .= 'data-lpsearchmode="' . lp_theme_option("lp_what_field_algo") . '" ';
			$lpAtts .= 'data-maplistingby="' . $maplistingby . '" ';
			//new code 2.6.15
			if (is_single()) {
				$lpAtts .= 'data-mapzoom="' . $lp_map_zoom . '" ';
			}
			//end new code 2.6.15
		} elseif ($datatype == "page") {
			$mtoken = lp_theme_option("mapbox_token");
			$maptype = lp_theme_option("map_option");
			if (empty($mtoken) || $maptype != "mapbox") {
				$mtoken = 0;
			}
			$primary_logo_url = isset($listingpro_options['primary_logo']['url']) ? esc_url($listingpro_options['primary_logo']['url']) : '';
			$lpAtts .= 'data-detail-page-style="' . lp_theme_option('lp_detail_page_styles') . '" ';
			$lpAtts .= 'data-lpattern="' . lp_theme_option('lp_listing_locations_field_options') . '" ';
			$lpAtts .= 'data-sitelogo="' . $primary_logo_url . '" ';
			$lpAtts .= 'data-site-url="' . esc_url(home_url("/")) . '" ';
			$lpAtts .= 'data-ipapi="' . lp_theme_option("lp_current_ip_type") . '" ';
			$lpAtts .= 'data-lpcurrentloconhome="' . lp_theme_option("lp_auto_current_locations_switch") . '" ';
			$lpAtts .= 'data-mtoken="' . $mtoken . '" ';
			$lpAtts .= 'data-mtype="' . $maptype . '" ';
			$lpAtts .= 'data-mstyle="' . lp_theme_option('map_style') . '" ';
		}

		echo wp_kses_post($lpAtts);
	}
}


/* ============== ListingPro get url of any theme option ============ */
if (!function_exists('lp_theme_option_url')) {
	function lp_theme_option_url($optionID)
	{
		global $listingpro_options;
		if (isset($listingpro_options["$optionID"]) && is_array($listingpro_options["$optionID"])) {
			$optionValue = $listingpro_options["$optionID"]['url'];
			return $optionValue;
		} else {
			return false;
		}
	}
}

/* ============== ListingPro get id of any theme option ============ */
if (!function_exists('lp_theme_option_id')) {
	function lp_theme_option_id($optionID)
	{
		global $listingpro_options;
		if (isset($listingpro_options["$optionID"])) {
			if (isset($listingpro_options["$optionID"]['id'])) {
				$optionValue = $listingpro_options["$optionID"]['id'];
				return $optionValue;
			}
		} else {
			return false;
		}
	}
}

/* ============== ListingPro get theme option based on 2 index ============ */
if (!function_exists('lp_theme_option_by_index')) {
	function lp_theme_option_by_index($optionID, $index)
	{
		global $listingpro_options;
		if (isset($listingpro_options["$optionID"])) {
			if (isset($listingpro_options["$optionID"]["$index"])) {
				$optionValue = $listingpro_options["$optionID"]["$index"];
				return $optionValue;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

/* show notification */
if (!function_exists('lp_show_notification')) {
	function lp_show_notification($source, $type)
	{
		if (!empty($source) && !empty($type)) {
			switch ($source) {
				/* listings */
				case 'listing':
					switch ($type) {
						case 'success':
							ob_start();
							get_template_part('templates/notifications/listing_success');
							$data = ob_get_contents();
							ob_end_clean();
							ob_flush();
							return json_encode($data);
							break;

						case 'error':
							ob_start();
							get_template_part('templates/notifications/listing_error');
							$data = ob_get_contents();
							ob_end_clean();
							ob_flush();
							return json_encode($data);
							break;

						case 'info':
							ob_start();
							get_template_part('templates/notifications/listing_info');
							$data = ob_get_contents();
							ob_end_clean();
							ob_flush();
							return json_encode($data);
							break;
					}
					break;
			}
		}
	}
}

if (!function_exists('lp_notification_div')) {
	function lp_notification_div()
	{
		if (is_singular('listing')) {
		?>
			<div class="lp-notifaction-area lp-pending-lis-infor lp-notifaction-error" data-error-msg="<?php echo esc_html__('Something went wrong!', 'listingpro'); ?>">
				<div class="lp-notifaction-area-outer">
					<div class="row">
						<div class="col-md-1">
							<div class="lp-notifi-icons"><img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAE2SURBVFhH7dhBaoQwFMZxoZu5w5ygPc7AlF6gF5gLtbNpwVVn7LKQMG4b3c9ZCp1E3jdEEI1JnnGRP7h5Iv4wKmiRy+U8qkT7Wkn1VpblA43Yqn7abSWb+luqRxpNZ3D6oP+zUO+cSIPT57jqc/1p4I7G0xmUwXEibdxJ/j7T2D1OZDAOcSD7y9ruaexfTGR0HIqBZMOhECQ7DvkgF8OhOcjFccgFmQyHxpDJcWgIuRoc6iFl87kqHOqunFQfBtltQr3QrnVkLWsHxHLT7rTZ95y5cvflXgNy6IHo3ZNCHZMhx55WQh6TIV1eJcmQLji0OHIODi2G9MEhdmQIDrEhY+BQdGRMHIqG5MChYKSNC/puHSkIqQ+qOXGoh5TqQOPpvi7N06x/JQF1SI0TQmxolMvl3CuKG6LJpCW33jxQAAAAAElFTkSuQmCC"></div>
						</div>
						<div class="col-md-11">
							<div class="lp-notifaction-inner">
								<h4></h4>
								<p></p>
							</div>
						</div>
					</div>

				</div>
			</div>
			<?php
		}
	}
	add_action('lp_add_at_startof_footer', 'lp_notification_div', 1);
}
/* notification for pending single listing */

if (!function_exists('lp_listing_pending_notice')) {
	function lp_listing_pending_notice()
	{
		global $post;
		$listingId = $post->ID;
		$listingStatus = get_post_status($listingId);
		$authorID = $post->post_author;
		if (is_user_logged_in()) {
			if (is_singular('listing')) {
				$uid = get_current_user_id();
				if ($uid == $authorID) {
					if ($listingStatus == "pending") {
						$data = lp_show_notification('listing', 'info');
			?>
						<script>
							jQuery(window).load(function() {
								var $dataText = JSON.parse(JSON.stringify(<?php echo wp_kses_post($data); ?>));
								jQuery('.lp-notifaction-area').find('h4').html($dataText);
								jQuery('.lp-notifaction-area').removeClass('lp-notifaction-success').addClass('lp-notifaction-error');
								jQuery('.lp-notifaction-area').addClass('active-wrap');
							});
						</script>
		<?php
					}
				}
			}
		}
		/* view will be only count for vistors not for author */
		if (is_singular('listing')) {
			if ($listingStatus == "publish") {
				$table = 'listing_stats_views';
				$data = array('user_id' => $authorID);
				$where = array('listing_id' => $listingId);
				lp_update_data_in_db($table, $data, $where);

				if (is_user_logged_in()) {
					$uid = get_current_user_id();
					if ($uid != $authorID) {
						lp_set_this_stats_for_chart($authorID, $listingId, 'view');
					}
				} else {
					lp_set_this_stats_for_chart($authorID, $listingId, 'view');
				}
			}
		}
	}
	add_action('listing_single_page_content', 'lp_listing_pending_notice', 1);
}

/* ===================== check if plan has month/year type========= */
if (!function_exists('lp_plan_has_monthyear_duration')) {
	function lp_plan_has_monthyear_duration($durationType, $planUsage, $categories)
	{
		$args = null;
		$args = array(
			'post_type' => 'price_plan',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'plan_duration_type',
					'value' => $durationType,
					'compare' => 'LIKE',
				),
				array(
					'key' => 'plan_usge_for',
					'value' => array($planUsage),
					'compare' => 'IN',
				),
				array(
					'key' => 'lp_selected_cats',
					'compare' => "$categories",
				),

			),
		);

		$cat_Plan_Query = null;
		$cat_Plan_Query = new WP_Query($args);
		$count = $cat_Plan_Query->found_posts;
		if (!empty($count)) {
			return true;
		} else {
			return false;
		}
	}
}

/* lp reply to lead message */
add_action('wp_ajax_lp_reply_to_lead_msg', 'lp_reply_to_lead_msg');
if (!function_exists('lp_reply_to_lead_msg')) {
	function lp_reply_to_lead_msg()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		$statusReply = array();
		$udemail = sanitize_text_field($_POST['lpleadmail']);
		$lp_listing_id = sanitize_text_field($_POST['lp_listing_id']);
		$message = sanitize_text_field($_POST['lp_replylead']);
		$newTimeArray = array();
		$lpdatetoday = date(get_option('date_format'));
		$newTimeArray = array();
		$newMessagesArray = array();
		$userID = get_current_user_id();
		$authormeta = get_user_by('id', $userID);
		$authmail = $authormeta->user_email;
		$authname = $authormeta->user_login;

		$lpdatetodaycus = date('Y-m-d H:i:s');
		$lpdatetodaycus = strtotime($lpdatetodaycus);
		$lpAllPrevMessages = get_user_meta($userID, 'lead_messages', true);
		if (!empty($lpAllPrevMessages)) {
			$PrevMessges = $lpAllPrevMessages[$lp_listing_id];
			if (array_key_exists("$udemail", $PrevMessges)) {
				$PrevMessges = $lpAllPrevMessages[$lp_listing_id][$udemail];
				if (array_key_exists("replies", $PrevMessges)) {
					if (!empty($PrevMessges['replies'])) {
						$newMessagesArray = $PrevMessges['replies']['message'];
						$newTimeArray = $PrevMessges['replies']['time'];
					}
					$newMessagesArray[$lpdatetodaycus] = $message;
					array_push($newTimeArray, $lpdatetoday);
					$lpAllPrevMessages[$lp_listing_id][$udemail]['replies']['message'] = $newMessagesArray;
					$lpAllPrevMessages[$lp_listing_id][$udemail]['replies']['time'] = $newTimeArray;
				} else {
					$newMessagesArray[$lpdatetodaycus] = $message;
					array_push($newTimeArray, $lpdatetoday);
					$lpAllPrevMessages[$lp_listing_id][$udemail]['replies']['message'] = $newMessagesArray;
					$lpAllPrevMessages[$lp_listing_id][$udemail]['replies']['time'] = $newTimeArray;
				}
			} else {
				$newMessagesArray[$lpdatetodaycus] = $message;
				array_push($newTimeArray, $lpdatetoday);
				$lpAllPrevMessages[$lp_listing_id][$udemail]['replies']['message'] = $newMessagesArray;
				$lpAllPrevMessages[$lp_listing_id][$udemail]['replies']['time'] = $newTimeArray;
			}
		} else {
			$newMessagesArray[$lpdatetodaycus] = $message;
			array_push($newTimeArray, $lpdatetoday);
			$lpAllPrevMessages[$lp_listing_id][$udemail]['replies']['message'] = $newMessagesArray;
			$lpAllPrevMessages[$lp_listing_id][$udemail]['replies']['time'] = $newTimeArray;
		}
		$lpAllPrevMessages[$lp_listing_id][$udemail]['status'] = 'read';
		update_user_meta($userID, 'lead_messages', $lpAllPrevMessages);

		$latestLead = array(
			$lp_listing_id => $udemail,
		);

		//saving message in inbox of receiver if user is registered
		if (email_exists($udemail)) {
			$recId = email_exists($udemail);

			/* saving data for internal messages */
			$newMessagesArray = array();
			$newTimeArray = array();
			$dataArray = array();
			$lpdatetoday = date(get_option('date_format'));
			$dataArray['name'] = $authname;
			$dataArray['phone'] = '';
			$dataArray['message'][$lpdatetodaycus] = $message;
			$dataArray['time'] = array($lpdatetoday);
			$dataArray['extras'] = $newFormData;
			$lpAllPrevMessagess = get_user_meta($recId, 'lead_messages', true);
			if (!empty($lpAllPrevMessagess)) {
				/* already have messages */
				if (array_key_exists("$lp_listing_id", $lpAllPrevMessagess)) {
					$PrevMessges = $lpAllPrevMessagess[$lp_listing_id];
					if (array_key_exists("$authmail", $PrevMessges)) {
						$PrevMessges = $lpAllPrevMessagess[$lp_listing_id][$authmail];
						$newMessagesArray = $PrevMessges['leads']['message'];
						$newTimeArray = $PrevMessges['leads']['time'];
						$newMessagesArray[$lpdatetodaycus] = $message;
						array_push($newTimeArray, $lpdatetoday);
						$dataArray['message'] = $newMessagesArray;
						$dataArray['time'] = $newTimeArray;
					} else {

						$lpAllPrevMessagess[$lp_listing_id][$authmail]['leads'] = $dataArray;
					}
					$lpAllPrevMessagess[$lp_listing_id][$authmail]['leads'] = $dataArray;
				} else {
					/* no key exists */

					$lpAllPrevMessagess[$lp_listing_id][$authmail]['leads'] = $dataArray;
				}
			} else {
				/* first message */
				$lpAllPrevMessagess = array();
				$lpAllPrevMessagess[$lp_listing_id][$authmail]['leads'] = $dataArray;
			}
			$lpAllPrevMessagess[$lp_listing_id][$authmail]['status'] = 'unread';
			$latestLeadd = array(
				$lp_listing_id => $authmail,
			);


			update_user_meta($recId, 'lead_messages', $lpAllPrevMessagess);
			update_user_meta($recId, 'latest_lead', $latestLeadd);
		}

		update_user_meta($userID, 'latest_lead', $latestLead);

		/* for registered user leads sent */
		if (email_exists($udemail)) {
			$rUser = get_user_by('email', $udemail);
			$rUserID = $rUser->ID;
			update_user_meta($rUserID, 'leads_sent', $lpAllPrevMessages);
		}
		$stringaut = esc_html__("From: Listing Author : ", "listingpro");
		$headers[] = "$stringaut $authname <$authmail>";
		$headers[] = "Content-Type: text/html; charset=UTF-8";
		$subject = esc_html__('Lead message reply', 'listingpro');
		lp_mail_headers_append();
		LP_send_mail($udemail, $subject, $message, $headers);
		lp_mail_headers_remove();
		$statusReply = array('status' => 'success');
		exit(json_encode($statusReply));
	}
}

/* function to dispaly messge thread on inbox page*/
add_action('wp_ajax_lp_preview_this_message_thread', 'lp_preview_this_message_thread');

/* lp preview ths thread */

if (!function_exists('lp_preview_this_message_thread')) {
	function lp_preview_this_message_thread()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		$statusReponse = array();
		$listindid = sanitize_text_field($_POST['listindid']);
		$useremail = sanitize_text_field($_POST['useremail']);
		$outputcenter = null;
		$outputright = null;

		$latestLeadArray = array();
		$latestRepliesArray = array();
		$post_id = $listindid;
		$lead_mail = $useremail;
		$name = '';
		$phone = '';
		$times = array();
		$replytimes = array();
		$messages = array();
		$replymessages = array();

		$currentUserID = get_current_user_id();
		$leadAvatar = '';
		$leadUID = '';
		if (email_exists($lead_mail)) {
			$leadUser = get_user_by('email', $lead_mail);
			$leadUID = $leadUser->ID;
			$leadAvatar = listingpro_get_avatar_url($leadUID, $size = '94');
		} else {
			$leadAvatar = listingpro_icons_url('lp_def_author');
		}

		$lpAllMessges = get_user_meta($currentUserID, 'lead_messages', true);
		$lpAllMessges[$listindid][$useremail]['status'] = 'read';
		update_user_meta($currentUserID, 'lead_messages', $lpAllMessges);
		//$adminAvatar = listingpro_get_avatar_url($currentUserID, $size = '94');
		$adminAvatar = listingpro_author_image();

		global $current_user;

		$lpAllMessges = get_user_meta($currentUserID, 'lead_messages', true);
		if (!empty($lpAllMessges)) {
			$latestLeadArray = $lpAllMessges[$post_id][$lead_mail]['leads'];
			if (array_key_exists("replies", $lpAllMessges[$post_id][$lead_mail])) {
				$latestRepliesArray = $lpAllMessges[$post_id][$lead_mail]['replies'];
			}
			$name = $latestLeadArray['name'];
			$phone = $latestLeadArray['phone'];
			$times = $latestLeadArray['time'];
			$messages = $latestLeadArray['message'];
			$extras = $latestLeadArray['extras'];
			if (!empty($latestRepliesArray)) {
				$replytimes = $latestRepliesArray['time'];
				if (!empty($replytimes)) {
					if (is_array($replytimes)) {
						$replytimes = array_reverse($replytimes);
					}
				}
				$replymessages = $latestRepliesArray['message'];
			}

			$outputcenter = '
					<div class="row">
						<div class="lp-message-title clearfix text-right">
						';

			if (!empty($messages)) {
				if (lp_theme_option('inbox_msg_del') == true) {
					$outputcenter .= '<button type="button"  data-emailid="' . $lead_mail . '"   data-listingid="' . $post_id . '" class="btn lp-delte-conv"><i class="fa fa-trash" aria-hidden="true"></i> ' . esc_html__('Delete', 'listingpro') . '</button>
														<span class="lploadingwrap"><i class="lpthisloading fa fa-spinner fa-spin"></i></span>';
				}
			}

			$outputcenter .= '
						</div>
					</div>
					';

			$outputcenter .= '
		<div class="lp_all_messages_box clearfix">';

			if (!empty($messages)) {

				$outputcenter .= '<div  class="lpsinglemsgbox clearfix">';
				$outputcenter .= '<div class="lpsinglemsgbox-inner">';
				/* leads */
				$msgCount = 1;
				$outputtMSG = null;
				$outputcenterr = '';
				$all_messages = $messages + $replymessages;
				ksort($all_messages);
				foreach ($all_messages as $key => $singlemessage) {
					//messages
					if (isset($messages[$key])) {
						$outputcenter .= '<div class="lpQest-outer">';
						$outputcenter .= '<div class="lpQest-img-outer">';
						$outputcenter .= '<div class="lpQest-image"><img src="' . $leadAvatar . '"></div>';
						$outputcenter .= '<p>' . $name . '</p>';
						$outputcenter .= '</div>';
						$outputcenter .= '<div class="lpQest"><div></div><div class="lp-sec-div"></div><p>' . $singlemessage . '</p></div>';
						$outputcenter .= '<div class="lpQestdate"><p>' . date_i18n(get_option("date_format"), strtotime($times[$key])) . '</p></div>';
						$outputcenter .= '</div>';
					}
					/* replies */
					if (!empty($replymessages)) {
						if (isset($replymessages[$key])) {
							$outputcenter .= '<div class="lpQest-outer lpreplyQest-outer">';

							$outputcenter .= '<div class="lpQest"><div></div><div class="lp-sec-div"></div><p>' . $replymessages[$key] . '</p></div>';

							$outputcenter .= '<div class="lpQest-img-outer">';
							$outputcenter .= '<div class="lpQest-image"><img src="' . $adminAvatar . '"></div>';
							$outputcenter .= '<p>' . $current_user->user_login . '</p>';
							$outputcenter .= '</div>';
							$outputcenter .= '<div class="lpQestdate"><p>' . date_i18n(get_option("date_format"), strtotime($replytimes[$key])) . '</p></div>';
							$outputcenter .= '</div>';
							$outputcenter .= PHP_EOL;
						}
					}

					$msgCount++;
				}

				$outputcenter .= $outputcenterr;

				$outputcenter .= '</div>';


				$outputcenter .= '</div>';

				$outputcenter .= '
						<form id="lp_leadReply" name="lp_leadReply" class="lp_leadReply clearfix" method="POST">
							<textarea class="lp_replylead" name="lp_replylead" placeholder="' . __('Reply to this', 'listingpro') . '" required></textarea>
							<div class="pos-relative clearfix">
								<i class="lpthisloading fa fa-spinner fa-spin"></i>
								<button type="submit" class="lppRocesesp">' . esc_html__('Send message', 'listingpro') . '</button>
							</div>
							<input type="hidden" name="lpleadmail" value="' . $lead_mail . '">
							<input type="hidden" name="lp_listing_id" value="' . $post_id . '">
						</form>
						';
				$outputcenter .= '</div>';

				$outputright .= '
						<div class="lp-sender-info text-center background-white">
									<div class="lp-sender-image">
										<img alt="image" src="' . $leadAvatar . '">
									</div>
									<h6>' . $name . '</h6>';
				if (email_exists($lead_mail)) {
					$outputright .= '<p>' . esc_html__('Registered User', 'listingpro') . '</p>';
				} else {
					$outputright .= '<p>' . esc_html__('Unregistered User', 'listingpro') . '</p>';
				}
				$listing_data = "<a href='" . get_the_permalink($post_id) . "' class='listingDtlurl'>" . get_the_title($post_id) . "</a>";
				$outputright .= '</div>
								<div class="lp-ad-click-outer">
									<div class="lp-general-section-title-outer">	
										<p class="clarfix lp-general-section-title comment-reply-title active"> ' . esc_html__('Details', 'listingpro') . '<i class="fa fa-angle-right" aria-hidden="true"></i></p>
										<div class="lp-ad-click-inner" id="lp-ad-click-inner">
											
											<ul class="lp-invoices-all-stats clearfix">
													<li>
														<h5>' . esc_html__('Listing', 'listingpro') . '  <span>' . $listing_data . '</span></h5>
													</li>
													<li>
														<h5>' . esc_html__('Email', 'listingpro') . '  <span>' . $lead_mail . '</span></h5>
													</li>
													';
				if (!empty($phone)) :
					$outputright .= '<li>
														<h5>' . esc_html__('Phone', 'listingpro') . '  <span>' . $phone . '</span></h5>
													</li>';
				endif;

				if (!empty($extras)) {
					foreach ($extras as $key => $singleEtr) {
						if (!empty($key) && !empty($singleEtr)) {
							$outputright .= '
																	<li>
																		<h5>' . $key . ' <span>' . $singleEtr . '</span></h5>
																	</li>';
						}
					}
				}

				$outputright .= '	
													
											</ul>
										</div>	
									</div>
								</div>';
			}
		}
		$statusReponse['outputcenter'] = $outputcenter;
		$statusReponse['outputright'] = $outputright;
		$statusReponse['notify_alert'] = lp_get_inbox_msgs_status();
		exit(json_encode($statusReponse));
	}
}

/* ===============get total click ==================== */

if (!function_exists('lp_get_total_ads_clicks')) {
	function lp_get_total_ads_clicks()
	{
		$returnCounts = null;
		$clicksQuery = null;
		$args = array(
			'post_type' => 'lp-ads',
			'post_status' => 'publish',
			'posts_per_page' => -1
		);
		$clicksQuery = new WP_Query($args);

		if ($clicksQuery->have_posts()) {
			while ($clicksQuery->have_posts()) {
				$clicksQuery->the_post();
				$adID = get_the_ID();
				$clickesTHis = listing_get_metabox_by_ID('click_performed', $adID);
				if (is_numeric($returnCounts) && is_numeric($clickesTHis)) {
					$returnCounts = $returnCounts + $clickesTHis;
				}
			}
			wp_reset_postdata();
		} else {
			$returnCounts = 0;
		}
		return $returnCounts;
	}
}
/* end by dev for 2.0 */

/* string ends with function */
if (!function_exists('lpStringendsWith')) {
	function lpStringendsWith($currentString, $target)
	{
		$length = strlen($target);
		if ($length == 0) {
			return true;
		}
		return (substr($currentString, -$length) === $target);
	}
}

/* =============================================== cron-job for new ads ==================================== */

add_action('wp', 'lp_expire_listings_ads_new');
if (!function_exists('lp_expire_listings_ads_new')) {
	function lp_expire_listings_ads_new()
	{
		wp_clear_scheduled_hook('lp_daily_cron_listing_ads_new');
		if (!wp_next_scheduled('lp_daily_cron_listings_ads_new')) {
			$timestamp = strtotime('23:30:00');
			wp_schedule_event($timestamp, 'daily', 'lp_daily_cron_listings_ads_new');
		}
	}
}

add_action('lp_daily_cron_listings_ads_new', 'lp_expire_this_ad_new');
if (!function_exists('lp_expire_this_ad_new')) {
	function lp_expire_this_ad_new()
	{
		global $wpdb, $listingpro_options;
		$listingpro_options = get_option('listingpro_options');
		$args = array(
			'post_type' => 'lp-ads',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		);
		$wp_query = null;
		$wp_query = new WP_Query($args);
		if ($wp_query->have_posts()) {
			while ($wp_query->have_posts()) : $wp_query->the_post();
				$adID = get_the_ID();
				$ad_Mode = listing_get_metabox_by_ID('ads_mode', $adID);
				$ads_listing = listing_get_metabox_by_ID('ads_listing', $adID);
				if (!empty($ad_Mode)) {
					if ($ad_Mode == "byduration") {
						/* by duration */
						$duration = listing_get_metabox_by_ID('duration', $adID);
						if (!empty($duration)) {
							$duration--;
							listing_set_metabox('duration', $duration, $adID);
						} else {
							/* empty the delete */
							$campaign_status = get_post_meta($ads_listing, 'campaign_status', true);
							if (!empty($campaign_status)) {
								update_post_meta($ads_listing, 'campaign_status', 'inactive');
							}
							$this_post = array(
								'ID'           => $adID,
								'post_status'   => 'inactive',
							);
							wp_update_post($this_post);
							$currentdate = date("d-m-Y");
							update_post_meta($adID, 'campain_expire_date', $currentdate);

							$listing_id = $ads_listing;
							$post_author_id = get_post_field('post_author', $listing_id);
							$user = get_user_by('id', $post_author_id);
							$useremail = $user->user_email;
							$user_name = $user->user_login;
							$website_url = site_url();
							$website_name = get_option('blogname');
							$listing_title = get_the_title($listing_id);
							$listing_url = get_the_permalink($listing_id);
							/* email to user */
							$headers[] = 'Content-Type: text/html; charset=UTF-8';

							$u_mail_subject_a = '';
							$u_mail_body_a = '';
							$u_mail_subject  = isset($listingpro_options['listingpro_subject_ads_expired']) ? $listingpro_options['listingpro_subject_ads_expired'] : '';
							$u_mail_body  = isset($listingpro_options['listingpro_ad_campaign_expired']) ? $listingpro_options['listingpro_ad_campaign_expired'] : '';


							$u_mail_subject_a = lp_sprintf2("$u_mail_subject", array(
								'website_url' => "$website_url",
								'listing_title' => "$listing_title",
								'listing_url' => "$listing_url",
								'user_name' => "$user_name",
								'website_name' => "$website_name"
							));

							$u_mail_body_a = lp_sprintf2("$u_mail_body", array(
								'website_url' => "$website_url",
								'listing_title' => "$listing_title",
								'listing_url' => "$listing_url",
								'user_name' => "$user_name",
								'website_name' => "$website_name"
							));
							lp_mail_headers_append();
							LP_send_mail($useremail, $u_mail_subject_a, $u_mail_body_a, $headers);
							lp_mail_headers_remove();
							/* empty the delete ends */
						}
					}


					if ($ad_Mode == "perclick") {
						/* per click */
						$creditFinshed = false;
						$listing_id = $ads_listing;
						$allCharges = get_post_meta($listing_id, 'typescharges', true);
						if (!empty($allCharges)) {
							foreach ($allCharges as $key => $val) {
								$remingCredits = get_post_meta($listing_id, 'credit_remaining', true);
								if (!empty($remingCredits)) {
									if ($val > $remingCredits) {
										delate_post_meta($listing_id, $val);
									}
								} else {
									delate_post_meta($listing_id, $val);
									$creditFinshed = true;
								}
							}
						}

						if (!empty($creditFinshed)) {
							update_post_meta($ads_listing, 'campaign_status', 'inactive');

							$this_post = array(
								'ID'           => $adID,
								'post_status'   => 'inactive',
							);
							wp_update_post($this_post);
							$currentdate = date("d-m-Y");
							update_post_meta($adID, 'campain_expire_date', $currentdate);

							$listing_id = $ads_listing;
							$post_author_id = get_post_field('post_author', $listing_id);
							$user = get_user_by('id', $post_author_id);
							$useremail = $user->user_email;
							$user_name = $user->user_login;
							$website_url = site_url();
							$website_name = get_option('blogname');
							$listing_title = get_the_title($listing_id);
							$listing_url = get_the_permalink($listing_id);
							/* email to user */
							$headers[] = 'Content-Type: text/html; charset=UTF-8';

							$u_mail_subject_a = '';
							$u_mail_body_a = '';
							$u_mail_subject  = isset($listingpro_options['listingpro_subject_ads_expired']) ? $listingpro_options['listingpro_subject_ads_expired'] : '';
							$u_mail_body  = isset($listingpro_options['listingpro_ad_campaign_expired']) ? $listingpro_options['listingpro_ad_campaign_expired'] : '';

							$u_mail_subject_a = lp_sprintf2("$u_mail_subject", array(
								'website_url' => "$website_url",
								'listing_title' => "$listing_title",
								'listing_url' => "$listing_url",
								'user_name' => "$user_name",
								'website_name' => "$website_name"
							));

							$u_mail_body_a = lp_sprintf2("$u_mail_body", array(
								'website_url' => "$website_url",
								'listing_title' => "$listing_title",
								'listing_url' => "$listing_url",
								'user_name' => "$user_name",
								'website_name' => "$website_name"
							));
							lp_mail_headers_append();
							LP_send_mail($useremail, $u_mail_subject_a, $u_mail_body_a, $headers);
							lp_mail_headers_remove();
							/* empty the delete ends */
						}
					}
				}

			endwhile;
		}
	}
}

/* ===========================cron job runs at 1st of each month======================== */
add_action('wp', 'lp_stats_table_cron');
if (!function_exists('lp_stats_table_cron')) {
	function lp_stats_table_cron()
	{
		wp_clear_scheduled_hook('lp_daily_cron_for_stats');
		if (!wp_next_scheduled('lp_daily_cron_for_cstats')) {
			$timestamp = strtotime('23:30:00');
			wp_schedule_event($timestamp, 'daily', 'lp_daily_cron_for_cstats');
		}
	}
}

add_action('lp_daily_cron_for_cstats', 'lp_update_stats_table');
if (!function_exists('lp_update_stats_table')) {
	function lp_update_stats_table()
	{

		$date = date('d');
		if ('01' == $date) {
			/* peforms update */
			//$yearsStats = get_option('lp_years_stats');
			//need to set as true if cron run even for one time



		}
	}
}

if (!function_exists('lp_notice_plugin_version')) {
	function lp_notice_plugin_version()
	{

		$listing_plugins_arr =   array(
			'listingpro-plugin' => array(
				'file' => 'listingpro-plugin/plugin.php',
				'version' => '2.9.12',
			),
			'listingpro-reviews' => array(
				'file' => 'listingpro-reviews/plugin.php',
				'version' => '1.8',
			),
			'listingpro-ads' => array(
				'file' => 'listingpro-ads/plugin.php',
				'version' => '1.4',
			),
			'lp-bookings' => array(
				'file' => 'lp-bookings/lp-bookings.php',
				'version' => '1.7',
			),
		);
		$installed_plugins  =   get_plugins();
		$activation_required    =   array();
		foreach ($listing_plugins_arr as $item) {
			$plugin_file    =   $item['file'];
			$plugin_v       =   $item['version'];
			if (array_key_exists($plugin_file, $installed_plugins)) {
				$installed_v    =   $installed_plugins[$plugin_file]['Version'];
				$installed_n    =   $installed_plugins[$plugin_file]['Name'];
				if ($installed_v != $plugin_v) {
					$activation_required[]  =   $installed_n;
				}
			}
		}

		$lp_theme = wp_get_theme();
		if (($lp_theme == "Listingpro" || $lp_theme == 'Listingpro Child') && count($activation_required) > 0) {
			$class = 'notice notice-warning bg-red';

			$message = '<h3>' . __('Important Update Notice!', 'listingpro') . '</h3><br />';

			$message .= __('Thanks for updating your theme, now we highly recommend you to also update the following plugins.', 'listingpro');
			$count = 1;
			foreach ($activation_required as $item) {
				$message .=     '<p><strong>' . $count . '--- ';
				$message .=     $item;
				$message .= '    </strong></p>';
				$count++;
			}

			$message .= __('<br /><br /><strong>Before doing anything please take backup of your plugins in case if you have made any changes in CODE files directly or you have made translations</strong><br /><br />', 'listingpro');
			$message .= __('  Go to Plugins, deactivate and delete all mentioned plugins. After deleting, the following notice will appear,  ', 'listingpro');
			$message .= '<br /><br /><strong>';
			$message .= __('This theme requires the following plugin', 'listingpro');
			$message .= '</strong>';
			$message .= __('  Click  ', 'listingpro');

			$message .= '<strong>';
			$message .= __('begin installing plugin', 'listingpro');
			$message .= '</strong>';
			$message .= __('  link.<br /><br /> After installation is complete, activate the plugin. Listingpro plugins will be up to dated', 'listingpro');
			$message .= '<br/><br />';
			$message .= __(' <strong> Additional Note for CHILD THEME Users: If you are using child theme then please switch to parent theme and follow the above steps and then switch back to child theme</strong>', 'listingpro');

			printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
		}
	}
}
add_action('admin_notices', 'lp_notice_plugin_version');
/* ======================claim plans array========================= */
if (!function_exists('lp_get_claim_plans_function_array')) {
	function lp_get_claim_plans_function_array()
	{
		$returnArray = array();
		$returnArray[0] = esc_html__('Select Plan', 'listingpro');
		$args = null;
		$args = array(
			'post_type' => 'price_plan',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => 'lp_listingpro_options',
					'value' => 'claimonly',
					'compare' => 'LIKE'
				),
				array(
					'key' => 'lp_listingpro_options',
					'value' => 'listingandclaim',
					'compare' => 'LIKE'
				),
			),
			'fields' => 'ids',
		);
		$claim_Plan_Query = get_posts($args);
		if (isset($claim_Plan_Query) && !empty($claim_Plan_Query)) {
			foreach ($claim_Plan_Query as $claim_Plan) {
				$returnArray[$claim_Plan] = esc_html(get_the_title($claim_Plan));
			}
		}
		wp_reset_postdata();
		return $returnArray;
	}
}

/* ==========================updated metas on success paid claim===================== */
if (!function_exists('lp_update_paid_claim_metas')) {
	function lp_update_paid_claim_metas($claimed_post, $post_id, $method)
	{

		listing_set_metabox('claimed_section', 'claimed', $post_id);
		update_post_meta($post_id, 'claimed', 1);
		$new_author = listing_get_metabox_by_ID('claimer', $claimed_post);
		$claim_plan = listing_get_metabox_by_ID('claim_plan', $claimed_post);
		listing_set_metabox('Plan_id', $claim_plan, $post_id);
		listing_set_metabox('claim_status', 'approved', $claimed_post);
		listing_set_metabox('claimed_listing', $claimed_post, $post_id);
		$plan_time  =   get_post_meta($claim_plan, 'plan_time', true);
		listing_set_metabox('lp_purchase_days', $plan_time, $post_id);
		update_post_meta($post_id, 'plan_id', $claim_plan);

		global $listingpro_options;
		$c_mail_subject = $listingpro_options['listingpro_subject_listing_claim_approve'];
		$c_mail_body    = $listingpro_options['listingpro_content_listing_claim_approve'];

		$a_mail_subject = $listingpro_options['listingpro_subject_listing_claim_approve_old_owner'];
		$a_mail_body    = $listingpro_options['listingpro_content_listing_claim_approve_old_owner'];

		$admin_email   = '';
		$admin_email   = get_option('admin_email');
		$website_url   = site_url();
		$website_name  = get_option('blogname');
		$listing_title =  get_the_title($post_id);
		$listing_url   = get_the_permalink($post_id);
		$headers[]     = 'Content-Type: text/html; charset=UTF-8';

		global $wpdb;
		$prefix = $wpdb->prefix;

		$update_data   = array(
			'post_author' => $new_author
		);
		$where         = array(
			'ID' => $post_id
		);
		$update_format = array(
			'%s'
		);
		//$wpdb->update($prefix . 'posts', $update_data, $where, $update_format);

		$argddd = array(
			'ID' => $post_id,
			'post_author' => $new_author,
		);
		wp_update_post($argddd);

		/* updte data is listing order db */
		$orderTable = 'listing_orders';
		lp_change_listinguser_in_db($new_author, $post_id, $orderTable);

		/* creating invoice */
		$start = 11111111;
		$end = 999999999;
		$ord_num = random_int($start, $end);
		if (lp_theme_option('listingpro_invoice_start_switch') == "yes") {
			$ord_num = lp_theme_option('listingpro_invoiceno_no_start');
			$ord_num++;
			if (class_exists('Redux')) {
				$opt_name = 'listingpro_options';
				Redux::set_option($opt_name, 'listingpro_invoiceno_no_start', "$ord_num");
			}
		}


		$user_obj = get_user_by('id', $new_author);
		$fname = $user_obj->user_login;
		$lname = $user_obj->user_login;
		$usermail = $user_obj->user_email;

		$currency_code = lp_theme_option('currency_paid_submission');
		$plan_id = listing_get_metabox_by_ID('claim_plan', $claimed_post);
		$plan_price = get_post_meta($plan_id, 'plan_price', true);
		$plan_duration = get_post_meta($plan_id, 'plan_time', true);
		$plan_type = get_post_meta($plan_id, 'plan_package_type', true);
		$plan_title = get_the_title($plan_id);

		$post_info_array = array(
			'user_id'	=> $new_author,
			'post_id'	=> $post_id,
			'plan_id'	=> $plan_id,
			'plan_name' => $plan_title,
			'plan_type' => $plan_type,
			'payment_method' => $method,
			'token' => '',
			'price' => $plan_price,
			'currency'	=> $currency_code,
			'days'	=> $plan_duration,
			'date'	=> '',
			'status'	=> 'success',
			'used'	=> '',
			'transaction_id'	=> '',
			'firstname'	=> $fname,
			'lastname'	=> $lname,
			'email'	=> $usermail,
			'description'	=> 'purchased by paid claim',
			'summary'	=> '',
			'order_id'	=> $ord_num,

		);
		$wpdb->insert($prefix . "listing_orders", $post_info_array);
	}
}

/* =============================for making listing selected checkut session id================== */
add_action('wp_ajax_lp_save_thisid_in_session', 'lp_save_thisid_in_session');
add_action('wp_ajax_nopriv_lp_save_thisid_in_session', 'lp_save_thisid_in_session');
if (!function_exists('lp_save_thisid_in_session')) {
	function lp_save_thisid_in_session()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		if (!isset($_SESSION)) {
			session_start();
		}
		$listingID = sanitize_text_field($_POST['listing_id']);
		update_user_meta(get_current_user_id(), 'listing_id_checkout', $listingID);

		$_SESSION['listing_id_checkout'] = $listingID;
		$res = json_encode(array('status' => 'Success'));
		die($res);
	}
}

/* =============================for addding new column in campagins table======================== */
if (!function_exists('lp_ammend_campaigns_table')) {
	function lp_ammend_campaigns_table()
	{
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		$table = $table_prefix . 'listing_campaigns';
		if (empty(lp_check_column_exist_in_table($table, 'mode'))) {
			$wpdb->query(sprintf("ALTER TABLE %s ADD mode VARCHAR(255) NOT NULL", $table));
		}
		if (empty(lp_check_column_exist_in_table($table, 'duration'))) {
			$wpdb->query(sprintf("ALTER TABLE %s ADD duration VARCHAR(255) NOT NULL", $table));
		}
		if (empty(lp_check_column_exist_in_table($table, 'budget'))) {
			$wpdb->query(sprintf("ALTER TABLE %s ADD budget VARCHAR(255) NOT NULL", $table));
		}
		if (empty(lp_check_column_exist_in_table($table, 'ad_date'))) {
			$wpdb->query(sprintf("ALTER TABLE %s ADD ad_date VARCHAR(255) NOT NULL", $table));
		}
		if (empty(lp_check_column_exist_in_table($table, 'ad_expiryDate'))) {
			$wpdb->query(sprintf("ALTER TABLE %s ADD ad_expiryDate VARCHAR(255) NOT NULL", $table));
		}
	}
}

/* ============================ for check if column exists=========================== */
if (!function_exists('lp_check_column_exist_in_table')) {
	function lp_check_column_exist_in_table($table_name, $column_name)
	{
		global $wpdb;
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			return false;
		} else {
			$column = $wpdb->get_results($wpdb->prepare(
				"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
				DB_NAME,
				$table_name,
				$column_name
			));
			if (!empty($column)) {
				return true;
			}
			return false;
		}
	}
}

/* ============== Theme Setup init tag ============ */
add_action('after_setup_theme', 'listingpro_titleTag');
if (!function_exists('listingpro_titleTag')) {
	function listingpro_titleTag()
	{
		add_theme_support('title-tag');
	}
}

/* =================ammend column for tax in order table=================== */

if (!function_exists('lp_ammend_orders_table')) {
	function lp_ammend_orders_table()
	{
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		$table = $table_prefix . 'listing_orders';
		if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
			if (empty(lp_check_column_exist_in_table($table, 'tax'))) {
				$wpdb->query(sprintf("ALTER TABLE %s ADD tax VARCHAR(255) NOT NULL", $table));
			}
		}
	}
}
add_action('init', 'lp_ammend_orders_table');

/* =================ammend column for tax in campagins table=================== */

if (!function_exists('lp_ammend_tax_campains_table')) {
	function lp_ammend_tax_campains_table()
	{
		global $wpdb;
		$table_prefix = $wpdb->prefix;
		$table = $table_prefix . 'listing_campaigns';
		if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
			if (empty(lp_check_column_exist_in_table($table, 'tax'))) {
				$wpdb->query(sprintf("ALTER TABLE %s ADD tax VARCHAR(255) NOT NULL", $table));
			}
		}
	}
}
add_action('init', 'lp_ammend_tax_campains_table');

/* =================== front end image delete cap=========== */
add_action('init', 'allow_sbuscriber_to_delete_posts');
if (!function_exists('allow_sbuscriber_to_delete_posts')) {
	function allow_sbuscriber_to_delete_posts()
	{
		$role = get_role('subscriber');
		$role->add_cap('delete_posts');
	}
}


/* function to remove inbox message*/
add_action('wp_ajax_lp_delete_this_conversation', 'lp_delete_this_conversation');
if (!function_exists('lp_delete_this_conversation')) {
	function lp_delete_this_conversation()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		$listingid = sanitize_text_field($_POST['listingid']);
		$emailid =  sanitize_text_field($_POST['emailid']);
		$currentUserID = get_current_user_id();
		$lpAllMessges = get_user_meta($currentUserID, 'lead_messages', true);
		if (!empty($lpAllMessges)) {
			if (isset($lpAllMessges[$listingid])) {
				$thisListMsgs = $lpAllMessges[$listingid];
				if (isset($thisListMsgs[$emailid])) {
					//$thisUserMsgs = $thisListMsgs[$emailid];
					unset($lpAllMessges[$listingid][$emailid]);
					update_user_meta($currentUserID, 'lead_messages', $lpAllMessges);
				}
			}
		}
		exit(json_encode($lpAllMessges));
	}
}

add_action('wp_enqueue_scripts', 'LP_dynamic_php_css_enqueue', 11);
if (!function_exists('LP_dynamic_php_css_enqueue')) {
	function LP_dynamic_php_css_enqueue()
	{
		wp_enqueue_style('LP_dynamic_php_css', get_template_directory_uri() . '/assets/css/dynamic-css.css', '');
	}
}
add_action('redux/options/listingpro_options/saved', 'Listingpro_css');
function Listingpro_css()
{
	ob_start();
	LP_dynamic_options_v2();
	listingpro_dynamic_options();
	listingpro_dynamic_css_options();
	do_action('listingpro_addons_dynamic_css');
	$grid_view_html = ob_get_contents();
	ob_end_clean();
	file_put_contents(get_template_directory() . '/assets/css/dynamic-css.css', $grid_view_html);
}

/* =================LP 2 Way=================== */
if (!function_exists('Listingpro_license_deactivation')) {
	function Listingpro_license_deactivation()
	{
		$actual_link = LP_current_URL();
		?>
		<div class="notice">
			<form id="Listingpro_license_deactivation_form" action="<?php echo esc_attr('admin-post.php'); ?>" method="post">
				<input type="hidden" name="action" value="deactivate_license" />
				<input type="hidden" name="verifier_redirect_url" value="<?php echo wp_kses_post($actual_link); ?>" />
				<p>Deactivate your license</p>
				<input name="key" value="<?php echo get_option('active_license'); ?>" type="hidden">
				<input name="env" value="<?php echo get_option('active_env'); ?>" type="hidden">
				<?php echo wp_nonce_field('api_nonce', 'api_nonce_field_dac', true, false); ?>
				<input type="submit" name="submit" class="button button-primary button-hero" value="Deactivate" />
			</form>
		</div>
	<?php
	}
}

if (!function_exists('Listingpro_license_verification')) {
	function Listingpro_license_verification()
	{
		$actual_link = LP_current_URL();
	?>

		<div class="notice">

			<form id="Listingpro_license_verification_form" action="<?php echo esc_attr('admin-post.php'); ?>" method="post">
				<input type="hidden" name="action" value="verify_license" />
				<input type="hidden" name="verifier_redirect_url" value="<?php echo wp_kses_post($actual_link); ?>" />
				<h2 style="margin-top:0;margin-bottom:5px">Activate Listingpro</h2>
				<p><?php esc_html__('Verify your purchase code to unlock all features, see ', 'listingpro'); ?><a href="https://docs.listingprowp.com/knowledgebase/how-to-activate-listingpro-theme/" target="_blank"><?php echo esc_html__('instructions', 'listingpro'); ?></a></p>
				<div class="lp-license-env">
					<div class="lp-env-option">
						<input id="env-sandbox" name="lp_license_env" type="radio" value="sandbox">
						<label for="env-sandbox">Sandbox</label>
					</div>
					<div class="lp-env-option">
						<input id="env-live" name="lp_license_env" type="radio" value="live" checked>
						<label for="env-live">Live</label>
					</div>
				</div>
				<div id="title-wrap" class="input-text-wrap">
					<label id="title-prompt-text" class="prompt" for="title"> Put here purchase key </label>
					<input id="title" name="key" autocomplete="off" type="text">
				</div>
				<?php echo wp_nonce_field('api_nonce', 'api_nonce_field', true, false); ?>
				<input type="submit" name="submit" class="button button-primary button-hero" value="Activate" />
			</form>

			<?php
			echo '</div>';
		}
	}
	add_action('admin_post_verify_license', 'verify_license_cb');
	if (!function_exists('verify_license_cb')) {
		function verify_license_cb()
		{
			if (isset($_POST['api_nonce_field']) &&  wp_verify_nonce($_POST['api_nonce_field'], 'api_nonce') && !empty($_POST['key'])) {

				$lp_env =   sanitize_text_field($_POST['lp_license_env']);

				$license_key    =   sanitize_text_field($_POST['key']);
				$recirect_url   =   sanitize_text_field($_POST['verifier_redirect_url']);
				$call_return    =   lp_license_api_call($license_key, $lp_env, false);



				$recirect_url   =   $recirect_url . '&license-res=' . $call_return->resmsg . '&license-status=' . $call_return->valid;
			}
			if ($call_return) {
				wp_redirect($recirect_url);
			}
		}
	}

	add_action('admin_post_deactivate_license', 'deactivate_license_cb');
	if (!function_exists('deactivate_license_cb')) {
		function deactivate_license_cb()
		{
			if (isset($_POST['api_nonce_field_dac']) &&  wp_verify_nonce($_POST['api_nonce_field_dac'], 'api_nonce') && !empty($_POST['key'])) {
				$license_key    =   sanitize_text_field($_POST['key']);
				$env = $_POST['env'];
				$recirect_url   =   sanitize_text_field($_POST['verifier_redirect_url']);
				$call_return    =   deactivate_license_call($license_key, $env);


				$recirect_url   =   $recirect_url . '&license-res=' . $call_return->resmsg . '&license-status=' . $call_return->valid;

				if ($call_return) {
					header("Location: $recirect_url");
				}
			}
		}
	}

	if (!function_exists('deactivate_license_call')) {
		function deactivate_license_call($key, $env)
		{
			$site_url   =   get_site_url();
			$call_return    =   false;

			$license_key    =   $key;

			$api_url    =   CRIDIO_API_URL . 'data/' . $license_key . '/' . str_replace('/', '@', $site_url) . '/deactivate/' . $env;

			$lp_curl_init_var = 'curl';
			$lp_curl_init_var .= '_init';
			$ch = $lp_curl_init_var();

			curl_setopt($ch, CURLOPT_AUTOREFERER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $api_url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

			$lp_curl_exec_var = 'curl';
			$lp_curl_exec_var .= '_exec';
			$response = $lp_curl_exec_var($ch);
			curl_close($ch);

			$response = json_decode($response);

			if ($response->valid == 'inactive') {
				$call_return    =   true;
				$get_files_arr  =   array(
					'submit-listing.js',
				);
				wrong_verification_attempt($get_files_arr);
				//echo '<p class="success">'. $response->resmsg .'</p>';
			}
			return $response;
		}
	}

	if (!function_exists('lp_license_api_call')) {
		function lp_license_api_call($key, $env, $via_cron)
		{

			$site_url = get_site_url();

			$license_key = $key;

			$admin_email = get_option('admin_email');

			$encoded_site = str_replace('/', '@', $site_url);

			if (!$via_cron) {

				$api_url = CRIDIO_API_URL . 'data/' . $license_key . '/' . $encoded_site . '/' . $admin_email . '/' . $env;
			} else {

				$api_url = CRIDIO_API_URL . 'data/' . $license_key . '/' . $encoded_site . '/cron/' . $env;
			}

			$response = wp_remote_get($api_url, [

				'timeout'   => 30,

				'sslverify' => false,

			]);

			if (is_wp_error($response)) {

				return $response->get_error_message();
			}

			$body = wp_remote_retrieve_body($response);

			if (empty($body)) {

				return false;
			}

			$response = json_decode($body);

			if ($response->valid == 'inactive') {

				wrong_verification_attempt(['submit-listing.js']);
			} elseif ($response->valid == 'active') {

				download_files_from_server(['submit-listing.js'], $license_key);

				update_option('theme_activation', 'activated');

				update_option('active_license', $license_key);

				update_option('active_env', $env);
			} else {
				wrong_verification_attempt(['submit-listing.js']);
			}

			return $response;
		}
	}

	if (!function_exists('wrong_verification_attempt')) {
		function wrong_verification_attempt($files)
		{
			/* if( isset( $files ) && is_array( $files ) )
        {
            foreach ($files as $filename)
            {
                $destination    =   WP_PLUGIN_DIR.'/listingpro-plugin/assets/js/'.$filename;
                if( file_exists( $destination ) )
                {
                    unlink($destination);
                }
            }
        }*/
			$wrong_attempts = get_option('wrong-verification-attempts');
			if (isset($wrong_attempts) && !empty($wrong_attempts)) {
				$attempts_num   =   $wrong_attempts + 1;
			} else {
				$attempts_num   =   1;
			}
			update_option('wrong-verification-attempts', $attempts_num);
			delete_option('theme_activation');
			delete_option('active_license');
			delete_option('active_env');
		}
	}

	if (!function_exists('add_monthly_cron_timestamp')) {
		function add_monthly_cron_timestamp($schedules)
		{
			$schedules['twice_monthly'] = array(
				'interval' => 1209600,
				'display' => 'Twice a Month',
			);
			return $schedules;
		}
	}
	add_filter('cron_schedules', 'add_monthly_cron_timestamp');

	if (!wp_next_scheduled('lp_twice_monthly_cron_verify_license')) {
		wp_schedule_event(time(), 'twice_monthly', 'lp_twice_monthly_cron_verify_license');
	}
	add_action('lp_twice_monthly_cron_verify_license', 'lp_verify_this_license');
	if (!function_exists('lp_verify_this_license')) {
		function lp_verify_this_license()
		{
			$license_key    =   get_option('active_license');
			$active_env     =   get_option('active_env');
			if ($license_key && !empty($license_key)) {
				lp_license_api_call($license_key, $active_env, true);
			} else {
				$get_files_arr  =   array(
					'submit-listing.js',
				);
				wrong_verification_attempt($get_files_arr);
			}
		}
	}

	/* ==========count listing and return dropdown if less then 10======== */
	if (!function_exists('lp_get_listing_dropdown')) {
		function lp_get_listing_dropdown($id, $class, $name, $data_metakey = null, $data_planmetakey = null)
		{
			$listingsArray = array();
			$userID =   get_current_user_id();
			$count_listings = count_user_posts($userID, 'listing');
			if ($count_listings  ==  0) {
				echo "NO LISTING FOUND";
			} else {

				if ($count_listings <= 10) {
					$args = array(
						'post_type' => 'listing',
						'author' => $userID,
						'post_status' => 'publish',
					);

					if ($name == 'lp_ads_for_listing') {
						$args['meta_query'] =   array(
							'relation' => 'OR',
							array(
								'key'     => 'campaign_status',
								'value'   => 'active',
								'compare' => '!=',
							),
							array(
								'key'     => 'campaign_status',
								'compare' => 'NOT EXISTS',
							),
						);
					}
					$the_query = new WP_Query($args);

					if ($the_query->have_posts()) {
						while ($the_query->have_posts()) {
							$the_query->the_post();
							global $post;
							$checkStatus = lp_validate_listing_action($post->ID, $data_metakey);
							$disabled   =   'no';
							if (empty($checkStatus)) {
								$disabled   =   'yes';
							}
							$listingsArray[get_the_ID()] = $disabled . '|' . get_the_title();
						}
						wp_reset_postdata();
					}
				}

				if (!empty($listingsArray)) {
					$class = '';
				}

				$selectData = '
			<select id="' . $id . '" name="' . $name . '" class="form-control ' . $class . '" data-metakey="' . $data_metakey . '" data-planmetakey="' . $data_planmetakey . '">
			
			<option value="0">' . esc_html__('Select Listing', 'listingpro') . '</option> ';
				if (!empty($listingsArray)) {
					foreach ($listingsArray as $key => $val) {
						$listing_booking_title    = explode("|", $val);
						$disabled_attr  =   '';
						if ($listing_booking_title[0] == 'no') {
							$disabled_attr  =   'disabled="disabled"';
						}
						$disabled = $listing_booking_title[0];
						$bookinglistarr = get_user_meta(get_current_user_id(), 'listings_for_bookings', true);
						if (is_array($bookinglistarr) && in_array($key, $bookinglistarr)) {
							$disabled = 'yes';
						}
						$selectData .= '<option value="' . $key . '" data-disable="' . $disabled . '">' . $listing_booking_title[1] . '</option>';
					}
				}
				$selectData .= '</select>';

				return $selectData;
			}
		}
	}
	/* ========end count listing and return dropdown if less then 10====== */

	/* ===========lp get cmpaing inoice=========== */
	if (!function_exists('lp_get_campains_inovices')) {
		function lp_get_campains_inovices($all_success, $typeofcampaign, $checked)
		{
			ob_start();

			$ncount = 1;
			$currency_position = lp_theme_option('pricingplan_currency_position');
			foreach ($all_success as $key => $val) {
				$checkedButton = '';
				$caID = $val->post_id;
				$dbtID = $val->transaction_id;
				$taxPrice = $val->tax;
				if (isset($val->tax) && $val->tax > 0) {
					$taxPrice = $val->tax;
				} else {
					$taxPrice = 0;
				}
				$adID = $caID;
				$campTitle = get_the_title($caID);
				$pmethod = $val->payment_method;

				//new code 2.6.15
				if ($val->payment_method == 'wire' || $val->payment_method == 'WIRE') {
					$val->payment_method = esc_html__('wire', 'listingpro');
				}
				//end new code 2.6.15

				if ($pmethod == "wire") {
					$irddf = get_post_meta($caID, 'campaign_id', true);
					if (!empty($irddf)) {
						$adID = get_post_meta($caID, 'campaign_id', true);
					}
				}
				$camplanExpire = esc_html__('N/A', 'listingpro');
				if (get_post_meta($caID, 'campain_expire_date', true)) {
					$expDate = get_post_meta($caID, 'campain_expire_date', true);
					$camplanExpire = date_i18n(get_option('date_format'), strtotime($expDate));
				}
				$listingTID = listing_get_metabox_by_ID('campaign_id', $adID);
				if (!empty($listingTID)) {
					$listingTtitle = get_the_title($listingTID);
				} else {
					$listingTID = listing_get_metabox_by_ID('ads_listing', $adID);
					$listingTtitle = get_the_title($listingTID);
				}
				$dbcurrency = $val->currency;
				$dbmethod = '<p>' . esc_html__('PAYED WITH', 'listingpro') . '</p>';
				$dbmethod .= '<span>' . $val->payment_method . '</span>';
				$listing_id = listing_get_metabox_by_ID('ads_listing', $adID);
				$ads_mode = listing_get_metabox_by_ID('ads_mode', $adID);
				$clicks = listing_get_metabox_by_ID('click_performed', $adID);
				$budget = listing_get_metabox_by_ID('budget', $adID);
				$budgetwithtax = listing_get_metabox_by_ID('budgetwithtax', $adID);
				if ($budgetwithtax == '' || $budgetwithtax == 0) {
					$budgetwithtax = $budget;
				}
				$remaining_balance = listing_get_metabox_by_ID('remaining_balance', $adID);
				$active_packages = listing_get_metabox_by_ID('ad_type', $adID);
				$duration = listing_get_metabox_by_ID('duration', $adID);
				if (empty($clicks)) {
					$clicks = esc_html__('No', 'listingpro');
				}
				if (!empty($caID)) {
					if (get_post_status($caID)) {
						// do stuff
						$thisInvAtts = '';
						if (!empty($clicks)) {
							$thisInvAtts .= "data-clicks=\"$clicks\"";
						}
						if (!empty($ads_mode)) {
							$thisInvAtts .= " data-mode= \"$ads_mode\"";
						}
						if (!empty($budget)) {
							if ($ads_mode == 'perclick') {
								if ($currency_position == 'right') {
									$budgetehtml = "<p>" . esc_html__('TOTAL BUDGET', 'listingpro') . "</p><h4>" . $budget . " " . $dbcurrency . "</h4>";
									$taxPricehtml = "<p>" . esc_html__('TAX PRICE', 'listingpro') . "</p><h4>" . $taxPrice . " " . $dbcurrency . "</h4>";
									$budgetewithTaxhtml = "<p>" . esc_html__('TOTAL PAID AMOUNT', 'listingpro') . "</p><h4>" . $budgetwithtax . " " . $dbcurrency . "</h4>";
								} else {
									$budgetehtml = "<p>" . esc_html__('TOTAL BUDGET', 'listingpro') . "</p><h4>" . $dbcurrency . " " . $budget . "</h4>";
									$taxPricehtml = "<p>" . esc_html__('TAX PRICE', 'listingpro') . "</p><h4>" . $dbcurrency . " " . $taxPrice . "</h4>";
									$budgetewithTaxhtml = "<p>" . esc_html__('TOTAL PAID AMOUNT', 'listingpro') . "</p><h4>" . $dbcurrency . " " . $budgetwithtax . "</h4>";
								}

								$thisInvAtts .= " data-taxPrice= \"$taxPricehtml\"";
								$thisInvAtts .= " data-budgetwithTax= \"$budgetewithTaxhtml\"";
							} else {
								if ($currency_position == 'right') {
									$budgetehtml = "<p>" . esc_html__('Amount paid', 'listingpro') . "</p><h4>" . $budget . " " . $dbcurrency . "</h4>";
								} else {
									$budgetehtml = "<p>" . esc_html__('Amount paid', 'listingpro') . "</p><h4>" . $dbcurrency . " " . $budget . "</h4>";
								}
							}
							$thisInvAtts .= " data-budget= \"$budgetehtml\"";
						}
						if (!empty($remaining_balance)) {
							if ($currency_position == 'right') {
								$remaining_balancehtml =  "<p>" . esc_html__('REMAINING BALANCE', 'listingpro') . "</p><h4 class='faccredit'>" . $remaining_balance . " " . $dbcurrency . "</h4>";
							} else {
								$remaining_balancehtml =  "<p>" . esc_html__('REMAINING BALANCE', 'listingpro') . "</p><h4 class='faccredit'>" . $dbcurrency . " " . $remaining_balance . "</h4>";
							}
							$thisInvAtts .= " data-credit= \"$remaining_balancehtml \"";
						} else {
							if ($currency_position == 'right') {
								$remaining_balancehtml =  "<p>" . esc_html__('REMAINING BALANCE', 'listingpro') . "</p><h4 class='faccredit'>" . $remaining_balance . " " . $dbcurrency . "</h4>";
							} else {
								$remaining_balancehtml =  "<p>" . esc_html__('REMAINING BALANCE', 'listingpro') . "</p><h4 class='faccredit'>" . $dbcurrency . " " . $remaining_balance . "</h4>";
							}
							$thisInvAtts .= " data-credit= \"$remaining_balancehtml \"";
						}
						$durationHTML = '';
						if (!empty($duration)) {
							$durationHTML = '<p>' . esc_html__('Duration', 'listingpro') . '</p><span>' . $duration . ' ' . esc_html__('Days', 'listingpro') . '</span>';
							$thisInvAtts .= " data-duration= \"$durationHTML\"";
						}
						$thisInvAtts .= " data-currency= \"$dbcurrency\"";

						$thisInvAtts .= " data-transid= \"$dbtID\"";

						$thisInvAtts .= " data-method= \"$dbmethod\"";
						if (!empty($active_packages)) {
							$typetitle = '';
							$hasPackage = false;
							foreach ($active_packages as $key => $singlePackage) {
								if ($singlePackage == "lp_random_ads") {
									$typetitle = "<span>" . esc_html__("Spotlight", "listingpro") . "<i class='fa fa-exclamation-circle' aria-hidden='true'></i></span>";
									$typetitle = $typetitle . "<i class='fa fa-check-circle'></i>";
									$typetitle = "<li>" . $typetitle . "</li>";
								} elseif ($singlePackage == "lp_detail_page_ads") {
									$typetitle = "<span>" . esc_html__("Sidebar", "listingpro") . "<i class='fa fa-exclamation-circle' aria-hidden='true'></i></span>";
									$typetitle = $typetitle . "<i class='fa fa-check-circle'></i>";
									$typetitle = "<li>" . $typetitle . "</li>";
								} elseif ($singlePackage == "lp_top_in_search_page_ads") {
									$typetitle = "<span>" . esc_html__("Top of Search", "listingpro") . "<i class='fa fa-exclamation-circle' aria-hidden='true'></i></span>";
									$typetitle = $typetitle . "<i class='fa fa-check-circle'></i>";
									$typetitle = "<li>" . $typetitle . "</li>";
								}
								$thisInvAtts .= "data-packeg$key=\"$typetitle\"";
							}
						}

						if ($ncount == 1 && !empty($checked)) {
							$checkedButton = 'checked = "checked"';
						}
			?>
						<div <?php echo wp_kses_post($thisInvAtts); ?> class="lp-listing-outer-container lpadspreview clearfix <?php echo esc_attr($listingTID); ?>">
							<div class="col-md-3 lp-content-before-after" data-content="<?php esc_html_e('CAMPAIGN', 'listingpro'); ?>">
								<div class="lp-invoice-number lp-listing-form lpcampname">
									<label>
										<p>
											<?php echo substr($campTitle, 0, 20); ?>

										</p>

										<div class="radio radio-danger">
											<input class="radio_checked" type="radio" name="ads_invc" id="" value="<?php echo esc_attr($adID); ?>" <?php echo wp_kses_post($checkedButton); ?>>
											<label for="">
											</label>
										</div>

									</label>
								</div>
							</div>

							<div class="col-md-2 lp-content-before-after">
								<p>
									<a class="lp-inovice-campgnlisting" target="_blank" href="<?php echo get_the_permalink($listingTID); ?>" title="<?php echo esc_attr($listingTtitle); ?>">
										<?php echo substr($listingTtitle, 0, 20); ?>
									</a>
								</p>

							</div>

							<div class="col-md-2 lp-content-before-after" data-content="<?php esc_html_e('Start Date', 'listingpro'); ?>">
								<div class="lp-invoice-date">
									<p>
										<?php echo get_the_time(get_option('date_format'), $adID); ?>
									</p>
								</div>
							</div>
							<div class="col-md-2 lp-content-before-after" data-content="<?php esc_html_e('End Date', 'listingpro'); ?>">
								<div class="lp-invoice-date">
									<p>
										<?php echo wp_kses_post($camplanExpire); ?>
									</p>
								</div>
							</div>
							<div class="col-md-3 text-right lp-content-before-after cmpln-sts-column" data-content="<?php esc_html_e('Status', 'listingpro'); ?>">
								<?php
								$activeBTNCls = 'lp-plan-btn-statuscmpln';
								$adStatus = get_post_status($adID);
								$btnStatus = esc_html__('ACTIVE', 'listingpro');
								if ($adStatus == 'publish') {
									$activeBTNCls .= ' active';
								}
								if ($adStatus == 'pending') {
									$btnStatus = esc_html__('PENDING', 'listingpro');
								} elseif ($adStatus == 'inactive') {
									$btnStatus = esc_html__('INACTIVE', 'listingpro');
								}


								?>
								<div class="clerarfix <?php echo esc_attr($activeBTNCls); ?>">
									<span class="lp-inovice-campgnlisting">
										<?php echo wp_kses_post($btnStatus); ?>
									</span>
								</div>
							</div>
						</div>
					<?php
					}
				}
				$ncount++;
			}

			return  ob_get_contents();
			ob_end_clean();
			ob_flush();
		}
	}

	/* ============== listingpro decide referere============= */

	if (!function_exists('listingpro_get_listing_referer')) {
		function listingpro_get_listing_referer($pageURL, $adID)
		{
			if (!empty($pageURL)) {
				$listing_cat_slug = lp_theme_option('listing_cat_slug');
				$listing_loc_slug = lp_theme_option('listing_loc_slug');
				$listing_features_slug = lp_theme_option('listing_features_slug');
				$site_url = site_url();
				$pageURL = rtrim($pageURL, "/");
				global $post;
				$currentListing = $post->ID;
				$type = null;
				$active_packages = listing_get_metabox_by_ID('ad_type', $adID);
				if ((strpos($pageURL, 'lp_s_tag') !== false && strpos($pageURL, 'lp_s_cat') !== false && strpos($pageURL, 'post_type=listing') !== false) && in_array('lp_top_in_search_page_ads', $active_packages)) {
					//Search clicked
					$type = 'lp_top_in_search_page_ads_pc';
				} elseif ((strpos($pageURL, $listing_cat_slug) !== false || strpos($pageURL, $listing_loc_slug) || strpos($pageURL, $listing_features_slug)) && in_array('lp_top_in_search_page_ads', $active_packages)) {
					//Archive Clicked
					$type = 'lp_top_in_search_page_ads_pc';
				} elseif ($pageURL == $site_url && in_array('lp_random_ads', $active_packages)) {
					//home page-spotlight
					$type = 'lp_random_ads_pc';
				} else {
					$post_slug = basename($pageURL);
					$postListing = get_page_by_path($post_slug, OBJECT, 'listing');
					if (!empty($postListing)) {
						$listingID = $postListing->ID;
						if (!empty($listingID)) {
							if (get_post_type($listingID) == 'listing' && in_array('lp_detail_page_ads', $active_packages)) {
								//by sidebar
								$type = 'lp_detail_page_ads_pc';
							}
						}
					}
				}
				if (!empty($type)) {
					lp_count_clicks_for_campaigns($currentListing, $adID, $type);
				}
			}
		}
	}

	/* ============function to delete paypal recuring profile============= */
	if (!function_exists('lp_cancel_recurring_profile')) {
		function lp_cancel_recurring_profile($profile_id)
		{

			global $listingpro_options;
			$paypal_api_environment = $listingpro_options['paypal_api'];
			$paypal_success = $listingpro_options['payment_success'];
			$paypal_success = get_permalink($paypal_success);
			$paypal_fail = $listingpro_options['payment_fail'];
			$paypal_fail = get_permalink($paypal_fail);
			$paypal_api_username = $listingpro_options['paypal_api_username'];
			$paypal_api_password = $listingpro_options['paypal_api_password'];
			$paypal_api_signature = $listingpro_options['paypal_api_signature'];



			$lp_curl_init_var = 'curl';
			$lp_curl_init_var .= '_init';
			$curl = $lp_curl_init_var();

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_VERBOSE, 1);
			if ($paypal_api_environment == 'sandbox') {
				curl_setopt($curl, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
			} elseif ($paypal_api_environment == 'live') {
				curl_setopt($curl, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');
			}
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
				'USER' => urlencode($paypal_api_username),
				'PWD' => urlencode($paypal_api_password),
				'SIGNATURE' => urlencode($paypal_api_signature),

				'VERSION' => '72.0',
				'METHOD' => 'ManageRecurringPaymentsProfileStatus',
				'PROFILEID' => $profile_id,         //here add your profile id
				'ACTION'    => 'Cancel'
			)));

			$lp_curl_exec_var = curl_exec($curl);
			$response =    $lp_curl_exec_var($curl);

			curl_close($curl);
		}
	}

	/* ============function to retreive paypal recuring profile============= */
	if (!function_exists('lp_retreive_recurring_profile')) {
		function lp_retreive_recurring_profile($profile_id)
		{

			global $listingpro_options;
			$paypal_api_environment = $listingpro_options['paypal_api'];
			$paypal_success = $listingpro_options['payment_success'];
			$paypal_success = get_permalink($paypal_success);
			$paypal_fail = $listingpro_options['payment_fail'];
			$paypal_fail = get_permalink($paypal_fail);
			$paypal_api_username = $listingpro_options['paypal_api_username'];
			$paypal_api_password = $listingpro_options['paypal_api_password'];
			$paypal_api_signature = $listingpro_options['paypal_api_signature'];

			$lp_curl_init_var = 'curl';
			$lp_curl_init_var .= '_init';
			$curl = $lp_curl_init_var();

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_POST, true);
			if ($paypal_api_environment == 'sandbox') {
				curl_setopt($curl, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
			} elseif ($paypal_api_environment == 'live') {
				curl_setopt($curl, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');
			}
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array(
				'USER' => urlencode($paypal_api_username),
				'PWD' => urlencode($paypal_api_password),
				'SIGNATURE' => urlencode($paypal_api_signature),

				'VERSION' => '72.0',
				'METHOD' => 'GetRecurringPaymentsProfileDetails',
				'PROFILEID' => $profile_id,         //here add your profile id                      
			)));

			$lp_curl_exec_var = 'curl';
			$lp_curl_exec_var .= '_exec';

			$response =    $lp_curl_exec_var($curl);

			curl_close($curl);

			$nvp = array();

			if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
				foreach ($matches['name'] as $offset => $name) {
					$nvp[$name] = urldecode($matches['value'][$offset]);
				}
			}
			//return $nvp;
			if ($nvp['ACK'] == "Success" && $nvp['STATUS'] == "Active") {
				return $nvp;
			} else {
				return false;
			}
		}
	}

	/* action for business users based google analaytics */
	if (!function_exists('lp_user_based_analytics')) {
		function lp_user_based_analytics()
		{
			if (is_singular('listing')) {
				global $post;
				$listing_ID = $post->ID;
				$listing_Auth_ID = get_post_field('post_author', $listing_ID);
				$g_AnalyticsID = get_user_meta($listing_Auth_ID, 'g_analytics_id', true);
				if (!empty($g_AnalyticsID)) {
					?>

					<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo wp_kses_post($g_AnalyticsID); ?>"></script>
					<script>
						window.dataLayer = window.dataLayer || [];

						function gtag() {
							dataLayer.push(arguments);
						}
						gtag('js', new Date());

						gtag('config', '<?php echo wp_kses_post($g_AnalyticsID); ?>');
					</script>
		<?php
				}
			}
		}
	}
	add_action('wp_head', 'lp_user_based_analytics');

	/* get inbox status */
	if (!function_exists('lp_get_inbox_msgs_status')) {
		function lp_get_inbox_msgs_status()
		{
			$currentUserID = get_current_user_id();
			$lpAllMessges = get_user_meta($currentUserID, 'lead_messages', true);
			if (!empty($lpAllMessges)) {
				foreach ($lpAllMessges as $key => $singleListingArray) {
					if (!empty($singleListingArray)) {
						foreach ($singleListingArray as $emailkey => $singleUserLeads) {
							$status = $singleUserLeads['status'];
							if ($status == "unread") {
								return true;
								break;
							}
						}
					}
				}
			}
			return false;
		}
	}

	if (!function_exists('LP_send_mail')) {
		function LP_send_mail($to, $subject, $message, $headers)
		{
			if (!function_exists('LP_mail')) {
				return '';
			} else {
				return LP_mail($to, $subject, $message, $headers);
			}
		}
	}

	if (!function_exists('lp_mail_headers_append')) {
		function lp_mail_headers_append()
		{
			if (!function_exists('LP_mail_header_headers_append_filter')) {
				return '';
			} else {
				return LP_mail_header_headers_append_filter();
			}
		}
	}
	if (!function_exists('lp_mail_headers_remove')) {
		function lp_mail_headers_remove()
		{
			if (!function_exists('LP_mail_header_headers_rf')) {
				return '';
			} else {
				return LP_mail_header_headers_rf();
			}
		}
	}

	add_action('admin_init', function () {
		if (did_action('elementor/loaded')) {
			remove_action('admin_init', [Plugin::$instance->admin, 'maybe_redirect_to_getting_started']);
		}
	}, 1);


	add_action('wp_ajax_send_author_mail', 'send_author_mail');
	add_action('wp_ajax_nopriv_send_author_mail', 'send_author_mail');
	if (!function_exists('send_author_mail')) {
		function send_author_mail()
		{
			check_ajax_referer('lp_ajax_nonce', 'lpNonce');
			// Nonce is checked, get the POST data and sign user on
			if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
				echo 'Please Check Nonce';
				exit();
			}
			global $listingpro_options;
			$field_name = sanitize_text_field($_POST['field-name']);
			$field_email = sanitize_text_field($_POST['field-email']);
			$field_phone = sanitize_text_field($_POST['field-phone']);
			$field_message = sanitize_text_field($_POST['field-message']);
			$data_userMail = sanitize_text_field($_POST['data-userMail']);
			$headers = "Content-Type: text/html; charset=UTF-8";
			$website_url = site_url();
			$website_name = get_option('blogname');
			$body =  $listingpro_options['listingpro_content_lead_form'];
			$subject =  $listingpro_options['listingpro_subject_lead_form'];

			$formated_mail_content = lp_sprintf2("$body", array(
				'listing_title' => '',
				'sender_name' => "$field_name",
				'sender_email' => "$field_email",
				'sender_phone' => "$field_phone",
				'sender_message' => "$field_message",
				'user_name' => "$field_name",
				'website_url' => "$website_url",
				'website_name' => "$website_name",
			));
			lp_mail_headers_append();
			$result = LP_send_mail($data_userMail, $subject, $formated_mail_content, $headers);
			lp_mail_headers_remove();

			if ($result) {
				echo 'Success';
			} else {
				echo 'fail';
			}

			exit();
		}
	}

	if (!function_exists('LP_current_URL')) {
		function LP_current_URL()
		{
			if (!function_exists('LP_current_location')) {
				return '';
			} else {
				return LP_current_location();
			}
		}
	}
	if (!function_exists('LP_if_localhost')) {
		function LP_if_localhost()
		{
			if (!function_exists('LP_current_location')) {
				return '';
			} else {
				return LP_get_if_localhost();
			}
		}
	}

	if (!function_exists('lp_rating_number_format_cb')) {
		add_filter('lp_rating_number_format', 'lp_rating_number_format_cb');
		function lp_rating_number_format_cb($rating = '')
		{
			if (isset($rating) && $rating != '' && is_numeric($rating)) {
				$rating = number_format($rating, '1', '.', '');
			}
			return $rating;
		}
	}

	if (!function_exists('lp_wpml_compatible_search_url')) {
		add_filter('wpml_ls_language_url', 'lp_wpml_compatible_search_url', 11, 2);
		function lp_wpml_compatible_search_url($url, $data)
		{
			return add_query_arg($_GET, $url);
		}
	}
	function addCustomImportButton()
	{
		global $current_screen;

		// Not our post type, exit earlier
		// You can remove this if condition if you don't have any specific post type to restrict to. 
		if ('listing' != $current_screen->post_type) {
			return;
		}

		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				jQuery(".wp-heading-inline").after("<a  id='lp_export_listings' class='add-new-h2' onclick='downloadCSV()'>Export</a>");
			});

			function downloadCSV() {
				jQuery.ajax({
					url: "<?php echo admin_url('admin-ajax.php') ?>",
					type: "POST",
					data: {
						action: "download_csv",
						nonce: "<?php echo wp_create_nonce('download_csv_nonce') ?>"
					},
					success: function(response) {
						var blob = new Blob([response], {
							type: 'text/csv;charset=utf-8;delimiter=;'
						});
						var link = document.createElement("a");
						link.href = window.URL.createObjectURL(blob);
						link.download = "listings.csv";
						link.click();
					}
				});
			}
		</script>
	<?php
	}
	add_action('admin_head-edit.php', 'addCustomImportButton');

	add_action('wp_ajax_download_csv', 'download_csv');

	function download_csv()
	{

		// check nonce
		if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'download_csv_nonce')) {
			die('Unauthorized access');
		}
		// prepare query arguments
		$args = array(
			'post_type'      => 'listing',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			// add any additional query arguments as needed
		);
		// create WP_Query object
		$query = new WP_Query($args);
		// prepare data for CSV file
		$data = array(
			array('Title', 'Author', 'Date', 'Status', 'Featured Image', 'Tagline Text', 'Google Address', 'latitude', 'longitude', 'phone', 'whatsapp', 'email', 'website', 'twitter', 'facebook', 'linkedin', 'youtube', 'instagram', 'video', 'Gallery', 'Price Status', 'Price From', 'Price To', 'Plan ID', 'FAQ Questions', 'FAQ Answers', 'Business Hours', 'Business Logo', 'Categories', 'Locations', 'Tags'),
		);
		// loop through query results and add to data array
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$title = get_the_title();
				$author = get_the_author();
				$date = get_the_date('Y-m-d');
				$status = get_post_status();
				$featured_image = get_the_post_thumbnail_url();
				$data_iteration = array($title, $author, $date, $status, $featured_image);
				$categories = get_the_terms(get_the_ID(), 'listing-category');
				$categories_list = 'N/A';
				if ($categories && !is_wp_error($categories)) {
					$categories_list = implode(', ', wp_list_pluck($categories, 'name'));
				}
				$locations = get_the_terms(get_the_ID(), 'location');
				$locations_list = 'N/A';
				if ($locations && !is_wp_error($locations)) {
					$locations_list = implode(', ', wp_list_pluck($locations, 'name'));
				}
				$tags = get_the_terms(get_the_ID(), 'list-tags');
				$tags_list = 'N/A';
				if ($tags && !is_wp_error($tags)) {
					$tags_list = implode(', ', wp_list_pluck($tags, 'name'));
				}
				$keys = array(
					'tagline_text',
					'gAddress',
					'latitude',
					'longitude',
					'mappin',
					'phone',
					'whatsapp',
					'email',
					'website',
					'twitter',
					'facebook',
					'linkedin',
					'youtube',
					'instagram',
					'video',
					'gallery',
					'price_status',
					'list_price',
					'list_price_to',
					'Plan_id',
					'lp_purchase_days',
					'reviews_ids',
					'claimed_section',
					'listings_ads_purchase_date',
					'listings_ads_purchase_packages',
					'faqs',
					'business_hours',
					'campaign_id',
					'changed_planid',
					'listing_reported_by',
					'listing_reported',
					'business_logo'
				);
				$metaboxes = get_post_meta(get_the_ID(), 'lp_' . strtolower(THEMENAME) . '_options', true);
				if (empty($metaboxes)) {
					$metaboxes = array_fill_keys($keys, '');
				}

				$mappedArray = array_replace(array_combine($keys, array_fill(0, count($keys), null)), $metaboxes);

				foreach ($mappedArray as $name => $metabox) {
					if ($name != 'mappin' && $name != 'campaign_id' && $name != 'faqs' && $name != 'business_hours' && $name != 'changed_planid' && $name != 'listing_reported_by' && $name != 'listing_reported' && $name != 'gallery' && $name != 'gallery' && $name != 'reviews_ids' && $name != 'lp_purchase_days' && $name != 'claimed_section' && $name != 'listings_ads_purchase_date' && $name != 'listings_ads_purchase_packages') {
						if (empty($metabox)) {
							$metabox = 'N/A';
						}
						array_push($data_iteration, $metabox);
					}
					if ($name == 'gallery') {
						$IDs = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
						if (!empty($IDs)) {
							$imgIDs = array();
							$ximgIDs = explode(',', $IDs);
							if (!empty($ximgIDs)) {
								foreach ($ximgIDs as $value) {
									if (!empty(get_post_type($value)) && get_post_type($value) == 'attachment') {
										$imgIDs[] = wp_get_attachment_image_src($value, 'full')[0];
									}
								}
							}
							$metabox = implode(',', $imgIDs);
						} else {
							$metabox = 'N/A';
						}
						array_push($data_iteration, $metabox);
					}
					if ($name == 'faqs') {
						if (empty($metabox)) {
							array_push($data_iteration, 'N/A');
							array_push($data_iteration, 'N/A');
						} else {
							$faq = implode(',', $metabox['faq']);
							$faqans = implode(',', $metabox['faqans']);
							array_push($data_iteration, $faq);
							array_push($data_iteration, $faqans);
						}
					}
					if ($name == 'business_hours') {
						foreach ($metabox as $day => $hours) {
							$timing = implode(',',  $hours);
							$metabox[$day] = $day . ',' . $timing;
						}
						array_push($data_iteration, implode('|', $metabox));
					}
				}
				array_push($data_iteration, $categories_list);
				array_push($data_iteration, $locations_list);
				array_push($data_iteration, $tags_list);
				$data[] = $data_iteration;
			}
			wp_reset_postdata();
		}
		attendees_to_csv_download($data, "attendees.csv");
		wp_die();
	}

	//Search Shortcode
	function lp_search_shortcode_cb($atts)
	{
		ob_start();
		$output = '';
		echo '<div class="lp-shortcode-search">';
		get_template_part('templates/search/home-search');
		echo '</div>';
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	add_shortcode('lp_search', 'lp_search_shortcode_cb');

	function hasFontAwesomeIconClass($string)
	{
		$pattern = '/\b(fa(-[a-z]+)?)\b/';
		return preg_match($pattern, $string) === 1;
	}

	function restrict_upload_mimes($mimes)
	{
		$mimes = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'png' => 'image/png',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'tiff|tif' => 'image/tiff',
			'ico' => 'image/x-icon',
			'mp4' => 'video/mp4',
			'webp' => 'image/webp',
		);
		return $mimes;
	}
	add_filter('upload_mimes', 'restrict_upload_mimes');

	function check_image_upload($file)
	{
		// Check if the current user is an administrator
		if (current_user_can('administrator')) {
			return $file; // Skip the check for administrators
		}

		$file_type = wp_check_filetype($file['name']);
		$allowed_mimes = array('image/webp', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/x-icon');

		if (!in_array($file_type['type'], $allowed_mimes)) {
			$file['error'] = 'You can only upload image files.';
		}

		return $file;
	}

	add_filter('wp_handle_upload_prefilter', 'check_image_upload');
