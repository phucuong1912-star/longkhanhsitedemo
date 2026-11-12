<?php
/* ================================save data via paypal====================================== */
if(!function_exists('lp_save_campaign_data')){
    function lp_save_campaign_data($price_packages, $transactionID, $method, $token, $status,$price, $budget, $listing_id, $adsofType, $ads_days, $ads_price, $taxPrice, $user_id = 0){
        global $wpdb,$listingpro_options;

        $dbprefix = $wpdb->prefix;
		$start = 11111111;
		$end = 999999999;
		$ord_num = random_int($start, $end);
		if(lp_theme_option('listingpro_invoice_start_switch')=="yes"){
			$ord_num = lp_theme_option('listingpro_invoiceno_no_start_ad');
			$token = $ord_num;
			$ord_num++;
			if (  class_exists( 'Redux' ) ) {
				$opt_name = 'listingpro_options';
				Redux::set_option( $opt_name, 'listingpro_invoiceno_no_start_ad', "$ord_num");
			}
		}
		
        //new code 2.6.15
        if(empty($price)){
            $price = '';
        }
        if(empty($budget)){
            $budget = '';
        }
        if(empty($listing_id)){
            $listing_id = '';
        }
        //end new code 2.6.15

        if(isset($user_id) && $user_id > 0){
            $user_ID = $user_id;
        }else{
            $user_ID = get_current_user_id();
        }
        $currency_code = '';
        $currency_code = lp_theme_option('currency_paid_submission');
        $priceKeyArray = 0;
        $packagesDetails ='';

        $expiryDate = '';
        $currentdate = date("d-m-Y");
        $expiryDate = date("d-m-Y", strtotime(' + '.$ads_days.' days'));

        $ad_title = lp_theme_option('lp_pro_title_offer');
        if(empty($ad_title)){
            $ad_title = esc_html__('Black Friday 50% Off', 'listingpro');
        }
        $my_post = array(
            'post_title'    => $ad_title,
            'post_status'   => 'publish',
            'post_type' => 'lp-ads',
        );
        $adID = wp_insert_post( $my_post );
        
        if($adsofType == "perclick"){
            $budgetwithTax = $budget;
            $budget        = (int)$budget - (int)$taxPrice;
            listing_set_metabox('budgetwithtax', $budgetwithTax, $adID);
        }
        
        listing_set_metabox('ads_listing', $listing_id, $adID);
        listing_set_metabox('ads_mode', $adsofType, $adID);

        if($adsofType=="perclick"){
            listing_set_metabox('remaining_balance', $budget, $adID);
        }else{
            update_post_meta($adID,'campain_expire_date', $expiryDate);
        }

        listing_set_metabox('duration', $ads_days, $adID);
        listing_set_metabox('budget', $budget, $adID);


        listing_set_metabox('ad_status', 'Active', $adID);
        listing_set_metabox('campaign_id', $adID, $listing_id);
        update_post_meta( $listing_id, 'campaign_status', 'active' );

        $priceKeyArray = array();
        if( !empty($price_packages) ){
            foreach( $price_packages as $val ){
                $priceKeyArray[] = $val;
                update_post_meta( $listing_id, $val, 'active' );
            }
        }

        if( !empty($priceKeyArray) ){

            listing_set_metabox('ad_type', $priceKeyArray, $adID);
        }


    if(!empty($price_packages)){
        //$price_packages = $_SESSION['price_package'];
        if(empty($budget)){
            if( !empty($price_packages) && is_array($price_packages) ){
                $sepVar = ' and ';
                if( count($price_packages) > 2 ){
                    $sepVar = ' , ';
                }
				$counter = 1;
                foreach( $price_packages as $val ){
					if( $counter == count($price_packages) ){
						$sepVar = '';
					}
                    if($val=="lp_random_ads"){
                        $packagesDetails .= esc_html__('Random Ads', 'listingpro');
                        $packagesDetails .= $sepVar;
                    }
                    if($val=="lp_detail_page_ads"){
                        $packagesDetails .= esc_html__('Detail Page Ads', 'listingpro');
                        $packagesDetails .= $sepVar;
                    }
                    if($val=="lp_top_in_search_page_ads"){
                        $packagesDetails .= esc_html__('Top in Search Page Ads', 'listingpro');
                        $packagesDetails .= $sepVar;
                    }

                    update_post_meta( $listing_id, $val, 'active' );
					$counter++;
                }
            }
        }
        else{
            if( !empty($price_packages) && is_array($price_packages) ){
                $sepVar = ' and ';
                if( count($price_packages) > 2 ){
                    $sepVar = ' , ';
                }
				$counter = 1;
                foreach( $price_packages as $val ){
					if( $counter == count($price_packages) ){
						$sepVar = '';
					}
                    if($val=="lp_random_ads"){
                        $packagesDetails .= esc_html__('Random Ads', 'listingpro');
                        $packagesDetails .= $sepVar;
                    }
                    if($val=="lp_detail_page_ads"){
                        $packagesDetails .= esc_html__('Detail Page Ads', 'listingpro');
                        $packagesDetails .= $sepVar;
                    }
                    if($val=="lp_top_in_search_page_ads"){
                        $packagesDetails .= esc_html__('Top in Search Page Ads', 'listingpro');
                        $packagesDetails .= $sepVar;
                    }
                    update_post_meta( $listing_id, $val, 'active' );
					$counter++;
                }
            }
        }
        }

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        lp_create_campaign_table();

        $insert_sql = array(
            'user_id' => $user_ID,
            'post_id' => $adID,
            'payment_method' => $method,
            'token' => $token,
            'price' => $ads_price,
            'currency' => $currency_code,
            'status' => $status,
            'transaction_id' => $transactionID,
            'mode' => $adsofType,
            'duration' => $ads_days,
            'budget' => $budget,
            'ad_date' => $currentdate,
            'ad_expiryDate' => $expiryDate,
            'tax' => $taxPrice
        );
        $table = 'listing_campaigns';
        if($method=="wire"){
            //for wire

            $data = array('post_id' => $adID,'status' => 'success');
            $where = array('transaction_id' => $transactionID);
            lp_update_data_in_db($table, $data, $where);

        }else{
            lp_insert_data_in_db($table, $insert_sql);
        }


        $current_user = wp_get_current_user();
        $user_email = $current_user->user_email;
        $admin_email = get_option('admin_email');
        $listing_title = get_the_title($listing_id);
        $listing_url = get_the_permalink($listing_id);
        $campaign_packages = $packagesDetails;

        $author_name = $current_user->user_login;
        $website_url = site_url();
        $website_name = get_option('blogname');
        $user_name = $author_name;
        /* for admin */
        $subject = lp_theme_option('listingpro_subject_campaign_activate');
        $mail_content = lp_theme_option('listingpro_content_campaign_activate');

        $formated_mail_content = lp_sprintf2("$mail_content", array(
            'campaign_packages' => "$campaign_packages",
            'listing_title' => "$listing_title",
            'listing_url' => "$listing_url",
            'website_url' => "$website_url",
            'website_name' => "$website_name",
            'user_name' => "$user_name",
            'author_name' => "$author_name"
        ));
        lp_mail_headers_append();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        LP_send_mail( $admin_email, $subject, $formated_mail_content, $headers);
        lp_mail_headers_remove();

        /* for author */

        $subject = lp_theme_option('listingpro_subject_campaign_activate_author');
        $mail_content = lp_theme_option('listingpro_content_campaign_activate_author');

        $formated_mail_content = lp_sprintf2("$mail_content", array(
            'campaign_packages' => "$campaign_packages",
            'listing_title' => "$listing_title",
            'listing_url' => "$listing_url",
            'website_url' => "$website_url",
            'website_name' => "$website_name",
            'user_name' => "$user_name",
        ));
        lp_mail_headers_append();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        LP_send_mail( $user_email, $subject, $formated_mail_content, $headers);
        lp_mail_headers_remove();

    }
}

/* =============================listingpro create campaign table=========================== */
if(!function_exists('lp_create_campaign_table')){
    function lp_create_campaign_table(){
        global $wpdb,$listingpro_options;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql="
           CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."listing_campaigns`
         (
              main_id bigint(20) NOT NULL auto_increment,
              user_id varchar(255) default NULL,
              post_id varchar(255) default NULL,
              payment_method varchar(255) default NULL,
              token varchar(255) default NULL,
              price varchar(255) default NULL,
              currency varchar(255) default NULL,
              status varchar(255) default NULL,
              transaction_id varchar(255) default NULL,
              PRIMARY KEY  (`main_id`),
              mode varchar(255) default NULL,
              duration varchar(255) default NULL,
              budget varchar(255) default NULL,
              ad_date varchar(255) default NULL,
              ad_expiryDate varchar(255) default NULL,
              tax varchar(255) default NULL

         );";
        dbDelta($sql);
    }
}

/* =========================listingpro insert data in db============================================== */

if(!function_exists('lp_insert_data_in_db')){
    function lp_insert_data_in_db($table, $dataArray){
        global $wpdb,$listingpro_options;
        $dbprefix = $wpdb->prefix;
        $table =$dbprefix.$table;
        $result = $wpdb->insert( $table, $dataArray, $format = null );

        if(!empty($result) && $result > 0){
            return true;
        }
        else{
            return false;
        }

    }
}

/* ===========================================listingpro delete data in db============================================== */

if(!function_exists('lp_delete_data_in_db')){
    function lp_delete_data_in_db($table, $where){
        global $wpdb,$listingpro_options;
        $dbprefix = $wpdb->prefix;
        $table =$dbprefix.$table;
        $result = $wpdb->delete( $table, $where, $where_format = null );

        if(!empty($result) && $result > 0){
            return true;
        }
        else{
            return false;
        }

    }
}

/* ============================================listingpro update data in db========================================= */

if(!function_exists('lp_update_data_in_db')){
    function lp_update_data_in_db($table, $data, $where){
        global $wpdb,$listingpro_options;
        $dbprefix = $wpdb->prefix;
        $table =$dbprefix.$table;

        $result = $wpdb->update( $table, $data, $where, $format = null, $where_format = null );
        if(!empty($result) && $result > 0){
            return true;
        }
        else{
            return false;
        }
    }
}

/* ============================================listingpro get data from db table ================================= */

if(!function_exists('lp_get_data_from_db')){
    function lp_get_data_from_db($table, $data, $condition){
        global $wpdb,$listingpro_options;

        $dbprefix = $wpdb->prefix;

        $table =$dbprefix.$table;
        if($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
            $query = "";
            $query = "SELECT $data from $table WHERE $condition ORDER BY main_id DESC";
            $result = $wpdb->get_results( $query);
            return $result;
        }
        return;

    }
}

/* =============================================listingpro create campains table ================================*/

if(!function_exists('lp_create_campaigns_table')){
    function lp_create_campaigns_table(){
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb,$listingpro_options;
        $dbprefix = $wpdb->prefix;
        $sql="
           CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."listing_campaigns`
         (
              main_id bigint(20) NOT NULL auto_increment,
              user_id varchar(255) default NULL,
              post_id varchar(255) default NULL,
              payment_method varchar(255) default NULL,
              token varchar(255) default NULL,
              price varchar(255) default NULL,
              currency varchar(255) default NULL,
              status varchar(255) default NULL,
              transaction_id varchar(255) default NULL,
              PRIMARY KEY  (`main_id`),
              mode varchar(255) default NULL,
              duration varchar(255) default NULL,
              budget varchar(255) default NULL,
              ad_date varchar(255) default NULL,
              ad_expiryDate varchar(255) default NULL

         );";
        dbDelta($sql);
    }
}

/* ==============================================listingpro create listings orders table =============================== */

if(!function_exists('lp_create_listings_orders_table')){
    function lp_create_listings_orders_table(){
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb,$listingpro_options;
        $dbprefix = $wpdb->prefix;
        $wpdb->query("CREATE TABLE IF NOT EXISTS `".$dbprefix."listing_orders` (
          `main_id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `user_id` TEXT NOT NULL ,
          `post_id` TEXT NOT NULL ,
          `plan_id` TEXT NOT NULL ,
          `plan_name` TEXT NOT NULL ,
          `plan_type` TEXT NOT NULL ,
          `payment_method` TEXT NOT NULL ,
          `token` TEXT NOT NULL ,
          `price` FLOAT UNSIGNED NOT NULL ,
          `currency` TEXT NOT NULL ,
          `days` TEXT NOT NULL ,
          `date` TEXT NOT NULL ,
          `status` TEXT NOT NULL ,
          `used` TEXT NOT NULL ,
          `transaction_id` TEXT NOT NULL ,
          `firstname` TEXT NOT NULL ,
          `lastname` TEXT NOT NULL ,
          `email` TEXT NOT NULL ,
          `description` TEXT NOT NULL ,
          `summary` TEXT NOT NULL ,
          `order_id` TEXT NOT NULL
          ) ENGINE = MYISAM; ");
    }
}

/* =============================================listingpro create campains table for views ================================*/

if(!function_exists('lp_create_stats_table_views')){
    function lp_create_stats_table_views(){
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb;
        $dbprefix = $wpdb->prefix;
        if(!lp_table_exists_check($wpdb->prefix."listing_stats_views")) {
        $sql="
           CREATE TABLE `".$wpdb->prefix."listing_stats_views`
         (
              main_id bigint(20) NOT NULL auto_increment,
              user_id varchar(255) default NULL,
              listing_id varchar(255) default NULL,
              listing_title varchar(255) default NULL,
              action_type varchar(255) default NULL,
              month LONGTEXT default NULL,
              year LONGTEXT default NULL,
              count varchar(255) default NULL,
              PRIMARY KEY  (`main_id`)
         );";
        dbDelta($sql);
        }
    }
}

/* =============================================listingpro create campains table for reviews ================================*/

if(!function_exists('lp_create_stats_table_reviews')){
    function lp_create_stats_table_reviews(){
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb;
        $dbprefix = $wpdb->prefix;
        if(!lp_table_exists_check($wpdb->prefix."listing_stats_reviews")) {
        $sql="
           CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."listing_stats_reviews`
         (
              main_id bigint(20) NOT NULL auto_increment,
              user_id varchar(255) default NULL,
              listing_id varchar(255) default NULL,
              listing_title varchar(255) default NULL,
              action_type varchar(255) default NULL,
              month LONGTEXT default NULL,
              year LONGTEXT default NULL,
              count varchar(255) default NULL,
              PRIMARY KEY  (`main_id`)
         );";
        dbDelta($sql);
        }
    }
}

/* =============================================listingpro create campains table for leads ================================*/

if(!function_exists('lp_create_stats_table_leads')){
    function lp_create_stats_table_leads(){
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb;
        $dbprefix = $wpdb->prefix;
        if(!lp_table_exists_check($wpdb->prefix."listing_stats_leads")) {
        $sql="
           CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."listing_stats_leads`
         (
              main_id bigint(20) NOT NULL auto_increment,
              user_id varchar(255) default NULL,
              listing_id varchar(255) default NULL,
              listing_title varchar(255) default NULL,
              action_type varchar(255) default NULL,
              month LONGTEXT default NULL,
              year LONGTEXT default NULL,
              count varchar(255) default NULL,
              PRIMARY KEY  (`main_id`)
         );";
        dbDelta($sql);
        }
    }
}

?>