<?php
/* The loop starts here. */
global $listingpro_options;
if (have_posts()) {
    while (have_posts()) {
        the_post();
        setPostViews(get_the_ID());
        $claimed_section = listing_get_metabox('claimed_section');
        $tagline_text = listing_get_metabox('tagline_text');
        $currentUserId = get_current_user_id();
        $plan_id = listing_get_metabox_by_ID('Plan_id', get_the_ID());
        if (!empty($plan_id)) {
            $plan_id = $plan_id;
        } else {
            $plan_id = 'none';
        }

        $contact_show = get_post_meta($plan_id, 'contact_show', true);
        $map_show = get_post_meta($plan_id, 'map_show', true);
        $video_show = get_post_meta($plan_id, 'video_show', true);
        $gallery_show = get_post_meta($plan_id, 'gallery_show', true);
        $tagline_show = get_post_meta($plan_id, 'listingproc_tagline', true);
        $location_show = get_post_meta($plan_id, 'listingproc_location', true);
        $website_show = get_post_meta($plan_id, 'listingproc_website', true);
        $social_show = get_post_meta($plan_id, 'listingproc_social', true);
        $faqs_show = get_post_meta($plan_id, 'listingproc_faq', true);
        $menu_show = get_post_meta($plan_id, 'listingproc_plan_menu', true);
        $price_show = get_post_meta($plan_id, 'listingproc_price', true);
        $tags_show = get_post_meta($plan_id, 'listingproc_tag_key', true);
        $hours_show = get_post_meta($plan_id, 'listingproc_bhours', true);
        $discounts_show = get_post_meta($plan_id, 'listingproc_plan_deals', true);
        $events_show = get_post_meta($plan_id, 'lp_eventsplan', true);

        if ($plan_id == "none") {
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
            $menu_show  =   'true';
            $tags_show = 'true';
            $hours_show = 'true';
            $discounts_show = 'true';
            $events_show = 'true';
        }

        $claim = '';
        if ($claimed_section == 'claimed') {
            $claim = '<span class="claimed"><i class="fa fa-check"></i> </span>';
        } elseif ($claimed_section == 'not_claimed') {
            $claim = '';
        }
        global $post;

        $resurva_url = get_post_meta($post->ID, 'resurva_url', true);
        $menuOption = false;
        $menuTitle = '';
        $menuImg = '';
        $menuMeta = get_post_meta($post->ID, 'menu_listing', true);
        if (!empty($menuMeta)) {
            $menuTitle = $menuMeta['menu-title'];
            $menuImg = $menuMeta['menu-img'];
            $menuOption = true;
        }

        $timekit = false;
        $timekit_booking = get_post_meta($post->ID, 'timekit_booking', true);

        if (!empty($timekit_booking)) {
            $timekitAPP = $timekit_booking['timekit-app'];
            $timekitAPI = $timekit_booking['timekit-api-token'];
            $timekitListing = $timekit_booking['listing_id'];
            $timekitName = $timekit_booking['timekit_name'];
            $timekitEmail = $timekit_booking['timekit_email'];
            $timekit = true;
        }



        /* get user meta */
        $user_id = $post->post_author;
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
        $whatsapp = listing_get_metabox('whatsapp');
        /* get user meta */
        $showReport = true;
        if (isset($listingpro_options['lp_detail_page_report_button'])) {
            if ($listingpro_options['lp_detail_page_report_button'] == 'off') {
                $showReport = false;
            }
        }
        $whatsappStatus = $listingpro_options['lp_detail_page_whatsapp_button'];
        $whatsappMsg = esc_html__('Hi, Contacting for you listing', 'listingpro');


        $priceRange = listing_get_metabox_by_ID('price_status', get_the_ID());
        $listingpTo = listing_get_metabox('list_price_to');
        $listingprice = listing_get_metabox_by_ID('list_price', get_the_ID());
        $lp_priceSymbol = $listingpro_options['listing_pricerange_symbol'];
        $title_pricy    =   '';
        if (($priceRange != 'notsay' && !empty($priceRange)) || !empty($listingpTo) || !empty($listingprice)) {
            $dollars = '';
            if ($priceRange == 'notsay') {
                $dollars = '';
            } elseif ($priceRange == 'inexpensive') {
                $dollars = '1';
            } elseif ($priceRange == 'moderate') {
                $dollars = '2';
            } elseif ($priceRange == 'pricey') {
                $dollars = '3';
            } elseif ($priceRange == 'ultra_high_end') {
                $dollars = '4';
            }
            $title_pricy    .=  '<span class="active">';
            for ($i = 0; $i < $dollars; $i++) {
                $title_pricy    .=  wp_kses_post($lp_priceSymbol);
            }
            $title_pricy    .=  '</span>';

            $title_pricy    .=  '<span class="grayscale">';
            $dollars = (int) $dollars;
            $greyDollar = 4 - $dollars;
            for ($i = 1; $i <= $greyDollar; $i++) {
                $title_pricy    .= wp_kses_post($lp_priceSymbol);
            }
            $title_pricy    .=  '</span>';
        }

        $timezone_string = get_option('timezone_string');
        if (isset($timezone_string) && !empty($timezone_string)) {
            date_default_timezone_set($timezone_string);
        }
        $strNow =   strtotime("NOW");
        $listing_discount_data = array();
        $listing_discount_offer =   get_post_meta(get_the_ID(), 'listing_discount_data', true);
        if (!empty($listing_discount_offer) && is_array($listing_discount_offer)) {
            foreach ($listing_discount_offer as $key => $val) {
                if (isset($val['disExpE']) && isset($val['disTimeE']) && isset($val['disTimeS'])) :
                    $couponExpiryE  = coupon_timestamp($val['disExpE'], $val['disTimeE']);
                    $couponExpiryS  = coupon_timestamp($val['disExpS'], $val['disTimeS']);
                    if (($strNow < $val['disExpE'] || empty($couponExpiryE)) && ($strNow > $couponExpiryS || empty($val['disExpS']))) :
                        $listing_discount_data[]  =   $val;
                    endif;
                endif;
            }
        }
?>

        <script>
            jQuery(document).ready(function(e) {
                jQuery('.app-view-gallery').slick({
                    dots: false,
                    arrows: false,
                });
                jQuery('.app--view-new-view-menu').click(function() {
                    jQuery('html, body').animate({
                        scrollTop: jQuery(".app-view-new-tabs").offset().top
                    }, 1000)
                });
            });
        </script>
        <!--==================================Section Open=================================-->
        <section class="aliceblue listing-app-view listing-app-view2 listing-second-view listing-app-view-new">
            <div class="app-view-new-gallery-title">
                <?php
                $meta_info_top_40   =   'meta_info_top_40';
                $IDs = get_post_meta($post->ID, 'gallery_image_ids', true);
                if (!empty($IDs) && $gallery_show == "true") {
                    $meta_info_top_40   =   '';
                    echo '<div class="app-view-gallery">';
                    $imgIDs = explode(',', $IDs);
                    foreach ($imgIDs as $imgID) {
                        $imgurl = wp_get_attachment_image_src($imgID, 'listingpro-blog-grid2');
                        $imgFull = wp_get_attachment_image_src($imgID, 'full');
                        if (!empty($imgurl)) {
                            echo '<div class="slide-img">';
                            echo '<div class="slide-img-overlay"></div>';
                            echo '  <img alt="image" src="' . $imgurl[0] . '">';
                            echo '</div>';
                        }
                    }

                    echo '</div>';
                } else {
                    $meta_info_top_40   =   '';
                    $Gall_alt_img = get_the_post_thumbnail_url($post->ID, 'listingpro-blog-grid2');
                    if ($Gall_alt_img == '') {
                        $Gall_alt_img = $listingpro_options['lp_def_featured_image']['url'];
                    }
                ?>
                    <div class="app-view-gallery">
                        <div class="slide-img">
                            <div class="slide-img-overlay"></div>
                            <img src="<?php echo wp_kses_post($Gall_alt_img); ?>" alt="alt image">
                        </div>
                    </div>
                <?php
                }
                ?>
                <div class="post-meta-info <?php echo esc_attr($meta_info_top_40); ?>">
                    <div class="container">
                        <div class="row">
                            <div class="app-view-new-rating-avg">
                                <?php
                                $NumberRating = listingpro_ratings_numbers($post->ID);
                                if ($NumberRating != 0) {
                                    echo lp_cal_listing_rate(get_the_ID());
                                ?>
                                    <span>
                                        <small><?php echo esc_attr($NumberRating); ?></small><?php echo esc_html__('Ratings', 'listingpro'); ?>
                                    </span>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="app-view-new-title">
                                <?php
                                $b_logo =   $listingpro_options['business_logo_switch'];
                                if ($b_logo) {
                                    $business_logo_url  =   '';
                                    $b_logo_default =   $listingpro_options['business_logo_default']['url'];

                                    $business_logo = listing_get_metabox_by_ID('business_logo', get_the_ID());

                                    if (empty($business_logo)) {
                                        $business_logo_url  =   $b_logo_default;
                                    } else {
                                        require_once(THEME_PATH . "/include/aq_resizer.php");
                                        $business_logo_url  =   aq_resize($business_logo, '82', '82', true, true, true);
                                    }
                                    if (!empty($business_logo_url)) {
                                ?>
                                        <div class="lp-listing-logo">
                                            <img src="<?php echo esc_url($business_logo_url); ?>" alt="Listing Logo">
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                                <div class="title-inner">
                                    <h1><?php echo wp_kses_post($title_pricy); ?> <span><?php the_title(); ?></span> <?php echo wp_kses_post($claim); ?></h1>
                                    <?php
                                    if (!empty($tagline_text) && $tagline_show == "true") {
                                    ?>
                                        <p><?php echo esc_attr($tagline_text); ?></p>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="app-view-new-actions">
                                <ul class="post-stat lp-appview-post-stat">
                                    <li id="fav-container">
                                        <a class="email-address add-to-fav" data-post-type="detail" href="" data-post-id="<?php echo get_the_ID(); ?>" data-success-text="<?php echo esc_html__('Saved', 'listingpro') ?>">
                                            <i class="fa <?php echo listingpro_is_favourite(get_the_ID(), $onlyicon = true); ?>"></i>
                                            <span class="email-icon"><?php echo listingpro_is_favourite(get_the_ID(), $onlyicon = false); ?></span>
                                        </a>
                                    </li>
                                    <li class="reviews sbutton">
                                        <?php listingpro_sharing(); ?>
                                    </li>
                                    <?php
                                    $lp_listing_menus   =   get_post_meta(get_the_ID(), 'lp-listing-menu', true);
                                    if (is_array($lp_listing_menus) && !empty($lp_listing_menus) && $menu_show == "true") {
                                    ?>
                                        <li class="app--view-new-view-menu">
                                            <i class="fa fa-cutlery"></i> <?php echo esc_html__('View Menu', 'listingpro'); ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $buisness_hours = listing_get_metabox('business_hours');
            if (!empty($buisness_hours)) {
                if ($hours_show == "true") {
            ?>
                    <div class="widget-box clearfix app-view-new-timings">
                        <?php get_template_part('include/timings'); ?>
                    </div>
            <?php
                }
            }
            ?>
            <div class="aliceblue clearfix">
                <?php if (!empty($timekit_booking) && $timekit == true) { ?>
                    <div class="widget-box">
                        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/assets/js/booking.js"></script>
                        <div id="bookingjs1">
                            <script type="text/javascript">
                                var widget1 = new TimekitBooking();
                                widget1.init({
                                    targetEl: '#bookingjs1',
                                    name: '<?php echo wp_kses_post($timekitName); ?>',
                                    email: '<?php echo wp_kses_post($timekitEmail); ?>',
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

                <?php if (!empty($resurva_url)) { ?>
                    <div class="make-reservation-outer"><a href="" class="secondary-btn make-reservation">
                            <i class="fa fa-calendar-check-o"></i>
                            <?php echo esc_html__('Book Now', 'listingpro'); ?>
                        </a>
                    </div>
                    <div class="ifram-reservation">
                        <div class="inner-reservations">
                            <a href="#" class="close-btn"><i class="fa fa-times"></i></a>
                            <iframe src="<?php echo esc_url($resurva_url); ?>" name="resurva-frame" frameborder="0"></iframe>
                        </div>
                    </div>
                <?php }
                ?>
            </div>
            <div class="app-view-new-ann-dis">
                <?php
                $coup_count = 0;
                if ($discounts_show == 'true' && !empty($listing_discount_data)) {
                ?>
                    <div class="app-view-dis-wrap">
                        <?php
                        foreach ($listing_discount_data as $listing_discount_datum) {
                            $coup_count++;
                        ?>

                            <?php




                            $btn_href   =   '';
                            $btn_class  =   'lp-copy-code';
                            if ($listing_discount_datum['disBL'] && !empty($listing_discount_datum['disBL'])) {
                                $btn_href   =   'href="' . $listing_discount_datum['disBL'] . '"';
                                $btn_class  =   '';
                            }
                            ?>
                            <div class="code-overlay"></div>
                            <div id="coup-<?php echo get_the_ID(); ?>" class="lp-listing-bottom-right">
                                <?php
                                if (!empty($listing_discount_datum['disOff']) || !empty($listing_discount_datum['disHea'])) :
                                    $hea_off    =   $listing_discount_datum['disOff'] . ' ' . $listing_discount_datum['disHea'];
                                    $off_    =   $listing_discount_datum['disOff'];
                                    if (strlen($listing_discount_datum['disOff']) > 18) {
                                        $off_   =   mb_substr($listing_discount_datum['disOff'], 0, 20) . '...';
                                    }

                                ?>
                                    <div class="discount-bar">
                                        <i class="fa fa-tags pull-left"></i>
                                        <?php if ($listing_discount_datum['disOff']) echo esc_attr($listing_discount_datum['disOff']); ?>
                                        <?php
                                        if (strlen($listing_discount_datum['disOff']) < 18) {
                                            $new_len    =   15 - strlen($listing_discount_datum['disOff']);
                                            if (strlen($listing_discount_datum['disHea']) > $new_len) {
                                                echo mb_substr($listing_discount_datum['disHea'], 0, $new_len) . '...';
                                            } else {
                                                $listing_discount_datum['disHea'];
                                            }
                                        }
                                        ?>
                                        <i class="fa fa-chevron-down pull-right"></i>
                                    </div>

                                    <div class="coupons-bottom-content-wrap">
                                        <?php
                                        if (!empty($listing_discount_datum['disExpE'])) :
                                            $exTime = strtotime('12:00 AM');
                                            if (!empty($listing_discount_datum['disTimeE'])) {
                                                $exTime = strtotime($listing_discount_datum['disTimeE']);
                                            }
                                            $couponExpiry  = coupon_timestamp($listing_discount_datum['disExpE'], $exTime);
                                        ?>
                                            <div class="archive-countdown-wrap">
                                                <div id="lp-deals-countdown<?php echo esc_attr($coup_count); ?>" class="lp-countdown lp-deals-countdown<?php echo esc_attr($coup_count); ?>" data-label-hours="<?php echo esc_html__('hours', 'listingpro'); ?>" data-label-mins="<?php echo esc_html__('min', 'listingpro'); ?>" data-label-secs="<?php echo esc_html__('sec', 'listingpro'); ?>" data-label-days="<?php echo esc_html__('days', 'listingpro'); ?>" data-minute="<?php echo date('i', $couponExpiry); ?>" data-hour="<?php echo date('H', $couponExpiry); ?>" data-day="<?php echo date('d', $couponExpiry); ?>" data-month="<?php echo date('m', $couponExpiry) - 1; ?>" data-year="<?php echo date('Y', $couponExpiry); ?>"></div>
                                            </div>
                                        <?php endif; ?>

                                        <a target="_blank" data-target-code="deal-copy-<?php echo get_the_ID(); ?>" <?php echo wp_kses_post($btn_href); ?> class="deal-button <?php echo esc_attr($btn_class); ?>"><i class="fa fa-gavel" aria-hidden="true"></i> <?php echo esc_attr($listing_discount_datum['disBT']); ?></a>

                                        <div class="dis-code-copy-pop deal-copy-<?php echo get_the_ID(); ?>" id="dicount-copy-<?php echo get_the_ID(); ?>">
                                            <span class="close-right-icon" data-target="deal-copy-<?php echo get_the_ID(); ?>"><i class="fa fa-times"></i></span>
                                            <div class="dis-code-copy-pop-inner">
                                                <div class="dis-code-copy-pop-inner-cell">
                                                    <p><?php echo esc_html__('Copy to clipboard', 'listingpro'); ?></p>
                                                    <p class="dis-code-copy-wrap"><input class="code-top-copy-<?php echo get_the_ID(); ?>" type="text" value="<?php echo esc_attr($listing_discount_datum['disCod']); ?>"> <a data-target-code="dicount-copy-<?php echo get_the_ID(); ?>" href="#" class="copy-now" data-coppied-label="<?php echo esc_html__('Copied', 'listingpro'); ?>"><?php echo esc_html__('Copy', 'listingpro'); ?></a></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                        <?php
                        }
                        ?>

                    </div>
                <?php
                }
                ?>

                <?php get_template_part('templates/single-list/listing-details-style3/content/list-announcements'); ?>
                <?php //get_template_part( 'templates/single-list/listing-details-style1/content/list-deals' ); 
                ?>
            </div>
            <div class="app-view-new-devider"></div>
            <div class="app-view-new-reviews app-view-new-sec">
                <h4><?php echo esc_html__('RATING', 'listingpro'); ?></h4>
                <div class="app-view-news-reviews-inner">
                    <?php $rating = get_post_meta(get_the_ID(), 'listing_rate', true); ?>
                    <p><?php esc_html_e('Rated', 'listingpro'); ?> <?php echo esc_attr($rating); ?> <?php esc_html_e('out of 5.0 based on ', 'listingpro'); ?><?php echo esc_attr($NumberRating); ?> <?php esc_html_e('ratings', 'listingpro'); ?></p>
                    <?php
                    $rating = (int) $rating;
                    if (!empty($rating)) {
                        $data_percent = @($rating / 5) * 100;
                    }
                    $data_percent = '';
                    $data_percent_avg   =   '0';
                    if (!empty($data_percent)) {
                        if ($data_percent < 10) {
                            $data_percent_avg   =   '8';
                        } elseif ($data_percent < 20) {
                            $data_percent_avg   =   '10';
                        } elseif ($data_percent < 30) {
                            $data_percent_avg   =   '20';
                        } elseif ($data_percent < 40) {
                            $data_percent_avg   =   '30';
                        } elseif ($data_percent < 40) {
                            $data_percent_avg   =   '30';
                        } elseif ($data_percent < 50) {
                            $data_percent_avg   =   '40';
                        } elseif ($data_percent < 60) {
                            $data_percent_avg   =   '50';
                        } elseif ($data_percent < 70) {
                            $data_percent_avg   =   '60';
                        } elseif ($data_percent < 80) {
                            $data_percent_avg   =   '70';
                        } elseif ($data_percent < 90) {
                            $data_percent_avg   =   '80';
                        } elseif ($data_percent < 100) {
                            $data_percent_avg   =   '90';
                        } elseif ($data_percent == 100) {
                            $data_percent_avg   =   '100';
                        }
                    }
                    $lp_multirating_switch  =   $listingpro_options['lp_multirating_switch'];
                    ?>
                    <?php
                    if ($lp_multirating_switch) {

                        $lp_multi_rating_fields =   get_listing_multi_ratings_fields(get_the_ID());


                    ?>
                        <div class="row">
                            <div class="col-xs-6">
                                <?php
                                if ($lp_multi_rating_fields) {
                                    $lp_listingpro_options_meta  =   get_post_meta(get_the_ID(), 'lp_listingpro_options', true);
                                    $listing_reviews_ids    =   $lp_listingpro_options_meta['reviews_ids'];
                                    $listing_reviews_ids_arr    =   explode(',', $listing_reviews_ids);

                                    foreach ($lp_multi_rating_fields as $k => $v) {
                                        $field_sum  =   0;
                                        $reviews_count_multi    =   0;
                                        foreach ($listing_reviews_ids_arr as $listing_review_id) {
                                            $review_meta    =   get_post_meta($listing_review_id, 'lp_listingpro_options', true);
                                            if (isset($review_meta[$k])) {
                                                $reviews_count_multi++;
                                                $field_sum  +=  $review_meta[$k];
                                            }
                                        }
                                        $rating_field_val = 0;
                                        if ($field_sum != 0) {
                                            $rating_field_val           =   @ceil($field_sum / $reviews_count_multi);
                                        }

                                        $rating_field_val_percent   =   ($rating_field_val / 5) * 100;
                                ?>
                                        <label><?php echo esc_attr($v); ?></label>
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo esc_attr($rating_field_val_percent); ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo esc_attr($rating_field_val_percent); ?>%">
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                            </div>
                        <?php
                    }
                        ?>
                        <?php
                        if ($lp_multirating_switch) {
                            echo '<div class="col-xs-6">';
                        }
                        if ($rating > 0) {
                        ?>
                            <div class="reviews-circle" data-percentage="<?php echo esc_attr($data_percent_avg); ?>">
                                <span class="review-progress-left">
                                    <span class="review-progress-bar"></span>
                                </span>
                                <span class="review-progress-right">
                                    <span class="review-progress-bar"></span>
                                </span>
                                <div class="review-progress-value">
                                    <div>
                                        <?php echo esc_attr($rating); ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        if ($lp_multirating_switch) {
                            echo '</div><div class="clearfix"></div> </div>';
                        }
                        ?>

                        </div>
                        <div class="single-inner-container-inner clearfix">


                            <div class="clearfix"></div>


                            <?php
                            //comments_template();

                            $allowedReviews = $listingpro_options['lp_review_switch'];
                            if (!empty($allowedReviews) && $allowedReviews == "1") {
                                if (get_post_status($post->ID) == "publish") {
                                    listingpro_get_reviews_form($post->ID);
                                }
                            }

                            ?>
                        </div>
                </div>

                <div class="app-view-new-devider"></div>
                <div class="app-view-new-info app-view-new-sec">
                    <h4><?php echo esc_html__('INFORMATION', 'listingpro'); ?></h4>
                    <?php
                    $claimed_section = listing_get_metabox('claimed_section');

                    $show_lead_form =   get_post_meta($plan_id, 'listingproc_leadform', true);
                    if ($plan_id == 'none') {
                        $showleadform =   true;
                    } else {
                        $showleadform   =   $show_lead_form;
                    }
                    if (isset($listingpro_options['lp_listing_claim_switch'])) {
                        if ($listingpro_options['lp_listing_claim_switch'] == 1) {
                            $showClaim = true;
                        } else {
                            $showClaim = false;
                        }
                    } else {
                        $showClaim = false;
                    }
                    $listingpricestatus = listing_get_metabox_by_ID('price_status', get_the_ID());


                    $lp_leadForm = $listingpro_options['lp_lead_form_switch'];
                    $claimed_section = listing_get_metabox('claimed_section');
                    $show_leadform_only_claimed = $listingpro_options['lp_lead_form_switch_claim'];
                    $show_lead_form =   get_post_meta($plan_id, 'listingproc_leadform', true);
                    if ($plan_id == 'none') {
                        $showleadform =   true;
                    } else {
                        $showleadform   =   $show_lead_form;
                    }
                    if ($show_leadform_only_claimed == true) {
                        if ($claimed_section == 'claimed') {
                            $showleadform = true;
                        } else {
                            $showleadform = false;
                        }
                    }
                    $website = listing_get_metabox('website');
                    $email_switcher = lp_theme_option('listingpro_email_display_switch');
                    $email = listing_get_metabox('email');
                    ?>
                    <?php if ((!empty($email) && $email_switcher == 'yes') || (!empty($website) && $website_show == 'true') || $showleadform == true || ($showReport == true && is_user_logged_in()) || (!empty($menuMeta) && $menuOption == true) || !empty($listingpTo) || !empty($listingprice) ||  ($showClaim == true && $claimed_section == 'not_claimed') || $listingpricestatus != "notsay") { ?>
                        <div class="widget-box listing-price">
                            <?php
                            if (!empty($menuMeta) && $menuOption == true) {
                            ?>
                                <div class="menu-hotel">
                                    <a href="#" class="open-modal">
                                        <?php echo listingpro_icons('resMenu'); ?>
                                        <span>
                                            <?php if (!empty($menuTitle)) {
                                                echo esc_attr($menuTitle);
                                            } else {
                                                echo esc_html__('See Full Menu', 'listingpro');
                                            } ?>
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
                                if ($price_show == "true") {
                                    echo listingpro_price_dynesty(get_the_ID());
                                }
                                ?>
                                <?php get_template_part('templates/single-list/claimed'); ?>
                                <?php
                                $whatsappStatus = $listingpro_options['lp_detail_page_whatsapp_button'];
                                $whatsappMsg = esc_html__('Hi, Contacting for you listing', 'listingpro');
                                if ($whatsappStatus == "on" && !empty($whatsapp)) {

                                    $whatsappobj = "https://api.whatsapp.com/send?";
                                    $whatsappobj .= "phone=$whatsapp";
                                    $whatsappobj .= "&";
                                    $whatsappobj .= "text=$whatsappMsg";
                                ?>
                                    <div class="lp-listing-phone-whatsapp claim-area ">
                                        <a class="phone-icon" href="<?php echo esc_url($whatsappobj); ?>" target="_blank">
                                            <span class="cat-icon">
                                                <i class="fa fa-whatsapp" aria-hidden="true"></i>
                                            </span>
                                            <strong>
                                                <?php echo esc_html__('Call on Whatsapp', 'listingpro'); ?>
                                            </strong>
                                        </a>
                                    </div>

                                <?php } ?>
                                <?php

                                if ($lp_leadForm == "1") {

                                    if ($showleadform == 'true') {
                                ?>
                                        <div class="claim-area app-view-lead-form-row">
                                            <span class="phone-icon">
                                                <img class="icon icons8-Message" width="20" height="20" alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAPnSURBVGhD7ZhZbA1RHMZbSy2xVD1YQvGECCnxIuJFaJAQRONBiFgqQiWVNG1vNxHCK03sEsuLiHijJJZYniSIaPCApGh5sNQWFK3fN/1P3cm9vb295fZOM1/y9T/3fP9zzv+bmXNmpmkBAgQI4C+UlJQM8zurqqoGpvGnpQfwQLiR935jZWXlV6LXiN1pvgJGNvdcIzDHdN8AIwejGfkEF1hOSiM/P78vJo6H1e4x0oL4E663/JREUVHRYGq8HF439BiphM12vIs+6a1dUwfUNQYTD1QjsR4etnq9i524BvGHtZ2CGc4IKQBqmUZtL1Ub8SExm9j+rsXxXBI+qI14lZhpUreBGnKh1rBqvVJcXDxU7TGNCOXl5VNIqjOtFmablHQw9zrYZLWcgG13SYdGhFAoNIrEu9KI9ZibblKykM7cO60+rd0damuVWhGXEYH2QSRfkE78TEzK9sw8Gcx3UvNCXY21JnkQtxEhLy+vN/oh5dBR2/NGk/4LmCeTObQ2VVcjnGdSBDplxAWdisn7r9sz42ZDrUnN8YLbeapJUZGQEYGOK8n9rnyOTxP/2fbMWDNgg419nzjapHaRsBGhoqJiDgO8s37X9GFjUsJgnEWMqTUoEzV6epsUE10yInDJJzLIM+tbW1ZWNs6kToNxNjHGLxvrCOxjUofoshGBK7PM7cuAr4kzTYoX2l73umPA53CIaXGhy0YYYAt93IfUG4tf4BJLiYmCgoJ+5J6xfhrnrY4Z9zFxkqV1iISNkNefzu7+3szxHmIG3G9tukW2WnpUoGfBm5bfyJWdz605luM7amPMj8Tllh4TCRkhZzwdnSc91HuPZzJ+b0f/LZ1YTexvUhv06kP7E8upC99elQ+PSYM6Sbv1DDM5KsjpnBH0ebDt8lPAZJM8QF+B/k158CkMccYX07YKHuW3u3XrhETdXtG0+J08eIldcbhJESA3biNakCXQ3VXOw5gLUmeZCfQccMYNJ+16M9jHccTVCgfmZ5HzyvppE4j6GR6XEdoGwXOmy0iI5nif5ukUs5CJqmENfc8TWQrxb9OlpaUj6HND8xO/wtUmtYG22Eb0nKDtkWm6pXJNSirs21xX0KlRx2ozObYRjpeQoJ1D7fc4ixNM6jZQj9aY88844i3iSGuPNAJ7wV3QeTEk6WRhYeEAZ6QUADXlQK0X1fpK66g9IxctNpGwxfqnFKgti9q05nSif8DrVrPHiNiA09nWLyWhZwsGdlOr+0kRYeS2Pm8tP+VBvUsx5K7lv0bCdwO/gLq3RRgxzVfgikQudhrzfEi99niN+JyOkbM9gBvsbgsQIECAAMlEWtoffgVJFTkMC6UAAAAASUVORK5CYII=">
                                                <strong><?php echo esc_html__('Contact Listing Owner', 'listingpro'); ?></strong>
                                            </span>
                                            <a class="phone-number leadformtrigger md-trigger">
                                                <?php echo esc_html__('Contact Now!', 'listingpro'); ?></a>
                                        </div>
                                        <div style="display: none;" class="widget-box business-contact app-view-lead-form">
                                            <?php get_template_part('mobile/templates/listing_app_view_leadform'); ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <div class="clearfix"></div>
                                <?php
                                if ($showReport == true && is_user_logged_in()) { ?>
                                    <div id="lp-report-listing" class="claim-area">
                                        <span class="phone-icon">
                                            <!-- Flag 2 icon by Icons8 -->
                                            <img class="icon icons8-Flag-2" width="20" height="20" alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAABAElEQVQ4T+1T0W2DUBDz3QTpBoyQERiBDZpJkAUDVN2gI6QbNBMkG4QNkgXgIotHlaYkNKjKV56EkLg7n5+xjWQREa8AsogwAEf0JzOzbUSs3f2T5PA9lcdfVpZl4+4Fyd1lC8kMQNF13UrgZvY+1qc5kisRE+BXVVX5zbX9QC5gAEszOwBodAvNRcQLgLWW/xnwfCHJhYAlzzljkZsFeO02T8Cp/zxdf4yG8lvbtlHX9Waa08+OgWFjZvtkTsVreGTaRaopfhuSMvPoSUl5kw937p5fyypJGVgpUZqUkn1CVFRV+06Ku3/MMrZkSaDNJetZgLe0fQLe67zf/f+u4QlxCbp3x7Q50wAAAABJRU5ErkJggg=="> <strong> <?php echo esc_html__('See something wrong? ', 'listingpro'); ?> </strong>
                                        </span>
                                        <!-- new code 2.6.15 -->
                                        <a data-toggle="modal" data-target="#lp_report_listing" class="phone-number" data-postid="<?php echo get_the_ID(); ?>" data-reportedby="<?php echo esc_attr($currentUserId); ?>" data-posttype="listing" href="#" id="lp-report-this-listing-popup"> <?php echo esc_html__('Report Now!', 'listingpro'); ?></a>
                                        <!-- new code 2.6.15 -->

                                    </div>
                                <?php } ?>
                                <?php
                                $email_switcher = lp_theme_option('listingpro_email_display_switch');
                                $email = listing_get_metabox('email');
                                if ($email_switcher == 'yes') { ?>
                                    <div class="lp-user-web">
                                        <a data-lpID="<?php echo esc_attr($post->ID); ?>" href="mailto:<?php echo esc_attr($email); ?>">
                                            <span class="cat-icon">
                                                <i class="fa fa-envelope"></i>
                                            </span>
                                            <span>
                                                <?php echo esc_html($email); ?>
                                            </span>
                                        </a>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($website)) {
                                    if ($website_show == "true") { ?>
                                        <div class="lp-user-web">
                                            <a data-lpID="<?php echo get_the_ID(); ?>" href="<?php echo esc_url($website); ?>" target="_blank">
                                                <span class="cat-icon">
                                                    <?php echo listingpro_icons('globe'); ?>
                                                    <!-- <i class="fa fa-globe"></i> -->
                                                </span>
                                                <span><?php echo esc_url($website); ?></span>
                                            </a>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <?php if (is_user_logged_in()) { ?>
                                <?php get_template_part('templates/single-list/claim-form'); ?>
                            <?php } ?>

                        </div>
                    <?php } ?>
                    <?php
                    $facebook = listing_get_metabox('facebook');
                    $twitter = listing_get_metabox('twitter');
                    $linkedin = listing_get_metabox('linkedin');
                    $youtube = listing_get_metabox('youtube');
                    $instagram = listing_get_metabox('instagram');
                    if ($social_show == "true") {
                        if (empty($facebook) && empty($twitter) && empty($linkedin) && empty($youtube) && empty($instagram)) {
                        } else {
                    ?>
                            <div class="widget-box widget-social">
                                <div class="widget-content clearfix">
                                    <ul class="list-style-none list-st-img">
                                        <?php if (!empty($facebook)) { ?>
                                            <li class="lp-fb">
                                                <a href="<?php echo esc_url($facebook); ?>" class="padding-left-0" target="_blank">
                                                    <i class="fa fa-facebook"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if (!empty($twitter)) { ?>
                                            <li class="lp-tw">
                                                <a href="<?php echo esc_url($twitter); ?>" class="padding-left-0" target="_blank">
                                                    <i class="fa-brands fa-square-x-twitter"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if (!empty($linkedin)) { ?>
                                            <li class="lp-li">
                                                <a href="<?php echo esc_url($linkedin); ?>" class="padding-left-0" target="_blank">
                                                    <i class="fa fa-linkedin"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if (!empty($youtube)) { ?>
                                            <li class="lp-yt">
                                                <a href="<?php echo esc_url($youtube); ?>#" class="padding-left-0" target="_blank">
                                                    <i class="fa fa-youtube"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if (!empty($instagram)) { ?>
                                            <li class="lp-insta">
                                                <a href="<?php echo esc_url($instagram); ?>#" class="padding-left-0" target="_blank">
                                                    <i class="fa fa-instagram"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>

                            </div><!-- ../widget-box  -->

                        <?php } ?>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>
                <div class="app-view-new-devider"></div>
                <div class="app-view-new-location app-view-new-sec">
                    <h4><?php echo esc_html__('LOCATION', 'listingpro'); ?></h4>
                    <div class="widget-box map-area">
                        <?php
                        $latitude = listing_get_metabox('latitude');
                        $longitude = listing_get_metabox('longitude');
                        $gAddress = listing_get_metabox('gAddress');
                        if (!empty($latitude) && !empty($longitude)) {
                            if (!empty($gAddress)) {
                                if ($location_show == "true") {
                        ?>
                                    <div class="add-on-map">
                                        <a>
                                            <span class="cat-icon">
                                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                                <!-- <i class="fa fa-map-marker"></i> -->
                                            </span>
                                            <span>
                                                <?php echo esc_attr($gAddress) ?>
                                            </span>
                                        </a>
                                    </div>
                                <?php } ?>
                            <?php }
                            if ($map_show == "true") {
                            ?>
                                <div class="widget-bg-color post-author-box lp-border-radius-5">
                                    <div class="widget-header margin-bottom-25 hideonmobile">
                                        <ul class="post-stat">
                                            <li>
                                                <a class="md-trigger parimary-link singlebigmaptrigger" data-lat="<?php echo esc_attr($latitude); ?>" data-lan="<?php echo esc_attr($longitude); ?>" data-modal="modal-4">
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
                                            <div id="singlepostmap" data-lat="<?php echo esc_attr($latitude); ?>" data-lan="<?php echo esc_attr($longitude); ?>" class="singlemap" data-pinicon="<?php echo esc_attr($lp_map_pin); ?>"></div>

                                        </div>
                                    </div>
                                </div><!-- ../widget-box  -->
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                <div class="app-view-new-devider"></div>
                <div class="app-view-new-tabs app-view-new-sec">
                    <div class="listing-tabs app-view">
                        <?php
                        $listingContent = get_the_content();
                        $faqs = listing_get_metabox('faqs');
                        $video = listing_get_metabox('video');
                        $lp_listing_menus   =   get_post_meta(get_the_ID(), 'lp-listing-menu', true);

                        $tags = get_the_terms($post->ID, 'features');
                        $extra_field_markup =   listing_all_extra_fields($post->ID);
                        $faq_count = 0;
                        if (!empty($faqs)) {
                            $faq_count  =   array_filter($faqs['faq']);
                            if (!empty($faq_count)) {
                                $faq_count  =   count($faq_count);
                            }
                        }
                        ?>
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <?php if ($listingContent != '') : ?><li role="presentation" class="active"><a href="#listing-detail" aria-controls="listing-detail" role="tab" data-toggle="tab"><?php echo esc_html__('Description  ', 'listingpro'); ?></a></li> <?php endif; ?>
                            <?php
                            if (!empty($extra_field_markup) || (!empty($tags) && count($tags) > 0 && $tags_show == true)) :
                            ?>
                                <li role="presentation"><a href="#listing-des" aria-controls="listing-des" role="tab" data-toggle="tab"><?php echo esc_html__('Details', 'listingpro'); ?></a></li>
                            <?php endif; ?>
                            <?php if (!empty($video) && $video_show == 'true') : ?> <li role="presentation"><a href="#listing-video" aria-controls="listing-video" role="tab" data-toggle="tab"><?php echo esc_html__('Video', 'listingpro'); ?></a></li><?php endif; ?>
                            <?php if (is_array($faq_count) || is_object($faq_count)) if ($faqs_show == "true" && count($faq_count) > 0) : ?><li role="presentation"><a href="#listing-faq" aria-controls="listing-faq" role="tab" data-toggle="tab"><?php echo esc_html__("FAQ's", 'listingpro'); ?></a></li> <?php endif; ?>
                            <?php if (is_array($lp_listing_menus) && !empty($lp_listing_menus) && $menu_show == "true") { ?>
                                <li><a href="#listing-menu" aria-controls="listing-menu" role="tab" data-toggle="tab"><?php echo esc_html__('Menu', 'listingpro'); ?></a></li>
                            <?php } ?>
                            <?php
                            if ($events_show == 'true') {
                            ?>
                                <li><a href="#listing-events" aria-controls="listing-events" role="tab" data-toggle="tab"><?php echo esc_html__('Events', 'listingpro'); ?></a></li>
                            <?php
                            }
                            ?>
                            <li><a href="#listing-reviews" aria-controls="listing-reviews" role="tab" data-toggle="tab"><?php echo esc_html__('Reviews', 'listingpro'); ?></a></li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <?php if ($listingContent != '') : ?>
                                <div role="tabpanel" class="tab-pane active" id="listing-detail">
                                    <div class="post-detail-content">
                                        <?php the_content(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($faqs_show == "true" && $faq_count > 0) : ?>
                                <div role="tabpanel" class="tab-pane" id="listing-faq">
                                    <?php
                                    $faq = $faqs['faq'];
                                    $faqans = $faqs['faqans'];
                                    if (!empty($faq[1])) {
                                    ?>
                                        <div class="post-row faq-section padding-top-10 clearfix">
                                            <!-- <div class="post-row-header clearfix margin-bottom-15">
                                                <h3><?php echo esc_html__('Quick questions', 'listingpro'); ?></h3>
                                            </div> -->
                                            <div class="post-row-accordion">
                                                <div id="accordion">
                                                    <?php for ($i = 1; $i <= (count($faq)); $i++) { ?>
                                                        <?php if (!empty($faq[$i])) { ?>
                                                            <h5>
                                                                <span class="question-icon"><?php echo esc_html__('Q', 'listingpro'); ?></span>
                                                                <span class="accordion-title"><?php echo esc_html($faq[$i]); ?></span>
                                                            </h5>
                                                            <div>
                                                                <p>
                                                                    <?php //echo do_shortcode($faqans[$i]); 
                                                                    ?>
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
                            <?php if (!empty($video) && $video_show == 'true') : ?>
                                <div role="tabpanel" class="tab-pane" id="listing-video">
                                    <?php if (!empty($video) && $video_show == 'true') : ?>
                                        <?php if (wp_oembed_get($video)) { ?>
                                            <?php echo wp_oembed_get($video); ?>
                                        <?php } else { ?>
                                            <?php echo wp_kses_post($video); ?>
                                        <?php } ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php
                            if (!empty($extra_field_markup) || (!empty($tags) && count($tags) > 0 && $tags_show == true)) :
                            ?>
                                <div role="tabpanel" class="tab-pane" id="listing-des">
                                    <?php
                                    if (!empty($tags) && count($tags) > 0) {
                                        if ($tags_show == "true") { ?>

                                            <div class="post-row">
                                                <div class="post-row-header post-row-header-features clearfix margin-bottom-15 padding-top-20">
                                                    <h3><?php echo esc_html__('Features', 'listingpro'); ?></h3>
                                                </div>
                                                <!-- <div class="post-row-header clearfix margin-bottom-15"><h3><?php echo esc_html__('Features', 'listingpro'); ?></h3></div> -->
                                                <ul class="features list-style-none clearfix">
                                                    <?php
                                                    foreach ($tags as $tag) {
                                                        $icon = listingpro_get_term_meta($tag->term_id, 'lp_features_icon');
                                                    ?>
                                                        <li>
                                                            <a href="<?php echo get_term_link($tag); ?>" class="parimary-link">
                                                                <span class="tick-icon">
                                                                    <?php if (!empty($icon)) { ?>
                                                                        <i class="fa <?php echo esc_attr($icon); ?>"></i>
                                                                    <?php } else { ?>
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
                                    <?php echo listing_all_extra_fields($post->ID); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (is_array($lp_listing_menus) && !empty($lp_listing_menus) && $menu_show == "true") {

                                require_once(THEME_PATH . "/include/aq_resizer.php");
                            ?>
                                <!--Start App View Menu-->
                                <div role="tabpanel" class="tab-pane" id="listing-menu">
                                    <div class="post-detail-content">
                                        <?php
                                        $user_id = get_the_author_meta('ID');
                                        $ordering_services = array();
                                        $get_ordering_services = get_user_meta($user_id, 'order_services', [0]);
                                        if (!empty($get_ordering_services)) {
                                            $ordering_services = $get_ordering_services;
                                        }
                                        if (!empty($ordering_services) || count($ordering_services) != 0) {
                                        ?>
                                            <div class="order_food_online_main-header">
                                                <div class="clearfix"></div>
                                                <h4 class="lp-detail-section-title pull-left"><?php echo esc_html__('Menu', 'listingpro'); ?></h4>
                                                <div class="pull-right order_food_online_container">
                                                    <span class="pull-left order_food_online_text"><?php echo esc_html__('order online', 'listingpro'); ?></span>
                                                    <div class="order_food_online_img pull-right">
                                                        <?php
                                                        foreach ($ordering_services as $k => $ordering_service) {
                                                            if ($ordering_service == esc_html__('Grubhub', 'listingpro')) {
                                                                echo '<a target="_blank" href="' . $k . '"><img src="' . get_template_directory_uri() . '/assets/images/menu_order/grubhub.png" alt="image"></a>';
                                                            } elseif ($ordering_service == esc_html__('Zomato', 'listingpro')) {
                                                                echo '<a target="_blank" href="' . $k . '"><img src="' . get_template_directory_uri() . '/assets/images/menu_order/zomato.png" alt="image"></a>';
                                                            } elseif ($ordering_service == esc_html__('Foodpanda', 'listingpro')) {
                                                                echo '<a target="_blank" href="' . $k . '"><img src="' . get_template_directory_uri() . '/assets/images/menu_order/food-panda.png" alt="image"></a>';
                                                            } elseif ($ordering_service == esc_html__('UberEats', 'listingpro')) {
                                                                echo '<a target="_blank" href="' . $k . '"><img src="' . get_template_directory_uri() . '/assets/images/menu_order/uber-eats.png" alt="image"></a>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        $lp_menu_type_auto_incr = 0;
                                        foreach ($lp_listing_menus as $menu_type => $lp_listing_menu) :

                                        ?>

                                            <div class="lp-menu-app-view-outer">

                                                <div class="lp-menu-type-heading" data-target="lp-menu-type-<?php echo esc_attr($lp_menu_type_auto_incr); ?>">

                                                    <span><?php echo esc_attr($menu_type); ?></span>
                                                    <i class="fa fa-angle-down" aria-hidden="true"></i>

                                                </div>

                                                <div class="lp-listing-appview-group-wrap lp-menu-type-<?php echo esc_attr($lp_menu_type_auto_incr); ?>">

                                                    <?php
                                                    $lp_menu_groups_auto_incr = 0;
                                                    foreach ($lp_listing_menu as $menu_group => $listing_menu) :

                                                        $total_menus    =   count($listing_menu);

                                                    ?>

                                                        <h6 class="lp-appview-group-heading" data-target="lp-menu-group-<?php echo esc_attr($lp_menu_groups_auto_incr); ?>">
                                                            <?php echo esc_attr($menu_group); ?><i class="fa fa-angle-down" aria-hidden="true"></i></h6>
                                                        <div class="lp-appview-menu-items-bygroup lp-menu-group-<?php echo esc_attr($lp_menu_groups_auto_incr); ?>">

                                                            <?php

                                                            $menu_counter   =   0;

                                                            foreach ($listing_menu as $lp_menu) :

                                                                $menu_counter++;

                                                                $menu_imgs      =   $lp_menu['mImage'];
                                                                $img_url_full   =   $menu_imgs;
                                                                $menu_images_arr    =   array();
                                                                if (strpos($menu_imgs, ',')) {
                                                                    $menu_images_arr    =   explode(',', $menu_imgs);
                                                                    $menu_images_arr    =   array_filter($menu_images_arr);
                                                                    $img_url    =   $menu_images_arr[0];
                                                                    $img_url_full   =   $menu_images_arr[0];
                                                                } else {
                                                                    $img_url    =   $menu_imgs;
                                                                }
                                                                if (empty($img_url)) {
                                                                    $img_url    =   get_template_directory_uri() . '/assets/images/menu-placeholder.jpg';
                                                                    $img_url_full   =   get_template_directory_uri() . '/assets/images/menu-placeholder.jpg';
                                                                } else {
                                                                    $img_url  = aq_resize($img_url, '65', '65', true, true, true);
                                                                }

                                                            ?>

                                                                <div class="lp-listing-appview-menu-item <?php if ($menu_counter == $total_menus) {
                                                                                                                echo 'last-item';
                                                                                                            } ?>">

                                                                    <div class="lp-menu-item-thumb">
                                                                        <?php
                                                                        if (is_array($menu_images_arr) && count($menu_images_arr) != 0) :
                                                                        ?>
                                                                            <div class="menu-gallery-pop" style="display: none;">
                                                                                <?php
                                                                                foreach ($menu_images_arr as $value) {
                                                                                    echo '<a rel="prettyPhoto[mgallery' . $menu_counter . ']" href="' . $value . '"><img alt="image" src="' . $value . '"></a>';
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                        <a href="<?php echo esc_url($img_url_full); ?>" rel="prettyPhoto[mgallery<?php echo esc_attr($menu_counter); ?>]"><img alt="image" src="<?php echo esc_url($img_url); ?>"></a>

                                                                    </div>

                                                                    <div class="lp-menu-item-detail">

                                                                        <a <?php if ($lp_menu['mLink']) : echo 'href="' . esc_url($lp_menu['mLink']) . '"';
                                                                            endif; ?> class="lp-menu-item-title"><?php echo esc_attr($lp_menu['mTitle']); ?></a>

                                                                        <?php

                                                                        if (!empty($lp_menu['mDetail'])) :

                                                                        ?>

                                                                            <span class="lp-menu-item-tags"><?php echo esc_attr($lp_menu['mDetail']); ?></span>

                                                                        <?php endif; ?>

                                                                    </div>

                                                                    <div class="lp-menu-item-price">
                                                                        <?php
                                                                        if (empty($lp_menu['mQuoteT'])) :
                                                                            $line_through =   '';
                                                                            if ($lp_menu['mNewPrice']) {
                                                                                $line_through   =   'line-through';
                                                                            }
                                                                        ?>
                                                                            <?php
                                                                            if ($lp_menu['mOldPrice']) :
                                                                            ?>
                                                                                <span class="old-price <?php echo esc_attr($line_through); ?>"><?php echo esc_attr($lp_menu['mOldPrice']); ?></span>
                                                                            <?php endif; ?>
                                                                            <?php
                                                                            if ($lp_menu['mNewPrice']) :
                                                                            ?>
                                                                                <span><?php echo esc_attr($lp_menu['mNewPrice']); ?></span>
                                                                            <?php endif; ?>
                                                                        <?php
                                                                        else :
                                                                            $quote_url  =   $lp_menu['mQuoteL'];
                                                                            if (empty($quote_url) || $quote_url == '#') {
                                                                                $quote_url  =   get_home_url();
                                                                            }
                                                                        ?>
                                                                            <a target="_blank" href="<?php echo esc_url($quote_url); ?>"><?php echo esc_attr($lp_menu['mQuoteT']); ?></a>
                                                                        <?php endif; ?>
                                                                    </div>

                                                                    <div class="clearfix"></div>

                                                                </div>

                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php $lp_menu_groups_auto_incr++;
                                                    endforeach; ?>

                                                </div>



                                            </div>

                                        <?php $lp_menu_type_auto_incr++;
                                        endforeach; ?>


                                    </div>
                                </div>
                                <!--End App View Menu-->
                            <?php } ?>
                            <?php
                            if ($events_show == 'true') {
                            ?>
                                <div role="tabpanel" class="tab-pane" id="listing-events">
                                    <div class="lp-appview-event">
                                        <?php get_template_part('templates/single-list/event'); ?>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                            <div role="tabpanel" class="tab-pane" id="listing-reviews">
                                <div id="submitreview">
                                    <?php
                                    //getting old reviews
                                    listingpro_get_all_reviews_app_view($post->ID);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="app-view-new-devider"></div>
                    <?php
                    if (class_exists('Listingpro_bookings')) {
                        include(ABSPATH . 'wp-content/plugins/listingpro-bookings/templates/bookings.php');
                    } ?>
                </div>
                <div class="content-white-area">
                    <?php
                    if (isset($listingpro_options['lp-gads-editor'])) {
                        $listingGAdsense = $listingpro_options['lp-gads-editor'];
                        if (!empty($listingGAdsense)) { ?>

                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <?php echo apply_filters('listingpro_show_google_ads', 'listing', get_the_ID()); ?>
                                </div>
                            </div>
                    <?php }
                    }
                    ?>
                    <div class="container single-inner-container single_listing">
                        <div class="row">


                            <div class="col-md-4 col-sm-4 col-xs-12">
                                <div class="sidebar-post">
                                    <?php if ($post->post_status == 'publish') { ?>
                                        <?php if (is_active_sidebar('listing_detail_sidebar')) { ?>
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
