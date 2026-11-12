<?php
//command center code
if(!function_exists('lp_commandcenter_script')) {
    function lp_commandcenter_script(){
        wp_register_script( 'commandcenterjs', WP_PLUGIN_URL . '/listingpro-plugin/assets/js/command-center.js', array( 'jquery') );
        wp_enqueue_script( 'commandcenterjs' );
        wp_localize_script( 'commandcenterjs', 'cc_vars', array( 
            'cc_nonce' => wp_create_nonce( 'cc_nonce' ),
        ));
        wp_enqueue_style('commandcenterStyle', plugins_url('/listingpro-plugin/assets/css/command-center.css'));
    }
}

add_action( 'admin_enqueue_scripts', 'lp_commandcenter_script' );

// Command Center
if(!function_exists('lp_command_center_menu')) {
    function lp_command_center_menu(){

        $submit_form_builder_state  =   get_option( 'listing_submit_form_state' );
        $multi_rating_switch            =   lp_theme_option('lp_multirating_switch');

        add_menu_page('ListingPro CC', 'ListingPro CC', 'manage_options', 'listingpro', 'lp_command_center', '', 25);
        add_submenu_page( 'listingpro', 'Dashboard', 'Dashboard', 'manage_options', 'Dashboard', 'lp_command_center');
        add_submenu_page( 'listingpro', 'Add Ons', 'Add Ons', 'manage_options', 'lp-cc-addons', 'lp_cc_addons');
        add_submenu_page( 'listingpro', 'Visualizer', 'Visualizer', 'manage_options', 'lp-cc-visualizer', 'lp_cc_Visualizer');
        add_submenu_page( 'listingpro', 'License', 'License', 'manage_options', 'lp-cc-license', 'lp_cc_license');
        remove_submenu_page('listingpro','listingpro');
        if($submit_form_builder_state == '1') {
            add_submenu_page('listingpro', 'Submit Form', 'Submit Form', 'manage_options', 'lp-submit-form', 'listingpro_submit_form_page');
        } else {
            add_submenu_page('listingpro', 'Submit Form', 'Submit Form', 'manage_options', 'lp-cc-visualizer', 'lp_cc_Visualizer');
        }
        if($multi_rating_switch == 1) {
            add_submenu_page( 'listingpro', 'Multi Rating', 'Multi Rating', 'manage_options', 'reviews_settings', 'reviews_settings_cb');
        } else {
            add_submenu_page( 'listingpro', 'Multi Rating', 'Multi Rating', 'manage_options', 'lp-cc-visualizer', 'lp_cc_Visualizer');
        }
    }
}


add_action('admin_menu', 'lp_command_center_menu');
if(!function_exists('lp_command_center')) {
    function lp_command_center(){
        require_once 'dashboard.php';
    }
}

if(!function_exists('lp_cc_addons')) {
    function lp_cc_addons(){
        require_once 'addons.php';
    }
}

if(!function_exists('lp_cc_license')) {
    function lp_cc_license(){
        require_once 'license.php';
    }
}

if(!function_exists('lp_cc_Visualizer')) {
    function lp_cc_Visualizer(){
        require_once 'visualizer.php';
    }
}

if(!function_exists('lp_cc_addons_actions')) {
    function lp_cc_addons_actions() {

        $return =   array();
        if ( isset( $_POST['cc_nonce'] ) && wp_verify_nonce( $_POST['cc_nonce'], 'cc_nonce' ) ) {
            if( is_user_logged_in() && is_admin() && current_user_can('install_plugins') && isset($_REQUEST) ) {
                $ccAction   =   sanitize_text_field($_REQUEST['ccAction']);
                $ccDestin   =   sanitize_text_field($_REQUEST['ccDestin']);
                $ccFile     =   sanitize_text_field($_REQUEST['ccFile']);


            if($ccAction == 'activate') {

                $plugin =   $ccFile;
                $current = get_option( 'active_plugins' );
                $plugin = plugin_basename( trim( $plugin ) );

                if ( !in_array( $plugin, $current ) ) {
                    $current[] = $plugin;
                    sort( $current );
                    do_action( 'activate_plugin', trim( $plugin ) );
                    update_option( 'active_plugins', $current );
                    do_action( 'activate_' . trim( $plugin ) );
                    do_action( 'activated_plugin', trim( $plugin) );

                    $return['status']   =   'success';
                }

            }
            if($ccAction == 'deactivate') {
                deactivate_plugins( $ccFile );

                $return['status']   =   'success';
            }
            if($ccAction == 'install') {
                if( $ccDestin == 'own' ) {

                    $file_Arr       =   explode('/', $ccFile);
                    $file_zip       =   get_template_directory().'/include/plugins/'.$file_Arr['0'].'.zip';
                    $destin_path    =   ABSPATH . 'wp-content/plugins/'.$file_Arr['0'].'.zip';

                    if(!file_exists($destin_path)){

                        WP_Filesystem();

                        $unzipfile = unzip_file( $file_zip, ABSPATH . 'wp-content/plugins/');
                        if(!is_wp_error($unzipfile)){
                            $plugin =   $ccFile;

                            $current = get_option( 'active_plugins' );
                            $plugin = plugin_basename( trim( $plugin ) );

                            if ( !in_array( $plugin, $current ) ) {
                                $current[] = $plugin;
                                sort( $current );
                                do_action( 'activate_plugin', trim( $plugin ) );
                                update_option( 'active_plugins', $current );
                                do_action( 'activate_' . trim( $plugin ) );
                                do_action( 'activated_plugin', trim( $plugin) );

                                $return['status']   =   'success';
                            }
                        }

                        //copy($file_zip, $destin_path);
                    }
                    $return['ccDestin']     =   $file_zip;
                    $return['destin_path']     =   $destin_path;
                }

                if($ccDestin == 'external') {
                    $ccFileUrl     =   $_REQUEST['ccFileUrl'];

                    $file_Arr       =   explode('/', $ccFile);
                    $file_zip       =   $ccFileUrl;
                    $destin_path    =   ABSPATH . 'wp-content/plugins/'.$file_Arr['0'].'.zip';

                    if(file_exists($destin_path)) {
                        unlink($destin_path);
                    }
                    if(!file_exists($destin_path)){
                        if(copy($file_zip, $destin_path)){
                            WP_Filesystem();
                            $unzipfile = unzip_file( $destin_path, ABSPATH . 'wp-content/plugins/');
                            if(!is_wp_error($unzipfile)){
                                unlink($destin_path);
                                $plugin =   $ccFile;

                                $current = get_option( 'active_plugins' );
                                $plugin = plugin_basename( trim( $plugin ) );

                                if ( !in_array( $plugin, $current ) ) {
                                    $current[] = $plugin;
                                    sort( $current );
                                    do_action( 'activate_plugin', trim( $plugin ) );
                                    update_option( 'active_plugins', $current );
                                    do_action( 'activate_' . trim( $plugin ) );
                                    do_action( 'activated_plugin', trim( $plugin) );
                                }
                            }
                        }
                    }
                    $return['status']   =   'success';
                }
            }
            if($ccAction == 'update') {

                $ccFilee    =   $_REQUEST['ccFile'];
                $destin_path    =   ABSPATH . "wp-content/plugins/$ccFile.zip";
                $paht_file  =   CRIDIO_FILES_URL."/updates/$ccFilee.zip";

                if(copy($paht_file, $destin_path)){
                    WP_Filesystem();
                    $unzipfile = unzip_file( $destin_path, ABSPATH . 'wp-content/plugins/');

                    if(!is_wp_error($unzipfile)) {
                        unlink($destin_path);
                    }
                }
                $addons_upadates    =   get_option('lp_addons_updates');

                $base_path  =   ABSPATH . 'wp-content/plugins/';
                if($_REQUEST['ccFile'] == 'appointments-updates') {
                    $files_arr          =   $addons_upadates['appointment_files'];
                    $plugin_folder      =   'listingpro-bookings';
                    $plugin_folder_from =   'appointments-updates';
                }
                if($_REQUEST['ccFile'] == 'lead-form-updates') {
                    $files_arr          =   $addons_upadates['lead_form_files'];
                    $plugin_folder      =   'listingpro-lead-form';
                    $plugin_folder_from =   'lead-form-updates';
                }
                $return['files']    =   $files_arr;

                foreach ($files_arr as $file_path => $file_name) {
                    if(strpos($file_path, 'root')!== false) {
                        $destin_pathh       =   $base_path.$plugin_folder.'/'.$file_name;
                        $destin_pathh_from  =   $base_path.$plugin_folder_from.'/'.$file_name;

                    } else {
                        $path_arr   =   explode('/', $file_path);
                        array_pop($path_arr);
                        $new_file_path  =   implode('/', $path_arr);
                        $destin_pathh       =   $base_path.$plugin_folder.'/'.$new_file_path.'/'.$file_name;
                        $destin_pathh_from  =   $base_path.$plugin_folder_from.'/'.$file_name;
                    }

                    $return[$destin_pathh]  =   $destin_pathh_from;
                    copy($destin_pathh_from, $destin_pathh);

                }

                lp_delete_addons_updates_temp($base_path.$plugin_folder_from);

                if($ccFilee == 'appointments-updates') {
                    $plugin_val =   'listingpro-bookings/listingpro-bookings.php';
                }
                if($ccFilee == 'lead-form-updates') {
                    $plugin_val =   'listingpro-lead-form/plugin.php';
                }
                $key            =   array_search($plugin_val, $addons_upadates['available_updates']);

                unset($addons_upadates['available_updates'][$key]);
                update_option('lp_addons_updates', $addons_upadates);
                $return['status']       =   'success';
            }
            }
        }
        die(json_encode($return));
    }
}

add_action( 'wp_ajax_lp_cc_addons_actions', 'lp_cc_addons_actions' );


function lp_delete_addons_updates_temp($temp_path) {
    if (is_dir($temp_path))
        $dir_handle = opendir($temp_path);
    if (!$dir_handle)
        return false;
    while($file = readdir($dir_handle)) {
        if ($file != "." && $file != "..") {
            unlink($temp_path."/".$file);
        }
    }
    closedir($dir_handle);
    rmdir($temp_path);
    return true;
}


if(!function_exists('lp_cc_pending_ajax_request')) {
    function lp_cc_pending_ajax_request() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Unauthorized', 'listingpro-plugin'));
        }
        if (empty($_REQUEST['cc_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['cc_nonce'])), 'cc_nonce')) {
            wp_die(esc_html__('Security check failed', 'listingpro-plugin'));
        }

        // The $_REQUEST contains all the data sent via ajax
        if ( isset($_REQUEST) ) {

            $selectedoption = $_REQUEST['selectedoptionr'];

            // Let's take the data that was sent and do something with it



            if ( $selectedoption == 'Claim Request') {

                ?>
                <div class="lp-cc-dashboard-user-activity-card-body claim-request">
                    <ul class="lp-cc-dashboard-user-activity-card-content-head">
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Item</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Date</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Price</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Status</li>
                    </ul>
                    <?php
                    $args = array(
                        'post_type'=> 'lp-claims',
                        'orderby'  => 'date',
                        'order'    => 'DESC',
                        'posts_per_page'    => 5,
                    );
                    $the_query = new WP_Query( $args );
                    $claime_Post_Counter = 0;
                    if($the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post();
                        $post_status = listing_get_metabox_by_ID('claim_status', get_the_ID());
                        if ($post_status == 'pending'){
                            $claime_Post_Counter++;

                            $myarray = listing_get_metabox_by_ID('claim_plan', get_the_ID());
                            $myplan = get_post_meta($myarray, 'plan_price', true);
                            if ($myplan <= 0) {
                                $myplanname = 'Free Claim';
                                $myplanprice = 'N/A';
                                $plan_price_exp = 'low';
                            }else{
                                $myplanname = 'Paid Claim';
                                $myplanprice = '$'.$myplan;
                                $plan_price_exp = 'high';
                            }
//

                            $now = time();
                            $your_date = strtotime(get_the_date());
                            $datediff = $now - $your_date;
                            $days = round($datediff / (60 * 60 * 24));
                            $month_remaining= intval($days / 30);
                            $years_remaining = intval($days / 365);
                            $print = 'In '.$days.' Days';
                            if ($days > 30) {
                                $print = 'In '.$month_remaining.' Month';
                            }if ($month_remaining > 11) {
                                $print = 'In '.$years_remaining.' Year';
                            }

// Getting Claim Status
                            $cfname = listing_get_metabox_by_ID('claimer_fname', get_the_ID());
                            $clname = listing_get_metabox_by_ID('claimer_lname', get_the_ID());

                            $claimername = $cfname.' '.$clname;
                            $claimstatus =  listing_get_metabox_by_ID('claim_status', get_the_ID());
                            $name = listing_get_metabox_by_ID('claimed_listing', get_the_ID());



//                        if ($claimstatus == "pending") {
//                            $optionformate = ' <!--<option>Pending</option><option>Approved</option>';
//                        }elseif ($claimstatus == "approved") {
//                            $optionformate = '<option>Approved</option><option>Pending</option>-->';
//                        }


                            echo '<ul class="lp-cc-dashboard-user-activity-card-content-body ' . $plan_price_exp . '">
    <li class="lp-cc-dashboard-user-activity-card-content-item">Claim For '.get_the_title($name).'<p>Listing Claimed By '.$claimername.'</p></li>
    <li class="lp-cc-dashboard-user-activity-card-content-item">'.get_the_date().'<p>'.$print.'</p></li>
    <li class="lp-cc-dashboard-user-activity-card-content-item">' . $myplanprice . '<p>' . $myplanname . '</p></li>
     <li class="lp-cc-dashboard-user-activity-card-content-item">

              <a href="'.get_edit_post_link(get_the_ID()).'"><button class="lp-cc-pending-review"><i class="fa fa-eye"></i>view</button></a>
       <!--
       <select data-id="'.get_the_id().'" class="lp-cc-dashboard-user-activity-card-content-action">
            <option>Actions</option>
            <option>Delete</option>
            -->';
//                        global $post,$current_user;
//                        get_currentuserinfo();
//                        if ($post->post_author == $current_user->ID) {
//                            echo '<option>Edit</option>';
//                        }
                            echo '
<!--       </select>   -->

    </li>
</ul>';
                        }
                    endwhile;
                    endif;
                    if ($claime_Post_Counter == 0){
                        echo '<div class="fzf_nothin_found">There Is No Pending Task...</div>';
                    }
                    if ($claime_Post_Counter > 5){
                        echo '<div class="lp-cc-dashboard-user-activity-card-footer"><span class="footer_short_des"><i class="fa fa-info-circle"></i>Only '.$the_query->found_posts.' pending tasks in the queue are listed in chronological order (oldest first) .</span><a href="'.admin_url('edit.php?post_type=lp-claims').'"><button class="activity-card-footer-view_more">Show All <i class="fa fa-caret-right"></i></button></a></div>';
                    }
                    ?>
                </div>

                <?php

            }
            if ( $selectedoption == 'New Listing') {

                ?>

                <div class="lp-cc-dashboard-user-activity-card-body new-listings">
                    <ul class="lp-cc-dashboard-user-activity-card-content-head">
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Item</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Date</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Price</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Status</li>
                    </ul>
                    <?php
                    $args = array(
                        'post_type'=> 'listing',
                        'orderby'  => 'date',
                        'order'    => 'DESC',
                        'posts_per_page'    => 5,
                        'post_status' => 'pending',
                    );
                    $the_query = new WP_Query( $args );
                    if($the_query->have_posts() ) :	while ( $the_query->have_posts() ) : $the_query->the_post();
                        // For Ago Days Passes
                        $now = time();
                        $your_date = strtotime(get_the_date());
                        $datediff = $now - $your_date;
                        $days = round($datediff / (60 * 60 * 24));
                        $month_remaining= intval($days / 30);
                        $years_remaining = intval($days / 365);
                        $print = 'In '.$days.' Days';
                        if ($days > 30) {
                            $print = 'In '.$month_remaining.' Month';
                        }if ($month_remaining > 11) {
                            $print = 'In '.$years_remaining.' Year';
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
                        echo '
<!--       </select>   -->
        
    </li>
</ul>';
                    endwhile;
                        if ($the_query->found_posts > 5){
                            echo '<div class="lp-cc-dashboard-user-activity-card-footer"><span class="footer_short_des"><i class="fa fa-info-circle"></i>Only '.$the_query->found_posts.' pending tasks in the queue are listed in chronological order (oldest first) .</span><a href="'.admin_url('edit.php?post_type=listing').'"><button class="activity-card-footer-view_more">Show All <i class="fa fa-caret-right"></i></button></a></div>';
                        }else{}
                    else :
                        echo '<div class="fzf_nothin_found">There Is No Pending Task...</div>';
                    endif;
                    ?>
                </div>


                <?php

            }
            if ( $selectedoption == 'Flagged Review') {

                ?>
                <div class="lp-cc-dashboard-user-activity-card-body reported-reviews">
                    <ul class="lp-cc-dashboard-user-activity-card-content-head">
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Item</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Count</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Action</li>
                    </ul>
                    <?php
                    $reportedLisings = get_option( 'lp_reported_reviews' );
                    $paged  =   '';
                    $ReportedLisints = array();
                    if( strpos($reportedLisings, ',') !== false ){
                        $ReportedLisints = explode(",",$reportedLisings);
                    }else{
                        $ReportedLisints[] = $reportedLisings;

                    }
                    $reports_query = new WP_Query(
                        array(
                            'post_type' => 'lp-reviews',
                            'post__in' => $ReportedLisints,
                            'post_status' => 'publish',
                            'order' =>  'DESC',
                            'posts_per_page' => 5,
                        )
                    );

                    if($reports_query->have_posts() ) : while (  $reports_query->have_posts() ) :  $reports_query->the_post();
                        $reportedCount = listing_get_metabox_by_ID('review_reported', get_the_ID());
                        echo '<ul class="lp-cc-dashboard-user-activity-card-content-body">
    <li class="lp-cc-dashboard-user-activity-card-content-item">'.get_the_title().'<p>'.get_the_author().'</p></li>
    <li class="lp-cc-dashboard-user-activity-card-content-item">'.$reportedCount.' Time<p>Reported</p></li>
    <li class="lp-cc-dashboard-user-activity-card-content-item">
           <a href="'.admin_url('admin.php?page=lp-review-flags').'"><button class="lp-cc-pending-review"><i class="fa fa-eye"></i>view</button></a>
        <!--<form class="wp-core-ui" method="post">
            <input type="submit" name="lp_review_report_submit" class="button action" value="'.esc_html__('Confirm', 'listingpro-plugin').'">
            <input type="hidden" name="review_id" value="'.get_the_ID().'">
        </form>-->
    </li>
</ul>';

                    endwhile;
                        if ($reports_query->found_posts > 5){
                            echo '<div class="lp-cc-dashboard-user-activity-card-footer"><span class="footer_short_des"><i class="fa fa-info-circle"></i>Only '.$reports_query->found_posts.' pending tasks in the queue are listed in chronological order (oldest first) .</span><a href="'.admin_url('admin.php?page=lp-review-flags').'"><button class="activity-card-footer-view_more">Show All <i class="fa fa-caret-right"></i></button></a></div>';
                        }else{}
                    else :
                        echo '<div class="fzf_nothin_found">There Is No Pending Task...</div>';
                    endif;
                    ?>
                </div>

                <?php

            }
            if ( $selectedoption == 'Flagged Listing') {


                ?>
                <div class="lp-cc-dashboard-user-activity-card-body reported-listing">
                    <ul class="lp-cc-dashboard-user-activity-card-content-head">
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Item</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Count</li>
                        <li class="lp-cc-dashboard-user-activity-card-content-item">Action</li>
                    </ul>
                    <?php
                    $reportedLisings = get_option('lp_reported_listings');
                    $ReportedLisints = array();
                    if (strpos($reportedLisings, ',') !== false)
                    {
                        $ReportedLisints = explode(",", $reportedLisings);
                    }
                    else
                    {
                        $ReportedLisints[] = $reportedLisings;
                    }
                    $reports_query = new WP_Query(array(
                        'post_type' => 'listing',
                        'post__in' => $ReportedLisints,
                        'post_status' => 'publish',
                        'order' =>  'DESC',
                        'posts_per_page' => 5,
                    ));
                    if($reports_query->have_posts() ) : while (  $reports_query->have_posts() ) :  $reports_query->the_post();
                        $reportedCount = listing_get_metabox_by_ID('listing_reported', get_the_ID());
                        if(!empty($reportedCount)){
                            if($reportedCount > 1){
                                $reportedCount .=' '.esc_html__('Times', 'listingpro-plugin');
                            }else{
                                $reportedCount .=' '.esc_html__('Time', 'listingpro-plugin');
                            }
                        }else{}
                        echo '<ul class="lp-cc-dashboard-user-activity-card-content-body">
    <li class="lp-cc-dashboard-user-activity-card-content-item">'.get_the_title().'<p>'.get_the_author().'</p></li>
    <li class="lp-cc-dashboard-user-activity-card-content-item">'.$reportedCount.'<p>Reported</p></li>
    <li class="lp-cc-dashboard-user-activity-card-content-item">
        <!--<form class="wp-core-ui" method="post">
            <input type="submit" name="lp_listing_report_submit" class="button action" value=" '.esc_html__("Confirm", "listingpro-plugin").'">
            <input type="hidden" name="listing_id" value=" '.get_the_ID().' ">
        </form>-->
               <a href="'.admin_url('admin.php?page=lp-flags').'"><button class="lp-cc-pending-review"><i class="fa fa-eye"></i>view</button></a>
    </li>
</ul>';

                    endwhile;
                        if ($reports_query->found_posts > 5){
                            echo '<div class="lp-cc-dashboard-user-activity-card-footer"><span class="footer_short_des"><i class="fa fa-info-circle"></i>Only '.$reports_query->found_posts.' pending tasks in the queue are listed in chronological order (oldest first) .</span><a href="'.admin_url('admin.php?page=lp-flags').'"><button class="activity-card-footer-view_more">Show All <i class="fa fa-caret-right"></i></button></a></div>';
                        }else{}
                    else :
                        echo '<div class="fzf_nothin_found">There Is No Pending Task...</div>';
                    endif;
                    ?>
                </div>

                <?php


            }



        }


        die();
    }
}


add_action( 'wp_ajax_lp_cc_pending_ajax_request', 'lp_cc_pending_ajax_request' );


if(!function_exists('lp_cc_pay_ajax_request')) {
    function lp_cc_pay_ajax_request() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Unauthorized', 'listingpro-plugin'));
        }
        if (empty($_REQUEST['cc_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['cc_nonce'])), 'cc_nonce')) {
            wp_die(esc_html__('Security check failed', 'listingpro-plugin'));
        }

        if ( isset($_REQUEST) ) {

            $selectedoption = $_REQUEST['selectedoptionrpay'];

            global $wpdb;

            if ($selectedoption == 'listing-invoice') {

                ?><div class="lp-cc-dashboard-user-activity-card-timeline">
                <?php

                $table = "listing_orders";
                $table = $wpdb->prefix.$table;
                $results = array();
                $query = "";
                if(lp_table_exists_check($table)) {
                    $query = "SELECT * from $table ORDER BY main_id DESC LIMIT 5";
                    $results = $wpdb->get_results( $query);
                }

                foreach($results as $Index=>$value){
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
                    }else{
                        echo '
            <div class="lp-cc-dashboard-user-activity-card-timeline-container ' . $bullet_class . ' right">
                <div class="lp-cc-dashboard-user-activity-card-timeline-container-content">
                    <p><i class="plan-tag">plan</i>Invoice #' . $inv_order_num . ' For <a href="' . get_post_permalink($value->post_id) . '">' . get_the_title($value->post_id) . '</a></p>
                    <span>N/A<i></i>Paid With <b>N/A</b><i></i>Invoice #' . $inv_order_num . '</span>
                </div>
            </div>
        ';
                    }
                }
                if (empty($results)) {
                    echo '<div class="fzf_nothin_found">There Is No Recent Payment Activity...</div>';
                }

                ?>
                </div><?php
            }
            if ($selectedoption == 'ad-invoice') {

                ?> <div class="lp-cc-dashboard-user-activity-card-timeline">
                <?php
                $adstable = "listing_campaigns";
                $adstable = $wpdb->prefix.$adstable;
                $adsresults = array();
                $query = "";
                if(lp_table_exists_check($adstable)) {
                    $query = "SELECT * from $adstable ORDER BY main_id DESC limit 5";
                    $adsresults = $wpdb->get_results( $query);
                }
                foreach($adsresults as $Index=>$value){
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
                        $listing_id = listing_get_metabox_by_ID( 'ads_listing' , $post_id );
                    }else{
                        $listing_id = $post_id;
                    }
                    if ($inv_status != 'pending') {
                        echo '
                            <div class="lp-cc-dashboard-user-activity-card-timeline-container ' . $bullet_class . ' right">
                                <div class="lp-cc-dashboard-user-activity-card-timeline-container-content">
                                    <p><i class="cusad-tag">ad</i> ' . $author_display_name . ' <i class="paid-color"> Paid ' . $inv_currency . '-' . $inv_price . ' </i> For <a href="'. get_post_permalink($listing_id) .'">'.get_the_title($listing_id).'</a></p>
                                    <span>' . $difference_text . '<i></i>Paid With <b>' . $inv_method . '</b><i></i>Invoice #' . $inv_order_num . '</span>
                                </div>
                            </div>
                        ';
                    }else{
                        echo '
                            <div class="lp-cc-dashboard-user-activity-card-timeline-container ' . $bullet_class . ' right">
                                <div class="lp-cc-dashboard-user-activity-card-timeline-container-content">
                                    <p><i class="cusad-tag">ad</i> ' . $author_display_name . ' <i class="paid-color"> Paid ' . $inv_currency . '-' . $inv_price . ' </i> For <a href="'. get_post_permalink($listing_id) .'">'.get_the_title($listing_id).'</a></p>
                                    <span>' . $difference_text . '<i></i>Paid With <b>' . $inv_method . '</b><i></i>Invoice #' . $inv_order_num . '</span>
                                </div>
                            </div>
                        ';
                    }
                }
                if (empty($adsresults)) {
                    echo '<div class="fzf_nothin_found">There Is No Recent Payment Activity...</div>';
                }

                ?>
                </div><?php

            }
            if ($selectedoption == 'pending') {

                ?>                <div class="lp-cc-dashboard-user-activity-card-timeline" >
                <?php

                $ads = $wpdb->prefix . 'listing_campaigns';
                $listing = $wpdb->prefix . 'listing_orders';
                $results = array();
                $resultads = array();
                $query = "";
                $queryads = "";

                if(lp_table_exists_check($listing)) {
                    $query = "SELECT * from $listing WHERE `status`='pending' ORDER BY main_id DESC limit 3";
                    $results = $wpdb->get_results($query);
                }

                if(lp_table_exists_check($ads)) {
                    $queryads = "SELECT * from $ads WHERE `status`='pending' ORDER BY main_id DESC limit 2";
                    $resultads = $wpdb->get_results($queryads);
                }

                $results_pOSt_Count = $wpdb->num_rows;
                $resultads_pOSt_Count = $wpdb->num_rows;

                if ($resultads_pOSt_Count == 1){
                    if(lp_table_exists_check($listing)) {
                        $query = "SELECT * from $listing limit 4";
                        $results = $wpdb->get_results($query);
                    }

                }elseif ($resultads_pOSt_Count == 0){
                    if(lp_table_exists_check($listing)) {
                        $query = "SELECT * from $listing limit 5";
                        $results = $wpdb->get_results($query);
                    }
                }

                if ($results_pOSt_Count == 2){
                    if(lp_table_exists_check($ads)) {
                        $queryads = "SELECT * from $ads limit 3";
                        $resultads = $wpdb->get_results($queryads);
                    }
                }elseif ($results_pOSt_Count == 1){
                    if(lp_table_exists_check($ads)) {
                        $queryads = "SELECT * from $ads limit 4";
                        $resultads = $wpdb->get_results($queryads);
                    }
                }elseif ($results_pOSt_Count == 0){
                    if(lp_table_exists_check($ads)) {
                        $queryads = "SELECT * from $ads limit 5";
                        $resultads = $wpdb->get_results($queryads);
                    }
                }

                $merG_arr = array_merge($results, $resultads);
                shuffle($merG_arr);



                foreach($merG_arr as $Index=>$value){
                    if (isset($value->summary)){
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
                        if ($inv_status == 'pending') {
                            echo '
            <div class="lp-cc-dashboard-user-activity-card-timeline-container ' . $bullet_class . ' right">
                <div class="lp-cc-dashboard-user-activity-card-timeline-container-content">
                    <p><i class="plan-tag">plan</i> ' . $author_display_name . ' <i class="paid-color"> Paid ' . $inv_currency . '-' . $inv_price . ' </i> For <a href="' . get_post_permalink($value->post_id) . '">' . get_the_title($value->post_id) . '</a></p>
                    <span>' . $difference_text . '<i></i>Paid With <b>' . $inv_method . '</b><i></i>Invoice #' . $inv_order_num . '</span>
                </div>
            </div>
        ';
                        }
                    }
                    elseif (isset($value->mode)){
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
                            $listing_id = listing_get_metabox_by_ID( 'ads_listing' , $post_id );
                        }else{
                            $listing_id = $post_id;
                        }

                        if ($inv_status == 'pending') {
                            echo '
                            <div class="lp-cc-dashboard-user-activity-card-timeline-container ' . $bullet_class . ' right">
                                <div class="lp-cc-dashboard-user-activity-card-timeline-container-content">
                                    <p><i class="cusad-tag">ad</i> ' . $author_display_name . ' <i class="paid-color"> Paid ' . $inv_currency . '-' . $inv_price . ' </i> For <a href="'. get_post_permalink($listing_id) .'">'.get_the_title($listing_id).'</a></p>
                                    <span>Paid With <b>' . $inv_method . '</b><i></i>Invoice #' . $inv_order_num . '</span>
                                </div>
                            </div>
                        ';
                        }
                        if (empty($resultads) && empty($results)) {
                            echo '<div class="fzf_nothin_found">There Is No Recent Payment Activity...</div>';
                        }

                    }
                }

                ?>
                </div><?php

            }

        }
        die();
    }
}


add_action( 'wp_ajax_lp_cc_pay_ajax_request', 'lp_cc_pay_ajax_request' );


add_action('wp_ajax_enable_form_builder', 'enable_form_builder');
if(!function_exists('enable_form_builder')) {
    function enable_form_builder()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Unauthorized', 'listingpro-plugin'));
        }
        if (empty($_REQUEST['cc_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['cc_nonce'])), 'cc_nonce')) {
            wp_die(esc_html__('Security check failed', 'listingpro-plugin'));
        }
        if (is_user_logged_in() && defined('DOING_AJAX') && DOING_AJAX) {
            $enable_data = intval($_POST['enable_data']);
            update_option('listing_submit_form_state', $enable_data);
            if (class_exists('Redux') && $enable_data == 1) {
                global $opt_name;
                $opt_name = 'listingpro_options';
                Redux::set_option($opt_name, 'listing_submit_page_style', 'style2');
            }
        }
    }
}

add_action('wp_ajax_enable_multi_rating', 'enable_multi_rating');
if(!function_exists('enable_multi_rating')) {
    function enable_multi_rating()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Unauthorized', 'listingpro-plugin'));
        }
        if (empty($_REQUEST['cc_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['cc_nonce'])), 'cc_nonce')) {
            wp_die(esc_html__('Security check failed', 'listingpro-plugin'));
        }
        if (is_user_logged_in() && defined('DOING_AJAX') && DOING_AJAX) {
            $enable_data = intval($_POST['enable_data']);
            update_option('lp_multirating_switch', $enable_data);
            if (class_exists('Redux')) {
                global $opt_name;
                $opt_name = 'listingpro_options';
                Redux::set_option($opt_name, 'lp_multirating_switch', $enable_data);
            }
        }
    }
}
add_action('wp_ajax_enable_lead_form', 'enable_lead_form');
if(!function_exists('enable_lead_form')) {
    function enable_lead_form()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Unauthorized', 'listingpro-plugin'));
        }
        if (empty($_REQUEST['cc_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['cc_nonce'])), 'cc_nonce')) {
            wp_die(esc_html__('Security check failed', 'listingpro-plugin'));
        }
        if (is_user_logged_in() && defined('DOING_AJAX') && DOING_AJAX) {
            $enable_data = intval($_POST['enable_data']);
            if($enable_data == 1) {
                $enable_data = 'yes';
            }
            update_option('lead-form-active', $enable_data);
        }
    }
}


if(!function_exists('lp_table_exists_check')) {
    function lp_table_exists_check($table_name) {
        global  $wpdb;

        $return =   true;
        $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
        $table_check    =   $wpdb->get_results($query);
        if(!count( $table_check )) {
            $return     =   false;
        }

        return $return;
    }
}