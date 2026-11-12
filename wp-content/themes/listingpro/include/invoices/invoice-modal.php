<?php

if(isset($_GET['lp_p_id'])){
	require_once( dirname(dirname( dirname( dirname(dirname( dirname( __FILE__ )))))) . '/wp-load.php' );
	$rowid = esc_html($_GET['lp_p_id']);
	$lp_invoice = esc_html($_GET['lp_invoice']);
	$results = '';
	
	$invoice = '';
	$date  = '';
	$to = '';
	$emailid = '';
	$company = '';
	$address = '';
	$price = '';
	$tax = '';
	$currency = '';
	$packagetype = '';
	$packagename = '';
	$addtionalinfo = '';
	global $wpdb, $listingpro_options;
	$logo = $listingpro_options['invoice_logo']['url'];
	$company = $listingpro_options['invoice_company_name'];
	$address = $listingpro_options['invoice_address'];
	$addtionalinfo = $listingpro_options['invoice_additional_info'];
	$lp_tax_swtich = $listingpro_options['lp_tax_swtich'];
	
	
	
		$prefix = '';
		$prefix = $wpdb->prefix;
		$table_name = '';
		if(!empty($lp_invoice) && $lp_invoice=="listinvoice"){
			$table_name = $prefix.'listing_orders';
		}
		else if(!empty($lp_invoice) && $lp_invoice=="adsinvoice"){
			$table_name = $prefix.'listing_campaigns';
		}
		
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
			
			if( !empty($rowid)){
				//return on admin side
				if(!empty($lp_invoice) && $lp_invoice=="listinvoice"){
					$results = $wpdb->get_results( 
									$wpdb->prepare("SELECT * FROM $table_name WHERE main_id=%s", $rowid) 
								 );
					foreach( $results as $data ){
						$invoice = $data->order_id;
						$plan_id = $data->plan_id;
						$orgprice = get_post_meta($plan_id, 'plan_price', true);
						$date  = $data->date;
						$uid = $data->user_id;
						$user = get_user_by( 'id', $uid );
						$to = $user->display_name;
						$emailid = $user->user_email;
						
						$price = $data->price;
						
						$currency = listingpro_currency_sign();
						$packagetype = $data->plan_type;
						$packagename = $data->plan_name;
						$tax_price = '';
						$package_price = 0;
						$package_price = get_post_meta($plan_id, 'plan_price', true);
						
						if($package_price!=$price){
							$tax_price = $price - $package_price;
							$tax = true;
						}						
					}
				}
				else{
					$results = $wpdb->get_results( 
									$wpdb->prepare("SELECT * FROM $table_name WHERE main_id=%s", $rowid) 
								 );
					foreach( $results as $data ){
						$adID = '';
						$adID = $data->post_id;
						$packagetype = listing_get_metabox_by_ID('ad_type', $adID);
						$packagename = $packagetype;
						
						$date = listing_get_metabox_by_ID('ad_date', $adID);
						$invoice = $data->transaction_id;
						$uid = $data->user_id;
						$user = get_user_by( 'id', $uid );
						$to = $user->display_name;
						$emailid = $user->user_email;
						$price = $data->price;
						$tax_price = '';
						$tPrice = 0;
						if(!empty($packagename)){
							foreach($packagename as $key){
								$tPrice = $tPrice + $listingpro_options["$key"];
							}
						}
						if($tPrice!=$price){
							$tax_price = $price - $tPrice;
							$tax = true;
						}
						
						$currency = listingpro_currency_sign();
						$campListingName = listing_get_metabox_by_ID('ads_listing', $adID);
						$campListingName = get_the_title($campListingName);
					}
				}
							
				
			}
			else{
				$results = esc_html__('no data', 'listingpro');
			}
		}
		else{
			$results = esc_html__('no table data', 'listingpro');
		}
	
	$output = null;
	
	$output = '
	
	<div class="col-md-12 modal-dialog" role="document">
		<div class="lp-list-detail">
			<div class="lp-detail-header">
				<div class="col-md-6">
					<img alt="image" src="'.$logo.'"/>
					<div class="lp-list-address">
						<p><strong> '.$to.' </strong></p>
						<p>'.$emailid.' </p>
					</div>
				</div>
				<div class="col-md-6">
					<div class="lp-list-date pull-right">
						<p><strong>'.esc_html__(' INVOICE ','listingpro').'</strong> : '.$invoice.' </p>
						<p><strong>'.esc_html__('DATE','listingpro').'</strong> : '.date("M j, Y", strtotime($date)).' </p>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				
			</div>
			<div class="col-md-6">
				<div class="lp-addres-com-detail pull-right">
				</div>
			</div>
			<div class="col-md-12">
				<table class="table invoice-total">
					<tbody>';
					if(!empty($lp_invoice) && $lp_invoice=="listinvoice"){
						if($packagetype=="Pay Per Listing"){
							$packagetype = esc_html__('Pay Per Listing', 'listingpro');
						}
						else{
							$packagetype = esc_html__('Package', 'listingpro');
						}
						$output .='
						<tr>
							<td class="lp-lst-info"><strong>'.esc_html__("Plan Type","listingpro").'</strong></td>
							<td class="lp-lst-info">'.$packagetype.'</td>
						</tr>
						<tr>
							<td class="lp-lst-info"><strong>'.esc_html__("Plan Name","listingpro").'</strong></td>
							<td class="lp-lst-info">'.$packagename.'</td>
						</tr>
						';
					}
					else{
						
						$output .='
						<tr>
							<td class="lp-lst-info"><strong>'.esc_html__("Ad For","listingpro").'</strong></td>
							<td class="lp-lst-info">'.$campListingName.'</td>
						</tr>';
						
					}
					
					if(!empty($tax) && !empty($price)){
						$output .='
							<tr>
								<td class="description"><strong>'.esc_html__('Tax','listingpro').'</strong></td>
								<td class="amount"><strong>'.esc_html__('Tax on price:','listingpro').'</strong> '.$currency.''.$tax_price.' </td>
							</tr>';
					}
					$output .='	
						<tr>
							<td class="description"><strong>'.esc_html__('Payment Total','listingpro').'</strong></td>
							<td class="amount"><strong>'.esc_html__('Total Price:','listingpro').'</strong> '.$currency.''.$price.' </td>
						</tr>
					</tbody>
				</table>
				<div class="">
					<p><strong>'.esc_html__('Additional Information:','listingpro').'</strong></p>
					<p>'.$addtionalinfo.'</p>
				</div>
				<div class="lp-addres-to-detail pull-left">
					<div class="lp-list-address">
						<p><strong>'.esc_html__('Company:','listingpro').'</strong> '.$company.'</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	';
	
	echo wp_kses_post( $output );
	die();
}
?>