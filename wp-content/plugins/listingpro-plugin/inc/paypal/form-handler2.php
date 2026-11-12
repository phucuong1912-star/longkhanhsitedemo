<?php
if (!function_exists('lp_form_handler2')) {
    function lp_form_handler2($__POST, $__GET)
    {
        session_start();
        /**
         * Add transaction info to database
         */
        global $wpdb, $listingpro_options;

        $dbprefix = $wpdb->prefix;

        //for ads payment
        if (!empty($__POST['lp_ads_for_listing']) && isset($__POST['lp_ads_for_listing']) && !empty($__POST['method']) && isset($__POST['method']) && is_array($__POST['lpadsoftype'])) {

            // lp_create_campaigns_table();
            lp_ammend_campaigns_table();
            $func_type = sanitize_text_field($__POST['func']);
            $pricetotal = 0;
            $lp_random_ads = $listingpro_options['lp_random_ads'];
            $lp_detail_page_ads = $listingpro_options['lp_detail_page_ads'];
            $lp_top_in_search_page_ads = $listingpro_options['lp_top_in_search_page_ads'];
            $currencyprice = $listingpro_options['currency_paid_submission'];
            $currency = $listingpro_options['currency_paid_submission'];

            $listing_id = isset($__POST['lp_ads_for_listing']) ? sanitize_text_field($__POST['lp_ads_for_listing']) : '';
            $method = isset($__POST['method']) ? sanitize_text_field($__POST['method']) : '';
            $price_package = '';
            if (isset($__POST['lpadsoftype'])) {
                if (is_array($__POST['lpadsoftype'])) {
                    $price_package = array_map('sanitize_text_field', $__POST['lpadsoftype']);
                } else {
                    $price_package = sanitize_text_field($__POST['lpadsoftype']);
                }
            }
            $_SESSION['price_package'] = $price_package;
            $taxPrice = isset($__POST['taxprice']) && is_numeric($sanitized_taxprice = sanitize_text_field($__POST['taxprice'])) ? (float)$sanitized_taxprice : 0.0;
            $_SESSION['taxprice'] = $taxPrice;
            $ads_price = isset($__POST['ads_price']) && is_numeric($sanitized_ads_price = sanitize_text_field($__POST['ads_price'])) ? (float)$sanitized_ads_price : 0.0;
            $adsType = isset($__POST['adsTypeval']) ? sanitize_text_field($__POST['adsTypeval']) : '';
            $pricetotal = $ads_price;
            $ads_duration = '';
            if ($adsType == "byduration") {
                /* via duration */
                $ads_duration = sanitize_text_field($__POST['adsduration_pd']);
            }

            /* paypal */
            if ($method == "paypal") {
                $GLOBALS['pricetotal'] = $pricetotal;
                $GLOBALS['adsType'] = $adsType;
                $GLOBALS['listing_id'] = $listing_id;
                $GLOBALS['price_package'] = $price_package;
                $GLOBALS['ads_duration'] = $ads_duration;
            }

            /* wire */ else if ($method == "wire") {

                if (is_array($price_package) && !empty($listing_id) && !empty($method)) {
                    $budget = $pricetotal;

                    // $budget = $budget - $taxPrice;

                    // lp_create_campaigns_table();
                    $user_ID = '';
                    $user_ID = get_current_user_id();
                    $status = 'pending';

                    $start = 11111111;
                    $end = 999999999;
                    $ord_num = random_int($start, $end);
                    $currentDate = date(get_option('date_format'));


                    $insert_data = array(
                        'user_id' => $user_ID,
                        'post_id' => $listing_id,
                        'payment_method' => $method,
                        'price' => $pricetotal,
                        'currency' => $currency,
                        'status' => $status,
                        'transaction_id' => $ord_num,
                        'mode' => $adsType,
                        'duration' => $ads_duration,
                        'budget' => $budget,
                        'ad_date' => $currentDate,
                        'tax' => $taxPrice,
                    );
                    $table = 'listing_campaigns';
                    lp_insert_data_in_db($table, $insert_data);
                    update_post_meta($listing_id, 'campaign_status', 'in progress');

                    $_SESSION['post_id'] = $listing_id;
                    $_SESSION['price_package'] = $price_package;
                    listing_set_metabox('listings_ads_purchase_packages', $price_package, $listing_id);
                    listing_set_metabox('adsType', $adsType, $listing_id);
                    listing_set_metabox('ads_duration', $ads_duration, $listing_id);
                    $checkout = $listingpro_options['payment-checkout'];
                    $checkout_url = get_permalink($checkout);
                    $perma = '';
                    $methodQuery = 'checkout=wire';
                    global $wp_rewrite;
                    if ($wp_rewrite->permalink_structure == '') {
                        $perma = "&";
                    } else {
                        $perma = "?";
                    }


                    $redirect = '';
                    $redirect = $checkout_url . $perma . $methodQuery;
                    wp_redirect($redirect);

                    exit();
                } else {
                    $backurl = site_url();
                    wp_redirect($backurl);
                    exit();
                }
            }
        }



        /**
         * End function
         */



        //if( !empty( $method ) && $method=="paypal" ){

        include_once(WP_PLUGIN_DIR . "/listingpro-plugin/inc/paypal/paypalapi2.php");
        /* for listing */
        if (isset($__GET['func']) && $__GET['func'] == 'confirm' && isset($__GET['token']) && isset($__GET['PayerID'])) {



            $var = new wp_PayPalAPI();
            $var->ConfirmExpressCheckout();

            if (isset($_SESSION['RETURN_URL'])) {
                $url = $_SESSION['RETURN_URL'];
                unset($_SESSION['RETURN_URL']);
                header('Location: ' . $url);
                exit;
            }

            if (is_numeric(get_option('paypal_success_page')) && get_option('paypal_success_page') > 0)
                header('Location: ' . get_permalink(get_option('paypal_success_page')));
            else
                header('Location: ' . home_url());
            exit;
        }



        if (! count($__POST))
            trigger_error('Payment error code: #00001', E_USER_ERROR);

        $allowed_func = array('start ads');
        if (count($__POST) && (! isset($__POST['func']) || ! in_array($__POST['func'], $allowed_func))) {

            trigger_error('Payment error code: #00002', E_USER_ERROR);
        }


        if ($__POST['func'] && (empty($pricetotal) || !is_numeric($pricetotal) || !isset($pricetotal))) {
            if (empty($pricetotal) || $pricetotal < 0) {
                trigger_error('Payment error code: #00003', E_USER_ERROR);
            }
        }



        switch ($__POST['func']) {

            case 'start ads':
                $var = new wp_PayPalAPI();
                $var->StartExpressCheckout();
                break;
        }
    }
}
