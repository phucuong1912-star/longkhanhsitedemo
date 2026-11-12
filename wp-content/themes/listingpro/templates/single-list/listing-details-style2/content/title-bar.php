<div class="post-meta-info">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-sm-8 col-xs-12">
                <div class="post-meta-left-box">
                    <?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
					<?php
					$b_logo =   $listingpro_options['business_logo_switch'];
					$allow_logo =   isset($listingpro_options['listingpro_allow_logo_styles_switch']) ? $listingpro_options['listingpro_allow_logo_styles_switch'] : '';
					if( $b_logo ==true && $allow_logo == "yes" ):
						$business_logo_url  =   '';
						$b_logo_default =   $listingpro_options['business_logo_default']['url'];
						$business_logo = listing_get_metabox_by_ID('business_logo',get_the_ID());
						if( empty( $business_logo ) )
						{
							$business_logo_url  =   $b_logo_default;
						}
						else
						{
							$business_logo_url  =   $business_logo;
						}
						if( !empty( $business_logo_url ) ):
							?>
							<div class="lp-listing-title">
								<div class="lp-listing-logo">
									<img src="<?php echo esc_attr( $business_logo_url ); ?>" alt="Listing Logo">
								</div>
							</div>
					<?php 
						endif; 
					endif; 
					?>
                    <h1><?php the_title(); ?> <?php echo wp_kses_post( $claim ); ?></h1>
                    <?php if(!empty($tagline_text)) {
                        if($tagline_show=="true"){?>
                            <p><?php echo esc_attr( $tagline_text ); ?></p>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="post-meta-right-box text-right clearfix margin-top-20">
                    <ul class="post-stat">
                        <?php
                        $favrt  =   listingpro_is_favourite_v2(get_the_ID());

                        ?>
                        <li id="fav-container">
                            <a href="" class="<?php if($favrt == 'yes'){echo 'remove-fav-v2';}else{echo 'add-to-fav-v2';} ?>" data-post-id="<?php echo get_the_ID(); ?>" data-post-type="detail">

                                <i class="<?php if($favrt == 'yes'){echo 'fa fa-bookmark';}else{echo 'fa-regular fa-bookmark-o';} ?>" aria-hidden="true"></i>

                                <?php if($favrt == 'yes'){echo esc_html__('Saved', 'listingpro');}else{echo esc_html__('Save', 'listingpro');} ?>

                            </a>
                        </li>
                        <li class="reviews sbutton">
                            <?php listingpro_sharing(); ?>
                        </li>
                    </ul>
                    <div class="padding-top-30">
								<span class="rating-section">
									<?php
                                    $NumberRating = listingpro_ratings_numbers($post->ID);
                                    if($NumberRating != 0){
                                        echo lp_cal_listing_rate(get_the_ID());
                                        ?>
                                        <span>
												<small><?php echo esc_attr( $NumberRating ); ?></small>
												<br>
                                            <?php echo esc_html__('Ratings', 'listingpro'); ?>
											</span>
                                        <?php
                                    }else{
                                        echo lp_cal_listing_rate(get_the_ID());
                                    }
                                    ?>
								</span>
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
                        <?php }else{
                            if (class_exists('ListingReviews')) {
                                $allowedReviews = $listingpro_options['lp_review_switch'];
                                if(!empty($allowedReviews) && $allowedReviews=="1"){
                                    if(get_post_status($post->ID)=="publish"){

                                        ?>
                                        <a href="#review-section" class="secondary-btn" id="clicktoreview2">
                                            <i class="fa fa-star"></i>
                                            <?php echo esc_html__('Submit Review', 'listingpro'); ?>
                                        </a>
                                        <?php
                                    }
                                }
                            }
                        }
                        ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>