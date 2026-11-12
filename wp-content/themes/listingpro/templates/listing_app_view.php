<?php
/* The loop starts here. */
global $listingpro_options;
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post(); 
		setPostViews(get_the_ID());
		$claimed_section = listing_get_metabox('claimed_section');
		$tagline_text = listing_get_metabox('tagline_text');
		
		$plan_id = listing_get_metabox_by_ID('Plan_id',get_the_ID());
		if(!empty($plan_id)){
			$plan_id = $plan_id;
		}else{
			$plan_id = 'none';
		}
		
		$contact_show = get_post_meta( $plan_id, 'contact_show', true );
		$map_show = get_post_meta( $plan_id, 'map_show', true );
		$video_show = get_post_meta( $plan_id, 'video_show', true );
		$gallery_show = get_post_meta( $plan_id, 'gallery_show', true );
		$tagline_show = get_post_meta( $plan_id, 'listingproc_tagline', true );
		$location_show = get_post_meta( $plan_id, 'listingproc_location', true );
		$website_show = get_post_meta( $plan_id, 'listingproc_website', true );
		$social_show = get_post_meta( $plan_id, 'listingproc_social', true );
		$faqs_show = get_post_meta( $plan_id, 'listingproc_faq', true );
		$price_show = get_post_meta( $plan_id, 'listingproc_price', true );
		$tags_show = get_post_meta( $plan_id, 'listingproc_tag_key', true );
		$hours_show = get_post_meta( $plan_id, 'listingproc_bhours', true );
		
		if($plan_id=="none"){
			$contact_show = 'true';
			$map_show = 'true';
			$video_show = 'true';
			$gallery_show = 'true';
			$tagline_show = 'true';
			$location_show = 'true';
			$website_show = 'true';
			$social_show = 'true';
			$faqs_show = 'true';
			$price_show = 'true';
			$tags_show = 'true';
			$hours_show = 'true';
		}

		$claim = '';
		if($claimed_section == 'claimed') {
			$claim = '<span class="claimed"><i class="fa fa-check"></i> '. esc_html__('Claimed', 'listingpro').'</span>';

		}elseif($claimed_section == 'not_claimed') {
			$claim = '';

		}
		global $post;
		
		$resurva_url = get_post_meta($post->ID, 'resurva_url', true);
		$menuOption = false;
		$menuTitle = '';
		$menuImg = '';
		$menuMeta = get_post_meta($post->ID, 'menu_listing', true);
		if(!empty($menuMeta)){
			$menuTitle = $menuMeta['menu-title'];
			$menuImg = $menuMeta['menu-img'];
			$menuOption = true;
		}
		
		$timekit = false;
		$timekit_booking = get_post_meta($post->ID, 'timekit_booking', true);
		
		if(!empty($timekit_booking)){
			$timekitAPP = $timekit_booking['timekit-app'];
			$timekitAPI = $timekit_booking['timekit-api-token'];
			$timekitListing = $timekit_booking['listing_id'];
			$timekitName = $timekit_booking['timekit_name'];
			$timekitEmail = $timekit_booking['timekit_email'];
			$timekit = true;
		}
		
		
		
		/* get user meta */
		$user_id=$post->post_author;
		$user_facebook = '';
		$user_linkedin = '';
		$user_clinkedin = '';
		$user_facebook = '';
		$user_instagram = '';
		$user_twitter = '';
		$user_pinterest = '';
		$user_cpinterest = '';

		$user_facebook = get_the_author_meta('facebook', $user_id);
		$user_linkedin = get_the_author_meta('linkedin', $user_id);
		$user_instagram = get_the_author_meta('instagram', $user_id);
		$user_twitter = get_the_author_meta('twitter', $user_id);
		$user_pinterest = get_the_author_meta('pinterest', $user_id);
		/* get user meta */
		?>
        <style>

        </style>
        <script>
            jQuery(document).ready(function(e){
                jQuery('.app-view-gallery').slick();
            })
        </script>
		<!--==================================Section Open=================================-->
		<section class="aliceblue listing-app-view listing-second-view">
            <?php
            $IDs = get_post_meta( $post->ID, 'gallery_image_ids', true );
            if ( !empty( $IDs ) )
            {
                echo '<div class="app-view-gallery">';
                $imgIDs = explode(',',$IDs);
                foreach( $imgIDs as $imgID )
                {
                    $imgurl = wp_get_attachment_image_src( $imgID, 'listingpro-listing-gallery');
                    $imgFull = wp_get_attachment_image_src( $imgID, 'full');
                    echo '<div class="slide-img">';
                    echo '  <img alt="image" src="'. $imgurl[0] .'">';
                    echo '</div>';
                }

                echo '</div>';
            }
            ?>

			<div class="post-meta-info">
				<div class="container">
					<div class="row">
						<div class="col-md-8 col-sm-8 col-xs-12">
							<div class="post-meta-left-box">
								<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
								<h1><?php the_title(); ?> <?php echo esc_url($claim); ?></h1>
								<?php if(!empty($tagline_text)) {
											if($tagline_show=="true"){?>
											<p><?php echo esc_attr($tagline_text); ?></p>
									<?php } ?>
								<?php } ?>
                                <span class="rating-section">
									<?php
                                    $NumberRating = listingpro_ratings_numbers($post->ID);
                                    if($NumberRating != 0){
                                        echo lp_cal_listing_rate(get_the_ID());
                                        ?>
                                        <span>
												<small><?php echo wp_kses_post($NumberRating); ?></small>
                                            <?php echo esc_html__('Ratings', 'listingpro'); ?>
											</span>
                                        <?php
                                    }else{
                                        echo lp_cal_listing_rate(get_the_ID());
                                    }
                                    ?>
								</span>
							</div>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12">
							<div class="post-meta-right-box text-right clearfix margin-top-20">
								<ul class="post-stat">
									<?php
                                        $favrt  =   listingpro_is_favourite_new(get_the_ID());
                                     ?>
									<li id="fav-container">
										<a class="email-address <?php if($favrt == 'yes'){echo 'remove-fav';}else{echo 'add-to-fav';} ?>" data-post-type="detail" href="" data-post-id="<?php echo get_the_ID(); ?>" data-success-text="<?php echo esc_html__('Saved', 'listingpro') ?>">
											<i class="fa <?php echo listingpro_is_favourite(get_the_ID(),$onlyicon=true); ?>"></i>
											<span class="email-icon">
												<?php echo listingpro_is_favourite(get_the_ID(),$onlyicon=false); ?>
											</span>
											 										
										</a>
									</li>
									<li class="reviews sbutton">
										<?php listingpro_sharing(); ?>
									</li>
                                    <li>
                                    <?php
                                        if(empty($resurva_url)){
                                            if (class_exists('ListingReviews'))
                                            {
                                                $allowedReviews = $listingpro_options['lp_review_switch'];
                                                if(!empty($allowedReviews) && $allowedReviews=="1")
                                                {
                                                    if(get_post_status($post->ID)=="publish")
                                                    {
                                                        ?>
                                                        <a href="#reply-title" id="clicktoreview">
                                                            <i class="fa fa-star"></i>
                                                            <?php echo esc_html__('Submit Review', 'listingpro'); ?>
                                                        </a>
                                                        <?php
                                                    }
                                                }
                                            }
                                        }
                                    ?>
                                    </li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="content-white-area">
				<div class="container single-inner-container single_listing" >
					<div class="row">
                        <?php if(!empty($timekit_booking) && $timekit == true){ ?>
                            <div class="widget-box">
                                <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/booking.js"></script>
                                <div id="bookingjs1">
                                    <script type="text/javascript">
                                        var widget1 = new TimekitBooking();
                                        widget1.init({
                                            targetEl: '#bookingjs1',
                                            name:     '<?php echo wp_kses_post($timekitName); ?>',
                                            email:    '<?php echo wp_kses_post($timekitEmail); ?>',
                                            apiToken: '<?php echo wp_kses_post($timekitAPI); ?>',
                                            calendar: '22f86f0c-ee80-470c-95e8-dadd9d05edd2',
                                            timekitConfig: {
                                                app: '<?php echo wp_kses_post($timekitAPP); ?>'
                                            }

                                        });
                                    </script>
                                </div>
                            </div>
                        <?php } ?>
                        <?php
                        $buisness_hours = listing_get_metabox('business_hours');
                        if(!empty($buisness_hours)){
                            if($hours_show=="true"){
                                ?>
                                <div class="widget-box">
                                    <?php get_template_part( 'include/timings' ); ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <?php if(!empty($resurva_url)){ ?>
                            <a href="" class="secondary-btn make-reservation">
                                <i class="fa fa-calendar-check-o"></i>
                                <?php echo esc_html__('Book Now', 'listingpro'); ?>
                            </a>
                            <div class="ifram-reservation">
                                <div class="inner-reservations">
                                    <a href="#" class="close-btn"><i class="fa fa-times"></i></a>
                                    <iframe src="<?php echo esc_url($resurva_url); ?>" name="resurva-frame" frameborder="0"></iframe>
                                </div>
                            </div>
                        <?php }
                        ?>
						<div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="widget-box map-area">
                                <?php
                                $latitude = listing_get_metabox('latitude');
                                $longitude = listing_get_metabox('longitude');
                                if(!empty($latitude) && !empty($longitude)){
                                    if($map_show=="true"){
                                        ?>
                                        <div class="widget-bg-color post-author-box lp-border-radius-5">
                                            <div class="widget-header margin-bottom-25 hideonmobile">
                                                <ul class="post-stat">
                                                    <li>
                                                        <a class="md-trigger parimary-link singlebigmaptrigger" data-lat="<?php echo esc_attr($latitude); ?>" data-lan="<?php echo esc_attr($longitude); ?>" data-modal="modal-4" >
                                                            <!-- <span class="phone-icon">
																		Marker icon by Icons8
																		<?php echo listingpro_icons('mapMarker'); ?>
																	</span>
																	<span class="phone-number ">
																		<?php echo esc_html__('View Large Map', 'listingpro'); ?>
																	</span> -->
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <?php
                                            $lp_map_pin = $listingpro_options['lp_map_pin']['url'];
                                            ?>
                                            <div class="widget-content ">
                                                <div class="widget-map pos-relative">
                                                    <div id="singlepostmap" class="singlemap" data-pinicon="<?php echo esc_attr($lp_map_pin); ?>"></div>
                                                    <div class="get-directions">
                                                        <a href="https://www.google.com/maps?daddr=<?php echo esc_attr($latitude); ?>,<?php echo esc_attr($longitude); ?>" target="_blank" >
																	<span class="phone-icon">
																		<i class="fa fa-map-o"></i>
																	</span>
                                                            <span class="phone-number ">
																		<?php echo esc_html__('Get Directions', 'listingpro'); ?>
																	</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- ../widget-box  -->
                                    <?php } ?>
                                <?php } ?>
                                <div class="listing-detail-infos margin-top-20 clearfix">
                                    <ul class="list-style-none list-st-img clearfix">
                                        <?php
                                        $gAddress = listing_get_metabox('gAddress');
                                        $phone = listing_get_metabox('phone');
                                        $website = listing_get_metabox('website');
                                        //if(empty($facebook) && empty($twitter) && empty($linkedin)){}else{
                                        ?>
                                        <?php if(!empty($gAddress)) {
                                            if($location_show=="true"){?>
                                                <li>
                                                    <a>
																<span class="cat-icon">
																	<?php echo listingpro_icons('mapMarkerGrey'); ?>
                                                                    <!-- <i class="fa fa-map-marker"></i> -->
																</span>
                                                        <span>
																	<?php echo wp_kses_post($gAddress) ?>
																</span>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if(!empty($phone)) { ?>
                                            <?php if($contact_show=="true"){ ?>
                                                <li class="lp-listing-phone">
                                                    <a data-lpID="<?php echo get_the_ID(); ?>" href="tel:<?php echo esc_attr($phone); ?>">
																<span class="cat-icon">
																	<?php echo listingpro_icons('phone'); ?>
                                                                    <!-- <i class="fa fa-mobile"></i> -->
																</span>
                                                        <span>
																	<?php echo esc_html($phone); ?>
																</span>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if(!empty($website)) {
                                            if($website_show=="true"){?>
                                                <li class="lp-user-web">
                                                    <a data-lpID="<?php echo get_the_ID(); ?>" href="<?php echo esc_url($website); ?>" target="_blank">
																	<span class="cat-icon">
																		<?php echo listingpro_icons('globe'); ?>
                                                                        <!-- <i class="fa fa-globe"></i> -->
																	</span>
                                                        <span><?php echo esc_url($website); ?></span>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php //} ?>
                                    </ul>
                                    <?php
                                    $facebook = listing_get_metabox('facebook');
                                    $twitter = listing_get_metabox('twitter');
                                    $linkedin = listing_get_metabox('linkedin');
                                    $youtube = listing_get_metabox('youtube');
                                    $instagram = listing_get_metabox('instagram');
                                    if($social_show=="true"){
                                        if(empty($facebook) && empty($twitter) && empty($linkedin) && empty($youtube) && empty($instagram)){}else{
                                            ?>
                                            <div class="widget-box widget-social">
                                                <div class="widget-content clearfix">
                                                    <ul class="list-style-none list-st-img">
                                                        <?php if(!empty($facebook)){ ?>
                                                            <li class="lp-fb">
                                                                <a href="<?php echo esc_url($facebook); ?>" class="padding-left-0" target="_blank">
                                                                    <!-- <i class="fa fa-facebook"></i> -->
                                                                    <?php echo listingpro_icons('fb'); ?>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if(!empty($twitter)){ ?>
                                                            <li class="lp-tw">
                                                                <a href="<?php echo esc_url($twitter); ?>" class="padding-left-0" target="_blank">
                                                                    <!-- <i class="fa fa-twitter"></i> -->
                                                                    <?php echo listingpro_icons('tw'); ?>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if(!empty($linkedin)){ ?>
                                                            <li  class="lp-li">
                                                                <a href="<?php echo esc_url($linkedin); ?>" class="padding-left-0" target="_blank">
                                                                    <!-- <i class="fa fa-linkedin"></i> -->
                                                                    <?php echo listingpro_icons('lnk'); ?>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if(!empty($youtube)){ ?>
                                                            <li  class="lp-li">
                                                                <a href="<?php echo esc_url($youtube); ?>#" class="padding-left-0" target="_blank">
                                                                    <!-- <i class="fa fa-linkedin"></i> -->
                                                                    <?php echo listingpro_icons('yt'); ?>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if(!empty($instagram)){ ?>
                                                            <li  class="lp-li">
                                                                <a href="<?php echo esc_url($instagram); ?>#" class="padding-left-0" target="_blank">
                                                                    <!-- <i class="fa fa-linkedin"></i> -->
                                                                    <?php echo listingpro_icons('insta'); ?>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            </div><!-- ../widget-box  -->
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php
                            $claimed_section = listing_get_metabox('claimed_section');
                            $priceRange = listing_get_metabox_by_ID('price_status', get_the_ID());
                            $listingpTo = listing_get_metabox('list_price_to');
                            $listingprice = listing_get_metabox_by_ID('list_price', get_the_ID());
                            $showClaim = true;
                            if(isset($listingpro_options['lp_listing_claim_switch'])){
                                if($listingpro_options['lp_listing_claim_switch']==1){
                                    $showClaim = true;
                                }else{
                                    $showClaim = false;
                                }
                            }
                            else{
                                $showClaim = false;
                            }
                            $listingpricestatus = listing_get_metabox_by_ID('price_status', get_the_ID());

                            ?>
                            <?php if( (!empty($menuMeta) && $menuOption == true ) || !empty($listingpTo) || !empty($listingprice) ||  ($showClaim==true && $claimed_section == 'not_claimed') || $listingpricestatus!="notsay" ) { ?>
                                <div class="widget-box listing-price">
                                    <?php
                                    if(!empty($menuMeta) && $menuOption == true){
                                        ?>
                                        <div class="menu-hotel">
                                            <a href="#" class="open-modal">
                                                <?php echo listingpro_icons('resMenu'); ?>
                                                <span>
														<?php if(!empty($menuTitle)){ echo esc_attr($menuTitle); }else{ echo esc_html__('See Full Menu','listingpro'); } ?>
													</span>
                                            </a>
                                            <div class="hotel-menu" style="display: none;">
                                                <div class="inner-menu">
                                                    <a href="#" class="close-menu-popup"><i class="fa fa-times"></i></a>
                                                    <img src="<?php echo esc_url($menuImg); ?>" alt="image">
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <div class="price-area">

                                        <?php
                                        if($price_show=="true"){
                                            echo listingpro_price_dynesty(get_the_ID());
                                        }
                                        ?>
                                        <?php get_template_part('templates/single-list/claimed' ); ?>

                                    </div>
                                    <?php get_template_part('templates/single-list/claim-form' ); ?>
                                </div>
                            <?php } ?>
                            <?php echo listing_all_extra_fields($post->ID); ?>
                            <div class="listing-tabs app-view">
                                <?php
                                $listingContent = get_the_content();
                                $faqs = listing_get_metabox('faqs');
                                $video = listing_get_metabox('video');
                                ?>
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <?php if( $listingContent != '' ): ?><li role="presentation" class="active"><a href="#listing-detail" aria-controls="listing-detail" role="tab" data-toggle="tab"><?php echo esc_html__('Detail', 'listingpro'); ?></a></li> <?php endif; ?>
                                    <?php if( $faqs_show== "true" && !empty($faqs) && count($faqs)>0 ): ?><li role="presentation"><a href="#listing-faq" aria-controls="listing-faq" role="tab" data-toggle="tab"><?php echo esc_html__("FAQ's", 'listingpro'); ?></a></li> <?php endif; ?>
                                    <?php if( !empty( $video ) && $video_show == 'true' ): ?> <li role="presentation"><a href="#listing-video" aria-controls="listing-video" role="tab" data-toggle="tab"><?php echo esc_html__('Video', 'listingpro'); ?></a></li><?php endif; ?>

                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <?php if( $listingContent != '' ): ?>
                                        <div role="tabpanel" class="tab-pane active" id="listing-detail">
                                            <div class="post-detail-content">
                                                <?php the_content(); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($faqs_show== "true" && !empty($faqs) && count($faqs)>0 ): ?>
                                        <div role="tabpanel" class="tab-pane" id="listing-faq">
                                            <?php
                                            $faq = $faqs['faq'];
                                            $faqans = $faqs['faqans'];
                                            if( !empty($faq[1])){
                                                ?>
                                                <div class="post-row faq-section padding-top-10 clearfix">
                                                    <!-- <div class="post-row-header clearfix margin-bottom-15">
                                                <h3><?php echo esc_html__('Quick questions', 'listingpro'); ?></h3>
                                            </div> -->
                                                    <div class="post-row-accordion">
                                                        <div id="accordion">
                                                            <?php for ($i = 1; $i <= (count($faq)); $i++) { ?>
                                                                <?php if( !empty($faq[$i])) { ?>
                                                                    <h5>
                                                                        <span class="question-icon"><?php echo esc_html__('Q', 'listingpro'); ?></span>
                                                                        <span class="accordion-title"><?php echo esc_html($faq[$i]); ?></span>
                                                                    </h5>
                                                                    <div>
                                                                        <p>
                                                                            <?php //echo do_shortcode($faqans[$i]); ?>
                                                                            <?php echo nl2br(do_shortcode($faqans[$i]), false); ?>
                                                                        </p>
                                                                    </div><!-- accordion tab -->
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if( !empty( $video ) && $video_show == 'true' ): ?>
                                    <div role="tabpanel" class="tab-pane" id="listing-video">
                                        <?php if( !empty( $video ) && $video_show == 'true' ): ?>
                                            <?php if(wp_oembed_get( $video )){?>
                                                <?php echo wp_oembed_get($video); ?>
                                            <?php }else{ ?>
                                                <?php echo wp_kses_post($video); ?>
                                            <?php } ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

							<?php 
							$tags = get_the_terms( $post->ID ,'features');
							if(!empty($tags)){ 
								if($tags_show=="true"){?>
									<div class="post-row padding-top-20">
										<!-- <div class="post-row-header clearfix margin-bottom-15"><h3><?php echo esc_html__('Features', 'listingpro'); ?></h3></div> -->
										<ul class="features list-style-none clearfix">
											<?php 
												foreach($tags as $tag) {
													$icon = listingpro_get_term_meta( $tag->term_id ,'lp_features_icon');
													?>								
												<li>
													<a href="<?php echo get_term_link($tag); ?>" class="parimary-link">
														<span class="tick-icon">
															<?php if(!empty($icon)) { ?>
																<i class="fa <?php echo esc_attr($icon); ?>"></i>
															<?php }else { ?>
																<i class="fa fa-check"></i>
															<?php } ?>
														</span>
														<?php echo esc_html($tag->name); ?>
													</a>
												</li>
											<?php } ?>
										</ul>
									</div>
								<?php } ?>
							<?php } ?>

							<div id="submitreview">
								<?php 
									//getting old reviews
									listingpro_get_all_reviews_app_view($post->ID);
								?>
							</div>
							<?php
								//comments_template();
								
								$allowedReviews = $listingpro_options['lp_review_switch'];
								if(!empty($allowedReviews) && $allowedReviews=="1"){
									if(get_post_status($post->ID)=="publish"){
										listingpro_get_reviews_form($post->ID);
									}
								}
								
							?>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12">
							<div class="sidebar-post">
								<?php if($post->post_status == 'publish'){ ?>
									<?php if(is_active_sidebar('listing_detail_sidebar')) { ?>
										<div class="sidebar">
											<?php dynamic_sidebar('listing_detail_sidebar'); ?>
										</div>
									<?php } ?>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!--==================================Section Close=================================-->
		<?php 
		global $post;
		echo listingpro_post_confirmation($post);
	} // end while
}