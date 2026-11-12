<?php

do_action('lp_enqueue_print_script');

do_action('lp_pdf_enqueue_scripts');

$currencyCode = listingpro_currency_sign();

$planID = '';

$latestInvoice = '';

$latestDate = '';

$latestAmount = '';

$latestTax = '';

$latestMethod = '';

$latestPlan = '';

$latestDuration = '';

$latestonlyPlanPrice = '';

$listTitle = '';

global $user_id, $listingpro_options;
$user_fname = get_the_author_meta('first_name', $user_id);
$user_lname = get_the_author_meta('last_name', $user_id);
// User contact meta
$user_address = get_the_author_meta('address', $user_id);
$user_phone   = get_the_author_meta('phone', $user_id);
$user_email   = get_the_author_meta('user_email', $user_id);

$currency_position = lp_theme_option('pricingplan_currency_position');
$resultsall        = get_ads_invoices_list($user_id, '', 'success');
$results1          = get_ads_invoices_list($user_id, '', 'success');
$results2          = get_ads_invoices_list($user_id, '', 'expired');
$results3          = get_ads_invoices_list($user_id, '', 'pending');
//$results = (object)array_merge((array)$results1,(array)$results2,(array)$results3);
//$resultsinArray = (array) $results;

if (!empty($resultsall)) {

?>
    <div class="tab-pane fade in active lp-new-invoices" id="lp-listings">
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-9 lp-left-panel-height">
            <div class="panel-heading">
                <h5 class="margin-bottom-20"><?php esc_html_e('All Invoices', 'listingpro'); ?></h5>
                
            </div>
            <div class="panel-body pos-relative" id="lp-new-invoices">
                <div class="lp-main-title clearfix">
                    <div class="col-md-3">
                        <p><?php esc_html_e('Reciept / Invoice', 'listingpro'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><?php esc_html_e('date', 'listingpro'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><?php esc_html_e('Status', 'listingpro'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><?php esc_html_e('amount', 'listingpro'); ?></p>
                    </div>

                </div>
                <div class="tab-content clearfix">

                    <?php
                    if (!empty($resultsall)) {

                    ?>
                        <div class="tab-pane fade in active" id="tab0default">
                            <?php
                            $lpCount = 1;
                            foreach ($resultsall as $data) {
                                $plan_priceORG = '';
                                $plan_price    = '';
                                $plan_title    = '';
                                $currencyCode  = (isset($data->currency) && $data->currency != '') ? $data->currency : $currencyCode;
                                if (isset($data->post_id) && !empty($data->post_id)) {
									$planID    = listing_get_metabox_by_ID('ads_listing', $data->post_id);
                                    $plan_title = get_the_title($planID);
                                    $plan_price = $data->price;
                                    $plan_price = number_format($plan_price, 2, '.', '');
                                }
                                $plan_priceORG = $plan_price;
                                if (!empty($plan_price)) {
                                    if ($currency_position == 'right') {
                                        $plan_price .= $currencyCode;
                                    } else {
                                        $plan_price = $currencyCode . $plan_price;
                                    }
                                }
                                $invoiceno = '';
                                if (isset($data->token) && !empty($data->token)) {
                                    $invoiceno = $data->token;
                                }
                                if(empty($invoiceno)){
                                    if (isset($data->transaction_id) && !empty($data->transaction_id)) {
                                        $invoiceno = $data->transaction_id;
                                    }
                                }
                                $invdate = '';
                                if (isset($data->ad_date) && !empty($data->ad_date)) {
                                    $invdate = $data->ad_date;
                                    $invdate = date_i18n(get_option('date_format'), strtotime($invdate));
                                }
                                $listId = '';
                                if (isset($data->post_id) && !empty($data->post_id)) {
									$listId    = listing_get_metabox_by_ID('ads_listing', $data->post_id);
                                    $listTitle = get_the_title($listId);
									if( empty($listId) ){
										$listTitle = get_the_title($data->post_id);
									}
									if( empty($listTitle) ){
										$listTitle = 'This Ad is deleted';
									}
                                }
                                $pmethod = '';
                                if (isset($data->payment_method) && !empty($data->payment_method)) {
                                    $pmethod = $data->payment_method;
                                }

                                //new code 2.6.15
                                if(!empty($pmethod)){
                                    if($pmethod == 'wire' || $pmethod == 'WIRE'){
                                        $pmethod = esc_html__('wire', 'listingpro');
                                    }
                                }
                                //end new code 2.6.15

                                $pstatus    = '';
                                $discounted = get_post_meta($listId, 'discounted', true);
                                if (isset($data->status) && !empty($data->status) && $discounted == '') {
                                    $pstatus = $data->status;
                                } else if ($discounted == 'yes') {
                                    $pstatus = $data->status . esc_html__(' (100% Discounted)', 'listingpro');
                                }

                                //Code to change the language in invoice
                                if ($pstatus == 'success') {
                                    $pstatus = esc_html__('success', 'listingpro');
                                } else if ($pstatus == 'pending') {
                                    $pstatus = esc_html__('pending', 'listingpro');
                                } else if ($pstatus == 'success (100% Discounted)') {
                                    $pstatus = esc_html__('success (100% Discounted)', 'listingpro');
                                }

                                $pdays = '';
                                if (isset($data->mode) && !empty($data->mode)) {
                                    $pdays = $data->mode;
                                }
                                if (empty($pdays)) {
                                    $pdays = esc_html__('Unlimited', 'listingpro');
                                }
                                $taxPrice      = 0;
                                $onlyPlanPrice = '';
                                if (isset($data->tax)) {
                                    if (!empty($data->tax)) {
                                        $taxPrice = $data->tax;
                                        $taxPrice = number_format($taxPrice, 2, '.', '');
                                    }
                                    $onlyPlanPrice = $plan_priceORG - $taxPrice;
                                    $onlyPlanPrice = round($onlyPlanPrice, 2);
                                }
                                /* if price saved in meta */

                                $lp_purchase_price = $data->price;;
                                $lp_purchase_tax   = $data->tax;;
                                if (!empty($lp_purchase_price)) {
                                    $onlyPlanPrice = round($lp_purchase_price, 2);
                                    $plan_priceORG = $onlyPlanPrice;
                                }
                                if (!empty($lp_purchase_tax)) {
                                    $lp_purchase_tax = number_format($lp_purchase_tax, 2, '.', '');
                                    $taxPrice        = $lp_purchase_tax;
                                }
                                /* end if price saved in meta */
                                $checked = '';
                                if ($lpCount == 1) {
                                    $listTitle           = get_the_title($listId);
                                    $latestInvoice       = $invoiceno;
                                    $latestDate          = $invdate;
                                    $latestPlanPriceORG  = (float) $plan_priceORG;
                                    $latestTax           = (float) $taxPrice;
                                    $latestAmount        = $latestPlanPriceORG + $latestTax;
                                    $latestMethod        = $pmethod;
                                    $latestPlan          = get_the_title($listId);
                                    $latestDuration      = $pdays;
                                    $invoicestatuslatest = $pstatus;

                                    //new code 2.6.15
                                    if(!empty($latestMethod)){
                                        if($latestMethod == 'wire' || $latestMethod == 'WIRE'){
                                            $latestMethod = esc_html__('wire', 'listingpro');
                                        }
                                    }
                                    //end new code 2.6.15
                                    
                                    $checked             = 'checked';
                                    if (!empty($lp_purchase_price)) {
                                        $latestonlyPlanPrice = round($lp_purchase_price, 2);
                                        $latestPlanPriceORG  = $latestonlyPlanPrice;
                                    } else {
                                        $latestonlyPlanPrice = $latestPlanPriceORG - $latestTax;
                                        $latestonlyPlanPrice = round($latestonlyPlanPrice, 2);
                                    }

                                    if ($currency_position == 'right') {
                                        $latestonlyPlanPrice .= $currencyCode;
                                    } else {
                                        $latestonlyPlanPrice = $currencyCode . $latestonlyPlanPrice;
                                    }
                                }

                                $dataAttass = 'data-inoviceno="' . $invoiceno . '" data-listtitle="' . $listTitle . '" data-date="' . $invdate . '" data-amount="' . $plan_price . '" data-tax="' . $taxPrice . '" data-method="' . $pmethod . '" data-plan="' . $plan_title . '" data-duration="' . $pdays . '" data-status="' . sprintf(esc_html__('%s', 'listingpro'), $pstatus) . '" data-duration="' . $pdays . '" data-orprice="' . $onlyPlanPrice . '"  ';

                                if ($data->payment_method == 'paypal') {
                            ?>
                                    <div class="lp-listing-outer-container clearfix" <?php echo wp_kses_post($dataAttass); ?>>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-number lp-listing-form">
                                                <label>
                                                    <p><?php echo esc_attr($invoiceno); ?></p>
                                                    <div class="radio radio-danger lp_right_preview_this_invoice">
                                                        <input id="<?php echo esc_attr($invoiceno); ?>" class="radio_checked" type="radio" name="method" value="<?php echo esc_attr($invoiceno); ?>" <?php echo esc_attr($checked); ?>>
                                                        <label for="<?php echo esc_attr($invoiceno); ?>">
                                                        </label>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-date">
                                                <p><?php echo esc_attr($invdate); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-status">
                                                <?php
                                                printf(esc_html__('%s', 'listingpro'), $pstatus);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-price clerarfix">
                                                <p><?php echo esc_attr($plan_price); ?></p>
                                                <a class="lp_preview_this_invoice" href="<?php echo esc_attr($invoiceno); ?>"><i class="fa fa-eye" aria-hidden="true"></i> <?php esc_html_e('View', 'listingpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    $lpCount++;
                                } else if ($data->payment_method != 'paypal') {
                                ?>
                                    <div class="lp-listing-outer-container clearfix" <?php echo wp_kses_post($dataAttass); ?>>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-number lp-listing-form">

                                                <label>
                                                    <p><?php echo esc_attr($invoiceno); ?></p>
                                                    <div class="radio radio-danger lp_right_preview_this_invoice">
                                                        <input id="<?php echo esc_attr($invoiceno); ?>" class="radio_checked" type="radio" name="method" value="<?php echo esc_attr($invoiceno); ?>" <?php echo esc_attr($checked); ?>>
                                                        <label for="<?php echo esc_attr($invoiceno); ?>">

                                                        </label>
                                                    </div>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-date">
                                                <p><?php echo esc_attr($invdate); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-status">
                                                <?php
                                                printf(esc_html__('%s', 'listingpro'), $pstatus);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-price clerarfix">
                                                <p><?php echo esc_attr($plan_price); ?></p>
                                                <a class="lp_preview_this_invoice" href="<?php echo esc_attr($invoiceno); ?>"><i class="fa fa-eye" aria-hidden="true"></i> <?php esc_html_e('View', 'listingpro'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                    $lpCount++;
                                }
                            }
                            ?>
                        </div>
                    <?php
                    } ?>
                </div>
                <!--popup for preview -->
                <div class="lp_popup_preview_invoice">
                    <div id="listing-invoices-popup" class="listing-invoices-popup">
                        <div class="popup-dialog">
                            <div class="md-content">
                                <div class="modal-header">
                                    <button type="button" class="close close_invoice_prev" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <!--leftside-->
                                        <div class="col-md-6 text-left">
                                            <div class="lp-invoice-leftinfo">
                                                <div class="margin-bottom-20">
                                                    <img class="img-responsive" src="<?php echo lp_theme_option_url('invoice_logo'); ?>" alt="<?php echo esc_attr('logo'); ?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <div class="lp-invoice-rightinfo">
                                                <div class="margin-bottom-20">
                                                    <span class="lp-infoice-label">
                                                        <?php echo esc_html__('PAID', 'listingpro'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!--leftside-->
                                        <div class="col-md-6 text-left">
                                            <div class="lp-invoice-leftinfo">
                                                <div class="margin-bottom-40">
                                                    <h3 class="modal-titl"><?php echo esc_html__('Invoice#', 'listingpro'); ?>
                                                        <span class="lppopinvoice"></span>
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <div class="lp-invoice-rightinfo">
                                                <div class="margin-bottom-40">
                                                    <p class="lp-invoice-popup-date">
                                                        <span><?php echo esc_html__('Date: ', 'listingpro'); ?></span><span class="lppopdate"></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!--leftside-->
                                        <div class="col-md-6 text-left">
                                            <div class="lp-invoice-leftinfo">
                                                <span>
                                                    <?php echo esc_html__('Invoice To: ', 'listingpro'); ?>
                                                </span>
                                                <span class="spanblock graycolor">
                                                    <?php echo esc_attr($user_fname) . ' ' . $user_lname; ?>
                                                </span>
                                                <span class="spanblock maxwidth130">
                                                    <?php echo esc_attr($user_address); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <div class="lp-invoice-rightinfo">
                                                <span>
                                                    <?php echo esc_html__('Pay To: ', 'listingpro'); ?>
                                                </span>
                                                <span class="spanblock graycolor">
                                                    <?php echo esc_html__('Business Name', 'listingpro'); ?>
                                                </span>
                                                <span class="spanblock">
                                                    <?php echo lp_theme_option('invoice_company_name'); ?>
                                                </span>
                                                <span class="spanblock">
                                                    <?php echo lp_theme_option('invoice_address'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!--leftside-->
                                        <div class="col-md-6 text-left">
                                            <div class="lp-invoice-leftinfo">
                                                <span class="spanblock">
                                                    <?php echo esc_attr($user_phone); ?>
                                                </span>
                                                <span class="spanblock">
                                                    <?php echo esc_attr($user_email); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <div class="lp-invoice-rightinfo">
                                                <span class="spanblock graycolor">
                                                    <?php echo esc_html__('Call Us For Help', 'listingpro'); ?>
                                                </span>
                                                <span class="spanblock">
                                                    <?php echo lp_theme_option('invoice_phone'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="lp-invoice-description-title">
                                                <div class="clearfix lp-invoice-description-title-inner">
                                                    <h3>
                                                        <span><?php echo esc_html__('Invoice Details', 'listingpro'); ?></span>
                                                    </h3>
                                                </div>
                                                <ul class="clearfix lp-invoice-planinfo-inner">
                                                    <li class="clearfix">
                                                        <div class="col-md-2 lp-in-trns-id padding-left-0">
                                                            <span class=""><?php echo esc_html__('Transaction ID', 'listingpro'); ?></span>
                                                            <p><?php echo esc_attr($latestInvoice); ?></p>
                                                        </div>
                                                        <div class="col-md-2 lp-in-trns-id padding-left-0">
                                                            <span class=""><?php echo esc_html__(' Listing Name ', 'listingpro'); ?></span>
                                                            <p class="lppoplist"></p>
                                                        </div>
                                                        <div class="col-md-2 lp-in-trns-id padding-left-0">
                                                            <span class=""><?php echo esc_html__(' Ad Type ', 'listingpro'); ?></span>
                                                            <p class="lppopduration"></p>
                                                        </div>
                                                        <div class="col-md-4 lp-in-trns-id padding-0 text-right">
                                                            <span class=""><?php echo esc_html__(' Amount ', 'listingpro'); ?></span>
                                                            <p class="lppopamount"></p>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="lp-invoices-other-details margin-bottom-30">
                                                <ul class="clearfix">
                                                    <li><?php echo esc_html__('Tax', 'listingpro'); ?> <span class="lppoptaxprice"></span></li>
                                                    <li><?php echo esc_html__('Plan Price', 'listingpro'); ?> <span class="lppopplanprice"></span></li>
                                                    <li class="lp-invoice-total-amount"><?php echo esc_html__('Total', 'listingpro'); ?>
                                                        <span class="lppopamount"></span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <p class="text-right lp-pay-with"><?php esc_html_e('Paid with', 'listingpro'); ?>
                                                <br />
                                                <img data-srcwire="<?php echo get_template_directory_uri() . '/assets/images/wire.png' ?>" data-srcpaypal="<?php echo get_template_directory_uri() . '/assets/images/paypal.png' ?>" data-srcstripe="<?php echo get_template_directory_uri() . '/assets/images/stripe.png' ?>" data-srcpaystack="<?php echo plugins_url('paystack-for-listingpro/assets/images/paystack.png'); ?>" data-srcrazorpay="<?php echo plugins_url('razorpay-for-listingpro/assets/images/logo.png'); ?>" data-srcpayfast="<?php echo plugins_url('payfast-for-listingpro/assets/images/logo.png'); ?>" data-srcpayu="<?php echo plugins_url('payu-for-listingpro/assets/images/logo.png'); ?>" data-srceway="<?php echo plugins_url('eway-for-listingpro/assets/images/logo.png'); ?>" data-srcmollie="<?php echo plugins_url('mollie-for-listingpro/assets/images/logo.png'); ?>" data-srcmercadopago="<?php echo plugins_url('mercadopago-for-listingpro/assets/images/logo.png'); ?>" data-srcstripeideal="<?php echo plugins_url('stripeideal-for-listingpro/assets/images/logo.png'); ?>" data-srcflutterwave="<?php echo plugins_url('flutterwave-for-listingpro/assets/images/logo.png'); ?>" data-srcmtn_momo="<?php echo plugins_url('mtn-momo-for-listingpro/assets/images/logo.png'); ?>" alt="image" src="" />
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="md-content">
                        <div class="modal-footer clearfix">
                            <div class="row">
                                <div class="col-md-6 text-left">
                                    <button type="button" class="downloadpdffullinv"><i class="fa fa-download" aria-hidden="true"></i> <?php esc_html_e('Download PDF', 'listingpro'); ?>
                                    </button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="pull-right printthisinvoice"><?php esc_html_e('Print', 'listingpro'); ?>
                                        <i class="fa fa-print" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 padding-right-0 lp-right-panel-height">
            <div class="lp-ad-click-outer">
                <div class="lp-general-section-title-outer lp_right_preview_invoice">
                    <p class="clarfix lp-general-section-title comment-reply-title active"> <?php echo esc_html__('Details', 'listingpro'); ?>
                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                    </p>
                    <div class="lp-ad-click-inner" id="lp-ad-click-inner">
                        <ul class="lp-invoices-all-stats clearfix">
                            <li>
                                <h5><?php echo esc_html__('INVOICE#', 'listingpro'); ?> <span class="lppopinvoice"><?php echo esc_attr($latestInvoice); ?></span></h5>
                            </li>
                            <li>
                                <h5><?php echo esc_html__('Date', 'listingpro'); ?> <span class="lppopdate"><?php echo esc_attr($latestDate); ?></span></h5>
                            </li>
                            <li>
                                <h5><?php echo esc_html__('Status', 'listingpro'); ?>
                                    <span class="lppopstatus">
                                        <?php
                                        echo sprintf(esc_html__('%s', 'listingpro'), $invoicestatuslatest);
                                        ?>
                                    </span>
                                </h5>
                            </li>
                            <?php
                            if (!empty($latestTax)) {
                            ?>
                                <li>
                                    <h5><?php echo esc_html__('Tax Price', 'listingpro'); ?> <span class="lppoptaxprice"><?php echo esc_attr($latestTax); ?></span></h5>
                                </li>
                            <?php
                            }
                            ?>
                            <li>
                                <h5><?php echo esc_html__('Total', 'listingpro'); ?> <span class="lppopamount"><?php echo esc_attr($latestAmount); ?></span></h5>
                            </li>
                            <li>
                                <h5><?php echo esc_html__('Method', 'listingpro'); ?> <span class="lppopmethod"><?php echo sprintf(esc_html__('%s', 'listingpro'), $latestMethod); ?></span>
                                </h5>
                            </li>
                            <li>
                                <h5><?php echo esc_html__('Listing', 'listingpro'); ?> <span class="lppopplan"><?php echo esc_attr($latestPlan); ?></span></h5>
                            </li>
                            <li>
                                <h5><?php echo esc_html__('Ad Type', 'listingpro'); ?> <span class="lppopduration"><?php echo esc_attr($latestDuration); ?></span></h5>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
} else {
?>
    <div class="lp-blank-section">
        <div class="col-md-12 blank-left-side">
            <img alt="image" src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
            <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
            <p><?php echo esc_html__('You must be here for the first time. You will see Listing invoices here.', 'listingpro'); ?></p>
        </div>
    </div>
<?php
}
?>
<div id="lpinvoiceforpdf" style="display:none">
    <h4><?php echo esc_html__('Invoice#', 'listingpro'); ?> <span class="lppopinvoice"></span></h4>
    <p class="lp-invoice-popup-date"><span><?php echo esc_html__('Date: ', 'listingpro'); ?></span><span class="lppopdate"></span></p>
    <p class="margin-bottom-10"><?php echo esc_html__('Billed To: ', 'listingpro'); ?></p>
    <p><?php echo esc_attr($user_fname); ?><?php echo esc_attr($user_lname); ?></p>
    <p><?php echo esc_attr($user_phone); ?></p>
    <p class="lp-invoice-email"><?php echo esc_attr($user_email); ?></p>
    <p><?php echo esc_attr($user_address); ?></p>
    <p><?php echo esc_html__('List Name : ', 'listingpro'); ?><span class="lllistname"><?php echo esc_attr($listTitle); ?></span>
    </p>
    <p class="lp-bill-bold"><?php echo lp_theme_option('invoice_company_name'); ?></p>
    <p><?php echo lp_theme_option('invoice_address'); ?> </p>
    <p class="lp-invoice-email"><?php echo get_option('admin_url'); ?> </p>
    <p>
        <span><?php echo esc_html__('Amount', 'listingpro'); ?></span>
    </p>
    <p class="lp-invoice-total-amount"><?php echo esc_html__('Tax Price', 'listingpro'); ?> <span class="lppopamountqqq"></span></p>
    <p class="lp-invoice-total-amount"><?php echo esc_html__('Total', 'listingpro'); ?> <span class="lppopamountwww"></span></p>
    <p class="lp-pay-with"><?php echo esc_html__('Paid with', 'listingpro'); ?><br />
        <span class="lppopmethod"></span>
    </p>
</div>