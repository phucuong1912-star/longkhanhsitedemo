<?php

if (!empty($_POST['lp_ads_for_listing']) && isset($_POST['lp_ads_for_listing']) && !empty($_POST['method']) && isset($_POST['method']) && is_array($_POST['lpadsoftype'])) {
    $queryVar = array();
    lp_form_handler2($_POST, '');
} elseif (isset($_GET['token']) && isset($_GET['PayerID'])) {
    $queryVar = array();
    $queryVar = array();
    $queryVar['func'] = 'confirm';
    $queryVar['token'] = $_GET['token'];
    $queryVar['PayerID'] = $_GET['PayerID'];
    lp_form_handler2('', $queryVar);
}
lp_create_campaigns_table();
$currencyCode = listingpro_currency_sign();
$showSpotlightAds = true;
$showSearchtopAds = true;
$showDetailpageAds = true;
$currencyPos = lp_theme_option('pricingplan_currency_position');
$taxPerfent = 0;
$taxRate = 0;
$taxPrice = 0;
$isTax = lp_theme_option('lp_tax_swtich');
if (!empty($isTax)) {
    $taxPerfent = lp_theme_option('lp_tax_amount');
    $taxRate = $taxPerfent;
}
$currency = lp_theme_option('currency_paid_submission');
$spotOrgprice = '';
$detailOrgprice = '';
$searchOrgprice = '';
$selctedPackages = '';
$selctedduration = '';
$selctedAmount = '';
$selctedMethod = '';
$selectedClicks = '';
$selectedPaidAmount = '';
$selectedCredit = '';
$adsTypeval = '';
$adsType = '';
$spotlightTax = 0;
$detailsTax = 0;
$searchTax = 0;
$typeofcampaign = lp_theme_option('listingpro_ads_campaign_style');
if ($typeofcampaign == "adsperclick") {
    $adsType = esc_html__('PPC', 'listingpro');
    $adsTypeval = 'perclick';
    $prefixCampaign = esc_html__('Cost Per Click', 'listingpro');
    $spotlightPrice = lp_theme_option('lp_random_ads_pc');
    $spotOrgprice = $spotlightPrice;
    $spotOnlyprice = $spotlightPrice;

    if (!empty($isTax)) {
        $spotpricePerPercent = $spotOrgprice / 100;
        $spottaxPrice = $spotpricePerPercent * $taxRate;
        $spotlightTax = $spottaxPrice;
        $spotOrgprice =  $spotOrgprice + $spottaxPrice;
    }
    $spotOrgprice = round($spotOrgprice, 2);
    if ($currencyPos == "right") {
        $spotlightPrice = $spotlightPrice . $currencyCode;
    } else {
        $spotlightPrice = $currencyCode . $spotlightPrice;
    }
    $detailpageprice = lp_theme_option('lp_detail_page_ads_pc');
    $detailOrgprice = $detailpageprice;
    $detailOnlyprice = $detailOrgprice;
    if (!empty($isTax)) {
        $detailpricePerPercent = $detailOrgprice / 100;
        $detailtaxPrice = $detailpricePerPercent * $taxRate;
        $detailsTax = $detailtaxPrice;
        $detailOrgprice =  $detailOrgprice + $detailtaxPrice;
    }
    $detailOrgprice = round($detailOrgprice, 2);
    if ($currencyPos == "right") {
        $detailpageprice = $detailpageprice . $currencyCode;
    } else {
        $detailpageprice = $currencyCode . $detailpageprice;
    }
    $searchpageprice = lp_theme_option('lp_top_in_search_page_ads_pc');
    $searchOrgprice = $searchpageprice;
    $searchOnlyprice = $searchOrgprice;
    if (!empty($isTax)) {
        $searchpricePerPercent = $searchOrgprice / 100;
        $searchtaxPrice = $searchpricePerPercent * $taxRate;
        $searchTax = $searchtaxPrice;
        $searchOrgprice =  $searchOrgprice + $searchtaxPrice;
    }
    $searchOrgprice = round($searchOrgprice, 2);
    if ($currencyPos == "right") {
        $searchpageprice = $searchpageprice . $currencyCode;
    } else {
        $searchpageprice = $currencyCode . $searchpageprice;
    }
} elseif ($typeofcampaign == "adsperduration") {
    $adsType = esc_html__('PPD', 'listingpro');
    $adsTypeval = 'byduration';
    $prefixCampaign = esc_html__('Cost Per Day', 'listingpro');
    $spotlightPrice = lp_theme_option('lp_random_ads');
    $spotOrgprice = $spotlightPrice;
    $spotOnlyprice = $spotlightPrice;
    if (!empty($isTax)) {
        $spotpricePerPercent = $spotOrgprice / 100;
        $spottaxPrice = $spotpricePerPercent * $taxRate;
        $spotlightTax = $spottaxPrice;
        $spotOrgprice =  $spotOrgprice + $spottaxPrice;
    }
    //$spotOrgprice = round($spotOrgprice,2);
    if ($currencyPos == "right") {
        $spotlightPrice = $spotlightPrice . $currencyCode;
    } else {
        $spotlightPrice = $currencyCode . $spotlightPrice;
    }
    $detailpageprice = lp_theme_option('lp_detail_page_ads');
    $detailOrgprice = $detailpageprice;
    $detailOnlyprice = $detailpageprice;
    if (!empty($isTax)) {
        $detailpricePerPercent = $detailOrgprice / 100;
        $detailtaxPrice = $detailpricePerPercent * $taxRate;
        $detailsTax = $detailtaxPrice;
        $detailOrgprice =  $detailOrgprice + $detailtaxPrice;
    }
    //$detailOrgprice = round($detailOrgprice,2);
    if ($currencyPos == "right") {
        $detailpageprice = $detailpageprice . $currencyCode;
    } else {
        $detailpageprice = $currencyCode . $detailpageprice;
    }
    $searchpageprice = lp_theme_option('lp_top_in_search_page_ads');
    $searchOrgprice = $searchpageprice;
    $searchOnlyprice = $searchpageprice;
    if (!empty($isTax)) {
        $searchpricePerPercent = $searchOrgprice / 100;
        $searchtaxPrice = $searchpricePerPercent * $taxRate;
        $searchTax = $searchtaxPrice;
        $searchOrgprice =  $searchOrgprice + $searchtaxPrice;
    }
    //$searchOrgprice = round($searchOrgprice,2);
    if ($currencyPos == "right") {
        $searchpageprice = $searchpageprice . $currencyCode;
    } else {
        $searchpageprice = $currencyCode . $searchpageprice;
    }
}
if (empty(lp_theme_option('lp_random_ads_switch'))) {
    $showSpotlightAds = false;
}
if (empty(lp_theme_option('lp_detail_page_ads_switch'))) {
    $showDetailpageAds = false;
}
if (empty(lp_theme_option('lp_top_in_search_page_ads_switch'))) {
    $showSearchtopAds = false;
}
$showcampaingprocess = true;
if (empty($showSpotlightAds) && empty($showDetailpageAds) && empty($showSearchtopAds)) {
    $showcampaingprocess = false;
}
global $user_id;
$firstClickCOunt = '';
$firstAmount = '';
$firstCredit = '';
$showPaypal = lp_theme_option('enable_paypal');
$showStripe = lp_theme_option('enable_stripe');
$showWire = lp_theme_option('enable_wireTransfer');
$showPaystack = lp_theme_option('enable_paystack');
$currency_position = lp_theme_option('pricingplan_currency_position');
global $wpdb;
$dbprefix = $wpdb->prefix;
$table = 'listing_campaigns';
$table_name = $dbprefix . $table;
$all_success = array();
$data = '*';
$condition = "status='success' AND user_id='$user_id'";
if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
    $all_success = lp_get_data_from_db($table, $data, $condition);
}
$activeCampaigns = array();
$inactiveCampaigns = array();
$pendingCampaigns = array();
if (!empty($all_success)) {
    $showAC = 0;
    foreach ($all_success as $key => $val) {
        $caID = $val->post_id;
        $campaignstat = get_post_status($caID);
        if (!empty($campaignstat)) {
            if (empty($showAC)) {
                //first selected values
                $clicks = listing_get_metabox_by_ID('click_performed', $caID);

                $currency = isset($val->currency) ? $val->currency : $currency;
                $dbtID = $val->transaction_id;
                $taxPrice = $val->tax;

                //new code 2.6.15
                if($val->payment_method == 'wire' || $val->payment_method == 'WIRE'){
                    $val->payment_method = esc_html__('wire', 'listingpro');
                }

                //end new code 2.6.15

                $dbmethod = '<p>' . esc_html__('PAYED WITH', 'listingpro') . '</p>';

                $dbmethod .= '<h4>' . $val->payment_method . '</h4>';



                $listing_id = listing_get_metabox_by_ID('ads_listing', $caID);

                $clicks = listing_get_metabox_by_ID('click_performed', $caID);

                $budget = listing_get_metabox_by_ID('budget', $caID);
                $budgetwithtax = listing_get_metabox_by_ID('budgetwithtax', $caID);
                $admode = listing_get_metabox_by_ID('ads_mode', $caID);
                $admode = 'ads' . $admode;

                $remaining_balance = listing_get_metabox_by_ID('remaining_balance', $caID);

                $active_packages = listing_get_metabox_by_ID('ad_type', $caID);

                $duration = listing_get_metabox_by_ID('duration', $caID);

                $selectedClicks = $clicks;
                $selectedPaidAmount = $budget;
                $selectedCredit = $remaining_balance;

                if (!empty($active_packages)) {

                    $typetitle = '';

                    $hasPackage = false;

                    foreach ($active_packages as $key => $singlePackage) {

                        if ($singlePackage == "lp_random_ads") {

                            $typetitle = '';

                            $typetitle .= '<span>' . esc_html__("Spotlight", "listingpro") . '<i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>';
                            $typetitle .= "<i class='fa fa-check-circle'></i>";
                        } elseif ($singlePackage == "lp_detail_page_ads") {

                            $typetitle = '';

                            $typetitle .= '<span>' . esc_html__("Sidebar", "listingpro") . '<i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>';
                            $typetitle .= "<i class='fa fa-check-circle'></i>";
                        } elseif ($singlePackage == "lp_top_in_search_page_ads") {

                            $typetitle = '';

                            $typetitle .= '<span>' . esc_html__("Top of Search", "listingpro") . '<i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>';
                            $typetitle .= "<i class='fa fa-check-circle'></i>";
                        }

                        $selctedPackages .= '<li>' . $typetitle . '</li>';
                    }
                }


                $selctedduration = '';

                if (!empty($durationHTML)) {

                    $selctedduration = '<h5>' . $durationHTML . '</h5>';
                }

                if (!empty($budget)) {
                    if ($currency_position == 'right') {
                        $selctedAmount = '<h4>' . $budget . ' ' . $dbcurrency;
                        '</h4>';
                    } else {
                        $selctedAmount = '<h4>' . $currency . ' ' . $budget;
                        '</h4>';
                    }
                }

                if (!empty($dbmethod)) {

                    $selctedMethod = $dbmethod;
                }

                $checkedButton = 'checked';



                $selectedClicks = $clicks;

                $selectedPaidAmount = $budget;

                $selectedCredit = $remaining_balance;
            }
            if ($campaignstat == 'publish') {
                array_push($activeCampaigns, $val);
            } elseif ($campaignstat == 'inactive') {
                array_push($inactiveCampaigns, $val);
            } elseif ($campaignstat == 'pending') {
                array_push($pendingCampaigns, $val);
            }
            $showAC++;
        }
    }
    if ($showAC == 0) {
        $all_success = '';
    }
}
?>
<div class="tab-pane fade in active lp-new-ad-compaign padding-bottom-50" id="lp-listings">
    <?php $ads_promo_url = '';

    $classcols = '9';
    if (empty($all_success)) {
        $classcols = '12';
    }
    $lp_left_static = '';
    if (!empty($all_success)) {
        $lp_left_static = 'lp-left-static';
    }
    ?>

    <div class="lp_camp_invoice_wrp">
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-<?php echo esc_attr($classcols . ' ' . $lp_left_static); ?> lp-left-panel-height">
            <!-- campaigns invoices-->

            <div class="panel-heading">
                <?php
                if (!empty($all_success)) {
                ?>
                    <h5 class="margin-bottom-20"><?php esc_html_e('All Campaigns', 'listingpro'); ?></h5>
                <?php
                } ?>
                <ul class="nav nav-tabs">
                    <?php
                    if (!empty($all_success)) {
                    ?>
                        <li class="active">
                            <a href="#tab1default" data-toggle="tab">
                                <?php esc_html_e('All', 'listingpro'); ?>
                            </a>
                        </li>
                    <?php
                    }

                    if (!empty($activeCampaigns)) {
                    ?>
                        <li>
                            <a href="#tab2default" data-toggle="tab">
                                <?php esc_html_e('Active', 'listingpro'); ?>
                            </a>
                        </li>
                    <?php
                    }

                    if (!empty($pendingCampaigns)) {
                    ?>
                        <li>
                            <a href="#tab3default" data-toggle="tab">
                                <?php esc_html_e('Pending', 'listingpro'); ?>
                            </a>
                        </li>
                    <?php
                    }

                    if (!empty($inactiveCampaigns)) {
                    ?>
                        <li>
                            <a href="#tab4default" data-toggle="tab">
                                <?php esc_html_e('Inactive', 'listingpro'); ?>
                            </a>
                        </li>
                    <?php
                    }
                    ?>
                    <?php
                    if (!empty($showcampaingprocess) && !empty($all_success)) {
                    ?>
                        <button type="button" class="lp-let-start-btn lp-add-new-btn">
                            <span>
                                <i class="fa fa-plus" aria-hidden="true">
                                </i>
                            </span>
                            <?php echo esc_html__('Add New', 'listingpro'); ?>
                        </button>
                    <?php
                    }
                    ?>
                </ul>
            </div>


            <?php
            if (!empty($all_success)) {
            ?>
                <div class="panel-body lp-new-packages" id="lp-new-invoices">
                    <div class="lp-main-title clearfix">

                        <div class="col-md-3">
                            <p>
                                <?php esc_html_e('Campaign', 'listingpro'); ?>
                            </p>
                        </div>
                        <div class="col-md-2">
                            <p>
                                <?php esc_html_e('Listing', 'listingpro'); ?>
                            </p>
                        </div>
                        <div class="col-md-2">
                            <p>
                                <?php esc_html_e('start date', 'listingpro'); ?>
                            </p>
                        </div>
                        <div class="col-md-2">
                            <p>
                                <?php esc_html_e('end date', 'listingpro'); ?>
                            </p>
                        </div>
                        <div class="col-md-3 text-right">
                            <p>
                                <?php esc_html_e('status', 'listingpro'); ?>
                            </p>
                        </div>
                    </div>
                    <div class="tab-content clearfix">
                        <!--1-->
                        <div class="tab-pane fade in active" id="tab1default">
                            <?php lp_get_campains_inovices($all_success, $typeofcampaign, true); ?>
                        </div>
                        <!--2-->
                        <div id="tab2default" class="tab-pane">
                            <?php lp_get_campains_inovices($activeCampaigns, $typeofcampaign, false); ?> </div>
                        <!--3-->
                        <div id="tab3default" class="tab-pane">
                            <?php lp_get_campains_inovices($pendingCampaigns, $typeofcampaign, false); ?> </div>
                        <!--4-->
                        <div id="tab4default" class="tab-pane">
                            <?php lp_get_campains_inovices($inactiveCampaigns, $typeofcampaign, false); ?>
                        </div>
                    </div>
                </div>

            <?php
            } else {
            ?>
                <div class="lp-blank-section" style="min-height:inherit">
                    <div class="col-md-12 blank-left-side">
                        <img alt='image' src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
                        <h1>
                            <?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?>
                        </h1>
                        <p class="margin-bottom-20">
                            <?php echo esc_html__('You must be here for the first time. If you like to add some thing, click the button below.', 'listingpro'); ?>
                        </p>
                        <button type="button" class="lp-let-start-btn lp-add-new-btn lp-add-new-btn add-new-open-form">
                            <span>
                                <i class="fa fa-plus" aria-hidden="true">
                                </i>
                            </span>
                            <?php echo esc_html__('Add new ad', 'listingpro'); ?>
                        </button>
                    </div>
                </div>
            <?php } ?>


        </div>

        <?php
        if (!empty($all_success)) {
        ?>
            <div class="col-md-3 lp-right-panel-height lp-right-static">
                <div class=" padding-right-0">
                    <div class="lp-ad-click-outer lp_selected_active_ad">
                        <div class="lploading"></div>
                        <div class="lp-general-section-title-outer">
                            <?php
                            if ($admode == "adsperclick") {
                            ?>
                                <div class="lp-ad-click-inner" id="lp-ad-click-inner">
                                    <div class="lp-ad-details-outer">
                                        <div class="lp-total-clicks">
                                            <div class="lp-total-clicks-inner">
                                                <?php
                                                if (empty($selectedClicks)) {
                                                    $selectedClicks = esc_html__('No', 'listingpro');
                                                }
                                                ?>
                                                <h4>
                                                    <?php echo esc_attr($selectedClicks); ?>
                                                </h4>
                                                <h5>
                                                    <?php echo esc_html__('clicks', 'listingpro'); ?>
                                                </h5>
                                            </div>
                                        </div>
                                        <p>
                                            <?php echo esc_html__('Total Ads Clicks', 'listingpro'); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php
                            } else {
                            ?>
                                <div class="lp-ad-click-inner" id="lp-ad-click-inner" style="display:none">
                                    <div class="lp-ad-details-outer">
                                        <div class="lp-total-clicks">
                                            <div class="lp-total-clicks-inner">
                                                <?php
                                                if (empty($selectedClicks)) {
                                                    $selectedClicks = esc_html__('No', 'listingpro');
                                                }
                                                ?>
                                                <h4>
                                                    <?php echo esc_attr($selectedClicks); ?>
                                                </h4>
                                                <h5>
                                                    <?php echo esc_html__('clicks', 'listingpro'); ?>
                                                </h5>
                                            </div>
                                        </div>
                                        <p>
                                            <?php echo esc_html__('Total Ads Clicks', 'listingpro'); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>

                        <div class="lp-general-section-title-outer">



                            <div class="lp-ad-click-inner" id="lp-ad-click-innerr">
                                <div class="lp-invoices-all-statss">

                                    <p class="clarfix lp-general-section-title comment-reply-title">
                                        <?php echo esc_html__('PLACEMENTS', 'listingpro'); ?>

                                    </p>


                                    <div class="clearfix lp-ads-placement-container padding-top-15 padding-bottom-15">
                                        <div class="col-md-12">
                                            <ul class="lp-ad-all-attached-packages clearfix">
                                                <?php echo wp_kses_post($selctedPackages); ?>
                                            </ul>

                                        </div>
                                    </div>

                                </div>

                                <?php
                                if ($admode == "adsperclick") {

                                ?>
                                    <p id="" class="clarfix lp-general-section-title comment-reply-title active">
                                        <?php echo esc_html__('Purchase Details', 'listingpro'); ?>
                                        <i class="fa fa-angle-right" aria-hidden="true">
                                        </i>
                                    </p>
                                    <ul class="lp-ad-all-stats clearfix lp-ad-packages-stats">
                                        <li class="lp-ad-payment-price">
                                            <p>
                                                <?php echo esc_html__('TOTAL BUDGET ', 'listingpro'); ?>
                                            </p>
                                            <h4 class="facmount">
                                                <?php echo esc_attr($selectedPaidAmount . ' ' . $currency); ?>
                                            </h4>
                                        </li>
                                        <li class='adsremaining'>
                                            <p>
                                                <?php echo esc_html__('REMAINING BALANCE', 'listingpro'); ?>
                                            </p>

                                            <h4 class="faccredit">
                                                <?php echo esc_attr($selectedCredit . ' ' . $currency); ?>
                                            </h4>
                                        </li>
                                        <li class="lp-ad-payment-tax-price">
                                            <p>
                                                <?php echo esc_html__('TAX Price ', 'listingpro'); ?>
                                            </p>
                                            <h4 class="facmount">
                                                <?php
                                                if (isset($taxPrice) && $taxPrice > 0) {
                                                    echo esc_attr($taxPrice . ' ' . $currency);
                                                } else {
                                                    echo esc_attr('0 ' . $currency);
                                                }
                                                ?>
                                            </h4>
                                        </li>
                                        <?php if (isset($budgetwithtax) && $budgetwithtax > 0) { ?>
                                            <li class="lp-ad-payment-with-tax">
                                                <p>
                                                    <?php echo esc_html__('TOTAL PAID AMOUNT ', 'listingpro'); ?>
                                                </p>
                                                <h4 class="facmount">
                                                    <?php echo esc_attr($budgetwithtax . ' ' . $currency); ?>
                                                </h4>
                                            </li>
                                        <?php } else { ?>
                                            <li class="lp-ad-payment-with-tax">
                                                <p>
                                                    <?php echo esc_html__('TOTAL PAID AMOUNT ', 'listingpro'); ?>
                                                </p>
                                                <h4 class="facmount">
                                                    <?php echo esc_attr($selectedPaidAmount . ' ' . $currency); ?>
                                                </h4>
                                            </li>
                                        <?php } ?>
                                        <li class="lp-ad-payment-method lp-ad-all-stats clearfix lp-ad-packages-stats">
                                            <?php echo wp_kses_post($selctedMethod); ?>
                                        </li>
                                        <li>
                                            <p>
                                                <?php echo esc_html__('TRANSACTION ID', 'listingpro'); ?>
                                            </p>

                                            <h4 class="faccredit adstransid">
                                                <?php echo esc_attr($dbtID); ?>
                                            </h4>
                                        </li>

                                    </ul>
                                <?php
                                }
                                ?>

                                <ul class="lp-ad-all-stats clearfix lp-ad-packages-stats">
                                    <?php
                                    if ($admode != "adsperclick") {
                                    ?>
                                        <p id="" class="clarfix lp-general-section-title comment-reply-title active">
                                            <?php echo esc_html__('Purchase Details', 'listingpro'); ?>
                                            <i class="fa fa-angle-right" aria-hidden="true">
                                            </i>
                                        </p>


                                    <?php
                                    }
                                    ?>

                                    <?php
                                    if ($admode != "adsperclick") {
                                    ?>

                                        <li class="lp-ad-payment-price">
                                            <p>
                                                <?php echo esc_html__('Amount paid', 'listingpro'); ?>
                                            </p>
                                            <?php echo wp_kses_post($selctedAmount); ?>
                                        </li>
                                        <li class='adsremaining'>
                                            <p>
                                                <?php echo esc_html__('Duration', 'listingpro'); ?>
                                            </p>

                                            <h4 class="faccredit">
                                                <?php echo esc_attr($duration) . ' ' . esc_html__('Days', 'listingpro'); ?>
                                            </h4>
                                        </li>
                                        <li class="lp-ad-payment-method lp-ad-all-stats clearfix lp-ad-packages-stats">
                                            <?php echo wp_kses_post($selctedMethod); ?>
                                        </li>
                                        <li>
                                            <p>
                                                <?php echo esc_html__('TRANSACTION ID', 'listingpro'); ?>
                                            </p>

                                            <h4 class="faccredit adstransid">
                                                <?php echo wp_kses_post($dbtID); ?>
                                            </h4>
                                        </li>
                                    <?php
                                    }
                                    ?>
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>
    <!--campaign form part-->
    <div class="clearfix lp-ad-step-two lp-ads-form-container">

        <div class="row">

            <div class="lp-review-sorting clearfix lp-compaignForm-leftside">
                <a href="" class="lp-view-all-btn"><i class="fa fa-angle-left" aria-hidden="true"></i> <?php echo esc_html__('All Ad Campaign', 'listingpro'); ?></a>
                <h5 class="margin-top-0 clearfix"><?php echo esc_html__('Create Ad Campaign', 'listingpro'); ?>
                    <a data-imgsrc="<?php echo get_template_directory_uri(); ?>/assets/images/examples/example-campaign.jpg" data-expandimage="bird" id="pop" href="" class="lp-view-larg-btn"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo esc_html__('Full View Example', 'listingpro'); ?></a>
                </h5>
            </div>
        </div>


        <div class="col-md-9 lp-compaignForm-leftside">
            <!--left side of camp-->

            <form id="lp-new-ad-compaignForm" method="POST" name="lp-new-ad-compaignForm" data-string="<?php esc_html_e('Minimum Budget Must Be Greater Then', 'listingpro'); ?>" data-type="<?php echo esc_attr($typeofcampaign); ?>" data-tax-percent="<?php echo wp_kses_post($taxPerfent); ?>" data-camp-currency="<?php echo wp_kses_post($currencyCode); ?>" data-paid_currency="<?php echo lp_theme_option('currency_paid_submission'); ?>" action="<?php echo esc_url($ads_promo_url); ?>">

                <div class="panel-body margin-bottom-30 lpcamppadding0">
                    <div class="lp-listing-selecter clearfix background-white  lp-coupon-box-row ">
                        <div class="form-group">
                            <div class="lp-listing-selecter-content">
                                <h5 class="lp-dashboard-top-label margin-bottom-15 margin-top-5">
                                    <?php esc_html_e('Choose a listing to ad campaign', 'listingpro'); ?><span class="camp-required">*</span>
                                </h5>

                                <div class="lp-listing-selecter-drop">
                                    <?php
                                    echo lp_get_listing_dropdown('', 'select2-ajaxx lp-search-listing-camp', 'lp_ads_for_listing', null, null);
                                    ?>
                                </div>

                            </div>
                        </div>

                    </div>


                    <div class="lp-listing-selecter clearfix background-white">
                        <div class="form-group col-sm-12">
                            <div class="lp-listing-selecter-content margin-top-5">
                                <?php
                                if ($typeofcampaign == "adsperduration") {
                                ?>
                                    <h5 class="lp-dashboard-top-label margin-bottom-15">
                                        <?php esc_html_e('Set Days For This Campaign', 'listingpro'); ?>
                                        <span class="help-text">
                                            <span class="help"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                                            <span class="help-tooltip">
                                                <p><?php esc_html_e('Please Put Days For This Ad Campaign ( Only Numeric Value )', 'listingpro'); ?></p>
                                            </span>
                                            <span class="camp-required">*</span>
                                        </span>

                                    </h5>

                                <?php
                                } elseif ($typeofcampaign == "adsperclick") { ?>
                                    <h5 class="lp-dashboard-top-label margin-bottom-15">
                                        <?php esc_html_e('Set Your Lifetime Budget', 'listingpro'); ?>
                                        <span class="help-text">
                                            <span class="help"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                                            <span class="help-tooltip">
                                                <p><?php esc_html_e('Please Put Budget For This Ad Campaign ( Without Currency Sign )', 'listingpro'); ?></p>
                                            </span>
                                            <span class="camp-required">*</span>
                                        </span>

                                    </h5>
                                    <p> <?php esc_html_e(' ', 'listingpro'); ?> </p>
                                <?php } ?>

                                <?php
                                if ($typeofcampaign == "adsperduration") {
                                ?>
                                    <input name="adsduration_pd" autocomplete="off" type="number" min="1" class=" lp-dashboard-text-field lp-search-listing" name="select" id="" placeholder="20">
                                <?php
                                } elseif ($typeofcampaign == "adsperclick") { ?>
                                    <input name="adsprice_pc" autocomplete="off" type="number" min="1" class="lp-search-listing" name="select" id="" placeholder="100">
                                <?php } ?>

                            </div>
                        </div>

                    </div>

                    <div class="lp-listing-selecter clearfix background-white">
                        <div class="form-group col-sm-12 lp-ad-step-two-inner">
                            <div class="lp-listing-selecter-content">
                                <h5 class="lp-dashboard-top-label margin-bottom-10">
                                    <?php esc_html_e('Select all Placement', 'listingpro'); ?>
                                    <span class="help-text">
                                        <span class="help"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                                        <span class="help-tooltip">
                                            <p><?php esc_html_e('Please Select Your Placement ( Where Ad`s Show ) ', 'listingpro'); ?></p>
                                        </span>
                                        <span class="camp-required">*</span>
                                    </span>

                                </h5>
                                <p>
                                    <?php esc_html_e(' ', 'listingpro'); ?>
                                </p>
                            </div>
                            <div class="row">
                                <?php
                                if (!empty($showSpotlightAds)) {
                                    $previewURL = get_template_directory_uri() . '/assets/images/preview1.jpg';
                                    $campaigns_name = esc_html__('Spotlight', 'listingpro');
                                ?>
                                    <div class="col-sm-4">
                                        <label for="spotlightcmp" class="checkbox lp-camp-lbl">
                                            <div class="lp-select-ad-place text-center">
                                                <input id="spotlightcmp" data-title="<?php echo wp_kses_post($campaigns_name); ?>" data-taxprice="<?php echo wp_kses_post($spotlightTax); ?>" data-orgprice="<?php echo wp_kses_post($spotOnlyprice); ?>" data-price="<?php echo wp_kses_post($spotOrgprice); ?>" data-preview="<?php echo esc_url($previewURL); ?>" type="checkbox" name="lpadsoftype[]" class="searchtags" value="lp_random_ads">
                                                <label></label>

                                                <div class="lp-select-ad">

                                                    <div class="lp-ad-price-content text-center">

                                                        <h5>
                                                            <?php echo esc_html__('Spotlight', 'listingpro'); ?>
                                                            <span class="help-text">
                                                                <span class="help"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                                                                <span class="help-tooltip">
                                                                    <p><?php esc_html_e('Select This Place For Ad To Place On Home Page', 'listingpro'); ?></p>
                                                                </span>

                                                            </span>
                                                        </h5>

                                                    </div>

                                                    <div class="lp-ad-location-image" data-toggle="modal" data-target="#ad_preview">
                                                        <div class="lp-ad-location-image-overlay"><i class="fa fa-eye" aria-hidden="true"></i></div>
                                                        <img data-model_img_src_ad="<?php echo get_template_directory_uri(); ?>/assets/images/examples/ads_camp_01.jpg" alt='image' src="<?php echo get_template_directory_uri(); ?>/assets/images/spotlight.png" />

                                                    </div>
                                                    <div class="lp-ad-price-content text-center">

                                                        <button type="button">
                                                            <?php echo wp_kses_post($spotlightPrice); ?>
                                                        </button>
                                                        <h6><?php echo wp_kses_post($prefixCampaign); ?></h6>
                                                    </div>

                                                </div>
                                            </div>

                                        </label>

                                    </div>
                                <?php
                                }
                                if (!empty($showSearchtopAds)) {
                                    $previewURL = get_template_directory_uri() . '/assets/images/preview1.jpg';
                                    $campaigns_name = esc_html__('Top Of Search', 'listingpro');
                                ?>
                                    <div class="col-sm-4">
                                        <label for="camp-for-search" class="checkbox lp-camp-lbl">
                                            <div class="lp-select-ad-place text-center">
                                                <input id="camp-for-search" data-taxprice="<?php echo wp_kses_post($searchTax); ?>" data-orgprice="<?php echo wp_kses_post($searchOnlyprice); ?>" data-title="<?php echo wp_kses_post($campaigns_name); ?>" data-price="<?php echo wp_kses_post($searchOrgprice); ?>" data-preview="<?php echo esc_url($previewURL); ?>" type="checkbox" name="lpadsoftype[]" class="searchtags" value="lp_top_in_search_page_ads">
                                                <label></label>

                                                <div class="lp-select-ad">

                                                    <div class="lp-ad-price-content text-center">

                                                        <h5>
                                                            <?php echo esc_html__('Top Of Search', 'listingpro'); ?>
                                                            <span class="help-text">
                                                                <span class="help"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                                                                <span class="help-tooltip">
                                                                    <p><?php esc_html_e('Select This Option To Place Your Ad On Top Of The Search Result', 'listingpro'); ?></p>
                                                                </span>

                                                            </span>
                                                        </h5>

                                                    </div>

                                                    <div class="lp-ad-location-image" data-toggle="modal" data-target="#ad_preview">
                                                        <div class="lp-ad-location-image-overlay"><i class="fa fa-eye" aria-hidden="true"></i></div>
                                                        <img data-model_img_src_ad="<?php echo get_template_directory_uri(); ?>/assets/images/examples/ads_camp_02.jpg" alt='image' src="<?php echo get_template_directory_uri(); ?>/assets/images/topofsearch.png" />
                                                    </div>
                                                    <div class="lp-ad-price-content text-center">

                                                        <button type="button">
                                                            <?php echo wp_kses_post($searchpageprice); ?>
                                                        </button>
                                                        <h6><?php echo wp_kses_post($prefixCampaign); ?></h6>
                                                    </div>

                                                </div>
                                            </div>

                                        </label>

                                    </div>
                                <?php
                                }
                                if (!empty($showDetailpageAds)) {
                                    $previewURL = get_template_directory_uri() . '/assets/images/preview1.jpg';
                                    $campaigns_name = esc_html__('Sidebar', 'listingpro');
                                ?>
                                    <div class="col-sm-4">
                                        <label for="for-sidebar-camp" class="checkbox lp-camp-lbl">
                                            <div class="lp-select-ad-place text-center">
                                                <input id="for-sidebar-camp" data-taxprice="<?php echo wp_kses_post($detailsTax); ?>" data-orgprice="<?php echo wp_kses_post($detailOnlyprice); ?>" data-title="<?php echo wp_kses_post($campaigns_name); ?>" data-price="<?php echo wp_kses_post($detailOrgprice); ?>" data-preview="<?php echo wp_kses_post($previewURL); ?>" type="checkbox" name="lpadsoftype[]" class="searchtags" value="lp_detail_page_ads">
                                                <label></label>

                                                <div class="lp-select-ad">

                                                    <div class="lp-ad-price-content text-center">

                                                        <h5>
                                                            <?php echo esc_html__('Sidebar', 'listingpro'); ?>
                                                            <span class="help-text">
                                                                <span class="help"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                                                                <span class="help-tooltip">
                                                                    <p><?php esc_html_e('Select This Option To Place Your Ad On Listing Detail Page Side Bar', 'listingpro'); ?></p>
                                                                </span>

                                                            </span>
                                                        </h5>

                                                    </div>

                                                    <div class="lp-ad-location-image" data-toggle="modal" data-target="#ad_preview">
                                                        <div class="lp-ad-location-image-overlay"><i class="fa fa-eye" aria-hidden="true"></i></div>
                                                        <img data-model_img_src_ad="<?php echo get_template_directory_uri(); ?>/assets/images/examples/ads_camp_03.jpg" alt='image' src="<?php echo get_template_directory_uri(); ?>/assets/images/listingsidebar.png" />
                                                    </div>
                                                    <div class="lp-ad-price-content text-center">

                                                        <button type="button">
                                                            <?php echo wp_kses_post($detailpageprice); ?>
                                                        </button>
                                                        <h6><?php echo wp_kses_post($prefixCampaign); ?></h6>
                                                    </div>

                                                </div>
                                            </div>

                                        </label>

                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div id="ad_preview" class="modal fade margin-top-60" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-body text-center">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <img src="" alt="Preview" id="imgsrc">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--payment method-->
                    <div class="lp-listing-selecter clearfix background-white">
                        <div class="lp_payment_methods_ads lp-select-payement-outer">
                            <div class="form-group col-sm-12">
                                <h5 class="lp-dashboard-top-label margin-bottom-0">
                                    <?php esc_html_e('Select a Payment Method', 'listingpro'); ?>
                                    <span class="help-text">
                                        <span class="help"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></span>
                                        <span class="help-tooltip">
                                            <p><?php esc_html_e('Please Select Your Payment Method To Pay Ad Charges', 'listingpro'); ?></p>
                                        </span>
                                        <span class="camp-required">*</span>
                                    </span>

                                </h5>

                                <?php
                                $enable_payment_methdos = apply_filters('lp_checkout_payment_methods', false);
                                if (!empty($showPaypal) || !empty($showStripe) || !empty($showWire) || !empty($showPaystack) || $enable_payment_methdos == true) {
                                ?>
                                    <div class="row">
                                        <?php
                                        if (!empty($showPaypal)) {
                                        ?>
                                            <div class="col-sm-4">

                                                <div class="lp-payement-images">


                                                    <input class="radio_checked" type="radio" name="method" id="adPaypalOpt" value="paypal">
                                                    <label class="lp-lbl-with-radio"></label>

                                                    <label for="adPaypalOpt" class="lp-label-wrp">

                                                        <img alt='image' src="<?php echo get_template_directory_uri() . '/assets/images/paypal.png'; ?>" />

                                                    </label>
                                                </div>

                                            </div>
                                        <?php
                                        }
                                        if (!empty($showStripe)) {
                                        ?>
                                            <div class="col-sm-4">
                                                <div class="lp-payement-images">


                                                    <input class="radio_checked" type="radio" name="method" id="adStripeOpt" value="stripe">
                                                    <label class="lp-lbl-with-radio"></label>

                                                    <label for="adStripeOpt" class="lp-label-wrp">

                                                        <img alt='image' src="<?php echo get_template_directory_uri() . '/assets/images/stripe.png'; ?>" />

                                                    </label>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        if (!empty($showWire)) {
                                        ?>
                                            <div class="col-sm-4">
                                                <div class="lp-payement-images">


                                                    <input class="radio_checked" type="radio" name="method" id="adWireOpt" value="wire">
                                                    <label class="lp-lbl-with-radio"></label>

                                                    <label for="adWireOpt" class="lp-label-wrp">

                                                        <img alt='image' src="<?php echo get_template_directory_uri() . '/assets/images/wire.png'; ?>" />

                                                    </label>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        do_action('lp_add_custom_payment_method_html', 'ads');
                                        ?>
                                    </div>
                                <?php
                                }
                                ?>
                                <?php
                                if (wp_is_mobile() && !empty($showcampaingprocess)) {
                                ?>
                                    <div class="clearfix">
                                        <div class="checkbox">
                                            <input id="lp-campaignTerms" name="lp-campaignTerms" type="checkbox" class="lp-campain-terms-cond" value="">
                                            <label for="lp-campaignTerms"><?php echo esc_html__('I accept that I have reviewed all the above details before proceedings. I understand the ad campaign cannot be edited or cancelled without getting in touch with the site admin', 'listingpro'); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 lp-menu-save-btns clearfix">
                                        <button class="lp-cancle-btn">
                                            <?php echo esc_html__('Cancel', 'listingpro'); ?>
                                        </button>
                                        <button type="button" class="lp-save-btn lp_campaign_paynow startpayforcampaignsss" disabled>
                                            <i class="fa fa-credit-card" aria-hidden="true">
                                            </i>
                                            <?php echo esc_html__('Pay Now', 'listingpro'); ?>
                                        </button>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>

                        <!--end payment method-->






                        <?php


                        if ($typeofcampaign == "adsperduration") {
                        ?>
                            <input type="hidden" name="ads_days" value="0">
                        <?php
                        }
                        $taxRate = '';
                        if (!empty(lp_theme_option('lp_tax_swtich'))) {
                            $taxRate = lp_theme_option('lp_tax_amount');
                        }
                        ?>
                        <input type="hidden" name="ads_price" value="0">
                        <input type="hidden" name="adsTypeval" value="<?php echo wp_kses_post($adsTypeval); ?>">
                        <input type="hidden" name="currency" value="<?php echo wp_kses_post($currency); ?>">
                        <input type="hidden" name="lp_ads_title" value="<?php echo esc_html__('Campaign Payment', 'listingpro'); ?>">
                        <input type="hidden" name="taxprice" value="">
                        <input type="hidden" name="func" value="start ads">

                    </div>
                    <?php
                    if (!wp_is_mobile()) {
                    ?>
                        <div class="lp-camp-bottom-secton clearfix">
                            <div class="checkbox">
                                <input id="lp-campaignTerms" name="lp-campaignTerms" type="checkbox" class="lp-campain-terms-cond" value="">
                                <label for="lp-campaignTerms"><?php echo esc_html__('I accept that I have reviewed all the above details before proceedings. I understand the ad campaign cannot be edited or cancelled without getting in touch with the site admin', 'listingpro'); ?></label>
                            </div>
                            <div class="lp-menu-save-btns lp-save-btn-container background-white">
                                <a href="" class="lp-unsaved-btn"><?php echo esc_html__('Unsaved Event', 'listingpro'); ?></a>
                                <button type="button" class="lp-save-btn lp_campaign_paynow startpayforcampaignsss" disabled>
                                    <i class="fa fa-credit-card" aria-hidden="true">
                                    </i>
                                    <?php echo esc_html__('Pay Now', 'listingpro'); ?>
                                </button>
                                <button class="lp-cancle-btn pull-right lp-margin-right-10">
                                    <?php echo esc_html__('Cancel', 'listingpro'); ?>
                                </button>
                            </div>
                        </div>
                    <?php
                    }
                    ?>

            </form>

        </div>


    </div>
    <!-- campaign form ends-->

    <div class="col-md-3 lp-compaignForm-righside">
        <!-- right site of camp-->
        <div class="lp-listing-selecter clearfix">
            <div class="col-md-12">
                <div class="lp-listing-selecter-content">
                    <h5><?php echo esc_html__('Summary', 'listingpro'); ?></h5>
                </div>


                <div class="row lp-camp-create-title">
                    <div class="col-md-8">
                        <h6><?php echo esc_html__('ITEM', 'listingpro'); ?></h6>
                    </div>

                    <div class="col-md-4">
                        <h6><?php echo esc_html__('AMOUNT', 'listingpro'); ?></h6>
                    </div>
                </div>



                <div class="row lp-camp-created-list">
                    <?php if (!empty($showSpotlightAds)) { ?>
                        <div class="col-md-8 lpcampain-spotlight">
                            <h6><i class="fa fa-circle greencheck graycheck"></i> <?php echo esc_html__('Spotlight Ad', 'listingpro'); ?></h6>
                        </div>

                        <div class="col-md-4 lpcampain-spotlight lpcampain-spotlight-price">
                            <h6><?php echo wp_kses_post($spotlightPrice) . ' <span class="lp-camp-typespan">' . $adsType . '</span>'; ?></h6>
                        </div>

                    <?php }
                    if (!empty($showSearchtopAds)) { ?>
                        <div class="col-md-8 lpcampain-topsearch">
                            <h6><i class="fa fa-circle greencheck graycheck"></i> <?php echo esc_html__('Top of Search Ad', 'listingpro'); ?></h6>
                        </div>

                        <div class="col-md-4 lpcampain-topsearch lpcampain-topsearch-price">
                            <h6><?php echo wp_kses_post($searchpageprice) . ' <span class="lp-camp-typespan">' . $adsType . '</span>'; ?></h6>
                        </div>
                    <?php }
                    if (!empty($showDetailpageAds)) { ?>
                        <div class="col-md-8 lpcampain-sidebar margin-bottom-15">
                            <h6><i class="fa fa-circle greencheck graycheck"></i> <?php echo esc_html__('Sidebar Ad', 'listingpro'); ?></h6>
                        </div>

                        <div class="col-md-4 lpcampain-sidebar lpcampain-sidebar-price">
                            <h6><?php echo wp_kses_post($detailpageprice) . ' <span class="lp-camp-typespan">' . $adsType . '</span>'; ?></h6>
                        </div>
                    <?php } ?>
                </div>
                <div class="lp-camp-border margin-bottom-20"></div>


                <div class="row lp-camp-created-list">
                    <?php
                    if ($typeofcampaign == "adsperduration") { ?>

                        <div class="col-md-8">
                            <h6><?php echo esc_html__('Duration', 'listingpro'); ?></h6>
                        </div>

                        <div class="col-md-4 lp-cmp-duration" data-days-text="<?php echo esc_attr__('Days', 'listingpro'); ?>">
                            <h6><?php echo esc_html__('Days', 'listingpro'); ?></h6>
                        </div>
                    <?php
                    } elseif ($typeofcampaign == "adsperclick") {
                    ?>


                        <div class="col-md-8">
                            <h6><?php echo esc_html__('Budget', 'listingpro'); ?></h6>
                        </div>

                        <div class="col-md-4 lp-cmp-budget">
                            <h6>0</h6>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="lp-camp-border margin-bottom-20"></div>


                <div class="row lp-camp-created-list">
                    <div class="col-md-8">
                        <h6 class="margin-bottom-0"><?php echo esc_html__('Subtotal', 'listingpro'); ?></h6>
                    </div>

                    <div class="col-md-4 lp-cmp-subtotal">
                        <h6>0</h6>
                    </div>

                    <?php
                    if (!empty($isTax)) { ?>
                        <div class="col-md-8">
                            <h6 class="margin-top-0"><?php echo esc_html__('Tax', 'listingpro'); ?></h6>
                        </div>

                        <div class="col-md-4 lp-cmp-taxtotal">
                            <h6 class="margin-top-0">0</h6>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="lp-camp-border"></div>


                <div class="row lp-camp-created-list padding-bottom-10 padding-top-10">
                    <div class="col-md-8">
                        <h5><?php echo esc_html__('Total', 'listingpro'); ?></h5>
                    </div>

                    <div class="col-md-4 lp-cmp-alltotal">
                        <h5>0</h5>
                    </div>
                </div>



            </div>
        </div>

        <div class="lp-camp-bottom-summary">
            <div class="lp-listing-selecter-content">
                <div class="col-md-12">
                    <h5><i><?php echo esc_html__('Get results with no surprises', 'listingpro'); ?></i></h5>
                    <div class="campfeatures">
                        <p><i class="fa fa-check greencheck"></i> <?php echo esc_html__('Get on top of the organic results instantly.', 'listingpro'); ?></p>

                        <p><i class="fa fa-check greencheck"></i> <?php echo esc_html__('Pay only when ads are clicked.', 'listingpro'); ?></p>

                        <p><i class="fa fa-check greencheck"></i> <?php echo esc_html__('Never spend more than your budget limit.', 'listingpro'); ?></p>

                    </div>


                    <h5><?php echo esc_html__('Need help getting started?', 'listingpro'); ?></h5>

                    <h5><i class="fa fa-phone"></i> <?php echo esc_html__('Call (877) 777-7777', 'listingpro'); ?></h5>



                </div>
            </div>
        </div>
    </div>

</div>
<?php
global $listingpro_options;
if( $listingpro_options['listingpro_scastripe_switch'] == 'yes' ){
	?>
	<script>
		var packages = [];
		document.getElementById("lp-new-ad-compaignForm").reset();
		jQuery('input[name="lpadsoftype[]"]').click(function() {
			if (jQuery(this).is(':checked')) {
				packages.push(jQuery(this).attr('value'));
			} else {
				var i = packages.indexOf(jQuery(this).val());
				if (i != -1) {
					packages.splice(i, 1);
				}
			}
		});
	</script>
	<?php
	wp_enqueue_script('stripe-sca');
}else{
	$pubilshableKey = '';
	$secritKey = '';
	$pubilshableKey = lp_theme_option('stripe_pubishable_key');
	$secritKey = lp_theme_option('stripe_secrit_key');
	$ajaxURL = '';
	$ajaxURL = admin_url('admin-ajax.php');


	?>
	<script>
		var listing_id;
		var packages = [];
		var taxprice = '';
		document.getElementById("lp-new-ad-compaignForm").reset();
		jQuery('input[name="lpadsoftype[]"]').click(function() {
			if (jQuery(this).is(':checked')) {
				packages.push(jQuery(this).attr('value'));
			} else {
				var i = packages.indexOf(jQuery(this).val());
				if (i != -1) {
					packages.splice(i, 1);
				}
			}
		});
		jQuery('#lp-new-ad-compaignForm').on('submit', function(e) {
			$this = jQuery(this);
			taxprice = $this.find('input[name="taxprice"]').val();
		});
		var handler = StripeCheckout.configure({
			key: "<?php echo wp_kses_post($pubilshableKey); ?>",
			image: "https://stripe.com/img/documentation/checkout/marketplace.png",
			locale: "auto",
			token: function(token) {
				token_id = token.id;
				token_email = token.email;
				jQuery('body').addClass('listingpro-loading');
				lpTotalPrice = '';
				if (jQuery('input[name=ads_price]').val()) {
					lpTotalPrice = jQuery('input[name="ads_price"]').val();
				} else if (jQuery('input[name="ads_price"]').val()) {
					lpTotalPrice = jQuery('input[name="ads_price"]').val();
				}

				if (jQuery('#lp-new-ad-compaignForm').data('type') == "adsperclick") {
					totalPrice = jQuery('input[name="adsprice_pc"]').val();
				} else {
					totalPrice = jQuery('input[name="ads_price"]').val();
				}
				jQuery.ajax({
					type: "POST",
					dataType: "json",
					url: "<?php echo esc_url($ajaxURL); ?>",
					data: {
						"action": "listingpro_save_package_stripe",
						"token": token_id,
						"email": token_email,
						"listing": jQuery('select[name=lp_ads_for_listing]').val(),
						"packages": packages,
						"lpTOtalprice": lpTotalPrice,
						"adsTypeval": jQuery('input[name=adsTypeval]').val(),
						"ads_days": jQuery('input[name=ads_days]').val(),
						"ads_price": totalPrice,
						"taxprice": jQuery('input[name=taxprice]').val(),
					},
					success: function(res) {
						jQuery('body').removeClass('listingpro-loading');
						if (res.status == "success") {
							redURL = res.redirect;
							window.location.href = redURL;
						} else {
							alert(res.status);
							jQuery('body').removeClass('listingpro-loading');
						}
					},
					error: function(errorThrown) {
						jQuery('body').removeClass('listingpro-loading');
						alert(errorThrown);
					}
				});
			}
		});
		window.addEventListener("popstate", function() {
			handler.close();
		});
	</script>
<?php } ?>