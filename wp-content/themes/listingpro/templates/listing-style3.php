<?php

global $listingpro_options;

if(have_posts()):

    while (have_posts()): the_post();



        setPostViews(get_the_ID());



        $user_id    =   get_current_user_id();



        $claimed_section = listing_get_metabox('claimed_section');

        $tagline_text = listing_get_metabox('tagline_text');

        $listingAuthorId = get_post_field( 'post_author', get_the_ID() );

        $currentUserId = get_current_user_id();

        $plan_id = listing_get_metabox_by_ID('Plan_id',get_the_ID());

        $gallery_show = get_post_meta( $plan_id, 'gallery_show', true );

        $NumberRating = listingpro_ratings_numbers(get_the_ID());

        $rating = get_post_meta( get_the_ID(), 'listing_rate', true );

        $rating_num_bg  =   '';

        $rating_num_clr  =   '';



        if( $rating < 2 ){ $rating_num_bg  =   'num-level1'; $rating_num_clr  =   'level1'; }
        elseif( $rating < 3 ){ $rating_num_bg  =   'num-level2'; $rating_num_clr  =   'level2'; }
        elseif( $rating < 4 ){ $rating_num_bg  =   'num-level3'; $rating_num_clr  =   'level3'; }
        elseif( $rating >= 4 ){ $rating_num_bg  =   'num-level4'; $rating_num_clr  =   'level4'; }

        $latitude = listing_get_metabox('latitude');

        $longitude = listing_get_metabox('longitude');

        $gAddress = listing_get_metabox('gAddress');

        $phone = listing_get_metabox('phone');

        $website = listing_get_metabox('website');

        $author_email = get_the_author_meta( 'user_email' );



        $facebook = listing_get_metabox('facebook');

        $twitter = listing_get_metabox('twitter');

        $linkedin = listing_get_metabox('linkedin');

        $youtube = listing_get_metabox('youtube');

        $instagram = listing_get_metabox('instagram');



        $buisness_hours = listing_get_metabox('business_hours');



        if(!empty($plan_id)){

            $plan_id = $plan_id;

        }else{

            $plan_id = 'none';

        }



        $contact_show = get_post_meta( $plan_id, 'contact_show', true );

        $map_show = get_post_meta( $plan_id, 'map_show', true );

        $video_show = get_post_meta( $plan_id, 'video_show', true );

        $gallery_show = get_post_meta( $plan_id, 'gallery_show', true );

        $menu_show = get_post_meta( $plan_id, 'listingproc_plan_menu', true );

        $tagline_show = get_post_meta( $plan_id, 'listingproc_tagline', true );

        $location_show = get_post_meta( $plan_id, 'listingproc_location', true );

        $website_show = get_post_meta( $plan_id, 'listingproc_website', true );

        $social_show = get_post_meta( $plan_id, 'listingproc_social', true );

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

            $price_show = 'true';

            $tags_show = 'true';

            $hours_show = 'true';

            $menu_show = 'true';

        }



        if(empty($tagline_text)){

            $tagline_text   =   '&nbsp;';

        }



        $lp_detail_page_additional_detail_position = $listingpro_options['lp_detail_page_additional_styles'];

        $showReport = true;

        if( isset( $listingpro_options['lp_detail_page_report_button'] ) )

        {

            if( $listingpro_options['lp_detail_page_report_button']=='off' )

            {

                $showReport = false;

            }

        }

        $resurva_url = get_post_meta($post->ID, 'resurva_url', true);



        $plan_id = listing_get_metabox_by_ID('Plan_id',get_the_ID());

        $gIDs = get_post_meta( $post->ID, 'gallery_image_ids', true );

        $sidebartop  =   'sidebar-top444';

        if( empty( $gIDs ) )

        {

            $sidebartop  =   'sidebar-top173';

        }

        $claimed_position = '';
        $title_len          =   strlen( $title );
        if( $title_len > 34 && $title_len < 43 )
        {
            $claimed_position   =   'position-static';
        }
        $claim = '';
        if($claimed_section == 'claimed') {
            $claim = '<span class="claimed '. $claimed_position .'"><i class="fa fa-check"></i> '. esc_html__('Claimed', 'listingpro').'</span>';
        }elseif($claimed_section == 'not_claimed') {
            $claim = '';
        }
        ?>
        <section class="lp-section lp-section-detail-page">
            <div class="lp-listing-top-title-header">
                <div class="container pos-relative">
                    <div class="row">
                        <div class="col-md-8 testt ">

                            <div class="lp-style3-header-wrap">

                                <?php
                                get_template_part( 'templates/single-list/listing-details-style3/content/gallery' );
                                include ( locate_template( 'templates/single-list/listing-details-style3/content/title-bar.php' ) )
                                ?>



                            </div>

                            <div class="lp-listing-action-btns">

                                <?php

                                $video = listing_get_metabox('video');

                                ?>

                                <ul>

                                    <li><?php listingpro_sharing_v2(); ?></li>

                                    <li>

                                        <?php
                                        $favrt  =   listingpro_is_favourite_v2(get_the_ID());
                                        ?>

                                        <a href="" class="<?php if($favrt == 'yes'){echo 'remove-fav-v2';}else{echo 'add-to-fav-v2';} ?>" data-post-id="<?php echo get_the_ID(); ?>" data-post-type="detail">

                                            <i class="<?php if($favrt == 'yes'){echo 'fa fa-bookmark';}else{echo 'fa-regular fa-bookmark-o';} ?>" aria-hidden="true"></i>

                                            <?php if($favrt == 'yes'){echo esc_html__('Saved', 'listingpro');}else{echo esc_html__('Save', 'listingpro');} ?>

                                        </a>

                                    </li>

                                    <?php

                                    if(!empty($video)  && $video_show == 'true'):

                                        if( strpos( $video, 'youtu.be' ) )

                                        {

                                            $video  =   str_replace( 'youtu.be', 'youtube.com/watch?v=', $video );

                                        }

                                        ?>

                                        <li><a href="<?php echo esc_attr($video); ?>" class="popup-youtube"><i class="fa fa-play-circle" aria-hidden="true"></i> <?php echo esc_html__('Watch Video', 'listingpro'); ?></a></li>

                                    <?php endif; ?>

                                    <?php

                                    $allowedReviews = $listingpro_options['lp_review_switch'];

                                    if( !empty( $allowedReviews ) && $allowedReviews == 1 && get_post_status( $post->ID )== "publish" ):

                                        ?>

                                        <li><a href="" class="review-form-toggle"><i class="fa fa-star" aria-hidden="true"></i> <?php echo esc_html__('Add a Review ', 'listingpro'); ?></a></li>

                                    <?php endif; ?>

                                    <?php

                                    $lp_listing_menus   =   get_post_meta( get_the_ID(), 'lp-listing-menu', true );

                                    if( is_array( $lp_listing_menus ) && !empty( $lp_listing_menus ) && $menu_show == 'true' ):

                                        ?>

                                        <li><a href="" class="lp-see-menu-btn"><i class="fa fa-cutlery" aria-hidden="true"></i> <?php echo esc_html__('See Menu', 'listingpro'); ?></a></li>

                                    <?php endif; ?>

                                </ul>

                                <div class="clearfix"></div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="container pos-relative">
                <div class="row">
                    <div class="col-md-8 test <?php if(!wp_is_mobile()){echo 'min-height-class';} ?>">
                        <?php
                        //show google ads
                        echo apply_filters('listingpro_show_google_ads', 'listing', get_the_ID());
                        ?>
                        <?php

                        $page_layout3    =   $listingpro_options['lp-detail-page-layout3-content']['general'];

                        if( isset( $page_layout3 ) )

                        {

                            foreach ( $page_layout3 as $key => $value )

                            {
                                switch ( $key )
                                {
                                    case 'lp_announcements_section' :
                                        get_template_part( 'templates/single-list/listing-details-style3/content/list-announcements' );
                                        break;
                                    case 'lp_content_section' :
                                        get_template_part( 'templates/single-list/listing-details-style3/content/list-description' );
                                        break;

                                    case 'lp_features_section':
                                        get_template_part( 'templates/single-list/listing-details-style3/content/list-features' );
                                        break;

                                    case 'lp_additional_section' :

                                        get_template_part( 'templates/single-list/listing-details-style3/content/list-additional' );

                                        break;

                                    case 'lp_faqs_section' :
                                        get_template_part( 'templates/single-list/listing-details-style3/content/list-faq' );
                                        break;


                                    case 'lp_offers_section' :
                                        $post_author_id = get_post_field( 'post_author', get_the_ID() );
                                        $discount_displayin =   get_user_meta( $post_author_id, 'discount_display_area', true );

                                        if( $discount_displayin == 'content' || empty( $discount_displayin ) )
                                        {
                                            get_template_part( 'templates/single-list/listing-details-style3/content/list-offer-deals-discount' );
                                        }

                                        break;

                                    case 'lp_reviewform_section' :
                                        get_template_part( 'templates/single-list/listing-details-style3/content/list-review-form' );
                                        break;

                                    case 'lp_reviews_section' :
                                        get_template_part( 'templates/single-list/listing-details-style3/content/list-reviews' );
                                        break;

                                    case 'lp_menu_section':
                                        get_template_part( 'templates/single-list/listing-details-style3/content/list-menu' );
                                        break;



                                    case 'lp_event_section':
                                        $post_author_id = get_post_field( 'post_author', get_the_ID() );
                                        $event_displayin =   get_user_meta( $post_author_id, 'event_display_area', true );
                                        if( $event_displayin == 'content' || empty( $event_displayin ) )
                                        {
                                            $GLOBALS['event_grid_call'] =   'content_area';
                                            get_template_part( 'templates/single-list/event' ) ;
                                        }
                                        break;



                                }

                            }

                        }

                        ?>



                    </div>
                    <div class="col-md-4 sidebar-top0">
                        <div class="lp-sidebar listing-page-sidebar">
                            <?php
                            $lp_detail_page_styles   =   $listingpro_options['lp_detail_page_styles'];
                            $page_layout3_sidebar    =   $listingpro_options['lp-detail-page-layout3-rsidebar']['sidebar'];
                            ?>
                            <div class="lp-widget lp-widget-top">
                                <?php

                                if( isset( $page_layout3_sidebar ) && !empty( $page_layout3_sidebar ) )
                                {
                                    foreach ( $page_layout3_sidebar as $key => $value )
                                    {
                                        switch ( $key )
                                        {
                                            case 'lp_sidebar_video':
                                                include ( locate_template( 'templates/single-list/listing-details-style3/sidebar/video.php' ) );
                                                break;
                                            case 'lp_mapsocial_section':
                                                include ( locate_template( 'templates/single-list/listing-details-style3/sidebar/map-social.php' ) );
                                                break;
                                            case 'lp_timing_section':
                                                include ( locate_template( 'templates/single-list/listing-details-style3/sidebar/timings.php' ) );
                                                break;
                                            case 'lp_booking_section':
                                                if(class_exists('Listingpro_bookings')){
                                                    include( WP_CONTENT_DIR . '/plugins/listingpro-bookings/templates/bookings.php' );
                                                }
                                                break;
                                            case 'lp_quicks_section':
                                                include ( locate_template( 'templates/single-list/listing-details-style3/sidebar/quicks.php' ) );
                                                break;
                                            case 'lp_additional_section':
                                                include ( locate_template( 'templates/single-list/listing-details-style3/sidebar/additional.php' ) );
                                                break;
                                            case 'lp_leadform_section':
                                                include ( locate_template( 'templates/single-list/listing-details-style3/sidebar/lead-form.php' ) );
                                                break;
                                            case 'lp_sidebarelemnts_section':
                                                if( is_active_sidebar( 'listing_detail_sidebar' ) )
                                                {
                                                    dynamic_sidebar( 'listing_detail_sidebar' );
                                                }
                                                break;
                                            case 'lp_offers_section':
                                                $post_author_id = get_post_field( 'post_author', get_the_ID() );
                                                $discount_displayin =   get_user_meta( $post_author_id, 'discount_display_area', true );
                                                if( $discount_displayin == 'sidebar' )
                                                {
                                                    get_template_part( 'templates/single-list/listing-details-style3/content/list-offer-deals-discount' );
                                                }
                                                break;
                                            case 'lp_event_section':
                                                $post_author_id = get_post_field( 'post_author', get_the_ID() );
                                                $event_displayin =   get_user_meta( $post_author_id, 'event_display_area', true );
                                                if( $event_displayin == 'sidebar' )
                                                {
                                                    $GLOBALS['event_grid_call'] =   'sidebar_area';
                                                    get_template_part( 'templates/single-list/event' ) ;
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
        </section>
        <?php
    endwhile; wp_reset_postdata(); endif;
global $post;
echo listingpro_post_confirmation($post);

?>