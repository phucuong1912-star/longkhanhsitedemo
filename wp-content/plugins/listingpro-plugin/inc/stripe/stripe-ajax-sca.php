<?php
/* ======================================= listing payment request ======================= */
add_action('wp_ajax_lp_sca_stripe_action', 'lp_sca_stripe_action_cb');
if (!function_exists('lp_sca_stripe_action_cb')) {
    function lp_sca_stripe_action_cb() {
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('status' => 'error' , 'message' => 'nonceerror'));
			die($res);
		}
        global $wpdb, $listingpro_options, $wp_rewrite;
		$secret_key = lp_theme_option('stripe_secrit_key');
		$current_user = wp_get_current_user();
		$product_id = $price_id = '';
		$planID = sanitize_text_field($_POST['plan_id']);
		$amount = sanitize_text_field($_POST['amount']);
		$listing_id = sanitize_text_field($_POST['listing_id']);
		$coupon = sanitize_text_field($_POST['coupon']);
		$amount = $amount * 100;
		//creation of new product
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $secret_key,
			),
			'body' => array(
				'name' => get_the_title( $listing_id ) . '\'s '.get_the_title($planID) . ' Plan',
			),
		);
		$response = wp_remote_post('https://api.stripe.com/v1/products', $args);
		if ( is_wp_error( $response ) ) {
			$return['status']   =   'error';
			$return['message'] = $response->get_error_message();
			die(json_encode($return));
		} else {
			$json_response = json_decode( wp_remote_retrieve_body( $response ), true );
			if( isset( $json_response['id'] ) ){
				$product_id =  $json_response['id'];
			}
		}
		if( $product_id ){
			$coupon_id = '';
			//check if coupon needs to be applied with recurring
			if( $_POST['recurring'] == 'yes' ){
				if (!empty($coupon)) {
					$planpriceOR = get_post_meta($planID, 'plan_price', true);
					$existingCoupon = lp_get_existing_coupons();
					if (!empty($existingCoupon)) {
						$returnKey = lp_search_coupon_in_array($coupon, $existingCoupon);
						if (isset($returnKey)) {
							$couponData = $existingCoupon[$returnKey];
							if (!empty($couponData)) {
								$couponType = '';
								if (isset($couponData['copontype']) && !empty($couponData['copontype']) ) {
									$couponType = $couponData['copontype'];
								}
								
								$discount = $couponData['discount'];
								$couponID = $coupon . rand();
								$disType = '';
								
								if (!empty($couponType)) {
									$discount = (float) $discount * 100;
									$discount = round($discount, 2);
									$discount = (int) $discount;
									$disType = 'amount_off';
								} else {
									$disType = 'percent_off';
								}
								if (lp_theme_option('lp_tax_swtich') == "1") {
									$taxrate = lp_theme_option('lp_tax_amount');
									$taxrate = ($taxrate / 100) * $planpriceOR;
								}
								$planpriceOR = $planpriceOR + $taxrate;
								$planpriceOR = (float) $planpriceOR * 100;
								$planpriceOR = round($planpriceOR, 2);
								$planpriceOR = (int) $planpriceOR;
								$amount = $planpriceOR;
								$args = array(
									'headers' => array(
										'Authorization' => 'Bearer ' . $secret_key,
									),
									'body' => array(
										'duration' => 'once',
										'name' => $coupon,
									),
								);
								if( $disType == 'amount_off' ){
									$args['body']['currency'] = $_POST['currency'];
									$args['body']['amount_off'] = $discount;
								}else{
									$args['body']['percent_off'] = $discount;
								}
								$coupon_res = wp_remote_post('https://api.stripe.com/v1/coupons', $args);
								if ( is_wp_error( $coupon_res ) ) {
									$return['status']   =   'error';
									$return['message'] = $coupon_res->get_error_message();
									die(json_encode($return));
								} else {
									$json_coupon = json_decode( wp_remote_retrieve_body( $coupon_res ), true );
									if( isset( $json_coupon['id'] ) ){
										$coupon_id =  $json_coupon['id'];
									}
								}
							}
						}
					}
					
				}
			}
			$plan_time  = get_post_meta($planID, 'plan_time', true);
			//creation of new price associated to above created product
			$args = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $secret_key,
				),
				'body' => array(
					'unit_amount' => $amount,
					'currency' => lp_theme_option('currency_paid_submission'),
					'product' => $product_id,
				),
			);
			if( $_POST['recurring'] == 'yes' ){
				$args['body']['recurring']['interval'] = 'day';
				$args['body']['recurring']['interval_count'] = $plan_time;
			}
			$response = wp_remote_post('https://api.stripe.com/v1/prices', $args);
			if ( is_wp_error( $response ) ) {
				$return['status']   =   'error';
				$return['message'] = $response->get_error_message();
				die(json_encode($return));
			} else {
				$json_response = json_decode( wp_remote_retrieve_body( $response ), true );
				if( isset( $json_response['id'] ) ){
					$price_id =  $json_response['id'];
				}
			}
		}
		if( $price_id ){
			//creation of new session payment intent based on price_id
			$lpURLChar = '?';
			if ($wp_rewrite->permalink_structure == '') {
				$lpURLChar = '&';
			}
			$success_url = $listingpro_options['payment-checkout'];
			if (!empty($success_url)) {
				$cancel_url = get_permalink($success_url);
				$success_url = get_permalink($success_url);
				$success_url .=$lpURLChar . 'lpcheckstatus=success';
				$cancel_url .=$lpURLChar . 'lpcheckstatus=fail';
			}
			$mode = 'payment';
			if( $_POST['recurring'] == 'yes' ){
				$mode = 'subscription';
			}
			$args = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $secret_key,
				),
				'body' => array(
					'success_url' => $success_url,
					'cancel_url' => $cancel_url,
					'line_items' => array( array(
						'price' => $price_id,
						'quantity' => 1,
					) ),
					'metadata' => array(
						'plan_id' => $planID,
						'taxRate' => sanitize_text_field($_POST['taxRate']),
						'recurring' => sanitize_text_field($_POST['recurring']),
						'listing_id' =>  $listing_id,
						'callFrom' => sanitize_text_field($_POST['callFrom']),
						'user_id' => get_current_user_id(),
						'coupon' => sanitize_text_field($_POST['coupon']),
						'currency' => sanitize_text_field($_POST['currency']),
					),
					'mode' => $mode,
				),
			);
			if( !empty($coupon_id) ){
				$args['body']['discounts'] = array(
					array( 'coupon' => $coupon_id )
				);
			}
			$response = wp_remote_post('https://api.stripe.com/v1/checkout/sessions', $args);
			if ( is_wp_error( $response ) ) {
				$return['status']   =   'error';
				$return['message'] = $response->get_error_message();
				die(json_encode($return));
			} else {
				//successful creation of payment object and redirection setup of user to pay
				$json_response = json_decode( wp_remote_retrieve_body( $response ), true );
				if(  isset($json_response['id']) && isset($json_response['url']) ){
					//save session id to user_meta to check status after payment
					update_user_meta( get_current_user_id() , 'session_id' , $json_response['id'] );
					$return['status']   =   'success';
					$return['redirect_url'] = $json_response['url'];
					die(json_encode($return));
				}else if( isset($json_response['error']) ){
					$return['status']   =   'error';
					$return['message'] = $json_response['error']['message'];
					die(json_encode($return));
				}
			}
		}
    }

}

add_action('wp_ajax_lp_sca_stripe_campaign_action', 'lp_sca_stripe_campaign_action_cb');

/* ======================================= campaign request ======================= */
if (!function_exists('lp_sca_stripe_campaign_action_cb')) {
    function lp_sca_stripe_campaign_action_cb() {
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('status' => 'error' , 'message' => 'nonceerror'));
			die($res);
		}
        global $wpdb, $listingpro_options, $wp_rewrite;
		$secret_key = lp_theme_option('stripe_secrit_key');
		$current_user = wp_get_current_user();
		$product_id = $price_id = '';
		$amount = sanitize_text_field($_POST['amount']);
		$packages = implode(",",$_POST['packages']);
		$listing_id = sanitize_text_field($_POST['pinfo']);
		$amount = $amount * 100;
		//creation of new product
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $secret_key,
			),
			'body' => array(
				'name' => get_the_title( $listing_id ) . '\'s Ads Campaign',
			),
		);
		$response = wp_remote_post('https://api.stripe.com/v1/products', $args);
		if ( is_wp_error( $response ) ) {
			$return['status']   =   'error';
			$return['message'] = $response->get_error_message();
			die(json_encode($return));
		} else {
			$json_response = json_decode( wp_remote_retrieve_body( $response ), true );
			if( isset( $json_response['id'] ) ){
				$product_id =  $json_response['id'];
			}
		}
		if( $product_id ){
			//creation of new price associated to above created product
			$plan_time  = get_post_meta($planID, 'plan_time', true);
			$args = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $secret_key,
				),
				'body' => array(
					'unit_amount' => $amount,
					'currency' => lp_theme_option('currency_paid_submission'),
					'product' => $product_id,
				),
			);
			$response = wp_remote_post('https://api.stripe.com/v1/prices', $args);
			if ( is_wp_error( $response ) ) {
				$return['status']   =   'error';
				$return['message'] = $response->get_error_message();
				die(json_encode($return));
			} else {
				$json_response = json_decode( wp_remote_retrieve_body( $response ), true );
				if( isset( $json_response['id'] ) ){
					$price_id =  $json_response['id'];
				}
			}
		}
		if( $price_id ){
			//creation of new session payment intent based on price_id
			$lpURLChar = '?';
			if ($wp_rewrite->permalink_structure == '') {
				$lpURLChar = '&';
			}
			$success_url = $listingpro_options['payment-checkout'];
			if (!empty($success_url)) {
				$cancel_url = get_permalink($success_url);
				$success_url = get_permalink($success_url);
				$success_url .=$lpURLChar . 'lpcheckstatus=success';
				$cancel_url .=$lpURLChar . 'lpcheckstatus=fail';
			}
			$mode = 'payment';
			$args = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $secret_key,
				),
				'body' => array(
					'success_url' => $success_url,
					'cancel_url' => $cancel_url,
					'line_items' => array( array(
						'price' => $price_id,
						'quantity' => 1,
					) ),
					'metadata' => array(
						'adsDays' => sanitize_text_field($_POST['adsDays']),
						'adsTypeVal' => sanitize_text_field($_POST['adsTypeVal']),
						'packages' => $packages,
						'taxPrice' => sanitize_text_field($_POST['taxPrice']),
						'totalPrice' => sanitize_text_field($_POST['totalPrice']),
						'listing_id' =>  $listing_id,
						'callFrom' => 'campaign',
						'user_id' => get_current_user_id(),
						'currency' => sanitize_text_field($_POST['currency']),
					),
					'mode' => $mode,
				),
			);
			$response = wp_remote_post('https://api.stripe.com/v1/checkout/sessions', $args);
			if ( is_wp_error( $response ) ) {
				$return['status']   =   'error';
				$return['message'] = $response->get_error_message();
				die(json_encode($return));
			} else {
				//successful creation of payment object and redirection setup of user to pay
				$json_response = json_decode( wp_remote_retrieve_body( $response ), true );
				if(  isset($json_response['id']) && isset($json_response['url']) ){
					//save session id to user_meta to check status after payment
					update_user_meta( get_current_user_id() , 'session_id' , $json_response['id'] );
					$return['status']   =   'success';
					$return['redirect_url'] = $json_response['url'];
					die(json_encode($return));
				}else if( isset($json_response['error']) ){
					$return['status']   =   'error';
					$return['message'] = $json_response['error']['message'];
					die(json_encode($return));
				}
			}
		}
    }
}
/* ======================================= payment data saved ======================= */
add_action('init', 'listingpro_sca_stripe_save_payment');
if (!function_exists('listingpro_sca_stripe_save_payment')) {
    function listingpro_sca_stripe_save_payment() {
		$session_id = get_user_meta(get_current_user_id() , 'session_id' , true );
		if( !empty($session_id) ){
			$secret_key = lp_theme_option('stripe_secrit_key');
			$url = 'https://api.stripe.com/v1/checkout/sessions/' . $session_id;
			$response = wp_remote_get($url, array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $secret_key,
					'Content-Type' => 'application/json',
				),
			));
			if (!is_wp_error($response) && isset($response['body'])) {
				$response_obh = json_decode($response['body'], true);
				if (isset($response_obh['id']) && $response_obh['payment_status'] == 'paid') {
					global $wpdb, $listingpro_options;
					$callFrom = $response_obh['metadata']['callFrom'];
					if( $callFrom == 'listing' || $callFrom == 'claim' ){
						$dbprefix = $wpdb->prefix;
						$coupon         = 	$response_obh['metadata']['coupon'];
						$planID         = 	$response_obh['metadata']['plan_id'];
						$recurring      = 	$response_obh['metadata']['recurring'];
						$listing        = 	$response_obh['metadata']['listing_id'];
						$currency       = 	$response_obh['metadata']['currency'];
						$taxRate		= 	$response_obh['metadata']['taxRate'];
						$subsrID       	= 	$response_obh['subscription'];
						$planprice 		= 	'';
						if( isset( $response_obh['subscription'] ) && !empty($response_obh['subscription'])  ){
							$subs_url = 'https://api.stripe.com/v1/subscriptions/' . $response_obh['subscription'];
							$subscription_res = wp_remote_get($subs_url, array(
								'headers' => array(
									'Authorization' => 'Bearer ' . $secret_key,
									'Content-Type' => 'application/json',
								),
							));
							if (!is_wp_error($subscription_res) && isset($subscription_res['body'])) {
								$subs_data = json_decode($subscription_res['body'], true);
								$token = $subs_data['default_payment_method'];
							}
						}else{
							$token = $response_obh['payment_intent'];
						}
						if(empty($planprice)){
							$planprice = get_post_meta($planID, 'plan_price', true);
							$planpriceorg = get_post_meta($planID, 'plan_price', true);
						}
						$claimPost = get_post_meta($listing, 'claimpID', true);
						if(isset($claimPost) && $callFrom == 'claim'){
							if(!empty($claimPost)){
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
								lp_update_paid_claim_metas($claimPost, $listing, 'payu-in');
								delete_post_meta($listing, 'claimpID');
							}
						}
						$planprice        = round($planprice, 2);
						$planprice        = (int)$planprice;
						$planpriceINVOICE = number_format(($response_obh['amount_total']/100), 2, '.', '');
						if( !empty($taxRate) ){
							$taxRate = $planprice*$taxRate;
							$taxRate = $taxRate/100;
						}
						$status           = 'success';
						$payment_method   = 'stripe';
						$user_id          = get_current_user_id();
						// Updating Listing Status
						$listing_status = get_post_status( $listing );
						if($listingpro_options['listings_admin_approved']=="no" || $listing_status=="publish" ){
							$my_post = array( 'ID' => $listing, 'post_date'  => date("Y-m-d H:i:s"), 'post_status'   => 'publish' );
						}
						else{
							$my_post = array( 'ID' => $listing, 'post_date'  => date("Y-m-d H:i:s"), 'post_status'   => 'pending' );
						}
						wp_update_post( $my_post );						
						if( $recurring == 'yes' ){
							$planDuration = (int) get_post_meta( $planID , 'plan_time', true);
							if ( empty($planDuration) || !is_numeric($planDuration) ) {
								$planDuration = 0;
							}
							$new_subsc = array('plan_id' => $planID, 'subscr_id' => $subsrID, 'listing_id' => $listing);
							lp_add_new_susbcription_meta($new_subsc);	
						}
						// Applying Coupon Code
						if(isset($coupon) && !empty($coupon)){
							listingpro_apply_coupon_code_at_payment( $coupon, $listing, $taxRate, $planprice );
						}
						/// Update New Plan ID & Remove previous subscription
						$ex_plan_id  = listing_get_metabox_by_ID('Plan_id', $listing);
						$new_plan_id = listing_get_metabox_by_ID('changed_planid', $listing);
						if(!empty($new_plan_id)){
							if( $ex_plan_id != $new_plan_id ){
								//lp_cancel_stripe_subscription($listing, $ex_plan_id);
								listing_set_metabox('Plan_id',$new_plan_id, $listing);
								listing_set_metabox('changed_planid','', $listing);
								listing_draft_save($listing, $user_id);
							}
						} 
						$listing_order = $wpdb->get_row( "SELECT * FROM ". $wpdb->prefix ."listing_orders WHERE post_id = $listing AND plan_id = $planID ORDER BY main_id DESC LIMIT 0, 1", ARRAY_A );
						$order_id      = isset($listing_order['order_id']) ? $listing_order['order_id'] : 0;
						$currency_position = lp_theme_option('pricingplan_currency_position');
						if ($currency_position == 'right') {
							$plan_price = $planpriceINVOICE . $currency;
						}else{
							$plan_price  = $currency . $planpriceINVOICE;
						}
						$plan_title    = isset($listing_order['plan_name']) ? $listing_order['plan_name'] : '';
						$invoice_no    = $order_id;

						$update_data = array(
							'currency'          => $currency,
							'date'              => date('d-m-Y'),
							'status'            => $status,
							'description'       => 'listing has been purchased',
							'payment_method'    => $payment_method,
							'summary'           => $recurring == 'yes' ? 'recurring' : $status,
							'price'             => $planpriceINVOICE,
							'tax'               => $taxRate,
							'token'             => $token,
							'transaction_id'    => $token,
							'status'            => $status,
							'used'              => 1
						);
						$where         = array('order_id' => $order_id);
						$update_format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s');
						$wpdb->update($wpdb->prefix.'listing_orders', $update_data, $where, $update_format);
						$admin_email     = get_option( 'admin_email' );
						$website_url     = site_url();
						$website_name    = get_option('blogname');
						$current_user    = wp_get_current_user();
						$useremail       = $current_user->user_email;
						$user_name       = $current_user->user_login;
						$listing_title   = esc_html(get_the_title($listing));
						$listing_url     = esc_url(get_the_permalink($listing));
						/// Sending Admin Email Notification
						$mail_subject = $listingpro_options['listingpro_subject_purchase_activated_admin'];
						$formated_mail_subject = lp_sprintf2("$mail_subject", array(
							'website_url'   => "$website_url",
							'website_name'  => "$website_name",
							'user_name'     => "$user_name",
						));
						$mail_content = $listingpro_options['listingpro_content_purchase_activated_admin'];
						$formated_mail_content = lp_sprintf2( "$mail_content", 
							array(
								'website_url'         => "$website_url",
								'listing_title'       => "$listing_title",
								'plan_title'          => "$plan_title",
								'plan_price'          => "$plan_price",
								'listing_url'         => "$listing_url",
								'invoice_no'          => "$invoice_no",
								'website_name'        => "$website_name",
								'payment_method'      => "$payment_method",
								'user_name'           => "$user_name",
							)
						);
						lp_mail_headers_append();
						$headers1[] = 'Content-Type: text/html; charset=UTF-8';
						LP_send_mail( $admin_email, $formated_mail_subject, $formated_mail_content, $headers1);

						/// Sending Listing Owner Email Notification
						$mail_subject2 = $listingpro_options['listingpro_subject_purchase_activated'];
						$formated_mail_subject2 = lp_sprintf2( "$mail_subject2", 
							array(
								'website_url'   => "$website_url",
								'website_name'  => "$website_name",
								'user_name'     => "$user_name",
							)
						);
						$mail_content2 = $listingpro_options['listingpro_content_purchase_activated'];
						$formated_mail_content2 = lp_sprintf2( "$mail_content2", 
							array(
								'website_url'        => "$website_url",
								'listing_title'      => "$listing_title",
								'plan_title'         => "$plan_title",
								'plan_price'         => "$plan_price",
								'listing_url'        => "$listing_url",
								'invoice_no'         => "$invoice_no",
								'website_name'       => "$website_name",
								'payment_method'     => "$payment_method",
								'user_name'          => "$user_name",
							)
						);

						lp_mail_headers_append();
						$headers[] = 'Content-Type: text/html; charset=UTF-8';
						LP_send_mail( $useremail, $formated_mail_subject2, $formated_mail_content2, $headers);
						lp_mail_headers_remove();
						update_user_meta( get_current_user_id() , 'session_id' , '' );
					}else if( $callFrom == 'campaign' ){
						$adsDays        = 	$response_obh['metadata']['adsDays'];
						$adsTypeVal     = 	$response_obh['metadata']['adsTypeVal'];
						$price_packages = 	explode( ",", $response_obh['metadata']['packages']);
						$listing        = 	$response_obh['metadata']['listing_id'];
						$currency       = 	$response_obh['metadata']['currency'];
						$taxPrice		= 	$response_obh['metadata']['taxPrice'];
						$totalPrice		= 	$response_obh['metadata']['totalPrice'];
						$user_id		= 	$response_obh['metadata']['user_id'];
						$token = $response_obh['payment_intent'];
						$amount = number_format(($response_obh['amount_total']/100), 2, '.', '');
						$budget = $amount;
						$payment_method = 'stripe';
						$status = 'success';
						lp_save_campaign_data($price_packages, $token, $payment_method, $token, $status, $amount, $budget, $listing, $adsTypeVal, $adsDays, $totalPrice, $taxPrice);
						update_user_meta( get_current_user_id() , 'session_id' , '' );
					}
				}
			}else{
				update_user_meta( get_current_user_id() , 'session_id' , '' );
			}
		}
	}
}
