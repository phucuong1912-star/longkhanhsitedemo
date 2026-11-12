<?php 
	$current_user = wp_get_current_user();
	$uid = $current_user->ID;
	$username = $current_user->user_login;
	$userEmail = $current_user->user_email;
	$currentURL = '';
	$perma = '';
	$dashQuery = 'dashboard=';
	$currentURL = get_permalink();
	global $wp_rewrite;
	if ($wp_rewrite->permalink_structure == ''){
		$perma = "&";
	}else{
		$perma = "?";
	}
?>
<div id="reviews" role="tab" class="tab-pane fade in">
	<div class="tab-header">
		<h3><?php echo esc_html__('Reviews Submitted', 'listingpro'); ?></h3>
	</div>
	<div class="aligncenter">
		<div id="commentsdiv" class="postbox  hide-if-js" style="display: block;">
			<div class="postbox">
				<div class="inside">
					<table class="widefat fixed striped comments wp-list-table comments-box">
						<tbody id="the-comment-list" data-wp-lists="list:comment">
							
							<div class="panel with-nav-tabs panel-default lp-tabs">
								
								<div class="panel-body">
									<div class="tab-content">
									  <div id="2" class="tab-pane fade in active">
										<!--place pending here -->
										<?php
											global $paged, $wp_query;
											if(isset($_POST['submit_response'])){
												
												$pid = '';
												$userName = '';
												$userEmail = '';
												$pid = $_POST['rewID'];
												$userName = $_POST['userName'];
												$userEmail = $_POST['userEmail'];
												$review_res = '';
												$review_res = $_POST['review_reply'];
												$body = $review_res;
												listing_set_metabox('review_reply', $review_res, $pid);
												$from = get_option('admin_email');
												$headers[] = 'Content-Type: text/html; charset=UTF-8';
												$headers[]= 'From: '.$from . "\r\n";
                                                LP_send_mail( $userEmail, esc_html__('Review Response', 'listingpro'), $body, $headers );
												
												
											}
											/* by zaheer on 28 march */
											
											$args = array(
												'post_type' => 'lp-reviews', 
												'posts_per_page' => -1, 
												'author'	=> $uid,
												'orderby' => 'date',
												'order'   => 'DESC',
												'paged' => $paged,
												'post_status'	=> 'publish'
											);
											/* end by zaheer on 28 march */
											
											$reviews_query = new WP_Query( $args );

											if ( $reviews_query->have_posts() ) {
												while ( $reviews_query->have_posts() ) {
													$reviews_query->the_post();
													
													$authorid = $reviews_query->post_author;
													$poststatus = listing_get_metabox_by_ID('review_status', get_the_ID());
													$review_post = listing_get_metabox_by_ID('listing_id', get_the_ID());
													
														
														echo '<tr id="comment-'.get_the_ID().'" class="comment byuser comment-author-admin bypostauthor even thread-even depth-1 approved" style="background-color: rgb(255, 255, 255);">';

														$data_active = '';
														$data_passive = '';

														$rating = listing_get_metabox_by_ID('rating' ,get_the_ID());
                                                                                                                /* by zaheer on 28 march */
														if(!empty($rating)){
															$rate = $rating;
														}
														else{
															$rate = 0;
														}
                                                                                                                $rating = apply_filters('lp_rating_number_format', $rating );
														/* end by zaheer on 28 march */
														

														
														echo '<td class="comment column-comment has-row-actions column-primary" data-colname="Comment">
															<h4> <span>'.esc_html__('You', 'listingpro').'</span> '.esc_html__('posted a review on ', 'listingpro').'<a href="'.get_the_permalink($review_post).'">'.get_the_title($review_post).'</a>
															</h4>
															<div class="review-count">
																<div class="reviews">
																	<h4>
																		<span>'.esc_html__('Rating:', 'listingpro').'</span>';
																		if( !empty($rating) ){
																			$blankstars = 5;
																			while( $rating > 0 ){
																				echo '<i class="fa fa-star"></i>';
																				$rating--;
																				$blankstars--;
																			}
																			while( $blankstars > 0 ){
																				echo '<i class="fa fa-star-o"></i>';
																				$blankstars--;
																			}
																		}
																		echo
																	'</h4>
																</div>
															</div>
															<a href="#" class="see_more_btn">'.esc_html__('See More', 'listingpro').'</a>
															<div class="review-content">
																<p><span><strong>'.esc_html__('Review:', 'listingpro').'</strong></span>'.get_the_content().'</p>
																<div class="reviews">';
																echo '<a href="#" class="open-reply pull-left">'.esc_html__('Edit this review','listingpro').'</a>';
																
																echo "<div class='post_response'>";
																//get_template_part('review-edit');
																get_template_part('templates/dashboard/review-edit');
																echo '</div>';
																		
																echo '
																</div>
															</div>';
															
															
															echo '
														</td>';
														
														
														
														echo '</tr>';
														echo '<tr class="style="background-color: rgb(255, 255, 255);">';
														echo '<td></td>';
														echo '</tr>';
														
													
												}
												wp_reset_postdata();
											}
											else{	?>
													<div class="lp-blank-section">
														<div class="col-md-12 blank-left-side">
															<img alt="image" src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
														   <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
															<p class="margin-bottom-20"><?php echo esc_html__('You must be here for the first time. You will see messages here when user will contact you through lead form.', 'listingpro'); ?></p>				
														</div>
														
													</div>
													<?php
											}
										
										?>
										
										
									  </div>
									  
									</div>
								</div>
							</div>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!--reviews-->