	<?php
			$output = null;
			
			global $listingpro_options;
			$lp_review_switch = $listingpro_options['lp_review_switch'];

			if(!isset($postGridCount)){
				$postGridCount = '0';
			}
			global $postGridCount;
			$postGridCount++;
			
			$deafaultFeatImg = lp_default_featured_image_listing();
			
				$listing_style = 'col-md-12 col-sm-12';
					
				$latitude = listing_get_metabox('latitude');
				$longitude = listing_get_metabox('longitude');
				$gAddress = listing_get_metabox('gAddress');
				$isfavouriteicon = listingpro_is_favourite_grids(get_the_ID(),$onlyicon=true);
				$isfavouritetext = listingpro_is_favourite_grids(get_the_ID(),$onlyicon=false);

				$adStatus = apply_filters('lp_get_ad_status', '', get_the_ID());
				$CHeckAd = '';
				$adClass = '';
				if($adStatus == 'active'){
					$CHeckAd = '<span class="listing-pro">'.esc_html__('Ad','listingpro').'</span>';
					$adClass = 'promoted';
				}
				$claimed_section = listing_get_metabox('claimed_section');

				$claim = '';
				$claimStatus = '';
				
				if($claimed_section == 'claimed') {
					if(is_singular( 'listing' ) ){
						$claimStatus = esc_html__('Claimed', 'listingpro');
					}
					$claim = '<span class="verified simptip-position-top simptip-movable" data-tooltip="'. esc_html__('Claimed', 'listingpro').'"><i class="fa fa-check"></i></span>';

				}elseif($claimed_section == 'not_claimed') {
					$claim = '';

				}
				
					?>
					<div class="row">
					<div class="<?php echo esc_attr($listing_style); ?> <?php echo esc_attr($adClass); ?> lp-grid-box-contianer grid_view2 card1 lp-grid-box-contianer1" data-title="<?php echo get_the_title(); ?>" data-postid="<?php echo get_the_ID(); ?>"   data-lattitue="<?php echo esc_attr($latitude); ?>" data-longitute="<?php echo esc_attr($longitude); ?>" data-posturl="<?php echo get_the_permalink(); ?>">
						<?php if(is_page_template('template-favourites.php')){ ?>
							<div class="remove-fav md-close" data-post-id="<?php echo get_the_ID(); ?>">
								<i class="fa fa-close"></i>
							</div>
						<?php } ?>
						<div class="lp-grid-box">
							<div class="lp-grid-desc-container lp-border clearfix">
								<div class="lp-grid-box-thumb-container" >
								<div class="lp-grid-box-thumb">
									<div class="show-img">
										<?php
											if ( has_post_thumbnail()) {
												$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'listingpro-listing-grid' );
													if(!empty($image[0])){
														echo "<a href='".get_the_permalink()."' >
																<img alt='image' src='" . $image[0] . "' />
															</a>";
													}else {
														echo '
														<a href="'.get_the_permalink().'" >
															<img src="'.esc_html__('https://placehold.co/184x135', 'listingpro').'" alt="image">
														</a>';
													}	
											}elseif(!empty($deafaultFeatImg)){
												
												echo "<a href='".get_the_permalink()."' >
														<img alt='image' src='".$deafaultFeatImg."' />
													</a>";
												
											}else {
												echo '
												<a href="'.get_the_permalink().'" >
													<img src="'.esc_html__('https://placehold.co/184x135', 'listingpro').'" alt="image">
												</a>';
											}
										?>
									</div>
									<div class="hide-img listingpro-list-thumb">
										<?php
											if ( has_post_thumbnail()) {
												$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'listingpro-blog-grid' );
													if(!empty($image[0])){
														echo "<a href='".get_the_permalink()."' >
																<img alt='image' src='" . $image[0] . "' />
															</a>";
													}else {
														echo '
														<a href="'.get_the_permalink().'" >
															<img src="'.esc_html__('https://placehold.co/90x42', 'listingpro').'" alt="image">
														</a>';
													}	
											}else {
												echo '
												<a href="'.get_the_permalink().'" >
													<img src="'.esc_html__('https://placehold.co/90x42', 'listingpro').'" alt="image">
												</a>';
											}
										?>
									</div>
							   	</div>
								<!--<div class="lp-grid-box-quick">
									<ul class="lp-post-quick-links">
										<li>
											<a href="#" data-post-type="grids" data-post-id="<?php echo esc_attr(get_the_ID()); ?>" data-success-text="<?php echo esc_html__('Saved', 'listingpro') ?>" class="status-btn add-to-fav lp-add-to-fav">
												<i class="<?php echo esc_attr($isfavouriteicon); ?>"></i> <span><?php echo esc_attr($isfavouritetext); ?></span>
											</a>
										</li>
										<li>
											<a class="icon-quick-eye md-trigger qickpopup" data-modal="modal-1<?php echo get_the_ID(); ?>"><i class="fa fa-eye"></i> <?php echo esc_html__('Preview', 'listingpro'); ?></a>
										</li>
									</ul>
								</div>-->
							</div>
								<div class="details">
										<h4 class="lp-h4">
											<a href="<?php echo get_the_permalink(); ?>">
												<?php echo wp_kses_post($CHeckAd); ?>
												<?php echo mb_substr(get_the_title(), 0, 20); ?>..
												<?php echo wp_kses_post($claim); ?>
											</a>
										</h4>
										<?php
											if($lp_review_switch==1){ ?>
										<ul>
											
											<li>
												<?php
													$NumberRating = listingpro_ratings_numbers($post->ID);
													if($NumberRating != 0){
														if($NumberRating <= 1){
															$review = esc_html__('Rating', 'listingpro');
														}else{
															$review = esc_html__('Ratings', 'listingpro');
														}
														echo lp_cal_listing_rate(get_the_ID());											
												?>
														<span>
															<?php echo esc_attr($NumberRating); ?>
															<?php echo esc_attr($review); ?>
														</span>
												<?php		
													}else{
														echo lp_cal_listing_rate(get_the_ID());
													}
												?>
											</li>
											
											<!--<li>
												<?php
													$cats = get_the_terms( get_the_ID(), 'listing-category' );
													if(!empty($cats)){
														foreach ( $cats as $cat ) {
															$category_image = listing_get_tax_meta($cat->term_id,'category','image');
															if(!empty($category_image)){
																echo '<span class="cat-icon"><img class="icon icons8-Food" src="'.$category_image.'" alt="cat-icon"></span>';
															}
															/*$term_link = get_term_link( $cat );
															echo '
															<a href="'.$term_link.'">
																'.$cat->name.'
															</a>';*/
														}
													}
												?>
											</li>-->
										</ul>
										<?php } ?>
									<?php
								$openStatus = listingpro_check_time(get_the_ID());
								$cats = get_the_terms( get_the_ID(), 'location' );
								if(!empty($openStatus) || !empty($cats)){
								?>
										<div class="lp-location">
											<div class="show">
												<?php
													$cats = get_the_terms( get_the_ID(), 'location' );
													if(!empty($cats)){
														echo '<span class="cat-icon">'.listingpro_icons('mapMarkerGrey').'</span>';
														foreach ( $cats as $cat ) {
															$term_link = get_term_link( $cat );
															echo '
															<a href="'.$term_link.'">
																'.$cat->name.'
															</a>';
														}
													}
												?>
											</div>
											<?php if(!empty($gAddress)) { ?>
												<div class="hide">
													<span class="cat-icon">
														<?php echo listingpro_icons('mapMarkerGrey'); ?>
													</span>
													<span class="text gaddress"><?php echo mb_substr($gAddress, 0, 30); ?>...</span>
												</div>
											<?php } ?>
										</div>
								
								<?php }?>
								</div>
							</div>
						</div>
					</div>
					</div>
					