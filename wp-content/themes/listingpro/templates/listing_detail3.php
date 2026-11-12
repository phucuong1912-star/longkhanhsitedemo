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

        ?>

        <!--==================================Section Open=================================-->

        <section class="aliceblue listing-second-view lp-detail-page-template-3">

            <?php

            //gallery

            include ( locate_template( 'templates/single-list/listing-details-style2/content/gallery.php' ) );



            //title bar

            include ( locate_template( 'templates/single-list/listing-details-style2/content/title-bar.php' ) );

            ?>



            <div class="content-white-area">



                <div class="container single-inner-container single_listing" >

                    <?php

                    //show google ads

                    echo apply_filters('listingpro_show_google_ads', 'listing', get_the_ID());

                    ?>

                    <div class="row">

                        <div class="col-md-8 col-sm-8 col-xs-12">





                            <div class="single-tabber2 margin-bottom-30" id="reply-title2">



                                <ul class="row list-style-none clearfix" data-tabs="tabs">

                                    <?php

                                    $pagecontentOption = $listingpro_options['lp-detail-page-layout2-content']['general'];

                                    if ($pagecontentOption):



                                        foreach ($pagecontentOption as $key=>$value) {



                                            switch($key) {


                                                case 'lp_event_section':
                                                    $plan_id = listing_get_metabox_by_ID('Plan_id',$post->ID);
                                                    $showevent = get_post_meta( $plan_id, 'lp_eventsplan', true );
                                                    $post_author_id = get_post_field( 'post_author', get_the_ID() );
                                                    $event_displayin =   get_user_meta( $post_author_id, 'event_display_area', true );
                                                    $attached_events   =   get_post_meta( $post->ID, 'event_id', true );
                                                    if (!is_array($attached_events)){
                                                        $showevent = false;
                                                    }
                                                    if ($showevent != 'false'){
														$showevent = get_post_meta( $plan_id, 'lp_eventsplan', true );
													}
                                                    if ($showevent != 'false'){
                                                        if( $event_displayin == 'content' || empty( $event_displayin ) ) {
                                                            ?>
                                                            <li class="lp-detailpage2-events-tab">
                                                                <a href="#eventstab" data-toggle="tab">
                                                                    <?php echo esc_html__( 'Events', 'listingpro' );?>
                                                                </a>
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    break;

                                                case 'lp_content_section': ?>

                                                    <li class="">

                                                        <a href="#adinfo" data-toggle="tab">

                                                            <?php echo esc_html__( 'Details', 'listingpro' );?>

                                                        </a>

                                                    </li>

                                                    <?php

                                                    break;



                                                case 'lp_video_section': ?>

                                                    <?php

                                                    $video = listing_get_metabox_by_ID('video', $post->ID);

                                                    if(!empty($video)){

                                                        ?>

                                                        <li class="">

                                                            <a href="#video" data-toggle="tab">

                                                                <?php echo esc_html__( 'Video', 'listingpro' );?>

                                                            </a>

                                                        </li>

                                                        <?php

                                                    } else {

                                                    }

                                                    ?>

                                                    <?php

                                                    break;



                                                case 'lp_faqs_section': ?>

                                                    <?php

                                                    $plan_id = listing_get_metabox_by_ID('Plan_id',$post->ID);

                                                    if(!empty($plan_id)){

                                                        $plan_id = $plan_id;

                                                    }else{

                                                        $plan_id = 'none';

                                                    }



                                                    $faqs_show = get_post_meta( $plan_id, 'listingproc_faq', true );

                                                    if($plan_id=="none"){

                                                        $faqs_show = 'true';

                                                    }



                                                    $faqs = listing_get_metabox_by_ID('faqs', $post->ID);

                                                    if($faqs_show=="true") {

                                                        if (!empty($faqs) && count($faqs) > 0) {

                                                            $faq = $faqs['faq'];

                                                            $faqans = $faqs['faqans'];

                                                            if (!empty($faq[1])) {

                                                                ?>

                                                                <li class="">

                                                                    <a href="#faqs" data-toggle="tab">

                                                                        <?php echo esc_html__('FAQs', 'listingpro'); ?>

                                                                    </a>

                                                                </li>

                                                                <?php

                                                            }

                                                        }

                                                    }

                                                    break;



                                                case 'lp_reviews_section': ?>

                                                    <li class="lpreviews">

                                                        <a href="#reviews" data-toggle="tab">

                                                            <?php echo esc_html__( 'Reviews ', 'listingpro' );?>

                                                        </a>

                                                    </li>

                                                    <?php

                                                    break;



                                                case 'lp_contacts_section': ?>
                                                    <?php
                                                    $plan_id = listing_get_metabox_by_ID( 'Plan_id', $post->ID );
                                                    if(empty($plan_id)){
                                                        $plan_id = 'none';
                                                    }
                                                    $leadform_show = get_post_meta( $plan_id, 'listingproc_leadform', true );
                                                    if($plan_id=="none"){
                                                        $leadform_show = 'true';
                                                    }
                                                    if($leadform_show   ==  "true"){
                                                        ?>
                                                        <li class="">

                                                            <a href="#contact" data-toggle="tab">

                                                                <?php echo esc_html__( 'Contact Info', 'listingpro' );?>

                                                            </a>

                                                        </li>
                                                    <?php } ?>
                                                    <?php

                                                    break;

                                                case 'lp_offers_section':

                                                    $strNow =   strtotime("NOW");
                                                    $listing_discount_data =   get_post_meta( get_the_ID(), 'listing_discount_data', true );
                                                    $discount_data = isset($listing_discount_data[0]) ? $listing_discount_data[0] : '';
                                                    if (is_array($listing_discount_data)):
                                                        if( ( $strNow < $discount_data['disExpE'] || empty( $discount_data['disExpE'] ) ) && ( $strNow > $discount_data['disExpS'] || empty( $discount_data['disExpS'] ) ) ) :

                                                            $discounts_dashoard =   true;

                                                            if( isset( $listingpro_options['discounts_dashoard'] ) && $listingpro_options['discounts_dashoard'] == 0 )

                                                            {

                                                                $discounts_dashoard =   false;

                                                            }

                                                            $post_author_id = get_post_field( 'post_author', get_the_ID() );

                                                            $discount_displayin =   get_user_meta( $post_author_id, 'discount_display_area', true );

                                                            $listing_discount_data  =   get_post_meta( get_the_ID(), 'listing_discount_data', true );



                                                            if( $discounts_dashoard == true &&

                                                                ( $discount_displayin == 'content' || empty( $discount_displayin ) ) &&

                                                                is_array( $listing_discount_data ) && !empty( $listing_discount_data )

                                                            ):

                                                                ?>



                                                                <li class="">

                                                                    <a href="#offers_deals" data-toggle="tab">

                                                                        <?php echo esc_html__( 'Offers/Deals', 'listingpro' );?>

                                                                    </a>

                                                                </li>

                                                                <?php

                                                            endif; endif; endif;  break;

                                                case 'lp_announcements_section' :

                                                    $announcements_show =   true;

                                                    if( isset( $listingpro_options['announcements_dashoard'] ) && $listingpro_options['announcements_dashoard'] == 0 )

                                                    {
                                                        $announcements_show =   false;
                                                    }

                                                    $lp_listing_announcements_raw  =   get_post_meta( get_the_ID(), 'lp_listing_announcements', true );
                                                    $lp_listing_announcements   =   array();
                                                    if( $lp_listing_announcements_raw != '' && is_array( $lp_listing_announcements_raw ) && count($lp_listing_announcements_raw) > 0 ) {
                                                        foreach ( $lp_listing_announcements_raw as $k => $v ) {
                                                            if($v['annStatus']) {
                                                                $lp_listing_announcements[] =   $v;
                                                            }
                                                        }
                                                    }

                                                    if( $lp_listing_announcements != '' && is_array( $lp_listing_announcements ) && count($lp_listing_announcements) > 0 ):

                                                        ?>

                                                        <li class="">

                                                            <a href="#announcements_tab" data-toggle="tab">

                                                                <?php echo esc_html__( 'Announcements', 'listingpro' );?>

                                                            </a>

                                                        </li>

                                                        <?php

                                                    endif;

                                                    break;



                                                case 'lp_menu_section' :

                                                    $lp_listing_menus   =   get_post_meta( get_the_ID(), 'lp-listing-menu', true );

                                                    if( is_array( $lp_listing_menus ) && !empty( $lp_listing_menus ) ):
                                                        ?>

                                                        <li class="">

                                                            <a href="#menu_tab" data-toggle="tab">

                                                                <?php echo esc_html__( 'Menu', 'listingpro' );?>

                                                            </a>

                                                        </li>

                                                        <?php

                                                    endif;

                                                    break;



                                            }



                                        }

                                    endif;

                                    ?>

                                </ul>



                            </div>

                            <div class="detail-page2-tab-content">

                                <div class="tab-content">

                                    <?php

                                    $pagecontentOption = $listingpro_options['lp-detail-page-layout2-content']['general'];

                                    if ($pagecontentOption):

                                        foreach ($pagecontentOption as $key=>$value) {



                                            switch($key) {

                                                case 'lp_event_section':
                                                    $plan_id = listing_get_metabox_by_ID('Plan_id',$post->ID);

                                                    $showevent = get_post_meta( $plan_id, 'lp_eventsplan', true );

                                                    if ($showevent != 'false'){


                                                        $post_author_id = get_post_field( 'post_author', get_the_ID() );
                                                        $event_displayin =   get_user_meta( $post_author_id, 'event_display_area', true );
                                                        if( $event_displayin == 'content' || empty( $event_displayin ) ) {
                                                            ?>
                                                            <div class="tab-pane" id="eventstab">
                                                                <?php
                                                                $GLOBALS['event_grid_call'] =   'content_area';
                                                                get_template_part( 'templates/single-list/event' ) ;
                                                                ?>
                                                            </div>
                                                            <?php
                                                        }}
                                                    break;

                                                case 'lp_content_section': ?>

                                                    <!--adinfo-->

                                                    <?php get_template_part('templates/single-list/listing-details-style2/content/adinfo'); ?>

                                                    <?php

                                                    break;



                                                case 'lp_video_section': ?>

                                                    <!--video-->

                                                    <?php get_template_part('templates/single-list/listing-details-style2/content/video'); ?>

                                                    <?php

                                                    break;



                                                case 'lp_faqs_section': ?>

                                                    <!--faq-->

                                                    <?php get_template_part('templates/single-list/listing-details-style2/content/faqs'); ?>

                                                    <?php

                                                    break;



                                                case 'lp_reviews_section': ?>

                                                    <!--reviews-->

                                                    <?php get_template_part('templates/single-list/listing-details-style2/content/reviews'); ?>

                                                    <?php

                                                    break;



                                                case 'lp_contacts_section':
                                                    $plan_id = listing_get_metabox_by_ID( 'Plan_id', $post->ID );
                                                    if(empty($plan_id)){
                                                        $plan_id = 'none';
                                                    }
                                                    $leadform_show = get_post_meta( $plan_id, 'listingproc_leadform', true );
                                                    if($plan_id=="none"){
                                                        $leadform_show = 'true';
                                                    }
                                                    if($leadform_show   ==  "true"){
                                                        get_template_part('templates/single-list/listing-details-style2/content/contacts');
                                                    }

                                                    break;

                                                case 'lp_offers_section':

                                                    $post_author_id = get_post_field( 'post_author', get_the_ID() );

                                                    $discount_displayin =   get_user_meta( $post_author_id, 'discount_display_area', true );



                                                    if( $discount_displayin == 'content' || empty( $discount_displayin ) )

                                                    {

                                                        get_template_part( 'templates/single-list/listing-details-style2/content/list-offer-deals-discount' );

                                                    }

                                                    break;



                                                case 'lp_announcements_section' :

                                                    get_template_part( 'templates/single-list/listing-details-style2/content/list-announcements' );

                                                    break;



                                                case 'lp_menu_section':

                                                    get_template_part( 'templates/single-list/listing-details-style2/content/list-menu' );

                                                    break;



                                            }



                                        }

                                    endif;

                                    ?>



                                </div>



                            </div>

                            <div class="clearfix"></div>



                        </div>



                        <div class="col-md-4 col-sm-4 col-xs-12">

                            <?php

                            $pagesidebrOption = $listingpro_options['lp-detail-page-layout2-rsidebar']['sidebar'];

                            if ($pagesidebrOption):

                                foreach ($pagesidebrOption as $key=>$value) {

                                    switch($key) {



                                        case 'lp_timing_section': get_template_part( 'templates/single-list/listing-details-style2/sidebar/timings' );

                                            break;

                                        case 'lp_booking_section':
                                            if(class_exists('Listingpro_bookings')){
                                                include( WP_CONTENT_DIR . '/plugins/listingpro-bookings/templates/bookings.php' );
                                            }
                                            break;

                                        case 'lp_mapsocial_section': get_template_part( 'templates/single-list/listing-details-style2/sidebar/map-contacts' );

                                            break;



                                        case 'lp_quicks_section': get_template_part( 'templates/single-list/listing-details-style2/sidebar/quicks' );

                                            break;

                                        case 'lp_additional_section': get_template_part( 'templates/single-list/listing-details-style2/sidebar/additional' );

                                            break;



                                        case 'lp_sidebarelemnts_section': get_template_part( 'templates/single-list/listing-details-style2/sidebar/def-sidebar' );

                                            break;



                                        case 'lp_event_section':

                                            $post_author_id = get_post_field( 'post_author', get_the_ID() );

                                            $event_displayin =   get_user_meta( $post_author_id, 'event_display_area', true );

                                            if( $event_displayin == 'sidebar' )

                                            {

                                                $GLOBALS['event_grid_call'] =   'sidebar_area';

                                                get_template_part( 'templates/single-list/event' ) ;

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

                                    }

                                }

                            endif;

                            ?>

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