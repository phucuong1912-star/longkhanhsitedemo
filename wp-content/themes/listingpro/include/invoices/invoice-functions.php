<?php

/* ====================== for campaign wire===================== */

if (!function_exists('get_campaign_wire_invoice')) {
	function get_campaign_wire_invoice($postid)
	{

		global $listingpro_options;
		$output = null;
		$logo = '';
		$company = '';
		$address = '';
		$phone = '';
		$additional = '';
		$thanku_text = '';
		$user_name = '';
		$taxIsOn = $listingpro_options['lp_tax_swtich'];
		$tax = '';

		$logo = $listingpro_options['invoice_logo']['url'];
		$company = $listingpro_options['invoice_company_name'];
		$address = $listingpro_options['invoice_address'];
		$phone = $listingpro_options['invoice_phone'];
		$additional = $listingpro_options['invoice_additional_info'];
		$thanku_text = $listingpro_options['invoice_thankyou'];
		$userrow = '';
		$userID = '';
		$userID = get_current_user_id();
		$table = 'listing_campaigns';
		$data = '*';
		$postid = absint( $postid );
		$condition = "post_id = $postid";
		$userrow = lp_get_data_from_db($table, $data, $condition);

		$plan_name = '';
		$plan_price = '';
		$org_plan_price = '0';
		$currency = listingpro_currency_sign();
		$invoice = '';
		$payment_method = '';
		$plansData = '';
		$duration = '';
		$currency_position = lp_theme_option('pricingplan_currency_position');

		if (is_array($userrow) && count($userrow) > 0) {

			$plan_price = $userrow[0]->price;
			//$plan_name = get_the_title($postid);
			$invoice = $userrow[0]->transaction_id;
			$payment_method = $userrow[0]->payment_method;
			$duration = $userrow[0]->duration;
		}

		$subtotalPrice = 0;

		$plan_price = (float)$plan_price;

		$typeofcampaign = lp_theme_option('listingpro_ads_campaign_style');
		if ($typeofcampaign == "adsperclick") {
			$tax = $listingpro_options['lp_tax_amount'];
			if (!empty($taxIsOn) && !empty($tax)) {
				$subtotalPrice = $plan_price - $tax;
			} else {
				$subtotalPrice = $plan_price;
			}
		} else {
			$price_packages = $_SESSION['price_package'];
			if (!empty($price_packages) && is_array($price_packages)) {
				foreach ($price_packages as $val) {
					$subtotalPrice = $subtotalPrice + lp_theme_option($val);
				}
			}
			$subtotalPrice = (int)$duration * (int)$subtotalPrice;
		}

		$taxPrice = 0;
		if (!empty($taxIsOn)) {
			$tax = $listingpro_options['lp_tax_amount'];
			$tax = (float) $tax;

			if (!empty($tax)) {
				$tax = (float)($tax / 100) * $subtotalPrice;
				$tax = round($tax, 2);
				$taxPrice = $tax;

				if ($currency_position == 'right') {
					$tax .= $currency;
				} else {
					$tax = $currency . $tax;
				}
			}
		}



		$plan_price = $subtotalPrice + $taxPrice;

		$plan_price = round($plan_price, 2);


		if ($currency_position == 'right') {
			$plan_price .= $currency;
			$subtotalPrice .= $currency;
		} else {
			$plan_price = $currency . $plan_price;
			$subtotalPrice = $currency . $subtotalPrice;
		}

		$user_info = get_userdata($userID);
		$fname = '';
		$lname = '';
		$usermail = '';
		$usermail = $user_info->user_email;
		$fname = $user_info->first_name;
		$lname = $user_info->last_name;
		$user_name = $user_info->user_login;

		$output = '
		<div class="checkout-invoice-area">
			<div class="top-heading-area">
				<div class="row">
					<div class="col-md-4 col-sm-4 col-xs-12">
						<img src="' . esc_attr($logo) . '" alt="Listingpro" style="width:122px" width="122" class="CToWUd">
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<p>' . esc_html__('Receipt', 'listingpro') . '</p>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12"></div>
				</div>
			</div>
			<div class="invoice-area">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<h4>' . esc_html__('Billed to :', 'listingpro') . '</h4>
						<ul>
							<li>' . $user_name . '</li>
							<li>' . $usermail . '</li>
						</ul>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<p>
							<strong>' . esc_html__('Invoice :', 'listingpro') . '</strong>
							#' . $invoice . '<br>
							<strong>' . esc_html__('Process With: Direct / Wire method', 'listingpro') . '</strong>
						</p>
					</div>
				</div>
				<div class="row heading-area">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<p><strong>' . esc_html__('Description', 'listingpro') . '</strong></p>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<p><strong>' . esc_html__('Payment instructions', 'listingpro') . '</strong></p>
					</div>					
					<div class="col-md-2 col-sm-2 col-xs-12"></div>
				</div>
				<div class="row invoices-company-details">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<a href="#" target="_blank">' . $company . '</a> <br>
						<p>' . $address . ' ' . '<span class="aBn" data-term="goog_1120388248" tabindex="0"><span class="aQJ">' . current_time('mysql') . '</span></span></b></p>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<p>' . $listingpro_options["direct_payment_instruction"] . '</p>
					</div>					
					<div class="col-md-2 col-sm-2 col-xs-12">
					</div>
				</div>
				<div class="row invoice-price-details">
					<div class="col-md-6 col-sm-6 col-xs-12">
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<ul class="clearfix">
							<li>' . esc_html__('Subtotal :', 'listingpro') . '</li>
							<li>' . $subtotalPrice . '</li>
						</ul>
						<ul class="clearfix">
							<li>' . esc_html__('Tax :', 'listingpro') . '</li>
							<li>' . $tax . '</li>
						</ul>
						<ul class="clearfix">
							<li>' . esc_html__('Amount Paid :', 'listingpro') . '</li>
							<li>0.00</li>
						</ul>
						<ul class="clearfix">
							<li>' . esc_html__('Balance due :', 'listingpro') . '</li>
							<li>' . $plan_price . '</li>
							
						</ul>
					</div>
				</div>
				<div class="thankyou-text text-center">
					<p>' . $thanku_text . '</p>
				</div>
			</div>
			<div class="checkout-bottom-area">
				' . $additional . '
			</div>
		</div>';


		$website_name = get_option('blogname');
		$admin_email = get_option('admin_email');

		$listing_title = get_the_title($postid);
		$listing_url = get_the_permalink($postid);


		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		//to admin


		$mail_subject = $listingpro_options['listingpro_subject_wire_invoice_admin'];
		$website_url = site_url();
		$website_name = get_option('blogname');

		$formated_mail_subject = lp_sprintf2("$mail_subject", array(
			'website_url' => "$website_url",
			'website_name' => "$website_name"
		));


		$mail_content = $listingpro_options['listingpro_content_wire_invoice_admin'];

		$formated_mail_content = lp_sprintf2("$mail_content", array(
			'website_url' => "$website_url",
			'listing_title' => "$listing_title",
			'plan_title' => "$plan_name",
			'plan_price' => "$plan_price",
			'listing_url' => "$listing_url",
			'invoice_no' => "$invoice",
			'website_name' => "$website_name",
			'user_name' => "$user_name",
			'payment_method' => "$payment_method"
		));
		lp_mail_headers_append();
		$emailresponse = LP_send_mail($admin_email, $formated_mail_subject, $formated_mail_content, $headers);



		// to user
		$to = $usermail;
		$subject = '';
		$body = '';
		$subjec = $listingpro_options['listingpro_subject_wire_invoice'];
		$bod = $listingpro_options['listingpro_content_wire_invoice'];

		$website_url = site_url();

		$subject = lp_sprintf2("$subjec", array(
			'website_url' => "$website_url",
			'website_name' => "$website_name"
		));

		$body = lp_sprintf2("$bod", array(
			'website_url' => "$website_url",
			'listing_title' => "$listing_title",
			'plan_title' => "$plan_name",
			'plan_price' => "$plan_price",
			'listing_url' => "$listing_url",
			'invoice_no' => "$invoice",
			'website_name' => "$website_name",
			'user_name' => "$user_name",
			'payment_method' => "$payment_method"
		));
		$emailresponse = LP_send_mail($to, $subject, $body, $headers);
		lp_mail_headers_remove();

		$listing_author = lp_theme_option("listing-author");
		$D_pageID = url_to_postid($listing_author);
		if ($D_pageID) {
			$listing_author = get_permalink($D_pageID);
		}

		$output .= '
			<div class="col-md-12">
				<a href="' . $listing_author . '" class="checkout-dashboard-bt">' . esc_html__("Go Back To Dashboard", "listingpro") . '</a>
			</div>
		';
		return $output;
	}
}

if (!function_exists('generate_wire_invoice')) {
	function generate_wire_invoice($postid)
	{

		global $listingpro_options, $wpdb;
		$output = null;
		$logo = '';
		$company = '';
		$address = '';
		$phone = '';
		$additional = '';
		$thanku_text = '';
		$user_name = '';
		$taxIsOn = $listingpro_options['lp_tax_swtich'];
		$tax = '';

		$logo = $listingpro_options['invoice_logo']['url'];
		$company = $listingpro_options['invoice_company_name'];
		$address = $listingpro_options['invoice_address'];
		$phone = $listingpro_options['invoice_phone'];
		$additional = $listingpro_options['invoice_additional_info'];
		$thanku_text = $listingpro_options['invoice_thankyou'];
		$userrow = '';
		$userID = '';
		$dbprefix = $wpdb->prefix;
		$counter = 1;
		$userID = '';
		$price = '';
		$invoiceno = '';
		$table = "listing_orders";
		$table = $dbprefix . $table;
		$results = array();
		$postid = (int) $postid;
		if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
			$query = "";
			$query = "SELECT * from $table WHERE post_id='$postid' ORDER BY main_id DESC";
			$results = $wpdb->get_results($query);
			$results = array_reverse($results);
		}

		foreach ($results as $Index => $Value) {
			$invoiceno = $Value->order_id;
			$date = date(get_option('date_format'), strtotime($Value->date));
			$price = $Value->price;
			$userID = $Value->user_id;
			$tax = $Value->tax;
		}

		$user_info = get_userdata($userID);
		$fname = '';
		$lname = '';
		$usermail = '';
		$usermail = $user_info->user_email;
		$fname = $user_info->first_name;
		$lname = $user_info->last_name;
		$user_name = $user_info->user_login;

		$currency = listingpro_currency_sign();
		$currency_position = lp_theme_option('pricingplan_currency_position');
		if (!empty($taxIsOn)) {
			$tax = round($tax, 2);
			$taxPrice = $tax;
			if ($currency_position == 'right') {
				$tax .= $currency;
			} else {
				$tax = $currency . $tax;
			}
			$subtotal = $price - $taxPrice;
			$subtotal = number_format($subtotal, 2, '.', '');
			if ($currency_position == 'right') {
				$subtotal .= $currency;
			} else {
				$subtotal = $currency . $subtotal;
			}
		}

		$user_info = get_userdata($userID);
		$fname = '';
		$lname = '';
		$usermail = '';
		$usermail = $user_info->user_email;
		$fname = $user_info->first_name;
		$lname = $user_info->last_name;
		$user_name = $user_info->user_login;
		if ($currency_position == 'right') {
			$price .= $currency;
		} else {
			$price = $currency . $price;
		}
		if (empty($taxIsOn)) {
			$subtotal = $price;
		}

		$output = '
		<div class="checkout-invoice-area">
			<div class="top-heading-area">
				<div class="row">
					<div class="col-md-4 col-sm-4 col-xs-12">
						<img src="' . esc_attr($logo) . '" alt="Listingpro" style="width:122px" width="122" class="CToWUd">
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<p>' . esc_html__('Receipt', 'listingpro') . '</p>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12"></div>
				</div>
			</div>
			<div class="invoice-area">
				<div class="row">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<h4>' . esc_html__('Billed to :', 'listingpro') . '</h4>
						<ul>
							<li>' . $user_name . '</li>
							<li>' . $usermail . '</li>
						</ul>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<p>
							<strong>' . esc_html__('Invoice :', 'listingpro') . '</strong>
							#' . $invoiceno . '<br>
							<strong>' . esc_html__('Process With: Direct / Wire method', 'listingpro') . '</strong>
						</p>
					</div>
				</div>
				<div class="row heading-area">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<p><strong>' . esc_html__('Description', 'listingpro') . '</strong></p>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<p><strong>' . esc_html__('Payment instructions', 'listingpro') . '</strong></p>
					</div>					
					<div class="col-md-2 col-sm-2 col-xs-12"></div>
				</div>
				<div class="row invoices-company-details">
					<div class="col-md-6 col-sm-6 col-xs-12">
						<a href="#" target="_blank">' . $company . '</a> <br>
						<p>' . $address . ' ' . '<span class="aBn" data-term="goog_1120388248" tabindex="0"><span class="aQJ">' . current_time('mysql') . '</span></span></b></p>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<p>' . $listingpro_options["direct_payment_instruction"] . '</p>
					</div>					
					<div class="col-md-2 col-sm-2 col-xs-12">
					</div>
				</div>
				<div class="row invoice-price-details">
					<div class="col-md-6 col-sm-6 col-xs-12">
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12">
						<ul class="clearfix">
							<li>' . esc_html__('Subtotal :', 'listingpro') . '</li>
							<li>' . $subtotal . '</li>
						</ul>
						<ul class="clearfix">
							<li>' . esc_html__('Amount Paid :', 'listingpro') . '</li>
							<li>0.00</li>
						</ul>
                                                <ul class="clearfix">
							<li>' . esc_html__('Tax :', 'listingpro') . '</li>
							<li>' . $tax . '</li>
						</ul>
						<ul class="clearfix">
							<li>' . esc_html__('Balance due :', 'listingpro') . '</li>
							<li>' . $price . '</li>
							
						</ul>
					</div>
				</div>
				<div class="thankyou-text text-center">
					<p>' . $thanku_text . '</p>
				</div>
			</div>
			<div class="checkout-bottom-area">
				' . $additional . '
			</div>
		</div>';

		$listing_author = lp_theme_option("listing-author");
		$D_pageID = url_to_postid($listing_author);
		if ($D_pageID) {
			$listing_author = get_permalink($D_pageID);
		}
		$output .= '
			<div class="col-md-12">
				<a href="' . $listing_author . '" class="checkout-dashboard-bt">' . esc_html__("Go Back To Dashboard", "listingpro") . '</a>
			</div>
		';
		return $output;
	}
}


/* ====================== for listing wire ======================*/

if (!function_exists('generate_wire_invoice__')) {

	function generate_wire_invoice__($postid)
	{
		$output = null;

		global $wpdb;
		$dbprefix = $wpdb->prefix;
		$counter = 1;
		$table = "listing_orders";
		$table = $dbprefix . $table;
		$results = array();
		if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
			$query = "";
			$query = "SELECT * from $table WHERE post_id='$postid' ORDER BY main_id DESC";
			$results = $wpdb->get_results($query);
			$results = array_reverse($results);
		}

		foreach ($results as $Index => $Value) {
			$invoiceno = $Value->order_id;
			$date = date(get_option('date_format'), strtotime($Value->date));
			$price = $Value->price;
		}

		$currency = listingpro_currency_sign();
		$currency_position = lp_theme_option('pricingplan_currency_position');
		if ($currency_position == 'right') {
			$price .= $currency;
		} else {
			$price = $currency . $price;
		}

		$output .= '<div class="checkout-transaction-receipt">
					<div class="row">
						<div class="col-md-12">
						';
		ob_start();
		include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/payment-steps-complete.php');
		$output .= ob_get_contents();
		ob_end_clean();
		ob_flush();
		$output .= '
							<div class="lp-checkout-transaction-pending">
								<span><i class="fa fa-check"></i> ' . esc_html__("Congratulations! Transaction is pending.", "listingpro") . '</span>
							</div>
						</div>
					</div>

					
					<div class="row">
						<div class="col-md-12">
							<div class="checkout-recipt-detil-inner">
								<div class="recipt-download-print">
										<span>
											<i class="fa fa-download"></i>
											
											<img alt="image" src="data:image/jpeg;base64,/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAABQAAD/4QMraHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjMtYzAxMSA2Ni4xNDU2NjEsIDIwMTIvMDIvMDYtMTQ6NTY6MjcgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzYgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjc0ODUxMkYwNThENjExRThCRjgzQzkzRjYxMEE4MUQ3IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjc0ODUxMkYxNThENjExRThCRjgzQzkzRjYxMEE4MUQ3Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzQ4NTEyRUU1OEQ2MTFFOEJGODNDOTNGNjEwQTgxRDciIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzQ4NTEyRUY1OEQ2MTFFOEJGODNDOTNGNjEwQTgxRDciLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7/7gAOQWRvYmUAZMAAAAAB/9sAhAACAgICAgICAgICAwICAgMEAwICAwQFBAQEBAQFBgUFBQUFBQYGBwcIBwcGCQkKCgkJDAwMDAwMDAwMDAwMDAwMAQMDAwUEBQkGBgkNCwkLDQ8ODg4ODw8MDAwMDA8PDAwMDAwMDwwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAAYABMDAREAAhEBAxEB/8QAcgAAAgMBAAAAAAAAAAAAAAAABAUBAwYJAQEBAQEAAAAAAAAAAAAAAAACAQADEAACAQMDAwIHAQAAAAAAAAABAgMREgQAIQUxExRRYXEiMlJEhAbEEQADAAEEAwEAAAAAAAAAAAAAARECQVGBwfAhEgP/2gAMAwEAAhEDEQA/AO9eLi4smLjSSY0UkkkSM7sikklQSSSNJt0KShGRhRDtPj46o8b3MYQqPS1hsTQbEg0J1lluZ47FHmZt1nYbuXdmnyUvpfd9f2b06e+r8olYVDPDi8XFlZMiw4+NirLPMxoqIiXMxPoAK6jVZU4jDfyH9NxONxnH8Vn5wxuQeZ0x4pmvMgmkMkVJFqpa2RQwrs1Qffp+mDbqBhmpDa/lfu/5dDTjsevPQqysDjOVx+Ji5DNKxceY5ZeOEqpHNIgFqzoRVgrCttQK9a6tabiD6aVHpy8BqBsmAhSCoLrsR0PXQ+WOoB8iDyL+8lnldy64Us8ey74XbV9dKOebhqp//9k=">
											<p>Receipt_001.pdf</p>
											<i class="fa fa-print" id="print-section-receipt"></i>
										</span>
								</div>
								<div id="printarea">
									<div class="col-md-12 receipt-info-padding">
										<div class="col-md-5">
											<h2>' . esc_html__("Receipt", "listingpro") . '</h2>
											<p>' . lp_theme_option("invoice_address") . '</p>
										</div>
										<div class="col-md-7">
											<div class="receipt-content-info">
												<p>' . esc_html__("No.", "listingpro") . $invoiceno . '</p>
												<p>' . $date . '</p>
												<p>' . lp_theme_option("invoice_company_name") . '</p>
												<p>' . lp_theme_option("direct_payment_instruction") . '</p>
												<p>' . lp_theme_option("invoice_address") . '</p>
												<p>' . lp_theme_option("invoice_phone") . '</p>
												<p>' . get_option("admin_email") . '</p>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<ul class="receipt-total-amount">
											<li>
												<span class="item-price-total-left"><b>' . esc_html__("Description", "listingpro") . '</b></span>
												<span class="item-price-total-right"><b>' . esc_html__("Amount", "listingpro") . '</b></span>
											</li>
											<li>
												<span class="item-price-total-left">' . esc_html__("Membership", "listingpro") . '</span>
												<span class="item-price-total-right">' . $price . '</span>
											</li>
											<li>
												<span class="item-price-total-left">' . get_option("blogname") . ' </span>
												<span class="item-price-total-right"></span>
											</li>
											<li>
												<span class="item-price-total-left"><b>' . esc_html__("SUBTOTAL", "listingpro") . '</b></span>
												<span class="item-price-total-right">' . $price . '</span>
											</li>
											<li>
												<span class="item-price-total-left"><b>' . esc_html__("PAID", "listingpro") . '</b></span>
												<span class="item-price-total-right">00.00</span>
											</li>
											<li>
												<span class="item-price-total-left"><b>' . esc_html__("TOTAL", "listingpro") . '</b></span>
												<span class="item-price-total-right"><b>' . $price . '</b></span>
											</li>

										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>';
		$listing_author = lp_theme_option("listing-author");
		$D_pageID = url_to_postid($listing_author);
		if ($D_pageID) {
			$listing_author = get_permalink($D_pageID);
		}
		$output .= '
                    <div class="col-md-12">
                                    <a href="' . $listing_author . '" class="checkout-dashboard-bt">' . esc_html__("Go Back To Dashboard", "listingpro") . '</a>
                                </div>
                ';
		return $output;
	}
}
