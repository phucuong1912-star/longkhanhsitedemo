<?php
/* function for ajax payment through stripe */
add_action('wp_ajax_listingpro_2checkout_pay', 'listingpro_2checkout_pay');
add_action('wp_ajax_nopriv_listingpro_2checkout_pay', 'listingpro_2checkout_pay');
if(!function_exists('listingpro_2checkout_pay')){
	function listingpro_2checkout_pay(){

		require_once("lib/Twocheckout.php");
		global $listingpro_options;
		$secid = '';
		$selid = '';
		$token = $_POST['token'];
		$tprice = $_POST['tprice'];
		$listing_id = $_POST['listing_id'];
		
		$userid = $_POST['userid'];
		$uname = $_POST['uname'];
		$umail = $_POST['umail'];
		$uphone = $_POST['uphone'];
		$uaddress = $_POST['uaddress'];
		$ucity = $_POST['ucity'];
		$ustate = $_POST['ustate'];
		$ucountry = $_POST['ucountry'];
		$uzip = $_POST['uzip'];
		
		$paypal_success = $listingpro_options['payment_success'];
		if(!empty($paypal_success)){
			$paypal_success = get_permalink($paypal_success);
		}
		
		if(isset($listingpro_options['2checkout_private_key'])){
			if(!empty($listingpro_options['2checkout_private_key'])){
				$secid = $listingpro_options['2checkout_private_key'];
			}
		}
		if( isset($listingpro_options['2checkout_acount_number']) ){
			if( !empty($listingpro_options['2checkout_acount_number']) ){
				$selid = $listingpro_options['2checkout_acount_number'];
			}
		}
		$currency = $listingpro_options['currency_paid_submission'];
		Twocheckout::privateKey($secid); //Private Key
		Twocheckout::sellerId($selid); // 2Checkout Account Number
		if( isset($listingpro_options['2checkout_api']) ){
			if( !empty($listingpro_options['2checkout_api']) ){
				if( $listingpro_options['2checkout_api']=="sandbox" ){
					Twocheckout::sandbox(true);
				}
				else{
					Twocheckout::sandbox(false);
				}
			}
			else{
				Twocheckout::sandbox(true);
			}
		}
		else{
			Twocheckout::sandbox(true);
		}

		try {
			$merchandorder = rand();
			$charge = Twocheckout_Charge::auth(array(
				"merchantOrderId" => "$merchandorder",
				"token"      => $token,
				"currency"   => $currency,
				"total"      => $tprice,
					"billingAddr" => array(
					"name" => $uname,
					"addrLine1" => $uaddress,
					"city" => $ucity,
					"state" => $ustate,
					"zipCode" => $uzip,
					"country" => $ucountry,
					"email" => $umail,
					"phoneNumber" => $uphone
				)
			));

			if ($charge['response']['responseCode'] == 'APPROVED') {
				$transactionId = $charge['response']['transactionId'];
				$method = "2checkout";
				$responn = listingpro_2checkout_save_date($userid, $listing_id, $method, $token, $currency, $transactionId);
				$response = json_encode(array('status'=>'success', 'token'=>$token, 'redirect'=>$paypal_success));
				die($response);
			}
			else{
				$response = json_encode(array('status'=>'error', 'token'=>$token, 'msg' => esc_html('Sorry! payment failed', 'listingpro')));
				die($response);
			}
		} catch (Twocheckout_Error $e) {
			$response = json_encode(array('status'=>'error', 'token'=>$token, 'msg'=>$e->getMessage()));
			die($response);
		}
		
	}
}

/* function for data submit in listing order table for 2checkout */

if(!function_exists('listingpro_2checkout_save_date')){
	function listingpro_2checkout_save_date($userid, $listing_id, $method, $token, $currency, $transactionId){
		global $listingpro_options, $wpdb;
		$dbprefix = $wpdb->prefix;
		$author_obj = get_user_by('id', $userid);
		$user_email = $author_obj->user_email;
		$status = 'success';
		
		$my_listing;
		if($listingpro_options['listings_admin_approved']=="no"){
			$my_listing = array( 'ID' => $listing_id, 'post_date'  => date("Y-m-d H:i:s"), 'post_status'   => 'publish' );
		}
		else{
			$my_listing = array( 'ID' => $listing_id, 'post_date'  => date("Y-m-d H:i:s"), 'post_status'   => 'pending' );
		}
		wp_update_post( $my_listing );
		
		$ex_plan_id = listing_get_metabox_by_ID('Plan_id', $listing_id);
		$new_plan_id = listing_get_metabox_by_ID('changed_planid', $listing_id);
		if(!empty($new_plan_id)){
			if( $ex_plan_id != $new_plan_id ){
				lp_cancel_stripe_subscription($listing_id, $ex_plan_id);
				listing_set_metabox('Plan_id',$new_plan_id, $listing_id);
				listing_set_metabox('changed_planid','', $listing_id);
			}
		}
		
		$thepost = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$dbprefix."listing_orders WHERE post_id = %d", $listing_id ) );
		
		$listing_title = get_the_title($listing_id);
		$invoice_no = $thepost->order_id;
		$date = date(get_option('date_format'));
		
		$update_data = array('currency' => $currency,
									   'date' => $date,
									   'status' => $status,
									   'used' => '1',
									   'transaction_id' => $transactionId,
									   'description' => 'listing has been purchased',
									   'payment_method' => $method,
									   'summary' => $status,
									   'token' => $token);
		
		$update_format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');
		$where = array('post_id' => $listing_id);
		$wpdb->update($dbprefix.'listing_orders', $update_data, $where, $update_format);
		
		
		$plan_id = listing_get_metabox_by_ID('Plan_id', $listing_id);
		$allowedPosts = get_post_meta($planID, 'plan_text' ,true);
		if(!empty($allowedPosts) && $allowedPosts=="1"){
					$update_status = array('status' => 'expired');
					$wheree = array('plan_id' => $plan_id);
					$update_formatt = array('%s');
					$wpdb->update($dbprefix.'listing_orders', $update_status, $wheree, $update_formatt);
		}
		
		
		
		$admin_email = get_option( 'admin_email' );
		$current_user = wp_get_current_user();
		$useremail = $current_user->user_email;
		
		$listing_title = get_the_title($listing_id);
		$payment_method = $method;
		
		$plan_title = $thepost->plan_name;
		$plan_price = $thepost->price.$thepost->currency;
		$listing_url = get_the_permalink($listing_id);
		$user_name = $current_user->user_login;
		
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
        LP_send_mail( $admin_email, $formated_mail_subject, $formated_mail_content, $headers1);
		
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
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
        LP_send_mail( $useremail, $formated_mail_subject2, $formated_mail_content2, $headers);
		lp_mail_headers_remove();
		
	}
}