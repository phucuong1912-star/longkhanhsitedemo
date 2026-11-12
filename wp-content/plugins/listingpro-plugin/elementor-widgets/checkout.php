<?php

namespace ElementorListingpro\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Listing_Checkout extends Widget_Base
{

    public function get_name()
    {
        return 'listing-checkout';
    }

    public function get_title()
    {
        return __('Listing Checkout', 'elementor-listingpro');
    }

    public function get_icon()
    {
        return 'eicon-posts-ticker';
    }

    public function get_categories()
    {
        return ['listingpro'];
    }
    protected function _register_controls()
    {

        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'elementor-listingpro'),
            ]
        );
        $this->add_control(
            'title',
            [
                'label' => __('Title', 'elementor-listingpro'),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'bank_transfer_img',
            [
                'label' => __('Bank Transfer Image', 'elementor-listingpro'),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'stripe_img',
            [
                'label' => __('Stripe Image', 'elementor-listingpro'),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'paypal_img',
            [
                'label' => __('Paypal Image', 'elementor-listingpro'),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'twocheckout_img',
            [
                'label' => __('2 Checkout Image', 'elementor-listingpro'),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_checkout($settings);
    }
    protected function content_template() {}
    public function render_plain_content() {}
}

if (!function_exists('listingpro_shortcode_checkout')) {
    function listingpro_shortcode_checkout($atts, $content = null)
    {
        if (!empty($_POST['post_id']) && isset($_POST['post_id']) && !empty($_POST['method']) && isset($_POST['method'])) {
            $queryVar = array();
            $queryVar['func'] = 'addrow';
            lp_form_handler($_POST, $queryVar);
        } elseif (isset($_GET['token']) && isset($_GET['PayerID'])) {
            $queryVar = array();
            $queryVar = array();
            $queryVar['func'] = 'confirm';
            $queryVar['token'] = $_GET['token'];
            $queryVar['PayerID'] = $_GET['PayerID'];
            lp_form_handler('', $queryVar);
        }

        extract(shortcode_atts(array(
            'title'   => '',
            'bank_transfer_img'   => '',
            'stripe_img'   => '',
            'paypal_img'   => '',
            'twocheckout_img'   => '',
        ), $atts));

        $output = null;
        global $listingpro_options;

        $bank_transfer_img =    isset($bank_transfer_img['id']) ? $bank_transfer_img['id'] : '';
        $stripe_img        =    isset($stripe_img['id']) ? $stripe_img['id'] : '';
        $paypal_img        =    isset($paypal_img['id']) ? $paypal_img['id'] : '';
        $twocheckout_img   =    isset($twocheckout_img['id']) ? $twocheckout_img['id'] : '';


        $pubilshableKey = '';
        $pubilshableKey = $listingpro_options['stripe_pubishable_key'];
        $currency = $listingpro_options['currency_paid_submission'];
        $ajaxURL = '';
        $ajaxURL = admin_url('admin-ajax.php');

        $paypalStatus = false;
        $stripeStatus = false;
        $wireStatus = false;
        $checkout2Status = false;
        if ($listingpro_options['enable_paypal'] == "1") {
            $paypalStatus = true;
        }
        if ($listingpro_options['enable_stripe'] == "1") {
            $stripeStatus = true;
        }
        if ($listingpro_options['enable_wireTransfer'] == "1") {
            $wireStatus = true;
        }
        if ($listingpro_options['enable_2checkout'] == "1") {
            $checkout2Status = true;
        }

        $currency = $listingpro_options['currency_paid_submission'];
        $currency_symbol = listingpro_currency_sign();
        $currency_position = '';
        $currency_position = $listingpro_options['pricingplan_currency_position'];

        $deafaultFeatImg = lp_default_featured_image_listing();

        /* ================================for claim paid payment============================== */
        if (isset($_GET['booking_id']) && isset($_GET['user_id'])) {
            $output = apply_filters('medicalpro_booking_checkout', '');
        } elseif (isset($_GET['listing_id']) && isset($_GET['claim_plan']) && isset($_GET['user_id']) && isset($_GET['claim_post'])) {


            $post_id = '';
            $order_id = '';
            $redirect = '';
            $recurringPayment = lp_theme_option('lp_enable_recurring_payment');


            $output = '<div class="page-container-four clearfix">';
            $output .= '<div class="col-md-10 col-md-offset-1">';

            $paid_mode = lp_theme_option('enable_paid_submission');
            $taxButton = lp_theme_option('lp_tax_swtich');

            if (!empty($paid_mode) && $paid_mode == "no") {
                $output .= '<p class="text-center">' . esc_html__('Sorry! Currently Free mode is activated', 'listingpro-plugin') . '</p>';
            } else {
                /* for steps */
                ob_start();
                include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/payment-steps.php');
                $output .= ob_get_contents();
                ob_end_clean();
                ob_flush();


                $output .= '<form autocomplete="off" id="listings_checkout_form" class="lp-listing-form" name ="listings_checkout_form" action="' . $redirect . '" method="post" data-recurring="' . $recurringPayment . '" data-currencypos="' . $currency_position . '" data-currencysymbol="' . $currency_symbol . '">';
                $output .= '<div class="row">';
                $output .= '<div class="col-md-8">';
                ob_start();
                include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/claim-checkout.php');
                $output .= ob_get_contents();
                ob_end_clean();
                ob_flush();

                // section selected listing details and coupons.

                $output .= '<div class="lp-checkout-coupon-outer">';
                $couponsSwitch = lp_theme_option('listingpro_coupons_switch');
                if ($couponsSwitch == "yes") {
                    $output .= '
									<div class="col-md-12 checkout-padding-top-bottom">
										<div class="col-md-6">
											<div class="lp-checkout-coupon-code">
												<div class="lp-onoff-switch-checkbox">
													<label class="switch-checkbox-label">
														<input type="checkbox" name="lp_checkbox_coupon" value="couponON">
														<span class="switch-checkbox-styling">
														</span>
													</label>
												</div>
												<span class="lp-text-switch-checkbox">' . esc_html__("Coupon Code", "listingpro-plugin") . '</span>
											</div>
										</div>
										<div class="col-md-6 apply-coupon-text-field">
											<input type="text" class="coupon-text-field" name="coupon-text-field" placeholder="' . esc_html__('Type Here', 'listingpro-plugin') . '" disabled>
											<button type="button" class="coupon-apply-bt" disabled>' . esc_html__('APPLY CODE', 'listingpro-plugin') . '</button>
										</div>
									</div>';
                }

                $output .= '
								<ul class="checkout-item-price-total">
									<li>
										<span class="item-price-total-left"><b>' . esc_html__('ITEM', 'listingpro-plugin') . '</b></span>
										<span class="item-price-total-right"><b>' . esc_html__('PRICE', 'listingpro-plugin') . '</b></span>

									</li>
									<li>
										<span class="item-price-total-left lp-subtotal-plan">' . esc_html__('Pro', 'listingpro-plugin') . '</span>
										<span class="item-price-total-right lp-subtotal-p-price"></span>

									</li>';
                if (!empty($taxButton)) {
                    $output .= '
										<li>
											<span class="item-price-total-left">' . esc_html__('Tax(Value Added Tax)', 'listingpro-plugin') . '</span>
											<span class="item-price-total-right lp-subtotal-taxamount"></span>

										</li>';
                }
                $output .= '
									<li>
										<span class="item-price-total-left"><b>' . esc_html__('Total', 'listingpro-plugin') . '</b></span>
										<span class="item-price-total-right lp-subtotal-total-price"><b></b></span>

									</li>

								</ul>

						</div>';

                $output .= '</div>';

                $output .= '<div class="col-md-4 lp-col-outer">';
                ob_start();
                include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/payment-methods.php');
                $output .= ob_get_contents();
                ob_end_clean();
                ob_flush();

                // checkbox term and conditions
                $termsCondition = lp_theme_option('payment_terms_condition');
                if (!empty($termsCondition)) {
                    $output .= '<div class="lp-new-term-style clearfix"><label class="filter_checkbox_container terms-checkbox-container">
                            <input type="checkbox">
                            <span class="filter_checkbox_checkmark"></span>
                        </label><a class="lpcheckouttac" target="_blank" href="' . get_the_permalink($termsCondition) . '">' . esc_html__('Terms And Conditions', 'listingpro-plugin') . '
                        </a>
                        
                        </div>
                        ';
                }

                $output .= '
						<button type="button" class="lp_payment_step_next firstStep" disabled>' . esc_html__('PROCEED TO NEXT', 'listingpro-plugin') . '</button>
					';
                $output .= '</div>';

                $output .= '</div>';


                $output .= '</form>';

                if ($listingpro_options['listingpro_scastripe_switch'] == 'yes') {
                    echo '<input type="hidden" id="lp_stripe_sca" value="yes">';
                    wp_enqueue_script('stripe-sca');
                } else {
                    $output .= '
							<button id="stripe-submit">' . esc_html__('Purchase', 'listingpro-plugin') . '</button>

								<script>
								var post_title = "";
								listings_id = "";
								listings_img = "";
								plan_price = "";
								currency = "";
								plan_id = "";
								listing_img = "";
								taxrate = "";
								coupon = "";
								jQuery("button.lp_payment_step_next").click(function(){
									listings_id = "";
									listings_id = jQuery("#listings_checkout_form input[name=listing_id]:checked").val();
									plan_id = "";
									plan_id = jQuery("#listings_checkout_form input[name=listing_id]:checked").data("planid");
									taxrate = jQuery("#listings_checkout_form input[name=listing_id]:checked").data("taxrate");
									coupon = jQuery("#listings_checkout_form input[name=coupon-text-field]").val();
								});
                                var recurringtext ="";
								jQuery("#listings_checkout_form").submit(function(){
									recurringtext = jQuery("input[name=lp-recurring-option]:checked").val();
								});
								
								var token_email, token_id;
								var handler = StripeCheckout.configure({
								  key: "' . $pubilshableKey . '",
								  image: "https://stripe.com/img/documentation/checkout/marketplace.png",
								  locale: "auto",
								  token: function(token) {
									console.log(token);
									token_id = token.id;
									token_email = token.email;
									jQuery("body").addClass("listingpro-loading");
									jQuery.ajax({
										type: "POST",
										dataType: "json",
										url: "' . $ajaxURL . '",
										data: { 
											"action": "listingpro_save_stripe", 
											"token": token_id, 
											"email": token_email, 
											"listing": listings_id, 
											"plan": plan_id,
											"taxrate": jQuery("#listings_checkout_form input[name=listings_tax_price]").val(),						
											"coupon" : coupon,						
											"recurring" : recurringtext,						
										},   
										success: function(res){
											if(res.status=="success"){
												redURL = res.redirect;
												if(res.status=="success"){
													window.location.href = redURL;
													jQuery("body").removeClass("listingpro-loading");
												}
											}
											if(res.status=="fail"){
												alert(res.redirect);
												jQuery("body").removeClass("listingpro-loading");
											}
											
										},
										error: function(errorThrown){
											alert(errorThrown);
											jQuery("body").removeClass("listingpro-loading");
										} 
									});
									

								  }
								});

								// Close Checkout on page navigation:
								window.addEventListener("popstate", function() {
								  handler.close();
								});
								</script>
								
								';
                }
            }
        }

        /* ================================for campaign wire============================== */ else if (isset($_GET['checkout']) && !empty($_GET['checkout']) && $_GET['checkout'] == "wire") {
            if (!isset($_SESSION)) {
                session_start();
            }

            $postID = isset( $_SESSION['post_id'] ) ? absint( $_SESSION['post_id'] ) : 0;
            if (!empty($postID)) {
                $output = '<div class="page-container-four clearfix">';
                $output .= '<div class="col-md-10 col-md-offset-1">';
                $output .= get_campaign_wire_invoice($postID);
                $output .= '</div>';
                $output .= '</div>';
                unset($_SESSION['post_id']);
            } else {
                $redirect = site_url();
                wp_redirect($redirect);
                exit();
            }
        }
        /* ================================for listings wire============================== */ else if (isset($_GET['method']) && !empty($_GET['method']) && $_GET['method'] == "wire") {
            if (!isset($_SESSION)) {
                session_start();
            }
            do_action('lp_pdf_enqueue_scripts');
            $postID = isset( $_SESSION['post_id'] ) ? absint( $_SESSION['post_id'] ) : 0;
            $discount = isset( $_SESSION['discount'] ) ? $_SESSION['discount'] : null;
            if (!empty($postID)) {
                $output = '<div class="page-container-four clearfix">';
                $output .= '<div class="col-md-10 col-md-offset-1">';
                $output .= generate_wire_invoice($postID);
                $output .= '</div>';
                $output .= '</div>';
                unset($_SESSION['post_id']);
            } else {
                $redirect = site_url();
                wp_redirect($redirect);
                exit();
            }
        }

        /* ================================for checkout success/failed ============================== */ else if (isset($_GET['lpcheckstatus']) && !empty($_GET['lpcheckstatus'])) {
            $lpcheckstatus = sanitize_file_name($_GET['lpcheckstatus']);
            /* for steps */
            $output .= '<div class="page-container-four clearfix lpcheckoutcomplete">';
            ob_start();
            include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/payment-steps-complete.php');
            $output .= ob_get_contents();
            ob_end_clean();
            ob_flush();
            $output .= '</div>';

            $allowed_statuses = array('success', 'fail');
            $lpcheckstatus = sanitize_file_name($_GET['lpcheckstatus']);
            if (in_array($lpcheckstatus, $allowed_statuses, true)) {
                ob_start();
                $lpcheckstatus = sanitize_file_name($_GET['lpcheckstatus']);
                include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/' . $lpcheckstatus . '.php');
                $output .= ob_get_contents();
                ob_end_clean();
                ob_flush();
            }


            /* ================================for checkout default page ============================== */
        } else {
            $post_id = '';
            $order_id = '';
            $redirect = '';
            $recurringPayment = lp_theme_option('lp_enable_recurring_payment');

            $output = '<div class="page-container-four clearfix">';
            $output .= '<div class="col-md-10 col-md-offset-1">';

            $paid_mode = lp_theme_option('enable_paid_submission');
            $taxButton = lp_theme_option('lp_tax_swtich');

            if (!empty($paid_mode) && $paid_mode == "no") {
                $output .= '<p class="text-center">' . esc_html__('Sorry! Currently Free mode is activated', 'listingpro-plugin') . '</p>';
            } else {
                /* for steps */
                ob_start();
                include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/payment-steps.php');
                $output .= ob_get_contents();
                ob_end_clean();
                ob_flush();


                $output .= '<form autocomplete="off" id="listings_checkout_form" class="lp-listing-form" name ="listings_checkout_form" action="' . $redirect . '" method="post" data-recurring="' . $recurringPayment . '" data-currencypos="' . $currency_position . '" data-currencysymbol="' . $currency_symbol . '">';
                $output .= '<div class="row">';
                $output .= '<div class="col-md-8">';
                if (isset($_POST['planid']) && isset($_POST['listingid'])) {
                    ob_start();
                    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/quick-checkout.php');
                    $output .= ob_get_contents();
                    ob_end_clean();
                    ob_flush();
                } else {
                    ob_start();
                    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/default-checkout.php');
                    $output .= ob_get_contents();
                    ob_end_clean();
                    ob_flush();
                }

                // section selected listing details and coupons.

                $output .= '<div class="lp-checkout-coupon-outer">';
                $couponsSwitch = lp_theme_option('listingpro_coupons_switch');
                if ($couponsSwitch == "yes") {
                    $output .= '
									<div class="col-md-12 checkout-padding-top-bottom">
										<div class="col-md-6">
											<div class="lp-checkout-coupon-code">
												<div class="lp-onoff-switch-checkbox">
													<label class="switch-checkbox-label">
														<input type="checkbox" name="lp_checkbox_coupon" value="couponON">
														<span class="switch-checkbox-styling">
														</span>
													</label>
												</div>
												<span class="lp-text-switch-checkbox">' . esc_html__("Coupon Code", "listingpro-plugin") . '</span>
											</div>
										</div>
										<div class="col-md-6 apply-coupon-text-field">
											<input type="text" class="coupon-text-field" name="coupon-text-field" placeholder="' . esc_html__('Type Here', 'listingpro-plugin') . '" disabled>
											<button type="button" class="coupon-apply-bt" disabled>' . esc_html__('APPLY CODE', 'listingpro-plugin') . '</button>
										</div>
									</div>';
                }

                $output .= '
								<ul class="checkout-item-price-total">
									<li>
										<span class="item-price-total-left"><b>' . esc_html__('ITEM', 'listingpro-plugin') . '</b></span>
										<span class="item-price-total-right"><b>' . esc_html__('PRICE', 'listingpro-plugin') . '</b></span>

									</li>
									<li>
										<span class="item-price-total-left lp-subtotal-plan"></span>
										<span class="item-price-total-right lp-subtotal-p-price"></span>

									</li>';
                if (!empty($taxButton)) {
                    $output .= '
										<li>
											<span class="item-price-total-left">' . esc_html__('Tax(Value Added Tax)', 'listingpro-plugin') . '</span>
											<span class="item-price-total-right lp-subtotal-taxamount"></span>

										</li>';
                }
                $output .= '
									<li>
										<span class="item-price-total-left"><b>' . esc_html__('Total', 'listingpro-plugin') . '</b></span>
										<span class="item-price-total-right lp-subtotal-total-price"><b></b></span>

									</li>

								</ul>

						</div>';

                $output .= '</div>';

                $output .= '<div class="col-md-4 lp-col-outer">';
                ob_start();
                include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/payment-methods.php');
                $output .= ob_get_contents();
                ob_end_clean();
                ob_flush();

                // checkbox term and conditions
                $termsCondition = lp_theme_option('payment_terms_condition');
                if (!empty($termsCondition)) {
                    $output .= '<div class="lp-new-term-style clearfix"><label class="filter_checkbox_container terms-checkbox-container">
                            <input type="checkbox">
                            <span class="filter_checkbox_checkmark"></span>
                        </label><a class="lpcheckouttac" target="_blank" href="' . get_the_permalink($termsCondition) . '">' . esc_html__('Terms And Conditions', 'listingpro-plugin') . '
                        </a>
                        
                        </div>
                        ';
                }

                $output .= '
						<button type="button" class="lp_payment_step_next firstStep" disabled>' . esc_html__('PROCEED TO NEXT', 'listingpro-plugin') . '</button>
					';
                $output .= '</div>';

                $output .= '</div>';


                $output .= '</form>';

                if ($listingpro_options['listingpro_scastripe_switch'] == 'yes') {
                    echo '<input type="hidden" id="lp_stripe_sca" value="yes">';
                    wp_enqueue_script('stripe-sca');
                } else {
                    $output .= '
							<button id="stripe-submit">' . esc_html__('Purchase', 'listingpro-plugin') . '</button>

								<script>
								var post_title = "";
								listings_id = "";
								listings_img = "";
								plan_price = "";
								currency = "";
								plan_id = "";
								listing_img = "";
								taxrate = "";
								coupon = "";
								jQuery("button.lp_payment_step_next").click(function(){
									listings_id = "";
									listings_id = jQuery("#listings_checkout_form input[name=listing_id]:checked").val();
									plan_id = "";
									plan_id = jQuery("#listings_checkout_form input[name=listing_id]:checked").data("planid");
									taxrate = jQuery("#listings_checkout_form input[name=listing_id]:checked").data("taxrate");
                                    coupon = jQuery("#listings_checkout_form input[name=coupon-text-field]").val();
								});
								var recurringtext ="";
								jQuery("#listings_checkout_form").submit(function(){
									recurringtext = jQuery("input[name=lp-recurring-option]:checked").val();
								});
								
								var token_email, token_id;
								var handler = StripeCheckout.configure({
								  key: "' . $pubilshableKey . '",
								  image: "https://stripe.com/img/documentation/checkout/marketplace.png",
								  locale: "auto",
								  token: function(token) {
									console.log(token);
									token_id = token.id;
									token_email = token.email;
									jQuery("body").addClass("listingpro-loading");
									jQuery.ajax({
										type: "POST",
										dataType: "json",
										url: "' . $ajaxURL . '",
										data: { 
											"action": "listingpro_save_stripe", 
											"token": token_id, 
											"email": token_email, 
											"listing": listings_id, 
											"plan": plan_id,
											"plan_price": jQuery("#listings_checkout_form input[name=plan_price]").val(),
											"taxrate": jQuery("#listings_checkout_form input[name=listings_tax_price]").val(),					
											"coupon": coupon,					
											"recurring" : recurringtext,						
										},   
										success: function(res){
											if(res.status=="success"){
												redURL = res.redirect;
												if(res.status=="success"){
													window.location.href = redURL;
													jQuery("body").removeClass("listingpro-loading");
												}
											}
											if(res.status=="fail"){
												alert(res.redirect);
												jQuery("body").removeClass("listingpro-loading");
											}
											
										},
										error: function(errorThrown){
											alert(errorThrown);
											jQuery("body").removeClass("listingpro-loading");
										} 
									});
									

								  }
								});

								// Close Checkout on page navigation:
								window.addEventListener("popstate", function() {
								  handler.close();
								});
								</script>
								
								';
                }
            }
        }
        if (!empty($checkout2Status)) {
            ob_start();
            include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/popup.php');

            $below_shortcode = ob_get_contents();
            ob_end_clean();
            $output .= $below_shortcode;
        }
        $output = apply_filters('lp_checkout_template_ui', $output, $atts);
        return $output;
    }
}
