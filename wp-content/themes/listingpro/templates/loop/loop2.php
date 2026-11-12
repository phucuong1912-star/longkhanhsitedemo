<?php
			$output = null;
			
			global $listingpro_options;
			$default_feature_img= lp_theme_option_url('lp_def_featured_image');
			$lp_review_switch = $listingpro_options['lp_review_switch'];

			if(!isset($postGridCount)){
				$postGridCount = '0';
			}
			global $postGridCount;			
			$postGridCount++;
			
			$listing_style = '';
				$listing_style = $listingpro_options['listing_style'];
				if(isset($_GET['list-style']) && !empty($_GET['list-style'])){
					$listing_style = esc_html($_GET['list-style']);
				}
				if(is_front_page()){
					$listing_style = 'col-md-4 col-sm-6';
					$postGridnumber = 3;
				}else{
					if($listing_style == '1'){
						$listing_style = 'col-md-4 col-sm-6';
						$postGridnumber = 3;
					}elseif($listing_style == '3' && !is_page()){
						$listing_style = 'col-md-6 col-sm-12';
						$postGridnumber = 2;
					}else{
						$listing_style = 'col-md-4 col-sm-6';
						$postGridnumber =3;
					}
				}
				if(is_page_template('template-favourites.php')){
					$listing_style = 'col-md-4 col-sm-6';
					$postGridnumber =3;
				}
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
					$claim = '<span class="verified simptip-position-top simptip-movable" data-tooltip="'. esc_html__('Claimed', 'listingpro').'"><i class="fa fa-check"></i> '.$claimStatus.'</span>';

				}elseif($claimed_section == 'not_claimed') {
					$claim = '';

				}
				$listing_layout = $listingpro_options['listing_views'];
			
					?>							
					<div class="<?php echo esc_attr($listing_style); ?> <?php echo esc_attr($adClass); ?> lp-grid-box-contianer grid_view_s2 grid_view2 card1 lp-grid-box-contianer1 listing-grid-view2-outer" data-title="<?php echo get_the_title(); ?>" data-postid="<?php echo get_the_ID(); ?>"   data-lattitue="<?php echo esc_attr($latitude); ?>" data-longitute="<?php echo esc_attr($longitude); ?>" data-posturl="<?php echo get_the_permalink(); ?>">
						<?php if(is_page_template('template-favourites.php')){ ?>
							<div class="remove-fav md-close" data-post-id="<?php echo get_the_ID(); ?>">
								<i class="fa fa-close"></i>
							</div>
						<?php } ?>
						<div class="lp-grid-box">
							<div class="lp-grid-box-thumb-container" >
								<div class="lp-grid-box-thumb">
									<div class="show-img">
										<?php
											if ( has_post_thumbnail()) {
												$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'listingpro-blog-grid2' );
													if(!empty($image[0])){
														echo "<a href='".get_the_permalink()."' >
																<img alt='image' src='" . $image[0] . "' />
															</a>";
													}else {
														echo '
														<a href="'.get_the_permalink().'" >
															<img src="'.esc_html__('https://placehold.co/372x400', 'listingpro').'" alt="image">
														</a>';
													}	
											}elseif(!empty($default_feature_img)){
												echo '
												<a href="'.get_the_permalink().'" >
													<img src="'.$default_feature_img.'" alt="image">
												</a>';
											}else {
												echo '
												<a href="'.get_the_permalink().'" >
													<img src="'.esc_html__('https://placehold.co/372x400', 'listingpro').'" alt="image">
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
															<img src="'.esc_html__('https://placehold.co/372x240', 'listingpro').'" alt="image">
														</a>';
													}	
											}else {
												echo '
												<a href="'.get_the_permalink().'" >
													<img src="'.esc_html__('https://placehold.co/372x240', 'listingpro').'" alt="image">
												</a>';
											}
										?>
									</div>
							   	</div>
								<div class="lp-grid-box-quick">
									<ul class="lp-post-quick-links clearfix">
										<?php
											$favrt  =   listingpro_is_favourite_new(get_the_ID());
										 ?>
										<li class="pull-left">
											<a href="#" data-post-type="grids" data-post-id="<?php echo esc_attr(get_the_ID()); ?>" data-success-text="<?php echo esc_html__('Saved', 'listingpro') ?>" class="status-btn <?php if($favrt == 'yes'){echo 'remove-fav';}else{echo 'add-to-fav';} ?> lp-add-to-fav">
												<i class="<?php echo esc_attr($isfavouriteicon); ?>"></i> <span><?php echo wp_kses_post($isfavouritetext); ?></span>
											</a>
										</li>
										
									</ul>
								</div>
							</div>
							<div class="lp-grid-desc-container lp-border clearfix">
								<div class="lp-grid-box-description ">
									<div class="lp-grid-box-left pull-left">
										<h4 class="lp-h4">
											<a href="<?php echo get_the_permalink(); ?>">
												<?php echo wp_kses_post($CHeckAd); ?>
												<?php echo mb_substr(get_the_title(), 0, 40); ?>
												<?php echo wp_kses_post($claim); ?>
											</a>
										</h4>
										<ul>
											<?php
											if($lp_review_switch==1){ ?>
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
											<?php } ?>
											
											<li>
												<?php
													$cats = get_the_terms( get_the_ID(), 'listing-category' );
													if(!empty($cats)){
														foreach ( $cats as $cat ) {
															$category_image = listing_get_tax_meta($cat->term_id,'category','image');
															if (!empty($category_image)) {
																if( hasFontAwesomeIconClass($category_image) ){
																	echo '<span class="cat-icon"><i class="icon icons-search-cat '.$category_image.'"></i></span>'; 
																}else{
																	echo '<span class="cat-icon"><img class="icon icons8-Food" src="' . $category_image . '" alt="cat-icon"></span>';
																}
															}
															$term_link = get_term_link( $cat );
															echo '
															<a href="'.$term_link.'">
																'.$cat->name.'
															</a>';
														}
													}
												?>
											</li>
										</ul>
										
									</div>
									<div class="lp-grid-box-right pull-right">
									</div>
								</div>
								<?php
								$openStatus = listingpro_check_time(get_the_ID());
								$cats = get_the_terms( get_the_ID(), 'location' );
								if(!empty($openStatus) || !empty($cats)){
								?>
									<div class="lp-grid-box-bottom">
										<div class="pull-left">
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
										<?php
											$openStatus = listingpro_check_time(get_the_ID());
											if(!empty($openStatus)){
												echo '
												<div class="pull-right">
													<a class="status-btn">';
														echo wp_kses_post($openStatus);
														echo ' 
													</a>
												</div>';
											}
										?>
										<div class="clearfix"></div>
									</div>
								
								<?php } ?>
							</div>
						</div>
					</div>
					
					<?php get_template_part('templates/preview'); ?>
								 
				<?php 
					if($postGridCount%$postGridnumber == 0){
						echo '<div class="clearfix"></div>';
					}
?>						