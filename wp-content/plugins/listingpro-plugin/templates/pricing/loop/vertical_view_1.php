<?php
	global $wpdb,$listingpro_options;
	$plan_package_type = get_post_meta( get_the_ID(), 'plan_package_type', true );
	$post_price = get_post_meta(get_the_ID(), 'plan_price', true);
    $taxOn = $listingpro_options['lp_tax_swtich'];
    if($taxOn=="1"){
        $showtaxwithprice = $listingpro_options['lp_tax_with_plan_swtich'];
        if($showtaxwithprice=="1"){
            $withtaxprice = true;
        }
    }
	$perMonthPrce = lp_show_monthly_plan_price(get_the_ID());
	$plan_type = '';
	$plan_type_name = '';
	$plan_type = get_post_meta(get_the_ID(), 'plan_package_type', true);
	if( $plan_type=="Pay Per Listing" ){
		$plan_type_name = esc_html__("Per Listing",'listingpro-plugin');
	}
	else{
		$plan_type_name = esc_html__("Per Package",'listingpro-plugin');
	}

	$plan_time = '';
	$plan_time = get_post_meta(get_the_ID(), 'plan_time', true);
	$posts_allowed_in_plan = '';
	$PostAllowedInPlan = get_post_meta(get_the_ID(), 'plan_text', true);
	if(!empty($PostAllowedInPlan)){
		$posts_allowed_in_plan = get_post_meta(get_the_ID(), 'plan_text', true);
		$posts_allowed_in_plan = trim($posts_allowed_in_plan);
	}
	else{
		$posts_allowed_in_plan = 'unlimited';
	}
	
	$contact_show = get_post_meta( get_the_ID(), 'contact_show', true );
	if($contact_show == 'true'){
		$contact_checked = 'checked';
	}else{
		$contact_checked = 'unchecked';
	}
	
	$map_show = get_post_meta( get_the_ID(), 'map_show', true );
	if($map_show == 'true'){
		$map_checked = 'checked';
	}else{
		$map_checked = 'unchecked';
	}
	
	$video_show = get_post_meta( get_the_ID(), 'video_show', true );
	if($video_show == 'true'){
		$video_checked = 'checked';
	}else{
		$video_checked = 'unchecked';
	}
	
	$gallery_show = get_post_meta( get_the_ID(), 'gallery_show', true );
	if($gallery_show == 'true'){
		$gallery_checked = 'checked';
	}else{
		$gallery_checked = 'unchecked';
	}
	
	$listingproc_tagline = get_post_meta( get_the_ID(), 'listingproc_tagline', true );
	if($listingproc_tagline == 'true'){
		$tagline_checked = 'checked';
	}else{
		$tagline_checked = 'unchecked';
	}
	
	$listingproc_location = get_post_meta( get_the_ID(), 'listingproc_location', true );
	if($listingproc_location == 'true'){
		$location_checked = 'checked';
	}else{
		$location_checked = 'unchecked';
	}
	
	$listingproc_website = get_post_meta( get_the_ID(), 'listingproc_website', true );
	if($listingproc_website == 'true'){
		$website_checked = 'checked';
	}else{
		$website_checked = 'unchecked';
	}
	
	$listingproc_social = get_post_meta( get_the_ID(), 'listingproc_social', true );
	if($listingproc_social == 'true'){
		$social_checked = 'checked';
	}else{
		$social_checked = 'unchecked';
	}
	
	$listingproc_faq = get_post_meta( get_the_ID(), 'listingproc_faq', true );
	if($listingproc_faq == 'true'){
		$faq_checked = 'checked';
	}else{
		$faq_checked = 'unchecked';
	}
	
	$listingproc_price = get_post_meta( get_the_ID(), 'listingproc_price', true );
	if($listingproc_price == 'true'){
		$price_checked = 'checked';
	}else{
		$price_checked = 'unchecked';
	}
	
	$listingproc_tag_key = get_post_meta( get_the_ID(), 'listingproc_tag_key', true );
	if($listingproc_tag_key == 'true'){
		$tag_key_checked = 'checked';
	}else{
		$tag_key_checked = 'unchecked';
	}
	
	$listingproc_bhours = get_post_meta( get_the_ID(), 'listingproc_bhours', true );
	if($listingproc_bhours == 'true'){
		$bhours_checked = 'checked';
	}else{
		$bhours_checked = 'unchecked';
	}
	
	/* new options  */
	$resurva_show = get_post_meta( get_the_ID(), 'listingproc_plan_reservera', true );
	if($resurva_show == "true"){
		$resurva_show = 'checked';
	}else{
		$resurva_show = 'unchecked';
	}
	
	$timekit_show = get_post_meta( get_the_ID(), 'listingproc_plan_timekit', true );
	if($timekit_show == "true"){
		$timekit_show = 'checked';
	}else{
		$timekit_show = 'unchecked';
	}
	
	$menu_show = get_post_meta( get_the_ID(), 'listingproc_plan_menu', true );
	if($menu_show == "true"){
		$menu_show = 'checked';
	}else{
		$menu_show = 'unchecked';
	}
	
	$announcment_show = get_post_meta( get_the_ID(), 'listingproc_plan_announcment', true );
	if($announcment_show == "true"){
		$announcment_show = 'checked';
	}else{
		$announcment_show = 'unchecked';
	}
	
	$deals_show = get_post_meta( get_the_ID(), 'listingproc_plan_deals', true );
	if($deals_show == "true"){
		$deals_show = 'checked';
	}else{
		$deals_show = 'unchecked';
	}
	
	$competitor_show = get_post_meta( get_the_ID(), 'listingproc_plan_campaigns', true );
	if($competitor_show == "true"){
		$competitor_show = 'checked';
	}else{
		$competitor_show = 'unchecked';
	}
	
	
	$featured_show = get_post_meta( get_the_ID(), 'lp_featured_imageplan', true );
	if($featured_show == "true"){
		$featured_show = 'checked';
	}else{
		$featured_show = 'unchecked';
	}
	
	
	$event_show = get_post_meta( get_the_ID(), 'lp_eventsplan', true );
	if($event_show == "true"){
		$event_show = 'checked';
	}else{
		$event_show = 'unchecked';
	}
	/* new options ends  */
    $bookings_show = get_post_meta(get_the_ID(), 'listingproc_bookings', true);
    if ($bookings_show == "true") {
        $bookings_show = 'checked';
    } else {
        $bookings_show = 'unchecked';
    }

    $leadform_show = get_post_meta(get_the_ID(), 'listingproc_leadform', true);
    if ($leadform_show == "true") {
        $leadform_show = 'checked';
    } else {
        $leadform_show = 'unchecked';
    }
	$plan_hot = '';
	$plan_hot = get_post_meta( get_the_ID(), 'plan_hot', true );
	
	$hotClass = '';
	if(!empty($plan_hot) && $plan_hot=="true") {
		$hotClass = 'featured-plan';
	}else {
		$hotClass = '';
	}
	
	/* here you go for dbs*/
	$results = null;
	$currentPlanID = get_the_ID();
	$plan_text = '';
	$used = '';
	$plan_limit_left = '';
    $dbprefix = '';
	$dbprefix = $wpdb->prefix;
	$user_ID = '';
	$user_ID = get_current_user_id();
	$results = null;
	$table_name = $dbprefix.'listing_orders';
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
		$results = $wpdb->get_results( "SELECT * FROM ".$dbprefix."listing_orders WHERE user_id ='$user_ID' AND plan_id='$currentPlanID' AND status = 'success' AND plan_type='$plan_type'" );
	}
	
	if( !empty($results) && count($results)>0 ){
		$used = '';
		$used = $results[0]->used;
		
		if(is_numeric($posts_allowed_in_plan)){
			$plan_limit_left = (int)$posts_allowed_in_plan - (int)$used;
		}
		else{
			$plan_limit_left = 'unlimited';
		}
		
	}
	else{
		$plan_limit_left = $PostAllowedInPlan;
	}

	if( !empty ( $plan_package_type ) ){
		if( $plan_package_type=="Pay Per Listing" ){
			$plan_text = '';
		}
		else if( $plan_package_type=="Package" ){
			$plan_text = get_post_meta(get_the_ID(), 'plan_text', true);
			if( !empty($plan_text) && isset($plan_text) ){
				$plan_text = esc_html__('Max. listings allowed : ', 'listingpro-plugin').$plan_text;
			}
		}
	}
	// start centerlized plan
        $classoffset = '';
        if(isset($GLOBALS['plans_count'])){
            if($GLOBALS['plans_count'] == '1'){
                $classoffset = 'col-md-offset-4';
            }
            if($GLOBALS['plans_count'] == '2'){
                $classoffset = 'col-md-offset-2';
            }
        }

    //End centerlized plan								

?>
	<div class="col-md-4 price-view-default <?php echo get_the_ID(). ' '.$hotClass.' '.$classoffset ?>">
				<div class="lp-price-main lp-border-radius-8 lp-border text-center">
					<?php 
						/* ================ */
						$user_ID = get_current_user_id();
						if( !empty($plan_type) && $plan_type=="Package" ){
							if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
								$results = $wpdb->get_results( "SELECT * FROM ".$dbprefix."listing_orders WHERE user_id ='$user_ID' AND plan_id='$currentPlanID' AND status = 'success' AND plan_type='$plan_type'" );
							}
							
							if(is_numeric($plan_limit_left)){
								if( !empty($results) && count($results)>0 ){
									if(!empty($post_price) && $plan_limit_left>0){
										echo '<div class="lp-sales-option">
														<div class="sales-offer">
															'.esc_html__("Active",'listingpro-plugin').'
														</div>
													</div>';
									}
								}
							}
							else if(!empty($post_price) && $plan_limit_left=="unlimited"){
								if( !empty($plan_type) && $plan_type=="Package" ){
									if( !empty($results) && count($results)>0 ){
										echo '<div class="lp-sales-option">
														<div class="sales-offer">
															'.esc_html__("Active",'listingpro-plugin').'
														</div>
													</div>';
									}
								}
							}
						}
						$plan_title_color = '';
						$plan_title_img =   '';
						$plan_title_bg  =   '';

						$plan_title_img = listing_get_metabox_by_ID('lp_price_plan_bg', get_the_ID()); 

						$plan_title_color = get_post_meta(get_the_ID(), 'plan_title_color', true);
						$classForBg = 'lp-title';
						if( isset($plan_title_img) && $plan_title_img != '' )
						{
							$plan_title_bg  =   "background: url($plan_title_img); background-size:cover;";
							$classForBg .= ' lp-overlay-pricing';
						}
						else
						{
							$plan_title_bg  =   "background-color: $plan_title_color;";
						}
						/* ================ */
						$title = ''; if (!empty(get_the_title())) : $title = '<a>'.get_the_title().'</a>'; endif;
					?>
					
					<div class="<?php echo $classForBg; ?>" style="<?php echo $plan_title_bg; ?>">
										<div class="lp-plane-top-wrape">
											<?php echo $title; ?>
											<?php
											if(!empty($post_price)){
												
												$pricewithCurr = null;
												$post_price = round($post_price,2);
												$post_price = (float)$post_price;
												if($withtaxprice=="1"){
															$taxrate = $listingpro_options['lp_tax_amount'];
															$taxprice = (float)(($taxrate/100)*$post_price);
															$post_price = (float)$post_price + (float)$taxprice;
															$post_price = number_format($post_price,2);
														}
												
														$lp_currency_position = $listingpro_options['pricingplan_currency_position'];
														if(isset($lp_currency_position) && $lp_currency_position=="left"){
															$pricewithCurr = listingpro_currency_sign().$post_price;
														}
														else{ 
															$pricewithCurr = $post_price.listingpro_currency_sign();
														}
												
												
												if(!empty($perMonthPrce)){
												?>
														<p><?php echo $perMonthPrce; ?><br>
														<span><?php echo esc_html__("Per Month", 'listingpro-plugin'); ?></span>
														</p>
													<?php
												}else{ 
												?>
															<p><?php echo $pricewithCurr; ?></p>
														<?php
												}
											}
											
											else{ ?>
												<p><?php echo esc_html__("Free", 'listingpro-plugin'); ?></p>
											<?php
											}
											
											if(!empty($perMonthPrce)){
													?>
													<span class="package-type"><?php echo $pricewithCurr.' '.esc_html__('billed Annually', 'listingpro-plugin'); ?></span><br>
													<?php
												}
											
											if(!empty($plan_type_name)){ 
												
												?>
													<span class="package-type"><?php echo $plan_type_name; ?></span><br><br>
												<?php
												
											}
											
											if(is_numeric($plan_limit_left)){
												if( !empty($results) && count($results)>0 ){
													if(!empty($post_price) && $plan_limit_left>0){ ?>
														<span style="font-size:12px;color:#fff" class="allowedListing"><?php echo esc_html__('Remaining Listings : ', 'listingpro-plugin') . $plan_limit_left; ?></span>
													
													<?php
													}
												}
											}
											
											?>
										</div>
					</div>
					
					<!--Bottom plan section -->
					
					<div class="lp-price-list" style="display:block;">
							<?php
								/* =============== */
									if(!empty($plan_hot) && $plan_hot=="true"){ ?>
										<div class="lp-hot"><?php echo esc_html__('Hot','listingpro-plugin'); ?></div>
									<?php
									}
								/* =============== */
							?>
							
									
									<ul class="lp-listprc">
											<li>
												<span class="icon-text">
													<img class="icon icons8-Cancel" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAFiUlEQVR4nO1aW2xVRRSdUl8YxQcqVoild8+htRijYjB+GEw0iAaLmpy79+kFia+qmPo2JHxQiT++PiS+vkw0fmmiHwoGEyM+PjSVGCTKw6IfAhrfiSZoAVlmn87cHri97T33nN7bGFbS5PTsM3v2zOyZvfaea8wx/E9xfi/OoAg3WsHTlrGRBDst43cSDBPjgD5bxjdW8K5lPKPfdq3ATDMVYJfgRFtEiQTvWcEhK0CaPxIctIxNVrC8O8QJDR/AnBDTifEQMfYljPrHCjZbxlqd7YBxga7Sgj4cr3/6bEN0q4wE64jxYbxSvj1jn+ps2IAKjOtIsDthwBfE6GtfidPT6lLXsoK7LeOrxITsJsGSybHeGNO+EieR4MVEh1t1UMagJbt2tJCghxg7nP7DxHhJ+zR5okMwyzK2xJ0w/raCBxctwnG5dmKMURckxho9INxkfdoe4lwvr2fVy+hkzLWCITdTQ0EvLjaTDOrFAmLssYLvu0poi99FuMMKfgwiLKxvJUYHMWhDnG0ahK4S2kgwX58LjKvLB0MRt6ZSpP5ZdifBYGcPTjVNwLwIXXEMGnG1J1IrSGzsoUauRBLzIpzlPYIEb5oBTDNpoKeR39iN2BPVgi0JPnJ2bGlbipP1fRBidu3BzsUJDVCmSbCCV50NezoF58XvGLeT4GsTonVCBWq8jxOTccTWAmKscTb8ZQWX+Pc+cJLgzomX09GOkWDXeFhGqAHRMv7VIJmUBYLI0xmNOdWVFFHytCOfiJ0OQYSFVrDfzfrDFR8MYFpiVY4Y5BFwLFYH0mcaDN3EJNjr+n95Ite3jLfG/CBmp4JDymIzUYE6oDHKMr50x/3747mNBsrYTsaBQojTKj5wSZEq+sA0EiFaibHBrcSOWiaRBB+7VVlcIdSszQnXTpbNVYx61g3iF8ugGts85SZ9oFI4OivLTINgGff4pCxgXJnyZNNJ3ziWUPNpaGaXt8FVjFkcp7qaexRxS5q2HYKLfKyrEJLgNxV2hzizJm0hWi3jdSu4zaSETpZl/OE84PHU7UPM9lG/QuiTmVpz5oBxczmbE/TXakRhOc4hwXeu7Rv1xKs5Iaa79vszD8S1uVcjcK0UW3Vr0eFoIlhX9UZG9lZ21/JKBcudr6txz1en2mghxmvOgL01s9gxoGmF0/NzpVCwq97NXhDc5MpBOphXxiKbelS6zv/UzWoygATzfdypEOpRluX4JcZVaqTT8XayAjIeEawHgduf2k+FMC51ZgyIgeBy76JKNbpDnEIRrnCVFwSC+0wOIME6N5AnK4WMZc6AzVk6KRRxITF+KBcsGD+5Tl/IorcKRVlqjoZyHE8alUCaDAh6USDGt+U6L2NTXkla1wrMVMIYV1VKmDHmR66gjCDCXVk71MKaMloSbM+TTdsIq9wKb6j+EaM3z8RKZ29uL9pNboCWVrfGGWwRMlHlIk5uChGuN1MMJOjx1GTCwG0FDzi/3tas4sNY0GQrkeb211Zh9EUxxiNmisAyVrtB7FTPqakRFXGt5zJUxKWmySgILnPM4XBQxDWpGitv8hcvylhNk9AhmOXZMjHW18swB91++bwZRWxbwgxfSCfGZ3Vfy2kROfZJR7sbuTIdicsl9Qr9P5PC+KLHpcHx/V4D9kxhZE/45GtXbrFIV8K7mSsUPDpuyTLbEbvapwTqTrlfabg981yCP20bIW35XYba0dtdvQxdP6lX1Vr98K7mKxla1kmbWZavpyOs8rTDujiR+ojNtDqM+92FpTdg2NHrAU18lM6roTqr+qfP+k5lxHjMMj5J/mBALz5J0F9zsMt7QHGpf+S3Jwfr+QmHslglgE35CUfVMz/CDVrOJME7mk8T41e3UsPuebump5rZ6f5q1gXrMZgG4D9lCrBDc6dxfgAAAABJRU5ErkJggg==" alt="icon-cross">
												</span>
												
												<span>
													<?php if($plan_time){ ?>
														<?php echo esc_html__('Duration', 'listingpro-plugin').' : '.$plan_time.' '.esc_html__('days', 'listingpro-plugin'); ?>
													<?php } else { ?>
														<?php echo esc_html__('Duration', 'listingpro-plugin'); ?>
														<?php echo esc_html__(' : Unlimited days', 'listingpro-plugin'); ?>
													<?php } ?>
                                                                                                </span>
											</li>
											<?php echo apply_filters('lp_price_plan_recurring_duration', '', get_the_ID(), 'vertical_view_1' ); ?>
											<?php
												if(!empty($posts_allowed_in_plan) && $plan_type=="Package"){ ?>
												<li>
													<span class="icon-text"><?php echo listingpro_icon8('checked'); ?></span>
													<span><?php echo esc_html__('Max. Listings : ', 'listingpro-plugin'). $posts_allowed_in_plan; ?>
													</span>
												</li>
											<?php
												}
											?>
											
											<?php
												
												if($listingpro_options['lp_showhide_address']=="1"){
													if(get_post_meta(get_the_ID(), 'map_show_hide', true)==''){
														echo '
														<li>
															<span class="icon icons8-Cancel">'.listingpro_icon8($map_checked).'</span>
															<span>'.esc_html__('Map Display', 'listingpro-plugin').'</span>
														</li>';
													}
												}
												if($listingpro_options['phone_switch']=="1"){
													if(get_post_meta(get_the_ID(), 'contact_show_hide', true)==''){
														echo '
																<li>
																	<span class="icon icons8-Cancel">'.listingpro_icon8($contact_checked).'</span>
																	<span>'.esc_html__('Contact Display', 'listingpro-plugin').'</span>
																</li>
																';
													}
												}
												if($listingpro_options['file_switch']=="1"){
													if(get_post_meta(get_the_ID(), 'gall_show_hide', true)==''){
														echo '
															<li>
																<span class="icon icons8-Cancel">'.listingpro_icon8($gallery_checked).'</span>
																<span>'.esc_html__('Image Gallery', 'listingpro-plugin').'</span>
															</li>
															';
													}
												}
												if($listingpro_options['vdo_switch']=="1"){
													if(get_post_meta(get_the_ID(), 'video_show_hide', true)==''){
														echo '
															<li>
																<span class="icon icons8-Cancel">'.listingpro_icon8($video_checked).'</span>
																<span>'.esc_html__('Video', 'listingpro-plugin').'</span>
															</li>
															';
													}
												}
												if(get_post_meta(get_the_ID(), 'tagline_show_hide', true)==''){
													echo '
													<li>
														<span class="icon-text">'.listingpro_icon8($tagline_checked).'</span>
														<span>'.esc_html__('Business Tagline', 'listingpro-plugin').'</span>
													</li>
													';
												}
												if($listingpro_options['location_switch']=="1"){
													if(get_post_meta(get_the_ID(), 'location_show_hide', true)==''){
														echo '
															<li>
																<span class="icon-text">'.listingpro_icon8($location_checked).'</span>
																<span>'.esc_html__('Location', 'listingpro-plugin').'</span>
															</li>';
													}
												}
												if($listingpro_options['web_switch']=="1"){
													if(get_post_meta(get_the_ID(), 'website_show_hide', true)==''){
														echo '
														<li>
															<span class="icon-text">'.listingpro_icon8($website_checked).'</span>
															<span>'.esc_html__('Website', 'listingpro-plugin').'</span>
														</li>';
													}
													
												}
												
												if($listingpro_options['listin_social_switch']=="1"){
													if(get_post_meta(get_the_ID(), 'social_show_hide', true)==''){
														echo '
														<li>
															<span class="icon-text">'.listingpro_icon8($social_checked).'</span>
															<span>'.esc_html__('Social Links', 'listingpro-plugin').'</span>
														</li>
														';
													}
												}
												if($listingpro_options['faq_switch']=="1"){
													if(get_post_meta(get_the_ID(), 'faqs_show_hide', true)==''){
														echo '
															<li>
																<span class="icon-text">'.listingpro_icon8($faq_checked).'</span>
																<span>'.esc_html__('FAQ', 'listingpro-plugin').'</span>
															</li>
															';
													}
												}
												if($listingpro_options['currency_switch']=="1"){
													if(get_post_meta(get_the_ID(), 'price_show_hide', true)==''){
														echo '
															<li>
																<span class="icon-text">'.listingpro_icon8($price_checked).'</span>
																<span>'.esc_html__('Price Range', 'listingpro-plugin').'</span>
															</li>
															';
													}
												}
												
												if($listingpro_options['tags_switch']=="1"){
													if(get_post_meta(get_the_ID(), 'tags_show_hide', true)==''){
														echo '
															<li>
																<span class="icon-text">'.listingpro_icon8($tag_key_checked).'</span>
																<span>'.esc_html__('Tags/Keywords', 'listingpro-plugin').'</span>
															</li>
															';
													}
												}
												if($listingpro_options['oph_switch']=="1"){
													if(get_post_meta(get_the_ID(), 'bhours_show_hide', true)==''){
														echo '		
														<li>
															<span class="icon-text">'.listingpro_icon8($bhours_checked).'</span>
															<span>'.esc_html__('Business Hours', 'listingpro-plugin').'</span>
														</li>
														';
													}
												}
												
												
												/* new option */
											if(lp_theme_option('lp_featured_file_switch')){
												if(get_post_meta(get_the_ID(), 'reserva_show_hide', true)==''){
													echo '
														<li>
															<span class="icon-text">'.listingpro_icon8($resurva_show).'</span>
															<span>'.esc_html__('Resurva', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											if(get_post_meta(get_the_ID(), 'timekit_show_hide', true)==''){
												echo '
													<li>
														<span class="icon-text">'.listingpro_icon8($timekit_show).'</span>
														<span>'.esc_html__('Timekit', 'listingpro-plugin').'</span>
													</li>
													';
											}
											if(get_post_meta(get_the_ID(), 'menu_show_hide', true)==''){
												
												echo '
												<li>
													<span class="icon-text">'.listingpro_icon8($menu_show).'</span>
													<span>'.esc_html__('Menu', 'listingpro-plugin').'</span>
												</li>
												';
											}
											if(get_post_meta(get_the_ID(), 'announcment_show_hide', true)==''){
												echo '
												<li>
													<span class="icon-text">'.listingpro_icon8($announcment_show).'</span>
													<span>'.esc_html__('Announcement', 'listingpro-plugin').'</span>
												</li>
												';
											}
											if(get_post_meta(get_the_ID(), 'deals_show_hide', true)==''){
												echo '
												<li>
													<span class="icon-text">'.listingpro_icon8($deals_show).'</span>
													<span>'.esc_html__('Deals-Offers-Discounts', 'listingpro-plugin').'</span>
												</li>
												';
											}
											if(get_post_meta(get_the_ID(), 'metacampaign_show_hide', true)==''){
												echo '
												<li>
													<span class="icon-text">'.listingpro_icon8($competitor_show).'</span>
													<span>'.esc_html__('Hide competitors Ads', 'listingpro-plugin').'</span>
												</li>
												';
											}
											if(get_post_meta(get_the_ID(), 'events_show_hide', true)==''){
												
												echo '
												<li>
													<span class="icon-text">'.listingpro_icon8($event_show).'</span>
													<span>'.esc_html__('Events', 'listingpro-plugin').'</span>
												</li>
												';
											}
                                            if (get_post_meta(get_the_ID(), 'bookings_show_hide', true) == '') {
                                                echo '
                                                <li>
                                                    <span class="icon-text">' . listingpro_icon8($bookings_show) . '</span>
                                                    <span>' . esc_html__('Bookings', 'listingpro-plugin') . '</span>
                                                </li> 
                                                ';
                                            }
                                            if (get_post_meta(get_the_ID(), 'leadform_show_hide', true) == '') {
                                                echo '
                                                <li>
                                                    <span class="icon-text">' . listingpro_icon8($leadform_show) . '</span>
                                                    <span>' . esc_html__('Lead Form', 'listingpro-plugin') . '</span>
                                                </li>  
                                                ';
                                            }

                                            $google_ads_show = get_post_meta(get_the_ID(), 'lp_hidegooglead', true);
                                            if ($google_ads_show == "true") {
                                                $google_ads_show = 'checked';
                                            } else {
                                                $google_ads_show = 'unchecked';
                                            }
                                            if (get_post_meta(get_the_ID(), 'googlead_show_hide', true) == '') {
                                                echo '<li>
                                                    <span class="icon-text">' . listingpro_icon8($google_ads_show) . '</span>
                                                    <span>' . esc_html__('Hide Google Ads', 'listingpro-plugin') . '</span>
                                                </li>';
                                            }
											/* new option emd */
												
												
												
												$lp_plan_more_fields = listing_get_metabox_by_ID('lp_price_plan_addmore',get_the_ID());
												if(!empty($lp_plan_more_fields)){
													foreach($lp_plan_more_fields as $morefield){
														if(!empty($morefield)){
															echo '<li>
																<span class="icon-text">'.listingpro_icon8('checked').'</span>
																<span>'.$morefield.'</span>
															</li>';
														}
													}
												}
											?>
											
									</ul>
									
									<form method="post" name="<?php echo get_the_ID(); ?>" action="<?php echo listingpro_url('submit-listing'); ?>" class="price-plan-button">
									<!-- for button -->
									<?php
										echo '<input type="hidden" name="plan_id" value="'.get_the_ID().'" />';
											
											if(empty($post_price) && $plan_type=="Package"){
												echo '<p>A <strong>'.esc_html__("Package",'listingpro-plugin').'</strong>'.esc_html__(" should have a price ",'listingpro-plugin').'</p>';

											}
											else if( !empty($plan_type) && $plan_type=="Package" ){
												if(!empty($plan_limit_left)){
											
													echo '<input id="submit'.get_the_ID().'" class="lp-price-free lp-without-prc btn" type="submit" value="'.esc_html__('Continue', 'listingpro-plugin').'" name="submit">';
												}
												else{
													echo '<input id="submit'.get_the_ID().'" class="lp-price-free lp-without-prc btn" type="submit" value="'.esc_html__('Continue', 'listingpro-plugin').'" name="submit">';
												}
											}
											else{
											
													echo '<input id="submit'.get_the_ID().'" class="lp-price-free lp-without-prc btn" type="submit" value="'.esc_html__('Continue', 'listingpro-plugin').'" name="submit">';
												
												
											}
											echo  wp_nonce_field( 'price_nonce', 'price_nonce_field'.get_the_ID() ,true, false );
											
											if(isset($_POST['lp_cat_plan_submit'])){
												$lp_s_cat= $_POST['lp-slected-plan-cat'];
												echo '<input type="hidden" value="'.$lp_s_cat.'" name="lp_pre_selected_cats" />';
											}
											
											
									?>
									<!-- for button -->
									</form>	
										
									
					</div>
									
					
				</div>
	</div>