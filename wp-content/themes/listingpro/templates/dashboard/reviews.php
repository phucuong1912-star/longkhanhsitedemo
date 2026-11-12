<?php
/* for response submit */
global $paged, $wp_query, $current_user;
$loggedinusername = $current_user->display_name;
if(isset($_POST['submit_response'])){

    $pid = '';
    $userName = '';
    $userEmail = '';
    $pid = sanitize_text_field($_POST['rewID']);
    $userName = sanitize_text_field($_POST['userName']);
    $userEmail = sanitize_text_field($_POST['userEmail']);
    $authEmail = sanitize_text_field($_POST['authEmail']);
    $review_author = get_post_field('post_author', $pid);
    $review_obj = get_user_by('id', $review_author);
    $reviewuserEmail = $review_obj->user_email;
    $userName = $review_obj->user_login;

    $website_url = site_url();
    $website_name = get_option('blogname');
    $listing_id = listing_get_metabox_by_ID('listing_id', $pid);

    $sender_authorid = get_post_field('post_author', $listing_id);
    $sender_author = get_user_by('id', $sender_authorid);
    $sender_name = $sender_author->user_login;

    $listing_title = get_the_title($listing_id);
    $listing_url = get_the_permalink($listing_id);



    $review_res = '';
    $review_res = sanitize_text_field($_POST['review_reply']);
    $reply = $review_res;
    // moin here strt
    $review_reply_time = '';
    $review_reply_time = sanitize_text_field($_POST['reviewTime']);
    listing_set_metabox('review_reply_time', $review_reply_time, $pid);
    // moin here ends
    $body = $review_res;
    listing_set_metabox('review_reply', $review_res, $pid);
    $from = $userEmail;
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $mailSubj = lp_theme_option('listingpro_subject_listing_rev_reply');
    $mailBody = lp_theme_option('listingpro_msg_listing_rev_reply');



    $formated_mailBody = lp_sprintf2("$mailBody", array(
        'review_reply_text' => "$body",
        'reply' => "$reply",
        'sender_name' => "$sender_name",
        'sender_name' => "$sender_name",
        'user_name' => "$userName",
        'website_url' => "$website_url",
        'listing_title' => "$listing_title",
        'listing_url' => "$listing_url",
    ));


    $mailSubj = lp_sprintf2("$mailSubj", array(
        'review_reply_text' => "$body",
        'reply' => "$reply",
        'sender_name' => "$sender_name",
        'sender_name' => "$sender_name",
        'user_name' => "$userName",
        'website_url' => "$website_url",
        'listing_title' => "$listing_title",
        'listing_url' => "$listing_url",
    ));
    lp_mail_headers_append();
    LP_send_mail( $reviewuserEmail, $mailSubj, $formated_mailBody, $headers );
    lp_mail_headers_remove();

}

$submittedReviews = getAllReviewsArray(true);
$recentReviews = getAllReviewsArray(false);
$recentReviews = array_filter($recentReviews);
$submittedReviews = array_filter($submittedReviews);
$ActClass = 'active';
$ActClass2 = '';
?>

<?php
global $listingpro_options;
$post_author = get_post_field( 'post_author', get_the_ID() );
$author_obj = get_user_by('id', $post_author);
$username = $author_obj->user_login;
$userEmail = $author_obj->user_email;
?>
<div id="reviews" role="tab" class="tab-pane fade in">

    <div class="panel with-nav-tabs panel-default lp-dashboard-tabs lp-all-reviews-outer col-md-11 align-center padding-bottom-50">
        <?php if(!empty($recentReviews) || !empty($submittedReviews)){ ?>
            <div class="panel-heading">

                <ul class="nav nav-tabs">
                    <?php if(!empty($recentReviews)){
                        $ActClass = '';
                        $ActClass2 = 'active';
                        ?>
                        <li class="active"><a href="#tab1default" data-toggle="tab"><?php echo esc_html__('all', 'listingpro'); ?></a></li>
                        <li><a href="#tab2default" data-toggle="tab"><?php echo esc_html__('received', 'listingpro'); ?></a></li>
                    <?php }else{
                        $ActClass = 'active';
                    }?>
                    <li class="<?php echo esc_attr($ActClass); ?>"><a href="#tab3default" data-toggle="tab"><?php echo esc_html__('submitted', 'listingpro'); ?></a></li>

                </ul>

            </div>
        <?php } ?>
        <div class="panel-body">

            <div class="tab-content clearfix">
                <div class="tab-pane fade in <?php echo esc_attr($ActClass2); ?>" id="tab1default">


                    <!--reveived reviews -->
                    <?php

                    if(empty($recentReviews) && empty($submittedReviews)){
                        ?>
                        <div class="lp-blank-section">
                            <div class="col-md-12 blank-left-side">
                                <img alt="image" src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
                                <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
                                <p><?php echo esc_html__('You must be here for the first time. You will see all reviews here.', 'listingpro'); ?></p>
                            </div>
                        </div>
                        <?php
                    }

                    if(!empty($recentReviews)){
                        $args = array(
                            'post_type' => 'lp-reviews',
                            'posts_per_page' => -1,
                            'post__in'	=> $recentReviews,
                            'orderby' => 'date',
                            'order'   => 'DESC',
                            'post_status'	=> 'publish'
                        );
                        $aReviews_query = new WP_Query( $args );

                        if ( $aReviews_query->have_posts() ) {
                            while ( $aReviews_query->have_posts() ) {
                                $aReviews_query->the_post();
                                $authorid = $aReviews_query->post_author;
                                $review_post = listing_get_metabox_by_ID('listing_id', get_the_ID());
                                $review_post_title = get_the_title($review_post);
                                $rating = listing_get_metabox_by_ID('rating' ,get_the_ID());
                                if(!empty($rating)){
                                    $rate = $rating;
                                }
                                else{
                                    $rate = 0;
                                }
                                $rating = apply_filters('lp_rating_number_format', $rating );
                                $review_author_email = get_the_author_meta( 'user_email');
                                $review_reply = listing_get_metabox_by_ID('review_reply',get_the_ID());
                                $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                                ?>

                                <div class="lp-reviews-inner-container">

                                    <div class="lp-review-detail-container">
                                        <div class="clearfix">
                                            <div class="lp-reviewer-name pull-left">
                                                <div class="lp-reviewer-image">
                                                </div>
                                                <div class="lp-reviewer-info">
                                                    <h4><?php echo get_the_author_meta('display_name'); ?> <span><?php echo esc_html__(' posted on ', 'listingpro').$review_post_title; ?></span></h4>
                                                    <!--<h5>Irving, TX</h5>-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="lp-review-details">
                                            <div class="lp-review-count">
                                                <p>
                                                    <?php
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
                                                    }?>
                                                    <span> <?php	echo esc_attr($rate);?></span>
                                                    <span class="lp-revies-timing"><?php echo get_the_date(); ?></span>
                                                </p>

                                            </div>
                                            <p class="lp-review-des"><?php echo get_the_content(); ?> </p>

                                            <?php if( !empty($gallery) ){ ?>
                                                <div class="clearfix">
                                                    <ul class="lp-reviewr-images clearfix review-img-slider">
                                                        <?php
                                                        //image gallery
                                                        $imagearray = explode(',', $gallery);
                                                        //new code 2.6.15
                                                        $imgurl = '';
                                                        if(!empty($imagearray)){
                                                            foreach( $imagearray as $image ){
                                                                $imgGal = wp_get_attachment_image( $image, 'listingpro-review-gallery-thumb', '', '' );
                                                                $imgGalFull = wp_get_attachment_image_src( $image,  'full');
                                                                if(!empty($imgGalFull)){
                                                                   $imgurl = $imgGalFull[0];
                                                                }
                                                                echo '<li><a class="galImgFull" href="'.$imgurl.'">'.$imgGal.'</a></li>';
                                                            }
                                                        }
                                                        //end new code 2.6.15
                                                        ?>

                                                    </ul>
                                                </div>
                                            <?php } ?>
                                            <!--reply button-->
                                            <?php
                                            if( !empty($review_reply) ){ ?>
                                                <div class="lp-owner-reply-outer clearfix">
                                                    <div class="lp-owner-image">
                                                        <img src="<?php echo listingpro_author_image(); ?>" alt="image" />
                                                    </div>
                                                    <div class="lp-owner-reply">
                                                        <h4><?php echo esc_attr($loggedinusername); ?></h4>
                                                        <p><?php echo esc_attr($review_reply); ?></p>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </div>

                                    </div>


                                </div>


                                <?php
                            }
                            wp_reset_postdata();
                        }
                    }
                    ?>

                    <!--submitted reviews -->

                    <?php

                    if(!empty($submittedReviews)){
                        $args = array(
                            'post_type' => 'lp-reviews',
                            'posts_per_page' => -1,
                            'post__in'	=> $submittedReviews,
                            'orderby' => 'date',
                            'order'   => 'DESC',
                            'post_status'	=> 'publish'
                        );
                        $sReviews_query = new WP_Query( $args );

                        if ( $sReviews_query->have_posts() ) {
                            while ( $sReviews_query->have_posts() ) {
                                $sReviews_query->the_post();
                                $authorid = $sReviews_query->post_author;
                                $review_post = listing_get_metabox_by_ID('listing_id', get_the_ID());
                                $review_post_title = get_the_title($review_post);
                                $rating = listing_get_metabox_by_ID('rating' ,get_the_ID());
                                if(!empty($rating)){
                                    $rate = $rating;
                                }
                                else{
                                    $rate = 0;
                                }
                                $rating = apply_filters('lp_rating_number_format', $rating );
                                $review_author_email = get_the_author_meta( 'user_email');
                                $review_reply = listing_get_metabox_by_ID('review_reply',get_the_ID());
                                $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                                ?>

                                <div class="lp-reviews-inner-container">

                                    <div class="lp-review-detail-container">
                                        <div class="clearfix">
                                            <div class="lp-reviewer-name pull-left">
                                                <div class="lp-reviewer-image">
                                                </div>
                                                <div class="lp-reviewer-info">
                                                    <h4><?php echo get_the_author_meta('display_name'); ?> <span><?php echo esc_html__(' posted on ', 'listingpro').$review_post_title; ?></span></h4>
                                                    <!--<h5>Irving, TX</h5>-->
                                                </div>
                                            </div>

                                        </div>
                                        <div class="lp-review-details">
                                            <div class="lp-review-count">
                                                <p>
                                                    <?php
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
                                                    }?>
                                                    <span> <?php	echo esc_attr($rate);?></span>
                                                    <span class="lp-revies-timing"><?php echo get_the_date(); ?></span>
                                                </p>

                                            </div>
                                            <p class="lp-review-des"><?php echo get_the_content(); ?> </p>
                                            <?php if( !empty($gallery) ){ ?>
                                                <div class="clearfix">
                                                    <ul class="lp-reviewr-images clearfix review-img-slider">
                                                        <?php
                                                        //image gallery
                                                        $imagearray = explode(',', $gallery);
                                                        //new code 2.6.15
                                                        $imgurl = '';
                                                        if(!empty($imagearray)){
                                                            foreach( $imagearray as $image ){
                                                                $imgGal = wp_get_attachment_image( $image, 'listingpro-review-gallery-thumb', '', '' );
                                                                $imgGalFull = wp_get_attachment_image_src( $image,  'full');
                                                                if(!empty($imgGalFull)){
                                                                   $imgurl = $imgGalFull[0];
                                                                }
                                                                echo '<li><a class="galImgFull" href="'.$imgurl.'">'.$imgGal.'</a></li>';
                                                            }
                                                        }
                                                        //end new code 2.6.15
                                                        ?>

                                                    </ul>
                                                </div>
                                            <?php } ?>


                                        </div>

                                    </div>


                                </div>


                                <?php
                            }
                            wp_reset_postdata();
                        }
                    }
                    ?>


                </div>
                <div class="tab-pane fade" id="tab2default">

                    <!--received-->
                    <?php
                    if(!empty($recentReviews)){
                        $args = array(
                            'post_type' => 'lp-reviews',
                            'posts_per_page' => -1,
                            'post__in'	=> $recentReviews,
                            'orderby' => 'date',
                            'order'   => 'DESC',
                            'post_status'	=> 'publish'
                        );
                        $aReviews_query = new WP_Query( $args );

                        if ( $aReviews_query->have_posts() ) {
                            while ( $aReviews_query->have_posts() ) {
                                $aReviews_query->the_post();
                                $authorid = $aReviews_query->post_author;
                                $review_post = listing_get_metabox_by_ID('listing_id', get_the_ID());
                                $review_post_title = get_the_title($review_post);
                                $rating = listing_get_metabox_by_ID('rating' ,get_the_ID());
                                if(!empty($rating)){
                                    $rate = $rating;
                                }
                                else{
                                    $rate = 0;
                                }
                                $rating = apply_filters('lp_rating_number_format', $rating );
                                $review_author_email = get_the_author_meta( 'user_email');
                                $review_reply = listing_get_metabox_by_ID('review_reply',get_the_ID());
                                $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                                ?>

                                <div class="lp-reviews-inner-container">

                                    <div class="lp-review-detail-container">
                                        <div class="clearfix">
                                            <div class="lp-reviewer-name pull-left">
                                                <div class="lp-reviewer-image">
                                                </div>
                                                <div class="lp-reviewer-info">
                                                    <h4><?php echo get_the_author_meta('display_name'); ?> <span><?php echo esc_html__(' posted on ', 'listingpro').$review_post_title; ?></span></h4>
                                                    <!--<h5>Irving, TX</h5>-->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="lp-review-details">
                                            <div class="lp-review-count">
                                                <p>
                                                    <?php
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
                                                    }?>
                                                    <span> <?php	echo esc_attr($rate);?></span>
                                                    <span class="lp-revies-timing"><?php echo get_the_date(); ?></span>
                                                </p>

                                            </div>
                                            <p class="lp-review-des"><?php echo get_the_content(); ?> </p>

                                            <?php if( !empty($gallery) ){ ?>
                                                <div class="clearfix">
                                                    <ul class="lp-reviewr-images clearfix review-img-slider">
                                                        <?php
                                                        //image gallery
                                                        $imagearray = explode(',', $gallery);
                                                        //new code 2.6.15
                                                        $imgurl = '';
                                                        if(!empty($imagearray)){
                                                            foreach( $imagearray as $image ){
                                                                $imgGal = wp_get_attachment_image( $image, 'listingpro-review-gallery-thumb', '', '' );
                                                                $imgGalFull = wp_get_attachment_image_src( $image,  'full');
                                                                if(!empty($imgGalFull)){
                                                                   $imgurl = $imgGalFull[0];
                                                                }
                                                                echo '<li><a class="galImgFull" href="'.$imgurl.'">'.$imgGal.'</a></li>';
                                                            }
                                                        }
                                                        //end new code 2.6.15
                                                        ?>

                                                    </ul>
                                                </div>
                                            <?php } ?>
                                            <!--reply button-->
                                            <?php
                                            if( !empty($review_reply) ){ ?>
                                                <div class="lp-owner-reply-outer clearfix">
                                                    <div class="lp-owner-image">
                                                        <img src="<?php echo listingpro_author_image(); ?>" alt="image" />
                                                    </div>
                                                    <div class="lp-owner-reply">
                                                        <h4><?php echo esc_attr($loggedinusername); ?></h4>
                                                        <p><?php echo esc_attr($review_reply); ?></p>
                                                    </div>
                                                </div>
                                                <?php
                                            }else{ ?>
                                                <div class="lp-public-reply-btn clearfix">
                                                    <a href="" class="open-reply closeddd"><?php echo esc_html__('Public reply','listingpro'); ?> </a>
                                                    <div class="post_response margin-top-30">
                                                        <form name="post_response" method="post" action="">
                                                            <textarea placeholder="<?php echo esc_html__('Write something in reply of this review', 'listingpro'); ?>" id="<?php the_ID();  ?>" name="review_reply" class="review_reply" style="width:100%"></textarea>
                                                            <input type="submit" value="<?php echo esc_html__('Send Reply', 'listingpro'); ?>" name="submit_response" class="lp-review-reply-btn">
                                                            <input type="hidden" value="<?php the_ID();  ?>" name="rewID">
                                                            <input type="hidden" value="<?php echo esc_attr($userEmail); ?>" name="userEmail">
                                                            <input type="hidden" value="<?php echo esc_attr($username); ?>" name="userName">
                                                            <input type="hidden" value="<?php echo esc_attr($review_author_email); ?>" name="authEmail">
                                                            <input type="hidden" value="<?php echo current_time('mysql'); ?>" name="reviewTime">
                                                        </form>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>

                                        </div>

                                    </div>


                                </div>


                                <?php
                            }
                            wp_reset_postdata();
                        }
                    }else{ ?>
                        <div class="lp-blank-section">
                            <div class="col-md-12 blank-left-side">
                                <img alt="image" src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
                                <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
                                <p class="margin-bottom-20"><?php echo esc_html__('You must be here for the first time. You will see here all recieved reviews on your own listings.', 'listingpro'); ?></p>
                            </div>

                        </div>
                    <?php } ?>
                </div>
                <div class="tab-pane fade in <?php echo esc_attr($ActClass); ?>" id="tab3default">

                    <!--submitted-->
                    <?php

                    if(!empty($submittedReviews)){
                        $args = array(
                            'post_type' => 'lp-reviews',
                            'posts_per_page' => -1,
                            'post__in'	=> $submittedReviews,
                            'orderby' => 'date',
                            'order'   => 'DESC',
                            'post_status'	=> 'publish'
                        );
                        $sReviews_query = new WP_Query( $args );

                        if ( $sReviews_query->have_posts() ) {
                            while ( $sReviews_query->have_posts() ) {
                                $sReviews_query->the_post();
                                $authorid = $sReviews_query->post_author;
                                $review_post = listing_get_metabox_by_ID('listing_id', get_the_ID());
                                $review_post_title = get_the_title($review_post);
                                $rating = listing_get_metabox_by_ID('rating' ,get_the_ID());
                                if(!empty($rating)){
                                    $rate = $rating;
                                }
                                else{
                                    $rate = 0;
                                }
                                $rating = apply_filters('lp_rating_number_format', $rating );
                                $review_author_email = get_the_author_meta( 'user_email');
                                $review_reply = listing_get_metabox_by_ID('review_reply',get_the_ID());
                                $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                                ?>

                                <div class="lp-reviews-inner-container">

                                    <div class="lp-review-detail-container">
                                        <div class="clearfix">
                                            <div class="lp-reviewer-name pull-left">
                                                <div class="lp-reviewer-image">
                                                </div>
                                                <div class="lp-reviewer-info">
                                                    <h4><?php echo get_the_author_meta('display_name'); ?> <span><?php echo esc_html__(' posted on ', 'listingpro').$review_post_title; ?></span></h4>
                                                    <!--<h5>Irving, TX</h5>-->
                                                </div>
                                            </div>

                                        </div>
                                        <div class="lp-review-details">
                                            <div class="lp-review-count">
                                                <p>
                                                    <?php
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
                                                    }?>
                                                    <span> <?php	echo esc_attr($rate);?></span>
                                                    <span class="lp-revies-timing"><?php echo get_the_date(); ?></span>
                                                </p>

                                            </div>
                                            <p class="lp-review-des"><?php echo get_the_content(); ?> </p>
                                            <?php if( !empty($gallery) ){ ?>
                                                <div class="clearfix">
                                                    <ul class="lp-reviewr-images clearfix review-img-slider">
                                                        <?php
                                                        //image gallery
                                                        $imagearray = explode(',', $gallery);
                                                        //new code 2.6.15
                                                        $imgurl = '';
                                                        if(!empty($imagearray)){
                                                            foreach( $imagearray as $image ){
                                                                $imgGal = wp_get_attachment_image( $image, 'listingpro-review-gallery-thumb', '', '' );
                                                                $imgGalFull = wp_get_attachment_image_src( $image,  'full');
                                                                if(!empty($imgGalFull)){
                                                                   $imgurl = $imgGalFull[0];
                                                                }
                                                                echo '<li><a class="galImgFull" href="'.$imgurl.'">'.$imgGal.'</a></li>';
                                                            }
                                                        }
                                                        //end new code 2.6.15
                                                        ?>

                                                    </ul>
                                                </div>
                                            <?php } ?>

                                            <div class="review-content">
                                                <div class="reviews lp-public-reply-btn clearfix">
                                                    <a href="#" data-reviewid="<?php echo get_the_ID(); ?>" class="delete-my-review"><?php echo esc_html__('Delete this review','listingpro'); ?></a>
                                                    <a href="#" class="open-reply"><?php echo esc_html__('Edit this review','listingpro'); ?></a>
                                                    <div class='post_response'>
                                                        <?php echo get_template_part('templates/dashboard/review-edit');?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>


                                </div>


                                <?php
                            }
                            wp_reset_postdata();
                        }
                    }else{ ?>
                        <div class="lp-blank-section">
                            <div class="col-md-12 blank-left-side">
                                <img alt="image" src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
                                <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
                                <p class="margin-bottom-20"><?php echo esc_html__('You must be here for the first time. You will see all posted reviews here.', 'listingpro'); ?></p>
                            </div>

                        </div>
                    <?php } ?>
                </div>

            </div>
        </div>
    </div>