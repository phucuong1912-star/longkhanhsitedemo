<?php

if( !function_exists('listing_draft_save') ){
    function listing_draft_save($postid, $userID = null){

        global $listingpro_options;
        global $wpdb;
        $dbprefix = '';
        $dbprefix = $wpdb->prefix;



        $user_ID = '';

        $plan_ID = '';
        $plan_title = '';
        $postmeta = get_post_meta($postid, 'lp_listingpro_options', true);
        $plan_ID = $postmeta['Plan_id'];
        $plan_title = get_the_title( $plan_ID );

        $plan_price = '';
        $plan_price = get_post_meta($plan_ID, 'plan_price', true);
        $enableTax = false;
        $Taxrate='';
        $Taxtype='';
        if($listingpro_options['lp_tax_swtich']=="1"){
            $enableTax = true;
            $Taxrate = $listingpro_options['lp_tax_amount'];
            $Taxrate = (float)($Taxrate*$plan_price);
            $Taxrate = (float)($Taxrate/100);
            $plan_price = $plan_price + $Taxrate;
        }
        $plan_time = '';
        $plan_time = get_post_meta($plan_ID, 'plan_time', true);
		
        if(empty($plan_time)){
            $plan_time = '';
        }
        $plan_type = '';
        $plan_type = get_post_meta($plan_ID, 'plan_package_type', true);

        $currency_code = '';
        $currency_code = $listingpro_options['currency_paid_submission'];

        $fname = '';
        $lname = '';
        $usermail = '';

        if(empty($userID)){
            $user_ID = get_current_user_id();
            $user_info = get_userdata($user_ID);
            $usermail = $user_info->user_email;
            $fname = $user_info->first_name;
            $lname = $user_info->last_name;
        }
        else{
            $user_ID = $userID;
            $user_info = get_userdata($userID);
            $usermail = $user_info->user_email;
            $fname = $user_info->first_name;
            $lname = $user_info->last_name;
        }

        if(empty($usermail)){
            $usermail = 'user@site.com';
        }

        if(empty($fname)){
            $fname = '';
        }

        if(empty($lname)){
            $lname = '';
        }

        $start = 11111111;
        $end = 999999999;
        $ord_num = random_int($start, $end);

        if(lp_theme_option('listingpro_invoice_start_switch')=="yes"){
            $ord_num = lp_theme_option('listingpro_invoiceno_no_start');
            $ord_num++;
            if (  class_exists( 'Redux' ) ) {
                $opt_name = 'listingpro_options';
                Redux::set_option( $opt_name, 'listingpro_invoiceno_no_start', "$ord_num");
            }
        }
		
		/* saving in post meta */
		listing_set_metabox('lp_purchase_invoiceno', $ord_num, $postid);
		listing_set_metabox('lp_purchase_days', $plan_time, $postid);
		/* saving in post meta */


        listing_set_metabox('order_id', $ord_num, $postid);
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
                      `order_id` TEXT NOT NULL, 
                      `tax` TEXT NOT NULL 
                      ) ENGINE = MYISAM; ");


        $post_info_array = array(

            'user_id'	=> $user_ID ,
            'post_id'	=> $postid,
            'plan_id'	=> $plan_ID ,
            'plan_name' => $plan_title,
            'plan_type' => $plan_type,
            'token' => '',
            'price' => $plan_price,
            'currency'	=> $currency_code ,
            'days'	=> $plan_time ,
            'date'	=> '',
            'status'	=> 'in progress',
            'used'	=> '' ,
            'transaction_id'	=>'',
            'firstname'	=> $fname,
            'lastname'	=> $lname,
            'email'	=> $usermail ,
            'description'	=> '' ,
            'summary'	=> '' ,
            'order_id'	=> $ord_num ,
            'tax'	=> $Taxrate ,

        );
        //exit(json_encode($post_info_array));

        if( !empty($plan_type) && $plan_type=="Pay Per Listing" ){
            $wpdb->insert($dbprefix."listing_orders", $post_info_array);
        }
        else if( !empty($plan_type) && $plan_type=="Package" ){

            $used = 0;
            $post_ids = '';
            $post_allowed_in_plan = '';
            $posts_allowed_in_plan = get_post_meta($plan_ID, 'plan_text', true);

            $results = $wpdb->get_results( "SELECT * FROM ".$dbprefix."listing_orders WHERE user_id ='$user_ID' AND plan_Id='$plan_ID' AND plan_type='$plan_type' AND status = 'success'" );

            if( !empty($results) && count($results) > 0 ){
                lp_update_credit_package($postid);
            }

            else{
                $wpdb->insert($dbprefix."listing_orders", $post_info_array);
            }

        }

    }
}

?>