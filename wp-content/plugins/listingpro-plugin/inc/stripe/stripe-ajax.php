<?php

add_action('wp_ajax_listingpro_save_stripe', 'listingpro_save_stripe');
add_action('wp_ajax_nopriv_listingpro_save_stripe', 'listingpro_save_stripe');

if (!function_exists('listingpro_save_stripe')) {
    function listingpro_save_stripe()
    {
        include_once(WP_PLUGIN_DIR . "/listingpro-plugin/inc/stripe/stripe-php/init.php");
        global $wpdb, $listingpro_options, $wp_rewrite;
        $lpURLChar = '?';
        if ($wp_rewrite->permalink_structure == '') {
            $lpURLChar = '&';
        }
        $secritKey = '';
        $secritKey = $listingpro_options['stripe_secrit_key'];
        $dbprefix = '';
        $dbprefix = $wpdb->prefix;

        $current_user = wp_get_current_user();
        $useremail = $current_user->user_email;
        $usereDname = $current_user->display_name;

        $paypal_success = $listingpro_options['payment-checkout'];
        if (!empty($paypal_success)) {
            $paypal_success = get_permalink($paypal_success);
            $paypal_success .= $lpURLChar . 'lpcheckstatus=success';
        }
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $coupon = isset($_POST['coupon']) ? sanitize_text_field($_POST['coupon']) : '';
        $planID = isset($_POST['plan']) ? sanitize_text_field($_POST['plan']) : '';


        $planprice = '';
        if (isset($_POST['plan_price']) && !empty($_POST['plan_price'])) {
            $planprice = sanitize_text_field($_POST['plan_price']);
        }
        $planpriceOR = get_post_meta($planID, 'plan_price', true);
        $taxrate = 0;
        if (isset($_POST['taxrate']) && !empty($_POST['taxrate'])) {
            $taxrate = sanitize_text_field($_POST['taxrate']);
        }
        $listing = (int) sanitize_text_field($_POST['listing']);
        $token = sanitize_text_field($_POST['token']);
        $subsrID = '';

        $recurring;
        if (isset($_POST['recurring']) && !empty($_POST['recurring'])) {
            $recurring = sanitize_text_field($_POST['recurring']);
        }

        $listing_title = get_the_title($listing);
        if (empty($planprice)) {
            $planprice = get_post_meta($planID, 'plan_price', true);
        }
        $plan_time = get_post_meta($planID, 'plan_time', true);

        /* for saving meta in post meta for invoice */
        $plan_priceformeta = $planprice;
        $plan_taxPrice = null;

        if (!empty($taxrate)) {
            $plan_taxPrice = $taxrate;
        }

        listing_set_metabox('lp_purchase_price', $plan_priceformeta, $listing);
        listing_set_metabox('lp_purchase_tax', $plan_taxPrice, $listing);


        /* end for saving meta in post meta for invoice */

        $planprice = $planprice + $taxrate;

        $planprice = (float) $planprice * 100;
        $planprice = round($planprice, 2);
        $planprice = (int) $planprice;
        $planpriceINVOICE = number_format(($planprice / 100), 2, '.', '');
        $currency = '';
        $charge = array();
        $currency = $listingpro_options['currency_paid_submission'];
        $user_id = get_current_user_id();
        \Stripe\Stripe::setApiKey("$secritKey");

        if (!empty($recurring)) {

            if (!empty($coupon)) {
                $planpriceOR = get_post_meta($planID, 'plan_price', true);
                $existingCoupon = lp_get_existing_coupons();
                if (!empty($existingCoupon)) {
                    $returnKey = lp_search_coupon_in_array($coupon, $existingCoupon);
                    if (isset($returnKey)) {
                        $couponData = $existingCoupon[$returnKey];
                        if (!empty($couponData)) {
                            $couponType = '';
                            if (isset($couponData['copontype'])) {
                                $couponType = $couponData['copontype'];
                            }
                            $discount = $couponData['discount'];
                            $couponID = $coupon . rand();
                            if (!empty($couponType)) {
                                $discount = (float) $discount * 100;
                                $discount = round($discount, 2);
                                $discount = (int) $discount;
                                $disType = array(
                                    'duration' => 'once',
                                    'id' => $couponID,
                                    'amount_off' => $discount,
                                    "currency" => $currency
                                );
                            } else {
                                if (lp_theme_option('lp_tax_swtich') == "1") {
                                    $taxrate = lp_theme_option('lp_tax_amount');
                                    $taxrate = ($taxrate / 100) * $planpriceOR;
                                }
                                $disType = array(
                                    'duration' => 'once',
                                    'id' => $couponID,
                                    'percent_off' => $discount,
                                );
                            }
                            $planpriceOR = $planpriceOR + $taxrate;
                            $planpriceOR = (float) $planpriceOR * 100;
                            $planpriceOR = round($planpriceOR, 2);
                            $planpriceOR = (int) $planpriceOR;
                            $planprice = $planpriceOR;
                        }
                    }
                }
            }

            if (!empty($plan_time)) {
                $plan_time = (int) $plan_time;
            } else {
                $response = '';
                $msG = '';
                $msG = esc_html__('Sorry! A plan should have a duration for recurring payment', 'listingpro');
                $response = json_encode(array('status' => 'fail', 'token' => $token, 'email' => $email, 'listing' => $listing, 'redirect' => $msG));

                die($response);
            }
            /* step-1 */
            $customer = \Stripe\Customer::create(array(
                "email" => $email,
                "source" => $token,
                'description' => $usereDname
            ));



            /* creat product */
            $product = \Stripe\Product::create(array(
                "name" => get_the_title($planID) . rand(),
                "type" => 'service',
            ));


            /* step-2 */
            $plan = \Stripe\Plan::create(array(
                "product" => array(
                    "name" => get_the_title($planID) . rand()
                ),
                "id" => "$user_id" . rand(),
                "interval" => "day",
                "interval_count" => $plan_time,
                "currency" => $currency,
                "amount" => $planprice,
            ));
            $couponStripe = '';
            if (!empty($discount)) {
                $couponStripe = \Stripe\Coupon::create($disType);
            }

            /* step-3 */
            $subscirptionObj = \Stripe\Subscription::create(array(
                "customer" => $customer->id,
                "items" => array(
                    array(
                        "plan" => $plan->id,
                    )
                ),
                'coupon' => $couponStripe,
            ));


            if (!empty($subscirptionObj)) {
                $subsrID = $subscirptionObj->id;
                $charge = array(
                    'amount_refunded' => 0,
                    'failure_code' => null,
                    'captured' => true,
                );
            } else {
                $response = '';
                $msG = '';
                $msG = esc_html__('Sorry! Error in transaction', 'listingpro');
                $response = json_encode(array('status' => 'fail', 'token' => $token, 'email' => $email, 'listing' => $listing, 'redirect' => $msG));

                die($response);
            }
        } else {
            $declined = false;
            try {
                $customer = \Stripe\Customer::create(array(
                    "email" => $email,
                    "source" => $token,
                    'description' => $usereDname
                ));

                $charge = \Stripe\Charge::create(array(
                    "amount" => $planprice,
                    "currency" => $currency,
                    "description" => "Listing payment",
                    "customer" => $customer->id,
                    "receipt_email" => $email
                ));
            } catch (\Stripe\Error\Card $e) {
                $declined = true;
            }
            if ($declined) {
                $response = '';
                $msG = '';
                $msG = esc_html__('Sorry! There is some problem in your stripe payment', 'listingpro');
                $response = json_encode(array('status' => 'fail', 'token' => $token, 'email' => $email, 'listing' => $listing, 'redirect' => $msG));

                die($response);
            }
        }

        if ($charge['amount_refunded'] == 0 && $charge['failure_code'] == null && $charge['captured'] == true) {

            /* for paid claim */
            $claimPost = get_post_meta($listing, 'claimpID', true);
            if (isset($claimPost)) {
                if (!empty($claimPost)) {
                    $new_author = listing_get_metabox_by_ID('claimer', $claimPost);
                    $exMetaboxes = get_post_meta($listing, 'lp_' . strtolower(THEMENAME) . '_options', true);
                    $feautes_metaBoxes = get_post_meta($listing, 'lp_' . strtolower(THEMENAME) . '_options_fields', true);
                    $argListing = array(
                        'ID' => $listing,
                        'post_author' => $new_author,
                    );
                    wp_update_post($argListing);
                    update_post_meta($listing, 'lp_' . strtolower(THEMENAME) . '_options', $exMetaboxes);
                    update_post_meta($listing, 'lp_' . strtolower(THEMENAME) . '_options_fields', $feautes_metaBoxes);
                    lp_update_paid_claim_metas($claimPost, $listing, 'stripe');
                    delete_post_meta($listing, 'claimpID');
                }
            }



            $status = 'success';
            $method = 'stripe';
            $currency = '';
            $currency = $listingpro_options['currency_paid_submission'];

            $my_post;
            $listing_status = get_post_status($listing);
            if ($listingpro_options['listings_admin_approved'] == "no" || $listing_status == "publish") {
                $my_post = array('ID' => $listing, 'post_date' => date("Y-m-d H:i:s"), 'post_status' => 'publish');
            } else {
                $my_post = array('ID' => $listing, 'post_date' => date("Y-m-d H:i:s"), 'post_status' => 'pending');
            }
            wp_update_post($my_post);
            listingpro_apply_coupon_code_at_payment($coupon, $listing, $taxrate, $planprice);
            $ex_plan_id = listing_get_metabox_by_ID('Plan_id', $listing);
            $new_plan_id = listing_get_metabox_by_ID('changed_planid', $listing);
            if (!empty($new_plan_id)) {
                if ($ex_plan_id != $new_plan_id) {
                    lp_cancel_stripe_subscription($listing, $ex_plan_id);
                    listing_set_metabox('Plan_id', $new_plan_id, $listing);
                    listing_set_metabox('changed_planid', '', $listing);

                    listing_draft_save($listing, $user_id);
                }
            }

            if (!empty($subsrID)) {
                $new_subsc = array('plan_id' => $planID, 'subscr_id' => $subsrID, 'listing_id' => $listing);
                lp_add_new_susbcription_meta($new_subsc);
            }




            $admin_email = '';
            $admin_email = get_option('admin_email');

            $listing_id = $listing;
            $listing_title = get_the_title($listing);


            $date = date(get_option('date_format'));


            $dbprefixx = $wpdb->prefix;
            $table = 'listing_orders';
            $getStripeData = array();
            $orderlpID = '';
            $table_name = $dbprefixx . $table;
            $data = '*';
            $condition = 'post_id="' . $listing_id . '"';
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
                $getStripeData = lp_get_data_from_db($table, $data, $condition);
            }

            if (!empty($getStripeData) && is_array($getStripeData)) {
                $orderlpID = $getStripeData[0]->order_id;;
            }

            if (!empty($recurring)) {
                $update_data = array(
                    'currency' => $currency,
                    'date' => $date,
                    'status' => $status,
                    'description' => 'listing has been purchased',
                    'payment_method' => $method,
                    'summary' => 'recurring',
                    'price' => $planpriceINVOICE,
                    'token' => $token
                );
            } else {


                $update_data = array(
                    'currency' => $currency,
                    'date' => $date,
                    'status' => $status,
                    'description' => 'listing has been purchased',
                    'payment_method' => $method,
                    'summary' => $status,
                    'price' => $planpriceINVOICE,
                    'token' => $token
                );
            }

            $where = array('order_id' => $orderlpID);

            $update_format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s');



            $wpdb->update($dbprefix . 'listing_orders', $update_data, $where, $update_format);


            $packageResult = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $dbprefix . "listing_orders WHERE post_id = %d", $listing));
            $planID = $packageResult->plan_id;
            $planUsed = $packageResult->used;

            $allowedPosts = '';
            $allowedPosts = get_post_meta($planID, 'plan_text', true);

            $update_data = array('used' => '1', 'transaction_id' => $token, 'status' => $status);

            $where = array('token' => $token);

            $update_format = array('%s', '%s', '%s');

            $wpdb->update($dbprefix . 'listing_orders', $update_data, $where, $update_format);

            if (!empty($allowedPosts) && $allowedPosts == "1") {
                $update_status = array('status' => 'expired');
                $wheree = array('plan_id' => $planID);
                $update_formatt = array('%s');
                $wpdb->update($dbprefix . 'listing_orders', $update_status, $wheree, $update_formatt);
            }

            $invoice_no = '';
            $invoice_no = '';
            $plan_title = '';
            $plan_price = '';
            $thepostt = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM {$dbprefix}listing_orders WHERE post_id = %d", $listing)
            );

            foreach ($thepostt as $sresultPost) {
                $invoice_no = $sresultPost->order_id;
                $invoice_no = $sresultPost->order_id;
                $plan_title = $sresultPost->plan_name;
                $plan_price = $sresultPost->price . $sresultPost->currency;
            }

            $current_user = wp_get_current_user();
            $useremail = $current_user->user_email;
            $user_name = $current_user->user_login;
            $admin_email = '';
            $admin_email = get_option('admin_email');

            $listing_title = get_the_title($listing);

            $payment_method = $method;


            $listing_url = get_the_permalink($listing);

            $mail_subject = $listingpro_options['listingpro_subject_purchase_activated_admin'];
            $website_url = site_url();
            $website_name = get_option('blogname');

            $formated_mail_subject = lp_sprintf2("$mail_subject", array(
                'website_url' => "$website_url",
                'website_name' => "$website_name",
                'user_name' => "$user_name",
            ));


            $mail_content = $listingpro_options['listingpro_content_purchase_activated_admin'];

            $formated_mail_content = lp_sprintf2("$mail_content", array(
                'website_url' => "$website_url",
                'listing_title' => "$listing_title",
                'plan_title' => "$plan_title",
                'plan_price' => "$plan_price",
                'listing_url' => "$listing_url",
                'invoice_no' => "$invoice_no",
                'website_name' => "$website_name",
                'payment_method' => "$payment_method",
                'user_name' => "$user_name",
            ));

            lp_mail_headers_append();
            $headers1[] = 'Content-Type: text/html; charset=UTF-8';
            LP_send_mail($admin_email, $formated_mail_subject, $formated_mail_content, $headers1);

            $mail_subject2 = $listingpro_options['listingpro_subject_purchase_activated'];
            $website_url = site_url();

            $formated_mail_subject2 = lp_sprintf2("$mail_subject2", array(
                'website_url' => "$website_url",
                'website_name' => "$website_name",
                'user_name' => "$user_name",
            ));

            $mail_content2 = $listingpro_options['listingpro_content_purchase_activated'];

            $formated_mail_content2 = lp_sprintf2("$mail_content2", array(
                'website_url' => "$website_url",
                'listing_title' => "$listing_title",
                'plan_title' => "$plan_title",
                'plan_price' => "$plan_price",
                'listing_url' => "$listing_url",
                'invoice_no' => "$invoice_no",
                'website_name' => "$website_name",
                'payment_method' => "$payment_method",
                'user_name' => "$user_name",
            ));

            lp_mail_headers_append();
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            LP_send_mail($useremail, $formated_mail_subject2, $formated_mail_content2, $headers);
            lp_mail_headers_remove();

            $response = '';
            $response = json_encode(array('status' => 'success', 'token' => $token, 'email' => $email, 'listing' => $listing, 'redirect' => $paypal_success));

            die($response);
        } else {
            $response = '';
            $msG = '';
            $msG = esc_html__('Sorry! Error in transaction', 'listingpro');
            $response = json_encode(array('status' => 'fail', 'token' => $token, 'email' => $email, 'listing' => $listing, 'redirect' => $msG));

            die($response);
        }
    }
}

/* ======================================= campaign data saved ======================= */

add_action('wp_ajax_listingpro_save_package_stripe', 'listingpro_save_package_stripe');
add_action('wp_ajax_nopriv_listingpro_save_package_stripe', 'listingpro_save_package_stripe');

if (!function_exists('listingpro_save_package_stripe')) {

    function listingpro_save_package_stripe()
    {
        include_once(WP_PLUGIN_DIR . "/listingpro-plugin/inc/stripe/stripe-php/init.php");
        global $wpdb, $listingpro_options, $wp_rewrite;
        $lpURLChar = '?';
        if ($wp_rewrite->permalink_structure == '') {
            $lpURLChar = '&';
        }
        $secritKey = '';
        $secritKey = lp_theme_option('stripe_secrit_key');
        $dbprefix = '';
        $dbprefix = $wpdb->prefix;
        $paypal_success = lp_theme_option('payment-checkout');
        $paypal_success = get_permalink($paypal_success);
        $paypal_success .= $lpURLChar . 'lpcheckstatus=success';
        $currency = '';
        $currency = lp_theme_option('currency_paid_submission');
        $response = '';
        $token = '';
        $status = 'success';
        $email = '';
        $ads_days = '';
        $price_packages = '';
        $listing_id = isset($_POST['listing']) ? (int) sanitize_text_field($_POST['listing']) : 0;
        $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $taxprice = isset($_POST['taxprice']) ? sanitize_text_field($_POST['taxprice']) : '';
        $adsTypeval = isset($_POST['adsTypeval']) ? sanitize_text_field($_POST['adsTypeval']) : '';
        $ads_days = isset($_POST['ads_days']) ? sanitize_text_field($_POST['ads_days']) : '';
        $ads_price = isset($_POST['ads_price']) ? sanitize_text_field($_POST['ads_price']) : '';
        if(isset($_POST['packages'])){
            if(is_array($_POST['packages'])){
                $price_packages = array_map('sanitize_text_field', $_POST['packages']);
            }else{
                $price_packages = sanitize_text_field($_POST['packages']);
            }
        }
        $budget = isset($_POST['lpTOtalprice']) ? sanitize_text_field($_POST['lpTOtalprice']) : '';
        $pkgPrice = $ads_price;
        $_SESSION['price_package'] = $price_packages;

        $enableTax = false;
        $taxrate = '';
        if (lp_theme_option('lp_tax_swtich') == "1") {
            $enableTax = true;
            $taxrate = lp_theme_option('lp_tax_amount');
            if (!empty($taxrate)) {
                //                $taxrate = ($taxrate / 100) * $ads_price;
                //                $ads_price = $ads_price + $taxrate;
            }
        }
        $ads_org_price = $ads_price;
        $ads_price = (float) $ads_price * 100;
        $ads_price = round($ads_price, 2);
        $ads_price = (int) $ads_price;

        $paid_price = (float) $budget * 100;
        $paid_price = round($paid_price, 2);
        $paid_price = (int) $paid_price;

        \Stripe\Stripe::setApiKey("$secritKey");
        $charge = \Stripe\Charge::create(array(
            "amount" => $paid_price,
            "currency" => "$currency",
            "description" => "Ads payment received",
            "source" => $token,
            "receipt_email" => $email
        ));

        if ($charge['amount_refunded'] == 0 && $charge['failure_code'] == null && $charge['captured'] == true) {


            if (session_id() == '') {
                session_start();
            }


            $tID = $token;
            $token = $token;
            $payment_method = 'stripe';

            lp_save_campaign_data($price_packages, $tID, $payment_method, $token, $status, $ads_price, $budget, $listing_id, $adsTypeval, $ads_days, $ads_org_price, $taxprice);

            $response = json_encode(array('status' => 'success', 'token' => $token, 'email' => $email, 'listing' => $listing_id, 'redirect' => $paypal_success, 'pgks' => $price_packages));

            die($response);
        } else {
            $msG = '';
            $msG = esc_html__('Sorry!, error in transaction', 'listingpro');
            $response = json_encode(array('status' => 'fail', 'token' => $token, 'email' => $email, 'listing' => $listing_id, 'redirect' => $paypal_success, 'pgks' => $price_packages, 'msg' => $msG));

            die($response);
        }
    }
}



/* =============================for claim payment via stripe */

add_action('wp_ajax_listingpro_claim_payment_via_stripe', 'listingpro_claim_payment_via_stripe');
add_action('wp_ajax_nopriv_listingpro_claim_payment_via_stripe', 'listingpro_claim_payment_via_stripe');

if (!function_exists('listingpro_claim_payment_via_stripe')) {

    function listingpro_claim_payment_via_stripe()
    {
        include_once(WP_PLUGIN_DIR . "/listingpro-plugin/inc/stripe/stripe-php/init.php");
        global $listingpro_options;
        $secritKey = '';
        $secritKey = $listingpro_options['stripe_secrit_key'];


        $paypal_success = $listingpro_options['payment-checkout'];
        if (!empty($paypal_success)) {
            $paypal_success = get_permalink($paypal_success);
            $paypal_success .= '?' . 'lpcheckstatus=success';
        }
        $claimerID = isset($_POST['claimerID']) ? sanitize_text_field($_POST['claimerID']) : '';
        $claimerEmail = get_user_meta($claimerID, 'email_for_claim', true);
        $listing_id = isset($_POST['listing_id']) ? (int) sanitize_text_field($_POST['listing_id']) : 0;
        $claimPost = isset($_POST['claimPost']) ? sanitize_text_field($_POST['claimPost']) : '';
        $claimPrice = isset($_POST['claimPrice']) ? sanitize_text_field($_POST['claimPrice']) : '';
        $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';

        $claimerDname = '';

        $currency = '';
        $charge = array();
        $currency = $listingpro_options['currency_paid_submission'];
        \Stripe\Stripe::setApiKey("$secritKey");

        $charge = \Stripe\Charge::create(array(
            "amount" => $claimPrice,
            "currency" => "$currency",
            "description" => "Claim Payment",
            "source" => $token,
        ));

        if ($charge['amount_refunded'] == 0 && $charge['failure_code'] == null && $charge['captured'] == true) {

            $exMetaboxes = get_post_meta($claimPost, 'lp_' . strtolower(THEMENAME) . '_options', true);
            $argClaims = array(
                'ID' => $claimPost,
                'post_author' => $claimerID,
            );
            wp_update_post($argClaims);
            update_post_meta($claimPost, 'lp_' . strtolower(THEMENAME) . '_options', $exMetaboxes);

            /* update claim to success */
            listing_set_metabox('claim_status', 'approved', $claimPost);
            listing_set_metabox('claimed_section', 'claimed', $listing_id);
            update_post_meta($listing_id, 'claimed', 1);
            listing_set_metabox('owner', $claimerID, $claimPost);

            /* updating post table */
            global $wpdb;
            $prefix = $wpdb->prefix;

            $status = 'success';
            $response = '';
            $response = json_encode(array('status' => 'success', 'token' => $token, 'email' => $email, 'listing' => $listing, 'redirect' => $paypal_success));

            die($response);
        } else {
            $response = '';
            $msG = '';
            $msG = esc_html__('Sorry! Error in transaction', 'listingpro');
            $response = json_encode(array('status' => 'fail', 'token' => $token, 'email' => $email, 'listing' => $listing, 'redirect' => $msG));

            die($response);
        }
    }
}

/* ======================================= campaign data saved ======================= */
