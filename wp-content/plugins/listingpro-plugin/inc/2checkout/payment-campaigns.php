<?php
/* function for ajax payment through 2checkout for campaigns */
add_action('wp_ajax_listingpro_2checkout_pay_campaigns', 'listingpro_2checkout_pay_campaigns');
add_action('wp_ajax_nopriv_listingpro_2checkout_pay_campaigns', 'listingpro_2checkout_pay_campaigns');
if(!function_exists('listingpro_2checkout_pay_campaigns')){
	function listingpro_2checkout_pay_campaigns(){

		require_once("lib/Twocheckout.php");
		global $listingpro_options;
		$secid = '';
		$selid = '';
		$token = $_POST['token'];
		$tprice = $_POST['tprice'];
		$listing_id = $_POST['listing_id'];
		$price_packages = $_POST['packages'];
		
		$userid = $_POST['userid'];
		$uname = $_POST['uname'];
		$umail = $_POST['umail'];
		$uphone = $_POST['uphone'];
		$uaddress = $_POST['uaddress'];
		$ucity = $_POST['ucity'];
		$ustate = $_POST['ustate'];
		$ucountry = $_POST['ucountry'];
		$uzip = $_POST['uzip'];
		$taxPrice = 0;
		$pkgPrice = 0;
			
		if(!empty($price_packages)){
			foreach($price_packages as $package){
				$pkgPrice = $pkgPrice + $listingpro_options["$package"];
			}
		}
		if(isset($_POST['taxprice'])){
			if(!empty($_POST['taxprice'])){
				$taxPrice = $_POST['taxprice'];
			}
		}
		$pkgPrice = $pkgPrice+$taxPrice;
		
		
		
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
				"total"      => $pkgPrice,
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
				
				
	
						$currentdate = date("d-m-Y");
		
						$my_post = array(
							'post_title'    => $listing_id,
							'post_status'   => 'publish',
							'post_type' => 'lp-ads',
						);
						$adID = wp_insert_post( $my_post );
						
						listing_set_metabox('ads_listing', $listing_id, $adID);
						
						listing_set_metabox('ad_status', 'Active', $adID);
						listing_set_metabox('ad_date', $currentdate, $adID);
		
						listing_set_metabox('campaign_id', $adID, $listing_id);
						update_post_meta( $listing_id, 'campaign_status', 'active' );
						
						$priceKeyArray;
						if( !empty($price_packages) ){
							foreach( $price_packages as $val ){
								$priceKeyArray[] = $val;
								update_post_meta( $listing_id, $val, 'active' );
							}
						}
						
						if( !empty($priceKeyArray) ){
							
							listing_set_metabox('ad_type', $priceKeyArray, $adID);
						}
						
						$tID = $transactionId;
						$token = $token;
						$payment_method = '2checkout';
						$status = "success";
						
						$responsed = lp_save_2cheeckout_campaign_data($adID, $tID, $payment_method, $token, $status, $price_packages, $pkgPrice, $listing_id);
				
				
				$response = json_encode(array('status'=>'success', 'token'=>$token, 'redirect'=>$paypal_success));
				die($response);
			}
			else{
				$response = json_encode(array('status'=>'error', 'token'=>'', 'msg' => esc_html('Sorry! payment failed', 'listingpro')));
				die($response);
			}
		} catch (Twocheckout_Error $e) {
			$response = json_encode(array('status'=>'error', 'token'=>'', 'msg'=>$e->getMessage()));
			die($response);
		}
		
	}
}

/* function for data submit in listing campaign table for 2checkout */
if(!function_exists('lp_save_2cheeckout_campaign_data')){
	function lp_save_2cheeckout_campaign_data( $adID, $transactionID, $method, $token, $status, $price_packages, $lpTOtalprice = '', $listing_id='' ){
		
		global $wpdb,$listingpro_options;
		$dbprefix = $wpdb->prefix;
		$user_ID = '';
		$user_ID = get_current_user_id();
		$currency_code = '';
		$currency_code = $listingpro_options['currency_paid_submission'];
		$priceKeyArray = 0;
		$packagesDetails ='';
		$enableTax = false;
		$Taxrate='';
		if($listingpro_options['lp_tax_swtich']=="1"){
			$enableTax = true;
			$Taxrate = $listingpro_options['lp_tax_amount'];
		}
		
		//$price_packages = $_SESSION['price_package'];
				if(empty($lpTOtalprice)){
					
					$pkgCnt = 0;
					$packagesDetailsArr = array();
					if( !empty($price_packages) && is_array($price_packages) ){
						foreach( $price_packages as $val ){
							if($val=="lp_random_ads"){
								$packagesDetailsArr[$pkgCnt]= esc_html__(' Random Ads ', 'listingpro');
								$pkgCnt++;
							}
							if($val=="lp_detail_page_ads"){
								$packagesDetailsArr[$pkgCnt]= esc_html__(' Detail Page Ads ', 'listingpro');
							}
							if($val=="lp_top_in_search_page_ads"){
								$packagesDetailsArr[$pkgCnt]= esc_html__(' Top in Search Page Ads ', 'listingpro');
							}
							$taxPrice = 0;
							if($enableTax=="1" && !empty($Taxrate)){
								$taxPrice = ($Taxrate / 100)*$listingpro_options[$val];
								$priceKeyArray = $listingpro_options[$val]+$priceKeyArray+$taxPrice;
							}
							else{
								$priceKeyArray = $listingpro_options[$val]+$priceKeyArray;
							}
							
						}
						
						$packagesDetails = implode( ',', $packagesDetailsArr );
						
					}
					else if(!empty($price_packages) && !is_array($price_packages)){
						
						$taxPrice = 0;
						if($enableTax=="1" && !empty($Taxrate)){
							$taxPrice = ($Taxrate / $priceKeyArray)*100;
							$priceKeyArray = $priceKeyArray+$taxPrice;
						}
						else{
							$priceKeyArray = $price_packages;
						}
					}
				}
				else{
					
					$pkgCnt = 0;
					$packagesDetailsArr = array();
					//$priceKeyArray = $lpTOtalprice;
					if( !empty($price_packages) && is_array($price_packages) ){
						foreach( $price_packages as $val ){
							if($val=="lp_random_ads"){
								$packagesDetailsArr[$pkgCnt]= esc_html__(' Random Ads ', 'listingpro');
								$pkgCnt++;
							}
							if($val=="lp_detail_page_ads"){
								$packagesDetailsArr[$pkgCnt]= esc_html__(' Detail Page Ads ', 'listingpro');
								$pkgCnt++;
							}
							if($val=="lp_top_in_search_page_ads"){
								$packagesDetailsArr[$pkgCnt]= esc_html__(' Top in Search Page Ads ', 'listingpro');
								$pkgCnt++;
							}
							//$priceKeyArray = $listingpro_options[$val]+$priceKeyArray;
							$priceKeyArray = $lpTOtalprice;
						}
						
						$packagesDetails = implode( ',', $packagesDetailsArr );
						
					}
				}

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
			  PRIMARY KEY  (`main_id`)
		 );";
		dbDelta($sql);
		
		$insert_sql ="
				INSERT INTO `".$wpdb->prefix."listing_campaigns` VALUES ('','$user_ID','$adID','$method','$token','$priceKeyArray','$currency_code','$status','$transactionID')";

        dbDelta($insert_sql);
		
		$current_user = wp_get_current_user();
		$user_email = $current_user->user_email;
		$admin_email = get_option('admin_email');
		$listing_title = get_the_title($listing_id);
		$listing_url = get_the_permalink($listing_id);
		$campaign_packages = $packagesDetails;
		
		$author_name = $current_user->user_login;
        /* for admin */
		$subject = $listingpro_options['listingpro_subject_campaign_activate'];
		$mail_content = $listingpro_options['listingpro_content_campaign_activate'];
		
		$website_url = site_url();
		$website_name = get_option('blogname');
		$user_name = $author_name;

		$formated_mail_content = lp_sprintf2("$mail_content", array(
			'website_url' => "$website_url",
			'website_name' => "$website_name",
			'user_name' => "$user_name",
			'campaign_packages' => "$campaign_packages",
			'listing_title' => "$listing_title",
			'listing_url' => "$listing_url",
			'author_name' => "$author_name"
		));
		
		lp_mail_headers_append();
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
        LP_send_mail( $admin_email, $subject, $formated_mail_content, $headers);
		
		 /* for author */
		 
		$subject = $listingpro_options['listingpro_subject_campaign_activate_author'];
		$mail_content = $listingpro_options['listingpro_content_campaign_activate_author'];
		
		$formated_mail_content = lp_sprintf2("$mail_content", array(
			'website_url' => "$website_url",
			'website_name' => "$website_name",
			'user_name' => "$user_name",
			'campaign_packages' => "$campaign_packages",
			'listing_title' => "$listing_title",
			'listing_url' => "$listing_url",
		));
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
        LP_send_mail( $user_email, $subject, $formated_mail_content, $headers);
		lp_mail_headers_remove();
		
	}
}
