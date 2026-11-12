<?php
    global $wpdb, $listingpro_options;
    if (!isset($_SESSION)) session_start();
	$selectThisId = '';
    $selectThisId = get_user_meta( get_current_user_id() , 'listing_id_checkout', true);

    if( empty($selectThisId) && isset($_SESSION['listing_id_checkout'])){
        $selectThisId = $_SESSION['listing_id_checkout'];
    }
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
	$Taxrate='';
	$Taxtype='';
	$outputCheck = null;
	if($listingpro_options['lp_tax_swtich']=="1"){
		$enableTax = true;
		$Taxrate = $listingpro_options['lp_tax_amount'];
		$Taxtype = $listingpro_options['lp_tax_label'];
	}
	
	$results = $wpdb->get_results( "SELECT * FROM ".$dbprefix."listing_orders WHERE user_id ='$user_ID' AND status = 'in progress' ORDER BY main_id DESC" );
	if( count($results) >0 ){
            $outputCheck .='<div class="lp-checkout-wrapper lp-checkout-wrapper-new">';
            
            foreach ( $results as $info ) {

                $checked = '';
                $active_box_class = '';
                if(isset($info->post_id)){
                    $post_id = $info->post_id;

                    if($selectThisId == $post_id){
                        $checked = 'checked';
                        $active_box_class = 'active-checkout-listing';
                    }

                }
                //$postmeta = get_post_meta($post_id, 'lp_listingpro_options', true);
                $plan_id = listing_get_metabox_by_ID('Plan_id',$post_id);
                $plan_price = get_post_meta($plan_id, 'plan_price', true);
                $plan_time = get_post_meta($plan_id, 'plan_time', true);
                $plan_duration = listing_get_metabox_by_ID('lp_purchase_days', $post_id);
                if (empty($plan_duration)) {
                    $plan_duration = esc_html__( 'Unlimited', 'listingpro-plugin');
                }
                $discounted = get_post_meta($post_id, 'discounted', true);
                $plan_type = get_post_meta($plan_id, 'plan_package_type', true);
                $terms = wp_get_post_terms( $post_id, 'listing-category', array() );
                $price = '';
                $price = $plan_price;
                $price = round($price,2);
                $deafaultFeatImg = lp_default_featured_image_listing();
                
                $listing_plan_extra_attr = apply_filters('lp_plan_extra_attr', '',  $plan_id, $post_id);
                $plan_time               = apply_filters('lp_checkout_plan_time', $plan_time,  $plan_id, $post_id);
                $rPAlrt = esc_html__('Sorry! Pricing plan associated with the current selection must have a defined duration', 'listingpro-plugin');

                $catname = '';
                if( count($terms)>0 ){
                    $catname = $terms[0]->name;
                }
                if(!empty($plan_price && $discounted == '')){

                    $outputCheck .='<div class="lp-user-listings clearfix '.$active_box_class.'" data-plantype="'.$plan_type.'" data-recurringtext="'.esc_html__('Recurring Payment?', 'listingpro-plugin').'"><div class="col-md-12 col-sm-12 col-xs-12 lp-listing-clm lp-checkout-page-outer lp-checkout-page-outer-new">';

                    $outputCheck .= '<div class="col-md-10 col-sm-6 col-xs-6">';
                        /* left side */
                    $outputCheck .= '<h3 id="lp-checkout-lisiting-heading">'.get_the_title($post_id).'</h3>';

                    $outputCheck .= '<div class="col-md-1 col-sm-2 col-xs-6">';

                        $outputCheck .='<div class="radio radio-danger lp_price_trigger_checkout">
                            <input '.$checked.' type="radio" name="listing_id" data-recurringerror=\''.$rPAlrt.'\' data-planduration=\''.$plan_duration.'\' data-taxenable = "'.$enableTax.'" data-planid="'.$plan_id.'" data-taxrate = "'.$Taxrate.'" data-planprice = "'.$plan_price.'" data-plantime = "'.$plan_time.'" data-title="'.get_the_title($plan_id).'" data-price="'.$price.'" id="'.$post_id.'" value="'.$post_id.'" '. $listing_plan_extra_attr .'>
                            <label for="'.$post_id.'"></label>
                        </div>';
                    $outputCheck .='</div>';

                    if ( has_post_thumbnail($post_id) ) { 

                        $imgurl = get_the_post_thumbnail_url($post_id, 'listingpro-review-gallery-thumb');
                        $outputCheck .= '<input type="hidden" name="listing_img" value="'.$imgurl.'">';
                        $outputCheck .='<div class="col-md-3">';
                        $outputCheck .='<img class="img-responsive" src="'.$imgurl.'" alt="image" />';
                        $outputCheck .='</div>';

                    }
                    elseif(!empty($deafaultFeatImg)){
                        $outputCheck .= '<input type="hidden" name="listing_img" value="'.$deafaultFeatImg.'">';
                        $outputCheck .='<div class="col-md-3">';
                        $outputCheck .='<img class="img-responsive" src="'.$deafaultFeatImg.'"alt="image" />';
                        $outputCheck .='</div>';
                    }
                    else {
                        $outputCheck .='<div class="col-md-3">';
                        $outputCheck .='<img class="img-responsive" src="'.esc_url('https://placehold.co/80x80').'" alt="image" />';
                        $outputCheck .='</div>';
                    }

                    $outputCheck .= '<div class="col-md-7">';
                        $outputCheck .= '<span class="lp-booking-dt"><p>'.esc_html__('Date: ','listingpro-plugin').
                        get_the_date('', $post_id).'</p></span>';

                        $outputCheck .= '<span class="lp-persons"><p>'.esc_html__('Category: ','listingpro-plugin')
                        .$catname.'</p></span>';

                        $outputCheck .= '<span class="lp-duration"><p>'.esc_html__('Duration: ','listingpro-plugin').$plan_duration.esc_html__(' Days','listingpro-plugin').'</p></span>';

                    $outputCheck .='</div>';


                        /* left side ends*/
                    $outputCheck .= '</div>';


                    $outputCheck .= '<div class="col-md-2 col-sm-6 col-xs-6 lp-checkout-price-currency-outer">';
                        /* right side */
                    $outputCheck .= '<div class="lp-checkout-price-currency">';
                    switch($currency_position){

                        case('left'):
                        $outputCheck .= $currency_symbol.$price;
                        break;

                        case('right'):
                        $outputCheck .= $price.$currency_symbol;
                        break;

                        default:
                        $outputCheck .= $currency_symbol.$price;

                    }
                    $outputCheck .='</div>';
                        /* right side ends*/
                    $outputCheck .= '</div>';

                    $outputCheck .='</div>';

                    $outputCheck .='</div>';
                }

                $outputCheck .= '<input type="hidden" name="currency" value="'.$currency.'">';
            }
            $outputCheck .='</div>';

            $outputCheck .= '<input type="hidden" name="listings_tax_price" value="">';
            $outputCheck .= '<input type="hidden" name="method" value="">';
            $outputCheck .= '<input type="hidden" name="func" value="start">';
            $outputCheck .= '<input type="hidden" name="plan_price" value="">';
            $outputCheck .= '<input type="hidden" name="post_title" value="">';
            $outputCheck .= '<input type="hidden" name="listings_id" value="">';
            $outputCheck .= '<input type="hidden" name="plan_id" value="">';
            $outputCheck .= '<input type="hidden" name="post_id" value="">';
            $outputCheck .= '<input type="hidden" name="plan_time" value="">';
            $outputCheck  = apply_filters('lp_checkout_plan_time', $outputCheck,  $plan_id, $post_id);
        }
        else{
             $outputCheck .='<p class="text-center">'.esc_html__('Sorry! You have no paid listings yet!','listingpro-plugin').'</p>';
        }
        $couponsSwitch = lp_theme_option('listingpro_coupons_switch');
        if($couponsSwitch=="yes"){
               $outputCheck .=ajax_response_markup();
        }
	echo $outputCheck;