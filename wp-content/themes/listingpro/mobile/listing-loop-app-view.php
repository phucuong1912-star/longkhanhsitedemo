<?php

global $wp_query;

$thisCatsArray = array();

$thisAdCatArray = array();
global $post;
$lpThisPost = $post->ID;

$lpThisPostPlan = listing_get_metabox_by_ID('Plan_id', $lpThisPost);

$restrictCampaign = get_post_meta($lpThisPostPlan, 'listingproc_plan_campaigns', true);

$showthisadinSidebar = true;

if (!empty($showthisadinSidebar)) {

			$output = null;

			global $listingpro_options;

			$lp_review_switch = $listingpro_options['lp_review_switch'];



			

			$postGridCount2 = '';

			if(!isset($postGridCount2)){

			$postGridCount2 = '0';

			}

			global $postGridCount2;   

			$postGridCount2++;



			$deafaultFeatImg = lp_default_featured_image_listing();		

			if ( !has_post_thumbnail() && $listingpro_options['lp_def_featured_image_from_gallery'] == 'enable')  {
				$IDs = get_post_meta(get_the_ID(), 'gallery_image_ids', true);


				$IDs = explode(',', $IDs);
				$imgurl_bgd = '';

				if (is_array($IDs)) {
					shuffle($IDs);

					$img_url = wp_get_attachment_image_src($IDs[0], 'listingpro-blog-grid');


					if(!empty( $img_url )){
						$deafaultFeatImg = $img_url[0];
						$deafaultFeatImg = $img_url[0];
					}

					if (!empty($imgurl)) {
						$deafaultFeatImg = $imgurl;
					} elseif (!empty($deafaultFeatImg)) {
						$deafaultFeatImg = $deafaultFeatImg;
					} else {
						$deafaultFeatImg = 'https://placehold.co/372x240';
					}

				}
			}

			

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

				$listing_layout = '';

				if(isset($GLOBALS['listing_layout_element'])){

					if( !empty( $GLOBALS['listing_layout_element'] ) || $GLOBALS['listing_layout_element'] != '' )

					{

						$listing_layout =  $GLOBALS['listing_layout_element'];

					}

				}

				else

                {

                    $listing_layout = $listingpro_options['listing_views'];

                    if( $listing_layout == 'grid_view_classic' || $listing_layout == 'grid_view2' || $listing_layout == 'grid_view_v2'|| $listing_layout == 'grid_view_v3' )

                    {

                        $listing_layout = 'grid_view';

                    }elseif( $listing_layout == 'lp-list-view-compact' ){

                        $listing_layout = 'list_view';
                    }

                }

				

				if( is_singular('listing') ){

                    $listing_layout = 'list_view';

                }

                ?>

					

					<div data-counter="<?php echo esc_attr($postGridCount2); ?>" class="<?php echo esc_attr($listing_layout); ?> <?php echo esc_attr($listing_style); ?> <?php echo esc_attr($adClass); ?> lp-grid-box-contianer clearfix view-toggle card1 lp-grid-box-contianer1 lp-grid-app-view" data-title="<?php echo get_the_title(); ?>" data-postid="<?php echo get_the_ID(); ?>"   data-lattitue="<?php echo esc_attr($latitude); ?>" data-longitute="<?php echo esc_attr($longitude); ?>" data-posturl="<?php echo get_the_permalink(); ?>">

					<?php if(is_page_template('template-favourites.php')){ ?>

					   <div class="remove-fav md-close" data-post-id="<?php echo get_the_ID(); ?>">

						<i class="fa fa-close"></i>

						

					   </div>

					  <?php } ?>

					  <div class="gaddress" style="display:none"><?php echo esc_attr($gAddress); ?></div>

						<div class="lp-grid-box lp-border">



                            <div class="lp-grid-box-thumb pos-relative">

								

                                <div class="show-img">

                                    <?php

                                        if ( has_post_thumbnail()) {

                                            $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'listingpro-thumb4' );

                                                if(!empty($image[0])){

                                                    echo "<a href='".get_the_permalink()."' >

                                                            <img alt='image' src='" . $image[0] . "' />

                                                        </a>";

                                                }else {

                                                    echo '

                                                    <a href="'.get_the_permalink().'" >

                                                        <img src="'.esc_html__('https://placehold.co/272x300', 'listingpro').'" alt="image">

                                                    </a>';

                                                }

                                        }

										elseif(!empty($deafaultFeatImg)){

											echo "<a href='".get_the_permalink()."' >

													<img alt='image' src='".$deafaultFeatImg."' />

												</a>";

										}

										else {

                                            echo '

                                            <a href="'.get_the_permalink().'" >

                                                <img src="'.esc_html__('https://placehold.co/272x300', 'listingpro').'" alt="image">

                                            </a>';

                                        }

                                    ?>

									

                                </div>

                                <div class="hide-img listingpro-list-thumb">

                                    <?php

                                        if ( has_post_thumbnail()) {

                                            $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'listingpro-author-thumb' );

                                                if(!empty($image[0])){

                                                    echo "<a href='".get_the_permalink()."' >

                                                            <img alt='image' src='" . $image[0] . "' />

                                                        </a>";

                                                }else {

                                                    echo '

                                                    <a href="'.get_the_permalink().'" >

                                                        <img src="'.esc_html__('https://placehold.co/63x63', 'listingpro').'" alt="image">

                                                    </a>';

                                                }

                                        }elseif(!empty($deafaultFeatImg)){

											echo "<a href='".get_the_permalink()."' >

													<img alt='image' src='".$deafaultFeatImg."' />

												</a>";

										}else {

                                            echo '

                                            <a href="'.get_the_permalink().'" >

                                                <img src="'.esc_html__('https://placehold.co/63x63', 'listingpro').'" alt="image">

                                            </a>';

                                        }

                                    ?>

									

                                </div>

								<a href="#" data-post-type="grids" data-post-id="<?php echo esc_attr(get_the_ID()); ?>" data-success-text="<?php echo esc_html__('Saved', 'listingpro') ?>" class="status-btn add-to-fav lp-add-to-fav">

												<i class="<?php echo esc_attr($isfavouriteicon); ?>"></i>

								</a>

								 <?php echo wp_kses_post($CHeckAd); ?>

                            </div>

							<div class="lp-grid-desc-container">

								<div class="lp-grid-box-description">

                                    <h4 class="lp-h4">

                                        <a href="<?php echo get_the_permalink(); ?>">

                                           <?php echo wp_kses_post($CHeckAd); ?>

                                            <?php echo mb_substr(get_the_title(), 0, 20);?>

                                            <?php echo wp_kses_post($claim); ?>

                                        </a>

                                    </h4>

									<div class="clearfix lp-grid-box-bottom-app-view">

										<div class="lp-grid-box-left pull-left">



											<ul>

												<li>

													<?php

														$NumberRating = listingpro_ratings_numbers($post->ID);



															if($NumberRating <= 1){

																$review = esc_html__('Rating', 'listingpro');

															}else{

																$review = esc_html__('Ratings', 'listingpro');

															}

													if($NumberRating != 0){

															echo lp_cal_listing_rate(get_the_ID());

													?>



													<?php

														}else{?>

															<span>

																<?php echo wp_kses_post($NumberRating); ?>

																<?php echo wp_kses_post($review); ?>

															</span>

														<?php }

													?>

												</li>

												<li class="middle">

													<?php echo listingpro_price_dynesty_text(get_the_ID()); ?>

												</li>

												<?php

												$cats = get_the_terms( get_the_ID(), 'listing-category' );

												if( !empty( $cats ) ){

												$catCount = 1;	

												?>

												<li class="grid-view-hide">

													<?php

														foreach ( $cats as $cat ) {

															if($catCount==1){

															$category_image = listing_get_tax_meta($cat->term_id,'category','image');

															$lp_default_map_pin = get_template_directory_uri() . '/assets/images/pins/pin.png';

														   if( empty( $category_image ) )

														   {

															   $category_image =   $lp_default_map_pin;

														   }

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

															$catCount++;

														}

													?>

												</li>

												<?php } ?>

												<?php

												$locs = get_the_terms( get_the_ID(), 'location' );

												if(!empty($locs)){

													$locCount = 1;	

													

													?>

													<li>

														<?php

														foreach ( $locs as $loc ) {

															if($locCount==1){

															echo '<span class="cat-icon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>';

															$term_link = get_term_link( $loc );

															echo '

															<a href="'.$term_link.'">

																'.$loc->name.'

															</a>';

															}

															$locCount++;

														}

														?>

													</li>

												<?php } ?>

											</ul>

										</div>

									</div>

								</div>

							</div>

                            

						</div>

					</div>

					

					<?php



				   $app_view_home  =   $listingpro_options['app_view_home'];

				   if( ( !is_page( $app_view_home ) || empty( $app_view_home) ) && !is_home() && !is_front_page() && !is_page_template('template-dashboard.php') && ($listing_layout = 'grid_view' || $listing_layout = 'grid_view2') ) {

						if($postGridCount2%2 == 0){

						echo '<div class="removeable clearfix"></div>';

					}

				   }

}

?>