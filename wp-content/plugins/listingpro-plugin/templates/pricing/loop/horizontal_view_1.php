<?php
	global $listingpro_options;
	$listing_access_only_users = $listingpro_options['lp_allow_vistor_submit'];
	$perMonthPrce = lp_show_monthly_plan_price(get_the_ID());
	$showAddListing = true;
	if( isset($listing_access_only_users)&& $listing_access_only_users==1 ){
		$showAddListing = false;
		if(is_user_logged_in()){
			$showAddListing = true;
		}
	}
	if( $showAddListing==false ){
		wp_redirect(home_url());
		exit;
	}
	global $wpdb;
	
	$lp_social_show;
	$lp_social_show = $listingpro_options['listin_social_switch'];
	$dbprefix = '';
	$dbprefix = $wpdb->prefix;
	$user_ID = '';
	$user_ID = get_current_user_id();
	$outputHorz = null;
	$results = null;
	$table_name = $dbprefix.'listing_orders';
	$limitLefts = '';
	$taxOn = $listingpro_options['lp_tax_swtich'];
	$withtaxprice = false;
	if($taxOn=="1"){
		$showtaxwithprice = $listingpro_options['lp_tax_with_plan_swtich'];
		if($showtaxwithprice=="1"){
			$withtaxprice = true;
		}
	}
	
	/* horizontal view */
	
							global $post;
							$plan_package_type = get_post_meta( get_the_ID(), 'plan_package_type', true );
							$post_price = get_post_meta(get_the_ID(), 'plan_price', true);
							$plan_time = '';
							$plan_time = get_post_meta(get_the_ID(), 'plan_time', true);
							$posts_allowed_in_plan = '';
							$posts_allowed_in_plan = get_post_meta(get_the_ID(), 'plan_text', true);
							$plan_type = $plan_package_type;
							if(!empty($plan_package_type) && $plan_package_type=="Package"){
								if(is_numeric($posts_allowed_in_plan)){
									$posts_allowed_in_plan = $posts_allowed_in_plan;
								}
								else{
									$posts_allowed_in_plan = esc_html__('unlimited', 'listingpro-plugin');
								}
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
                            $bookings_show = get_post_meta( get_the_ID(), 'listingproc_bookings', true );
                            if($bookings_show == "true"){
                                $bookings_show = 'checked';
                            }else{
                                $bookings_show = 'unchecked';
                            }
                            $leadform_show = get_post_meta( get_the_ID(), 'listingproc_leadform', true );
                            if($leadform_show == "true"){
                                $leadform_show = 'checked';
                            }else{
                                $leadform_show = 'unchecked';
                            }
							/* new options ends  */
							
							
							
							$plan_type_name = '';
							if( $plan_package_type=="Pay Per Listing" ){
								$plan_type_name = esc_html__("Per Listing",'listingpro-plugin');
							}
							else{
								$plan_type_name = esc_html__("Per Package",'listingpro-plugin');
							}
							
				
							$plan_text = '';
							$used = '';
							$plan_limit_left = '';
							$limitLefts = null;
							$currentPlanID = get_the_ID();
							
							if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
								$results = $wpdb->get_results( "SELECT * FROM ".$dbprefix."listing_orders WHERE user_id ='$user_ID' AND plan_id ='$currentPlanID' AND status = 'success' AND plan_type='$plan_package_type'"  );
							}
							
							$used = '';
							if(!empty($plan_package_type) && $plan_package_type=="Package"){
									$plan_text = esc_html__('Per Package ', 'listingpro-plugin');
									$plan_limit_left = $posts_allowed_in_plan;
								}
								else if(!empty($plan_package_type) && $plan_package_type=="Pay Per Listing"){
									$plan_text = esc_html__('Per Listing ', 'listingpro-plugin');
								}
							if( !empty($results) && count($results)>0 ){
								if(!empty($plan_package_type) && $plan_package_type=="Package"){
									
									/* package details */
									/* foreach ( $results as $info ) {
										$used = $info->used;
									} */
									
									$used = $results[0]->used;
									
									if(is_numeric($posts_allowed_in_plan)){
										$plan_limit_left = (int)$posts_allowed_in_plan - (int)$used;
									}
									else{
										$plan_limit_left = $posts_allowed_in_plan;
									}
									
									$plan_text = esc_html__('Per Package ', 'listingpro-plugin');
								}
								else if(!empty($plan_package_type) && $plan_package_type=="Pay Per Listing"){
									$plan_text = esc_html__('Per Listing ', 'listingpro-plugin');
								}
								
								
								
							}
							
							$plan_title_color = '';
                            $plan_title_img =   '';
                            $plan_title_bg  =   '';

                            $plan_title_img = listing_get_metabox_by_ID('lp_price_plan_bg', get_the_ID()); 
							
                            $plan_title_color = get_post_meta(get_the_ID(), 'plan_title_color', true);
							$classForBg = 'price-plan-box-upper';
                            if( isset($plan_title_img) && $plan_title_img != '' )
                            {
                                $plan_title_bg  =   "background: url($plan_title_img); background-size:cover;";
								$classForBg .= ' lp-overlay-pricing';
                            }
                            else
                            {
                                $plan_title_bg  =   "background-color: $plan_title_color;";
                            }
							$title = ''; if (!empty(get_the_title())) : $title = '<span class="pull-left">'.get_the_title().'</span>'; endif;
							$outputHorz .='
							<div class="price-plan-box lp-border-radius-8 '.get_the_ID().'">
								<div class="'.$classForBg.'" style="'.$plan_title_bg.'">
									<div class="lp-plane-top-wrape">
										<h1 class="clearfix">
											'.$title;
											if(!empty($post_price)){
												if($withtaxprice=="1"){
													$taxrate = $listingpro_options['lp_tax_amount'];
													$taxprice = (float)(($taxrate/100)*$post_price);
													$post_price = (float)$post_price + (float)$taxprice;
												}
												$post_price = round($post_price,2);
												$lp_currency_position = $listingpro_options['pricingplan_currency_position'];
												if(isset($lp_currency_position) && $lp_currency_position=="left"){
													$outputHorz .='<span class="pull-right">'.listingpro_currency_sign().$post_price.'</span>';
												}
												else{
													$outputHorz .='<span class="pull-right">'.$post_price.listingpro_currency_sign().'</span>';
												}
											}
											else{
												$outputHorz .='<span class="pull-right">'.esc_html__("Free", "listingpro-plugin").'</span>';
											}
											
										if(!empty($perMonthPrce)){
											$outputHorz .= '
												<h1 class="clearfix">
													<span class="pull-left">'.esc_html__("Per Month Price", "listingpro-plugin").'</span>
													
													<span class="pull-right">'.$perMonthPrce.'</span>
												</h1>
											';
										}
											
										$listingCalculations = null;	
										$outputHorz .=
										'</h1>
										<p class="clearfix">
											<span class="pull-left">'.$plan_text.'</span>
											<span class="pull-right">';
												if( !empty($plan_time) ){
													$outputHorz .= esc_html__('Duration', 'listingpro-plugin').' : '.$plan_time.' '.esc_html__('days', 'listingpro-plugin');
												}
												else{
													$outputHorz .= esc_html__('Duration', 'listingpro-plugin');
													$outputHorz .= esc_html__(' : Unlimited days', 'listingpro-plugin');
												}
                                                                                                
                                                                                                $recurring_output = apply_filters('lp_price_plan_recurring_duration', '', get_the_ID(), 'horizontal_view_1' );
                                                                                                $outputHorz .= $recurring_output;
												
												if( $plan_package_type=="Package" ){
													$plan_text = get_post_meta(get_the_ID(), 'plan_text', true);
													if( !empty($plan_text) && isset($plan_text) ){
														$plan_text = esc_html__('Total Listings : ', 'listingpro-plugin').$plan_text;
														$listingCalculations = $plan_text;
													}
													else{
														$plan_text = esc_html__('unlimited', 'listingpro-plugin');
														$plan_text = esc_html__('Total Listings : ', 'listingpro-plugin').$plan_text;
														$listingCalculations = $plan_text;
													}
											
													if( !empty($plan_limit_left) && is_numeric($plan_limit_left) ){
														$limitLefts .= esc_html__('Available Listings : ', 'listingpro-plugin'). $plan_limit_left;
														$listingCalculations .= ' '.$limitLefts;
													}
													
													
													if(!empty($used) && isset($used)){
														$used =  $used;
														$used = esc_html__(' Used :', 'listingpro-plugin').$used;
													}
													
													$listingCalculations .= $used;
													
													$outputHorz .='<p class="clearfix">'.$listingCalculations.'</p>';
												}
												
												$outputHorz .='
											</span>
										</p>
									</div>
								</div>
								<div class="price-plan-box-bottom lp-border clearfix">
									<div class="price-plan-content  pull-left">
										<ul class="list-style-none">
										
											';
											if($listingpro_options['lp_showhide_address']=="1"){
												if(get_post_meta(get_the_ID(), 'map_show_hide', true)==''){
													$outputHorz .='
													
													<li>
														<span class="icon-text">'.listingpro_icon8($map_checked).'</span>
														<span>'.esc_html__('Map Display', 'listingpro-plugin').'</span>
													</li>
													';
												}
											}
											if($listingpro_options['phone_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'contact_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($contact_checked).'</span>
														<span>'.esc_html__('Contact Display', 'listingpro-plugin').'</span>
													</li>
													';
												}
											}
											if($listingpro_options['file_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'gall_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($gallery_checked).'</span>
														<span>'.esc_html__('Image Gallery', 'listingpro-plugin').'</span>
													</li>
													';
												}
											}
											if($listingpro_options['vdo_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'video_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($video_checked).'</span>
														<span>'.esc_html__('Video', 'listingpro-plugin').'</span>
													</li>
													';
												}
											}
											if(get_post_meta(get_the_ID(), 'tagline_show_hide', true)==''){
												$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($tagline_checked).'</span>
														<span>'.esc_html__('Business Tagline', 'listingpro-plugin').'</span>
													</li>';
											}
											
											if($listingpro_options['location_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'location_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($location_checked).'</span>
														<span>'.esc_html__('Location', 'listingpro-plugin').'</span>
													</li>';
												}
											}
											if($listingpro_options['web_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'website_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($website_checked).'</span>
														<span>'.esc_html__('Website', 'listingpro-plugin').'</span>
													</li>';
												}
											}
											if($listingpro_options['listin_social_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'social_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($social_checked).'</span>
														<span>'.esc_html__('Social Links', 'listingpro-plugin').'</span>
													</li>
													';
												}
											}
											if($listingpro_options['faq_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'faqs_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($faq_checked).'</span>
														<span>'.esc_html__('FAQ', 'listingpro-plugin').'</span>
													</li>
													';
												}
											}
											if($listingpro_options['currency_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'price_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($price_checked).'</span>
														<span>'.esc_html__('Price Range', 'listingpro-plugin').'</span>
													</li>
													';
												}
											}
											
											if($listingpro_options['tags_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'tags_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($tag_key_checked).'</span>
														<span>'.esc_html__('Tags/Keywords', 'listingpro-plugin').'</span>
													</li>
													';
												}
											}
											if($listingpro_options['oph_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'bhours_show_hide', true)==''){
														$outputHorz .='
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
													$outputHorz .='
														<li>
															<span class="icon-text">'.listingpro_icon8($resurva_show).'</span>
															<span>'.esc_html__('Resurva', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											if(get_post_meta(get_the_ID(), 'timekit_show_hide', true)==''){
												$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($timekit_show).'</span>
														<span>'.esc_html__('Timekit', 'listingpro-plugin').'</span>
													</li>
													';
											}
												if(get_post_meta(get_the_ID(), 'menu_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($menu_show).'</span>
														<span>'.esc_html__('Menu', 'listingpro-plugin').'</span>
													</li>
													';
												}
												if(get_post_meta(get_the_ID(), 'announcment_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($announcment_show).'</span>
														<span>'.esc_html__('Announcment', 'listingpro-plugin').'</span>
													</li>
													';
												}
												if(get_post_meta(get_the_ID(), 'deals_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($deals_show).'</span>
														<span>'.esc_html__('Deals-Offers-Discounts', 'listingpro-plugin').'</span>
													</li>
													';
												}
												if(get_post_meta(get_the_ID(), 'metacampaign_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($competitor_show).'</span>
														<span>'.esc_html__('Hide competitors Ads', 'listingpro-plugin').'</span>
													</li>
													';
												}
												if(get_post_meta(get_the_ID(), 'events_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_icon8($event_show).'</span>
														<span>'.esc_html__('Events', 'listingpro-plugin').'</span>
													</li>
													';
												}
                                            if(get_post_meta(get_the_ID(), 'bookings_show_hide', true)==''){
                                                $outputHorz .='
                                                <li>
                                                    <span class="icon-text">'.listingpro_icon8($bookings_show).'</span>
                                                    <span>'.esc_html__('Bookings', 'listingpro-plugin').'</span>
                                                </li>
                                                ';
                                            }
                                            if(get_post_meta(get_the_ID(), 'leadform_show_hide', true)==''){
                                                $outputHorz .='
                                                <li>
                                                    <span class="icon-text">'.listingpro_icon8($leadform_show).'</span>
                                                    <span>'.esc_html__('Lead Form', 'listingpro-plugin').'</span>
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
                                                $outputHorz .= '<li>
                                                    <span class="icon-text">' . listingpro_icon8($google_ads_show) . '</span>
                                                    <span>' . esc_html__('Hide Google Ads', 'listingpro-plugin') . '</span>
                                                </li>';
                                            }
											/* new option emd */
											
											$lp_plan_more_fields = listing_get_metabox_by_ID('lp_price_plan_addmore',get_the_ID());
											if(!empty($lp_plan_more_fields)){
												foreach($lp_plan_more_fields as $morefield){
													if(!empty($morefield)){
														$outputHorz .='<li>
															<span class="icon-text">'.listingpro_icon8('checked').'</span>
															<span>'.$morefield.'</span>
														</li>';
													}
												}
											}
                                            
											
											$outputHorz .='
										</ul>
									</div>
									<form  enctype="multipart/form-data" method="post" action="'.listingpro_url('submit-listing').'" class="price-plan-button  pull-right">
										<input type="hidden" name="plan_id" value="'.get_the_ID().'" />';
										
										if( !empty($plan_package_type) && $plan_package_type=="Package" ){
											$plan_price = get_post_meta(get_the_ID(), 'plan_price', true);
											if(!empty($plan_price)){
											
												if(!empty($plan_limit_left)){
													
												
													$outputHorz .='<input id="submit'.$post->ID.'" class="lp-secondary-btn btn-second-hover" type="submit" value="'.esc_html__('Get Started Now', 'listingpro-plugin').'" name="submit">';
												}
												else{
													$outputHorz .='<input id="submit'.$post->ID.'" class="lp-secondary-btn btn-second-hover" type="submit" value="'.esc_html__('Get Started Now', 'listingpro-plugin').'" name="submit">';
												}
											}
											else{
												$outputHorz .='<p>A <strong>'.esc_html__("Package",'listingpro-plugin').'</strong>'.esc_html__(" should have a price ",'listingpro-plugin').'</p>';
											}
										}
										
										else{
											$outputHorz .='<input id="submit'.$post->ID.'" class="lp-secondary-btn btn-second-hover" type="submit" value="'.esc_html__('Get Started Now', 'listingpro-plugin').'" name="submit">';
										}
										
										$outputHorz .= wp_nonce_field( 'price_nonce', 'price_nonce_field'.$post->ID ,true, false );
										$outputHorz .='
									</form>
								</div>
							</div>';
	echo $outputHorz;
?>