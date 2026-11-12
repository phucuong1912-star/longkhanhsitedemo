<?php
global $listingpro_options;
$paypalStatus = false;
$stripeStatus = false;
$wireStatus = false;
$twocheckStatus = false;
if($listingpro_options['enable_paypal']=="1"){
	$paypalStatus = true;
}
if($listingpro_options['enable_stripe']=="1"){
	$stripeStatus = true;
}
if($listingpro_options['enable_wireTransfer']=="1"){
	$wireStatus = true;
}
if($listingpro_options['enable_2checkout']=="1"){
	$twocheckStatus = true;
}

$wireStatus = apply_filters('lp_wire_payment_method', $wireStatus);

$bank_transfer_img = $bank_transfer_img ?? 0;
$stripe_img = $stripe_img ?? 0;
$paypal_img = $paypal_img ?? 0;
$twocheckout_img = $twocheckout_img ?? 0;

$bank_img_url = null;
$stripe_img_url = null;
$paypal_img_url = null;
$twocheckout_img_url = null;

$bank_img_url = wp_get_attachment_url($bank_transfer_img);
$stripe_img_url = wp_get_attachment_url($stripe_img);
$paypal_img_url = wp_get_attachment_url($paypal_img);
$twocheckout_img_url = wp_get_attachment_url($twocheckout_img);

$output .='<div class="lp-rightbnk-transfer-msg lp-rightbnk-transfer-msg-new">';
	if($wireStatus==true){
		$output .='<div class="lp-method-wrap">';
		$output .='<label>';
		
		
		$output .='<div class="radio radio-danger">
						<input type="radio" name="plan" id="rd1" value="wire">
						<label for="rd1">
						</label>
					</div><p>'.esc_html__('Bank Transfer','listingpro-plugin').'</p>';
					if(!empty($bank_img_url)){
						$output .='<div class="lp-checkout-payment-img"><img alt="image" src="'.esc_attr($bank_img_url).'"></div>';
					}
					else{
						$output .='<div class="lp-checkout-payment-img"><img alt="image" src="'.get_template_directory_uri().'/assets/images/bank_transfer.png"></div>';
					}
		$bankinfo = '';
		$bankinfo = $listingpro_options['direct_payment_instruction'];
		$output .='</label>';
		$output .='<div class="lp-tranfer-info">';
			
		//$output .= $bankinfo;
		$output .='</div>';
    $output .='</div>';
	}
		
		if($paypalStatus==true){
			$output .='<div class="lp-method-wrap">';
			$output .='<label>';
			
			
			$output .='<div class="radio radio-danger">
								<input type="radio" name="plan" id="rd2" value="paypal">
								<label for="rd2">
								 
								</label>
							</div><p>'.esc_html__('Paypal','listingpro-plugin').'</p>';
			
			if(!empty($paypal_img_url)){
				$output .= '<div class="lp-checkout-payment-img"><img alt="image" src="'.esc_attr($paypal_img_url).'"></div>';
			}
			else{
				$output .= '<div class="lp-checkout-payment-img"><img alt="image" src="'.get_template_directory_uri().'/assets/images/paypal.png"></div>';
			}
			//$output .= esc_html__('Paypal', 'listingpro-plugin');
			$output .='</label>';
			$output .='</div>';
		}
		
		if($twocheckStatus==true){
			$output .='<div class="lp-method-wrap">';
			$output .='<label>';
			
			
			$output .='<div class="radio radio-danger">
								<input type="radio" name="plan" id="rd4" value="2checkout">
								<label for="rd4">
								 
								</label>
							</div><p>'.esc_html__('2 Checkout','listingpro-plugin').'</p>';
			
			if(!empty($twocheckout_img_url)){
				$output .= '<div class="lp-checkout-payment-img"><img alt="image" src="'.esc_attr($twocheckout_img_url).'"></div>';
			}
			else{
				$output .= '<div class="lp-checkout-payment-img"><img alt="image" src="'.get_template_directory_uri().'/assets/images/2checkout-logo.png"></div>';
			}
			$output .='</label>';
			$output .='</div>';
		}
		
		if($stripeStatus==true){
			$output .='<div class="lp-method-wrap">';
			$output .='<label>';
			
			
			$output .='<div class="radio radio-danger">
								<input type="radio" name="plan" id="rd3" value="stripe">
								<label for="rd3">
								 
								</label>
						</div><p>'.esc_html__('Stripe','listingpro-plugin').'</p>';
			
			if(!empty($stripe_img_url)){
				$output .= '<div class="lp-checkout-payment-img"><img alt="image" src="'.esc_attr($stripe_img_url).'"></div>';
			}
			else{
				$output .= '<div class="lp-checkout-payment-img"><img alt="image" src="'.get_template_directory_uri().'/assets/images/stripe.png"></div>';
			}
			//$output .= esc_html__('Stripe', 'listingpro-plugin');
			$output .='</label>';
			$output .='</div>';
		}
		

		/* ==============================custom action================================== */
		ob_start();
        do_action('lp_add_custom_payment_method_html', 'listing');
		$below_shortcode = ob_get_contents();
		ob_end_clean();
		$output .= $below_shortcode;

		/* ==============================end custom action================================== */

        if(lp_theme_option('lp_enable_recurring_payment')=="yes" && !isset($_GET['listing_id']) && !isset($_GET['claim_plan']) && !isset($_GET['user_id']) && !isset($_GET['claim_post'])){
            $auto_recurring_option = '';
            if (lp_theme_option('lp_enable_auto_recurring_payment_switch') == "yes") {
                $auto_recurring_option = 'checked';
            }
            $output .='<div class="lp-method-wrap lp-checkout-recurring-wrap">
                            <div class="lp-checkout-coupon-code">
                                <div class="lp-onoff-switch-checkbox">
                                    <label class="switch-checkbox-label">
                                        <input type="checkbox" name="lp-recurring-option" value="yes" ' . $auto_recurring_option . '>
                                        <span class="switch-checkbox-styling">
                                        </span>
                                    </label>
                                </div>
                                <span class="lp-text-switch-checkbox">'.esc_html__("Recurring Payment", "listingpro-plugin").'</span>';
                                if (lp_theme_option('lp_enable_auto_recurring_payment_switch') == "yes") {
                                    $output .= '<h6 class="payment-recurring-message">' . esc_html__("Alert! Recurring Payment Option is Enabled By Default.", "listingpro-plugin") . '<i class="fa fa-times-circle rec_alert_remove"></i></h6>';
                                }  
            $output .=    '</div>';
            $output .='</div>';
        }

            $enable_payment_methdos = apply_filters('lp_checkout_payment_methods', false);

		if($wireStatus==false && $stripeStatus==false && $paypalStatus==false && $twocheckStatus==false && $enable_payment_methdos == false){
			$output .= esc_html__('Sorry! You have not enable any payment method', 'listingpro-plugin');
		}
	$output .='</div>';
	$output .='<div class="lp-recurring-button-wrap"></div>';