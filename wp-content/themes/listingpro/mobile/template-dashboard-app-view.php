<?php

$userID = '';
if (is_user_logged_in()) {

    $current_user = wp_get_current_user();
    $userID = $current_user->ID;
} else {
    wp_redirect(home_url());
    exit;
}

/* by zaheer on 21 march */
$published_listings = '';
$pending_listings = '';
$expired_listings = '';
$all_listings = '';
$count_listings = wp_count_posts('listing', 'readable');
$published_listings = count_user_posts_by_status('listing', 'publish', $userID, false);
$pending_listings = count_user_posts_by_status('listing', 'pending', $userID, false);
$expired_listings = count_user_posts_by_status('listing', 'expired', $userID, false);
$all_listings = $published_listings + $pending_listings + $expired_listings;
/* end by zaheer on 21 march */

$simpleDashboard = false;
if (empty($published_listings) && empty($pending_listings) && empty($expired_listings)) {
    $simpleDashboard = true;
}

$updateTab = false;
global $user_id, $listingpro_options;

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$userSubscriptions = false;
$userSubs = get_user_meta($user_id, 'listingpro_user_sbscr', true);
if (!empty($userSubs)) {
    if (is_array($userSubs) && count($userSubs) > 0) {
        $userSubscriptions = true;
    }
}
$rmessage = '';
$rType = '';
if (isset($_POST['removeid']) && !empty($_POST['removeid'])) {
    $rID = $_POST['removeid'];
    $rpost = get_post($rID);
    $rpost_author = $rpost->post_author;
    if ($user_id == $rpost_author && current_user_can('delete_post', $rID)) {
        wp_delete_post($rID);
        $rmessage = esc_html__('Post has deleted succesfully', "listingpro");
        $rType = 'success';
    } else {
        $rmessage = esc_html__('You have no permission to delete this post', "listingpro");
        $rType = 'warning';
    }
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
// User Name
$user_fname = get_the_author_meta('first_name', $user_id);
$user_lname = get_the_author_meta('last_name', $user_id);
// User contact meta
$user_address = get_the_author_meta('address', $user_id);
$user_phone = get_the_author_meta('phone', $user_id);
$user_email = get_the_author_meta('user_email', $user_id);
// User Social links
$user_facebook = get_the_author_meta('facebook', $user_id);
$user_linkedin = get_the_author_meta('linkedin', $user_id);
$user_instagram = get_the_author_meta('instagram', $user_id);
$user_twitter = get_the_author_meta('twitter', $user_id);
$user_pinterest = get_the_author_meta('pinterest', $user_id);
// User BIO
$user_desc = get_the_author_meta('description', $user_id);
$user_ID = $user_id;
if ($user_ID) {

    if (isset($_POST['profileupdate'])) {
        if (!isset($_POST['profile_update_nonce']) || !wp_verify_nonce($_POST['profile_update_nonce'], 'profile_update_action')) {
            $message = esc_html__("Nonce verification failed. Please try again.", "listingpro");
            $mType = 'error';
        } else {
            $message = esc_html__("Your profile updated successfully.", "listingpro");
            $mType = 'success';

            $first = esc_html($_POST['first_name']);
            $last = esc_html($_POST['last_name']);
            $email = esc_html($_POST['email']);
            $user_phone = esc_html($_POST['phone']);
            $user_address = esc_html($_POST['address']);
            $description = esc_html($_POST['desc']);

            $facebook = esc_html($_POST['facebook']);
            $linkedin = esc_html($_POST['linkedin']);
            $instagram = esc_html($_POST['instagram']);
            $twitter = esc_html($_POST['twitter']);
            $pinterest = esc_html($_POST['pinterest']);

            $oldpassword      = esc_html($_POST['oldpassword']);
            $password = esc_html($_POST['pwd']);
            $confirm_password = esc_html($_POST['confirm']);

            $stored_hash = $wpdb->get_var($wpdb->prepare("SELECT user_pass FROM $wpdb->users WHERE ID = %d", $user_ID));

            update_user_meta($user_ID, 'first_name', $first);
            update_user_meta($user_ID, 'last_name', $last);
            update_user_meta($user_ID, 'phone', $user_phone);
            update_user_meta($user_ID, 'address', $user_address);
            update_user_meta($user_ID, 'description', $description);

            update_user_meta($user_ID, 'facebook', $facebook);
            update_user_meta($user_ID, 'linkedin', $linkedin);
            update_user_meta($user_ID, 'instagram', $instagram);
            update_user_meta($user_ID, 'twitter', $twitter);
            update_user_meta($user_ID, 'pinterest', $pinterest);

            $your_image_url = $_POST['your_author_image_url'];
            $author_avatar_url = get_user_meta($user_ID, "listingpro_author_img_url", true);
            if ($your_image_url != '') {
                update_user_meta($user_ID, 'listingpro_author_img_url', $your_image_url);
            } else {
                update_user_meta($user_ID, 'listingpro_author_img_url', $author_avatar_url);
            }

            if (isset($email) && is_email($email)) {
                wp_update_user(array('ID' => $user_ID, 'user_email' => $email));
            } else {
                $message = esc_html__("Please enter a valid email id.", "listingpro");
                $mType = 'error';
            }

            if (!empty($password) || !empty($confirm_password) || !empty($oldpassword)) {
                if (empty($oldpassword)) {
                    $message = "Please enter old passwrod";
                    $mType   = 'error';
                } else if (!wp_check_password($oldpassword, $stored_hash, $user_id)) {
                    $message = "Old passwrod is incorrect";
                    $mType   = 'error';
                } else if (empty($password) && !empty($confirm_password)) {
                    $message = esc_html__('Password Mismatch', 'listingpro');
                    $mType   = 'error';
                } else if (strlen($password) < 5 || strlen($password) > 15) {
                    $message = "Password must be 5 to 15 characters in length";
                    $mType   = 'error';
                } else if (isset($password) && $password != $confirm_password) {
                    $message = esc_html__('Password Mismatch', 'listingpro');
                    $mType   = 'error';
                } else if (isset($password) && !empty($password)) {
                    $lpCUser = wp_get_current_user();
                    $update  = wp_set_password($password, $user_ID);
                    wp_set_auth_cookie($lpCUser->ID);
                    wp_set_current_user($lpCUser->ID);
                    do_action('wp_login', $lpCUser->user_login, $lpCUser);
                    $message = esc_html__('Your profile updated successfully.', 'listingpro');
                    $mType   = esc_html__('success', 'listingpro');
                }
            }
            $updateTab = true;
        }
    }
}

$contactSupport = $listingpro_options['contact_support'];
$resurva_bookings_enable = $listingpro_options['resurva_bookings_enable'];
$timekit_bookings_enable = $listingpro_options['timekit_bookings_enable'];
$menu_bookings_enable = true;


/* by zaheer on 28 march */
$published_campaings = lp_count_user_campaigns($userID);
/* end by zaheer on 28 march */
?>
<?php get_header('app-view'); ?>
<script type="text/javascript">
    jQuery(document).ready(function(e) {
        jQuery(document).on('click', '.app-dashboard-menu-toggle.open-menu', function(e) {
            jQuery(this).removeClass('open-menu');
            jQuery(this).addClass('close-menu');
            jQuery(this).html('<i class="fa fa-times" aria-hidden="true"></i>');
            jQuery('.app-view-dashboard-menu').animate({
                marginLeft: '0px'
            }, 500, function() {

            })
        })
        jQuery(document).on('click', '.app-dashboard-menu-toggle.close-menu', function(e) {
            jQuery(this).addClass('open-menu');
            jQuery(this).removeClass('close-menu');
            jQuery(this).html('<i class="fa fa-bars" aria-hidden="true"></i>');
            jQuery('.app-view-dashboard-menu').animate({
                marginLeft: '-220px'
            }, 500, function() {

            })
        })
    })
</script>
<!--==================================Section Open=================================-->
<?php
if (class_exists('ListingproPlugin')) {
    $current_user = wp_get_current_user();
    $u_display_name = $current_user->display_name;
    if (empty($u_display_name)) {
        $u_display_name = $current_user->nickname;
    }
?>
    <section class="aliceblue">
        <div class="admin-top-section">
            <div class="user-details">
                <div class="row pos-relative">
                    <div class="user-portfolio">
                        <div class="user-info">
                            <div class="user-thumb">
                                <img class="avatar-circle" src="<?php echo listingpro_author_image(); ?>" alt="userimg" />
                            </div>
                            <div class="user-text">
                                <h5 class="user-name margin-top-0">
                                    <span><?php esc_html_e('Howdy!', 'listingpro'); ?></span>
                                    <?php echo esc_attr($u_display_name); ?>
                                </h5>
                                <p><?php echo esc_html($user_address); ?></p>
                                <p><?php echo esc_html($user_phone); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="admin-top-section-bar">
                <div class="col-sm-4 admin-menue-icon">
                    <button type="button" class="navbar-toggle app-dashboard-menu-toggle open-menu">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="col-sm-8 lp-contact-support">
                    <?php if (!empty($contactSupport)) { ?>
                        <div class="">
                            <a href="<?php echo esc_url($contactSupport); ?>" class="secondary-btn">
                                <i class="fa fa-support"></i>
                                <?php esc_html_e('Contact Support', 'listingpro'); ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>
            </div>

        </div>

        <?php
        $dashPage = '';
        $opendClass = '';
        $openedClasss = '';
        $activeListing = '';
        $activePending = '';
        $activeExpired = '';
        if (isset($_GET['dashboard'])) {
            $dashPage = esc_html($_GET['dashboard']);
        }

        if (!empty($dashPage) && ($dashPage == "listing" || $dashPage == "pending" || $dashPage == "expired")) {
            $opendClass = 'opened';

            if ($dashPage == "listing") {
                $activeListing = 'class="active"';
            } else if ($dashPage == "pending") {
                $activePending = 'class="active"';
            } else if ($dashPage == "expired") {
                $activeExpired = 'class="active"';
            }
        }

        $lft_panel = '';
        $openedClass = '';
        $camp_Listing = '';
        $activeCampListing = '';
        $invcListing = '';
        $activeinvoiceListing = '';
        if (isset($_GET['dashboard'])) {
            $lft_panel = esc_html($_GET['dashboard']);
        }
        if (!empty($lft_panel) && ($lft_panel == "campaigns" || $lft_panel == "active-campaigns")) {


            if ($lft_panel == "campaigns") {
                $camp_Listing = 'class="active"';
            } else if ($lft_panel == "active-campaigns") {
                $activeCampListing = 'class="active"';
            }
        }

        if (!empty($lft_panel) && ($lft_panel == "list-invoices" || $lft_panel == "ads-invoices")) {
            $openedClasss = 'opened';

            if ($lft_panel == "list-invoices") {
                $activeinvoiceListing = 'class="active"';
            } else if ($lft_panel == "ads-invoices") {
                $invcListing = 'class="active"';
            }
        }

        $review_panel = '';
        $reviewOpenedClass = '';
        $review_Listing = '';
        $activeReviewListing = '';
        if (isset($_GET['dashboard'])) {
            $review_panel = esc_html($_GET['dashboard']);
        }
        if (!empty($review_panel) && ($review_panel == "reviews" || $review_panel == "reviews-submited")) {
            $reviewOpenedClass = 'opened';

            if ($review_panel == "reviews") {
                $review_Listing = 'class="active"';
            } else if ($review_panel == "reviews-submited") {
                $activeReviewListing = 'class="active"';
            }
        }

        $tymkit_panel = '';
        $tymkitOpenedClass = '';
        $tymkit_Listing = '';
        $activeTymkitListing = '';
        if (isset($_GET['dashboard'])) {
            $tymkit_panel = esc_html($_GET['dashboard']);
        }
        if (!empty($tymkit_panel) && ($tymkit_panel == "bookings" || $tymkit_panel == "timekit-bookings")) {
            $tymkitOpenedClass = 'opened';

            if ($tymkit_panel == "bookings") {
                $tymkit_Listing = 'class="active"';
            } else if ($tymkit_panel == "timekit-bookings") {
                $activeTymkitListing = 'class="active"';
            }
        }

        $creat_menu_openedClass     =   '';
        $creat_menu_panel           =   '';
        $create_menu_a              =   '';
        $create_groups_a            =   '';
        $create_types_a             =   '';
        $create_serv_s_a            =   '';
        if (isset($_GET['dashboard'])) {
            $creat_menu_panel   =   esc_html($_GET['dashboard']);
        }
        if (!empty($creat_menu_panel) && ($creat_menu_panel == 'create-menu' || $creat_menu_panel == 'menu-types' || $creat_menu_panel == 'menu-groups') || $creat_menu_panel == 'services-screen') {
            $creat_menu_openedClass =   'opened';
            if ($creat_menu_panel == 'create-menu') {
                $create_menu_a  =   'class="active"';
            } elseif ($creat_menu_panel  ==  'menu-types') {
                $create_types_a =   'class="active"';
            } elseif ($creat_menu_panel == 'menu-groups') {
                $create_groups_a    =   'class="active"';
            } elseif ($creat_menu_panel == 'services-screen') {
                $create_serv_s_a    =   'class="active"';
            }
        }

        $activeDashboardMenu = '';
        $activeAnnouncementMenu = '';
        $activeEventsMenu = '';
        $activeDiscountMenu = '';
        $activeprofileMenu = '';
        $activesavedMenu = '';
        $activesavedListing = '';
        $activesavedinvoices = '';

        $activepackagesMenu = '';
        $activecampaignsMenu = '';
        $activereviewsMenu = '';
        $activeBookingsMenu = '';
        $activeMenuItem = '';
        $activemenues = '';
        $activeSubscriptionMenu = '';
        $activeinbox     = '';
        if (!empty($dashPage)) {
            if ($dashPage == "main-screen") {
                $activeDashboardMenu = 'class="active-dash-menu"';
            } else if ($dashPage == 'announcements') {
                $activeAnnouncementMenu =   'class="active-dash-menu"';
            } else if ($dashPage == 'events') {
                $activeEventsMenu =   'class="active-dash-menu"';
            } elseif ($dashPage == 'discounts') {
                $activeDiscountMenu =   'class="active-dash-menu"';
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
            } else if ($dashPage == "menus") {
                $activemenues = 'class="active-dash-menu"';
            } else if ($dashPage == "inbox") {
                $activeinbox = 'class="active-dash-menu"';
            }
        }


        $currentURL = '';
        $perma = '';
        $dashQuery = 'dashboard=';
        $currentURL = get_permalink();
        global $wp_rewrite;
        if ($wp_rewrite->permalink_structure == '') {
            $perma = "&";
        } else {
            $perma = "?";
        }

        $dashboard_usr_show = true;
        $my_listings_show = true;
        $saved_listing_show = true;
        $invoices_dashboard_show = true;
        global $wpdb;
        $dbprefix = '';
        $post_ids = '';
        $dbprefix = $wpdb->prefix;
        $user_ID = '';
        $user_ID = get_current_user_id();
        $results = '';
        $resultss = '';
        $results = $wpdb->get_results("SELECT * FROM {$dbprefix}listing_orders WHERE user_id ='$user_ID' AND plan_type ='Package' AND status='success'");
        $resultss = $wpdb->get_results("SELECT * FROM {$dbprefix}listing_orders WHERE user_id ='$user_ID' AND plan_type ='Package' AND status='expired'");

        if (empty($results) && empty($resultss)) {
            $dashboard_packages_show = false;
        } else {
            $dashboard_packages_show = true;
        }
        $ad_compaigns_show = true;
        $review_dashoard_show = true;
        $booking_dashoard_show = true;
        $menu_dashoard_show = true;
        $my_profile_show = true;
        $log_out_show = true;
        $announcements_show =   true;
        $events_show =   true;
        $discounts_show =   true;
        $lead_form_show =   false;
        $lead_form_user_dashboard =    get_option('lead_form_user_dashboard');
        if ($lead_form_user_dashboard == 1) {
            $lead_form_show = true;
        }
        $dashboard_usr = $listingpro_options['dashboard_usr'];
        if ($dashboard_usr == 0) {
            $dashboard_usr_show = false;
        }
        if (isset($listingpro_options['announcements_dashoard']) && $listingpro_options['announcements_dashoard'] == 0) {
            $announcements_show =   false;
        }
        if (isset($listingpro_options['events_dashoard']) && $listingpro_options['events_dashoard'] == 0) {
            $events_show =   false;
        }
        if (isset($listingpro_options['discounts_dashoard']) && $listingpro_options['discounts_dashoard'] == 0) {
            $discounts_show =   false;
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
        <div class="dashboard-content dashboard-content-app-view">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="pull-left left-panel tbl-cell app-view-dashboard-menu">
                        <div class="dashboard-tabs lp-main-tabs text-center">
                            <!-- Left Panel Navigation Starts -->
                            <ul>
                                <?php if ($simpleDashboard == false) { ?>

                                    <?php if ($dashboard_usr_show == true) { ?>
                                        <li>
                                            <a <?php echo esc_attr($activeDashboardMenu); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'main-screen'); ?>"><i class="fa fa-dashboard"></i><?php esc_html_e(' Dashboard', 'listingpro'); ?></a>
                                        </li>
                                    <?php } ?>

                                <?php } ?>
                                <?php
                                if ($simpleDashboard == false && $dashboard_usr_show == true && $announcements_show == true):
                                ?>
                                    <li><a <?php echo esc_attr($activeAnnouncementMenu); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'announcements'); ?>"><span class="sub_icon sub_iconfirst"><i style="color:#fff" class="fa fa-microphone" aria-hidden="true"></i> </span> <?php echo esc_html__('Announcements', 'listingpro'); ?> </a> </li>
                                <?php endif; ?>
                                <?php
                                if ($simpleDashboard == false && $dashboard_usr_show == true && $events_show == true):
                                ?>
                                    <li><a <?php echo esc_attr($activeEventsMenu); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'events'); ?>">
                                            <span class="sub_icon sub_iconfirst"><i class="fa fa-calendar" aria-hidden="true"></i></span> <?php echo esc_html__('Events', 'listingpro'); ?>


                                        </a> </li>
                                <?php endif; ?>
                                <?php
                                if ($simpleDashboard == false && $dashboard_usr_show == true && $discounts_show == true):
                                ?>
                                    <li><a <?php echo esc_attr($activeDiscountMenu); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'discounts'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-bar-chart" aria-hidden="true"></i></span> <?php echo esc_html__('Discounts/Deals', 'listingpro'); ?> </a> </li>
                                <?php endif; ?>
                                <?php
                                if ($simpleDashboard == false && $dashboard_usr_show == true && $lead_form_show == true):
                                ?>
                                    <li><a href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'lead_form'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-bullhorn" aria-hidden="true"></i></span> <?php echo esc_html__('Lead Form', 'listingpro'); ?> </a> </li>
                                <?php endif; ?>
                                <?php if ($menu_bookings_enable == 1) { ?>
                                    <?php if ($simpleDashboard == false) { ?>
                                        <?php if ($menu_dashoard_show == true) {
                                            $food_menu_icon = lp_theme_option_url('food_menu_icon');
                                            if (empty($food_menu_icon)) {
                                        ?>


                                                <li><a <?php echo esc_attr($activemenues); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'menus'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-credit-card" aria-hidden="true"></i></span> <?php esc_html_e('Menu', 'listingpro'); ?> </a></li>
                                            <?php } elseif (!empty($food_menu_icon)) { ?>
                                                <li><a <?php echo esc_attr($activemenues); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'menus'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-credit-card" aria-hidden="true"></i></span> <?php esc_html_e('Menu', 'listingpro'); ?> </a></li>
                                            <?php } ?>

                                        <?php } ?>
                                <?php }
                                } ?>
                                <?php if ($simpleDashboard == true) { ?>

                                    <?php if ($my_profile_show == true) { ?>
                                        <li>
                                            <a <?php echo esc_attr($activeprofileMenu); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'update-profile'); ?>"><i class="fa fa-user-circle"></i><?php esc_html_e(' My Profile', 'listingpro'); ?></a>
                                        </li>
                                    <?php } ?>

                                    <?php if ($saved_listing_show == true) { ?>
                                        <li><a <?php echo esc_attr($activesavedMenu); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'saved'); ?>"><i class="fa fa-heart" aria-hidden="true"></i><?php esc_html_e('Saved', 'listingpro'); ?> </a></li>
                                    <?php } ?>

                                <?php } ?>

                                <?php if ($simpleDashboard == false) { ?>
                                    <?php if ($my_listings_show == true) { ?>
                                        <li><a <?php echo esc_attr($activesavedListing); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'listings'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-map-marker" aria-hidden="true"></i></span> <?php esc_html_e('Listings', 'listingpro'); ?> </a></li>
                                    <?php } ?>

                                    <li><a <?php echo esc_attr($activeinbox); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'inbox'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-envelope-o" aria-hidden="true"></i></span> <?php esc_html_e('Inbox', 'listingpro'); ?> </a></li>


                                    <?php if ($invoices_dashboard_show == true) { ?>
                                        <li><a <?php echo esc_attr($activesavedinvoices); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'invoices'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></span> <?php esc_html_e('Invoices', 'listingpro'); ?> </a></li>
                                    <?php } ?>
                                    <?php if ($saved_listing_show == true) { ?>
                                        <li><a <?php echo esc_attr($activesavedMenu); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'saved'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-heart" aria-hidden="true"></i></span> <?php esc_html_e('Saved', 'listingpro'); ?> </a></li>
                                    <?php } ?>
                                    <?php if ($dashboard_packages_show == true) { ?>
                                        <li><a <?php echo esc_attr($activepackagesMenu); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'packages'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-gift" aria-hidden="true"></i></span> <?php esc_html_e('Packages', 'listingpro'); ?> </a></li>
                                    <?php } ?>
                                    <?php if ($userSubscriptions == true) { ?>
                                        <li>
                                            <a <?php echo esc_attr($activeSubscriptionMenu); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'subscription'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-flag"></i></span> <?php esc_html_e(' Subscriptions', 'listingpro'); ?> </a>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                    <?php
                                    if (class_exists('ListingAds')) {
                                    ?>
                                        <?php if ($ad_compaigns_show == true) { ?>
                                            <li class="">
                                                <a href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'active-campaigns'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-envelope" aria-hidden="true"></i> </span><?php esc_html_e('Ad Campaigns ', 'listingpro'); ?> </a>

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
                                            <a href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'reviews'); ?>"><span class="sub_icon sub_iconfirst"><i class="fa fa-star" aria-hidden="true"></i> </span> <?php esc_html_e(' Reviews ', 'listingpro'); ?> </a>
                                        </li>
                                    <?php } ?>
                                <?php
                                }
                                ?>

                                <?php if ($timekit_bookings_enable == 1 || $resurva_bookings_enable == 1) { ?>
                                    <?php if ($simpleDashboard == false) { ?>

                                        <?php if ($booking_dashoard_show == true) { ?>
                                            <li class="dropdown dropdown-reviews <?php echo esc_attr($tymkitOpenedClass); ?>">
                                                <a href="#"><i class="fa fa-calendar"></i><?php esc_html_e('Bookings ', 'listingpro'); ?> <i class="fa fa-angle-down"></i></a>
                                                <ul class="<?php echo esc_attr($tymkitOpenedClass); ?>">
                                                    <?php if ($resurva_bookings_enable == 1) { ?>
                                                        <li <?php echo esc_attr($tymkit_Listing); ?>><a href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'bookings'); ?>"><i class="fa fa-angle-right"></i><?php esc_html_e(' Resurva', 'listingpro'); ?></a></li>
                                                    <?php } ?>
                                                    <?php if ($timekit_bookings_enable == 1) { ?>
                                                        <li <?php echo esc_attr($activeTymkitListing); ?>><a href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'timekit-bookings'); ?>"><i class="fa fa-angle-right"></i><?php esc_html_e(' Timekit', 'listingpro'); ?></a></li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        <?php } ?>
                                <?php }
                                } ?>

                                <?php if ($simpleDashboard == false) { ?>
                                    <?php if ($my_profile_show == true) { ?>
                                        <li>
                                            <a <?php echo esc_attr($activeprofileMenu); ?> href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'update-profile'); ?>"><i class="fa fa-user-circle"></i><?php esc_html_e(' My Profile', 'listingpro'); ?></a>
                                        </li>


                                    <?php } ?>
                                <?php } ?>
                                <?php if ($log_out_show == true) { ?>
                                    <li><a href="<?php echo wp_logout_url(); ?>"><i class="fa fa-unlock-alt"></i><?php esc_html_e(' Logout', 'listingpro'); ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <?php if ($simpleDashboard == false) { ?>
                        <?php
                        $dashboard = isset($_GET['dashboard']) ? sanitize_file_name($_GET['dashboard']) : '';
                        if (isset($_GET['dashboard']) && !empty($_GET['dashboard'])) {
                            if ($_GET['dashboard'] == 'main-screen') {
                                get_template_part('mobile/templates/dashboard/main-screen-app-view');
                            } else {
                                get_template_part('templates/dashboard/' . $dashboard . '');
                            }
                        } else {
                            get_template_part('mobile/templates/dashboard/main-screen-app-view');
                        }
                        ?>
                    <?php } ?>

                    <?php

                    if ($simpleDashboard == true) {
                        if (isset($_GET['dashboard']) && !empty($_GET['dashboard'])) {
                            get_template_part('templates/dashboard/' . $dashboard . '');
                        } else {
                            get_template_part('templates/dashboard/update-profile');
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
<?php } else { ?>
    <div class="col-md-12">
        <p><?php esc_html_e('Sorry! You have no permisssion to access this page. Please contact web admin.', 'listingpro'); ?></p>
    </div>
<?php } ?>
<!--==================================Section Close=================================-->
<div class="md-overlay"></div>