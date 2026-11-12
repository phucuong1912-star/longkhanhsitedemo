<?php
/* for changeplan */
if(!empty($_POST['planid']) && !empty($_POST['listingid'])){
	
	global $listingpro_options;
	$listing_id = $_POST['listingid'];
	$plan_id = $_POST['planid'];
	$post_id = $listing_id;
	$plan_price = get_post_meta($plan_id, 'plan_price', true);
	$plan_duration = get_post_meta($plan_id, 'plan_time', true);
    if (empty($plan_duration)) {
        $plan_duration = esc_html__( 'Unlimited', 'listingpro-plugin');
    }
	$terms = wp_get_post_terms( $post_id, 'listing-category', array() );
	$plan_type = get_post_meta($plan_id, 'plan_package_type', true);
	$currency = '';
	$currency = $listingpro_options['currency_paid_submission'];
	$currency_symbol = listingpro_currency_sign();
	$currency_position = '';
	$currency_position = $listingpro_options['pricingplan_currency_position'];
	$enableTax = false;
	$Taxrate='';
	$Taxtype='';
	$price = '';
	$plan_price = round($plan_price,2);
	$price = $plan_price;
	
	$deafaultFeatImg = lp_default_featured_image_listing();
					
	if($listingpro_options['lp_tax_swtich']=="1"){
		$enableTax = true;
		$Taxrate = $listingpro_options['lp_tax_amount'];
		$Taxtype = $listingpro_options['lp_tax_label'];
	}
	$catname = '';
	if( count($terms)>0 ){
		$catname = $terms[0]->name;
	}
										
	listing_set_metabox('changed_planid',$plan_id, $listing_id);
		$output .='<h2 class="lp_select_listing_heading">'.esc_html__('SELECT A LISTING', 'listingpro-plugin').'</h2>';
		$output .='<div class="lp-checkout-wrapper">';
		
			if(!empty($plan_price)){
					$output .='<div class="lp-user-listings clearfix active-checkout-listing" data-plantype="'.$plan_type.'" data-recurringtext="'.esc_html__('Recurring Payment?', 'listingpro-plugin').'"><div class="col-md-12 col-sm-12 col-xs-12 lp-listing-clm lp-checkout-page-outer">';
					
					$output .= '<div class="col-md-1 col-sm-2 col-xs-6">';
					
					$output .='<div class="radio radio-danger lp_price_trigger_checkout">
									<input checked type="radio" name="listing_id" data-planid="'.$plan_id.'" data-taxenable = "'.$enableTax.'" data-taxrate = "'.$Taxrate.'" data-planprice = "'.$plan_price.'" data-title="'.get_the_title($plan_id).'" data-price="'.$price.'" id="'.$post_id.'" value="'.$post_id.'">
									<label for="'.$post_id.'">
									 
									</label>
								</div>';
					$output .='</div>';
					
					if ( has_post_thumbnail($post_id) ) { 
						
						$imgurl = get_the_post_thumbnail_url($post_id, 'listingpro-checkout-listing-thumb');
						$output .= '<input type="hidden" name="listing_img" value="'.$imgurl.'">';
						$output .='<div class="col-md-3">';
						$output .='<img class="img-responsive" src="'.$imgurl.'" alt="image" />';
						$output .='</div>';
						
					}elseif(!empty($deafaultFeatImg)){
						$output .= '<input type="hidden" name="listing_img" value="'.$deafaultFeatImg.'">';
						$output .='<div class="col-md-3">';
						$output .='<img class="img-responsive" src="'.$deafaultFeatImg.'" alt="image" />';
						$output .='</div>';
					}else {
						$output .='<div class="col-md-3">';
						$output .='<img class="img-responsive" src="'.esc_url('https://placehold.co/372x240').'" alt="image" />';
						$output .='</div>';
					} 
					$output .= '<h5>';
					$output .= get_the_title($post_id);
					$output .='</h5>';
					$output .= '<div class="col-md-2 col-sm-2 col-xs-6">';
					
					$output .= '<span class="lp-booking-dt">'.esc_html__('Date:','listingpro-plugin').'</span>
					<p>'.get_the_date('', $post_id).'</p>';
					
					$output .='</div>';
					$output .= '<div class="col-md-2 col-sm-2 col-xs-6">';
					
					$output .= '<span class="lp-persons">'.esc_html__('Category:','listingpro-plugin').'</span>
					<p>'.$catname.'</p>';
					
					$output .='</div>';
					$output .= '<div class="col-md-2 col-sm-2 col-xs-6">';
					
					$output .= '<span class="lp-duration">'.esc_html__('Duration:','listingpro-plugin').'</span>
					<p>'.$plan_duration.esc_html__(' Days','listingpro-plugin').'</p>';
					
					$output .='</div>';
					$output .= '<div class="col-md-2 col-sm-2 col-xs-6">';
					
					if(!empty($currency_position)){
						if($currency_position=="left"){
							$output .= '<span class="lp-booking-type">'.esc_html__('Price:','listingpro-plugin').'</span>
					<p>'.$currency_symbol.$plan_price.'</p>';
						}
						else{
							$output .= '<span class="lp-booking-type">'.esc_html__('Price:','listingpro-plugin').'</span>
					<p>'.$plan_price.$currency_symbol.'</p>';
						}
					}
					else{
						$output .= '<span class="lp-booking-type">'.esc_html__('Price:','listingpro-plugin').'</span>
					<p>'.$currency_symbol.$plan_price.'</p>';
					}
					
					
					
					
					$output .= '<input type="hidden" name="plan_price" value="'.$price.'">';
					$output .= '<input type="hidden" name="currency" value="'.$currency.'">';
					$output .= '<input type="hidden" name="post_title" value="'.get_the_title($post_id).'">';
					$output .= '<input type="hidden" name="listings_id" value="'.$post_id.'">';
					$output .= '<input type="hidden" name="plan_id" value="'.$plan_id.'">';
					
					$output .='</div>';
					
					$output .='</div>';
					$output .='</div>';
				}
			
	$output .='</div>';
	
	$output .= '<input type="hidden" name="listings_tax_price" value="">';
        $output .= '<input type="hidden" name="method" value="">';
        $output .= '<input type="hidden" name="func" value="start">';
		$output .= '<input type="hidden" name="plan_price" value="">';
		$output .= '<input type="hidden" name="post_title" value="">';
		$output .= '<input type="hidden" name="listings_id" value="">';
		$output .= '<input type="hidden" name="plan_id" value="">';
		$output .= '<input type="hidden" name="post_id" value="">';
		
	
}