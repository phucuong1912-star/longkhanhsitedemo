
<?php
	global $listingpro_options;
	$listing_access_only_users = $listingpro_options['lp_allow_vistor_submit'];
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
	$output1 = null;
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
	
	/* vertical view code */
									
							global $post;
							$plan_package_type = get_post_meta( get_the_ID(), 'plan_package_type', true );
							$post_price = get_post_meta(get_the_ID(), 'plan_price', true);

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
							
							$plan_hot = '';
							$plan_hot = get_post_meta( get_the_ID(), 'plan_hot', true );
							
							$plan_type = '';
							$plan_type_name = '';
							$plan_type = get_post_meta(get_the_ID(), 'plan_package_type', true);
							if( $plan_type=="Pay Per Listing" ){
								$plan_type_name = esc_html__("Per Listing",'listingpro-plugin');
							}
							else{
								$plan_type_name = esc_html__("Per Package",'listingpro-plugin');
							}
							
							$currentPlanID = get_the_ID();
							$plan_text = '';
							$used = '';
							$plan_limit_left = '';
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

							$hotClass = '';
							if(!empty($plan_hot) && $plan_hot=="true") {
								$hotClass = 'featured-plan';
							}else {
								$hotClass = '';
							}


							// class="featured-active-plan" for active plans 

							$output1 .='
							
							<div class="col-md-4 '.get_the_ID().' '.$hotClass.' pricing_plans_v2">
								<div class="lp-price-main text-center">';

							// Active Plan badge
							$output1.='<div class="lp-active-badge-on-plan">
								<p>Active Plan</p>
								
							</div>';
								
								$user_ID = get_current_user_id();
								
								
								if( !empty($plan_type) && $plan_type=="Package" ){
									if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
										$results = $wpdb->get_results( "SELECT * FROM ".$dbprefix."listing_orders WHERE user_id ='$user_ID' AND plan_id='$currentPlanID' AND status = 'success' AND plan_type='$plan_type'" );
									}
									
									if(is_numeric($plan_limit_left)){
										if( !empty($results) && count($results)>0 ){
											if(!empty($post_price) && $plan_limit_left>0){
												$output1 .='<div class="lp-sales-option">
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
												$output1 .='<div class="lp-sales-option">
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

									/*if( isset($plan_title_img) && $plan_title_img != '' )
									{
										//$plan_title_bg  =   "background: url($plan_title_img); background-size:cover;";
										//$classForBg .= ' lp-overlay-pricing';
										
									}
									else
									{
										//$plan_title_bg  =   "background-color: $plan_title_color;";
									}
									*/
									if($plan_title_img == ''){
										$bg_img = plugins_url('/').'listingpro-plugin/images/free-plan.png';		
									}else{
										$bg_img = $plan_title_img;
									}

									$title = ''; if (!empty(get_the_title())) : $title = '<a>'.get_the_title().'</a>'; endif;
									$output1 .='
									<div class="'.$classForBg.' version_bottom_padding" style="background-color:#fff;">
										<div class="lp-plane-top-wrape lp_plan_image_heading lp_plan_image_heading_v6">

											'.$title.'
											<div class="lp_plan_image_icons">
											<img alt="image" src="'.$bg_img.'"></div>';
											
											// per package day and listing
											$output1.='<div class="listing_day_duration listing_day_duration_v6">';
												$output1 .= '<span>';
												if( !empty($plan_time) ){
													$output1 .= $plan_time.' '.esc_html__('days', 'listingpro-plugin');
												}
												else{
													$output1 .= esc_html__('Unlimited days', 'listingpro-plugin');
												}
												$output1 .= '</span>';
												
												if(!empty($posts_allowed_in_plan) && $plan_type=="Package"){

													$output1 .= '<span>'. $posts_allowed_in_plan.esc_html__(' Listings', 'listingpro-plugin').'</span>';
												}
											
												if(empty($posts_allowed_in_plan) && $plan_type=="Package"){

													$output1 .= '<span>'.esc_html__('Listings : Unlimited', 'listingpro-plugin').'</span>';
												}
											$output1.= '</div>';


											if(!empty($post_price)){
												if($withtaxprice=="1"){
													$taxrate = $listingpro_options['lp_tax_amount'];
													$taxprice = (float)(($taxrate/100)*$post_price);
													$post_price = (float)$post_price + (float)$taxprice;
												}
												$post_price = round($post_price,2);
												$lp_currency_position = $listingpro_options['pricingplan_currency_position'];
												if(isset($lp_currency_position) && $lp_currency_position=="left"){
													$output1 .='<p>'.'<sup>'.listingpro_currency_sign().'</sup>'.$post_price.'</p>';
												}
												else{
													$output1 .='<p>'.$post_price.'<sup>'.listingpro_currency_sign().'</sup>'.'</p>';
												}
											}
											else{
												$output1 .='<p>'.esc_html__("Free", 'listingpro-plugin').'</p>';
											}
											

											if(is_numeric($plan_limit_left)){
												if( !empty($results) && count($results)>0 ){
													if(!empty($post_price) && $plan_limit_left>0){
														$output1 .= '<span style="font-size:12px;color:#fff" class="allowedListing">'.esc_html__('Remaining Listings : ', 'listingpro-plugin') . $plan_limit_left.'</span>';
													}
												}
											}
										$output1 .='	</div>';
									$output1 .='<form method="post" action="'.listingpro_url('submit-listing').'" class="price-plan-button price-plan-button-v6">
											<input type="hidden" name="plan_id" value="'.$post->ID.'" />';
											
											if(empty($post_price) && $plan_type=="Package"){
												$output1 .='<p>A <strong>'.esc_html__("Package",'listingpro-plugin').'</strong>'.esc_html__(" should have a price ",'listingpro-plugin').'</p>';

											}
											else if( !empty($plan_type) && $plan_type=="Package" ){
												if(!empty($plan_limit_left)){
											
													$output1 .='<input id="submit'.$post->ID.'" class="lp-price-free lp-without-prc btn" type="submit" value="'.esc_html__('Choose Plan', 'listingpro-plugin').'" name="submit">';
												}
												else{
													$output1 .='<input id="submit'.$post->ID.'" class="lp-price-free lp-without-prc btn" type="submit" value="'.esc_html__('Choose Plan', 'listingpro-plugin').'" name="submit">';
												}
											}
											else{
											
													$output1 .='<input id="submit'.$post->ID.'" class="lp-price-free lp-without-prc btn" type="submit" value="'.esc_html__('Choose Plan', 'listingpro-plugin').'" name="submit">';
												
												
											}
											$output1 .= wp_nonce_field( 'price_nonce', 'price_nonce_field'.$post->ID ,true, false );
											
											if(isset($_POST['lp_cat_plan_submit'])){
												$lp_s_cat= $_POST['lp-slected-plan-cat'];
												$output1 .= '<input type="hidden" value="'.$lp_s_cat.'" name="lp_pre_selected_cats" />';
											}
											
											$output1 .='
										</form>
									</div>';

									$output1 .='
									
									
							</div>

							<div class="lp-view-all-price-feature"><span class="lp-hide-show-price-features">'.esc_html__('View All Features', 'listingpro-plugin').'</span></div>';


							$output1 .='<div class="section-lp-price-list">';

							if(!empty($plan_hot) && $plan_hot=="true"){
										//$output1 .='<div class="lp-hot">'.esc_html__('Hot','listingpro-plugin').'</div>';

										$output1 .='<div class="lp-hot">
													<img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEoAAABdCAYAAAACA/BSAAAACXBIWXMAABYlAAAWJQFJUiTwAAAEcklEQVR4nO3cv0+TQQDG8QestA3oK9GUNoGGEOtkYusKjazGhcHFDXTVGCcGFx1I3DBxcCOwMDDpgP4BNC6SoNGtmCgaCyWKr9RAa20d2oNS2r53fe/XS++bsEBf7vjkerw9GrpGn3yaBDCJDmn/54++zTepm99WHnxmuc4HYBjANRGT0rJyGQBeAIizXNYtZDL6d2UwOfuU5YJOhQKA+4PJ2QnaB3cyFADMDyZnh2ke2OlQFir7lWOdDgVQ7lcGqpLjfmWgDmu5Xxmow1ruVwbqaE33KwN1vIb7lYFq3LH9ykA17th+ZaCad2S/MlCtO9ivDJRz84PJ2XMGyjkLwAufqtEjlg8Zuyh93MD5C/i28qCL9TplK+rhjRAS0aCq4ZlTApWIBhEfCuDOWL+K4dtKCRQBig8FPLOqpEOR1UTyyqqSDlUP45VVJRWqfjWRvLCqpEI1A/HCqpIG1Ww1kXRfVdKgnCB0X1Xc78xjoR70BU4hEQ3gjL8bsQE/wpYP4bPOQz27FcG7r/vY3f+HdLaATbuIjF1EeiuPXL7Ee6pMuYKKhXqQvNSLq9Eg+vzduBjqcT0h8vRMxnqPfD6XL2E9W0DG/ov0VgFLq7brsVhy9dRLZwuIWD7EhwJckFrV5+9GfCiAZKwXaxt7QsdqlOs9amZ5G68/7vKYi2O5fAn3Fr8jnS1IGa82Lpu5DCyVSADH33oisVQjAZxvD0Rg6YAECLiP4omlCxIg6IaTB5ZOSIDAO/OZ5W1X1y+t2togAQKhYi7vq9xezzthUGHrtKvrIy6v5524FTXgbkWIvtNnTdunHq/vwSuBK8rf8uvr2YLjiYDbpy/PhP0BtNmxyubvIuZSO3j1YRcRy4fbY/24fvlMw8fGBnqwkv4jaopMCYFqdABXC0TK2EXMLG9jLrXTEOxqNIg57IiYInNCoCLW4bfN5UuYS+20PD+qBXt4I3RwJhW2lP3F/1hCZhK2fMjlS1hatbH01qY+nczYRdxd/I5ENIg7Y/0tz9hlJwQqvVXAzecbbR/frm3s4e7iHhLRoLI3c9QnBIrXBqziJLNZ5v1RlBkoygwUZQaKMgNFmYGizEBRZqAoM1CUGSjKDBRlBooylVBfFI7NnCqo96j8E5kpReMzpwLqPYDx1PTIr9T0yDw8giUb6gCJfKKK9VjyPJiTCWWjDomUmh55BGBB4lyYkwXVFImUmh6ZhMZYMqAI0junB+qMJRqKGomkK5ZIKGYkko5YoqDaRiLphiUCyjUSSScs3lDckEi6YPGE4o5E0gGLF5QwJJJqLF5QQpFIKrF4QE3JQCKpwnILNVV9USu1KtZLmWO6gVKCVNMkKqcRUmoXSjUSqi+wxyEJqx0o5UgkmVisUNogkWRhsUBph0SSgUULpS0SSTQWDZT2SCSRWE5QnkEiicJqBbXgNSSSCKxmUAvVu1/PxhurEZTnkUg8seqhTgwSiRdWLdSJQyLVYLX9xhACdWKRSFWsCVQOGdkbffJpuFwuo1M+2v15/wPdmpDUltAwdgAAAABJRU5ErkJggg==">
													</div>';
									}
										

							$output1 .='
														
								<div class="lp-price-list">';
									
										$output1 .='<ul class="lp-listprc">
											<li class="outer_tooltip_price">
												<span class="icon-text">
													<i class="awesome_plan_icon_check fa fa-check-circle"></i>
												</span>';
												$output1 .= '<span>';
												if( !empty($plan_time) ){
													$output1 .= esc_html__('Duration', 'listingpro-plugin').' : '.$plan_time.' '.esc_html__('days', 'listingpro-plugin');
												}
												else{
													$output1 .= esc_html__('Duration', 'listingpro-plugin');
													$output1 .= esc_html__(' : Unlimited days', 'listingpro-plugin');
												}
												$output1 .= '</span>';
												$output1 .= '<div class="tooltip_price_features">
															<span><i class="fa fa-question"></i></span>
															<p class="lp_tooltip_text">Lorem ipsum dolor sit amet, lorem sit.</p>
															</div>';
												$output1 .='</li>';
												
												if(!empty($posts_allowed_in_plan) && $plan_type=="Package"){
													$output1 .='<li class="outer_tooltip_price">';
													$output1 .='<span class="icon-text">'.listingpro_fontawesome_icon('checked').'</span>';
													$output1 .= '<span>'.esc_html__('Max. Listings : ', 'listingpro-plugin'). $posts_allowed_in_plan.'</span>';
													$output1 .='</li>';
												}
											
												if(empty($posts_allowed_in_plan) && $plan_type=="Package"){
													$output1 .='<li>';
													$output1 .='<span class="icon-text">'.listingpro_fontawesome_icon('checked').'</span>';
													$output1 .= '<span>'.esc_html__('Max. Listings : Unlimited', 'listingpro-plugin').'</span>';

													$output1 .='</li>';
												}
											
											if($listingpro_options['lp_showhide_address']=="1"){
												if(get_post_meta(get_the_ID(), 'map_show_hide', true)==''){
													$output1 .='
													<li>
														<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($map_checked).'</span>
														<span>'.esc_html__('Map Display', 'listingpro-plugin').'</span>
													</li>';
												}
											}
											if($listingpro_options['phone_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'contact_show_hide', true)==''){
													$output1 .='
															<li>
																<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($contact_checked).'</span>
																<span>'.esc_html__('Contact Display', 'listingpro-plugin').'</span>

															</li>';
												}
											}
											if($listingpro_options['file_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'gall_show_hide', true)==''){
													$output1 .='
														<li>
															<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($gallery_checked).'</span>
															<span>'.esc_html__('Image Gallery', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											if($listingpro_options['vdo_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'video_show_hide', true)==''){
													$output1 .='
														<li>
															<span class="icon icons8-Cancel">'.listingpro_fontawesome_icon($video_checked).'</span>
															<span>'.esc_html__('Video', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											if(get_post_meta(get_the_ID(), 'tagline_show_hide', true)==''){
												$output1 .='
												<li>
													<span class="icon-text">'.listingpro_fontawesome_icon($tagline_checked).'</span>
													<span>'.esc_html__('Business Tagline', 'listingpro-plugin').'</span>
												</li>
												';
											}
											if($listingpro_options['location_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'location_show_hide', true)==''){
													$output1 .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($location_checked).'</span>
															<span>'.esc_html__('Location', 'listingpro-plugin').'</span>
														</li>';
												}
											}
											if($listingpro_options['web_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'website_show_hide', true)==''){
													$output1 .='
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($website_checked).'</span>
														<span>'.esc_html__('Website', 'listingpro-plugin').'</span>
													</li>';
												}
												
											}
											
											if($listingpro_options['listin_social_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'social_show_hide', true)==''){
													$output1 .='
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($social_checked).'</span>
														<span>'.esc_html__('Social Links', 'listingpro-plugin').'</span>
													</li>
													';
												}
											}
											if($listingpro_options['faq_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'faqs_show_hide', true)==''){
													$output1 .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($faq_checked).'</span>
															<span>'.esc_html__('FAQ', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											if($listingpro_options['currency_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'price_show_hide', true)==''){
													$output1 .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($price_checked).'</span>
															<span>'.esc_html__('Price Range', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}
											
											if($listingpro_options['tags_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'tags_show_hide', true)==''){
													$output1 .='
														<li>
															<span class="icon-text">'.listingpro_fontawesome_icon($tag_key_checked).'</span>
															<span>'.esc_html__('Tags/Keywords', 'listingpro-plugin').'</span>
														</li>
														';
												}
											}

$google_ads_show = get_post_meta(get_the_ID(), 'lp_hidegooglead', true);
if ($google_ads_show == "true") {
    $google_ads_show = 'checked';
} else {
    $google_ads_show = 'unchecked';
}
if (get_post_meta(get_the_ID(), 'googlead_show_hide', true) == '') {
    $output1 .= '<li>
                                                    <span class="icon-text">' . listingpro_fontawesome_icon($google_ads_show) . '</span>
                                                    <span>' . esc_html__('Hide Google Ads', 'listingpro-plugin') . '</span>
                                                </li>';
}
											if($listingpro_options['oph_switch']=="1"){
												if(get_post_meta(get_the_ID(), 'bhours_show_hide', true)==''){
													$output1 .='		
													<li>
														<span class="icon-text">'.listingpro_fontawesome_icon($bhours_checked).'</span>
														<span>'.esc_html__('Business Hours', 'listingpro-plugin').'</span>
													</li>
													';
												}
											}
											$lp_plan_more_fields = listing_get_metabox_by_ID('lp_price_plan_addmore',get_the_ID());
											if(!empty($lp_plan_more_fields)){
												foreach($lp_plan_more_fields as $morefield){
													if(!empty($morefield)){
														$output1 .='<li>
															<span class="icon-text">'.listingpro_fontawesome_icon('checked').'</span>
															<span>'.$morefield.'</span>
														</li>';
													}
												}
											}
											$output1 .='
										</ul>
									</div>
								</div>
							</div>';

		echo $output1;


?>

