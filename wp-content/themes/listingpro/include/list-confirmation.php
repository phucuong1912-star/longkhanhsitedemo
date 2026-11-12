<?php
/**
 * List Confirmation.
 *
 */
/* ============== ListingPro listing confirmation ============ */

	if ( ! function_exists( 'listingpro_post_confirmation' ) ) {

		function listingpro_post_confirmation($post) {
			if (isset($_POST['publish']) && !empty($_POST['publish_post']) ) {	
				$my_post = array();
				$my_post['ID'] = $_POST['publish_post'];
				$my_post['post_status'] = 'publish';
				// Update the post into the database
				$postid = wp_update_post( $my_post );
				//update package counter in db
				//lp_update_credit_package($_POST['publish_post']);
				// generating email
				global $listingpro_options;
				$current_user = wp_get_current_user();
				$user_email = $current_user->user_email;
				$user_name = $current_user->user_login;
				$website_url = site_url();
				$website_name = get_option('blogname');
				$listing_title = get_the_title($postid);
				$listing_url = get_the_permalink($postid);
				$subject = $listingpro_options['listingpro_subject_listing_approved'];
				$mail_content = $listingpro_options['listingpro_listing_approved'];



				$formated_mail_content = lp_sprintf2("$mail_content", array(
					'website_url' => "$website_url",
					'listing_title' => "$listing_title",
					'website_name' => "$website_name",
					'user_name' => "$user_name",
					'listing_url' => "$listing_url"
				));
				
				$from = get_option('admin_email');
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$headers[]= 'From: '.$from . "\r\n";



				lp_mail_headers_append();
                LP_send_mail( $user_email, $subject, $formated_mail_content, $headers);
				lp_mail_headers_remove();
				wp_redirect($listing_url);
			}	
			$current_user = wp_get_current_user();
			global $wpdb;
			$dbprefix = $wpdb->prefix;
			$ftablename = 'listing_orders';
			$ftablename =$dbprefix.$ftablename;
			if ($post->post_author == $current_user->ID) {
				global $wp_rewrite,$listingpro_options;
				$edit_post_page_id = $listingpro_options['edit-listing'];

                $E_pageID = url_to_postid($edit_post_page_id);
				if ($E_pageID) {
					$edit_post_page_id = get_permalink($E_pageID);
				}

				$postID = $post->ID;
				if ($wp_rewrite->permalink_structure == ''){
					//we are using ?page_id
					$edit_post = $edit_post_page_id."&lp_post=".$postID;

				}else{
					//we are using permalinks
					$edit_post = $edit_post_page_id."?lp_post=".$postID;
				}

				if(is_single()){
					
				?>
				<div class="unhidebar-section">
                    <i class="fa fa-angle-up" aria-hidden="true"></i>
                </div>
				<div class="lp_confirmation">
					<div class="widget-box padding-0 lp-border-radius-5">
						<div class="widget-content">
				<?php } ?>
                <div class="lp-confi-bottom-bar"><i class="fa fa-angle-down" aria-hidden="true"></i><?php echo esc_html__( 'Hide This Bar', 'listingpro' ); ?></div>
							<ul class="list-style-none list-st-img">
								<li>
									<a class="edit-list" href="<?php echo esc_url($edit_post); ?>">
										<span><i class="fa fa-pencil" aria-hidden="true"></i><?php echo esc_html__('Edit','listingpro'); ?></span>
									</a>
								</li>
								<?php 
								if (get_post_status ( $post->ID ) == 'pending' || get_post_status ( $post->ID ) == 'expired') {
								$checkout = $listingpro_options['payment-checkout'];
								$checkout_url = get_permalink( $checkout );
								?>
									<li>
										<?php
											$paidmode = '';
											$paidmode = $listingpro_options['enable_paid_submission'];
											$planID = '';
											$planPrice = '';
											$postmeta = get_post_meta($post->ID, 'lp_listingpro_options', true);
											$planID = $postmeta['Plan_id'];
											$planPrice = get_post_meta($planID, 'plan_price', true);
											$plan_type = '';
											$plan_type = get_post_meta($planID, 'plan_package_type', true);
											$check_plan_credit = lp_check_package_has_credit($planID);
											$checkIfpurchasedandpending = lp_if_listing_in_purchased_package($planID, $post->ID);
											$paybuttonText = '';
											$paybuttonText = esc_html__('Pay & Publish', 'listingpro');
                                            $discounted = get_post_meta($postID, 'discounted', true);
										?>
										<?php
											if( !empty($paidmode) && $paidmode=="yes" ){
											?>
													<?php
													//check if plan_type already purchased
													if( !empty($checkIfpurchasedandpending) && $checkIfpurchasedandpending==true ){
													?>
														<?php if($listingpro_options['listings_admin_approved']=="no"){ ?>
																<form id="lp_recheck" method="post">
																	<input class="lp-review-btn btn-second-hover" type="submit" value="<?php echo esc_html__('Publish','listingpro'); ?>" name="publish">
																	<input type="hidden" value="<?php echo esc_attr($postID); ?>" name="publish_post">
																</form>
														<?php } ?>
														<?php
													}
													//check if plan_type not purchased
													else if(!empty($planPrice) ){
														$listing_payment_status;	
														$table = 'listing_orders';
														$data = '*';
														$condition = 'post_id="'.$post->ID.'" AND plan_id="'.$planID.'" AND status="pending"';
														if($wpdb->get_var("SHOW TABLES LIKE '$ftablename'") == $ftablename) {
															$listing_payment_status = lp_get_data_from_db($table, $data, $condition);
														}
														
														if(empty($listing_payment_status)){
															$condition = '';
															$condition = 'post_id="'.$post->ID.'" AND plan_id="'.$planID.'" AND status="success"';
															if($wpdb->get_var("SHOW TABLES LIKE '$ftablename'") == $ftablename) {
																$listing_payment_status = lp_get_data_from_db($table, $data, $condition);
															}
															
															if(empty($listing_payment_status)){
																$condition = '';
																$condition = 'post_id LIKE "%'.$post->ID.'%" AND plan_id="'.$planID.'" AND status="expired"';
																if($wpdb->get_var("SHOW TABLES LIKE '$ftablename'") == $ftablename) {
																	$listing_payment_status = lp_get_data_from_db($table, $data, $condition);
																}
																if(empty($listing_payment_status) && $discounted ==''){
															?>
																<a href="<?php echo esc_url($checkout_url);  ?>" class="lp-review-btn btn-second-hover text-center lp-pay-publish-btn" data-lpthisid="<?php echo esc_attr( $postID ); ?>" title="pay"><i class="fa fa-credit-card" aria-hidden="true"></i><?php echo esc_attr( $paybuttonText ); ?></a>

															<?php
																}
															}
														}
														
														/* if expired
													
														if(get_post_status ( $post->ID ) == 'expired'){
														?>
															<form id="expirelistinsubmission" method="post" action="<?php echo esc_url($checkout_url);  ?>">	
																<input type="hidden" name="planid" value="<?php echo esc_attr( $planID ); ?>" />
																<input type="hidden" name="listingid" value="<?php echo esc_attr( $post->ID ); ?>" />
																<input type="submit" class="lp-review-btn btn-second-hover text-center lp-pay-publish-btn" data-lpthisid="<?php echo esc_attr( $postID ); ?>" value="<?php echo esc_attr( $paybuttonText ); ?>" />
															</form>
														<?php																
														}
														
														 end if expired */
														 
													}
													if( empty($planPrice) ){
													?>
														<?php if($listingpro_options['listings_admin_approved']=="no"){ ?> 
																<form id="lp_recheck" method="post">
                                                                    <span><i class="fa fa-credit-card" aria-hidden="true"></i></span>
																	<input class="lp-review-btn btn-second-hover" type="submit" value="<?php echo esc_html__('Publish','listingpro'); ?>" name="publish">
																	<input type="hidden" value="<?php echo esc_attr($postID); ?>" name="publish_post">
																</form>

														<?php } ?>
													<?php
													}
													/*
													else if( !empty($plan_type) && $plan_type=="Pay Per Listing" &&  !empty($planPrice) ){ ?>
														<a href="<?php echo esc_url($checkout_url);  ?>" class="lp-review-btn btn-second-hover text-center lp-pay-publish-btn" data-lpthisid="<?php echo esc_attr( $postID ); ?>" title="pay"><?php echo esc_html__('Pay','listingpro'); ?></a>
													<?php	
													}*/
													
													?>
										<?php }else{ ?>
													<?php if($listingpro_options['listings_admin_approved']=="no"){ ?> 
															<form id="lp_recheck" method="post">
                                                                <span><i class="fa fa-credit-card" aria-hidden="true"></i></span>
																<input class="lp-review-btn btn-second-hover" type="submit" value="<?php echo esc_html__('Publish','listingpro'); ?>" name="publish">
																<input type="hidden" value="<?php echo esc_attr($postID); ?>" name="publish_post">
															</form>
													<?php } ?>
										<?php } ?>
										
								</li>
						<?php } ?>
							</ul>
				<?php if(is_single()){ ?>
						</div>
						</div>
					</div>
				<?php		
				}
			}		

		}

	}