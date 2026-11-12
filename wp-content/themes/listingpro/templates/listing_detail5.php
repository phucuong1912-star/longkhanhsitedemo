<?php
/* The loop starts here. */
global $listingpro_options;
$currentUserId = get_current_user_id();
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

        $showReport = true;
        if( isset($listingpro_options['lp_detail_page_report_button']) ){
            if( $listingpro_options['lp_detail_page_report_button']=='off' ){
                $showReport = false;
            }
        }

        $claim = '';
        if($claimed_section == 'claimed') {
            $claim = '<span class="claimed"><i class="fa fa-check"></i> </span>';

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
            if(isset($menuMeta['menu-title'])) {
                    $menuTitle = $menuMeta['menu-title'];
            }

            $menuImg  =   '';
            if(isset($menuMeta['menu-img'])) {
                    $menuImg = $menuMeta['menu-img'];
            }
            $menuOption = true;        
        }

        $timekit = false;
        $timekit_booking = get_post_meta($post->ID, 'timekit_bookings', true);

        if(!empty($timekit_booking)){
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
        $tags = get_the_terms( $post->ID ,'features');
        ?>
        <!--==================================Section Open=================================-->

        <section class="aliceblue listing-second-view lp-detail-page-template-3 lp-detail-page-template-style3">
            <div class="post-meta-info">
                <?php
                $lp_detail_slider_styles = $listingpro_options['lp_detail_slider_styles'];
                $IDs = get_post_meta( $post->ID, 'gallery_image_ids', true );
                ?>
                <div class="container">
                    <div class="row padding-bottom-20">
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="detail-page3-tab-content">
                                <div class="tab-content">
                                    <div class="tab-pane <?php if( !empty( $IDs ) ){echo 'active';} ?>" id="gallery">
                                        <!--=======Galerry=====-->
                                        <?php

                                        if($lp_detail_slider_styles == 'style1'){
                                            if (!empty($IDs)) {
                                                if($gallery_show=="true"){
                                                    $imgIDs = explode(',',$IDs);
                                                    $numImages = count($imgIDs);
                                                    if($numImages >= 1 ){ ?>
                                                        <div class="pos-relative">
                                                            <div class="spinner">
                                                                <div class="double-bounce1"></div>
                                                                <div class="double-bounce2"></div>
                                                            </div>
                                                            <div class="single-page-slider-container style1">
                                                                <div class="row">
                                                                    <div class="">
                                                                        <div class="listing-slide2 img_<?php echo esc_attr($numImages); ?>" data-images-num="<?php echo esc_attr($numImages); ?>">
                                                                            <?php
                                                                            //$imgSize = 'listingpro-gal';
                                                                            require_once (THEME_PATH . "/include/aq_resizer.php");
                                                                            $imgSize = 'listingpro-detail_gallery';

                                                                            foreach($imgIDs as $imgID){

                                                                                if($numImages == 3){
                                                                                    $img_url = wp_get_attachment_image_src( $imgID, 'full');
                                                                                    $imgurl = aq_resize( $img_url[0], '770', '566', true, true, true);
                                                                                    $imgSrc = $imgurl;
                                                                                }elseif($numImages == 2){
                                                                                    $img_url = wp_get_attachment_image_src( $imgID, 'full');
                                                                                    $imgurl = aq_resize( $img_url[0], '770', '566', true, true, true);
                                                                                    $imgSrc = $imgurl;
                                                                                }elseif($numImages == 1){
                                                                                    $img_url = wp_get_attachment_image_src( $imgID, 'full');
                                                                                    $imgurl = aq_resize( $img_url[0], '770', '566', true, true, true);
                                                                                    $imgSrc = $imgurl;
                                                                                }elseif($numImages == 4){
                                                                                    $img_url = wp_get_attachment_image_src( $imgID, 'full');
                                                                                    $imgurl = aq_resize( $img_url[0], '770', '566', true, true, true);
                                                                                    $imgSrc = $imgurl;
                                                                                }else {
                                                                                    /* $imgurl = wp_get_attachment_image_src( $imgID, $imgSize);
                                                                                    $imgSrc = $imgurl[0]; */
                                                                                    $img_url = wp_get_attachment_image_src( $imgID, 'full');
                                                                                    $imgurl = aq_resize( $img_url[0], '770', '566', true, true, true);
                                                                                    $imgSrc = $imgurl;
                                                                                }
                                                                                $imgFull = wp_get_attachment_image_src( $imgID, 'full');
                                                                                if(!empty($imgurl[0])){
                                                                                    echo '
																							<div class="slide">
																								<a href="'. $imgFull[0] .'" rel="prettyPhoto[gallery1]">
																									<img src="'. $imgSrc .'" alt="'.get_the_title().'" />
																								</a>
																							</div>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    } else{
                                                        $imgurl = wp_get_attachment_image_src( $imgIDs[0], 'listingpro-listing-gallery');
                                                        $imgFull = wp_get_attachment_image_src( $imgID, 'full');
                                                        if(!empty($imgurl[0])){
                                                            echo '
																<div class="slide_ban text-center">
																	<a href="'. $imgFull[0] .'" rel="prettyPhoto[gallery1]">
																		<img src="'. $imgurl[0] .'" alt="'.get_the_title().'" />
																	</a>
																</div>';
                                                        }
                                                    }
                                                }
                                            }
                                        } else if($lp_detail_slider_styles == 'style2') {
                                            if (!empty($IDs)) {
                                                if($gallery_show=="true"){
                                                    $imgIDs = explode(',',$IDs);
                                                    $numImages = count($imgIDs);
                                                    if($numImages >= 1 ){ ?>
                                                        <div class="pos-relative">
                                                            <div class="spinner">
                                                                <div class="double-bounce1"></div>
                                                                <div class="double-bounce2"></div>
                                                            </div>
                                                            <div class="single-page-slider-container style2">
                                                                <div class="row">
                                                                    <div class="">
                                                                        <div class="listing-slide img_<?php echo esc_attr($numImages); ?>" data-images-num="<?php echo esc_attr($numImages); ?>">
                                                                            <?php
                                                                            $slider_height = $listingpro_options['slider_height'];
                                                                            //$imgSize = 'listingpro-gal';
                                                                            require_once (THEME_PATH . "/include/aq_resizer.php");
                                                                            $imgSize = 'listingpro-detail_gallery';
                                                                            foreach($imgIDs as $imgID){
                                                                                $img_url = wp_get_attachment_image_src( $imgID, 'full');
                                                                                $imgSrc = $img_url;
                                                                                $imgFull = wp_get_attachment_image_src( $imgID, 'full');
                                                                                $gstyle= 'style="height:'.$slider_height.'px;object-fit: cover"';
                                                                                if(!empty($img_url[0])){
                                                                                    echo '
																							<div class="slide">
																								<a href="'. $imgFull[0] .'" rel="prettyPhoto[gallery1]">
																									<img '.$gstyle.' src="'. $img_url[0] .'" alt="'.get_the_title().'" />
																								</a>
																							</div>';
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }
                                                    else{
                                                        $imgurl = wp_get_attachment_image_src( $imgIDs[0], 'listingpro-listing-gallery');
                                                        $imgFull = wp_get_attachment_image_src( $imgID, 'full');
                                                        if(!empty($imgurl[0])){
                                                            echo '
																<div class="slide_ban text-center">
																	<a href="'. $imgFull[0] .'" rel="prettyPhoto[gallery1]">
																		<img src="'. $imgurl[0] .'" alt="'.get_the_title().'" />
																	</a>
																</div>';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                        <!--=======END Galerry=====-->
                                    </div>

                                    <?php
                                    $video = listing_get_metabox_by_ID('video', $post->ID);
                                    if(!empty($video))
                                    {


                                        ?>
                                        <div class="tab-pane <?php if( empty( $IDs ) ){echo 'active';} ?>" id="video2">


                                            <div class="widget-video">

                                                <?php
                                                $htmlvideocode = wp_oembed_get($video);
                                                echo wp_kses_post($htmlvideocode);
                                                ?>
                                            </div>

                                        </div>

                                    <?php } ?>
                                </div>
                            </div>
                            <div class="lp-total-meta">
                                <ul class="clearfix" data-tabs="tabs">
                                    <?php
                                    if($gallery_show=="true" && $numImages > 0){
                                        ?>
                                        <li class="active">
                                            <a href="#gallery" data-toggle="tab">
                                                <i class="fa fa-camera" aria-hidden="true"></i> <span>2</span> <?php echo esc_html__( 'Images', 'listingpro' );?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php
                                    $video = listing_get_metabox_by_ID('video', $post->ID);
                                    if(!empty($video))
                                    {
                                        ?>
                                        <li class="">
                                            <a href="#video2" data-toggle="tab">
                                                <i class="fa fa-video-camera" aria-hidden="true"></i> <?php echo esc_html__( 'Video', 'listingpro' );?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>

                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <div class="post-meta-right-box text-center clearfix lp-post-meta-right-box-style3">
                                <?php
                                $author_avatar_url = get_user_meta(get_the_author_meta( 'ID' ), "listingpro_author_img_url", true);
                                $avatar ='';
                                if(!empty($author_avatar_url)) {
                                    $avatar =  $author_avatar_url;

                                } else {
                                    $avatar_url = listingpro_get_avatar_url (get_the_author_meta( 'ID' ), $size = '94' );
                                    $avatar =  $avatar_url;

                                }
                                ?>
                                <div class="author-img">
                                    <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><img src="<?php echo esc_url($avatar); ?>" alt="image"></a>
                                </div>
                                <div class="post-meta-left-box">

                                    <h1><?php the_title(); ?> <?php echo wp_kses_post($claim); ?></h1>

                                </div>
                                <div class="padding-top-30 padding-bottom-20">
									<span class="rating-section">
										<?php
                                        $NumberRating = listingpro_ratings_numbers($post->ID);
                                        if($NumberRating != 0){
                                            echo lp_cal_listing_rate(get_the_ID());
                                            ?>
                                            <span>
													<small><?php echo esc_attr($NumberRating); ?></small>

                                                <?php echo esc_html__('Ratings', 'listingpro'); ?>
												</span>
                                            <?php
                                        }else{
                                            echo lp_cal_listing_rate(get_the_ID());
                                        }
                                        ?>
									</span>



                                </div>
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

                                </ul>
                                <?php $phone = listing_get_metabox('phone');
                                $gAddress = listing_get_metabox('gAddress');
                                ?>
                                <?php if(!empty($phone)) { ?>
                                    <?php if($contact_show=="true"){ ?>
                                        <div class="lp-grid3-phone padding-bottom-10">
                                            <i class="fa fa-phone" aria-hidden="true"></i> <?php echo esc_html($phone); ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <?php if(!empty($gAddress)) {
                                    if($location_show=="true"){?>
                                        <div class="lp-details-address padding-bottom-20">

                                            <p class="margin-0"><i class="fa fa-map-marker" aria-hidden="true"></i>
                                                <span>
													<?php echo esc_attr($gAddress); ?>
												</span>
                                            </p>
                                            <a href="https://www.google.com/maps?daddr=<?php echo esc_attr($latitude); ?>,<?php echo esc_attr($longitude); ?>" target="_blank" class="theme-color">

												<span class="phone-number ">
													<?php echo esc_html__('View On Map', 'listingpro'); ?>
												</span>
                                            </a>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <div class="lp-qoute-butn">
                                    <a href="" id="freeQuoteForm"><?php echo esc_html__('Get a Free Quote', 'listingpro'); ?></a>
                                    <?php if(!empty($resurva_url)){ ?>
                                        <a href="" class=" make-reservation">
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
                                                    <a href="#reply-title" class="" id="clicktoreview">

                                                        <?php echo esc_html__('Write a Review', 'listingpro'); ?>
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
                    <div class="post-row lp-feature-row-outer">
                        <!-- <div class="post-row-header clearfix margin-bottom-15"><h3><?php echo esc_html__('Features', 'listingpro'); ?></h3></div> -->
                        <ul class="features list-style-none clearfix">

                            <?php
                            $location = '';
                            $location = get_the_terms( get_the_ID(), 'location' );
                            if ($location) { ?>
                                <li>
                                    <span><img alt='image' src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAARbSURBVGhD7ZpbqFVFGICPXbWbJaQV1UsXhShIIwxUVMiEVLqISCDqg6X1lJmggVA+ZIWaUGiBYQ9RmtqFoKSUsAQhCC9UppJlUKhoZmaFmX3fxNB5WLe996y9j+AHH5yZM2dm1mVm/pl1us6SzTX4KL6FO/AXPI1/4c/4Gb6I9+L52OO4Bd/Fv9GOV/EgPoUXYce5ElfiKbRzf+KH+AjegT6h8/BivBlH4yL8BuMF/YTTsWPcivvQzpzA5/AKrMpd+CnGC3oNL8S2MhZ/QzuwCa/DZnkAj6J1fY59sS3chr+jDa/AFIN2EO5B69yA52KtOCb2ow2+bEZCrsIf0bqd2WrlDbShjeggTs0QjE97hBl1MBj/weM4wIyamINeyFbsZUZqPkIbeDqkqnMpXvDfj5XojT+gbTkRJOUG9Gk4U11mRglDcQ3+inZId+EzeDmWMRP9Gwd+UuaiFb8dUvk42yxBLzpewBF0nYnpAzgci+iPLrJGClUuvDIfoJ2YElL5LEPL2fH5GMeS77pPKb6eDmgHdhHGZZadEFKJ+B6t1DAjj5EYO+mqnYUX5LRtuZ1YtF4sRsvNC6lExFfFmCmP+NSeCKl8nLa/RsuONyOHx9Eyr4ZUAgwZrNBpNw9XdwPGk1glxIhTbNGiOhkt49qVBKfDsgsxyrXMdyFVzt1oeZ9iHg+hZV4PqUT8gVaat5obuvh7N09VcABbfl1IZfMYWsYJJBnuGazUO5+H06plbgypYl5Ayy4MqWz8nWUWhFQijK2sdExIZbMcLbMqpPIxODRsdwK53Ywc3kPrmxhSiTAatdInQyqb6zEGfHlTpuvKF2iZssU1btoGhlQipqKVepeKeBDjvn0LuoV1IXSL66tyGP2d4UrR7OZNsZxPLune5Fq0YmOnso3UPRj3FVmux35YhHt+yxZNBk3zFVq5K3gZfXAauga4fTX4Mwbz6VThfbSth0MqMc+ilb8SUvXh0/IczFfUiSE5npp4Ib7njewvGsWnYDvJQ/jubEMbmRRS9fAl2kZZpN0Ss9BGDLHrwInC+g+h46w2LsF4nlu0mDXLJ2jdRSt+Mp5HG1sbUunwxlivcd3VZtSNM4k7QLeiTgCpWI1eSOrzskKWoo2+E1Kt4w1xunXabeX4tWGMmeIJySgzWiSODRfNtmMAaePbsZV4KO5N/F6S9MSkKn4C2I12otlQwrjtW7QOp/aOcR/Gu9nM54B4wFB2otIWPkY74yeGRjBUP4b+rWF+x7kJnY7d8TUy8ONhXdmOsq3MRju1F4vOviKG+Jb3sKJsb9JWfL/dd9i5l8wowHXC82DL3m9GT6P7KzbOjAzOQb85ehFvmtFT8Z8F7KSzWFa8FE/1/f7Ro16pLNyT29nN2H0DNgzjzq+2z2op8Vt7PMqJp4QGmvGQL+mBW93cid59Oz4DfTr+7EFfxxe+RvFMy85H/cbiGfEZSTyh9ATSr8JnLL5GjomiM+Oz/E9X17/6sy9A5nhpzgAAAABJRU5ErkJggg=="></span>
                                    <?php
                                    $location = get_the_terms( get_the_ID(), 'location' );

                                    echo esc_attr($location[0]->name);
                                    ?>
                                </li>
                            <?php } ?>
                            <?php
                            $category = '';
                            $category = get_the_terms( get_the_ID(), 'listing-category' );
                            if ($category) { ?>
                                <li>
                                    <span><img alt='image' src="data:image/png;base64,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"></span>
                                    <?php


                                    echo esc_attr($category[0]->name);
                                    ?>
                                </li>
                            <?php }?>
                            <li>
                                <span><img alt='image' src="data:image/png;base64,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"></span>
                                Travels up to
                                200km
                            </li>
                            <?php
                            if($price_show=="true"){
                                ?>
                            <li>
                                <?php echo wp_kses_post(listingpro_price_dynesty($post->ID)); ?>

                            </li>
                               <?php
                            }
                                ?>
                        </ul>

                    </div>
                </div>
            </div>
            <section class="profile-sticky-bar">
                <div class="container">
                    <div class="stickynavbar">
                        <div class="pull-left">
                            <nav id='nav_bar'>
                                <ul class='nav_links'>

                                    <?php
                                    $page_layout5   =   $listingpro_options['lp-detail-page-layout5-content']['general'];

                                    if( isset( $page_layout5 ) )
                                    {
                                        foreach ( $page_layout5 as $k => $val ){
                                            switch ($k)
                                            {
                                                case 'lp_content_section':
                                                    echo '<li><a href="#detail5-content">'.esc_html__('Overview', 'listingpro').'</a></li>';
                                                    break;
                                                case 'lp_announcements_section':
                                                    echo '<li><a href="#detail5-announcements">'.esc_html__('Announcements', 'listingpro').'</a></li>';
                                                    break;

                                                case 'lp_services_section':
                                                    echo '<li><a href="#detail5-services">'.esc_html__('Services', 'listingpro').'</a></li>';
                                                    break;
                                                case 'lp_offers_section':
                                                    echo '<li><a href="#detail5-offers">'.esc_html__('Offers', 'listingpro').'</a></li>';
                                                    break;
                                                case 'lp_faqs_section':
                                                    echo '<li><a href="#detail5-faq">'.esc_html__('Faqs', 'listingpro').'</a></li>';
                                                    break;
                                                case 'lp_reviews_section':
                                                    echo '<li><a href="#detail5-reviews">'.esc_html__('Reviews', 'listingpro').'</a></li>';
                                                    break;
                                            }
                                        }
                                    }
                                    ?>
                                </ul>
                            </nav>
                        </div>
                        <div class="pull-right">
                            <div class="lp-stickynavbar-buttons lp-qoute-butn">
                                <a href="#detail5-quote" id="freeQuoteForm"><?php echo esc_html__('Get a Free Quote', 'listingpro'); ?></a>
                            </div>
                            <div class="lp-sticky-user-name">
                                <?php
                                $current_user = wp_get_current_user();
                                $u_display_name = $current_user->display_name;
                                if(empty($u_display_name)){
                                    $u_display_name = $current_user->nickname;
                                }
                                global $listingpro_options;
                                $authorURL = $listingpro_options['listing-author'];
                                $D_pageID = url_to_postid($authorURL);
                                if ($D_pageID) {
	                                $authorURL = get_permalink($D_pageID);
                                }
                                if (is_user_logged_in()) {
                                    if (class_exists('ListingproPlugin')) {
                                        ?>
                                        <a href="<?php echo esc_url($authorURL); ?>">
                                            <?php
                                            echo  esc_html($u_display_name);
                                            ?>
                                        </a>
                                    <?php }else{ ?>
                                        <a href="<?php echo get_author_posts_url($current_user->ID); ?>">
                                            <?php
                                            echo  esc_html($u_display_name);
                                            ?>
                                        </a>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="author-img">
                                <?php
                                $author_avatar_url = get_user_meta(get_the_author_meta( 'ID' ), "listingpro_author_img_url", true);
                                $avatar ='';
                                if(!empty($author_avatar_url)) {
                                    $avatar =  $author_avatar_url;

                                } else {
                                    $avatar_url = listingpro_get_avatar_url (get_the_author_meta( 'ID' ), $size = '94' );
                                    $avatar =  $avatar_url;

                                }




                                ?>


                                <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><img src="<?php echo esc_url($avatar); ?>" alt="image"></a>
                            </div>
                        </div>
                    </div>


                </div>
            </section>
            <div class="content-white-area">

                <div class="single-inner-container single_listing lp-single_listing-style3" >
                    <?php
						//show google ads
						echo apply_filters('listingpro_show_google_ads', 'listing', get_the_ID());
					?>
                    <?php
                    $page_layout5   =   $listingpro_options['lp-detail-page-layout5-content']['general'];

                    if( isset( $page_layout5 ) )
                    {
                        foreach ( $page_layout5 as $k => $val ){
                            switch ($k)
                            {
                                case 'lp_content_section':
                                    get_template_part( 'templates/single-list/listing-detail-style5/content' );
                                    break;

                                case 'lp_services_section':
                                    get_template_part( 'templates/single-list/listing-detail-style5/services' );
                                    break;

                                case 'lp_announcements_section':
                                    get_template_part( 'templates/single-list/listing-detail-style5/announcements' );
                                    break;

                                case 'lp_offers_section':
                                    $DDO_design =   $listingpro_options['discount_deals_offers_design'];

                                    if( isset( $DDO_design ) && !empty( $DDO_design ) )
                                    {
                                        $DDO_design =   $DDO_design;
                                    }
                                    else
                                    {
                                        $DDO_design =   1;
                                    }
                                    if( $DDO_design == 2 )
                                    {
                                        get_template_part( 'templates/single-list/listing-detail-style5/offers' );
                                    }
                                    else
                                    {
                                        get_template_part( 'templates/single-list/listing-detail-style5/deals' );
                                    }

                                    break;

                                case 'lp_faqs_section':
                                    get_template_part( 'templates/single-list/listing-detail-style5/faq' );
                                    break;

                                case 'lp_reviews_section':
                                    get_template_part( 'templates/single-list/listing-detail-style5/reviews' );
                                    break;

                                case 'lp_quote_section':
                                    get_template_part( 'templates/single-list/listing-detail-style5/quote' );
                                    break;
                            }
                        }
                    }
                    ?>

                    <?php  ?>

                </div>

            </div>
        </section>
        <!--==================================Section Close=================================-->
        <?php
        global $post;
        echo listingpro_post_confirmation($post);
    } // end while
}