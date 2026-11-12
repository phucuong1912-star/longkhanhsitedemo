<div class="tab-pane fade in active" id="updateprofile">
    <?php
    global $listingpro_options;
    global $wpdb;
    $dbprefix = '';
    $post_ids = '';
    $dbprefix = $wpdb->prefix;
    $user_id = '';
    $user_id = get_current_user_id();
    $results = '';
    $resultss = '';
    $userSubscriptionsp = array();
    $userSubscriptionss = get_user_meta($user_id, 'listingpro_user_sbscr', true);
    if (is_array($userSubscriptionss)) :
        $userSubscriptions = array_reverse($userSubscriptionss);
    endif;
    include_once(LISTINGPRO_PLUGIN_PATH . "inc/stripe/stripe-php/init.php");
    $strip_sk = $listingpro_options['stripe_secrit_key'];
    \Stripe\Stripe::setApiKey($strip_sk);
    $currency = listingpro_currency_sign();
    $currency_position = lp_theme_option('pricingplan_currency_position');
    ?>
    <!-- Active Packages -->
    <div class="subscriptions">
        <div class="active-subscirptions-area">
            <?php if (!empty($userSubscriptions) && count($userSubscriptions) > 0) {
            ?>
                <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-11 align-center" id="lp-listings">
                    <h5 class="margin-bottom-20"><?php esc_html_e('All Subscriptions', 'listingpro'); ?></h5>
                    <div class="panel-body lp-new-packages" id="lp-new-invoices">
                        <div class="lp-main-title clearfix">
                            <div class="col-md-1">
                                <p><?php esc_html_e('No.', 'listingpro'); ?></p>
                            </div>
                            <div class="col-md-2">
                                <p><?php esc_html_e('Subscription', 'listingpro'); ?></p>
                            </div>
                            <div class="col-md-2 text-center">
                                <p><?php esc_html_e('Listing Title', 'listingpro'); ?></p>
                            </div>
                            <div class="col-md-1 padding-0">
                                <p><?php esc_html_e('Duration', 'listingpro'); ?></p>
                            </div>
                            <div class="col-md-2">
                                <p><?php esc_html_e('Price', 'listingpro'); ?></p>
                            </div>
                            <div class="col-md-2">
                                <p><?php esc_html_e('Upcoming renewal', 'listingpro'); ?></p>
                            </div>
                            <div class="col-md-2 text-center">
                                <p><?php esc_html_e('Action', 'listingpro'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content clearfix background-white">
                        <div class="tab-pane fade in active" id="tab1default">
                            <div class="">
                                <?php
                                global $wpdb;
                                $n = 1;
                                foreach ($userSubscriptions as $subscription) {

                                    try {
                                        $plan_id = $subscription['plan_id'];
                                        $subscr_id = $subscription['subscr_id'];
                                        $listing_id = $subscription['listing_id'];


                                        if (strpos($subscr_id, 'sub_') !== false && !isset($subscription['method'])) {
                                            /* stripe */
                                            try {
                                                $subscrObj = \Stripe\Subscription::retrieve($subscr_id);
                                                if ($subscrObj->status != 'active') {
                                                    break;
                                                }
                                                $subscrID = $subscrObj->id;
                                                $planStripe = $subscrObj->plan;
                                                $stripePrice = $planStripe->amount;
                                                $stripePrice = (float) $stripePrice / 100;
                                                $stripePrice = round($stripePrice, 2);
                                                $nextpayment = $subscrObj->current_period_end;
                                                $unsub_btn = '<a class="delete-subsc-btn" href="' . $subscrID . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                            } catch (Exception $e) {
                                                $subscrID = '';
                                                $planStripe = '';
                                                $stripePrice = '';
                                                $stripePrice = '';
                                                $stripePrice = '';
                                                $nextpayment = '';
                                                $unsub_btn = esc_html__('Invalid Stripe Api Keys. Contact Support.', 'listingpro');
                                            }
                                        } elseif (isset($subscription['method']) && $subscription['method'] == 'razorpay') {
                                            $subscrID = $subscription['subscr_id'];
                                            $unsub_btn = '<a class="delete-subsc-btn razorpay-unsub" href="' . $subscrID . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                            $nextpayment = strtotime("+ " . get_post_meta($plan_id, 'plan_time', true) . " days");
                                            $interval = get_post_meta($plan_id, 'razorpay_recurring_duration', true);
                                            $interval = ($interval == '') ? 'monthly' : $interval;
                                            if ($interval == 'daily') {
                                                $nextpayment = strtotime("+ 1 days");
                                            } else if ($interval == 'weekly') {
                                                $nextpayment = strtotime("+ 7 days");
                                            } else if ($interval == 'monthly') {
                                                $nextpayment = strtotime("+ 1 month");
                                            } else if ($interval == 'yearly') {
                                                $nextpayment = strtotime("+ 1 year");
                                            }
                                        } elseif (isset($subscription['method']) && $subscription['method'] == 'payfast') {
                                            $subscrID = $subscription['subscr_id'];
                                            if (isset($subscription['next_payment'])) {
                                                $nextpayment = $subscription['next_payment'];
                                            } else {
                                                $nextpayment = strtotime("now");
                                            }
                                            $unsub_btn = '<a class="unsubscribe-btn payfast-unsub" href="' . $subscrID . '" data-token="' . $subscription['token'] . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                        } elseif (isset($subscription['method']) && $subscription['method'] == 'mollie') {
                                            $subscrID = $subscription['subscr_id'];
                                            if (isset($subscription['next_payment'])) {
                                                $nextpayment = $subscription['next_payment'];
                                            } else {
                                                $nextpayment = strtotime("now");
                                            }
                                            $unsub_btn = '<a class="unsubscribe-btn mollie-unsub" href="' . $subscrID . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                        } elseif (isset($subscription['method']) && $subscription['method'] == 'flutterwave') {
                                            $subscrID = $subscription['subscr_id'];
                                            if (isset($subscription['next_payment'])) {
                                                $nextpayment = $subscription['next_payment'];
                                            } else {
                                                $nextpayment = strtotime("now");
                                            }
                                            $unsub_btn = '<a class="unsubscribe-btn flutterwave-unsub" href="' . $subscrID . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                        } elseif (strpos($subscr_id, 'SUB_') !== false) {
                                            $subscrID = $subscription['subscr_id'];
                                            if (isset($subscription['next_payment'])) {
                                                $nextpayment = $subscription['next_payment'];
                                            } else {
                                                $nextpayment = strtotime("now");
                                            }
                                            $unsub_btn = '<a class="delete-subsc-btn paystack-unsub" data-mailToekn="' . $subscription['email_tokent'] . '" href="' . $subscrID . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                        } else {
                                            /* paypal */
                                            $subscrIDOBJ = lp_retreive_recurring_profile($subscr_id);
                                            $subscrID = $subscrIDOBJ['PROFILEID'];
                                            $stripePrice = $subscrIDOBJ['AMT'];
                                            $nextpayment = $subscrIDOBJ['NEXTBILLINGDATE'];
                                            $nextpayment = strtotime($nextpayment);
                                            if (!isset($subscrIDOBJ['LASTPAYMENTDATE']) && isset($subscrIDOBJ['BILLINGFREQUENCY'])) {
                                                $nextpayment = strtotime(date(get_option('date_format'), $nextpayment) . ' + ' . $subscrIDOBJ['BILLINGFREQUENCY'] . ' days');
                                            }
                                            $unsub_btn = '<a class="delete-subsc-btn" href="' . $subscrID . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                        }


                                        $listing_title = get_the_title($listing_id);
                                        $plan_price = get_post_meta($plan_id, 'plan_price', true);
                                        $plan_duration = get_post_meta($plan_id, 'plan_time', true);
                                        $plan_duration = trim($plan_duration);
                                        $taxStatus = '';

                                        $dbprefix = $wpdb->prefix;
                                        $myPrice = $wpdb->get_row("SELECT * FROM " . $dbprefix . "listing_orders WHERE plan_id = $plan_id AND post_id = $listing_id ORDER BY main_id DESC");
                                        if ((isset($subscription['method'])) && ($subscription['method'] == 'razorpay' || $subscription['method'] == 'paystack' || $subscription['method'] == 'payfast' || $subscription['method'] == 'mollie' || $subscription['method'] == 'flutterwave')) {
                                            if (isset($myPrice->price)) {
                                                $stripePrice = $myPrice->price;
                                            }
                                        }
                                        $currency = isset($myPrice->currency) ? $myPrice->currency : $currency;
                                        if ($stripePrice == $plan_price) {
                                            $taxStatus = esc_html__('exc. tax', 'listingpro');
                                        } else {
                                            $plan_price = $stripePrice;
                                            $taxStatus = esc_html__('inc. tax', 'listingpro');
                                        }
                                        if (!empty($plan_price)) :
                                            if ($currency_position == 'right') {
                                                $plan_price .= $currency;
                                            } else {
                                                $plan_price = $currency . $plan_price;
                                            }
                                        endif;
                                        $dayVar = esc_html__('Days', 'listingpro');
                                        if (!empty($plan_duration)) {
                                            if ($plan_duration == 1) {
                                                $dayVar = esc_html__('Day', 'listingpro');
                                            }
                                        }
                                ?>
                                        <div class="lp-listing-outer-container clearfix">
                                            <div class="col-md-1"><?php echo esc_attr($n); ?></div>
                                            <div class="col-md-2 padding-0"><?php echo esc_attr($subscrID); ?></div>
                                            <div class="col-md-2 text-center"><?php echo esc_attr($listing_title); ?></div>
                                            <div class="col-md-1 padding-0"><?php echo esc_attr($plan_duration) . ' ' . $dayVar; ?></div>
                                            <?php
                                            if ((isset($subscription['method'])) && ($subscription['method'] == 'razorpay' || $subscription['method'] == 'paystack' || $subscription['method'] == 'payfast' || $subscription['method'] == 'mollie' || $subscription['method'] == 'flutterwave')) {
                                                if ($currency_position == 'right') { ?>
                                                    <div class="col-md-2"><?php echo esc_attr($myPrice->price) . $currency . " ($taxStatus)"; ?></div>
                                                <?php
                                                } else { ?>
                                                    <div class="col-md-2 padding-0"><?php echo esc_attr($currency) . $myPrice->price . " ($taxStatus)"; ?></div>
                                                <?php }
                                            } else {
                                                ?>
                                                <div class="col-md-2"><?php if (!empty($plan_price)) : echo esc_attr($plan_price) . " ($taxStatus)";
                                                                        endif; ?></div>
                                            <?php
                                            }
                                            ?>
                                            <div class="col-md-2"><?php if (!empty($nextpayment)) {
                                                                        echo date(get_option('date_format'), $nextpayment);
                                                                    } ?></div>
                                            <div class="col-md-2 text-center"><?php echo wp_kses_post($unsub_btn); ?></div>
                                        </div>


                                    <?php
                                        $n++;
                                    } catch (Exception $e) {
                                        $userSubscriptionsp[] = $subscription;
                                    }
                                }

                                /* for paypal */

                                if (!empty($userSubscriptionsp)) {
                                    foreach ($userSubscriptionsp as $subscription) {

                                        $plan_id = $subscription['plan_id'];
                                        $subscr_id = $subscription['subscr_id'];
                                        $subscrID = $subscr_id;
                                        $listing_id = $subscription['listing_id'];
                                        $listing_title = get_the_title($listing_id);

                                        $plan_price = get_post_meta($plan_id, 'plan_price', true);
                                        $plan_duration = get_post_meta($plan_id, 'plan_time', true);
                                        $plan_duration = trim($plan_duration);
                                        $taxStatus = '';
                                        $plan_price = get_post_meta($plan_id, 'plan_price', true);
                                        $dayVar = esc_html__('Days', 'listingpro');
                                        if (!empty($plan_duration)) {
                                            if ($plan_duration == 1) {
                                                $dayVar = esc_html__('Day', 'listingpro');
                                            }
                                        }
                                        $pfx_date = get_the_date('', $listing_id);
                                        $pfx_date = date(get_option('date_format'), strtotime($pfx_date . "+$plan_duration days"));

                                        if ($plan_price == $plan_price) {
                                            $taxStatus = esc_html__('exc. tax', 'listingpro');
                                        } else {
                                            $plan_price = $plan_price;
                                            $taxStatus = esc_html__('inc. tax', 'listingpro');
                                        }

                                        if ($currency_position == 'right') {
                                            $plan_price .= $currency;
                                        } else {
                                            $plan_price = $currency . $plan_price;
                                        }
                                    ?>
                                        <div class="lp-listing-outer-container clearfix">
                                            <div class="col-md-1"><?php echo esc_attr($n); ?></div>
                                            <div class="col-md-2"><?php echo esc_attr($subscrID); ?></div>
                                            <div class="col-md-2"><?php echo esc_attr($listing_title); ?></div>
                                            <div class="col-md-1 padding-0"><?php echo esc_attr($plan_duration) . ' ' . $dayVar; ?></div>
                                            <div class="col-md-2"><?php echo esc_attr($plan_price) . " ($taxStatus)"; ?></div>
                                            <div class="col-md-2"><?php echo esc_attr($pfx_date); ?></div>
                                            <div class="col-md-2"><a class="delete-subsc-btn" href="<?php echo esc_attr($subscrID); ?>" data-cmsg="<?php echo esc_html__('Are you sure you want to proceed action?', 'listingpro'); ?>"><?php echo esc_html__('Unsubscribe', 'listingpro'); ?></a></div>
                                        </div>


                                <?php
                                        $n++;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>



            <?php }
            if (empty($userSubscriptions)) { ?>

                <div class="lp-blank-section">
                    <div class="col-md-12 blank-left-side">
                        <img alt="image" src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
                        <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>

                    </div>
                </div>

            <?php } ?>
        </div>


    </div>