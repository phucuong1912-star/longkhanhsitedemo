<?php
	/* 
		This class will only work with listingpro theme
	*/
	if(!class_exists('LP_payment')){
		class LP_payment{
			
			/* ============== defaul constructure ====================== */
			public function __construct(){
				/* constructure code if needed*/
			}
			/* ============== payment success page ===================== */
			public function lp_get_success_page(){
				$payment_success = lp_theme_option('payment_success');
				$payment_success = get_permalink( $payment_success );
				return $payment_success;
			}
			
			/* ============== payment error page ===================== */
			public function lp_get_error_page(){
				$payment_fail = lp_theme_option('payment_fail');
				$payment_fail = get_permalink( $payment_fail );
				return $payment_fail;
			}
			
			/* ============== get total price ===================== */
			public function lp_get_total_purchase_price($listing_id, $plan_id){
				$totalPrice = 0;
				$taxPrice = 0;
				//here need check for listing_id if it has plan type month based or yearly
				$plan_price = get_post_meta($plan_id, 'plan_price', true);
				$is_Enable = lp_theme_option('lp_tax_swtich');
				if($is_Enable=='1'){
					$Taxrate = lp_theme_option('lp_tax_amount');
					$taxPrice = (float)($Taxrate*$plan_price);
					$taxPrice = (float)($Taxrate/100);
				}
				
				$totalPrice = $plan_price + $taxPrice;
				/* $totalPrice = round($totalPrice, 2); */
				return $totalPrice;
			}
			
			/* ============== save payment data in db */
			public function lp_save_payment_details_in_db($dataArray = array()){
				$queryStatus = null;
				if(!empty($dataArray)){
					
					$listing_id = '';
					if(isset($dataArray['listing_id'])){
						$listing_id = $dataArray['listing_id'];
					}
					$status = '';
					if(isset($dataArray['status'])){
						$status = $dataArray['status'];
					}
					$description = '';
					if(isset($dataArray['description'])){
						$description = $dataArray['description'];
					}
					$summary = '';
					if(isset($dataArray['summary'])){
						$summary = $dataArray['summary'];
					}
					$token = '';
					if(isset($dataArray['token'])){
						$token = $dataArray['token'];
					}
					$translation_id = '';
					if(isset($dataArray['translation_id'])){
						$translation_id = $dataArray['translation_id'];
					}
					
					if(!empty($listing_id) && !empty($status) && !empty($description) && !empty($summary) && !empty($token) && !empty($translation_id)){
						//save info now
						$where = array();
						$ord_num = listing_get_metabox_by_ID('order_id', $listing_id);
						if(!empty($ord_num)){
							$where = array('order_id' => $ord_num);
						}else{
							$where = array('post_id' => $listing_id);
						}
						
						global $wpdb;
						$dbprefix = $wpdb->prefix;
						$date = date('d-m-Y');
						$update_data = array(
							'token' => $token,	
							'date' => $date,
							'status' => $status,
							'used' => '1',
							'translation_id' => $translation_id,
							'description' => $description,
							'summary' => $summary,
                        );
						
						$queryStatus = $wpdb->update($dbprefix.'listing_orders', $update_data, $where);
						
					}
					
					
				}
				return $queryStatus;
			}
			
			/* ============== save data during listing pending if it is selected any plan */
			public function lp_save_initial_plan_data_in_db(){
				$queryStatus = null;
				
				return $queryStatus;
			}
			
		}
	}
?>