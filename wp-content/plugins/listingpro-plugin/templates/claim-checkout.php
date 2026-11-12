<?php

global $wpdb, $listingpro_options;
$dbprefix = '';
$dbprefix = $wpdb->prefix;
$user_ID = '';
$user_ID = get_current_user_id();
$currency = '';
$currency = $listingpro_options['currency_paid_submission'];
$currency_symbol = listingpro_currency_sign();
$currency_position = '';
$currency_position = $listingpro_options['pricingplan_currency_position'];
$enableTax = false;
$outputClaimLisitng = null;
$Taxrate = '';
$Taxtype = '';
if ($listingpro_options['lp_tax_swtich'] == "1") {
    $enableTax = true;
    $Taxrate = $listingpro_options['lp_tax_amount'];
    $Taxtype = $listingpro_options['lp_tax_label'];
}

$claimListing_id =  $_GET['listing_id'];
$post_id =  $claimListing_id;
$claim_plan =  $_GET['claim_plan'];
$user_id =  $_GET['user_id'];
$claim_post =  $_GET['claim_post'];
$plan_id = $claim_plan;
$authorId = listing_get_metabox_by_ID('claimer', $claim_post);
$claimeDListing = listing_get_metabox_by_ID('claimed_listing', $claim_post);
$claim_status = listing_get_metabox_by_ID('claim_status', $claim_post);
$claimPlan = listing_get_metabox_by_ID('claim_plan', $claim_post);
$invalidClaim = false;
if ($claim_status == "pending") {
    if ($authorId == $user_id && $claimListing_id == $claimeDListing && $plan_id == $claimPlan) {
        update_post_meta($post_id, 'claimpID', $claim_post);
        $outputClaimLisitng .= '<div class="lp-checkout-wrapper lp-checkout-wrapper-new ' . $claim_post . '">';

        $plan_price = get_post_meta($plan_id, 'plan_price', true);
        $plan_duration = get_post_meta($plan_id, 'plan_time', true);
        if (empty($plan_duration)) {
            $plan_duration = esc_html__('Unlimited', 'listingpro-plugin');
        }
        $plan_type = get_post_meta($plan_id, 'plan_package_type', true);
        $terms = wp_get_post_terms($post_id, 'listing-category', array());
        $price = '';
        $price = $plan_price;
        $deafaultFeatImg = lp_default_featured_image_listing();

        $catname = '';
        if (count($terms) > 0) {
            $catname = $terms[0]->name;
        }
        if (!empty($plan_price)) {
            $outputClaimLisitng .= '<div class="lp-user-listings clearfix" data-plantype="' . $plan_type . '" data-recurringtext="' . esc_html__('Recurring Payment?', 'listingpro-plugin') . '"><div class="col-md-12 col-sm-12 col-xs-12 lp-listing-clm lp-checkout-page-outer lp-checkout-page-outer-new">';

            $outputClaimLisitng .= '<div class="col-md-10 col-sm-6 col-xs-6">';
            /* left side */
            $outputClaimLisitng .= '<h3 id="lp-checkout-lisiting-heading">' . get_the_title($post_id) . '</h3>';

            $outputClaimLisitng .= '<div class="col-md-1 col-sm-2 col-xs-6">';

            $outputClaimLisitng .= '<div class="radio radio-danger lp_price_trigger_checkout">
                                <input type="radio" name="listing_id" data-taxenable = "' . $enableTax . '" data-planid="' . $plan_id . '" data-taxrate = "' . $Taxrate . '" data-planprice = "' . $plan_price . '" data-title="' . get_the_title($plan_id) . '" data-price="' . $price . '" id="' . $post_id . '" value="' . $post_id . '">
                                <label for="' . $post_id . '">

                                </label>
                            </div>';
            $outputClaimLisitng .= '</div>';

            if (has_post_thumbnail($post_id)) {

                $imgurl = get_the_post_thumbnail_url($post_id, 'listingpro-review-gallery-thumb');
                $outputClaimLisitng .= '<input type="hidden" name="listing_img" value="' . $imgurl . '">';
                $outputClaimLisitng .= '<div class="col-md-3">';
                $outputClaimLisitng .= '<img class="img-responsive" src="' . $imgurl . '" alt="image" />';
                $outputClaimLisitng .= '</div>';
            } elseif (!empty($deafaultFeatImg)) {
                $outputClaimLisitng .= '<input type="hidden" name="listing_img" value="' . $deafaultFeatImg . '">';
                $outputClaimLisitng .= '<div class="col-md-3">';
                $outputClaimLisitng .= '<img class="img-responsive" src="' . $deafaultFeatImg . '" alt="image" />';
                $outputClaimLisitng .= '</div>';
            } else {
                $outputClaimLisitng .= '<div class="col-md-3">';
                $outputClaimLisitng .= '<img class="img-responsive" src="' . esc_url('https://placehold.co/80x80') . '" alt="image" />';
                $outputClaimLisitng .= '</div>';
            }

            $outputClaimLisitng .= '<div class="col-md-7">';
            $outputClaimLisitng .= '<span class="lp-booking-dt"><p>' . esc_html__('Date: ', 'listingpro-plugin') .
                get_the_date('', $post_id) . '</p></span>';

            $outputClaimLisitng .= '<span class="lp-persons"><p>' . esc_html__('Category: ', 'listingpro-plugin')
                . $catname . '</p></span>';

            $outputClaimLisitng .= '<span class="lp-duration"><p>' . esc_html__('Duration: ', 'listingpro-plugin') . $plan_duration . esc_html__(' Days', 'listingpro-plugin') . '</p></span>';

            $outputClaimLisitng .= '</div>';


            /* left side ends*/
            $outputClaimLisitng .= '</div>';


            $outputClaimLisitng .= '<div class="col-md-2 col-sm-6 col-xs-6 lp-checkout-price-currency-outer">';
            /* right side */
            $outputClaimLisitng .= '<div class="lp-checkout-price-currency">';
            switch ($currency_position) {

                case ('left'):
                    $outputClaimLisitng .= $currency_symbol . $price;
                    break;

                case ('right'):
                    $outputClaimLisitng .= $price . $currency_symbol;
                    break;

                default:
                    $outputClaimLisitng .= $currency_symbol . $price;
            }
            $outputClaimLisitng .= '</div>';
            /* right side ends*/
            $outputClaimLisitng .= '</div>';

            $outputClaimLisitng .= '</div>';

            $outputClaimLisitng .= '</div>';
        }

        $outputClaimLisitng .= '<input type="hidden" name="currency" value="' . $currency . '">';
        $outputClaimLisitng .= '</div>';

        $outputClaimLisitng .= '<input type="hidden" name="listings_tax_price" value="">';
        $outputClaimLisitng .= '<input type="hidden" name="method" value="">';
        $outputClaimLisitng .= '<input type="hidden" name="func" value="start">';
        $outputClaimLisitng .= '<input type="hidden" name="plan_price" value="">';
        $outputClaimLisitng .= '<input type="hidden" name="post_title" value="">';
        $outputClaimLisitng .= '<input type="hidden" id="claimID" name="claim_id" value="' . $claim_post . '">';
        $outputClaimLisitng .= '<input type="hidden" name="listings_id" value="">';
        $outputClaimLisitng .= '<input type="hidden" name="plan_id" value="">';
        $outputClaimLisitng .= '<input type="hidden" name="post_id" value="">';
    } else {

        $invalidClaim = true;
    }
} else {
    $invalidClaim = true;
}
if (!empty($invalidClaim)) {
    $outputClaimLisitng .= '<div class="lp-checkout-wrapper lp-checkout-wrapper-new">';
    $outputClaimLisitng .= '<p>' . esc_html__('Sorry! No Paid Claim request found', 'listingpro-plugin') . '</p>';
    $outputClaimLisitng .= '</div>';
}

echo $outputClaimLisitng;
