<?php

//session_start();

/**
 * PayPal API
 */
if (!class_exists('wp_PayPalAPI')) {

    class wp_PayPalAPI {

        /**
         * Start express checkout
         */
        function StartExpressCheckout() {

            global $listingpro_options, $wp_rewrite;
            $lpURLChar = '?';
            if ($wp_rewrite->permalink_structure == '') {
                $lpURLChar = '&';
            }
            $paypal_api_environment = $listingpro_options['paypal_api'];
            $paypal_success = $listingpro_options['payment-checkout'];
            $paypal_success = get_permalink($paypal_success);
            $paypal_success .=$lpURLChar . 'lpcheckstatus=success';
            $paypal_fail = $listingpro_options['payment-checkout'];
            $paypal_fail = get_permalink($paypal_fail);
            $paypal_fail .=$lpURLChar . 'lpcheckstatus=fail';
            $paypal_api_username = $listingpro_options['paypal_api_username'];
            $paypal_api_password = $listingpro_options['paypal_api_password'];
            $paypal_api_signature = $listingpro_options['paypal_api_signature'];
            $currency_code = $listingpro_options['currency_paid_submission'];

            if ($paypal_api_environment != 'sandbox' && $paypal_api_environment != 'live')
                trigger_error('Environment does not defined! Please define it at the plugin configuration page!', E_USER_ERROR);

            /* if ( $paypal_fail === FALSE || ! is_numeric($paypal_fail) )
              trigger_error('Cancel page not defined! Please define it at the plugin configuration page!', E_USER_ERROR);

              if ( $paypal_success === FALSE || ! is_numeric($paypal_success) )
              trigger_error('Success page not defined! Please define it at the plugin configuration page!', E_USER_ERROR); */

            global $plan_price, $post_id, $coupon;

            $plan_price = round($plan_price,2);

            $lpPayDesc = null;
            $lpPayDesc = esc_html__('listing title', 'listingpro') . ':' . get_the_title($post_id) . ' ';
            $lpPayDesc .= esc_html__('price', 'listingpro') . ':' . $plan_price;

            $PAYMENTREQUEST_0_DESC = $lpPayDesc;
            $AMT = $plan_price;

            $CURRENCYCODE = $currency_code;

            // FIELDS
            $fields = array(
                'USER' => urlencode($paypal_api_username),
                'PWD' => urlencode($paypal_api_password),
                'SIGNATURE' => urlencode($paypal_api_signature),
                'VERSION' => urlencode('72.0'),
                'PAYMENTREQUEST_0_PAYMENTACTION' => urlencode('Sale'),
                'PAYMENTREQUEST_0_AMT0' => urlencode($AMT),
                'PAYMENTREQUEST_0_CUSTOM' => urlencode($post_id),
                'PAYMENTREQUEST_0_AMT' => urlencode($AMT),
                'PAYMENTREQUEST_0_ITEMAMT' => urlencode($AMT),
                'ITEMAMT' => urlencode($AMT),
                'PAYMENTREQUEST_0_CURRENCYCODE' => urlencode($CURRENCYCODE),
                'RETURNURL' => urlencode($paypal_success),
                'CANCELURL' => urlencode($paypal_fail),
                'METHOD' => urlencode('SetExpressCheckout'),
                'POSTID' => urlencode('postid')
            );

            $fields['PAYMENTREQUEST_0_CUSTOM'] = $post_id;

            if (isset($PAYMENTREQUEST_0_DESC))
                $fields['PAYMENTREQUEST_0_DESC'] = $PAYMENTREQUEST_0_DESC;

            if (isset($paypal_success))
                $_SESSION['RETURN_URL'] = $paypal_success;

            if (isset($paypal_fail))
                $fields['CANCELURL'] = $paypal_fail;

            $fields['PAYMENTREQUEST_0_QTY0'] = 1;
            $fields['PAYMENTREQUEST_0_AMT'] = $fields['PAYMENTREQUEST_0_AMT'];


            if (isset($_POST['TAXAMT'])) {
                $fields['PAYMENTREQUEST_0_TAXAMT'] = $_POST['TAXAMT'];
                $fields['PAYMENTREQUEST_0_AMT'] += $_POST['TAXAMT'];
            }


            if (isset($_POST['HANDLINGAMT'])) {
                $fields['PAYMENTREQUEST_0_HANDLINGAMT'] = $_POST['HANDLINGAMT'];
                $fields['PAYMENTREQUEST_0_AMT'] += $_POST['HANDLINGAMT'];
            }

            if (isset($_POST['SHIPPINGAMT'])) {
                $fields['PAYMENTREQUEST_0_SHIPPINGAMT'] = $_POST['SHIPPINGAMT'];
                $fields['PAYMENTREQUEST_0_AMT'] += $_POST['SHIPPINGAMT'];
            }

            if (isset($_SESSION['lp_paypal_session'])) {
                $fields['L_BILLINGTYPE0'] = 'RecurringPayments';
                $fields['L_BILLINGAGREEMENTDESCRIPTION0'] = 'Exemplo';
            }

            $fields_string = '';

            foreach ($fields as $key => $value)
                $fields_string .= $key . '=' . $value . '&';

            rtrim($fields_string, '&');

            // CURL
            $ch = curl_init();

            if ($paypal_api_environment == 'sandbox')
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
            elseif ($paypal_api_environment == 'live')
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');

            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6); //6 is for TLSV1.2
            //execute post
            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            parse_str($result, $result);

            if ($result['ACK'] == 'Success') {

                if ($paypal_api_environment == 'sandbox')
                    header('Location: https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' . $result['TOKEN']);
                elseif ($paypal_api_environment == 'live')
                    header('Location: https://www.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' . $result['TOKEN']);
                exit;
            } else {
                print_r($result);
            }
        }

        /**
         * Validate payment
         */
        function ConfirmExpressCheckout() {

            global $listingpro_options;
            $paypal_api_environment = $listingpro_options['paypal_api'];
            $paypal_success = $listingpro_options['payment_success'];
            $paypal_success = get_permalink($paypal_success);
            $paypal_fail = $listingpro_options['payment_fail'];
            $paypal_fail = get_permalink($paypal_fail);
            $paypal_api_username = $listingpro_options['paypal_api_username'];
            $paypal_api_password = $listingpro_options['paypal_api_password'];
            $paypal_api_signature = $listingpro_options['paypal_api_signature'];

            // FIELDS
            $fields = array(
                'USER' => urlencode($paypal_api_username),
                'PWD' => urlencode($paypal_api_password),
                'SIGNATURE' => urlencode($paypal_api_signature),
                'PAYMENTREQUEST_0_PAYMENTACTION' => urlencode('Sale'),
                'VERSION' => urlencode('72.0'),
                'TOKEN' => urlencode($_GET['token']),
                'METHOD' => urlencode('GetExpressCheckoutDetails')
            );

            $fields_string = '';
            foreach ($fields as $key => $value)
                $fields_string .= $key . '=' . $value . '&';
            rtrim($fields_string, '&');

            // CURL
            $ch = curl_init();

            if ($paypal_api_environment == 'sandbox')
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
            elseif ($paypal_api_environment == 'live')
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');

            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6); //6 is for TLSV1.2
            //execute post
            $result = curl_exec($ch);
            //close connection
            curl_close($ch);

            $nvp = array();

            if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $result, $matches)) {
                foreach ($matches['name'] as $offset => $name) {
                    $nvp[$name] = urldecode($matches['value'][$offset]);
                }
            }

            parse_str($result, $result);
            $post_id = $result['PAYMENTREQUEST_0_CUSTOM'];
            $var = new wp_PayPalAPI();
            if ($result['ACK'] == 'Success') {
                $var->SavePayment($result, 'pending');
                /* paypal subscription */
                if (isset($_SESSION['lp_paypal_session'])) {

                    $var->create_recurring_payments_profile($result, 'success', $post_id);
                } else {
                    /* one time payment */
                    $var->DoExpressCheckout($result, $post_id);
                }
            } else {
                $var->SavePayment($result, 'failed');
                //wp_PayPalAPI::SavePayment($result, 'failed');
            }
        }

        /**
         * Close transaction
         */
        function DoExpressCheckout($result, $post_id) {

            global $listingpro_options;
            $paypal_api_environment = $listingpro_options['paypal_api'];
            $paypal_success = $listingpro_options['payment_success'];
            $paypal_success = get_permalink($paypal_success);
            $paypal_fail = $listingpro_options['payment_fail'];
            $paypal_fail = get_permalink($paypal_fail);
            $paypal_api_username = $listingpro_options['paypal_api_username'];
            $paypal_api_password = $listingpro_options['paypal_api_password'];
            $paypal_api_signature = $listingpro_options['paypal_api_signature'];

            // FIELDS
            $fields = array(
                'USER' => urlencode($paypal_api_username),
                'PWD' => urlencode($paypal_api_password),
                'SIGNATURE' => urlencode($paypal_api_signature),
                'VERSION' => urlencode('72.0'),
                'PAYMENTREQUEST_0_PAYMENTACTION' => urlencode('Sale'),
                'PAYERID' => urlencode($result['PAYERID']),
                'TOKEN' => urlencode($result['TOKEN']),
                'PAYMENTREQUEST_0_AMT' => urlencode($result['AMT']),
                'PAYMENTREQUEST_0_CURRENCYCODE' => urlencode($result['CURRENCYCODE']),
                'METHOD' => urlencode('DoExpressCheckoutPayment')
            );

            $fields_string = '';
            foreach ($fields as $key => $value)
                $fields_string .= $key . '=' . $value . '&';
            rtrim($fields_string, '&');

            // CURL
            $ch = curl_init();

            if ($paypal_api_environment == 'sandbox')
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
            elseif ($paypal_api_environment == 'live')
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');

            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSLVERSION, 6); //6 is for TLSV1.2
            //execute post
            $result = curl_exec($ch);
            //close connection
            curl_close($ch);


            parse_str($result, $result);
            if ($result['ACK'] == 'Success') {

                wp_PayPalAPI::UpdatePayment($result, 'success', $post_id);
            } else {
                wp_PayPalAPI::UpdatePayment($result, 'failed', $post_id);
            }
        }

        /**
         * Save payment result into database
         */
        function SavePayment($result, $status) {
            global $wpdb;
            $dbprefix = $wpdb->prefix;
            $date = date(get_option('date_format'));

            if (isset($_SESSION['lp_paypal_session'])) {
                $summary = 'recurring';
            } else {
                $summary = serialize($result);
            }

            $update_data = array(
                'price' => $result['PAYMENTREQUEST_0_AMT'],
                'currency' => $result['CURRENCYCODE'],
                'date' => $date,
                'status' => $status,
                'description' => $result['PAYMENTREQUEST_0_DESC'],
                'summary' => $summary,
                'token' => $result['TOKEN']);

            $where = array('post_id' => $result['PAYMENTREQUEST_0_CUSTOM']);

            $update_format = array('%s', '%s', '%s', '%s', '%s', '%s');

            $wpdb->update($dbprefix . 'listing_orders', $update_data, $where, $update_format);

            $token = '';
            $tokenn = $result['TOKEN'];
        }

        /**
         * Update payment
         */
        function UpdatePayment($result, $status, $post_id) {
            global $wpdb, $listingpro_options;
            $dbprefix = $wpdb->prefix;

            if ($status == "success" && !empty($post_id)) {


                $claimPost = get_post_meta($post_id, 'claimpID', true);
                if (isset($claimPost)) {
                    if (!empty($claimPost)) {

                        $new_author = listing_get_metabox_by_ID('claimer', $claimPost);
                        $exMetaboxes = get_post_meta($post_id, 'lp_' . strtolower(THEMENAME) . '_options', true);
                        $feautes_metaBoxes = get_post_meta($post_id, 'lp_' . strtolower(THEMENAME) . '_options_fields', true);
                        $argListing = array(
                            'ID' => $post_id,
                            'post_author' => $new_author,
                        );
                        wp_update_post($argListing);
                        update_post_meta($post_id, 'lp_' . strtolower(THEMENAME) . '_options', $exMetaboxes);
                        update_post_meta($post_id, 'lp_' . strtolower(THEMENAME) . '_options_fields', $feautes_metaBoxes);

                        lp_update_paid_claim_metas($claimPost, $post_id, 'paypal');
                        delete_post_meta($post_id, 'claimpID');
                    }
                }

                $my_post;

                $listing_status = get_post_status($post_id);

                if ($listingpro_options['listings_admin_approved'] == "no" || $listing_status == "publish") {
                    $my_post = array('ID' => $post_id, 'post_date' => date("Y-m-d H:i:s"), 'post_status' => 'publish');
                } else {
                    $my_post = array('ID' => $post_id, 'post_date' => date("Y-m-d H:i:s"), 'post_status' => 'pending');
                }
                wp_update_post($my_post);

                $ex_plan_id = listing_get_metabox_by_ID('Plan_id', $post_id);
                $new_plan_id = listing_get_metabox_by_ID('changed_planid', $post_id);
                if (!empty($new_plan_id)) {
                    if ($ex_plan_id != $new_plan_id) {
                        lp_cancel_stripe_subscription($post_id, $ex_plan_id);
                        listing_set_metabox('Plan_id', $new_plan_id, $post_id);
                        listing_set_metabox('changed_planid', '', $post_id);
                    }
                }

                $thepost = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $dbprefix . "listing_orders WHERE post_id = %d", $post_id));

                $current_user = wp_get_current_user();
                $useremail = $current_user->user_email;
                $user_name = $current_user->user_login;
                $admin_email = '';
                $admin_email = get_option('admin_email');

                $listing_id = $post_id;
                $listing_title = get_the_title($post_id);
                $invoice_no = $thepost->order_id;
                $payment_method = $thepost->payment_method;

                $plan_title = $thepost->plan_name;
                $plan_price = isset($result['PAYMENTREQUEST_0_AMT']) ? $result['PAYMENTREQUEST_0_AMT'] : $result['PAYMENTINFO_0_AMT'];
                $plan_price = $plan_price . $thepost->currency;
                $listing_url = get_the_permalink($listing_id);


                //to admin
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
                    'user_name' => "$user_name",
                    'plan_title' => "$plan_title",
                    'plan_price' => "$plan_price",
                    'listing_url' => "$listing_url",
                    'invoice_no' => "$invoice_no",
                    'website_name' => "$website_name",
                    'payment_method' => "$payment_method"
                ));

                $headers1[] = 'Content-Type: text/html; charset=UTF-8';
                lp_mail_headers_append();
                LP_send_mail($admin_email, $formated_mail_subject, $formated_mail_content, $headers1);
                lp_mail_headers_remove();
                // to user

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
                    'user_name' => "$user_name",
                    'listing_title' => "$listing_title",
                    'plan_title' => "$plan_title",
                    'plan_price' => "$plan_price",
                    'listing_url' => "$listing_url",
                    'invoice_no' => "$invoice_no",
                    'website_name' => "$website_name",
                    'payment_method' => "$payment_method"
                ));

                lp_mail_headers_append();
                $headers[] = 'Content-Type: text/html; charset=UTF-8';

                LP_send_mail($useremail, $formated_mail_subject2, $formated_mail_content2, $headers);
                lp_mail_headers_remove();

                /* on 28 march  */
                $packageResult = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $dbprefix . "listing_orders WHERE post_id = %d", $post_id));
                $planID = $packageResult->plan_id;
                $planUsed = $packageResult->used;

                $allowedPosts = '';
                $plan_type = get_post_meta($planID, 'plan_package_type', true);
                if ($plan_type == "Package") {
                    $allowedPosts = get_post_meta($planID, 'plan_text', true);
                }

				$transaction_id = isset($result['PAYMENTINFO_0_TRANSACTIONID']) ? $result['PAYMENTINFO_0_TRANSACTIONID'] : '';
				
                $update_data = array('used' => '1', 'transaction_id' => $transaction_id , 'status' => $status);

                $where = array('token' => $result['TOKEN']);

                $update_format = array('%s', '%s', '%s');

                $wpdb->update($dbprefix . 'listing_orders', $update_data, $where, $update_format);

                if (!empty($allowedPosts) && $allowedPosts == "1") {
                    $update_status = array('status' => 'expired');
                    $wheree = array('plan_id' => $planID);
                    $update_formatt = array('%s');
                    $wpdb->update($dbprefix . 'listing_orders', $update_status, $wheree, $update_formatt);
                }
            } else {
				$transaction_id = isset($result['PAYMENTINFO_0_TRANSACTIONID']) ? $result['PAYMENTINFO_0_TRANSACTIONID'] : '';
                $update_data = array('used' => '1', 'transaction_id' => $transaction_id , 'status' => $status);

                $where = array('token' => $result['TOKEN']);

                $update_format = array('%s', '%s', '%s');

                $wpdb->update($dbprefix . 'listing_orders', $update_data, $where, $update_format);
            }
            if (isset($_SESSION['coupon_code']) && $_SESSION['coupon_code'] != '') {
                listingpro_apply_coupon_code_at_payment($_SESSION['coupon_code'], $post_id, $fields['PAYMENTREQUEST_0_TAXAMT'], $result['PAYMENTREQUEST_0_AMT']);
            }
        }

        function create_recurring_payments_profile($response, $pstatus, $post_id) {
            global $listingpro_options;
            $paypal_api_environment = $listingpro_options['paypal_api'];
            $paypal_success = $listingpro_options['payment_success'];
            $paypal_success = get_permalink($paypal_success);
            $paypal_fail = $listingpro_options['payment_fail'];
            $paypal_fail = get_permalink($paypal_fail);
            $paypal_api_username = $listingpro_options['paypal_api_username'];
            $paypal_api_password = $listingpro_options['paypal_api_password'];
            $paypal_api_signature = $listingpro_options['paypal_api_signature'];
            
            $planID = listing_get_metabox_by_ID('Plan_id', $post_id);
            $new_plan_id = listing_get_metabox_by_ID('changed_planid', $post_id);
            if(isset($new_plan_id) && $new_plan_id > 0 ){
                $planID = $new_plan_id;
            }
            $plan_time = get_post_meta($planID, 'plan_time', true);
            if (!empty($plan_time)) {
                $plan_time = (int) $plan_time;
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POST, true);

            if ($paypal_api_environment == 'sandbox')
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.sandbox.paypal.com/nvp');
            elseif ($paypal_api_environment == 'live')
                curl_setopt($ch, CURLOPT_URL, 'https://api-3t.paypal.com/nvp');

            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
                'USER' => urlencode($paypal_api_username),
                'PWD' => urlencode($paypal_api_password),
                'SIGNATURE' => urlencode($paypal_api_signature),
                'METHOD' => 'CreateRecurringPaymentsProfile',
                'VERSION' => '72.0',
                'TOKEN' => $response['TOKEN'],
                'PayerID' => $response['PAYERID'],
                'PROFILESTARTDATE' => gmdate("Y-m-d\TH:i:s\Z"),
                'DESC' => 'Exemplo',
                'BILLINGPERIOD' => 'Day',
                'BILLINGFREQUENCY' => $plan_time,
                'AMT' => $response['AMT'],
                'CURRENCYCODE' => $response['CURRENCYCODE'],
                'RETURNURL' => urlencode( $paypal_success),
                'CANCELURL' => urlencode($paypal_fail),
            )));

            $resp = curl_exec($ch);

            curl_close($ch);
            parse_str($resp, $respArray);

            $recStatus = array();

            if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $resp, $matches)) {
                foreach ($matches['name'] as $offset => $name) {
                    $recStatus[$name] = urldecode($matches['value'][$offset]);
                }
            }
            if ($recStatus['ACK'] == 'Success' && isset($recStatus['PROFILEID'])) {
                if (isset($recStatus['PROFILESTATUS'])) {
                    if ($recStatus['PROFILESTATUS'] == 'ActiveProfile') {
                        //success recurring
                        $new_subsc = array('plan_id' => $planID, 'subscr_id' => $recStatus['PROFILEID'], 'listing_id' => $post_id);
                        lp_add_new_susbcription_meta($new_subsc);
                        wp_PayPalAPI::UpdatePayment($response, 'success', $post_id);

                        if (isset($_SESSION['lp_paypal_session'])) {
                            unset($_SESSION['lp_paypal_session']);
                        }
                    }
                }
            } else {
                //error
                wp_PayPalAPI::UpdatePayment($response, 'failed', $post_id);
            }
        }

    }

}