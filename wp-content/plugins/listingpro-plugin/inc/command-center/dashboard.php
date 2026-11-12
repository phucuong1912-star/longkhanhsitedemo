<?php lp_open_chat_box();
$content = get_option( 'lp_overview_api_content' );
if( !isset($content['listingpro_options']['services_link']) &&  empty($content['listingpro_options']['services_link']) ){
	$content['listingpro_options']['services_link'] = 'https://listingprowp.com/services/';
}
?>
<div class="lp-cc-dashboard-content-container">
    <div class="lp-cc-dashboard-navbar">
        <div class="lp-cc-dashboard-navbar-brand">
            <img alt="image" src="<?php echo get_site_url(); ?>/wp-content/plugins/listingpro-plugin/images/logo.png"/>
        </div>
         <div class="lp-cc-dashboard-navbar-nav">
            <span>Need Customization?<a href="<?php echo esc_url($content['listingpro_options']['services_link']); ?>" target="_blank" class="lp-need-customization">Request Free Quote</a></span>
			<span class="or"></span>
			<span>Need Help?<a href="https://docs.listingprowp.com/" target="_blank">Go to Help Center</a></span>
         </div>
    </div>
    <div class="lp-cc-dashboard-content-header">
        <p class="lp-cc-dashboard-content-header-title">Welcome to ListingPro Command Center!</p>
        <?php
        $verification_check =   get_option( 'theme_activation' );
        if($verification_check != 'activated') {
            ?>
            <p class="lp-cc-dashboard-content-header-title-des">ListingPro is now installed and ready to use! Get ready to
                built your dream directory. Please <a href="<?php echo admin_url('admin.php?page=lp-cc-license'); ?>">activate your license</a> to get automatic updates, enable
                Visualiz
                <wbr>
                er modules, install free Add-ons and more. We hope you enjoy it
            </p>
            <?php
        }
        ?>
    </div>
	
    <div class="lp-cc-dashboard-content-btn-group">
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=listingpro" class="active float-l">Dashboard</a>
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-addons" class="float-l">Add-ons</a>
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-visualizer" class="float-l">Visualizer</a>
        <a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-license" class="float-l">License</a>
		<?php if( isset($content['listingpro_options']['services_link']) &&  !empty($content['listingpro_options']['services_link']) ){ ?>
		<a href="<?php echo esc_url($content['listingpro_options']['services_link']); ?>" target="_blank" class="lp-services-link float-l">Services</a>
		<?php } ?>
    </div>
</div>

<div class="lp-cc-dashboard-body-container">
    <div class="lp-cc-dashboard-time-range-bar" style="display: none;">
        <?php
        $query_date = date("Y/m/d");
        $cmonth_date = date("m");
        $today_date = date("dS Y");
        $last30days = date('dS Y', strtotime('-30 days', strtotime($query_date)));
        $last15days = date('dS Y', strtotime('-15 days', strtotime($query_date)));
        $last7days = date('dS Y', strtotime('-7 days', strtotime($query_date)));
        $last3days = date('dS Y', strtotime('-3 days', strtotime($query_date)));
        $last30daysm = date('m', strtotime('-30 days', strtotime($query_date)));
        $last15daysm = date('m', strtotime('-15 days', strtotime($query_date)));
        $last7daysm = date('m', strtotime('-7 days', strtotime($query_date)));
        $last3daysm = date('m', strtotime('-3 days', strtotime($query_date)));
        $last30daysmq = DateTime::createFromFormat('!m', $last30daysm);
        $last15daysmq = DateTime::createFromFormat('!m', $last15daysm);
        $last7daysmq = DateTime::createFromFormat('!m', $last7daysm);
        $last3daysmq = DateTime::createFromFormat('!m', $last3daysm);
        $cmonth = DateTime::createFromFormat('!m', $cmonth_date);
        $last30daysdata = $last30daysmq->format('F');
        $last15daysdata = $last15daysmq->format('F');
        $last7daysdata = $last7daysmq->format('F');
        $last3daysdata = $last3daysmq->format('F');
        $cmonthdata = $cmonth->format('F');
        ?>
        <h3 class="lp-cc-dashboard-time-range-bar-time-title days-count-30"
            style="display: block;"><?php echo $last30daysdata . ' ' . $last30days . ' - ' . $cmonthdata . ' ' . $today_date; ?></h3>
        <h3 class="lp-cc-dashboard-time-range-bar-time-title days-count-15"><?php echo $last15daysdata . ' ' . $last15days . ' - ' . $cmonthdata . ' ' . $today_date; ?></h3>
        <h3 class="lp-cc-dashboard-time-range-bar-time-title days-count-7"><?php echo $last7daysdata . ' ' . $last7days . ' - ' . $cmonthdata . ' ' . $today_date; ?></h3>
        <h3 class="lp-cc-dashboard-time-range-bar-time-title days-count-3"><?php echo $last3daysdata . ' ' . $last3days . ' - ' . $cmonthdata . ' ' . $today_date; ?></h3>
        <div class="selectParent">
            <select class="lp-cc-dashboard-time-range-bar-time-dropdown"
                    style="background: #ffffff url('<?php echo get_site_url(); ?>/wp-content/plugins/listingpro-plugin/images/calender-icon.png') no-repeat left center;">
                <option>Last 30 Days</option>
                <option>Last 15 Days</option>
                <option>Last 7 Days</option>
                <option>Last 3 Days</option>
            </select>
            <i class="fa fa-sort"></i>
        </div>
    </div>
    <?php
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE)
        $alt_class_card = 'lp-cc-dashboard-user-data-detail-alt';
    elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE)
        $alt_class_card = '';
    elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== FALSE)
        $alt_class_card = 'lp-cc-dashboard-user-data-detail-alt';
    elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== FALSE)
        $alt_class_card = '';
    elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== FALSE)
        $alt_class_card = 'lp-cc-dashboard-user-data-detail-alt';
    else
        $alt_class_card = '';
    ?>
    <div class="clearfix"></div>
    <div class="lp-cc-dashboard-user-data-detail <?php echo $alt_class_card ?>">
        <div class="lp-cc-dashboard-user-data-detail-card">
            <div class="lp-cc-dashboard-user-data-detail-card-header">
                <?php
                $all_users = get_users();
                $visitors_arr = array();
                $members_arr = array();
                foreach ($all_users as $user) {
                    $count_listings = count_user_posts($user->ID, 'listing');
                    if ($count_listings > 0) {
                        $members_arr[$user->ID] = $user->ID;
                    } else {
                        $visitors_arr[$user->ID] = $user->ID;
                    }
                }
                ?>
                <p>TOTAL Users</p>
                <span>
                    <?php
                    $result = count_users();
                    echo $result['total_users'];
                    ?>
                </span>
            </div>
            <div class="lp-cc-dashboard-user-data-detail-card-content">
                <a href="<?php echo admin_url('users.php?s&action=-1&new_role&users_type_top=listing_owners&top=Filter&paged=1&action2=-1&new_role2&users_type_bottom'); ?>"><span>LISTING OWNERS</span></a>
                <span><?php echo count($members_arr); ?></span>
            </div>
            <div class="lp-cc-dashboard-user-data-detail-card-content">
                <a href="<?php echo admin_url('users.php?s&action=-1&new_role&users_type_top=general_users&top=Filter&paged=1&action2=-1&new_role2&users_type_bottom'); ?>"><span>OTHERS</span></a>
                <span>
                    <?php
                    echo count($visitors_arr);
                    ?>
                </span>
            </div>
            <div class="lp-cc-dashboard-user-data-detail-card-footer">
                <?php
                $DBRecord = array();
                $args = array(
                    'orderby' => 'post_count',
                    'who' => '',
                    'order' => 'ASD',
                    'has_published_posts' => true,
                );
                $users = get_users($args);
                $i = 0;
                foreach ($users as $user) {
                    $author_avatar_url = get_user_meta($user->ID, "listingpro_author_img_url", true);
                    if ($author_avatar_url == '') {
                        $author_avatar_url = get_avatar_url($user->ID);
                    }
                    echo '<span><a href="' . get_site_url() . '/author/' . $user->user_login . '"><img alt="image" src="' . $author_avatar_url . '"></a></span>';
                    $i++;
                    if ($i == 3) break;
                }
                ?>
                <p>TOP MEMBERS</p>
            </div>
        </div>
        <div class="lp-cc-dashboard-user-data-detail-card">
            <div class="lp-cc-dashboard-user-data-detail-card-header">
                <p>total LISTINGS</p>
                <span>
                    <?php
                    $publish = wp_count_posts('listing')->publish;
                    $future = wp_count_posts('listing')->future;
                    $draft = wp_count_posts('listing')->draft;
                    $pending = wp_count_posts('listing')->pending;
                    $private = wp_count_posts('listing')->private;
                    $inherit = wp_count_posts('listing')->inherit;
                    $inactive = wp_count_posts('listing')->inactive;
                    $expired = wp_count_posts('listing')->expired;
                    $count_posts = $publish + $future + $draft + $pending + $private + $inherit + $inactive + $expired;
                    echo $count_posts;
                    ?>
                </span>
            </div>
            <div class="lp-cc-dashboard-user-data-detail-card-content">
                <a href="<?php echo admin_url('edit.php?post_status=publish&post_type=listing'); ?>"><span>ACTIVE LISTINGS</span></a>
                <span>
                    <?php
                    echo $publish;
                    ?>
                </span>
            </div>
            <div class="lp-cc-dashboard-user-data-detail-card-content">
                <a href="<?php echo admin_url('edit.php?post_status=pending&post_type=listing'); ?>"><span>PENDING LISTINGS</span></a>
                <span>
                    <?php
                    echo $pending
                    ?>
                </span>
            </div>
            <div class="lp-cc-dashboard-user-data-detail-card-footer">
                <?php
                global $listingpro_options;
                $listing_def_img = $listingpro_options['lp_def_featured_image'];
                $args = array(
                    'post_type' => 'listing',
                    'meta_key' => 'post_views_count',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                );
                $the_query = new WP_Query($args);
                if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post();
                    $imgurl = get_the_post_thumbnail_url(get_the_id(), 'small');
                    if ($imgurl == '') {
                        $imgurl = $listing_def_img['url'];
                        if ($imgurl == '') {
                            $imgurl = 'https://placehold.co/50';
                        }
                    }
                    echo '<span><a href="' . get_post_permalink(get_the_id()) . '"><img alt="image" src="' . $imgurl . '"></a></span>';
                endwhile;endif;
                ?>
                <p>TOP LISTINGS</p>
            </div>
        </div>
        <?php
        if(class_exists('ListingAds')) {
            ?>
            <div class="lp-cc-dashboard-user-data-detail-card">
                <div class="lp-cc-dashboard-user-data-detail-card-header">
                    <p>Total Ads</p>
                    <span>
                    <?php
                    $publish = wp_count_posts('lp-ads')->publish;
                    $future = wp_count_posts('lp-ads')->future;
                    $draft = wp_count_posts('lp-ads')->draft;
                    $pending = wp_count_posts('lp-ads')->pending;
                    $private = wp_count_posts('lp-ads')->private;
                    $trash = wp_count_posts('lp-ads')->trash;
                    $inherit = wp_count_posts('lp-ads')->inherit;
                    $inactive = wp_count_posts('lp-ads')->inactive;
                    $expired = wp_count_posts('lp-ads')->expired;
                    $count_posts = $publish + $future + $draft + $pending + $private + $inherit + $inactive + $expired;
                    echo $count_posts;
                    ?>
                </span>
                </div>
                <div class="lp-cc-dashboard-user-data-detail-card-content">
                    <a href="<?php echo admin_url('edit.php?post_status=publish&post_type=lp-ads'); ?>"><span>Active ads</span></a>
                    <span>
                    <?php
                    echo $publish;
                    ?>
                </span>
                </div>
                <div class="lp-cc-dashboard-user-data-detail-card-content">
                    <a href="<?php echo admin_url('edit.php?post_status=inactive&post_type=lp-ads'); ?>"><span>INACTIVE ADS</span></a>
                    <span class="">
                    <?php
                    echo $inactive;
                    ?>
                </span>
                </div>
                <div class="lp-cc-dashboard-user-data-detail-card-footer">
                    <?php
                    global $wpdb;
                    $adstable = "listing_campaigns";
                    $adstable = $wpdb->prefix . $adstable;
                    $adsresults = array();
                    $query = "";
                    if(lp_table_exists_check($adstable)) {
                        $query = "SELECT * from $adstable ORDER BY budget DESC limit 3";
                    }
                    $adsresults = $wpdb->get_results($query);
                    global $listingpro_options;
                    $listing_def_img = $listingpro_options['lp_def_featured_image'];
                    $index = 0;
                    if(is_array($adsresults)) {
                        foreach ($adsresults as $key => $value) {
                            $imgurl = get_the_post_thumbnail_url($value->post_id, 'small');
                            if ($imgurl == '') {
                                $imgurl = $listing_def_img['url'];
                                if ($imgurl == '') {
                                    $imgurl = 'https://placehold.co/50';
                                }
                            }
                            echo '<span><a href="' . get_post_permalink($value->post_id) . '"><img alt="image" src="' . $imgurl . '"></a></span>';
                            $index++;
                            if ($index === 3) break;
                        }
                    }

                    ?>
                    <p>TOP ADS</p>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="clearfix"></div>
    <div class="lp-cc-dashboard-user-listing-data <?php echo $alt_class_card ?>">
        <div class="border"></div>
        <?php
        $yearsStats = get_option('lp_years_stats');
        global $wpdb;
        $resultViews    =   '';
        $totalViewsCounts = 0;
        if(lp_table_exists_check($wpdb->prefix . 'listing_stats_views')) {
            $resultViews = $wpdb->get_results('SELECT sum(count) as totalviews FROM ' . $wpdb->prefix . 'listing_stats_views');
        }
        if (!empty($resultViews)) {
            $totalViewsCounts = $resultViews[0]->totalviews;
            if (empty($totalViewsCounts)) {
                $totalViewsCounts = 0;
            }
        }
        $totalReviewsCounts = 0;
        $resultReviews  =   '';
        if(lp_table_exists_check($wpdb->prefix . 'listing_stats_reviews')) {
            $resultReviews = $wpdb->get_results('SELECT sum(count) as totalviews FROM ' . $wpdb->prefix . 'listing_stats_reviews');
        }

        if (!empty($resultReviews)) {
            $totalReviewsCounts = $resultReviews[0]->totalviews;
            if (empty($totalReviewsCounts)) {
                $totalReviewsCounts = 0;
            }
        }
        $totalLeadsCounts = 0;
        $resultLeads    =   '';
        if(lp_table_exists_check($wpdb->prefix . 'listing_stats_leads')) {
            $resultLeads = $wpdb->get_results('SELECT sum(count) as totalviews FROM ' . $wpdb->prefix . 'listing_stats_leads');
        }
        if (!empty($resultLeads)) {
            $totalLeadsCounts = $resultLeads[0]->totalviews;
            if (empty($totalLeadsCounts)) {
                $totalLeadsCounts = 0;
            }
        }
        ?>
        <div class="lp-cc-dashboard-user-listing-data-card-container">
            <div class="lp-cc-dashboard-user-listing-data-card">
                <p><?php echo $totalViewsCounts; ?></p>
                <p>total listing VIEws</p>
            </div>
            <div class="lp-cc-dashboard-user-listing-data-card">
                <p><?php echo $totalReviewsCounts; ?></p>
                <p>total reviews</p>
            </div>
            <div class="lp-cc-dashboard-user-listing-data-card">
                <p><?php echo $totalLeadsCounts; ?></p>
                <p>total leaDs</p>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <input type="hidden" value="admin_url( 'admin-ajax.php' )">
    <div class="lp-cc-dashboard-user-activity">
        <div class="lp-cc-dashboard-user-activity-task lp-cc-dashboard-user-activity-card <?php echo $alt_class_card ?>">
            <div class="border"></div>
            <div class="lp-cc-dashboard-user-activity-card-header">
                <p>Pending Tasks</p>
                <div class="lp-dashboard-custom-select-dropdown lp-dashboard-custom-select-dropdown-pending">
                    <img alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/ajax-load.gif'; ?>"
                         class="lp-cc-custom-preloader">
                    <span class="lp-dashboard-custom-select-dropdown-placeholder"><i class="fade">Show:</i> New Listings</span>
                    <ul>
                        <li data-value="New Listing">New Listings</li>
                        <li data-value="Claim Request">Claim Requests</li>
                        <li data-value="Flagged Review">Flagged Reviews</li>
                        <li data-value="Flagged Listing">Flagged Listings</li>
                    </ul>
                </div>
            </div>
            <div class="lp-cc-dashboard-user-activity-card-content">
                <div class="lp-cc-custom-preloader-white-space"></div>
                <div class="lp-cc-dashboard-user-activity-card-body new-listings">
                    <ul class="lp-cc-dashboard-user-activity-card-content-head">
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Item</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Date</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Price</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Status</li>
                    </ul>
                    <?php
                    $args = array(
                        'post_type' => 'listing',
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'posts_per_page' => 5,
                        'post_status' => 'pending',
                    );
                    $the_query = new WP_Query($args);
                    if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post();
// For Ago Days Passes
                        $now = time();
                        $your_date = strtotime(get_the_date());
                        $datediff = $now - $your_date;
                        $days = round($datediff / (60 * 60 * 24));
                        $month_remaining = intval($days / 30);
                        $years_remaining = intval($days / 365);
                        $print = 'In ' . $days . ' Days';
                        if ($days > 30) {
                            $print = 'In ' . $month_remaining . ' Month';
                        }
                        if ($month_remaining > 11) {
                            $print = 'In ' . $years_remaining . ' Year';
                        }
// Getting Pricing Plan
                        $myplan = listing_get_metabox_by_ID('Plan_id', get_the_ID());
                        $myplan_price = get_post_meta($myplan, 'plan_price', true);
                        $plan_price_exp = '';

                        if ($myplan_price <= 0) {
                            $myplanname = 'Free Plan';
                            $myplanprice = 'N/A';
                            $plan_price_exp = 'low';
                        } else {
                            $myplanname = 'Paid Plan';
                            $myplanprice = '$' . $myplan_price;
                            $plan_price_exp = 'high';
                        }
                        echo '<ul class="lp-cc-dashboard-user-activity-card-content-body ' . $plan_price_exp . '">
                            <li class="lp-cc-dashboard-user-activity-card-content-item">' . get_the_title() . '<p>New Listing By ' . get_the_author() . '</p></li>
                            <li class="lp-cc-dashboard-user-activity-card-content-item">' . get_the_date() . '<p>' . $print . '</p></li>
                            <li class="lp-cc-dashboard-user-activity-card-content-item">' . $myplanprice . '<p>' . $myplanname . '</p></li>
                            <li class="lp-cc-dashboard-user-activity-card-content-item">
                                <a href="' . get_edit_post_link(get_the_ID()) . '"><button class="lp-cc-pending-review"><i class="fa fa-eye"></i>view</button></a>
                                   <!-- 
                                   <select data-id="' . get_the_id() . '" class="lp-cc-dashboard-user-activity-card-content-action">
                                        <option>Actions</option>
                                        <option>Delete</option> 
                                        -->';
                        //                        global $post,$current_user;
                        //                        get_currentuserinfo();
                        //                        if ($post->post_author == $current_user->ID) {
                        //                            echo '<option>Edit</option>';
                        //                        }
                        echo '               <!--       </select>   --></li>
                    </ul>';
                    endwhile;
                        if ($the_query->found_posts > 5) {
                            echo '<div class="lp-cc-dashboard-user-activity-card-footer"><span class="footer_short_des"><i class="fa fa-info-circle"></i>Only ' . $the_query->found_posts . ' pending tasks in the queue are listed as RECENT FIRST .</span><a href="' . admin_url('edit.php?post_type=listing') . '"><button class="activity-card-footer-view_more">Show All <i class="fa fa-caret-right"></i></button></a></div>';
                        } else {
                        }
                    else :
                        echo '<div class="fzf_nothin_found">There Is No Pending Task...</div>';
                    endif;
                    ?>
                </div>
            </div>
        </div>
        <div class="lp-cc-dashboard-user-activity-payment lp-cc-dashboard-user-activity-card <?php echo $alt_class_card ?>">
            <div class="border"></div>
            <div class="lp-cc-dashboard-user-activity-card-header">
                <p>Payment Activity</p>
                <div class="lp-dashboard-custom-select-dropdown lp-dashboard-custom-select-dropdown-pay">
                    <img alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/ajax-load.gif'; ?>"
                         class="lp-cc-custom-preloader">
                    <span class="lp-dashboard-custom-select-dropdown-placeholder"><i class="fade">show:</i> Recent</span>
                    <ul>
                        <li data-value="listing-invoice">Listing Invoices</li>
                        <!-- <li data-value="listing-receipts">Listing Receipts</li> -->
                        <li data-value="ad-invoice">Ads Invoices</li>
                        <li data-value="pending">Pending Approval</li>
                    </ul>
                </div>
            </div>
            <div class="lp-cc-dashboard-user-activity-card-content">
                <div class="lp-cc-custom-preloader-white-space"></div>
                <div class="lp-cc-dashboard-user-activity-card-timeline">
                    <?php
                    $ads = $wpdb->prefix . 'listing_campaigns';
                    $listing = $wpdb->prefix . 'listing_orders';
                    $results = array();
                    $resultads = array();
                    $query = "";
                    $queryads = "";
                    if(lp_table_exists_check($listing)) {
                        $query = "SELECT * from $listing ORDER BY main_id DESC limit 3";
                    }
                    if(lp_table_exists_check($ads)) {
                        $queryads = "SELECT * from $ads ORDER BY main_id DESC limit 2";
                    }
                    $results = $wpdb->get_results($query);
                    $results_pOSt_Count = $wpdb->num_rows;
                    $resultads = $wpdb->get_results($queryads);
                    $resultads_pOSt_Count = $wpdb->num_rows;
                    if ($resultads_pOSt_Count == 1) {
                        if(lp_table_exists_check($listing)) {
                            $query = "SELECT * from $listing ORDER BY main_id DESC limit 4";
                        }
                        $results = $wpdb->get_results($query);
                    } elseif ($resultads_pOSt_Count == 0) {
                        if(lp_table_exists_check($listing)) {
                            $query = "SELECT * from $listing ORDER BY main_id DESC limit 5";
                        }
                        $results = $wpdb->get_results($query);
                    }
                    if ($results_pOSt_Count == 2) {
                        if(lp_table_exists_check($queryads)) {
                            $queryads = "SELECT * from $ads ORDER BY main_id DESC limit 3";
                        }
                        $resultads = $wpdb->get_results($queryads);
                    } elseif ($results_pOSt_Count == 1) {
                        if(lp_table_exists_check($ads)) {
                            $queryads = "SELECT * from $ads ORDER BY main_id DESC limit 4";
                        }
                        $resultads = $wpdb->get_results($queryads);
                    } elseif ($results_pOSt_Count == 0) {
                        if(lp_table_exists_check($ads)) {
                            $queryads = "SELECT * from $ads ORDER BY main_id DESC limit 5";
                        }
                        $resultads = $wpdb->get_results($queryads);
                    }
                    if(!is_array($results)) {
                        $results    =   array();
                    }
                    if(!is_array($resultads)) {
                        $resultads  =   array();
                    }
                    $merG_arr = array_merge($results, $resultads);
                    shuffle($merG_arr);
                    foreach ($merG_arr as $Index => $value) {
                        if (isset($value->summary)) {
                            $user_id = $value->user_id;
                            $inv_currency = $value->currency;
                            $inv_price = $value->price;
                            $inv_order_num = $value->order_id;
                            $inv_status = $value->status;
                            $inv_method = $value->payment_method;
                            $inv_customer = get_user_by('ID', $user_id);
                            $author_display_name = $inv_customer->display_name;
                            $today_str = strtotime("today");
                            $invoice_date = strtotime($value->date);
                            $diff = abs($today_str - $invoice_date);
                            $years_diff = floor($diff / (365 * 60 * 60 * 24));
                            $months_diff = floor(($diff - $years_diff * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                            $days_diff = floor(($diff - $years_diff * 365 * 60 * 60 * 24 - $months_diff * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                            $difference_text = '';
                            if ($days_diff != 0) {
                                $difference_text = $days_diff . ' days ago';
                            } else {
                                $difference_text = 'Today';
                            }
                            $bullet_class = '';
                            if ($inv_status == 'pending') {
                                $bullet_class = 'danger';
                            }
                            if ($inv_status == 'in progress') {
                                $bullet_class = 'inactive';
                            }
                            if ($inv_status != 'in progress') {
                                echo '
                                    <div class="lp-cc-dashboard-user-activity-card-timeline-container ' . $bullet_class . ' right">
                                        <div class="lp-cc-dashboard-user-activity-card-timeline-container-content">
                                            <p><i class="plan-tag">plan</i> ' . $author_display_name . ' <i class="paid-color"> Paid ' . $inv_currency . '-' . $inv_price . ' </i> For <a href="' . get_post_permalink($value->post_id) . '">' . get_the_title($value->post_id) . '</a></p>
                                            <span>' . $difference_text . '<i></i>Paid With <b>' . $inv_method . '</b><i></i>Invoice #' . $inv_order_num . '</span>
                                        </div>
                                    </div>
                                ';
                            } else {
                                echo '
                                <div class="lp-cc-dashboard-user-activity-card-timeline-container ' . $bullet_class . ' right">
                                    <div class="lp-cc-dashboard-user-activity-card-timeline-container-content">
                                        <p><i class="plan-tag">plan</i>Invoice #' . $inv_order_num . ' For <a href="' . get_post_permalink($value->post_id) . '">' . get_the_title($value->post_id) . '</a></p>
                                        <span>N/A<i></i>Paid With <b>N/A</b><i></i>Invoice #' . $inv_order_num . '</span>
                                    </div>
                                </div>
                            ';
                            }
                        } elseif (isset($value->mode)) {
                            $user_id = $value->user_id;
                            $inv_currency = $value->currency;
                            $inv_price = $value->price;
                            $post_id = $value->post_id;
                            $inv_order_num = $value->transaction_id;
                            $inv_status = $value->status;
                            $duration = $value->duration;
                            $inv_method = $value->payment_method;
                            $inv_customer = get_user_by('ID', $user_id);
                            $author_display_name = $inv_customer->display_name;
                            $today_str = strtotime("today");
                            $invoice_date = get_the_date('U', $post_id);
                            $diff = abs($today_str - $invoice_date);
                            $years_diff = floor($diff / (365 * 60 * 60 * 24));
                            $months_diff = floor(($diff - $years_diff * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                            $days_diff = floor(($diff - $years_diff * 365 * 60 * 60 * 24 - $months_diff * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                            $difference_text = '';
                            if ($days_diff != 0) {
                                $difference_text = $days_diff . ' days ago';
                            } else {
                                $difference_text = 'Today';
                            }
                            $bullet_class = '';
                            if ($inv_status == 'pending') {
                                $bullet_class = 'danger';
                            }
                            if ($inv_status == 'in progress') {
                                $bullet_class = 'inactive';
                            }
                            if ($inv_status != 'pending') {
                                $listing_id = listing_get_metabox_by_ID('ads_listing', $post_id);
                            } else {
                                $listing_id = $post_id;
                            }
                            if ($inv_status != 'pending') {
                                echo '
                                    <div class="lp-cc-dashboard-user-activity-card-timeline-container ' . $bullet_class . ' right">
                                        <div class="lp-cc-dashboard-user-activity-card-timeline-container-content">
                                            <p><i class="cusad-tag">ad</i> ' . $author_display_name . ' <i class="paid-color"> Paid ' . $inv_currency . '-' . $inv_price . ' </i> For <a href="' . get_post_permalink($listing_id) . '">' . get_the_title($listing_id) . '</a></p>
                                            <span>' . $difference_text . '<i></i>Paid With <b>' . $inv_method . '</b><i></i>Invoice #' . $inv_order_num . '</span>
                                        </div>
                                    </div>
                                ';
                            } else {
                                echo '
                                    <div class="lp-cc-dashboard-user-activity-card-timeline-container ' . $bullet_class . ' right">
                                        <div class="lp-cc-dashboard-user-activity-card-timeline-container-content">
                                            <p><i class="cusad-tag">ad</i> ' . $author_display_name . ' <i class="paid-color"> Paid ' . $inv_currency . '-' . $inv_price . ' </i> For <a href="' . get_post_permalink($listing_id) . '">' . get_the_title($listing_id) . '</a></p>
                                            <span>' . $difference_text . '<i></i>Paid With <b>' . $inv_method . '</b><i></i>Invoice #' . $inv_order_num . '</span>
                                        </div>
                                    </div>
                                ';
                            }
                        }
                    }
                    if(empty($merG_arr)) {
                        echo '<div class="fzf_nothin_found">There Is No Recent Payment Activity...</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="lp-cc-dashboard-directory-info <?php echo $alt_class_card ?>">
        <div class="border"></div>
        <div class="mini-card-container">
            <div class="mini-card-content">
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=location&post_type=listing'); ?>">
                    <p class="top">
                        <?php
                        $count = wp_count_terms('location');
                        echo $count;
                        ?>
                    </p>
                    <p class="bottom">listings LOCATIONS</p>
                </a>
            </div>

            <div class="mini-card-content">
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=listing-category&post_type=listing'); ?>">
                    <p class="top">
                        <?php
                        $count = wp_count_terms('listing-category');
                        echo $count;
                        ?>
                    </p>
                    <p class="bottom">listings Categories</p>
                </a>
            </div>

            <div class="mini-card-content">
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=list-tags&post_type=listing'); ?>">
                    <p class="top">
                        <?php
                        $count = wp_count_terms('list-tags');
                        echo $count;
                        ?>
                    </p>
                    <p class="bottom">listings tags</p>
                </a>
            </div>

            <div class="mini-card-content">
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=features&post_type=listing'); ?>">
                    <p class="top">
                        <?php
                        $count = wp_count_terms('features');
                        echo $count;
                        ?>
                    </p>
                    <p class="bottom">listings features</p>
                </a>
            </div>

            <div class="mini-card-content">
                <a href="<?php echo admin_url('edit.php?post_type=form-fields'); ?>">
                    <p class="top">
                        <?php
                        $count = wp_count_posts('form-fields')->publish;
                        echo $count;
                        ?>
                    </p>
                    <p class="bottom">listings form fields</p>
                </a>
            </div>

        </div>
    </div>

</div>