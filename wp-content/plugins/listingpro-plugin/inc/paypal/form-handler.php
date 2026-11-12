<?php
if(!function_exists('lp_form_handler')){
    function lp_form_handler($__POST, $__GET){

        session_start();
        /**
         * Form posting handler
         */
        /**
         * Add transaction info to database
         */
        global $wpdb;
        global $method, $post_id, $wpdb, $listingpro_options;

        $dbprefix = $wpdb->prefix;

        $post_id = '';
        $method = '';
        $plan_id = '';
        $user_id = '';
        $payment_desc = '';
        $plan_price = '';
        $plan_time = '';
        $date = '';
        $amount = '';
        $currency_code = '';
        $payment_success = '';
        $payment_fail = '';
        $plan_price_withtax = '';
        
        if(!empty($__POST['post_id']) && isset($__POST['post_id']) && !empty($__POST['method']) && isset($__POST['method'])){

            /* for coupons */
            $discount = null;
            $ord_num = '';
            $coupon = '';
            $copontype = '';
            $couponChek = isset($__POST['lp_checkbox_coupon']) ? sanitize_text_field($__POST['lp_checkbox_coupon']) : '';
            if(isset($__POST['coupon-text-field']) && $couponChek == 'couponON'){
                if(!empty($__POST['coupon-text-field'])){
                    $cCode = sanitize_text_field($__POST['coupon-text-field']);
                    $_SESSION['coupon_code'] = $cCode;
                    $exCoupons = lp_get_existing_coupons();
                    if(!empty($exCoupons)){
                        foreach($exCoupons as $sCoupon){
                            $thisCCode = $sCoupon['code'];
                            if($thisCCode==$cCode){
                                $discount = $sCoupon['discount'];
                                $copontype = $sCoupon['copontype'];
                            }
                        }
                    }
                }
            }

            $lpRecurring = '';
            if(isset($__POST['lp-recurring-option'])){
                $lpRecurring = sanitize_text_field($__POST['lp-recurring-option']);
                $_SESSION['lp_paypal_session'] = $lpRecurring;
            }

            $method = $__POST['method'];
            $post_id = absint( $__POST['post_id'] );
            if ( empty( $post_id ) ) {
                return;
            }
            $coupon = isset($__POST['coupon-text-field']) ? sanitize_text_field($__POST['coupon-text-field']) : '';
            $plan_price = sanitize_text_field($__POST['plan_price']);
            $user_id = get_current_user_id();

            $user_info = get_userdata($user_id);
            $usermail = $user_info->user_email;
            $fname = $user_info->first_name;
            $lname = $user_info->last_name;
            $new_plan_id = listing_get_metabox_by_ID('changed_planid', $post_id);
            if(isset($__POST['claim_id'])){
                if(!empty($__POST['claim_id'])){
                    $claim_id = sanitize_text_field($__POST['claim_id']);
                    $new_plan_id = listing_get_metabox_by_ID('claim_plan', $claim_id);
                }
            }
            if(!empty($new_plan_id)){
                $plan_id = $new_plan_id;
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
                if(isset($__POST['claim_id'])){
                    if(!empty($__POST['claim_id'])){
                        update_post_meta($post_id, 'claimOrderNo', $ord_num);
                        update_post_meta($post_id, 'claimPlan_id', $new_plan_id);
                    }
                }

                $plan_title = get_the_title($new_plan_id);
                $plan_price = get_post_meta($new_plan_id, 'plan_price', true);

                if(!empty($discount)){
                    if ($copontype == 'on') {
                        $plan_price = $plan_price - $discount;
                    }
                    else{
                        $discount_price = ($discount/100)*$plan_price;
                        $plan_price = $plan_price - $discount_price;
                    }
                }

                $plan_price_withtax = $plan_price;
                $plan_time = get_post_meta($new_plan_id, 'plan_time', true);
                if(empty($plan_time)){
                    $plan_time = '';
                }
                $plan_type = get_post_meta($new_plan_id, 'plan_package_type', true);
                $Taxrate='';
                $Taxtype='';
                if($listingpro_options['lp_tax_swtich']=="1"){
                    $enableTax = true;
                    $Taxrate = $listingpro_options['lp_tax_amount'];
                    $Taxrate = (float)($Taxrate*$plan_price);
                    $Taxrate = (float)($Taxrate/100);

                    if(!empty($discount)){
                        $discount_tax = ($discount/100)*$Taxrate;
                        $Taxrate = $Taxrate - $discount_tax;
                    }

                    $plan_price_withtax = $plan_price + $Taxrate;
                    $currency_code = $listingpro_options['currency_paid_submission'];
                }

                $post_info_array = array(
                    'user_id'	=> $user_id ,
                    'post_id'	=> $post_id,
                    'plan_id'	=> $new_plan_id ,
                    'plan_name' => $plan_title,
                    'plan_type' => $plan_type,
                    'payment_method' => $method,
                    'token' => '',
                    'price' => $plan_price_withtax,
                    'currency'	=> $currency_code ,
                    'days'	=> $plan_time ,
                    'date'	=> '',
                    'status'	=> 'change plan pending',
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
                $table = 'listing_orders';
                $retData = lp_insert_data_in_db($table, $post_info_array);


            }else{
                $plan_id = listing_get_metabox_by_ID('Plan_id' , $post_id);

                //updating payment method
                $update_data = array('payment_method' => $method);
                $where = array('post_id' => $post_id);
                $update_format = array('%s', '%s');
                $wpdb->update($dbprefix.'listing_orders', $update_data, $where, $update_format);
            }

            //$postmeta = get_post_meta($post_id, 'lp_listingpro_options', true);
            //$plan_id = $postmeta['Plan_id'];


            $payment_desc = esc_html__('Enjoy using our features of Listings subscription in very cheap price', 'listingpro');

            /* for saving meta in post meta for invoice */
                $meta_plan_price = get_post_meta($plan_id, 'plan_price', true);
            $plan_priceformeta = $meta_plan_price;
            $plan_taxPrice = null;
            if(!empty($discount)){
                if($copontype == 'on'){
                    $plan_priceformeta = $meta_plan_price - $discount;
                }
                else{
                    $discount_price = (float)($meta_plan_price/100);
                    $discount_price = (float)($discount_price*$discount);
                    $plan_priceformeta = $meta_plan_price - $discount_price;
                }
            }

             if($listingpro_options['lp_tax_swtich']=="1"){
                $Trate = $listingpro_options['lp_tax_amount'];
                $Trate = (float)($Trate*$meta_plan_price);
                $Trate = (float)($Trate/100);

                if(!empty($discount)){
                    $discount_tax = ($discount/100)*$Trate;
                    $Trate = $Trate - $discount_tax;
                }
                $plan_taxPrice = $Trate;
            }

            listing_set_metabox('lp_purchase_price', $plan_priceformeta, $post_id);
            listing_set_metabox('lp_purchase_tax', $plan_taxPrice, $post_id);

            /* end for saving meta in post meta for invoice */



            $plan_time = listing_get_metabox_by_ID('plan_time' , $plan_id);
            //$plan_time = get_post_meta($plan_id, 'plan_time', true);
            $date = date('d/m/Y H:i:s');
            $payment_fail = $listingpro_options['payment_fail'];
            $payment_success = $listingpro_options['payment_success'];
            $currency_code = $listingpro_options['currency_paid_submission'];



            $planID = $plan_id;
            $amount = $plan_price;
            $enableTax = false;
            $Taxratee='';
            $Taxtype='';
            if($listingpro_options['lp_tax_swtich']=="1"){
                $enableTax = true;
                $Taxratee = $listingpro_options['lp_tax_amount'];
            }
            if(!empty($Taxratee)){
				$sanitized_taxprice = sanitize_text_field($__POST['listings_tax_price']);
                $taxprice = (float) $sanitized_taxprice;
                $plan_price = $plan_price + $taxprice;
                $plan_price = round($plan_price,2);
                $amount = $plan_price + $taxprice;
                $amount = round($amount,2);
            }

            $plan_name = $plan_time;
            $currency = $currency_code;
            $token = '';
            $GLOBALS['post_id'] = $post_id;
            $GLOBALS['plan_id'] = $planID;
            $GLOBALS['plan_price'] = $plan_price;
            $GLOBALS['currency'] = $currency;
            $GLOBALS['discount'] = $discount;
            if( !empty($method) && $method=="wire" ){
                //updating payment method
                $date = date(get_option('date_format'));
                $update_data = array('status' => 'pending', 'date' => $date, 'price' => $plan_price, 'tax' => $plan_taxPrice);
                if(!empty($new_plan_id)){
                    $where = array('post_id' => $post_id, 'order_id' => $ord_num);
                }else{
                    $where = array('post_id' => $post_id);
                }

                $update_format = array('%s', '%s', '%s', '%s');
                $wpdb->update($dbprefix.'listing_orders', $update_data, $where, $update_format);

                $_SESSION['post_id'] = $post_id;
                $_SESSION['plan_id'] = $planID;
                $_SESSION['amount'] = $amount;
                $_SESSION['currency'] = $currency;
                $_SESSION['discount'] = $discount;

                listingpro_apply_coupon_code_at_payment($coupon,$post_id,$plan_taxPrice,$plan_price);
                $checkout = $listingpro_options['payment-checkout'];
                $checkout_url = get_permalink( $checkout );
                $perma = '';
                $methodQuery = 'method=wire';
                global $wp_rewrite;
                if ($wp_rewrite->permalink_structure == ''){
                    $perma = "&";
                }else{
                    $perma = "?";
                }


                $redirect = '';
                $redirect = $checkout_url.$perma.$methodQuery;
                wp_redirect($redirect);
                exit();
            }

            else if(!empty($method) && $method=="stripe"){
                $update_data = array('status' => 'in progress');
                $where = array('post_id' => $post_id);
                $update_format = array('%s', '%s');
                $wpdb->update($dbprefix.'listing_orders', $update_data, $where, $update_format);
                $current_user_obj = wp_get_current_user();
                $current_user_mail = $current_user_obj->user_email;


                $_SESSION['post_id'] = $post_id;
                $_SESSION['currency'] = $currency;
                $_SESSION['price'] = $amount;
                $_SESSION['mail'] = $current_user_mail;
                $redirect = '';
                $redirect = WP_PLUGIN_DIR .'/listingpro-plugin/inc/stripe/index.php';
                wp_redirect($redirect);
                exit();
            }


            else if(!empty($method) && $method=="2checkout"){
                echo esc_attr( $method );
                exit();
            }



            /* action for custom payment method */
            do_action('lp_process_new_payment_method', $method, $post_id, $plan_id);


        }




        /**
         * End function
         */



        //if( !empty( $method ) && $method=="paypal" ){

        include_once (WP_PLUGIN_DIR ."/listingpro-plugin/inc/paypal/paypalapi.php");

        if ( isset($__GET['func']) && $__GET['func'] == 'confirm' && isset($__GET['token']) && isset($__GET['PayerID']) ) {


            //wp_PayPalAPI::ConfirmExpressCheckout();

            $var = new wp_PayPalAPI();
            $var->ConfirmExpressCheckout();

            if ( isset( $_SESSION['RETURN_URL'] ) ) {
                $url = $_SESSION['RETURN_URL'];
                unset($_SESSION['RETURN_URL']);
                header('Location: '.$url);
                exit;
            }

            if ( is_numeric(get_option('paypal_success_page')) && get_option('paypal_success_page') > 0 )
                header('Location: '.get_permalink(get_option('paypal_success_page')));
            else
                header('Location: '.home_url());
            exit;
        }

        if ( ! count($__POST) )
            trigger_error('Payment error code: #00001', E_USER_ERROR);

        $allowed_func = array('start');
        if ( count($__POST) && (! isset($__POST['func']) || ! in_array($__POST['func'], $allowed_func)) )
            trigger_error('Payment error code: #00002', E_USER_ERROR);

        if ( count($__POST) && (! isset($plan_price) || ! is_numeric($plan_price) || $plan_price < 0) )
            trigger_error('Payment error code: #00003', E_USER_ERROR);

        switch ( $__POST['func'] ) {
            case 'start':
                //wp_PayPalAPI::StartExpressCheckout();
                $var = new wp_PayPalAPI();
                $var->StartExpressCheckout();
                break;
        }
    }
}