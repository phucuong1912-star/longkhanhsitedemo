<?php
if (!function_exists('listingpro_get_all_reviews')) {
    function listingpro_get_all_reviews($postid)
    {

        global $listingpro_options;
        $showReport = true;
        if (isset($listingpro_options['lp_detail_page_review_report_button'])) {
            if ($listingpro_options['lp_detail_page_review_report_button'] == 'off') {
                $showReport = false;
            }
        }
        $lp_multi_rating_state        =   $listingpro_options['lp_multirating_switch'];

        if ($lp_multi_rating_state == 1 && !empty($lp_multi_rating_state)) {
            $lp_multi_rating_fields_active    =    array();
            for ($x = 1; $x <= 5; $x++) {
                $lp_multi_rating_fields =   get_listing_multi_ratings_fields($postid);
            }
        }

?>

        <?php
        $currentUserId = get_current_user_id();
        $key = 'reviews_ids';
        $review_idss = listing_get_metabox_by_ID($key, $postid);
        $review_ids = '';
        if (!empty($review_idss)) {
            $review_ids = explode(",", $review_idss);
        }

        $active_reviews_ids = array();
        if (!empty($review_ids) && is_array($review_ids)) {
            $review_ids = array_unique($review_ids);
            foreach ($review_ids as $reviewID) {
                if (get_post_status($reviewID) == "publish" && !empty($reviewID)) {
                    $active_reviews_ids[] = $reviewID;
                }
            }
            if (count($active_reviews_ids) == 1) {
                $label = esc_html__('Review for ', 'listingpro') . get_the_title($postid);
            } else {
                $label = esc_html__('Reviews for ', 'listingpro') . get_the_title($postid);
            }
            $colclass = 'col-md-12';
            $reviewFilter = false;
            if (lp_theme_option('lp_listing_reviews_orderby') == 'on') {
                $colclass = 'col-md-8';
                $reviewFilter = true;
            }
        ?>
            <div class="row">
                <div class="<?php echo esc_attr($colclass); ?>">
                    <?php
                    echo '<h4 class="lp-total-reviews">' . count($active_reviews_ids) . ' ' . $label . '</h4>';
                    ?>
                </div>
                <?php
                if (!empty($reviewFilter)) { ?>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sel1"><?php echo esc_html__('Filter By : ', 'listingpro'); ?></label>
                            <select class="form-control" id="lp_reivew_drop_filter">
                                <option value="DESC"><?php echo esc_html__('Newest', 'listingpro'); ?></option>
                                <option value="ASC"><?php echo esc_html__('Oldest', 'listingpro'); ?></option>
                                <option value="listing_rate"><?php echo esc_html__('Highest Rated', 'listingpro'); ?></option>
                                <option value="listing_rate_lowest"><?php echo esc_html__('Lowest Rated', 'listingpro'); ?></option>

                            </select>
                        </div>
                        <div class="review-filter-loader">
                            <img alt="image" src="<?php echo THEME_DIR . '/assets/images/search-load.gif' ?>">
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <?php
        } else {
        }

        $reviewOrder = 'DESC';
        if (!empty($review_ids) && count($review_ids) > 0) {
            $review_ids = array_reverse($review_ids, true);
            echo '<div class="reviews-section">';
            //foreach( $review_ids as $key=>$review_id ){
            $args = array(
                'post_type'  => 'lp-reviews',
                'orderby'    => 'date',
                'order'      => $reviewOrder,
                'post__in'     => $review_ids,
                'post_status'    => 'publish',
                'posts_per_page'    => -1
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                echo '';
                while ($query->have_posts()) {
                    $query->the_post();
                    global $post;
                    echo '<article class="review-post">';
                    // moin here strt
                    $review_reply = '';
                    $review_reply = listing_get_metabox_by_ID('review_reply', get_the_ID());


                    $review_reply_time = listing_get_metabox_by_ID('review_reply_time', get_the_ID());
                    // moin here ends

                    $rating = listing_get_metabox_by_ID('rating', get_the_ID());
                    $rating = apply_filters('lp_rating_number_format', $rating);
                    $exRating = get_post_meta(get_the_ID(), 'rating', true);
                    if (empty($exRating)) {
                        update_post_meta(get_the_ID(), 'rating', $rating);
                    }
                    $rate = $rating;
                    $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                    $author_id = $post->post_author;

                    $author_avatar_url = get_user_meta($author_id, "listingpro_author_img_url", true);
                    $avatar;
                    if (!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url($author_id, $size = '94');
                        $avatar =  $avatar_url;
                    }
                    $user_reviews_count = count_user_posts($author_id, 'lp-reviews');
            ?>
                    <figure>
                        <div class="review-thumbnail">
                            <a href="<?php echo get_author_posts_url($author_id); ?>">
                                <img src="<?php echo esc_attr($avatar); ?>" alt="image">
                            </a>
                        </div>
                        <figcaption>
                            <h4><a href="<?php echo get_author_posts_url($author_id); ?>"><?php the_author(); ?></a></h4>
                            <p><i class="fa fa-star"></i> <?php echo esc_attr($user_reviews_count); ?> <?php esc_html_e('Reviews', 'listingpro'); ?></p>
                        </figcaption>
                    </figure>
                    <section class="details">
                        <div class="top-section">
                            <h3><?php the_title(); ?></h3>
                            <!-- New update 2.6.10 -->
                            <time><?php echo get_the_time(get_option('date_format')) . ' ' . get_the_time(get_option('time_format')); ?></time>
                            <!-- End New update 2.6.10 -->
                            <div class="review-count">
                                <?php
                                if ($lp_multi_rating_state == 1 && !empty($lp_multi_rating_state)) {
                                    $post_rating_data   =   get_post_meta($post->ID, 'lp_listingpro_options', true);
                                    $lp_multi_rating_fields_count =   0;
                                    $show_multi_rate_drop   =   false;
                                    if (is_array($lp_multi_rating_fields) || is_object($lp_multi_rating_fields)) {
                                        $lp_multi_rating_fields_count   =   count($lp_multi_rating_fields);
                                    }
                                    if ($lp_multi_rating_fields_count > 0) {
                                        if (array_key_exists(0, $post_rating_data)) {
                                            $show_multi_rate_drop   =   true;
                                        }
                                    }
                                    if ($show_multi_rate_drop) {
                                        echo '<a href="#" data-rate-box="multi-box-' . $post->ID . '" class="open-multi-rate-box"><i class="fa fa-chevron-down" aria-hidden="true"></i>' . esc_html__('View All', 'listingpro') . '</a>';

                                ?>
                                        <div class="lp-multi-star-wrap" id="multi-box-<?php echo esc_attr($post->ID); ?>">
                                            <?php
                                            if (count($lp_multi_rating_fields) > 0) {
                                                foreach ($lp_multi_rating_fields as $k => $v) {
                                                    $field_rating_val   =   '';
                                                    if (isset($post_rating_data[$k])) {
                                                        $field_rating_val   =   $post_rating_data[$k];
                                                    }
                                            ?>
                                                    <div class="lp-multi-star-field rating-with-colors <?php echo review_rating_color_class($field_rating_val); ?>">
                                                        <label><?php echo esc_attr($v);  ?></label>
                                                        <p>
                                                            <i class="fa <?php if ($field_rating_val > 0) {
                                                                                echo 'fa-star';
                                                                            } else {
                                                                                echo 'fa-star-o';
                                                                            } ?>" aria-hidden="true"></i>
                                                            <i class="fa <?php if ($field_rating_val > 1) {
                                                                                echo 'fa-star';
                                                                            } else {
                                                                                echo 'fa-star-o';
                                                                            } ?>" aria-hidden="true"></i>
                                                            <i class="fa <?php if ($field_rating_val > 2) {
                                                                                echo 'fa-star';
                                                                            } else {
                                                                                echo 'fa-star-o';
                                                                            } ?>" aria-hidden="true"></i>
                                                            <i class="fa <?php if ($field_rating_val > 3) {
                                                                                echo 'fa-star';
                                                                            } else {
                                                                                echo 'fa-star-o';
                                                                            } ?>" aria-hidden="true"></i>
                                                            <i class="fa <?php if ($field_rating_val > 4) {
                                                                                echo 'fa-star';
                                                                            } else {
                                                                                echo 'fa-star-o';
                                                                            } ?>" aria-hidden="true"></i>
                                                        </p>
                                                    </div>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                                <?php

                                $review_rating = listing_get_metabox_by_ID('rating', get_the_ID());
                                $review_rating = apply_filters('lp_rating_number_format', $review_rating);

                                ?>
                                <div class="rating rating-with-colors <?php echo review_rating_color_class($review_rating); ?>">
                                    <?php
                                    listingpro_ratings_stars('rating', get_the_ID());
                                    ?>
                                </div>
                                <?php echo lp_cal_listing_rate(get_the_ID(), 'lp_review', true); ?>
                            </div>
                        </div>
                        <div class="content-section">
                            <p><?php the_content(); ?></p>
                            <?php if (!empty($gallery)) {
                                $imagearray = explode(',', $gallery);
                                $imagearray_count = count($imagearray);
                                if ($imagearray_count > 0) {
                                    require_once(THEME_PATH . "/include/aq_resizer.php");
                            ?>
                                    <div class="images-gal-section">
                                        <div class="row">
                                            <div class="img-col review-img-slider">
                                                <?php
                                                //image gallery
                                                $imgurl = '';
                                                if (!empty($imagearray)) {
                                                    foreach ($imagearray as $image) {
                                                        $imgGal = wp_get_attachment_image($image, 'listingpro-review-gallery-thumb', '', '');
                                                        $imgGalFull = wp_get_attachment_image_src($image, 'full');
                                                        if (!empty($imgGalFull) && isset($imgGalFull)) {
                                                            $imgGalThum = aq_resize($imgGalFull[0], '150', '115', true, true, true);
                                                            if (isset($imgGalFull[0])) {
                                                                $imgurl = $imgGalFull[0];
                                                            } else {
                                                                $imgurl = '';
                                                            }
                                                            echo '<a class="galImgFull" href="' . $imgurl . '" rel="prettyPhoto[gallery2]"><img src="' . $imgGalThum . '" alt="image"></a>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <?php
                            $interests = '';
                            $Lols = '';
                            $loves = '';
                            $interVal = esc_html__('Interesting', 'listingpro');
                            $lolVal = esc_html__('Lol', 'listingpro');
                            $loveVal = esc_html__('Love', 'listingpro');

                            $interests = listing_get_metabox_by_ID('review_' . $interVal . '', get_the_ID());
                            $Lols = listing_get_metabox_by_ID('review_' . $lolVal . '', get_the_ID());
                            $loves = listing_get_metabox_by_ID('review_' . $loveVal . '', get_the_ID());


                            if (empty($interests)) {
                                $interests = 0;
                            }
                            if (empty($Lols)) {
                                $Lols = 0;
                            }
                            if (empty($loves)) {
                                $loves = 0;
                            }
                            ?>
                            <div class="bottom-section">
                                <form action="#">
                                    <span><?php echo esc_html__('Was this review ...?', 'listingpro'); ?></span>
                                    <ul>
                                        <li>
                                            <a class="instresting reviewRes" href="#" data-reacted="<?php echo esc_html__('You already reacted', 'listingpro'); ?>" data-restype='<?php echo esc_attr($interVal); ?>' data-id='<?php the_ID(); ?>' data-score='<?php echo esc_attr($interests); ?>'>
                                                <i class="fa fa-thumbs-o-up"></i><?php echo esc_html__('Interesting', 'listingpro'); ?><span class="interests-score"><?php if (!empty($interests)) echo esc_attr($interests); ?></span>
                                                <span class="lp_state"></span>
                                            </a>

                                        </li>
                                        <li>
                                            <a class="lol reviewRes" href="#" data-reacted="<?php echo esc_html__('You already reacted', 'listingpro'); ?>" data-restype='<?php echo esc_attr($lolVal); ?>' data-id='<?php the_ID(); ?>' data-score='<?php echo esc_attr($Lols); ?>'>
                                                <i class="fa fa-smile-o"></i><?php echo esc_html__('Lol', 'listingpro'); ?><span class="interests-score"><?php if (!empty($Lols)) echo esc_attr($Lols); ?></span>
                                                <span class="lp_state"></span>
                                            </a>

                                        </li>
                                        <li>
                                            <a class="love reviewRes" href="#" data-reacted="<?php echo esc_html__('You already reacted', 'listingpro'); ?>" data-restype='<?php echo esc_attr($loveVal); ?>' data-id='<?php the_ID(); ?>' data-id='<?php the_ID(); ?>' data-score='<?php echo esc_attr($loves); ?>'>
                                                <i class="fa fa-heart-o"></i><?php echo esc_html__('Love', 'listingpro'); ?><span class="interests-score"><?php if (!empty($loves)) echo esc_attr($loves); ?></span>
                                                <span class="lp_state"></span>
                                            </a>

                                        </li>
                                        <?php
                                        if ($showReport == true && is_user_logged_in()) { ?>
                                            <!-- new code 2.6.15 -->
                                            <li id="lp-report-review">
                                                <a data-toggle="modal" data-target="#lp_report_listing" data-postid="<?php echo get_the_ID(); ?>" data-reportedby="<?php echo esc_attr($currentUserId); ?>" data-posttype="reviews" href="#" id="lp-report-this-review-popup" class="report"><i class="fa fa-flag" aria-hidden="true"></i><?php esc_html_e('Report', 'listingpro'); ?></a>
                                            </li>
                                            <!-- end new code 2.6.15 -->
                                        <?php } ?>
                                    </ul>
                                </form>
                            </div>
                        </div>
                    </section>

                    <?php if (!empty($review_reply)) { ?>
                        <section class="details detail-sec">
                            <div class="owner-response">
                                <h3><?php esc_html_e('Owner Response', 'listingpro'); ?></h3>
                                <?php
                                if (!empty($review_reply_time)) { ?>
                                    <time><?php echo date_i18n("F j, Y h:i:s a", strtotime($review_reply_time)); ?></time>
                                <?php } ?>
                                <p><?php echo esc_attr($review_reply); ?></p>

                            </div>
                        </section>
                    <?php } ?>
                    <!-- moin here ends-->
        <?php
                    echo '</article>';
                }
                echo '';
                wp_reset_postdata();
            } else {
            }
            //}
            echo '</div>';
        }
    }
}



if (!function_exists('listingpro_get_all_reviews_app_view')) {
    function listingpro_get_all_reviews_app_view($postid)
    {

        global $listingpro_options;
        $showReport = true;
        if (isset($listingpro_options['lp_detail_page_review_report_button'])) {
            if ($listingpro_options['lp_detail_page_review_report_button'] == 'off') {
                $showReport = false;
            }
        }
        $lp_multi_rating_state        =   $listingpro_options['lp_multirating_switch'];

        if ($lp_multi_rating_state == 1 && !empty($lp_multi_rating_state)) {
            $lp_multi_rating_fields =   get_listing_multi_ratings_fields($postid);
        }
        ?>
        <?php
        $key = 'reviews_ids';
        $review_idss = listing_get_metabox_by_ID($key, $postid);
        $review_ids = '';
        $currentUserId = get_current_user_id();
        if (!empty($review_idss)) {
            $review_ids = explode(",", $review_idss);
        }
        $active_reviews_ids = array();
        if (!empty($review_ids) && is_array($review_ids)) {
            $review_ids = array_unique($review_ids);
            foreach ($review_ids as $reviewID) {
                if (get_post_status($reviewID) == "publish" && !empty($reviewID)) {
                    $active_reviews_ids[] = $reviewID;
                }
            }
            if (count($active_reviews_ids) == 1) {
                $label = esc_html__('Review for ', 'listingpro') . get_the_title($postid);
            } else {
                $label = esc_html__('Reviews for ', 'listingpro') . get_the_title($postid);
            }
            //echo '<h3 class="comment-reply-title">'.count($active_reviews_ids).' '.$label.'</h3>';
        } else {
        }
        if (!empty($review_ids) && count($review_ids) > 0) {
            $review_ids = array_reverse($review_ids, true);
            echo '<div class="reviews-section">';
            //foreach( $review_ids as $key=>$review_id ){
            $args = array(
                'post_type'  => 'lp-reviews',
                'orderby'    => 'date',
                'order'      => 'ASC',
                'post__in'   => $review_ids,
                'post_status'   => 'publish',
                'posts_per_page'    => -1
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                echo '';
                while ($query->have_posts()) {
                    $query->the_post();
                    global $post;
                    echo '<article class="review-post">';
                    // moin here strt
                    $review_reply = '';
                    $review_reply = listing_get_metabox_by_ID('review_reply', get_the_ID());

                    $review_reply_time = listing_get_metabox_by_ID('review_reply_time', get_the_ID());
                    // moin here ends
                    $rating = listing_get_metabox_by_ID('rating', get_the_ID());
                    $rating = apply_filters('lp_rating_number_format', $rating);
                    $rate = $rating;
                    $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                    $author_id = $post->post_author;
                    $author_avatar_url = get_user_meta($author_id, "listingpro_author_img_url", true);
                    $avatar;
                    if (!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url($author_id, $size = '94');
                        $avatar =  $avatar_url;
                    }
                    $user_reviews_count = count_user_posts($author_id, 'lp-reviews');
        ?>
                    <figure>
                        <div class="clearfix">
                            <div class="review-thumbnail">
                                <a href="<?php echo get_author_posts_url($author_id); ?>">
                                    <img src="<?php echo esc_attr($avatar); ?>" alt="image">
                                </a>
                            </div>
                            <figcaption>
                                <h3><?php the_title(); ?></h3>
                                <time><?php echo get_the_time(get_option('date_format')); ?></time>
                                <div class="review-count">
                                    <?php
                                    if ($lp_multi_rating_state == 1 && !empty($lp_multi_rating_state)) {
                                        $post_rating_data   =   get_post_meta($post->ID, 'lp_listingpro_options', true);
                                        $show_multi_rate_drop   =   false;
                                        if (count($lp_multi_rating_fields) > 0) {
                                            if (array_key_exists(0, $post_rating_data)) {
                                                $show_multi_rate_drop   =   true;
                                            }
                                        }
                                        if ($show_multi_rate_drop) {
                                            echo '<a href="#" data-rate-box="multi-box-' . $post->ID . '" class="open-multi-rate-box"><i class="fa fa-chevron-down" aria-hidden="true"></i>' . esc_html__('View All', 'listingpro') . '</a>';
                                    ?>
                                            <div class="lp-multi-star-wrap" id="multi-box-<?php echo esc_attr($post->ID); ?>">
                                                <?php
                                                if (count($lp_multi_rating_fields) > 0) {
                                                    foreach ($lp_multi_rating_fields as $k => $v) {
                                                        $field_rating_val   =   '';
                                                        if (isset($post_rating_data[$k])) {
                                                            $field_rating_val   =   $post_rating_data[$k];
                                                        }
                                                ?>
                                                        <div class="lp-multi-star-field rating-with-colors <?php echo review_rating_color_class($field_rating_val); ?>">
                                                            <label><?php echo esc_attr($v);  ?></label>
                                                            <p>
                                                                <i class="fa <?php if ($field_rating_val > 0) {
                                                                                    echo 'fa-star';
                                                                                } else {
                                                                                    echo 'fa-star-o';
                                                                                } ?>" aria-hidden="true"></i>
                                                                <i class="fa <?php if ($field_rating_val > 1) {
                                                                                    echo 'fa-star';
                                                                                } else {
                                                                                    echo 'fa-star-o';
                                                                                } ?>" aria-hidden="true"></i>
                                                                <i class="fa <?php if ($field_rating_val > 2) {
                                                                                    echo 'fa-star';
                                                                                } else {
                                                                                    echo 'fa-star-o';
                                                                                } ?>" aria-hidden="true"></i>
                                                                <i class="fa <?php if ($field_rating_val > 3) {
                                                                                    echo 'fa-star';
                                                                                } else {
                                                                                    echo 'fa-star-o';
                                                                                } ?>" aria-hidden="true"></i>
                                                                <i class="fa <?php if ($field_rating_val > 4) {
                                                                                    echo 'fa-star';
                                                                                } else {
                                                                                    echo 'fa-star-o';
                                                                                } ?>" aria-hidden="true"></i>
                                                            </p>
                                                        </div>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                    <?php
                                        }
                                    }
                                    ?>
                                    <?php

                                    $review_rating = listing_get_metabox_by_ID('rating', get_the_ID());
                                    $review_rating = apply_filters('lp_rating_number_format', $review_rating);

                                    ?>
                                    <div class="rating rating-with-colors <?php echo review_rating_color_class($review_rating); ?>">
                                        <?php
                                        listingpro_ratings_stars('rating', get_the_ID());
                                        ?>
                                    </div>
                                    <?php echo lp_cal_listing_rate(get_the_ID(), 'lp_review', true); ?>
                                </div>
                            </figcaption>
                        </div>
                    </figure>
                    <section class="details">
                        <div class="content-section">
                            <p><?php the_content(); ?></p>
                            <?php if (!empty($gallery)) {
                                $imagearray = explode(',', $gallery);
                                $imagearray_count = count($imagearray);
                                if ($imagearray_count > 0) {
                                    require_once(THEME_PATH . "/include/aq_resizer.php");
                            ?>
                                    <div class="images-gal-section">
                                        <div class="row">
                                            <div class="img-col review-img-slider">
                                                <?php
                                                //image gallery
                                                $imgurl = '';
                                                if (!empty($imagearray)) {
                                                    foreach ($imagearray as $image) {
                                                        $imgGal = wp_get_attachment_image($image, 'listingpro-review-gallery-thumb', '', '');
                                                        $imgGalFull = wp_get_attachment_image_src($image, 'full');
                                                        if (!empty($imgGalFull) && isset($imgGalFull)) {
                                                            $imgGalThum = aq_resize($imgGalFull[0], '150', '115', true, true, true);
                                                            if (isset($imgGalFull[0])) {
                                                                $imgurl = $imgGalFull[0];
                                                            } else {
                                                                $imgurl = '';
                                                            }
                                                            echo '<a class="galImgFull" href="' . $imgurl . '" rel="prettyPhoto[gallery2]"><img src="' . $imgGalThum . '" alt="image"></a>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <?php
                            $interests = '';
                            $Lols = '';
                            $loves = '';
                            $interVal = esc_html__('Interesting', 'listingpro');
                            $lolVal = esc_html__('Lol', 'listingpro');
                            $loveVal = esc_html__('Love', 'listingpro');
                            $interests = listing_get_metabox_by_ID('review_' . $interVal . '', get_the_ID());
                            $Lols = listing_get_metabox_by_ID('review_' . $lolVal . '', get_the_ID());
                            $loves = listing_get_metabox_by_ID('review_' . $loveVal . '', get_the_ID());
                            if (empty($interests)) {
                                $interests = 0;
                            }
                            if (empty($Lols)) {
                                $Lols = 0;
                            }
                            if (empty($loves)) {
                                $loves = 0;
                            }
                            ?>
                            <div class="bottom-section">
                                <form action="#">
                                    <span><?php echo esc_html__('Was this review ...?', 'listingpro'); ?></span>
                                    <ul>
                                        <li>
                                            <a class="instresting reviewRes" href="#" data-reacted="<?php echo esc_html__('You already reacted', 'listingpro'); ?>" data-restype='<?php echo esc_attr($interVal); ?>' data-id='<?php the_ID(); ?>' data-score='<?php echo esc_attr($interests); ?>'>
                                                <i class="fa fa-thumbs-o-up"></i><?php echo esc_html__('Interesting', 'listingpro'); ?><span class="interests-score"><?php if (!empty($interests)) echo esc_attr($interests); ?></span>
                                                <span class="lp_state"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="lol reviewRes" href="#" data-reacted="<?php echo esc_html__('You already reacted', 'listingpro'); ?>" data-restype='<?php echo esc_attr($lolVal); ?>' data-id='<?php the_ID(); ?>' data-score='<?php echo esc_attr($Lols); ?>'>
                                                <i class="fa fa-smile-o"></i><?php echo esc_html__('Lol', 'listingpro'); ?><span class="interests-score"><?php if (!empty($Lols)) echo esc_attr($Lols); ?></span>
                                                <span class="lp_state"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="love reviewRes" href="#" data-reacted="<?php echo esc_html__('You already reacted', 'listingpro'); ?>" data-restype='<?php echo esc_attr($loveVal); ?>' data-id='<?php the_ID(); ?>' data-id='<?php the_ID(); ?>' data-score='<?php echo esc_attr($loves); ?>'>
                                                <i class="fa fa-heart-o"></i><?php echo esc_html__('Love', 'listingpro'); ?><span class="interests-score"><?php if (!empty($loves)) echo esc_attr($loves); ?></span>
                                                <span class="lp_state"></span>
                                            </a>
                                        </li>
                                        <?php
                                        if ($showReport == true && is_user_logged_in()) { ?>
                                            <li id="lp-report-review">
                                                <!-- new code 2.6.15 -->
                                                <a data-toggle="modal" data-target="#lp_report_listing" data-postid="<?php echo get_the_ID(); ?>" data-reportedby="<?php echo esc_attr($currentUserId); ?>" data-posttype="reviews" href="#" id="lp-report-this-review-popup" class="report"><i class="fa fa-flag" aria-hidden="true"></i><?php esc_html_e('Report', 'listingpro'); ?></a>
                                                <!-- end new code 2.6.15 -->
                                            </li>
                                        <?php } ?>

                                    </ul>
                                </form>
                            </div>
                        </div>
                    </section>
                    <?php if (!empty($review_reply)) { ?>
                        <section class="details detail-sec">
                            <div class="owner-response">
                                <h3><?php esc_html_e('Owner Response', 'listingpro'); ?></h3>
                                <?php
                                if (!empty($review_reply_time)) { ?>
                                    <time><?php echo date_i18n("F j, Y h:i:s a", strtotime($review_reply_time)); ?></time>
                                <?php } ?>
                                <p><?php echo esc_attr($review_reply); ?></p>
                            </div>
                        </section>
                    <?php } ?>
                    <!-- moin here ends-->
<?php
                    echo '</article>';
                }
                echo '';
                wp_reset_postdata();
            } else {
            }
            //}
            echo '</div>';
        }
    }
}

?>