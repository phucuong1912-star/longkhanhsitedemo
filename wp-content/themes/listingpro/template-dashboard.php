<?php

/**
 * Template name: Dashboard
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */
?>
<?php
global $listingpro_options, $post;
$listing_mobile_view = $listingpro_options['single_listing_mobile_view'];
$unreadMsgs          = lp_get_inbox_msgs_status();

$userID = '';
if (is_user_logged_in()) {

	$current_user = wp_get_current_user();
	$userID       = $current_user->ID;
} else {
	wp_redirect(home_url());
	exit;
}

/* by zaheer on 21 march */
$published_listings = '';
$pending_listings   = '';
$expired_listings   = '';
$all_listings       = '';
$count_listings     = wp_count_posts('listing', 'readable');
$published_listings = count_user_posts_by_status('listing', 'publish', $userID, false);
$pending_listings   = count_user_posts_by_status('listing', 'pending', $userID, false);
$expired_listings   = count_user_posts_by_status('listing', 'expired', $userID, false);
$all_listings       = $published_listings + $pending_listings + $expired_listings;
/* end by zaheer on 21 march */

$simpleDashboard = false;
if (empty($published_listings) && empty($pending_listings) && empty($expired_listings)) {
	$simpleDashboard = true;
}

$current_user       = wp_get_current_user();
$userID             = $current_user->ID;
$published_listings = count_user_posts_by_status('groups', 'publish', $userID, false);
$pending_listings   = count_user_posts_by_status('groups', 'pending', $userID, false);
$expired_listings   = count_user_posts_by_status('groups', 'expired', $userID, false);
$showEpItems        = 'on';
if (empty($published_listings) && empty($pending_listings) && empty($expired_listings)) {
	$showEpItems = 'off';
}

$updateTab = false;
global $user_id, $listingpro_options;

$current_user      = wp_get_current_user();
$user_id           = $current_user->ID;
$userSubscriptions = false;
$userSubs          = get_user_meta($user_id, 'listingpro_user_sbscr', true);
if (! empty($userSubs)) {
	if (is_array($userSubs) && count($userSubs) > 0) {
		$userSubscriptions = true;
	}
}
$rmessage = '';
$rType    = '';
if (isset($_POST['removeid']) && ! empty($_POST['removeid'])) {
	$rID          = sanitize_text_field($_POST['removeid']);
	$rpost        = get_post($rID);
	$rpost_author = $rpost->post_author;
	if ($user_id == $rpost_author && current_user_can('delete_post', $rID)) {
		wp_delete_post($rID);
		$rmessage = esc_html__('Post has deleted succesfully', "listingpro");
		$rType    = 'success';
	} else {
		$rmessage = esc_html__('You have no permission to delete this post', "listingpro");
		$rType    = 'warning';
	}
}

$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
// User Name
$user_fname = get_the_author_meta('first_name', $user_id);
$user_lname = get_the_author_meta('last_name', $user_id);
// User contact meta
$user_address = get_the_author_meta('address', $user_id);
$user_phone   = get_the_author_meta('phone', $user_id);
$user_email   = get_the_author_meta('user_email', $user_id);
// User Social links
$user_facebook  = get_the_author_meta('facebook', $user_id);
$user_linkedin  = get_the_author_meta('linkedin', $user_id);
$user_instagram = get_the_author_meta('instagram', $user_id);
$user_twitter   = get_the_author_meta('twitter', $user_id);
$user_pinterest = get_the_author_meta('pinterest', $user_id);
// User BIO
$user_desc = get_the_author_meta('description', $user_id);
$user_ID   = $user_id;
if (isset($_POST['profileupdate'])) {
	if (!isset($_POST['profile_update_nonce']) || !wp_verify_nonce($_POST['profile_update_nonce'], 'profile_update_action')) {
		$message = esc_html__("Nonce verification failed. Please try again.", "listingpro");
		$mType = 'error';
	}
}

$contactSupport = $listingpro_options['contact_support'];

/* by zaheer on 28 march */
$published_campaings = lp_count_user_campaigns($userID);
/* end by zaheer on 28 march */
?>
<?php get_header(); ?>
<input type="hidden" id="datepicker-lang" value="<?php echo get_option('WPLANG'); ?>">
<!--==================================Section Open=================================-->
<?php
if (class_exists('ListingproPlugin')) {
	$current_user   = wp_get_current_user();
	$u_display_name = $current_user->display_name;
	if (empty($u_display_name)) {
		$u_display_name = $current_user->nickname;
	}
?>
	<div class="modal fade lp-modal-content-image-outer" id="imagemodal" tabindex="-1" role="dialog">
		<div class="modal-dialog  lp-modal-content-image">
			<div class="modal-content">
				<div class="modal-body">
					<span data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></span>
					<img alt="image" src="" id="imagepreview" class="imagepreview">
				</div>
			</div>
		</div>
	</div>
	<section class="aliceblue">

		<?php
		$dashPage      = '';
		$opendClass    = '';
		$openedClasss  = '';
		$activeListing = '';
		$activePending = '';
		$activeExpired = '';
		if (isset($_GET['dashboard'])) {
			$dashPage = esc_html($_GET['dashboard']);
		}

		if (! empty($dashPage) && ($dashPage == "listing" || $dashPage == "pending" || $dashPage == "expired")) {
			$opendClass = 'opened';

			if ($dashPage == "listing") {
				$activeListing = 'class="active"';
			} else if ($dashPage == "pending") {
				$activePending = 'class="active"';
			} else if ($dashPage == "expired") {
				$activeExpired = 'class="active"';
			}
		}

		$lft_panel            = '';
		$openedClass          = '';
		$camp_Listing         = '';
		$activeCampListing    = '';
		$invcListing          = '';
		$activeinvoiceListing = '';
		if (isset($_GET['dashboard'])) {
			$lft_panel = esc_html($_GET['dashboard']);
		}
		if (! empty($lft_panel) && ($lft_panel == "campaigns" || $lft_panel == "active-campaigns")) {
			$openedClass = 'opened';

			if ($lft_panel == "campaigns") {
				$camp_Listing = 'class="active"';
			} else if ($lft_panel == "active-campaigns") {
				$activeCampListing = 'class="active"';
			}
		}

		if (! empty($lft_panel) && ($lft_panel == "list-invoices" || $lft_panel == "ads-invoices")) {
			$openedClasss = 'opened';

			if ($lft_panel == "list-invoices") {
				$activeinvoiceListing = 'class="active"';
			} else if ($lft_panel == "ads-invoices") {
				$invcListing = 'class="active"';
			}
		}

		$review_panel        = '';
		$reviewOpenedClass   = '';
		$review_Listing      = '';
		$activeReviewListing = '';
		if (isset($_GET['dashboard'])) {
			$review_panel = esc_html($_GET['dashboard']);
		}
		if (! empty($review_panel) && ($review_panel == "reviews" || $review_panel == "reviews-submited")) {
			$reviewOpenedClass = 'opened';

			if ($review_panel == "reviews") {
				$review_Listing = 'class="active"';
			} else if ($review_panel == "reviews-submited") {
				$activeReviewListing = 'class="active"';
			}
		}

		$creat_menu_openedClass = '';
		$creat_menu_panel       = '';
		$create_menu_a          = '';
		$create_groups_a        = '';
		$create_types_a         = '';
		$create_serv_s_a        = '';
		if (isset($_GET['dashboard'])) {
			$creat_menu_panel = esc_html($_GET['dashboard']);
		}
		if (! empty($creat_menu_panel) && ($creat_menu_panel == 'create-menu' || $creat_menu_panel == 'menu-types' || $creat_menu_panel == 'menu-groups') || $creat_menu_panel == 'services-screen') {
			$creat_menu_openedClass = 'opened';
			if ($creat_menu_panel == 'create-menu') {
				$create_menu_a = 'class="active"';
			} else if ($creat_menu_panel == 'menu-types') {
				$create_types_a = 'class="active"';
			} else if ($creat_menu_panel == 'menu-groups') {
				$create_groups_a = 'class="active"';
			} else if ($creat_menu_panel == 'services-screen') {
				$create_serv_s_a = 'class="active"';
			}
		}

		$activeDashboardMenu    = '';
		$activeAnnouncementMenu = '';
		$activeEventsMenu       = '';

		$activeEarningsMenu = '';
		$activeTicketsMenu  = '';
		$activeGroupMenu    = '';

		$activeBookingsMenu  = '';
		$activeDiscountMenu  = '';
		$activeImgMenu       = '';
		$activeprofileMenu   = '';
		$activesavedMenu     = '';
		$activesavedListing  = '';
		$activesavedinvoices = '';
		$activeadsavedinvoices = '';

		$activepackagesMenu     = '';
		$activecampaignsMenu    = '';
		$activereviewsMenu      = '';
		$activeBookingsMenu     = '';
		$activeMyBookingsMenu   = '';
		$activeEarnings         = '';
		$activeMenuItem         = '';
		$activemenues           = '';
		$activeSubscriptionMenu = '';
		$activeinbox            = '';
		$activeadcompn          = '';
		if (! empty($dashPage)) {
			if ($dashPage == "main-screen") {
				$activeDashboardMenu = 'class="active-dash-menu"';
			} else if ($dashPage == 'announcements') {
				$activeAnnouncementMenu = 'class="active-dash-menu"';
			} else if ($dashPage == 'events') {
				$activeEventsMenu = 'class="active-dash-menu"';
			} else if ($dashPage == 'tickets-earnings') {
				$activeEarningsMenu = 'class="active-dash-menu"';
			} else if ($dashPage == 'my-tickets') {
				$activeTicketsMenu = 'class="active-dash-menu"';
			} else if ($dashPage == 'groups') {
				$activeGroupMenu = 'class="active-dash-menu"';
			} else if ($dashPage == 'manage-booking') {
				$activeBookingsMenu = 'class="active-dash-menu"';
			} else if ($dashPage == 'my-bookings') {
				$activeMyBookingsMenu = 'class="active-dash-menu"';
			} else if ($dashPage == 'earnings') {
				$activeEarnings = 'class="active-dash-menu"';
			} else if ($dashPage == 'discounts') {
				$activeDiscountMenu = 'class="active-dash-menu"';
			} else if ($dashPage == "update-profile") {
				$activeprofileMenu = 'class="active-dash-menu"';
			} else if ($dashPage == "saved") {
				$activesavedMenu = 'class="active-dash-menu"';
			} else if ($dashPage == "packages") {
				$activepackagesMenu = 'class="active-dash-menu"';
			} else if ($dashPage == "campaigns") {
				$activecampaignsMenu = 'class="active-dash-menu"';
			} else if ($dashPage == "services-screen") {
				$activeMenuItem = 'class="active-dash-menu"';
			} else if ($dashPage == "subscription") {
				$activeSubscriptionMenu = 'class="active-dash-menu"';
			} else if ($dashPage == "listings") {
				$activesavedListing = 'class="active-dash-menu"';
			} else if ($dashPage == "invoices") {
				$activesavedinvoices = 'class="active-dash-menu"';
			} else if ($dashPage == "campaign-invoices") {
				$activeadsavedinvoices = 'class="active-dash-menu"';
			} else if ($dashPage == "menus") {
				$activemenues = 'class="active-dash-menu"';
			} else if ($dashPage == 'services-screen') {
				$activeImgMenu = 'class="active-dash-menu"';
			} else if ($dashPage == "inbox") {
				$activeinbox = 'class="active-dash-menu"';
			} else if ($dashPage == "active-campaigns") {
				$activeadcompn = 'class="active-dash-menu"';
			}
		}


		$currentURL = '';
		$perma      = '';
		$dashQuery  = 'dashboard=';
		$currentURL = get_permalink();
		global $wp_rewrite;
		if ($wp_rewrite->permalink_structure == '') {
			$perma = "&";
		} else {
			$perma = "?";
		}

		$dashboard_usr_show      = true;
		$my_listings_show        = true;
		$saved_listing_show      = true;
		$invoices_dashboard_show = true;
		global $wpdb;
		$dbprefix = '';
		$post_ids = '';
		$dbprefix = $wpdb->prefix;
		$user_ID  = '';
		$user_ID  = get_current_user_id();
		$results  = '';
		$resultss = '';

		$listing_orders_table = $dbprefix . 'listing_orders';
		if (lp_table_exists_check($listing_orders_table)) {
			$results  = $wpdb->get_results("SELECT * FROM {$dbprefix}listing_orders WHERE user_id ='$user_ID' AND plan_type ='Package' AND status='success'");
			$resultss = $wpdb->get_results("SELECT * FROM {$dbprefix}listing_orders WHERE user_id ='$user_ID' AND plan_type ='Package' AND status='expired'");
		}

		if (empty($results) && ! is_array($resultss)) {
			$dashboard_packages_show = false;
		} else {
			$dashboard_packages_show = true;
		}
		$ad_compaigns_show      = true;
		$review_dashoard_show   = true;
		$bookings_show          = true;
		$booking_dashoard_show  = true;
		$menu_dashoard_show     = true;
		$img_menu_dashoard_show = true;
		$my_profile_show        = true;
		$log_out_show           = true;
		$announcements_show     = true;
		$events_show            = true;
		$discounts_show         = true;
		$lead_form_show         = false;
		global $listingpro_customizer_options;
		$lead_form_user_dashboard = get_option('lead_form_user_dashboard');
		if ($lead_form_user_dashboard == 1) {
			$lead_form_show = true;
		}
		$dashboard_usr = $listingpro_options['dashboard_usr'];
		if ($dashboard_usr == 0) {
			$dashboard_usr_show = false;
		}
		if (isset($listingpro_options['announcements_dashoard']) && $listingpro_options['announcements_dashoard'] == 0) {
			$announcements_show = false;
		}
		if (isset($listingpro_options['events_dashoard']) && $listingpro_options['events_dashoard'] == 0) {
			$events_show = false;
		}
		if (isset($listingpro_options['discounts_dashoard']) && $listingpro_options['discounts_dashoard'] == 0) {
			$discounts_show = false;
		}
		$my_listings = $listingpro_options['my_listings'];
		if ($my_listings == 0) {
			$my_listings_show = false;
		}
		$saved_listing = $listingpro_options['saved_listing'];
		if ($saved_listing == 0) {
			$saved_listing_show = false;
		}
		$invoices_dashboard = $listingpro_options['invoices_dashboard'];
		if ($invoices_dashboard == 0) {
			$invoices_dashboard_show = false;
		}
		$dashboard_packages = $listingpro_options['dashboard_packages'];
		if ($dashboard_packages == 0) {
			$dashboard_packages_show = false;
		}
		$ad_compaigns = $listingpro_options['ad_compaigns'];
		if ($ad_compaigns == 0) {
			$ad_compaigns_show = false;
		}
		$review_dashoard = $listingpro_options['review_dashoard'];
		if ($review_dashoard == 0) {
			$review_dashoard_show = false;
		}

		$booking_dashoard = $listingpro_options['booking_dashoard'];
		if ($booking_dashoard == 0) {
			$booking_dashoard_show = false;
		}

		$earnings = false;
		if (isset($listingpro_options['earnings_dashoard'])) {
			$earnings    = true;
			$earningsTAB = $listingpro_options['earnings_dashoard'];
			if ($earningsTAB == 0) {
				$earnings = false;
			}
		}

		$menu_dashoard = $listingpro_options['menu_dashoard'];
		if ($menu_dashoard == 0) {
			$menu_dashoard_show = false;
		}

		$my_profile = $listingpro_options['my_profile'];
		if ($my_profile == 0) {
			$my_profile_show = false;
		}
		$log_outt = $listingpro_options['log_outt'];

		if ($log_outt == 0) {
			$log_out_show = false;
		}

		?>
		<div class="clearfix"></div>
		<div id="wrapper" class="active lp-dashboard-new clearfix lp-dashboard-new-active">


			<div id="sidebar-wrapper">
				<div id="sidebar_menu" class="sidebar-nav">
					<div class="sidebar-brand clearfix">
						<a href="<?php echo esc_url(home_url('/')); ?>">
							<?php
							if (is_front_page()) {
								listingpro_primary_logo();
							} else {
								listingpro_secondary_logo();
							}
							?>
						</a>

						<a id="menu-toggle" href="#"><span id="main_icon"
								class="glyphicon glyphicon-align-justify"></span>
						</a>

					</div>


				</div>
				<ul class="sidebar-nav clearfix" id="sidebar">
					<?php if ($simpleDashboard == false) { ?>

						<?php if ($dashboard_usr_show == true) { ?>
							<li>
								<a <?php echo $activeDashboardMenu; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'main-screen'; ?>"><span
										class="sub_icon sub_iconfirst"> <i
											class="fa fa-dashboard"></i> </span><?php esc_html_e(' Dashboard', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Dashboard', 'listingpro'); ?>"><i
											class="fa fa-dashboard"></i></span></a>
							</li>
						<?php } ?>
						<?php
						if ($simpleDashboard == false && $dashboard_usr_show == true && $announcements_show == true):
						?>
							<li><a <?php echo $activeAnnouncementMenu; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'announcements'; ?>"><span
										class="sub_icon sub_iconfirst"><i class="fa fa-microphone"
											aria-hidden="true"></i></span> <?php echo esc_html__('Announcements', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Announcements', 'listingpro'); ?>"> <i
											class="fa fa-microphone" aria-hidden="true"></i></span></a></li>
						<?php endif; ?>

						<?php

						if ($simpleDashboard == false && $dashboard_usr_show == true && $events_show == true && ! class_exists('EventPro')):
						?>
							<li><a <?php echo $activeEventsMenu; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'events'; ?>">
									<span class="sub_icon sub_iconfirst"><i class="fa fa-calendar"
											aria-hidden="true"></i></span> <?php echo esc_html__('Events', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Events', 'listingpro'); ?>"> <i
											class="fa fa-calendar" aria-hidden="true"></i></span>


								</a></li>
						<?php endif; ?>
						<?php
						if ($simpleDashboard == false && $dashboard_usr_show == true && $booking_dashoard_show == true && class_exists('MedicalPro')):
						?>
							<li>
								<a <?php echo $activeBookingsMenu; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'manage-booking'; ?>">
									<span class="sub_icon sub_iconfirst"><i class="fa fa-pencil-square-o"
											aria-hidden="true"></i></span> <?php echo esc_html__('Appointments', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Appointments', 'listingpro'); ?>"> <i
											class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
								</a>
							</li>
						<?php endif;
					}
					if ($dashboard_usr_show == true && $booking_dashoard_show == true && class_exists('MedicalPro')):
						?>
						<li>
							<a <?php echo $activeMyBookingsMenu; ?>
								href="<?php echo $currentURL . $perma . $dashQuery . 'my-bookings'; ?>">
								<span class="sub_icon sub_iconfirst"><i class="fa fa-pencil-square-o"
										aria-hidden="true"></i></span> <?php echo esc_html__('My Bookings', 'listingpro'); ?>
								<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
									data-tooltip="<?php echo esc_html__('My Bookings', 'listingpro'); ?>"> <i
										class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
							</a>
						</li>
					<?php endif;

					if ($dashboard_usr_show == true && class_exists('EventPro')):
					?>
						<li>
							<a <?php echo $activeGroupMenu; ?>
								href="<?php echo $currentURL . $perma . $dashQuery . 'groups'; ?>">
								<span class="sub_icon sub_iconfirst"><i class="fa fa-users"
										aria-hidden="true"></i></span> <?php echo esc_html__('Groups', 'listingpro'); ?>
								<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
									data-tooltip="<?php echo esc_html__('Groups', 'listingpro'); ?>"> <i
										class="fa fa-users" aria-hidden="true"></i></span>
							</a>
						</li>
					<?php endif;
					if ($dashboard_usr_show == true && class_exists('EventPro') && $events_show == true):
					?>
						<li>
							<a <?php echo $activeEventsMenu; ?>
								href="<?php echo $currentURL . $perma . $dashQuery . 'events'; ?>">
								<span class="sub_icon sub_iconfirst"><i class="fa fa-calendar"
										aria-hidden="true"></i></span> <?php echo esc_html__('Events', 'listingpro'); ?>
								<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
									data-tooltip="<?php echo esc_html__('Events', 'listingpro'); ?>"> <i
										class="fa fa-calendar" aria-hidden="true"></i></span>
							</a>
						</li>
					<?php endif;
					if ($dashboard_usr_show == true && class_exists('EventPro')):
					?>
						<li>
							<a <?php echo $activeTicketsMenu; ?>
								href="<?php echo $currentURL . $perma . $dashQuery . 'my-tickets'; ?>">
								<span class="sub_icon sub_iconfirst"><i class="fa fa-ticket"
										aria-hidden="true"></i></span> <?php echo esc_html__('My Tickets', 'listingpro'); ?>
								<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
									data-tooltip="<?php echo esc_html__('My Tickets', 'listingpro'); ?>"> <i
										class="fa fa-ticket" aria-hidden="true"></i></span>
							</a>
						</li>
					<?php endif;
					if ($dashboard_usr_show == true && $showEpItems == 'on' && class_exists('EventPro')):
					?>
						<li>
							<a <?php echo $activeEarningsMenu; ?>
								href="<?php echo $currentURL . $perma . $dashQuery . 'tickets-earnings'; ?>">
								<span class="sub_icon sub_iconfirst"><i class="fa fa-money"
										aria-hidden="true"></i></span> <?php echo esc_html__('My Earnings', 'listingpro'); ?>
								<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
									data-tooltip="<?php echo esc_html__('My Earnings', 'listingpro'); ?>"> <i
										class="fa fa-money" aria-hidden="true"></i></span>
							</a>
						</li>
					<?php endif;

					if ($dashboard_usr_show == true && $earnings == true && $simpleDashboard == false && class_exists('MedicalPro')):
					?>
						<li>
							<a <?php echo $activeEarnings; ?>
								href="<?php echo $currentURL . $perma . $dashQuery . 'earnings'; ?>">
								<span class="sub_icon sub_iconfirst"><i class="fa fa-usd"
										aria-hidden="true"></i></span> <?php echo esc_html__('My Earnings', 'listingpro'); ?>
								<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
									data-tooltip="<?php echo esc_html__('My Earnings', 'listingpro'); ?>"> <i
										class="fa fa-usd" aria-hidden="true"></i></span>
							</a>
						</li>
						<?php endif;
					if ($simpleDashboard == false) {
						if ($simpleDashboard == false && $dashboard_usr_show == true && $booking_dashoard_show == true && class_exists('Listingpro_bookings')):
						?>
							<li>
								<a <?php echo $activeBookingsMenu; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'manage-booking'; ?>">
									<span class="sub_icon sub_iconfirst"><i class="fa fa-pencil-square-o"
											aria-hidden="true"></i></span> <?php echo esc_html__('Appointments', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Appointments', 'listingpro'); ?>"> <i
											class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
								</a>
							</li>
						<?php endif; ?>
						<?php
						if ($simpleDashboard == false && $dashboard_usr_show == true && $lead_form_show == true && class_exists('Listingpro_lead_form')):
						?>
							<li><a href="<?php echo $currentURL . $perma . $dashQuery . 'lead_form'; ?>"><span
										class="sub_icon sub_iconfirst"><i class="fa fa-bullhorn"
											aria-hidden="true"></i></span> <?php echo esc_html__('Lead Form', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Lead Form', 'listingpro'); ?>"> <i
											class="fa fa-bullhorn" aria-hidden="true"></i></span></a></li>
						<?php endif; ?>
						<?php
						if ($simpleDashboard == false && $dashboard_usr_show == true && $discounts_show == true):
						?>
							<li><a <?php echo $activeDiscountMenu; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'discounts'; ?>"><span
										class="sub_icon sub_iconfirst"><i class="fa fa-tags" aria-hidden="true"></i></span> <?php echo esc_html__('Coupons', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Coupons', 'listingpro'); ?>"><i
											class="fa fa-tags" aria-hidden="true"></i></span></a></li>
						<?php endif; ?>

					<?php } ?>
					<?php if ($simpleDashboard == false) { ?>
						<?php if ($menu_dashoard_show == true) { ?>
							<li><a <?php echo $activemenues; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'menus'; ?>"><span
										class="sub_icon sub_iconfirst"><i class="fa fa-cutlery"
											aria-hidden="true"></i></span> <?php esc_html_e('Menu', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Menu', 'listingpro'); ?>"><i
											class="fa fa-cutlery" aria-hidden="true"></i></span></a></li>
						<?php } ?>
					<?php } ?>


					<?php if ($simpleDashboard == true) { ?>

						<?php if ($my_profile_show == true) { ?>
							<li>
								<a <?php echo $activeprofileMenu; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'update-profile'; ?>"><span
										class="sub_icon sub_iconfirst"><i
											class="fa fa-user-circle"></i></span> <?php esc_html_e(' My Profile', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('My Profile', 'listingpro'); ?>"><i
											class="fa fa-paper-plane-o" aria-hidden="true"></i></span></a>
							</li>
						<?php } ?>

						<?php if ($saved_listing_show == true) { ?>
							<li><a <?php echo $activesavedMenu; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'saved'; ?>"><span
										class="sub_icon sub_iconfirst"><i class="fa fa-heart"
											aria-hidden="true"></i></span> <?php esc_html_e('Saved', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Saved', 'listingpro'); ?>"><i
											class="fa fa-heart" aria-hidden="true"></i></span></a></li>
						<?php } ?>

						<li><a <?php echo $activeinbox; ?>
								href="<?php echo $currentURL . $perma . $dashQuery . 'inbox'; ?>"><span
									class="sub_icon sub_iconfirst"><i class="fa fa-envelope-o"
										aria-hidden="true"></i></span> <?php esc_html_e('Inbox', 'listingpro'); ?>
								<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
									data-tooltip="<?php echo esc_html__('Inbox', 'listingpro'); ?>"><i
										class="fa fa-envelope-o" aria-hidden="true"></i></span>
								<?php if (! empty($unreadMsgs)) {
								?>
									<span class="unreadMsgDot"></span>
								<?php
								} ?>
							</a>

						</li>

					<?php } ?>
					<?php if ($simpleDashboard == false) { ?>
						<?php if ($my_listings_show == true) { ?>
							<li><a <?php echo $activesavedListing; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'listings'; ?>"><span
										class="sub_icon sub_iconfirst"><i class="fa fa-map-marker"
											aria-hidden="true"></i></span> <?php esc_html_e('Listings', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('listings', 'listingpro'); ?>"><i
											class="fa fa-map-marker" aria-hidden="true"></i></span></a></li>
						<?php } ?>

						<li><a <?php echo $activeinbox; ?>
								href="<?php echo $currentURL . $perma . $dashQuery . 'inbox'; ?>"><span
									class="sub_icon sub_iconfirst"><i class="fa fa-envelope-o"
										aria-hidden="true"></i></span> <?php esc_html_e('Inbox', 'listingpro'); ?>
								<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
									data-tooltip="<?php echo esc_html__('Inbox', 'listingpro'); ?>"><i
										class="fa fa-envelope-o" aria-hidden="true"></i></span>
								<?php if (! empty($unreadMsgs)) {
								?>
									<span class="unreadMsgDot"></span>
								<?php
								} ?>
							</a>


						</li>


						<?php if ($invoices_dashboard_show == true) { ?>
							<li><a <?php echo $activesavedinvoices; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'invoices'; ?>"><span
										class="sub_icon sub_iconfirst"><i class="fa fa-paper-plane-o"
											aria-hidden="true"></i></span> <?php esc_html_e('Invoices', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Invoices', 'listingpro'); ?>"><i
											class="fa fa-paper-plane-o" aria-hidden="true"></i></span></a></li>
							<li><a <?php echo $activeadsavedinvoices; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'campaign-invoices'; ?>"><span
										class="sub_icon sub_iconfirst"><i class="fa fa-paper-plane-o"
											aria-hidden="true"></i></span> <?php esc_html_e('Ads Invoices', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Ads Invoices', 'listingpro'); ?>"><i
											class="fa fa-paper-plane-o" aria-hidden="true"></i></span></a></li>
						<?php } ?>
						<?php if ($saved_listing_show == true) { ?>
							<li><a <?php echo $activesavedMenu; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'saved'; ?>"><span
										class="sub_icon sub_iconfirst"><i class="fa fa-heart"
											aria-hidden="true"></i></span> <?php esc_html_e('Saved', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Saved', 'listingpro'); ?>"><i
											class="fa fa-heart" aria-hidden="true"></i></span></a></li>
						<?php } ?>
						<?php if ($dashboard_packages_show == true) { ?>
							<li><a <?php echo $activepackagesMenu; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'packages'; ?>"><span
										class="sub_icon sub_iconfirst"><i class="fa fa-gift" aria-hidden="true"></i></span> <?php esc_html_e('Packages', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Packages', 'listingpro'); ?>"><i
											class="fa fa-gift" aria-hidden="true"></i></span></a></li>
						<?php } ?>
						<?php if ($userSubscriptions == true) { ?>
							<li>
								<a <?php echo $activeSubscriptionMenu; ?>
									href="<?php echo $currentURL . $perma . $dashQuery . 'subscription'; ?>"><span
										class="sub_icon sub_iconfirst"><i
											class="fa fa-flag"></i></span> <?php esc_html_e(' Subscriptions', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('My Subscriptions', 'listingpro'); ?>"><i
											class="fa fa-flag"></i></span></a>
							</li>
						<?php
						}
						?>
						<?php
						if (class_exists('ListingAds')) {
						?>
							<?php if ($ad_compaigns_show == true) { ?>
								<li class="<?php echo esc_attr($openedClass); ?>">
									<a <?php echo $activeadcompn; ?>
										href="<?php echo $currentURL . $perma . $dashQuery . 'active-campaigns'; ?>"><span
											class="sub_icon sub_iconfirst"><i class="fa fa-bullhorn"
												aria-hidden="true"></i> </span><?php esc_html_e('Ad Campaigns ', 'listingpro'); ?>
										<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
											data-tooltip="<?php echo esc_html__('Ad Campaigns', 'listingpro'); ?>"><i
												class="fa fa-bullhorn" aria-hidden="true"></i></span></a>

								</li>
							<?php } ?>
					<?php
						}
					}
					?>
					<?php
					if (class_exists('ListingReviews')) {
					?>
						<?php if ($review_dashoard_show == true) { ?>
							<li class="<?php echo esc_attr($reviewOpenedClass); ?>">
								<a href="<?php echo $currentURL . $perma . $dashQuery . 'reviews'; ?>"><span
										class="sub_icon sub_iconfirst"><i class="fa fa-star" aria-hidden="true"></i> </span> <?php esc_html_e(' Reviews ', 'listingpro'); ?>
									<span class="sub_icon sub_iconsecond simptip-position-right simptip-movable"
										data-tooltip="<?php echo esc_html__('Reviews', 'listingpro'); ?>"><i
											class="fa fa-star" aria-hidden="true"></i></span></a>
							</li>
						<?php } ?>
					<?php
					}
					?>
					<?php
					if (class_exists('Listingpro_timekit_resurva') && function_exists('listingpro_tr_dash_nav')) {
						listingpro_tr_dash_nav();
					}
					?>
				</ul>

			</div>


			<div id="page-content-wrapper">
				<input type="hidden" id="select2-ajax-noresutls"
					value="<?php echo esc_html_e('No results found', 'listingpro'); ?>">
				<input type="hidden" id="select2-ajax-tooshort"
					value="<?php echo esc_html_e('Please enter 3 or more characters', 'listingpro'); ?>">
				<input type="hidden" id="select2-ajax-searching"
					value="<?php echo esc_html_e('Searching...', 'listingpro'); ?>">
				<div class="page-content inset dashboard-content">
					<div class="lp-user-header clearfix">

						<div class="pull-right lp-section-app-head-area">
							<div class="lp-user-meta">
								<ul class="clearfix">
									<li>
										<p><?php echo $u_display_name; ?></p>
									</li>
									<li>
										<div class="user-meta-image ">
											<a href=""><img src="<?php echo listingpro_author_image(); ?>"
													alt="<?php echo esc_attr__('avatar', 'listingpro'); ?>" /></a>
										</div>

									</li>
									<li>
										<div class="lp-user-header-details lp-dot-extra-buttons">
											<a href="javascript:void();"><i class="fa fa-bars"
													aria-hidden="true"></i></a>
											<ul class="lp-user-menu list-style-none">
												<?php if ($simpleDashboard == false) { ?>
													<?php if ($my_profile_show == true) { ?>
														<li>
															<a href="<?php echo $currentURL . $perma . $dashQuery . 'update-profile'; ?>">
																<i class="fa fa-user-circle"></i> <?php esc_html_e(' My Profile', 'listingpro'); ?>
															</a>
														</li>


													<?php } ?>
												<?php } ?>
												<?php if ($log_out_show == true) { ?>
													<li>
														<a href="<?php echo wp_logout_url(esc_url(home_url('/'))); ?>"><span><i
																	class="fa fa-lock" aria-hidden="true"></i>
																<?php echo esc_html__('Logout', 'listingpro'); ?></span></a>
													</li>
												<?php } ?>
											</ul>
										</div>
									</li>
								</ul>
							</div>
						</div>
						<?php if (! empty($contactSupport)) { ?>
							<div class="lp-contact-support-outer pull-right">
								<a target="_blank" href="<?php echo esc_url($contactSupport); ?>">
									<i class="fa fa-phone" aria-hidden="true"></i>
									<?php esc_html_e(' Contact Support', 'listingpro'); ?>
								</a>
							</div>
						<?php } ?>
						<?php
						$addURL = listingpro_url('add_listing_url_mode');
						if (! empty($addURL)) {
						?>
							<div class="lp-contact-support-outer pull-right">
								<a target="_blank" href="<?php echo esc_url($addURL); ?>">
									<i class="fa fa-plus" aria-hidden="true"></i>
									<?php esc_html_e(' Add Listing', 'listingpro'); ?>
								</a>
							</div>
						<?php } ?>

						<div class="lp-contact-support-outer pull-right dashboard-home-icon-st">
							<a href="<?php echo esc_url(home_url('/')); ?>">
								<i class="fa fa-home" aria-hidden="true"></i>
							</a>
						</div>

					</div>
					<?php $dashboard = isset($_GET['dashboard']) ? sanitize_file_name($_GET['dashboard']) : ''; ?>
					<div class="row">
						<div class="col-md-12 padding-0">
							<div class="right-panel">
								<?php if ($simpleDashboard == false) { ?>
									<?php

									if (isset($_GET['dashboard']) && ! empty($_GET['dashboard'])) {
										if (esc_html($_GET['dashboard']) == 'discounts' || esc_html($_GET['dashboard']) == 'announcements' || esc_html($_GET['dashboard']) == 'offers' || esc_html($_GET['dashboard']) == 'menu-types' || esc_html($_GET['dashboard']) == 'menu-groups' || esc_html($_GET['dashboard']) == 'create-menu') {
											get_template_part('templates/dashboard/' . esc_html($_GET['dashboard']) . '');
										} else if (esc_html($_GET['dashboard']) == 'bookings' || esc_html($_GET['dashboard']) == 'timekit-bookings') {
											$file = WP_CONTENT_DIR . '/plugins/listingpro-timekit-resurva/dashboard/' . esc_html($_GET['dashboard']) . '.php';
											require($file);
										} else if (esc_html($_GET['dashboard']) == 'manage-booking' && class_exists('MedicalPro')) {
											$file = WP_CONTENT_DIR . '/plugins/medicalpro/templates/dashboard/' . esc_html($_GET['dashboard']) . '.php';
											require($file);
										} else if (esc_html($_GET['dashboard']) == 'manage-booking' && class_exists('Listingpro_bookings')) {
											$file = WP_CONTENT_DIR . '/plugins/listingpro-bookings/templates/' . esc_html($_GET['dashboard']) . '.php';
											require($file);
										} else if (esc_html($_GET['dashboard']) == 'lead_form' && class_exists('Listingpro_lead_form')) {
											$file = WP_CONTENT_DIR . '/plugins/listingpro-lead-form/templates/' . esc_html($_GET['dashboard']) . '.php';
											require($file);
										} //EventPro
										else if (esc_html($_GET['dashboard']) == 'events' && class_exists('EventPro')) {
										} else if (esc_html($_GET['dashboard']) == 'saved' && class_exists('EventPro')) {
										} else if (esc_html($_GET['dashboard']) == 'groups' && class_exists('EventPro')) {
										} else if (esc_html($_GET['dashboard']) == 'tickets-earnings' && class_exists('EventPro')) {
										} //EventPro

										else {
											get_template_part('templates/dashboard/' . $dashboard . '');
										}
									} else {
										get_template_part('templates/dashboard/main-screen');
									}
									?>
								<?php }

								if (isset($_GET['dashboard']) && esc_html($_GET['dashboard']) == 'my-bookings' && class_exists('MedicalPro')) {
									$file = WP_CONTENT_DIR . '/plugins/medicalpro/templates/dashboard/' . esc_html($_GET['dashboard']) . '.php';
									require($file);
								} else if (isset($_GET['dashboard']) && esc_html($_GET['dashboard']) == 'earnings' && class_exists('MedicalPro')) {
									$file = WP_CONTENT_DIR . '/plugins/medicalpro/templates/dashboard/' . esc_html($_GET['dashboard']) . '.php';
									require($file);
								} //EventPro
								else if (isset($_GET['dashboard']) && esc_html($_GET['dashboard']) == 'events' && class_exists('EventPro')) {
									$file = EVENTPRO_PLUGIN_PATH . '/templates/dashboard/' . esc_html($_GET['dashboard']) . '.php';
									require($file);
								} else if (isset($_GET['dashboard']) && esc_html($_GET['dashboard']) == 'saved' && class_exists('EventPro')) {
									$file = EVENTPRO_PLUGIN_PATH . '/templates/dashboard/' . esc_html($_GET['dashboard']) . '.php';
									require($file);
								} else if (isset($_GET['dashboard']) && esc_html($_GET['dashboard']) == 'groups' && class_exists('EventPro')) {
									$file = EVENTPRO_PLUGIN_PATH . '/templates/dashboard/' . esc_html($_GET['dashboard']) . '.php';
									require($file);
								} else if (isset($_GET['dashboard']) && $showEpItems == 'on' && esc_html($_GET['dashboard']) == 'tickets-earnings' && class_exists('EventPro')) {
									$file = EVENTPRO_PLUGIN_PATH . '/templates/dashboard/' . esc_html($_GET['dashboard']) . '.php';
									require($file);
								} else if (isset($_GET['dashboard']) && esc_html($_GET['dashboard']) == 'my-tickets' && class_exists('EventPro')) {
									$file = EVENTPRO_PLUGIN_PATH . '/templates/dashboard/' . esc_html($_GET['dashboard']) . '.php';
									require($file);
								}
								//EventPro

								if ($simpleDashboard == true) {
									if (isset($_GET['dashboard']) && ! empty($_GET['dashboard'])) {
										if (esc_html($_GET['dashboard']) == 'discounts' || esc_html($_GET['dashboard']) == 'announcements' || esc_html($_GET['dashboard']) == 'offers') {
											get_template_part('templates/dashboard/' . esc_html($_GET['dashboard']) . '');
										} //EventPro
										else if (esc_html($_GET['dashboard']) == 'events' && class_exists('EventPro')) {
										} else if (esc_html($_GET['dashboard']) == 'saved' && class_exists('EventPro')) {
										} else if (esc_html($_GET['dashboard']) == 'groups' && class_exists('EventPro')) {
										} else if (esc_html($_GET['dashboard']) == 'tickets-earnings' && class_exists('EventPro')) {
										} else if (esc_html($_GET['dashboard']) == 'my-tickets' && class_exists('EventPro')) {
										} //EventPro

										else {
											get_template_part('templates/dashboard/' . $dashboard . '');
										}
									} else {
										get_template_part('templates/dashboard/update-profile');
									}
								}
								?>
							</div>

						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</section>
<?php } else { ?>
	<div class="col-md-12">
		<p><?php esc_html_e('Sorry! You have no permisssion to access this page. Please contact web admin.', 'listingpro'); ?></p>
	</div>
<?php } ?>
<!--==================================Section Close=================================-->
<div class="md-overlay"></div>
<?php get_footer(); ?>