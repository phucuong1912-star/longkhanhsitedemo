<?php
	global $listingpro_options;
	$claimed_section = listing_get_metabox('claimed_section');
	$lp_review_switch = $listingpro_options['lp_review_switch'];
	$claim = '';
	$claimStatus = '';
	$CHeckAd = '';
	$adClass = '';
	$adStatus = apply_filters('lp_get_ad_status', '', get_the_ID());
	if ($adStatus == 'active') {
		$CHeckAd = '<span class="listing-pro">' . esc_html__('Ad', 'listingpro') . '</span>';
		$adClass = 'promoted';
	}
	$deafaultFeatImg = lp_default_featured_image_listing();

	if ($claimed_section == 'claimed') {
		if (is_singular('listing')) {
			$claimStatus = esc_html__('Claimed', 'listingpro');
		}
		$claim = '<span class="verified simptip-position-top simptip-movable" data-tooltip="' . esc_html__('Claimed', 'listingpro') . '"><i class="fa fa-check"></i> ' . $claimStatus . '</span>';
	} elseif ($claimed_section == 'not_claimed') {
		$claim = '';
	}

	$favrt  =   listingpro_is_favourite_new(get_the_ID());

	$menu_check     =   get_post_meta(get_the_ID(), 'lp-listing-menu', true);

	$phone          =   listing_get_metabox('phone');

	$gAddress       =   listing_get_metabox('gAddress');

	$lp_lat         =   listing_get_metabox_by_ID('latitude', get_the_ID());

	$lp_lng         =   listing_get_metabox_by_ID('longitude', get_the_ID());


	$PM_btns_all    =   '';

	$is_phone       =   '';

	$is_menu        =   '';

	$is_gAddress    =   '';



	if (!empty($phone) && !empty($menu_check) && is_array($menu_check) && !empty($gAddress)) {

		$PMG_btns_both   =   'ok';
	}

	if (!empty($phone)) {

		$is_phone   =   'ok';
	}

	if (!empty($menu_check) && is_array($menu_check)) {

		$is_menu    =   'ok';
	}

	if (!empty($gAddress)) {

		$is_gAddress    =   'ok';
	}

	$listing_style = '';
	$listing_style = $listingpro_options['listing_style'];
	if (isset($_GET['list-style']) && !empty($_GET['list-style'])) {
		$listing_style = esc_html($_GET['list-style']);
	}
	if (is_front_page()) {
		$listing_style = 'col-md-4 col-sm-6';
		$postGridnumber = 3;
	} else {
		if ($listing_style == '1') {
			$listing_style = 'col-md-4 col-sm-6';
			$postGridnumber = 3;
		} elseif ($listing_style == '3' && !is_page()) {
			$listing_style = 'col-md-6 col-sm-12';
			$postGridnumber = 2;
		} elseif ($listing_style == '5') {
			$listing_style = 'col-md-12 col-sm-12';
			$postGridnumber = 2;
		} else {
			$listing_style = 'col-md-6 col-sm-6';
			$postGridnumber = 2;
		}
	}
	if (is_page_template('template-favourites.php')) {
		$listing_style = 'col-md-4 col-sm-6';
		$postGridnumber = 3;
	}
	$gAddress = listing_get_metabox('gAddress');
	lp_get_lat_long_from_address($gAddress, get_the_ID());
	$latitude = listing_get_metabox_by_ID('latitude', get_the_ID());
	$longitude = listing_get_metabox_by_ID('longitude', get_the_ID());
	$gAddress = listing_get_metabox('gAddress');
	$phone = listing_get_metabox('phone');
	if (!empty($latitude)) {
		$latitude = str_replace(",", ".", $latitude);
	}
	if (!empty($longitude)) {
		$longitude = str_replace(",", ".", $longitude);
	}
	$lp_default_map_pin = $listingpro_options['lp_map_pin']['url'];
	if (empty($lp_default_map_pin)) {
		$lp_default_map_pin = get_template_directory() . '/assets/images/pins/pin.png';
	}
	
	$featureImg = '';

	if (has_post_thumbnail()) {

		$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid');

		if (!empty($image[0])) {
			$featureImg = $image[0];
		} elseif (!empty($deafaultFeatImg)) {

			$featureImg = $deafaultFeatImg;
		} else {
			$featureImg = 'https://placehold.co/372x240';
		}

		//$featureImg = $image[0];
	} else if ($listingpro_options['lp_def_featured_image_from_gallery'] == 'enable') {

		//  echo "yes";
		$IDs = get_post_meta(get_the_ID(), 'gallery_image_ids', true);

		$IDs = explode(',', $IDs);

		if (is_array($IDs)) {
			shuffle($IDs);

			$img_url = wp_get_attachment_image_src($IDs[0], 'listingpro-blog-grid');

			$imgurl = $img_url[0];
			if (!empty($imgurl)) {
				$featureImg = $imgurl;
			} elseif (!empty($deafaultFeatImg)) {

				$featureImg = $deafaultFeatImg;
			} else {
				$featureImg = 'https://placehold.co/372x240';
			}
		}
	} elseif (!empty($deafaultFeatImg)) {

		$featureImg = $deafaultFeatImg;
	} else {

		$featureImg = 'https://placehold.co/372x240';
	}

	?><div data-feaimg="<?php echo esc_url($featureImg); ?>" class="<?php echo esc_attr($listing_style); ?> <?php echo esc_attr($adClass); ?> lp-grid-box-contianer grid_view_s1 grid_view2 card1 lp-grid-box-contianer1" data-title="<?php echo get_the_title(); ?>" data-postid="<?php echo get_the_ID(); ?>" data-lattitue="<?php echo esc_attr($latitude); ?>" data-longitute="<?php echo esc_attr($longitude); ?>" data-posturl="<?php echo get_the_permalink(); ?>" data-lppinurl="<?php echo esc_url($lp_default_map_pin); ?>">

		<div class="classic-view-grid-container">
			<div class="classic-view-grid-thumbnail">
				<?php
				echo "<a  class='classic-thumbnail-url' href=' " . get_the_permalink() . "' >
												<img alt='image' src='" . $featureImg . "' />
											</a>";
				?>
				<div class="hide listingpro-list-thumb lp-grid-box-thumb">
				<?php
				echo "<a  class='classic-thumbnail-url' href=' " . get_the_permalink() . "' >
												<img alt='image' src='" . $featureImg . "' />
											</a>";
				?>
				</div>
				<div class="classic-view-grid-saved-options">
					<ul class="lp-post-quick-links">

						<li>


							<a href="#" data-post-id="<?php echo get_the_ID(); ?>" data-post-type="list" class="lp-listing-favrt <?php if ($favrt == 'yes') {
																																		echo 'remove-fav-v2';
																																	} else {
																																		echo 'add-to-fav-v2';
																																	} ?>">



								<i class="fa fa-heart<?php if ($favrt != 'yes') {
															echo '-o';
														} ?>" aria-hidden="true"></i>



							</a>
						</li>
						<li>
							<a class="icon-quick-eye md-trigger qickpopup" data-mappin="<?php echo esc_url($lp_default_map_pin); ?>" data-modal="modal-126"><i class="fa fa-eye"></i></a>
						</li>
					</ul>
				</div>
				
			</div>
			<div class="classic-view-grids-content-area">
				<div class="classic-view-grid-content-area">
					<div class="lp-grid-box-left">
						<?php echo wp_kses_post($CHeckAd);
						if (!empty(wp_kses_post($CHeckAd)) && isset($CHeckAd)) {
							$class_ad = 'ad';
						} else {
							$class_ad = '';
						}
						?>
						<h4 class="lp-h4 <?php echo $class_ad; ?>">
							<a href="<?php echo get_the_permalink(); ?>">
								<?php echo mb_substr(get_the_title(), 0, 34) ?>
								<?php echo wp_kses_post($claim); ?>
							</a>
							
						</h4>
						<div class="lp-listing-content-grid">
							<ul>
								<?php
								if ($lp_review_switch == 1) { ?>
									<li class="lp-classic-reviews">
										<?php
										$NumberRating = listingpro_ratings_numbers($post->ID);
										if ($NumberRating != 0) {


											$rating = get_post_meta(get_the_ID(), 'listing_rate', true);
											echo '<span>' . $rating . '</span>';
											$rating_num_bg = '';

											$rating_num_clr = '';



											if ($rating < 3) {
												$rating_num_bg = 'num-level1';
												$rating_num_clr = 'level1';
											}
											if ($rating < 4) {
												$rating_num_bg = 'num-level2';
												$rating_num_clr = 'level2';
											}
											if ($rating < 5) {
												$rating_num_bg = 'num-level3';
												$rating_num_clr = 'level3';
											}
											if ($rating >= 5) {
												$rating_num_bg = 'num-level4';
												$rating_num_clr = 'level4';
											}

										?>



											<div class="lp-rating-stars-outers lp-listing-stars">

												<span class="lp-star-box <?php if ($rating >= 1) {
																				echo 'filled' . ' ' . $rating_num_clr;
																			} ?>"><i class="fa fa-star" aria-hidden="true"></i></span>



												<span class="lp-star-box <?php if ($rating >= 2) {
																				echo 'filled' . ' ' . $rating_num_clr;
																			} ?>"><i class="fa fa-star" aria-hidden="true"></i></span>



												<span class="lp-star-box <?php if ($rating >= 3) {
																				echo 'filled' . ' ' . $rating_num_clr;
																			} ?>"><i class="fa fa-star" aria-hidden="true"></i></span>



												<span class="lp-star-box <?php if ($rating >= 4) {
																				echo 'filled' . ' ' . $rating_num_clr;
																			} ?>"><i class="fa fa-star" aria-hidden="true"></i></span>



												<span class="lp-star-box <?php if ($rating >= 5) {
																				echo 'filled' . ' ' . $rating_num_clr;
																			} ?>"><i class="fa fa-star" aria-hidden="true"></i></span>

											</div>
											<span>
												(<?php echo esc_attr($NumberRating); ?>)
											</span>
										<?php
										}
										?>
									</li>
								<?php } ?>
								<li class="classic-category-loop-main">
									<div class="classic-category-loop">
										<?php
										$cats = get_the_terms(get_the_ID(), 'listing-category');
										if (!empty($cats)) {
											$catCount = 1;
											foreach ($cats as $cat) {
												if ($catCount == 1) {
													$category_image = listing_get_tax_meta($cat->term_id, 'category', 'image');
													if (!empty($category_image)) {
														if( hasFontAwesomeIconClass($category_image) ){
															echo '<span class="cat-icon"><i class="icon icons-search-cat '.$category_image.'"></i></span>'; 
														}else{
															echo '<span class="cat-icon"><img class="icon icons8-Food" src="' . $category_image . '" alt="cat-icon"></span>';
														}
													}
													$term_link = get_term_link($cat);
													echo '
													<a href="' . $term_link . '">
														' . $cat->name . '
													</a>';
													$catCount++;
												}
											}
										}
										?>
									</div>
									<?php
									if(listingpro_price_dynesty_text($post->ID)){
										echo '<div class="classic-category-loop-price">
										. '.listingpro_price_dynesty_text($post->ID) .'
									</div>';
									}
									
									?>
									
								</li>
							</ul>
						</div>
					</div>

				</div>
				<?php
				$openStatus = listingpro_check_time(get_the_ID());
				$cats = get_the_terms(get_the_ID(), 'location');
				if (!empty($openStatus) || !empty($cats)) {
				?>
					<div class="lp-grid-box-bottom content">
						<div class="pull-div">
							<div class="show">
								<?php
								$countlocs = 1;
								$cats = get_the_terms(get_the_ID(), 'location');
								if (!empty($cats)) {
									echo '<i class="fa fa-map-marker" aria-hidden="true"></i>';
									foreach ($cats as $cat) {
										if ($countlocs == 1) {
											$term_link = get_term_link($cat);
											echo '
												<a href="' . $term_link . '">' . $cat->name . '</a>';
										}
										$countlocs++;
									}
								}

								?>
							</div>
							<?php if (!empty($gAddress)) { ?>
								<div class="hide">
									<span class="cat-icon">
										<i class="fa fa-map-marker" aria-hidden="true"></i>
									</span>
									<span class="text gaddress"><?php echo mb_substr($gAddress, 0, 30); ?>...</span>
								</div>
							<?php } ?>
						</div>
						<?php
						$openStatus = listingpro_check_time(get_the_ID());
						if (!empty($openStatus)) {
							echo '
								<div class="pull-div">
									<a class="status-btn">';
							echo wp_kses_post($openStatus);
							echo ' 
									</a>
								</div>';
						}
						?>
					</div>

				<?php }
				if ((!empty($phone)) || (!empty($gAddress))) :

				?>

					<div class="lp-new-grid-bottom-button">

						<ul class="clearfix">



							<?php

							if ($is_phone == 'ok') :

							?>

								<li onclick="myFuction(this)" class="show-number-wrap hereIam" style="<?php //if( $is_menu == '' ){ echo 'width:100%;'; } 
																										?>">

									<p><i class="fa fa-phone-square" aria-hidden="true"></i><span class="show-number"><?php esc_html_e('call Now', 'listingpro'); ?></span><a href="tel:<?php echo esc_attr($phone); ?>" class="grind-number"><?php echo esc_attr($phone); ?></a></p>

								</li>

							<?php endif; ?>

							<?php

							if ($is_gAddress == 'ok') :

							?>

								<li style="<?php //if( $is_phone == '' ){ echo 'width:50%;'; } 
											?>">

									<a href="" data-lid="<?php echo get_the_ID(); ?>" data-lat="<?php echo esc_attr($lp_lat); ?>" data-lng="<?php echo esc_attr($lp_lng); ?>" class="show-loop-map-popup"><i class="fa-sharp fa-solid fa-diamond-turn-right"></i><?php esc_html_e('Get Direction', 'listingpro'); ?></a>

								</li>

							<?php endif; ?>


						</ul>

					</div>

				<?php endif; ?>
			</div>
		</div>



		<?php if (is_page_template('template-favourites.php')) { ?>
			<div class="remove-fav md-close" data-post-id="<?php echo get_the_ID(); ?>">
				<i class="fa fa-close"></i>
			</div>
		<?php } ?>

	</div>