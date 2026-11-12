<?php
if (!function_exists('listingpro_get_reviews_form')) {
    function listingpro_get_reviews_form($postid)
    {
        if (class_exists('ListingReviews')) {

            global $listingpro_options;
            $listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];
            $lp_Reviews_OPT = $listingpro_options['lp_review_submit_options'];
            $gSiteKey = '';
            $gSiteKey = lp_theme_option('lp_recaptcha_site_key');
            $enableCaptcha = lp_check_receptcha('lp_recaptcha_reviews');
            $privacy_policy = $listingpro_options['payment_terms_condition'];
            $privacy_review = $listingpro_options['listingpro_privacy_review'];

            $lp_images_count = '555';
            $lp_images_size = '999999999999999999999999999999999999999999999999999';
            $lp_imagecount_notice = '';
            $lp_imagesize_notice = '';
            if (lp_theme_option('lp_listing_reviews_images_count_switch') == 'yes') {
                $lp_images_count = lp_theme_option('lp_listing_reviews_images_counter');
                $lp_imagecount_notice = esc_html__("Max. allowed images are ", 'listingpro');
                $lp_imagecount_notice .= $lp_images_count;
            }
            if (lp_theme_option('lp_listing_reviews_images_size_switch') == 'yes') {
                $lp_images_size = lp_theme_option('lp_listing_reviews_images_sizes');
                $lp_imagesize_notice = esc_html__('Max. allowed images size is ', 'listingpro');
                $lp_imagesize_notice .= $lp_images_size . esc_html__(' Mb', 'listingpro');
                $lp_images_size = $lp_images_size * 1000000;
            }
            $enableUsernameField = lp_theme_option('lp_register_username');

            $lp_multi_rating_state        =   $listingpro_options['lp_multirating_switch'];
            if ($lp_multi_rating_state == 1 && !empty($lp_multi_rating_state)) {
                $lp_multi_rating_fields =   get_listing_multi_ratings_fields($postid);
            }

            $lp_detail_page_styles  =   $listingpro_options['lp_detail_page_styles'];
            $multi_col_class    =   'col-md-6';
            if ($lp_detail_page_styles == 'lp_detail_page_styles5') {
                $multi_col_class    =   'col-md-3';
            }
            if (is_user_logged_in()) {

?>
                <div class="review-form" id="review-section">
                    <?php if ($listing_mobile_view == 'app_view2') { ?>
                        <h3 id="reply-title" class="comment-reply-title text-center"><i class="fa fa-star" aria-hidden="true"></i> <?php esc_html_e('Add Review', 'listingpro'); ?> </h3>
                    <?php } else { ?>
                        <h3 id="reply-title" class="comment-reply-title"><i class="fa-regular fa-star-o"></i> <?php esc_html_e('Rate us and Write a Review', 'listingpro'); ?> <i class="fa fa-caret-down"></i></h3>
                    <?php } ?>
                    <form data-lp-recaptcha="<?php echo wp_kses_post($enableCaptcha); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>" data-multi-rating="<?php echo esc_attr($lp_multi_rating_state); ?>" id="rewies_form" name="rewies_form" action="" method="post" enctype="multipart/form-data" data-imgcount="<?php echo esc_attr($lp_images_count); ?>" data-imgsize="<?php echo esc_attr($lp_images_size); ?>" data-countnotice="<?php echo esc_attr($lp_imagecount_notice); ?>" data-sizenotice="<?php echo esc_attr($lp_imagesize_notice); ?>">
                        <?php
                        if ($lp_multi_rating_state == 1 && is_array($lp_multi_rating_fields) && !empty($lp_multi_rating_fields)) {
                            echo '<div class="col-md-12 padding-left-0 lp-multi-rating-ui-wrap">';
                            $lp_rating_field_counter    =    1;
                            //New update 2.6.10
                            $switch  = get_option('lp_multirating_switch');
                            if ($switch == 1 && !empty($switch)) {
                                $multi_rating_fileds = $lp_multi_rating_fields;
                            } else {
                                $multi_rating_fileds = $lp_multi_rating_fields['default'];
                            }
                            foreach ($multi_rating_fileds as $k => $lp_multi_rating_field) {
                                //End New update 2.6.10
                        ?>
                                <div class="<?php echo esc_attr($multi_col_class); ?> padding-left-0">
                                    <div class="list-style-none form-review-stars">
                                        <p><?php echo esc_attr($lp_multi_rating_field); ?></p>
                                        <input type="hidden" data-mrf="<?php echo esc_attr($k); ?>" id="review-rating-<?php echo esc_attr($k); ?>" name="rating-<?php echo esc_attr($k); ?>" class="rating-tooltip lp-multi-rating-val" data-filled="fa fa-star fa-2x" data-empty="fa-regular fa-star-o fa-2x" />

                                    </div>
                                </div>

                            <?php
                                $lp_rating_field_counter++;
                            }
                            echo '<div class="clearfix"></div>';
                            ?>
                            <div class="col-md-6 padding-left-0">
                                <div class="form-group submit-images">
                                    <label for="post_gallery submit-images"><?php esc_html_e('Select Images', 'listingpro'); ?></label>
                                    <a href="#" class="browse-imgs"><?php esc_html_e('Browse', 'listingpro'); ?></a>
                                    <input type="file" id="filer_input2" name="post_gallery[]" multiple="multiple" />
                                </div>
                            </div>
                            <div class="clearfix"></div>
                </div>
            <?php
                        }
            ?>


            <?php
                if ($lp_multi_rating_state == 0) {
            ?>
                <div class="col-md-6 padding-left-0">
                    <div class="form-group margin-bottom-40">
                        <p class="padding-bottom-15"><?php esc_html_e('Your Rating for this listing', 'listingpro'); ?></p>
                        <div class="sfdfdf list-style-none form-review-stars">
                            <input type="hidden" id="review-rating" name="rating" class="rating-tooltip" data-filled="fa fa-star fa-2x" data-empty="fa-regular fa-star-o fa-2x" />
                            <div class="review-emoticons">
                                <div class="review angry"><?php echo listingpro_icons('angry'); ?></div>
                                <div class="review cry"><?php echo listingpro_icons('crying'); ?></div>
                                <div class="review sleeping"><?php echo listingpro_icons('sleeping'); ?></div>
                                <div class="review smily"><?php echo listingpro_icons('smily'); ?></div>
                                <div class="review cool"><?php echo listingpro_icons('cool'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 pull-right padding-right-0">
                    <div class="form-group submit-images">
                        <label for="post_gallery submit-images"><?php esc_html_e('Select Images', 'listingpro'); ?></label>
                        <a href="#" class="browse-imgs"><?php esc_html_e('Browse', 'listingpro'); ?></a>
                        <input type="file" id="filer_input2" name="post_gallery[]" multiple="multiple" />
                    </div>
                </div>
                <div class="clearfix"></div>
            <?php
                }
            ?>

            <div class="form-group">
                <label for="post_title"><?php esc_html_e('Title', 'listingpro'); ?></label>
                <input placeholder="<?php esc_html_e('Example: It was an awesome experience to be there', 'listingpro'); ?>" type="text" id="post_title" class="form-control" name="post_title" />
            </div>
            <div class="form-group">
                <label for="post_description"><?php esc_html_e('Review', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>
                <textarea placeholder="<?php esc_html_e('Tip: A great review covers food, service, and ambiance. Got recommendations for your favorite dishes and drinks, or something everyone should try here? Include that too!', 'listingpro'); ?>" id="post_description" class="form-control" rows="8" name="post_description"></textarea>
                <p><?php esc_html_e('Your review is recommended to be at least 140 characters long :)', 'listingpro'); ?></p>
            </div>

            <?php
                if (!empty($privacy_policy) && $privacy_review == "yes") {
            ?>
                <div class="form-group lp_privacy_policy_Wrap">
                    <input class="lpprivacycheckboxopt" id="reviewpolicycheck" type="checkbox" name="reviewpolicycheck" value="true">
                    <label for="reviewpolicycheck"><a target="_blank" href="<?php echo get_the_permalink($privacy_policy); ?>" class="help" target="_blank"><?php echo esc_html__('I Agree', 'listingpro'); ?></a></label>
                    <div class="help-text">
                        <a class="help" target="_blank"><i class="fa fa-question"></i></a>
                        <div class="help-tooltip">
                            <p><?php echo esc_html__('You agree & accept our Terms & Conditions for posting this review?', 'listingpro'); ?></p>
                        </div>
                    </div>
                </div>
                <p class="form-submit post-reletive">
                    <input name="submit_review" type="submit" id="submit" class="lp-review-btn btn-second-hover" value="<?php esc_html_e('Submit Review', 'listingpro'); ?>" disabled>
                    <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($postid); ?>" id="comment_post_ID">
                    <input type="hidden" name="errormessage" value="<?php esc_html_e('Please fill Email, Title, Description and Rating', 'listingpro'); ?>">
                    <span class="review_status"></span>
                    <img class="loadinerSearch" width="100px" alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/ajax-load.gif' ?>">
                </p>
            <?php
                } else {
            ?>
                <p class="form-submit post-reletive">
                    <input name="submit_review" type="submit" id="submit" class="lp-review-btn btn-second-hover" value="<?php esc_html_e('Submit Review', 'listingpro'); ?>">
                    <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($postid); ?>" id="comment_post_ID">
                    <input type="hidden" name="errormessage" value="<?php esc_html_e('Please fill Email, Title, Description and Rating', 'listingpro'); ?>">
                    <span class="review_status"></span>
                    <img class="loadinerSearch" width="100px" alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/ajax-load.gif' ?>">
                </p>
            <?php
                }
            ?>


            </form>
            </div>
        <?php
            } else {
        ?>
            <div class="review-form">
                <?php if ($listing_mobile_view == 'app_view2') { ?>
                    <h3 id="reply-title" class="comment-reply-title text-center"><i class="fa fa-star" aria-hidden="true"></i> <?php esc_html_e('Add Review', 'listingpro'); ?> </h3>
                <?php } else { ?>
                    <h3 id="reply-title" class="comment-reply-title"><i class="fa-regular fa-star-o"></i> <?php esc_html_e('Rate us and Write a Review', 'listingpro'); ?> <i class="fa fa-caret-down"></i></h3>
                <?php } ?>
                <?php
                if ($lp_Reviews_OPT == "instant_sign_in") {
                ?>
                    <form class="" data-lp-recaptcha="<?php echo wp_kses_post($enableCaptcha); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>" data-multi-rating="<?php echo esc_attr($lp_multi_rating_state); ?>" id="rewies_form" name="rewies_form" action="" method="post" enctype="multipart/form-data" data-imgcount="<?php echo esc_attr($lp_images_count); ?>" data-imgsize="<?php echo esc_attr($lp_images_size); ?>" data-countnotice="<?php echo esc_attr($lp_imagecount_notice); ?>" data-sizenotice="<?php echo esc_attr($lp_imagesize_notice); ?>">
                    <?php
                } else {
                    ?>
                        <form data-lp-recaptcha="<?php echo wp_kses_post($enableCaptcha); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>" class="reviewformwithnotice" data-multi-rating="<?php echo esc_attr($lp_multi_rating_state); ?>" id="rewies_formm" name="rewies_form" action="#" method="post" enctype="multipart/form-data" data-imgcount="<?php echo esc_attr($lp_images_count); ?>" data-imgsize="<?php echo esc_attr($lp_images_size); ?>" data-countnotice="<?php echo esc_attr($lp_imagecount_notice); ?>" data-sizenotice="<?php echo esc_attr($lp_imagesize_notice); ?>">
                        <?php } ?>

                        <?php
                        if ($lp_multi_rating_state == 1 && is_array($lp_multi_rating_fields) && !empty($lp_multi_rating_fields)) {
                            echo '<div class="col-md-12 padding-left-0 lp-multi-rating-ui-wrap">';
                            $lp_rating_field_counter    =    1;
                            //New update 2.6.10
                            $switch  = get_option('lp_multirating_switch');
                            if ($switch == 1 && !empty($switch)) {
                                $multi_rating_fileds = $lp_multi_rating_fields;
                            } else {
                                $multi_rating_fileds = $lp_multi_rating_fields['default'];
                            }
                            //End New update 2.6.10

                            foreach ($multi_rating_fileds as $k => $lp_multi_rating_field) {
                        ?>
                                <div class="<?php echo esc_attr($multi_col_class); ?> padding-left-0">
                                    <div class="sfdfdf list-style-none form-review-stars">
                                        <p><?php echo esc_attr($lp_multi_rating_field); ?></p>
                                        <input type="hidden" data-mrf="<?php echo esc_attr($k); ?>" id="review-rating-<?php echo esc_attr($k); ?>" name="rating-<?php echo esc_attr($k); ?>" class="rating-tooltip lp-multi-rating-val" data-filled="fa fa-star fa-2x" data-empty="fa-regular fa-star-o fa-2x" />

                                    </div>
                                </div>

                            <?php
                                $lp_rating_field_counter++;
                            }
                            echo '<div class="clearfix"></div>';
                            ?>
                            <div class="col-md-6 padding-left-0">
                                <div class="form-group submit-images">
                                    <label for="post_gallery submit-images"><?php esc_html_e('Select Images', 'listingpro'); ?></label>
                                    <a href="#" class="browse-imgs"><?php esc_html_e('Browse', 'listingpro'); ?></a>
                                    <input type="file" id="filer_input2" name="post_gallery[]" multiple="multiple" />
                                </div>
                            </div>
                            <div class="clearfix"></div>
            </div>
        <?php
                        }
        ?>


        <?php
                if ($lp_multi_rating_state == 0) {
        ?>
            <div class="col-md-6 padding-left-0">
                <div class="form-group margin-bottom-40">
                    <p class="padding-bottom-15"><?php esc_html_e('Your Rating for this listing', 'listingpro'); ?></p>
                    <input type="hidden" id="review-rating" name="rating" class="rating-tooltip" data-filled="fa fa-star fa-2x" data-empty="fa-regular fa-star-o fa-2x" />
                    <div class="review-emoticons">
                        <div class="review angry"><?php echo listingpro_icons('angry'); ?></div>
                        <div class="review cry"><?php echo listingpro_icons('crying'); ?></div>
                        <div class="review sleeping"><?php echo listingpro_icons('sleeping'); ?></div>
                        <div class="review smily"><?php echo listingpro_icons('smily'); ?></div>
                        <div class="review cool"><?php echo listingpro_icons('cool'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 pull-right padding-right-0">
                <div class="form-group submit-images">
                    <label for="post_gallery submit-images"><?php esc_html_e('Select Images', 'listingpro'); ?></label>
                    <a href="#" class="browse-imgs"><?php esc_html_e('Browse', 'listingpro'); ?></a>
                    <input type="file" id="filer_input2" name="post_gallery[]" multiple="multiple" />
                </div>
            </div>
            <div class="clearfix"></div>
        <?php
                }
        ?>
        <?php
                if ($lp_Reviews_OPT == "instant_sign_in") {
                    if ($enableUsernameField == true) { ?>
                <div class="form-group">
                    <label for="u_mail"><?php esc_html_e('User Name', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>
                    <input type="text" placeholder="<?php esc_html_e('john', 'listingpro'); ?>" id="lp_custom_username" class="form-control" name="lp_custom_username" />
                </div>

            <?php } ?>
            <div class="form-group">
                <label for="u_mail"><?php esc_html_e('Email', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>
                <input type="email" placeholder="<?php esc_html_e('you@website.com', 'listingpro'); ?>" id="u_mail" class="form-control" name="u_mail" />
            </div>
        <?php } ?>

        <div class="form-group">
            <label for="post_title"><?php esc_html_e('Title', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>
            <input type="text" placeholder="<?php esc_html_e('Example: It was an awesome experience to be there', 'listingpro'); ?>" id="post_title" class="form-control" name="post_title" />
        </div>
        <div class="form-group">
            <label for="post_description"><?php esc_html_e('Review', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>
            <textarea placeholder="<?php esc_html_e('Tip: A great review covers food, service, and ambiance. Got recommendations for your favorite dishes and drinks, or something everyone should try here? Include that too!', 'listingpro'); ?>" id="post_description" class="form-control" rows="8" name="post_description"></textarea>
            <p><?php esc_html_e('Your review is recommended to be at least 140 characters long', 'listingpro'); ?></p>
        </div>


        <?php

                if (!empty($privacy_policy) && $privacy_review == "yes") {
        ?>
            <div class="form-group lp_privacy_policy_Wrap">
                <input class="lpprivacycheckboxopt" id="reviewpolicycheck" type="checkbox" name="reviewpolicycheck" value="true">
                <label for="reviewpolicycheck"><a target="_blank" href="<?php echo get_the_permalink($privacy_policy); ?>" class="help" target="_blank"><?php echo esc_html__('I Agree', 'listingpro'); ?></a></label>
                <div class="help-text">
                    <a class="help" target="_blank"><i class="fa fa-question"></i></a>
                    <div class="help-tooltip">
                        <p><?php echo esc_html__('You agree & accept our Terms & Conditions for posting this review?', 'listingpro'); ?></p>
                    </div>
                </div>
            </div>


            <p class="form-submit">
                <?php
                    if ($lp_Reviews_OPT == "sign_in") {

                        $reviewDataAtts = '';
                        $extraDataatts = 'data-modal="modal-3"';
                ?>
                    <?php if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                            $reviewDataAtts = 'data-toggle="modal" data-target="#app-view-login-popup"';
                            $extraDataatts = '';
                        }
                    ?>

                    <input name="submit_review" <?php echo wp_kses_post($reviewDataAtts); ?> type="submit" id="submit" class="lp-review-btn btn-second-hover md-trigger" <?php echo wp_kses_post($extraDataatts); ?> value="<?php echo esc_html__('Submit Review ', 'listingpro'); ?>" disabled>
                <?php
                    } elseif ($lp_Reviews_OPT == "instant_sign_in") {
                ?>
                    <input name="submit_review" type="submit" id="submit" class="lp-review-btn btn-second-hover" value="<?php echo esc_html__('Signup & Submit Review ', 'listingpro'); ?>" disabled>
                <?php } ?>
                <span class="review_status"></span>
                <img class="loadinerSearch" width="100px" alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/ajax-load.gif' ?>">
            </p>
        <?php
                } else {
        ?>
            <p class="form-submit">
                <?php
                    if ($lp_Reviews_OPT == "sign_in") {

                        $reviewDataAtts = '';
                        $extraDataatts = 'data-modal="modal-3"';
                ?>
                    <?php if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                            $reviewDataAtts = 'data-toggle="modal" data-target="#app-view-login-popup"';
                            $extraDataatts = '';
                        }
                    ?>

                    <input name="submit_review" <?php echo wp_kses_post($reviewDataAtts); ?> type="submit" id="submit" class="lp-review-btn btn-second-hover md-trigger" <?php echo wp_kses_post($extraDataatts); ?> value="<?php echo esc_html__('Submit Review ', 'listingpro'); ?>">
                <?php
                    } elseif ($lp_Reviews_OPT == "instant_sign_in") {
                ?>
                    <input name="submit_review" type="submit" id="submit" class="lp-review-btn btn-second-hover" value="<?php echo esc_html__('Signup & Submit Review ', 'listingpro'); ?>">
                <?php } ?>

                <span class="review_status"></span>
                <img class="loadinerSearch" width="100px" alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/ajax-load.gif' ?>">
            </p>
        <?php
                }
        ?>
        <input type="hidden" name="errormessage" value="<?php esc_html_e('Please fill Email, Title, Description and Rating', 'listingpro'); ?>">

        <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($postid); ?>" id="comment_post_ID">


        </form>
        </div>
    <?php

            }
        }
    }
}

if (!function_exists('listingpro_get_reviews_form_v2')) {

    function listingpro_get_reviews_form_v2($postid)

    {
        if (class_exists('ListingReviews')) {

            global $listingpro_options;
            $lp_Reviews_OPT = $listingpro_options['lp_review_submit_options'];

            $gSiteKey = '';

            $gSiteKey = $listingpro_options['lp_recaptcha_site_key'];

            $enableCaptcha = lp_check_receptcha('lp_recaptcha_reviews');

            $lp_images_count = '555';
            $lp_images_count = (int)$lp_images_count;
            $lp_images_size = '999999999999999999999999999999999999999999999999999';
            $lp_imagecount_notice = '';
            $lp_imagesize_notice = '';
            if (lp_theme_option('lp_listing_reviews_images_count_switch') == 'yes') {
                $lp_images_count = lp_theme_option('lp_listing_reviews_images_counter');
                $lp_imagecount_notice = esc_html__("Max. allowed images are ", 'listingpro');
                $lp_imagecount_notice .= $lp_images_count;
            }
            if (lp_theme_option('lp_listing_reviews_images_size_switch') == 'yes') {
                $lp_images_size = lp_theme_option('lp_listing_reviews_images_sizes');
                $lp_imagesize_notice = esc_html__('Max. allowed images size is ', 'listingpro');
                $lp_imagesize_notice .= $lp_images_size . esc_html__(' Mb', 'listingpro');
                $lp_images_size = $lp_images_size * 1000000;
            }
            $enableUsernameField = lp_theme_option('lp_register_username');

            $lp_multi_rating_state        =   $listingpro_options['lp_multirating_switch'];

            if ($lp_multi_rating_state == 1 && !empty($lp_multi_rating_state)) {
                $lp_multi_rating_fields =   get_listing_multi_ratings_fields($postid);
            }

            $multi_left_col =   '';

            $multi_right_col =   '';



            if ($lp_multi_rating_state == 1 && is_array($lp_multi_rating_fields) && !empty($lp_multi_rating_fields)) {

                $multi_left_col =   'lp-review-form-top-multi';

                $multi_right_col =   'lp-review-images-multi';
            }



            if (is_user_logged_in()) {

    ?>

        <div class="lp-listing-review-form" id="review-section">



            <h2><?php echo esc_html__('Write a review', 'listingpro'); ?> <i class="fa fa-chevron-down"></i></h2>

            <form data-lp-recaptcha="<?php echo wp_kses_post($enableCaptcha); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>" , data-multi-rating="<?php echo esc_attr($lp_multi_rating_state); ?>" id="rewies_form" name="rewies_form" action="" method="post" enctype="multipart/form-data" data-imgcount="<?php echo esc_attr($lp_images_count); ?>" data-imgsize="<?php echo esc_attr($lp_images_size); ?>" data-countnotice="<?php echo esc_attr($lp_imagecount_notice); ?>" data-sizenotice="<?php echo esc_attr($lp_imagesize_notice); ?>">

                <div class="lp-review-form-top <?php echo esc_attr($multi_left_col); ?>">

                    <?php

                    if ($lp_multi_rating_state == 1 && is_array($lp_multi_rating_fields) && !empty($lp_multi_rating_fields)) {

                    ?>

                        <div class="lp-review-stars">

                            <span class="stars-label"><?php esc_html_e('Your Rating', 'listingpro'); ?></span>

                            <i class="fa-regular fa-star-o" data-rating="1"></i>

                            <i class="fa-regular fa-star-o" data-rating="2"></i>

                            <i class="fa-regular fa-star-o" data-rating="3"></i>

                            <i class="fa-regular fa-star-o" data-rating="4"></i>

                            <i class="fa-regular fa-star-o" data-rating="5"></i>

                        </div>

                    <?php
                    } else {
                    ?>

                        <div class="lp-review-stars">

                            <span class="stars-label"><?php esc_html_e('Your Rating', 'listingpro'); ?></span>

                            <div class="lp-listing-stars">

                                <input type="hidden" id="review-rating" name="rating" class="rating-tooltip" data-filled="fa fa-star" data-empty="fa-regular fa-star-o" />

                                <div class="review-emoticons">

                                    <div class="review angry"><?php echo listingpro_icons('angry'); ?></div>

                                    <div class="review cry"><?php echo listingpro_icons('crying'); ?></div>

                                    <div class="review sleeping"><?php echo listingpro_icons('sleeping'); ?></div>

                                    <div class="review smily"><?php echo listingpro_icons('smily'); ?></div>

                                    <div class="review cool"><?php echo listingpro_icons('cool'); ?></div>

                                </div>

                            </div>

                        </div>

                    <?php

                    }

                    ?>

                    <div class="form-group submit-images lp-review-images <?php echo esc_attr($multi_right_col); ?>">

                        <label for="post_gallery submit-images"><?php esc_html_e('Select Images', 'listingpro'); ?></label>

                        <a href="#" class="browse-imgs"><?php esc_html_e('Browse', 'listingpro'); ?></a>

                        <input type="file" id="filer_input2" name="post_gallery[]" multiple="multiple" />

                    </div>



                    <div class="clearfix"></div>

                </div>

                <div class="lp-review-form-bottom">



                    <?php

                    if ($lp_multi_rating_state == 1 && is_array($lp_multi_rating_fields) && !empty($lp_multi_rating_fields)) {

                        echo '<div class="form-group">';

                        echo '<div class="col-md-12 padding-left-0 lp-multi-rating-ui-wrap">';

                        $lp_rating_field_counter    =    1;
                        //New update 2.6.10
                        $switch  = get_option('lp_multirating_switch');
                        if ($switch == 1 && !empty($switch)) {
                            $multi_rating_fileds = $lp_multi_rating_fields;
                        } else {
                            $multi_rating_fileds = $lp_multi_rating_fields['default'];
                        }
                        //End New update 2.6.10

                        foreach ($multi_rating_fileds as $k => $lp_multi_rating_field) {

                    ?>

                            <div class="col-md-6 padding-left-0">

                                <div class="sfdfdf list-style-none form-review-stars">

                                    <p><?php echo esc_attr($lp_multi_rating_field); ?></p>

                                    <input type="hidden" data-mrf="<?php echo esc_attr($k); ?>" id="review-rating-<?php echo esc_attr($k); ?>" name="rating-<?php echo esc_attr($k); ?>" class="rating-tooltip lp-multi-rating-val" data-filled="fa fa-star fa-2x" data-empty="fa-regular fa-star-o fa-2x" />



                                </div>

                            </div>

                    <?php

                            $lp_rating_field_counter++;
                        }

                        echo '<div class="clearfix"></div>';

                        echo '</div>';

                        echo '</div>';
                    }

                    ?>



                    <div class="form-group">

                        <label for="post_title"><?php esc_html_e('Title', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>

                        <input placeholder="<?php esc_html_e('Example: It was an awesome experience to be there', 'listingpro'); ?>" type="text" id="post_title" class="form-control" name="post_title" />

                    </div>

                    <div class="form-group">

                        <label for="post_description"><?php esc_html_e('Review', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>

                        <textarea placeholder="<?php esc_html_e('Tip: A great review covers food, service, and ambiance. Got recommendations for your favorite dishes and drinks, or something everyone should try here? Include that too! And remember.', 'listingpro'); ?>" id="post_description" class="form-control" rows="8" name="post_description"></textarea>

                        <p><?php esc_html_e('Your review is recommended to be at least 140 characters long :)', 'listingpro'); ?></p>

                    </div>


                    <?php
                    $privacy_policy = $listingpro_options['payment_terms_condition'];
                    $privacy_review = $listingpro_options['listingpro_privacy_review'];
                    if (!empty($privacy_policy) && $privacy_review == 'yes') {
                    ?>
                        <div class="form-group lp_privacy_policy_Wrap">
                            <input class="lpprivacycheckboxopt" id="reviewpolicycheck" type="checkbox" name="reviewpolicycheck" value="true">
                            <label for="reviewpolicycheck"><a target="_blank" href="<?php echo get_the_permalink($privacy_policy); ?>" class="help" target="_blank"><?php echo esc_html__('I Agree', 'listingpro'); ?></a></label>
                            <div class="help-text">
                                <a class="help" target="_blank"><i class="fa fa-question"></i></a>
                                <div class="help-tooltip">
                                    <p><?php echo esc_html__('You agree & accept our Terms & Conditions for posting this review?', 'listingpro'); ?></p>
                                </div>
                            </div>
                        </div>
                        <p class="form-submit post-reletive">
                            <input name="submit_review" type="submit" id="submit" class="review-submit-btn" value="<?php esc_html_e('Submit Review', 'listingpro'); ?>" disabled>
                            <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($postid); ?>" id="comment_post_ID">
                            <input type="hidden" name="errormessage" value="<?php esc_html_e('Please fill Email, Title, Description and Rating', 'listingpro'); ?>">
                            <span class="review_status"></span>
                            <img class="loadinerSearch" width="100px" alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/ajax-load.gif' ?>">
                        </p>
                    <?php
                    } else {
                    ?>
                        <?php
                        ?>
                        <p class="form-submit post-reletive">
                            <input name="submit_review" type="submit" id="submit" class="review-submit-btn" value="<?php esc_html_e('Submit Review', 'listingpro'); ?>">
                            <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($postid); ?>" id="comment_post_ID">
                            <input type="hidden" name="errormessage" value="<?php esc_html_e('Please fill Email, Title, Description and Rating', 'listingpro'); ?>">
                            <span class="review_status"></span>
                            <img class="loadinerSearch" width="100px" alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/ajax-load.gif' ?>">
                        </p>
                    <?php
                    }
                    ?>

                </div>



            </form>

        </div>

    <?php

            } else { ?>

        <?php
                if (!is_user_logged_in()) {
                    $popup_style = $listingpro_options['login_popup_style'];
                    if ($popup_style == 'style1' && $lp_Reviews_OPT != "instant_sign_in") {
                        $review_form_popup    =    'class="lp-listing-review-form review-bar-login md-trigger" data-modal="modal-3"';
                    } elseif ($popup_style == 'style2' && $lp_Reviews_OPT != "instant_sign_in") {
                        $review_form_popup    =    'class="lp-listing-review-form app-view-popup-style" data-target="#app-view-login-popup"';
                    } elseif ($lp_Reviews_OPT == "instant_sign_in") {
                        $review_form_popup    =    'class="lp-listing-review-form review-bar-login"';
                    }
                }
        ?>
        <div <?php echo wp_kses_post($review_form_popup); ?>>


            <h2><?php echo esc_html__('Write a review', 'listingpro'); ?> <i class="fa fa-chevron-down"></i></h2>

            <?php

                if ($lp_Reviews_OPT == "instant_sign_in") {

            ?>

                <form data-lp-recaptcha="<?php echo wp_kses_post($enableCaptcha); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>" class="" data-multi-rating="<?php echo esc_attr($lp_multi_rating_state); ?>" id="rewies_form" name="rewies_form" action="" method="post" enctype="multipart/form-data" data-imgcount="<?php echo esc_attr($lp_images_count); ?>" data-imgsize="<?php echo esc_attr($lp_images_size); ?>" data-countnotice="<?php echo esc_attr($lp_imagecount_notice); ?>" data-sizenotice="<?php echo esc_attr($lp_imagesize_notice); ?>">

                <?php

                } else {

                ?>

                    <form data-lp-recaptcha="<?php echo wp_kses_post($enableCaptcha); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>" class="reviewformwithnotice" data-multi-rating="<?php echo esc_attr($lp_multi_rating_state); ?>" id="rewies_formm" name="rewies_form" action="#" method="post" enctype="multipart/form-data" data-imgcount="<?php echo esc_attr($lp_images_count); ?>" data-imgsize="<?php echo esc_attr($lp_images_size); ?>" data-countnotice="<?php echo esc_attr($lp_imagecount_notice); ?>" data-sizenotice="<?php echo esc_attr($lp_imagesize_notice); ?>">



                    <?php } ?>

                    <div class="lp-review-form-top <?php echo esc_attr($multi_left_col); ?>">

                        <div class="lp-review-stars">

                            <span class="stars-label"><?php esc_html_e('Your Rating', 'listingpro'); ?></span>

                            <?php

                            if ($lp_multi_rating_state == 1 && is_array($lp_multi_rating_fields) && !empty($lp_multi_rating_fields)) {

                            ?>

                                <i class="fa-regular fa-star-o" data-rating="1"></i>

                                <i class="fa-regular fa-star-o" data-rating="2"></i>

                                <i class="fa-regular fa-star-o" data-rating="3"></i>

                                <i class="fa-regular fa-star-o" data-rating="4"></i>

                                <i class="fa-regular fa-star-o" data-rating="5"></i>

                            <?php

                            } else {

                            ?>

                                <div class="lp-listing-stars">

                                    <input type="hidden" id="review-rating" name="rating" class="rating-tooltip" data-filled="fa fa-star" data-empty="fa-regular fa-star-o" />

                                    <div class="review-emoticons">

                                        <div class="review angry"><?php echo listingpro_icons('angry'); ?></div>

                                        <div class="review cry"><?php echo listingpro_icons('crying'); ?></div>

                                        <div class="review sleeping"><?php echo listingpro_icons('sleeping'); ?></div>

                                        <div class="review smily"><?php echo listingpro_icons('smily'); ?></div>

                                        <div class="review cool"><?php echo listingpro_icons('cool'); ?></div>

                                    </div>

                                </div>

                            <?php

                            }

                            ?>

                        </div>

                        <div class="form-group submit-images lp-review-images">

                            <label for="post_gallery submit-images"><?php esc_html_e('Select Images', 'listingpro'); ?></label>

                            <a href="#" class="browse-imgs"><?php esc_html_e('Browse', 'listingpro'); ?></a>

                            <input type="file" id="filer_input2" name="post_gallery[]" multiple="multiple" />

                        </div>



                        <div class="clearfix"></div>

                    </div>

                    <div class="clearfix"></div>

                    <div class="lp-review-form-bottom">

                        <div class="form-group">

                            <?php

                            if ($lp_multi_rating_state == 1 && is_array($lp_multi_rating_fields) && !empty($lp_multi_rating_fields)) {

                                echo '<div class="col-md-12 padding-left-0 lp-multi-rating-ui-wrap">';

                                $lp_rating_field_counter    =    1;

                                foreach ($lp_multi_rating_fields as $k => $lp_multi_rating_field) {

                            ?>

                                    <div class="col-md-6 padding-left-0">

                                        <div class="sfdfdf list-style-none form-review-stars">

                                            <p><?php echo esc_attr($lp_multi_rating_field); ?></p>

                                            <input type="hidden" data-mrf="<?php echo esc_attr($k); ?>" id="review-rating-<?php echo esc_attr($k); ?>" name="rating-<?php echo esc_attr($k); ?>" class="rating-tooltip lp-multi-rating-val" data-filled="fa fa-star fa-2x" data-empty="fa-regular fa-star-o fa-2x" />

                                        </div>

                                    </div>

                            <?php

                                    $lp_rating_field_counter++;
                                }

                                echo '<div class="clearfix"></div>';

                                echo '</div>';
                            }

                            ?>

                        </div>

                        <?php
                        if ($lp_Reviews_OPT == "instant_sign_in") {

                            if ($enableUsernameField == true) { ?>
                                <div class="form-group">
                                    <label for="u_mail"><?php esc_html_e('User Name', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>
                                    <input type="text" placeholder="<?php esc_html_e('john', 'listingpro'); ?>" id="lp_custom_username" class="form-control" name="lp_custom_username" />
                                </div>

                            <?php } ?>

                            <div class="form-group">

                                <label for="u_mail"><?php esc_html_e('Email', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>

                                <input type="email" placeholder="<?php esc_html_e('you@website.com', 'listingpro'); ?>" id="u_mail" class="form-control" name="u_mail" />

                            </div>

                        <?php } ?>



                        <div class="form-group">

                            <label for="post_title"><?php esc_html_e('Title', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>

                            <input placeholder="<?php esc_html_e('Example: It was an awesome experience to be there', 'listingpro'); ?>" type="text" id="post_title" class="form-control" name="post_title" />

                        </div>

                        <div class="form-group">

                            <label for="post_description"><?php esc_html_e('Review', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>

                            <textarea placeholder="<?php esc_html_e('Tip: A great review covers food, service, and ambiance. Got recommendations for your favorite dishes and drinks, or something everyone should try here? Include that too! And remember.', 'listingpro'); ?>" id="post_description" class="form-control" rows="8" name="post_description"></textarea>

                            <p><?php esc_html_e('Your review recommended to be at least 140 characters long :)', 'listingpro'); ?></p>

                        </div>


                        <?php
                        if (!empty($privacy_policy) && $privacy_review == "yes") {
                        ?>
                            <div class="form-group lp_privacy_policy_Wrap">
                                <input class="lpprivacycheckboxopt" id="reviewpolicycheck" type="checkbox" name="reviewpolicycheck" value="true">
                                <label for="reviewpolicycheck"><a target="_blank" href="<?php echo get_the_permalink($privacy_policy); ?>" class="help" target="_blank"><?php echo esc_html__('I Agree', 'listingpro'); ?></a></label>
                                <div class="help-text">
                                    <a class="help" target="_blank"><i class="fa fa-question"></i></a>
                                    <div class="help-tooltip">
                                        <p><?php echo esc_html__('You agree & accept our Terms & Conditions for posting this review?', 'listingpro'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <p class="form-submit">
                                <?php
                                if ($lp_Reviews_OPT == "sign_in") {
                                ?>
                                    <input name="submit_review" type="submit" id="submit" class="review-submit-btn md-trigger" data-modal="modal-3" value="<?php echo esc_html__('Submit Review ', 'listingpro'); ?>" disabled>
                                <?php
                                } elseif ($lp_Reviews_OPT == "instant_sign_in") {
                                ?>
                                    <input name="submit_review" type="submit" id="submit" class="review-submit-btn" value="<?php echo esc_html__('Signup & Submit Review ', 'listingpro'); ?>" disabled>
                                <?php } ?>
                                <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($postid); ?>" id="comment_post_ID">
                                <span class="review_status"></span>
                                <img class="loadinerSearch" width="100px" alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/ajax-load.gif' ?>">
                            </p>
                        <?php
                        } else {
                        ?>
                            <p class="form-submit">
                                <?php
                                if ($lp_Reviews_OPT == "sign_in") {
                                ?>
                                    <input name="submit_review" type="submit" id="submit" class="review-submit-btn md-trigger" data-modal="modal-3" value="<?php echo esc_html__('Submit Review ', 'listingpro'); ?>">
                                <?php
                                } elseif ($lp_Reviews_OPT == "instant_sign_in") {
                                ?>
                                    <input name="submit_review" type="submit" id="submit" class="review-submit-btn" value="<?php echo esc_html__('Signup & Submit Review ', 'listingpro'); ?>">
                                <?php } ?>
                                <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($postid); ?>" id="comment_post_ID">
                                <span class="review_status"></span>
                                <img class="loadinerSearch" width="100px" alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/ajax-load.gif' ?>">
                            </p>
                        <?php

                        }
                        ?>
                        <input type="hidden" name="errormessage" value="<?php esc_html_e('Please fill Email, Title, Description and Rating', 'listingpro'); ?>">
                    </div>

                    </form>

        </div>

<?php

            }
        }
    }
}

function get_listing_multi_ratings_fields($postID)
{
    $multi_rating_settings          =   get_option('lp-ratings-settings');

    $listing_terms = wp_get_post_terms($postID, 'listing-category');

    $first_term =   '';
    if ($listing_terms) {
        $first_term         =   $listing_terms[0]->name;
        $first_term    =   html_entity_decode($first_term);
    }
    if (is_array($multi_rating_settings)) {
        if (array_key_exists($first_term, $multi_rating_settings)) {
            $rating_fields_data =   $multi_rating_settings[$first_term];
        } else {
            foreach ($multi_rating_settings as $key => $val) {
                if (!empty($first_term) && strpos($key, $first_term) !== false) {
                    $rating_fields_data =   $val;
                    break;
                }
            }
        }
    }

    if (!isset($rating_fields_data)) {
        //New update 2.6.10
        global $listingpro_options;
        $switch  = get_option('lp_multirating_switch');
        if ($switch == 1 && !empty($switch)) {
            $rating_fields_data =   get_option('lp-ratings-default-settings');
            $rating_fields_data =   isset($rating_fields_data['default']) ? $rating_fields_data['default'] : '';
        } else {
            for ($i = 1; $i <= 4; $i++) {
                $field_active_status    =   $listingpro_options['lp_multi_ratiing' . $i . '_switch'];
                if ($field_active_status == 1) {
                    $rating_fields_data['default'][]   =   $listingpro_options['lp_multi_ratiing' . $i . '_label_switch'];
                }
            }
        }
        //End New update 2.6.10
    }
    return $rating_fields_data;
}
?>