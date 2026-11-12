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
	
	
	$showCatsBoxes = false;
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
	


							$plan_hot = '';
							$plan_hot = get_post_meta( get_the_ID(), 'plan_hot', true );
							
							
							
							
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

                            $hotClass = '';
							if(!empty($plan_hot) && $plan_hot=="true") {
								$hotClass = 'featured-plan';
							}else {
								$hotClass = '';
							}
							
							//class="featured-active-plan" for active plans 
							$title = ''; if (!empty(get_the_title())) : $title = '<span>'.get_the_title().'</span>'; endif;
							$outputHorz .='
							<div class="lp_hori_view_plan_left_section '.get_the_ID().' '.$hotClass.'">
							<div class="lp-active-badge-on-plan">
								<img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEoAAABdCAYAAAACA/BSAAAACXBIWXMAABYlAAAWJQFJUiTwAAAEcklEQVR4nO3cv0+TQQDG8QestA3oK9GUNoGGEOtkYusKjazGhcHFDXTVGCcGFx1I3DBxcCOwMDDpgP4BNC6SoNGtmCgaCyWKr9RAa20d2oNS2r53fe/XS++bsEBf7vjkerw9GrpGn3yaBDCJDmn/54++zTepm99WHnxmuc4HYBjANRGT0rJyGQBeAIizXNYtZDL6d2UwOfuU5YJOhQKA+4PJ2QnaB3cyFADMDyZnh2ke2OlQFir7lWOdDgVQ7lcGqpLjfmWgDmu5Xxmow1ruVwbqaE33KwN1vIb7lYFq3LH9ykA17th+ZaCad2S/MlCtO9ivDJRz84PJ2XMGyjkLwAufqtEjlg8Zuyh93MD5C/i28qCL9TplK+rhjRAS0aCq4ZlTApWIBhEfCuDOWL+K4dtKCRQBig8FPLOqpEOR1UTyyqqSDlUP45VVJRWqfjWRvLCqpEI1A/HCqpIG1Ww1kXRfVdKgnCB0X1Xc78xjoR70BU4hEQ3gjL8bsQE/wpYP4bPOQz27FcG7r/vY3f+HdLaATbuIjF1EeiuPXL7Ee6pMuYKKhXqQvNSLq9Eg+vzduBjqcT0h8vRMxnqPfD6XL2E9W0DG/ov0VgFLq7brsVhy9dRLZwuIWD7EhwJckFrV5+9GfCiAZKwXaxt7QsdqlOs9amZ5G68/7vKYi2O5fAn3Fr8jnS1IGa82Lpu5DCyVSADH33oisVQjAZxvD0Rg6YAECLiP4omlCxIg6IaTB5ZOSIDAO/OZ5W1X1y+t2togAQKhYi7vq9xezzthUGHrtKvrIy6v5524FTXgbkWIvtNnTdunHq/vwSuBK8rf8uvr2YLjiYDbpy/PhP0BtNmxyubvIuZSO3j1YRcRy4fbY/24fvlMw8fGBnqwkv4jaopMCYFqdABXC0TK2EXMLG9jLrXTEOxqNIg57IiYInNCoCLW4bfN5UuYS+20PD+qBXt4I3RwJhW2lP3F/1hCZhK2fMjlS1hatbH01qY+nczYRdxd/I5ENIg7Y/0tz9hlJwQqvVXAzecbbR/frm3s4e7iHhLRoLI3c9QnBIrXBqziJLNZ5v1RlBkoygwUZQaKMgNFmYGizEBRZqAoM1CUGSjKDBRlBooylVBfFI7NnCqo96j8E5kpReMzpwLqPYDx1PTIr9T0yDw8giUb6gCJfKKK9VjyPJiTCWWjDomUmh55BGBB4lyYkwXVFImUmh6ZhMZYMqAI0junB+qMJRqKGomkK5ZIKGYkko5YoqDaRiLphiUCyjUSSScs3lDckEi6YPGE4o5E0gGLF5QwJJJqLF5QQpFIKrF4QE3JQCKpwnILNVV9USu1KtZLmWO6gVKCVNMkKqcRUmoXSjUSqi+wxyEJqx0o5UgkmVisUNogkWRhsUBph0SSgUULpS0SSTQWDZT2SCSRWE5QnkEiicJqBbXgNSSSCKxmUAvVu1/PxhurEZTnkUg8seqhTgwSiRdWLdSJQyLVYLX9xhACdWKRSFWsCVQOGdkbffJpuFwuo1M+2v15/wPdmpDUltAwdgAAAABJRU5ErkJggg==">
							</div>
								<ul class="horizontal_view_list">';
								if(!empty($plan_hot) && $plan_hot=="true"){
										//echo '<div class="lp-hot">'.esc_html__('Hot','listingpro-plugin').'</div>';

										$outputHorz .='<div class="lp-hot">
													<img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEoAAABdCAYAAAACA/BSAAAACXBIWXMAABYlAAAWJQFJUiTwAAAEcklEQVR4nO3cv0+TQQDG8QestA3oK9GUNoGGEOtkYusKjazGhcHFDXTVGCcGFx1I3DBxcCOwMDDpgP4BNC6SoNGtmCgaCyWKr9RAa20d2oNS2r53fe/XS++bsEBf7vjkerw9GrpGn3yaBDCJDmn/54++zTepm99WHnxmuc4HYBjANRGT0rJyGQBeAIizXNYtZDL6d2UwOfuU5YJOhQKA+4PJ2QnaB3cyFADMDyZnh2ke2OlQFir7lWOdDgVQ7lcGqpLjfmWgDmu5Xxmow1ruVwbqaE33KwN1vIb7lYFq3LH9ykA17th+ZaCad2S/MlCtO9ivDJRz84PJ2XMGyjkLwAufqtEjlg8Zuyh93MD5C/i28qCL9TplK+rhjRAS0aCq4ZlTApWIBhEfCuDOWL+K4dtKCRQBig8FPLOqpEOR1UTyyqqSDlUP45VVJRWqfjWRvLCqpEI1A/HCqpIG1Ww1kXRfVdKgnCB0X1Xc78xjoR70BU4hEQ3gjL8bsQE/wpYP4bPOQz27FcG7r/vY3f+HdLaATbuIjF1EeiuPXL7Ee6pMuYKKhXqQvNSLq9Eg+vzduBjqcT0h8vRMxnqPfD6XL2E9W0DG/ov0VgFLq7brsVhy9dRLZwuIWD7EhwJckFrV5+9GfCiAZKwXaxt7QsdqlOs9amZ5G68/7vKYi2O5fAn3Fr8jnS1IGa82Lpu5DCyVSADH33oisVQjAZxvD0Rg6YAECLiP4omlCxIg6IaTB5ZOSIDAO/OZ5W1X1y+t2togAQKhYi7vq9xezzthUGHrtKvrIy6v5524FTXgbkWIvtNnTdunHq/vwSuBK8rf8uvr2YLjiYDbpy/PhP0BtNmxyubvIuZSO3j1YRcRy4fbY/24fvlMw8fGBnqwkv4jaopMCYFqdABXC0TK2EXMLG9jLrXTEOxqNIg57IiYInNCoCLW4bfN5UuYS+20PD+qBXt4I3RwJhW2lP3F/1hCZhK2fMjlS1hatbH01qY+nczYRdxd/I5ENIg7Y/0tz9hlJwQqvVXAzecbbR/frm3s4e7iHhLRoLI3c9QnBIrXBqziJLNZ5v1RlBkoygwUZQaKMgNFmYGizEBRZqAoM1CUGSjKDBRlBooylVBfFI7NnCqo96j8E5kpReMzpwLqPYDx1PTIr9T0yDw8giUb6gCJfKKK9VjyPJiTCWWjDomUmh55BGBB4lyYkwXVFImUmh6ZhMZYMqAI0junB+qMJRqKGomkK5ZIKGYkko5YoqDaRiLphiUCyjUSSScs3lDckEi6YPGE4o5E0gGLF5QwJJJqLF5QQpFIKrF4QE3JQCKpwnILNVV9USu1KtZLmWO6gVKCVNMkKqcRUmoXSjUSqi+wxyEJqx0o5UgkmVisUNogkWRhsUBph0SSgUULpS0SSTQWDZT2SCSRWE5QnkEiicJqBbXgNSSSCKxmUAvVu1/PxhurEZTnkUg8seqhTgwSiRdWLdSJQyLVYLX9xhACdWKRSFWsCVQOGdkbffJpuFwuo1M+2v15/wPdmpDUltAwdgAAAABJRU5ErkJggg==">
													</div>';
									}
								$outputHorz .='<li>
								<label>
								<input type="radio" name="plan_id" value="'.get_the_ID().'" />
								'.$title.'
								</label>
									
									<div class="per_user_per_listing_price per_user_per_listing_price-hv2">';

										if(!empty($post_price)){
											
													$pricewithCurr = null;
													$post_price = number_format($post_price,2,'.','');
													$post_price = (float)$post_price;
													if($withtaxprice=="1"){
															$taxrate = $listingpro_options['lp_tax_amount'];
															$taxprice = (float)(($taxrate/100)*$post_price);
															$post_price = (float)$post_price + (float)$taxprice;
														}
												
														$lp_currency_position = $listingpro_options['pricingplan_currency_position'];
														if(isset($lp_currency_position) && $lp_currency_position=="left"){
															$pricewithCurr = listingpro_currency_sign().$post_price;
														}
														else{ 
															$pricewithCurr = $post_price.listingpro_currency_sign();
														}
														
														
														if(!empty($perMonthPrce)){
															$outputHorz .='<span>'.$perMonthPrce.'</span>';
														}else{
															
															$outputHorz .='<span>'.$pricewithCurr.'</span>';
														}
														
													}
													else{
														$outputHorz .='<span>'.esc_html__("Free", "listingpro-plugin").'</span>';
													}

													
													if(!empty($perMonthPrce)){
														$outputHorz .='<p>/ '.esc_html__("per month", "listingpro-plugin").'</p><br>';
														
														$outputHorz .='<p> '.$pricewithCurr.esc_html__("billed Annually", "listingpro-plugin").'</p>';
														
														$outputHorz .='<p> '.$plan_text.'</p>';
														;
													}else{
														$outputHorz .='<p>/ '.$plan_text.'</p>';
													}

									$outputHorz.='</div>';
									
									$outputHorz .='</li>';
									
									

$outputHorz .='<div class="lp-list-form-outer-postion"><ul class="lp-listprc">
											<li>
												<span class="icon-text">
													<i class="awesome_plan_icon_check fa fa-check-circle"></i>
												</span>';
												$outputHorz .= '<span>';
												if( !empty($plan_time) ){
													$outputHorz .= esc_html__('Duration', 'listingpro-plugin').' : '.$plan_time.' '.esc_html__('days', 'listingpro-plugin');
												}
												else{
													$outputHorz .= esc_html__('Duration', 'listingpro-plugin');
													$outputHorz .= esc_html__(' : Unlimited days', 'listingpro-plugin');
												}
												$outputHorz .= '</span>';
												$outputHorz .= '<div class="tooltip_price_features">
															<span><i class="fa fa-question"></i></span>
															<p class="lp_tooltip_text">'.esc_html__('Lorem ipsum dolor sit amet, lorem sit.', 'listingpro-plugin').'</p>
															</div>';
												$outputHorz .='</li>';
                                                                                                
                                                                                                $recurring_output = apply_filters('lp_price_plan_recurring_duration', '', get_the_ID(), 'horizontal_view_2' );
                                                                                                $outputHorz .= $recurring_output;
												
												if(!empty($posts_allowed_in_plan) && $plan_type=="Package"){
													$outputHorz .='<li>';
													$outputHorz .='<span class="icon-text">'.listingpro_fontawesome_icon('checked').'</span>';
													$outputHorz .= '<span>'.esc_html__('Max. Listings : ', 'listingpro-plugin'). $posts_allowed_in_plan.'</span>';
													$outputHorz .='</li>';
												}
											
												if(empty($posts_allowed_in_plan) && $plan_type=="Package"){
													$outputHorz .='<li>';
													$outputHorz .='<span class="icon-text">'.listingpro_fontawesome_icon('checked').'</span>';
													$outputHorz .= '<span>'.esc_html__('Max. Listings : Unlimited', 'listingpro-plugin').'</span>';
													$outputHorz .='</li>';
												}
											
											if($listingpro_options['lp_showhide_address']=="1"){
												if(get_post_meta(get_the_ID(), 'map_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($map_checked).'</span>
														<span>'.esc_html__('Map Display', 'listingpro-plugin').'</span>
													</li>';
												}
											}
											if($listingpro_options['phone_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'contact_show_hide', true)==''){
													$outputHorz .='
															<li>
																<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($contact_checked).'</span>
																<span>'.esc_html__('Contact Display', 'listingpro-plugin').'</span>
															</li>
															';
												}
											}
											if($listingpro_options['file_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'gall_show_hide', true)==''){
													$outputHorz .='
														<li>
															<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($gallery_checked).'</span>
															<span>'.esc_html__('Image Gallery', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											if($listingpro_options['vdo_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'video_show_hide', true)==''){
													$outputHorz .='
														<li>
															<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($video_checked).'</span>
															<span>'.esc_html__('Video', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											if(get_post_meta(get_the_ID(), 'tagline_show_hide', true)==''){
												$outputHorz .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($tagline_checked).'</span>
													<span>'.esc_html__('Business Tagline', 'listingpro-plugin').'</span>
												</li>
												';
											}
											if($listingpro_options['location_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'location_show_hide', true)==''){
													$outputHorz .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($location_checked).'</span>
															<span>'.esc_html__('Location', 'listingpro-plugin').'</span>
														</li>';
												}
											}
											if($listingpro_options['web_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'website_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($website_checked).'</span>
														<span>'.esc_html__('Website', 'listingpro-plugin').'</span>
													</li>';
												}
												
											}
											
											if($listingpro_options['listin_social_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'social_show_hide', true)==''){
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($social_checked).'</span>
														<span>'.esc_html__('Social Links', 'listingpro-plugin').'</span>
													</li>
													';
												}
											}
											if($listingpro_options['faq_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'faqs_show_hide', true)==''){
													$outputHorz .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($faq_checked).'</span>
															<span>'.esc_html__('FAQ', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											if($listingpro_options['currency_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'price_show_hide', true)==''){
													$outputHorz .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($price_checked).'</span>
															<span>'.esc_html__('Price Range', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											
											if($listingpro_options['tags_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'tags_show_hide', true)==''){
													$outputHorz .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($tag_key_checked).'</span>
															<span>'.esc_html__('Tags/Keywords', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											if($listingpro_options['oph_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'bhours_show_hide', true)==''){
													$outputHorz .='		
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($bhours_checked).'</span>
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
															<span class="icon-text">'.listingpro_fontawesome_icon($resurva_show).'</span>
															<span>'.esc_html__('Resurva', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											if(get_post_meta(get_the_ID(), 'timekit_show_hide', true)==''){
												$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($timekit_show).'</span>
														<span>'.esc_html__('Timekit', 'listingpro-plugin').'</span>
													</li>
													';
											}
											if(get_post_meta(get_the_ID(), 'menu_show_hide', true)==''){
												
													$outputHorz .='
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($menu_show).'</span>
														<span>'.esc_html__('Menu', 'listingpro-plugin').'</span>
													</li>
													';
											}
											if(get_post_meta(get_the_ID(), 'announcment_show_hide', true)==''){
												$outputHorz .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($announcment_show).'</span>
													<span>'.esc_html__('Announcment', 'listingpro-plugin').'</span>
												</li>
												';
											}
											if(get_post_meta(get_the_ID(), 'deals_show_hide', true)==''){
												$outputHorz .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($deals_show).'</span>
													<span>'.esc_html__('Deals-Offers-Discounts', 'listingpro-plugin').'</span>
												</li>
												';
											}
											if(get_post_meta(get_the_ID(), 'metacampaign_show_hide', true)==''){
												$outputHorz .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($competitor_show).'</span>
													<span>'.esc_html__('Hide competitors Ads', 'listingpro-plugin').'</span>
												</li>
												';
											}
											if(get_post_meta(get_the_ID(), 'events_show_hide', true)==''){	
												$outputHorz .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($event_show).'</span>
													<span>'.esc_html__('Events', 'listingpro-plugin').'</span>
												</li>
												';
											}
                                            if(get_post_meta(get_the_ID(), 'bookings_show_hide', true)==''){
                                                $outputHorz .='
                                                <li>
                                                    <span class="icon-text">'.listingpro_fontawesome_icon($bookings_show).'</span>
                                                    <span>'.esc_html__('Bookings', 'listingpro-plugin').'</span>
                                                </li>
                                                ';
                                            }
                                            if(get_post_meta(get_the_ID(), 'leadform_show_hide', true)==''){
                                                $outputHorz .='
                                                <li>
                                                    <span class="icon-text">'.listingpro_fontawesome_icon($leadform_show).'</span>
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
    $outputHorz .=' <li>
                                                    <span class="icon-text">' . listingpro_fontawesome_icon($google_ads_show) . '</span>
                                                    <span>' . esc_html__('Hide Google Ads', 'listingpro-plugin') . '</span>
                                                </li>';
}

											/* new option emd */
											
											$lp_plan_more_fields = listing_get_metabox_by_ID('lp_price_plan_addmore',get_the_ID());
											if(!empty($lp_plan_more_fields)){
												foreach($lp_plan_more_fields as $morefield){
													if(!empty($morefield)){
														$outputHorz .='<li>
															<span class="icon-text">'.listingpro_fontawesome_icon('checked').'</span>
															<span>'.$morefield.'</span>
														</li>';
													}
												}
											}
											$outputHorz .='
										</ul>

										<form  enctype="multipart/form-data" method="post" action="'.listingpro_url('submit-listing').'" class="price-plan-button horizontial_view_width">
										<input type="hidden" name="plan_id" value="'.get_the_ID().'"/>';
										
										if( !empty($plan_package_type) && $plan_package_type=="Package" ){
											$plan_price = get_post_meta(get_the_ID(), 'plan_price', true);
											if(!empty($plan_price)){
											
												if(!empty($plan_limit_left)){
													
												
													$outputHorz .='<input id="submit'.$post->ID.'" class="lp-secondary-btn btn-second-hover lp-secondary-choose" type="submit" value="'.esc_html__('CHOOSE PLAN', 'listingpro-plugin').'" name="submit">';
												}
												else{
													$outputHorz .='<input id="submit'.$post->ID.'" class="lp-secondary-btn btn-second-hover lp-secondary-choose" type="submit" value="'.esc_html__('CHOOSE PLAN', 'listingpro-plugin').'" name="submit">';
												}
											}
											else{
												$outputHorz .='<p>A <strong>'.esc_html__("Package",'listingpro-plugin').'</strong>'.esc_html__(" should have a price ",'listingpro-plugin').'</p>';
											}
										}
										
										else{
											$outputHorz .='<input id="submit'.$post->ID.'" class="lp-secondary-btn btn-second-hover lp-secondary-choose" type="submit" value="'.esc_html__('CHOOSE PLAN', 'listingpro-plugin').'" name="submit">';
										}
										
										$outputHorz .= wp_nonce_field( 'price_nonce', 'price_nonce_field'.$post->ID ,true, false );
										$outputHorz .='
									</form>
									<input class="lp-secondary-btn btn-second-hover lp-secondary-choose lp-claim-plan-btn" type="submit" value="'.esc_html__('CHOOSE PLAN', 'listingpro-plugin').'" name="submit" style="display:none;">
									</div>

								</ul>';

								$outputHorz .='';
								
								

							$outputHorz .='</div>';
							
	echo $outputHorz;
?>