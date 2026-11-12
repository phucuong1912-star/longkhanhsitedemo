<?php
$listingpro_options =   get_option('listingpro_options');
//do not write above this line
/* ============== proceed only if version is active ============ */
//if( empty( $version2_active ) || $version2_active == 0 ) return false;
/* ============== version2 Style Load ============ */
add_action('wp_enqueue_scripts', 'listingpro_style_version2');
if (!function_exists('listingpro_style_version2')) {
    function listingpro_style_version2()
    {
        wp_enqueue_style('font-rock-salt', 'https://fonts.googleapis.com/css?family=Rock+Salt');
        wp_enqueue_style('font-quicksand', 'https://fonts.googleapis.com/css?family=Quicksand');
        wp_enqueue_style('version2-countdown', THEME_DIR . '/assets/lib/countdown/flipclock.css');
        wp_enqueue_style('version2-styles', THEME_DIR . '/assets/css/main-new.css');
        wp_enqueue_style('version2-colors', THEME_DIR . '/assets/css/colors-new.css');
        //    wp_enqueue_style('version2-font-family', THEME_DIR . '/layouts/assets/css/font-family.css');
        //For events app view map
        if (is_singular('events') && wp_is_mobile()) {
            wp_enqueue_script('singlepostmap-version2', THEME_DIR . '/assets/js/singlepostmap-new.js', 'jquery', '', true);
        }
    }
}
/* ============== version2 scripts Load ============ */
add_action('wp_enqueue_scripts', 'listingpro_scripts_version2');
if (!function_exists('listingpro_scripts_version2')) {
    function listingpro_scripts_version2()
    {
        global $listingpro_options;
        $current_page_url    =   get_permalink(get_the_ID());
        $mobile_view =   '';
        $listing_layout    = isset($listingpro_options['lp_detail_page_styles']) ? $listingpro_options['lp_detail_page_styles'] : '';
        $author_page_url = lp_theme_option("listing-author");
        $D_pageID = url_to_postid($author_page_url);
        if ($D_pageID) {
            $author_page_url = get_permalink($D_pageID);
        }
        wp_enqueue_script('version-countdown-js', THEME_DIR . '/assets/lib/countdown/flipclock.min.js', 'jquery', '', true);
        if (($listing_layout == 'lp_detail_page_styles4' || $listing_layout == 'lp_detail_page_styles3' && !wp_is_mobile())
            || ($listing_layout == 'lp_detail_page_styles4' || $listing_layout == 'lp_detail_page_styles3' && $mobile_view == 'responsive_view' && wp_is_mobile())
        ) {
            wp_enqueue_script('singlepostmap-version2', THEME_DIR . '/assets/js/singlepostmap-new.js', 'jquery', '', true);
        }
        if ($current_page_url == $author_page_url) {
            $lp_wp_lang    =   get_option('WPLANG');
            $available_locales  =   array(
                'de_DE',
                'af',
                'ar_DZ',
                'ar',
                'az',
                'be',
                'bg',
                'cs',
                'bs',
                'ca',
                'cy_GB',
                'da',
                'de',
                'el',
                'en_AU',
                'en_GB',
                'en_NZ',
                'eo',
                'es',
                'et',
                'eu',
                'fa',
                'fi',
                'fo',
                'fr_CA',
                'fr_CH',
                'fr',
                'gl',
                'he',
                'hi',
                'hr',
                'hu',
                'hy',
                'id',
                'is',
                'it_CH',
                'it',
                'js',
                'ka',
                'kk',
                'km',
                'ko',
                'ky',
                'lb',
                'lt',
                'lv',
                'mk',
                'ml',
                'ms',
                'nb',
                'nl_BE',
                'nl',
                'nn',
                'no',
                'pl',
                'pt',
                'pt_BR',
                'rm',
                'ro',
                'ru',
                'sk',
                'sl',
                'sq',
                'sr',
                'sr_SR',
                'sv',
                'ta',
                'tj',
                'pt_AO',
                'th',
                'tr',
                'uk',
                'vi',
                'zh_CN',
                'zh_HK',
                'zh_TW'
            );
            if (!empty($lp_wp_lang) && in_array($lp_wp_lang, $available_locales)) {
                wp_enqueue_script('datelocale', 'https://sandbox.listingprowp.com/datepicker-locales/datepicker-' . $lp_wp_lang . '.js', array('jquery-ui'));
            }
        }
        wp_enqueue_script('Main-Version2', THEME_DIR . '/assets/js/main-new.js', 'jquery', '', true);
    }
}
/* ============== version2 dynamic options ============ */
require_once THEME_PATH . '/include/dynamic-options-new.php';
/* ============== Check TIme ============ */
if (!function_exists('listingpro_check_time_v2')) {
    function listingpro_check_time_v2($postid, $status = false)
    {
        $output = '';
        $buisness_hours = listing_get_metabox_by_ID('business_hours', $postid);
        if (!empty($buisness_hours) && count($buisness_hours) > 0) {
            if (!empty($postid)) {
                $lat = listing_get_metabox_by_ID('latitude', $postid);
                $long = listing_get_metabox_by_ID('longitude', $postid);
            }
            //$timezone = getClosestTimezone($lat, $long);
            $timezone  = get_option('gmt_offset');
            $time = gmdate("H:i", time() + 3600 * ($timezone + date("I")));
            $day =  gmdate("l");
            $time = strtotime($time);
            $lang = get_locale();
            setlocale(LC_ALL, $lang . '.utf-8');
            $day = strftime("%A");
            $day = ucfirst($day);
            foreach ($buisness_hours as $key => $value) {
                $keyArray[] = $key;
                if ($day == $key) {
                    $dayName = esc_html__('Today', 'listingpro');
                } else {
                    $dayName = $key;
                }
                $opencheck = $value['open'];
                $open = $value['open'];
                $open = str_replace(' ', '', $open);
                $closecheck = $value['close'];
                $close = $value['close'];
                $close = str_replace(' ', '', $close);
                $open = @strtotime($open);
                $close = @strtotime($close);
                $newTimeOpen = date('h:i A', $open);
                $newTimeClose = date('h:i A', $close);
                if ($day == $key) {
                    if (empty($opencheck) && empty($closecheck)) {
                        if ($status == false) {
                            $output = esc_html__('24 hours open', 'listingpro');
                        } else {
                            $output = 'open';
                        }
                    } elseif ($time > $open && $time < $close) {
                        if ($status == false) {
                            $output = esc_html__('Open', 'listingpro');
                        } else {
                            $output = 'open';
                        }
                    } else {
                        if ($status == false) {
                            $output = esc_html__('Closed', 'listingpro');
                        } else {
                            $output = 'close';
                        }
                    }
                }
            }
            if (is_array($keyArray) && !in_array($day, $keyArray)) {
                $output = esc_html__('Day Off!', 'listingpro');
            }
        } else {
            if ($status == true) {
                $output = 'close';
            }
        }
        return $output;
    }
}
/* ============== Top bar share icons ============ */
if (!function_exists('listingpro_sharing_topbar')) {
    function listingpro_sharing_topbar()
    {
        global  $listingpro_options;
        $fb_h =   $listingpro_options['fb_h'];
        $tw_h =   $listingpro_options['tw_h'];
        $insta_h =   $listingpro_options['insta_h'];
        $tumb_h =   $listingpro_options['tumb_h'];
        $f_yout_h =   $listingpro_options['f-yout_h'];
        $f_linked_h =   $listingpro_options['f-linked_h'];
        $f_pintereset_h =   $listingpro_options['f-pintereset_h'];
        $f_vk_h =   $listingpro_options['f-vk_h'];
?>
        <ul>
            <?php
            if ($fb_h != '' && $fb_h != '#') {
                echo '<li><a href="' . $fb_h . '" target="_blank"><i class="fa fa-facebook"></i></a></li>';
            }
            if ($tw_h != '' && $tw_h != '#') {
                echo '<li><a href="' . $tw_h . '" target="_blank"><i class="fa-brands fa-square-x-twitter"></i></a></li>';
            }
            if ($insta_h != '' && $insta_h != '#') {
                echo '<li><a href="' . $insta_h . '" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>';
            }
            if ($tumb_h != '' && $tumb_h != '#') {
                echo '<li><a href="' . $tumb_h . '" target="_blank"><i class="fa fa-tumblr" aria-hidden="true"></i></a></li>';
            }
            if ($f_yout_h != '' && $f_yout_h != '#') {
                echo '<li><a href="' . $f_yout_h . '" target="_blank"><i class="fa fa-youtube-play" aria-hidden="true"></i></a></li>';
            }
            if ($f_linked_h != '' && $f_linked_h != '#') {
                echo '<li><a href="' . $f_linked_h . '" target="_blank"><i class="fa fa-linkedin"></i></a></li>';
            }
            if ($f_pintereset_h != '' && $f_pintereset_h != '#') {
                echo '<li><a href="' . $f_pintereset_h . '" target="_blank"><i class="fa fa-pinterest"></i></a></li>';
            }
            if ($f_vk_h != '' && $f_vk_h != '#') {
                echo '<li><a href="' . $f_vk_h . '" target="_blank"><i class="fa fa-vk" aria-hidden="true"></i></a></li>';
            }
            ?>
        </ul>
    <?php
    }
}
/* ============== Detail Page Reviews ============ */
if (!function_exists('listingpro_get_all_reviews_v2')) {
    function listingpro_get_all_reviews_v2($postid)
    {
        global $listingpro_options;
        $showReport = true;
        $reviewOrder = 'DESC';
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
            $l_title    =   get_the_title($postid);
            if (isset($GLOBALS['listID']) && !empty($GLOBALS['listID'])) {
                $l_title    =   '<a href="' . get_permalink($postid) . '">' . get_the_title($postid) . '</a>';
            }
            if (count($active_reviews_ids) == 1) {
                $label = esc_html__('Review for ', 'listingpro') . $l_title;
            } else {
                $label = esc_html__('Reviews for ', 'listingpro') . $l_title;
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
        if (!empty($review_ids) && count($review_ids) > 0) {
            $review_ids = array_reverse($review_ids, true);
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
                    $review_reply = '';
                    $review_reply = listing_get_metabox_by_ID('review_reply', get_the_ID());
                    $review_reply_time = '';
                    $review_reply_time = listing_get_metabox_by_ID('review_reply_time', get_the_ID());
                    // moin here ends
                    $rating = number_format(listing_get_metabox_by_ID('rating', get_the_ID()), 1, '.', '');
                    $exRating = get_post_meta(get_the_ID(), 'rating', true);
                    if (empty($exRating)) {
                        update_post_meta(get_the_ID(), 'rating', $rating);
                    }
                    $rate = $rating;
                    $gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
                    $author_id = $post->post_author;
                    $author_avatar_url = get_user_meta($author_id, "listingpro_author_img_url", true);
                    $avatar = '';
                    if (!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url($author_id, $size = '94');
                        $avatar =  $avatar_url;
                    }
                    $user_reviews_count = count_user_posts($author_id, 'lp-reviews');
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
                    $reacted_msg    =   esc_html__('You already reacted', 'listingpro');
                    $rating_num_bg  =   '';
                    $rating_num_clr  =   '';
                    if ($rating < 2) {
                        $rating_num_bg  =   'num-level1';
                        $rating_num_clr  =   'level1';
                    } elseif ($rating < 3) {
                        $rating_num_bg  =   'num-level2';
                        $rating_num_clr  =   'level2';
                    } elseif ($rating < 4) {
                        $rating_num_bg  =   'num-level3';
                        $rating_num_clr  =   'level3';
                    } elseif ($rating >= 4) {
                        $rating_num_bg  =   'num-level4';
                        $rating_num_clr  =   'level4';
                    }
            ?>
                    <div class="lp-listing-review">
                        <div class="lp-review-left">
                            <div class="lp-review-thumb">
                                <a href="<?php echo get_author_posts_url($author_id); ?>">
                                    <img src="<?php echo esc_attr($avatar); ?>" alt="image">
                                </a>
                            </div>
                            <span class="lp-review-name"><a href="<?php echo get_author_posts_url($author_id); ?>"><?php the_author(); ?></a></span>
                            <span class="lp-review-count"><i class="fa fa-star" aria-hidden="true"></i> <?php echo esc_attr($user_reviews_count); ?> <?php esc_html_e('Reviews', 'listingpro'); ?></span>
                        </div>
                        <div class="lp-review-right">
                            <div class="lp-review-right-top">
                                <strong><?php the_title(); ?></strong>
                                <time><?php echo get_the_time('F j, Y g:i a'); ?></time>
                                <div class="lp-review-stars">
                                    <?php
                                    if ($lp_multi_rating_state == 1 && !empty($lp_multi_rating_state)) {
                                        $post_rating_data   =   get_post_meta($post->ID, 'lp_listingpro_options', true);
                                        $show_multi_rate_drop   =   false;
                                        if (is_array($lp_multi_rating_fields)) {
                                            if (count($lp_multi_rating_fields) > 0) {
                                                if (array_key_exists('field1', $post_rating_data)) {
                                                    $show_multi_rate_drop   =   true;
                                                }
                                            }
                                        }
                                        if ($show_multi_rate_drop) {
                                            echo '<a href="#" data-rate-box="multi-box-' . $post->ID . '" class="open-multi-rate-box"><i class="fa fa-chevron-down" aria-hidden="true"></i> ' . esc_html__('View All', 'listingpro') . '</a>';
                                    ?>
                                            <div class="lp-multi-star-wrap" id="multi-box-<?php echo esc_attr($post->ID); ?>">
                                                <?php
                                                if (count($lp_multi_rating_fields) > 0) {
                                                    foreach ($lp_multi_rating_fields as $k => $v) {
                                                        $field_key  =   $k + 1;
                                                        $field_key  =   'field' . $field_key;
                                                        $field_rating_val   =   '';
                                                        if (isset($post_rating_data[$field_key])) {
                                                            $field_rating_val   =   $post_rating_data[$field_key];
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
                                    <div class="lp-listing-stars">
                                        <div class="lp-rating-stars-outer">
                                            <span class="lp-star-box <?php if ($rating >= 1) {
                                                                            echo 'filled' . ' ' . $rating_num_clr;
                                                                        } ?>"><i class="fa fa-star" aria-hidden="true"></i></span>
                                            <span class="lp-star-box <?php if ($rating >= 2) {
                                                                            echo 'filled' . ' ' . $rating_num_clr;
                                                                        } ?>"><i class="fa fa-star" aria-hidden="true"></i></span>
                                            <span class="lp-star-box <?php if ($rating >= 3) {
                                                                            echo 'filled' . ' ' . $rating_num_clr;
                                                                        } ?>"><i class="fa fa-star" aria-hidden="true"></i></span>
                                            <span class="lp-star-box <?php if ($rating >= 4) {
                                                                            echo 'filled' . ' ' . $rating_num_clr;
                                                                        } ?>"><i class="fa fa-star" aria-hidden="true"></i></span>
                                            <span class="lp-star-box <?php if ($rating >= 5) {
                                                                            echo 'filled' . ' ' . $rating_num_clr;
                                                                        } ?>"><i class="fa fa-star" aria-hidden="true"></i></span>
                                        </div>
                                        <?php
                                        if ($rating != 0) :
                                        ?>
                                            <span class="lp-rating-num <?php echo esc_attr($rating_num_bg); ?>"><?php echo esc_attr($rating); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="lp-review-right-content">
                                <?php the_content(); ?>
                                <?php
                                if (!empty($gallery)) {
                                    $imagearray = explode(',', $gallery);
                                    $imagearray_count = count($imagearray);
                                    if ($imagearray_count > 0) {
                                        require_once(THEME_PATH . "/include/aq_resizer.php");
                                ?>
                                        <div class="lp-reivew-gallery">
                                            <div class="row">
                                                <div class="listing-review-slider" data-review-thumbs="<?php echo esc_attr($imagearray_count); ?>">
                                                    <?php
                                                    if (!empty($imagearray)) {
                                                        $img_url = '';
                                                        foreach ($imagearray as $image) {
                                                            $imgGalFull = wp_get_attachment_image_src($image, 'full');
                                                            if (!empty($imgGalFull) && isset($imgGalFull)) {
                                                                $imgGalThum  = aq_resize($imgGalFull[0], '150', '115', true, true, true);
                                                                if (isset($imgGalFull[0])) {
                                                                    $img_url = $imgGalFull[0];
                                                                } else {
                                                                    $img_url = '';
                                                                }
                                                                echo '<div class="col-md-3"><a href="' . $img_url . '" class="galImgFull" rel="prettyPhoto[gallery2]"><img src="' . $imgGalThum . '" alt="image"></a></div>';
                                                            }
                                                        }
                                                    }

                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                                <div class="lp-review-right-bottom">
                                    <span id="lp-review-text-align"><?php echo esc_html__('Was this review ...?', 'listingpro'); ?></span>
                                    <a href="#" data-restype="<?php echo esc_attr($interVal); ?>" data-reacted="<?php echo esc_attr($reacted_msg); ?>" data-id="<?php the_ID(); ?>" data-score="<?php echo esc_attr($interests); ?>" class="review-interesting review-reaction"><i class="fa fa-thumbs-o-up"></i><span class="react-msg"></span> <?php esc_html_e('Interesting', 'listingpro'); ?> <span class="react-count"><?php echo esc_attr($interests); ?></span></a>
                                    <a href="#" data-restype="<?php echo esc_attr($lolVal); ?>" data-reacted="<?php echo esc_attr($reacted_msg); ?>" data-id="<?php the_ID(); ?>" data-score="<?php echo esc_attr($Lols); ?>" class="review-lol review-reaction"><i class="fa fa-smile-o"></i> <span class="react-msg"></span> <span class="react-msg"></span><?php esc_html_e('LOL', 'listingpro'); ?> <span class="react-count"><?php echo esc_attr($Lols); ?></span></a>
                                    <a href="#" data-restype="<?php echo esc_attr($loveVal); ?>" data-reacted="<?php echo esc_attr($reacted_msg); ?>" data-id="<?php the_ID(); ?>" data-score="<?php echo esc_attr($loves); ?>" class="review-love review-reaction"><i class="fa fa-heart-o"></i><span class="react-msg"></span> <span class="react-msg"></span><?php esc_html_e('Love', 'listingpro'); ?> <span class="react-count"><?php echo esc_attr($loves); ?></span></a>
                                    <?php
                                    if ($showReport == true && is_user_logged_in()) :
                                    ?>
                                        <!-- new code 2.6.15 -->
                                        <a data-toggle="modal" data-target="#lp_report_listing" id="lp-report-this-review-popup" href="#" class="review-love" data-postid="<?php echo get_the_ID(); ?>" data-reportedby="<?php echo esc_attr($currentUserId); ?>" data-posttype="reviews"><i class="fa fa-flag" aria-hidden="true"></i> <?php esc_html_e('Report', 'listingpro'); ?></a>
                                        <!-- new code 2.6.15 -->
                                    <?php
                                    endif;
                                    ?>
                                </div>
                            </div>
                            <?php if (!empty($review_reply)) { ?>
                                <div class="lp-deatil-reply-review-area">
                                    <div class="owner-response">
                                        <h3><?php esc_html_e('Author Response', 'listingpro'); ?></h3>
                                        <?php
                                        if (!empty($review_reply_time)) { ?>
                                            <time><?php echo date_i18n("F j, Y h:i:s a", strtotime($review_reply_time)); ?></time>
                                        <?php } ?>
                                        <p><?php echo esc_attr($review_reply); ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
        <?php
                }
                echo '';
                wp_reset_postdata();
            } else {
            }
            //}
        }
    }
}
if (!function_exists('activity_reviews')) {
    function activity_reviews($review_id, $author_id)
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
            $lp_multi_rating_fields =   get_listing_multi_ratings_fields($review_id);
        }
        $currentUserId = get_current_user_id();

        $review_reply = '';
        $rating = '';
        $review_reply_time = '';
        if (class_exists('ListingproPlugin')) {
            $review_reply = listing_get_metabox_by_ID('review_reply', $review_id);
            $review_reply_time = listing_get_metabox_by_ID('review_reply_time', $review_id);
            $rating = listing_get_metabox_by_ID('rating', $review_id);
        }

        $rating = apply_filters('lp_rating_number_format', $rating);
        $rate = $rating;
        $gallery = get_post_meta($review_id, 'gallery_image_ids', true);
        $author_avatar_url = get_user_meta($author_id, "listingpro_author_img_url", true);
        $avatar = '';
        if (!empty($author_avatar_url)) {
            $avatar =  $author_avatar_url;
        } else {
            $avatar_url = listingpro_get_avatar_url($author_id, $size = '94');
            $avatar =  $avatar_url;
        }
        $user_reviews_count = count_user_posts($author_id, 'lp-reviews');
        $interests = '';
        $Lols = '';
        $loves = '';
        $interVal = esc_html__('Interesting', 'listingpro');
        $lolVal = esc_html__('Lol', 'listingpro');
        $loveVal = esc_html__('Love', 'listingpro');
        if (class_exists('ListingproPlugin')) {
            $interests = listing_get_metabox_by_ID('review_' . $interVal . '', $review_id);
            $Lols = listing_get_metabox_by_ID('review_' . $lolVal . '', $review_id);
            $loves = listing_get_metabox_by_ID('review_' . $loveVal . '', $review_id);
        }
        if (empty($interests)) {
            $interests = 0;
        }
        if (empty($Lols)) {
            $Lols = 0;
        }
        if (empty($loves)) {
            $loves = 0;
        }
        $reacted_msg    =   esc_html__('You already reacted', 'listingpro');
        $rating_num_bg  =   '';
        $rating_num_clr  =   '';
        if ($rating < 3) {
            $rating_num_bg  =   'num-level1';
            $rating_num_clr  =   'level1';
        }
        if ($rating < 4) {
            $rating_num_bg  =   'num-level2';
            $rating_num_clr  =   'level2';
        }
        if ($rating < 5) {
            $rating_num_bg  =   'num-level3';
            $rating_num_clr  =   'level3';
        }
        if ($rating >= 5) {
            $rating_num_bg  =   'num-level4';
            $rating_num_clr  =   'level4';
        }
        ?>
        <div class="lp-listing-review">
            <div class="lp-review-left">
                <div class="lp-review-thumb">
                    <img src="<?php echo esc_attr($avatar); ?>" alt="image">
                </div>
                <?php
                $authorOBJ = get_user_by('ID', $author_id);
                $author_display_name = $authorOBJ->display_name;
                ?>
                <span class="lp-review-name"><a href="<?php echo get_author_posts_url($author_id); ?>"><?php echo esc_attr($author_display_name); ?></a></span>
                <span class="lp-review-count"><i class="fa fa-star" aria-hidden="true"></i> <?php echo esc_attr($user_reviews_count); ?> <?php esc_html_e('Reviews', 'listingpro'); ?></span>
            </div>
            <div class="lp-review-right">
                <div class="lp-review-right-top">
                    <strong><?php echo get_the_title($review_id); ?></strong>
                    <time>
                        <?php
                        echo human_time_diff(get_the_time('U', $review_id)) . ' ' . esc_html__('ago', 'listingpro');
                        //echo get_the_time('F j, Y g:i a', $review_id);
                        ?>
                    </time>
                    <div class="lp-review-stars">
                        <?php
                        if ($lp_multi_rating_state == 1 && !empty($lp_multi_rating_state)) {
                            echo '<a href="#" data-rate-box="multi-box-' . $review_id . '" class="open-multi-rate-box"><i class="fa fa-chevron-down" aria-hidden="true"></i> ' . esc_html__('View All', 'listingpro') . '</a>';
                            $post_rating_data   =   get_post_meta($review_id, 'lp_listingpro_options', true);
                        ?>
                            <div class="lp-multi-star-wrap" id="multi-box-<?php echo esc_attr($review_id); ?>">
                                <?php
                                foreach ($lp_multi_rating_fields as $k => $v) {
                                    $field_rating_val   =   '';
                                    if (isset($post_rating_data[$k])) {
                                        $field_rating_val   =   $post_rating_data[$k];
                                    }
                                ?>
                                    <div class="lp-multi-star-field  rating-with-colors <?php echo review_rating_color_class($field_rating_val); ?>">
                                        <?php
                                        $label_start = null;
                                        $label_end = null;
                                        $label = null;
                                        if (isset($v['label'])) {
                                            $label = $v['label'];
                                        }
                                        if (isset($v['label_start'])) {
                                            $label_start = $v['label_start'];
                                        }
                                        if (isset($v['label_end'])) {
                                            $label_end = $v['label_end'];
                                        }
                                        ?>
                                        <label><?php echo esc_html__($label, 'listingpro');  ?></label>
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
                                        <span class="lp-multi-star-label-start"><?php echo esc_html__($label_start, 'listingpro'); ?></span>
                                        <span class="lp-multi-star-label-end"><?php echo esc_html__($label_end, 'listingpro'); ?></span>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="lp-listing-stars">
                            <div class="lp-rating-stars-outer">
                                <span class="lp-star-box <?php if ($rating >= 1) {
                                                                echo 'filled' . ' ' . $rating_num_clr;
                                                            } ?>"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box <?php if ($rating >= 2) {
                                                                echo 'filled' . ' ' . $rating_num_clr;
                                                            } ?>"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box <?php if ($rating >= 3) {
                                                                echo 'filled' . ' ' . $rating_num_clr;
                                                            } ?>"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box <?php if ($rating >= 4) {
                                                                echo 'filled' . ' ' . $rating_num_clr;
                                                            } ?>"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <span class="lp-star-box <?php if ($rating >= 5) {
                                                                echo 'filled' . ' ' . $rating_num_clr;
                                                            } ?>"><i class="fa fa-star" aria-hidden="true"></i></span>
                                <?php
                                if ($rating != 0) :
                                ?>
                            </div>
                            <span class="lp-rating-num <?php echo esc_attr($rating_num_bg); ?>"><?php echo esc_attr($rating); ?> </span>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="lp-review-right-content">
                    <?php
                    $content_post = get_post($review_id);
                    $content = $content_post->post_content;
                    echo '<p>' . $content . '</p>';
                    ?>
                    <p></p>
                    <?php
                    if (!empty($gallery)) {
                        $imagearray = explode(',', $gallery);
                        $imagearray_count = count($imagearray);
                        if ($imagearray_count > 0) {
                            require_once(THEME_PATH . "/include/aq_resizer.php");
                    ?>
                            <div class="lp-reivew-gallery">
                                <div class="row">
                                    <div class="listing-review-slider" data-review-thumbs="<?php echo esc_attr($imagearray_count); ?>">
                                        <?php
                                        if (!empty($imagearray)) {
                                            $img_url = '';

                                            foreach ($imagearray as $image) {
                                                $imgGalFull = wp_get_attachment_image_src($image, 'full');
                                                if (!empty($imgGalFull) && isset($imgGalFull)) {
                                                    $imgGalThum  = aq_resize($imgGalFull[0], '150', '115', true, true, true);
                                                    if (isset($imgGalFull[0])) {
                                                        $img_url = $imgGalFull[0];
                                                    } else {
                                                        $img_url = '';
                                                    }
                                                    echo '<div class="col-md-3"><a href="' . $img_url . '" class="galImgFull" rel="prettyPhoto[gallery2]"><img src="' . $imgGalThum . '" alt="image"></a></div>';
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                    <div class="lp-review-right-bottom">
                        <?php echo esc_html__('Was this review ...?', 'listingpro'); ?>
                        <a href="#" data-restype="<?php echo esc_attr($interVal); ?>" data-reacted="<?php echo esc_attr($reacted_msg); ?>" data-id="<?php the_ID(); ?>" data-score="<?php echo esc_attr($interests); ?>" class="review-interesting review-reaction"><i class="fa fa-thumbs-o-up"></i><span class="react-msg"></span> <?php esc_html_e('Interesting', 'listingpro'); ?> <span class="react-count"><?php echo esc_attr($interests); ?></span></a>
                        <a href="#" data-restype="<?php echo esc_attr($lolVal); ?>" data-reacted="<?php echo esc_attr($reacted_msg); ?>" data-id="<?php the_ID(); ?>" data-score="<?php echo esc_attr($Lols); ?>" class="review-lol review-reaction"><i class="fa fa-smile-o"></i> <span class="react-msg"></span> <span class="react-msg"></span><?php esc_html_e('LOL', 'listingpro'); ?> <span class="react-count"><?php echo esc_attr($Lols); ?></span></a>
                        <a href="#" data-restype="<?php echo esc_attr($loveVal); ?>" data-reacted="<?php echo esc_attr($reacted_msg); ?>" data-id="<?php the_ID(); ?>" data-score="<?php echo esc_attr($loves); ?>" class="review-love review-reaction"><i class="fa fa-heart-o"></i><span class="react-msg"></span> <span class="react-msg"></span><?php esc_html_e('Love', 'listingpro'); ?> <span class="react-count"><?php echo esc_attr($loves); ?></span></a>
                        <?php
                        if ($showReport == true && is_user_logged_in()) :
                        ?>
                            <a id="lp-report-this-review" href="#" class="review-love" data-postid="<?php echo get_the_ID(); ?>" data-reportedby="<?php echo esc_attr($currentUserId); ?>" data-posttype="reviews"><i class="fa fa-flag" aria-hidden="true"></i> <?php esc_html_e('Report', 'listingpro'); ?></a>
                        <?php
                        endif;
                        ?>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php
    }
}
/* ============== ListingPro Price Range Symbol ============ */
if (!function_exists('listing_price_range_symbol')) {
    function listing_price_range_symbol($postid)
    {
        $priceRange = listing_get_metabox_by_ID('price_status', $postid);
        $listingpTo = listing_get_metabox('list_price_to');
        $listingprice = listing_get_metabox_by_ID('list_price', $postid);
        $return =   array();
        $dollars = '';
        $tip = '';
        if (($priceRange != 'notsay' && !empty($priceRange)) || !empty($listingpTo) || !empty($listingprice)) {
            if ($priceRange == 'notsay') {
                $dollars = '';
                $tip = '';
            } elseif ($priceRange == 'inexpensive') {
                $dollars = '1';
                $tip = esc_html__('Inexpensive', 'listingpro');
            } elseif ($priceRange == 'moderate') {
                $dollars = '2';
                $tip = esc_html__('Moderate', 'listingpro');
            } elseif ($priceRange == 'pricey') {
                $dollars = '3';
                $tip = esc_html__('Pricey', 'listingpro');
            } elseif ($priceRange == 'ultra_high_end') {
                $dollars = '4';
                $tip = esc_html__('Ultra High End', 'listingpro');
            }
            global $listingpro_options;
            $lp_priceSymbol  = isset($listingpro_options['listing_pricerange_symbol']) ? $listingpro_options['listing_pricerange_symbol'] : '';
            $return['dollars']  =   $dollars;
            $return['symbol']   =   $lp_priceSymbol;
            $return['tip']      =   $tip;
            return $return;
        }
    }
}
/* ============== ListingPro Price Range ============ */
if (!function_exists('listing_price_range')) {
    function listing_price_range($postid)
    {
        $listingpTo = listing_get_metabox('list_price_to');
        $listingprice = listing_get_metabox_by_ID('list_price', $postid);
        if (is_singular('listing')) {
            if (!empty($listingpTo) || !empty($listingprice)) {
                echo '<span class="lp-listing-price-range">';
                if (!empty($listingprice)) {
                    echo esc_html($listingprice);
                }
                if (!empty($listingpTo)) {
                    echo ' - ';
                    echo esc_html($listingpTo);
                }
                echo '</span>';
            }
        } else {
            if (!empty($listingpTo) || !empty($listingprice)) {
                $currency  =   listing_price_range_symbol(get_the_ID());
                echo '<div class="lp-listing-price-range">';
                echo '<span class="lp-listing-price-range-currency">' . $currency['symbol'] . '</span>';
                echo '<span class="lp-listing-price-range-text">';
                if (!empty($listingprice)) {
                    echo esc_html($listingprice);
                }
                if (!empty($listingpTo)) {
                    echo ' - ';
                    echo esc_html($listingpTo);
                }
                echo '</span>';
                echo '</div>';
            }
        }
    }
}
/* ============== ListingPro sidebar extra fields ============ */
if (!function_exists('listing_all_extra_fields_v2_right')) {
    function listing_all_extra_fields_v2_right($postid)
    {
        $output = '';
        $count = 1;
        $metaboxes = get_post_meta($postid, 'lp_' . strtolower(THEMENAME) . '_options_fields', true);
        if (!empty($metaboxes)) {
            unset($metaboxes['lp_feature']);
            $bottom35   =   '';
            if (!empty($metaboxes)) {
                $numberOF = count($metaboxes);
                if ($numberOF > 5) {
                    $bottom35   =   'bottom35';
                }
                $output = null;
                $output .= '<div class="lp-listing-additional-details lp-widget-inner-wrap ' . $bottom35 . '">';
                $output .= '  <h4>' . esc_html__('Additional Details', 'listingpro') . '</h4>';
                $output .= '<ul>';
                foreach ($metaboxes as $slug => $value) {
                    $queried_post = get_page_by_path($slug, OBJECT, 'form-fields');
                    if (!empty($queried_post)) {
                        $dieldsID = $queried_post->ID;
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        //new code 2.6.15
                        if (!empty($value) && $value == 'Yes' || $value == 'yes') {
                            $value  =   '<i class="fa fa-check" aria-hidden="true"></i>';
                            $value  =   esc_html__('Yes', 'listingpro');
                        }

                        if (!empty($value) && $value == 'No' || $value == 'no') {
                            $value  =   '<i class="fa fa-close" aria-hidden="true"></i>';
                            $value  =   esc_html__('No', 'listingpro');
                        }
                        //end new code 2.6.15

                        if (!empty($value)) {
                            $output .= '<li><label>' . get_the_title($dieldsID) . '</label><span>' . $value . '</span></li>';
                        }
                    }
                    if ($count == 5) {
                        break;
                    }
                    $count++;
                }
                $output .= '</ul>';
                if ($numberOF > 5) {
                    $count = 1;
                    $output .=  '<ul class="additional-detail-hidden">';
                    foreach ($metaboxes as $slug => $value) {
                        if ($count > 5) {
                            $queried_post = get_page_by_path($slug, OBJECT, 'form-fields');
                            if (!empty($queried_post)) {
                                $dieldsID = $queried_post->ID;
                                if (is_array($value)) {
                                    $value = implode(', ', $value);
                                }

                                //new code 2.6.15
                                if (!empty($value) && $value == 'Yes' || $value == 'yes') {
                                    $value  =   '<i class="fa fa-check" aria-hidden="true"></i>';
                                    $value  =   esc_html__('Yes', 'listingpro');
                                }

                                if (!empty($value) && $value == 'No' || $value == 'no') {
                                    $value  =   '<i class="fa fa-close" aria-hidden="true"></i>';
                                    $value  =   esc_html__('No', 'listingpro');
                                }
                                //end new code 2.6.15

                                if (!empty($value)) {
                                    $output .= '<li><label>' . get_the_title($dieldsID) . '</label><span>' . $value . '</span></li>';
                                }
                            }
                        }
                        $count++;
                    }
                    $output .=  '</ul>';
                    $output .=  '<button data-contract="' . esc_html__('Contract', 'listingpro') . '" data-expand="' . esc_html__('Expand', 'listingpro') . '" class="toggle-additional-details"><i class="fa fa-plus" aria-hidden="true"></i> ' . esc_html__('Expand', 'listingpro') . '</button>';
                }
                $output .=  '</div>';
            }
            return $output;
        }
    }
}
if (!function_exists('listing_all_extra_fields_v2')) {
    function listing_all_extra_fields_v2($postid)
    {
        $output = '';
        $count = 0;
        $metaboxes = get_post_meta($postid, 'lp_' . strtolower(THEMENAME) . '_options_fields', true);
        if (!empty($metaboxes)) {
            unset($metaboxes['lp_feature']);
            if (!empty($metaboxes)) {
                $numberOF = count($metaboxes);
                $output = null;
                $output .=  '<h4 class="lp-detail-section-title">' . esc_html__('Additional Details', 'listingpro') . '</h4>';
                $output .= '<div class="lp-listing-specs">';
                $output .= '<ul>';
                foreach ($metaboxes as $slug => $value) {
                    $queried_post = get_page_by_path($slug, OBJECT, 'form-fields');
                    if (!empty($queried_post)) {
                        $dieldsID = $queried_post->ID;
                        if (is_array($value)) {
                            $value = implode(', ', $value);
                        }

                        //new update 2.6.15
                        if (!empty($value) && $value == 'Yes' || $value == 'yes') {
                            $value  =   esc_html__('Yes', 'listingpro');
                        }

                        if (!empty($value) && $value == 'No' || $value == 'no') {
                            $value  =   esc_html__('No', 'listingpro');
                        }

                        //end new update 2.6.15

                        if (!empty($value)) {
                            $output .= '<li><label>' . get_the_title($dieldsID) . '</label><span>' . $value . '</span></li>';
                        }
                    }
                }
                $output .= '</ul>';
                $output .= '<div class="clearfix"></div></div>';
                // closing
            }
            return $output;
        }
    }
}
/* ============== Listingpro Sharing ============ */
if (!function_exists('listingpro_sharing_v2')) {
    function listingpro_sharing_v2()
    {
    ?>
        <a href="" class="lp-single-sharing"><i class="fa fa-share-alt" aria-hidden="true"></i> <?php echo esc_html__('Share', 'listingpro'); ?></a>
        <div class="md-overlay hide"></div>
        <div class="social-icons post-socials smenu">
            <div>
                <a href="<?php echo listingpro_social_sharing_buttons('facebook'); ?>" target="_blank">
                    <!-- Facebook icon by Icons8 -->
                    <i class="fa fa-facebook"></i>
                </a>
            </div>
            <div>
                <a href="<?php echo listingpro_social_sharing_buttons('twitter'); ?>" target="_blank">
                    <!-- twitter icon by Icons8 -->
                    <i class="fa-brands fa-square-x-twitter"></i>
                </a>
            </div>
            <div>
                <a href="<?php echo listingpro_social_sharing_buttons('linkedin'); ?>" target="_blank">
                    <!-- linkedin icon by Icons8 -->
                    <i class="fa fa-linkedin"></i>
                </a>
            </div>
            <div>
                <a href="<?php echo listingpro_social_sharing_buttons('pinterest'); ?>" target="_blank">
                    <!-- pinterest icon by Icons8 -->
                    <i class="fa fa-pinterest"></i>
                </a>
            </div>
            <div>
                <a href="<?php echo listingpro_social_sharing_buttons('reddit'); ?>" target="_blank">
                    <!-- reddit icon by Icons8 -->
                    <i class="fa fa-reddit"></i>
                </a>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
    }
}
/* ============== is favourite or not only ============ */
if (!function_exists('listingpro_is_favourite_v2')) {
    function listingpro_is_favourite_v2($postid)
    {
        if (is_user_logged_in()) {
            $uid = get_current_user_id();
            $favposts = get_user_meta($uid, 'lp_saved_user_posts', true);
            if (!is_array($favposts)) {
                $favposts   =   (array) $favposts;
            }
        } else {
            $favposts = (isset($_COOKIE['newco'])) ? explode(',', (string)$_COOKIE['newco']) : array();
            $favposts = array_map('absint', $favposts); // Clean cookie input, it's user input!
        }
        $return = 'no';
        if (in_array($postid, $favposts)) {
            $return = 'yes';
        }
        return $return;
    }
}
/* ============== ListingPro Add to favorite ============ */
add_action('wp_ajax_listingpro_add_favorite_v2',        'listingpro_add_favorite_v2');
add_action('wp_ajax_nopriv_listingpro_add_favorite_v2', 'listingpro_add_favorite_v2');
if (!function_exists('listingpro_add_favorite_v2')) {
    function listingpro_add_favorite_v2()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        // Load current favourite posts from cookie
        $favposts = (isset($_COOKIE['newco'])) ? explode(',', (string) $_COOKIE['newco']) : array();
        $favposts = array_map('absint', $favposts); // Clean cookie input, it's user input!
        // Add (or remove) favourite post IDs
        $favposts[] = sanitize_text_field($_POST['post-id']);
        $type = sanitize_text_field($_POST['type']);
        //$path = parse_url(get_option('siteurl'), PHP_URL_PATH);
        //$host = parse_url(get_option('siteurl'), PHP_URL_HOST);
        // Update cookie with new favourite posts
        if (is_user_logged_in()) {
            $uid = get_current_user_id();
            $savedListing = get_user_meta($uid, 'lp_saved_user_posts', true);
            if (!empty($savedListing)) {
                $savedListing = get_user_meta($uid, 'lp_saved_user_posts', true);
            } else {
                $savedListing = array();
            }
            $savedListing[] = sanitize_text_field($_POST['post-id']);
            update_user_meta($uid, 'lp_saved_user_posts', array_unique($savedListing));
        } else {
            $time_to_live = 3600 * 24 * 30; // 30 days
            setcookie('newco', implode(',', array_unique($favposts)), time() + $time_to_live, "/");
        }
        $done = json_encode(array("type" => $type, "active" => 'yes', "id" => $favposts, 'text' => esc_html__('Saved', 'listingpro')));
        die($done);
    }
}
/* ============== ListingPro Remove from favorite ============ */
add_action('wp_ajax_listingpro_remove_favorite_v2',        'listingpro_remove_favorite_v2');
add_action('wp_ajax_nopriv_listingpro_remove_favorite_v2', 'listingpro_remove_favorite_v2');
if (!function_exists('listingpro_remove_favorite_v2')) {
    function listingpro_remove_favorite_v2()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field(sanitize_text_field($_POST['lpNonce'])), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        // Load current favourite posts from cookie
        $favposts = (isset($_COOKIE['newco'])) ? explode(',', (string) $_COOKIE['newco']) : array();
        $favposts = array_map('absint', $favposts); // Clean cookie input, it's user input!
        // Add (or remove) favourite post IDs
        $favpostsd = sanitize_text_field($_POST['post-id']);
        $type = sanitize_text_field($_POST['type']);
        if (is_user_logged_in()) {
            $uid = get_current_user_id();
            $savedinMeta = get_user_meta($uid, 'lp_saved_user_posts', true);
            if (!empty($savedinMeta)) {
                foreach ($savedinMeta as $index => $value) {
                    if ($value == $favpostsd) {
                        unset($savedinMeta[$index]);
                    }
                }
            }
            update_user_meta($uid, 'lp_saved_user_posts', $savedinMeta);
        } else {
            foreach ($favposts as $index => $value) {
                if ($value == $favpostsd) {
                    unset($favposts[$index]);
                }
            }
            $time_to_live = 3600 * 24 * 30; // 30 days
            setcookie('newco', implode(',', array_unique($favposts)), time() + $time_to_live, "/");
        }
        $done = json_encode(array("type" => $type, "remove" => 'yes', "id" => $favposts, 'text' => esc_html__('Save', 'listingpro')));
        die($done);
    }
}
/* ============== ListingPro add announcement ============ */
add_action('wp_ajax_add_announcements_cb', 'add_announcements_cb');
if (!function_exists('add_announcements_cb')) {
    function add_announcements_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $return                     =   array();
        $lp_listing_announcements   =   array();
        $user_id    =   get_current_user_id();
        $user_idd   =   sanitize_text_field($_POST['user_id']);
        $announcement_data['annMsg'] = isset($_POST['annMsg']) ? sanitize_text_field($_POST['annMsg']) : null;
        $announcement_data['annBT'] = isset($_POST['annBT']) ? sanitize_text_field($_POST['annBT']) : null;
        $announcement_data['annBL'] = isset($_POST['annBL']) ? sanitize_text_field($_POST['annBL']) : null;
        $announcement_data['annLI'] = isset($_POST['annLI']) ? sanitize_text_field($_POST['annLI']) : null;
        $announcement_data['annSt'] = isset($_POST['annSt']) ? sanitize_text_field($_POST['annSt']) : null;
        $announcement_data['annTI'] = isset($_POST['annTI']) ? sanitize_text_field($_POST['annTI']) : null;
        $announcement_data['annType'] = isset($_POST['annType']) ? sanitize_text_field($_POST['annType']) : null;
        $announcement_data['annIC'] = isset($_POST['annIC']) ? sanitize_text_field($_POST['annIC']) : null;
        $announcement_data['annStatus'] = 1;
        $annUP = isset($_POST['annUP']) ? sanitize_text_field($_POST['annUP']) : null;
        $annID = isset($_POST['annID']) ? sanitize_text_field($_POST['annID']) : null;
        $annLI = isset($_POST['annLI']) ? sanitize_text_field($_POST['annLI']) : null;
        if ($user_id != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        if ($annID != '' && $annUP == 'yes') {
            $annID_arr  =   explode('-', $annID);
            $ann_index  =   $annID_arr[1];
            $existing_data  =   get_post_meta($annLI, 'lp_listing_announcements', true);
            $existing_data[$ann_index]['annMsg'] = isset($_POST['annMsg']) ? sanitize_text_field($_POST['annMsg']) : null;
            $existing_data[$ann_index]['annBT'] = isset($_POST['annBT']) ? sanitize_text_field($_POST['annBT']) : null;
            $existing_data[$ann_index]['annBL'] = isset($_POST['annBL']) ? sanitize_text_field($_POST['annBL']) : null;
            $existing_data[$ann_index]['annSt'] = isset($_POST['annSt']) ? sanitize_text_field($_POST['annSt']) : null;
            $existing_data[$ann_index]['annTI'] = isset($_POST['annTI']) ? sanitize_text_field($_POST['annTI']) : null;
            $existing_data[$ann_index]['annType'] = isset($_POST['annType']) ? sanitize_text_field($_POST['annType']) : null;
            $existing_data[$ann_index]['annIC'] = isset($_POST['annIC']) ? sanitize_text_field($_POST['annIC']) : null;
            update_post_meta($annLI, 'lp_listing_announcements', $existing_data);
            $return['status'] = 'success';
            $return['msg'] = esc_html__('Announcement updated', 'listingpro');
            die(json_encode($return));
        } elseif ($annID != '' && $annUP == 'on-off') {
            $annID_arr  =   explode('-', $annID);
            $ann_index  =   $annID_arr[1];
            $status =   sanitize_text_field($_POST['status']);
            if ($status ==  'active') {
                $status =   0;
            } elseif ($status == 'inactive') {
                $status =   1;
            }
            $existing_data  =   get_post_meta($annID_arr[0], 'lp_listing_announcements', true);
            $target_data    =   $existing_data[$ann_index];
            $existing_data[$ann_index]['annStatus']    =   $status;
            update_post_meta($annID_arr[0], 'lp_listing_announcements', $existing_data);
            $return['status'] = $status;
            die(json_encode($return));
        } else {
            $checkStatus = lp_validate_listing_action($annLI, 'announcment');
            if (empty($checkStatus)) {
                $return['status']   =   'error';
                $return['msg']      =   esc_html__('Announcements are not allowed with this listing', 'listingpro');
                die(json_encode($return));
            }
            $data_arr[] =   $announcement_data;
            $existing_data  =   get_post_meta($annLI, 'lp_listing_announcements', true);
            if (is_array($existing_data) && !empty($existing_data)) {
                $new_data   =   array_merge($existing_data, $data_arr);
                update_post_meta($annLI, 'lp_listing_announcements', $new_data);
                $return['status'] = 'success';
                $return['msg'] = esc_html__('Announcement added successfully', 'listingpro');
                die(json_encode($return));
            } else {
                update_post_meta($annLI, 'lp_listing_announcements', $data_arr);
                $return['status'] = 'success';
                $return['msg'] = esc_html__('Announcement added successfully', 'listingpro');
                die(json_encode($return));
            }
        }
    }
}
/* ============== ListingPro add Offers ============ */
add_action('wp_ajax_add_offer_cb', 'add_offer_cb');
if (!function_exists('add_offer_cb')) {
    function add_offer_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $user_id    =   get_current_user_id();
        $user_idd   =   sanitize_text_field($_POST['user_id']);
        $listing_offer_data['offerTitle']     =   sanitize_text_field($_POST['offerTitle']);
        $listing_offer_data['offerDes']     =   sanitize_text_field($_POST['offerDes']);
        $listing_offer_data['offerExp']      =   strtotime(sanitize_text_field($_POST['offerExp']));
        $listing_offer_data['offerBT']      =   sanitize_text_field($_POST['offerBT']);
        $listing_offer_data['offerBL']      =   sanitize_text_field($_POST['offerBL']);
        $listing_offer_data['offerLI']     =   sanitize_text_field($_POST['offerLI']);
        $listing_offer_data['offerImg']     =   sanitize_text_field($_POST['offerImg']);
        $offerID      =   sanitize_text_field($_POST['offerID']);
        $offerUP      =   sanitize_text_field($_POST['offerUP']);
        if ($user_id != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        $data_arr[] =   $listing_offer_data;
        if ($offerUP == 'yes' && $offerID != '') {
            $offerID_arr        =   explode('-', $offerID);
            $existing_offers    =   get_post_meta($offerID_arr[1], 'lp_listing_offers', true);
            $listing_offer_data['offerImg']  =   $existing_offers[$offerID_arr[0]]['offerImg'];
            $existing_offers[$offerID_arr[0]]   =   $listing_offer_data;
            update_post_meta($offerID_arr[1], 'lp_listing_offers', $existing_offers);
            $return['status']   =   'success';
            $return['msg']      =   esc_html__('offer updated', 'listingpro');
            die(json_encode($return));
        } else {
            $existing_offers                =   get_post_meta(sanitize_text_field($_POST['offerLI']), 'lp_listing_offers', true);
            if ($existing_offers != '' && $existing_offers != false) {
                $new_data   =   array_merge($existing_offers, $data_arr);
                update_post_meta(sanitize_text_field($_POST['offerLI']), 'lp_listing_offers', $new_data);
                $return['status']   =   'success';
                $return['msg']      =   esc_html__('offer added', 'listingpro');
                die(json_encode($return));
            } else {
                update_post_meta(sanitize_text_field($_POST['offerLI']), 'lp_listing_offers', $data_arr);
                $return['status']   =   'success';
                $return['msg']      =   esc_html__('offer added', 'listingpro');
                die(json_encode($return));
            }
        }
        $return['status']   =   'error';
        $return['msg']      =   esc_html__('Something went wrong', 'listingpro');
        die(json_encode($return));
    }
}
add_action('wp_ajax_add_menu_type_cb', 'add_menu_type_cb');
if (!function_exists('add_menu_type_cb')) {
    function add_menu_type_cb()
    {
        $user_id        =   get_current_user_id();
        $user_idd       =   sanitize_text_field($_POST['user_id']);
        $type_data['type']    =   sanitize_text_field($_POST['type']);
        $data_arr[] =   $type_data;
        if ($user_id != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        $existing_data  =   get_user_meta($user_id, 'user_menu_types', true);
        if (is_array($existing_data) && !empty($existing_data)) {
            $new_data   =   array_merge($existing_data, $data_arr);
            update_user_meta($user_id, 'user_menu_types', $new_data);
            $var_key    =   array_search(sanitize_text_field($_POST['type']), $new_data);
            $return['status']   =   'success';
            $return['data_index']      =   $var_key;
            die(json_encode($return));
        } else {
            update_user_meta($user_id, 'user_menu_types', $data_arr);
            $var_key    =   array_search(sanitize_text_field($_POST['type']), $data_arr);
            $return['status']   =   'success';
            $return['data_index']      =   $var_key;
            die(json_encode($return));
        }
    }
}
add_action('wp_ajax_add_menu_service_cb', 'add_menu_service_cb');
if (!function_exists('add_menu_service_cb')) {
    function add_menu_service_cb()
    {
        $user_id        =   get_current_user_id();
        $user_idd       =   sanitize_text_field($_POST['user_id']);
        $serviceVal     =   sanitize_text_field($_POST['serviceVal']);
        $serviceUrl     =   sanitize_text_field($_POST['serviceUrl']);
        $data_arr   =   array();
        if ($user_id != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        $existing_data  =   get_user_meta($user_id, 'order_services', true);
        if (is_array($existing_data) && !empty($existing_data)) {
            $existing_data[$serviceUrl]   =   $serviceVal;
            update_user_meta($user_id, 'order_services', $existing_data);
            $var_key    =   array_search($serviceVal, $existing_data);
            $embed_markup   =   '<li><a href="' . $var_key . '" target="_blank">' . $serviceVal . '</a> <span class="del-order-service" data-uid="' . $user_id . '" data-target="' . $var_key . '"><i class="fa fa-trash"></i></span></li>';
            $return['status']   =   'success';
            $return['msg']      =   $embed_markup;
            die(json_encode($return));
        } else {
            $data_arr[$serviceUrl]   =   $serviceVal;
            update_user_meta($user_id, 'order_services', $data_arr);
            $var_key    =   array_search($serviceVal, $data_arr);
            $embed_markup   =   '<li><a href="' . $var_key . '" target="_blank">' . $serviceVal . '</a> <span class="del-order-service" data-uid="' . $user_id . '" data-target="' . $var_key . '"><i class="fa fa-trash"></i></span></li>';
            $return['status']   =   'success';
            $return['msg']      =   $embed_markup;
            die(json_encode($return));
        }
    }
}
add_action('wp_ajax_del_ordering_service', 'del_ordering_service');
if (!function_exists('del_ordering_service')) {
    function del_ordering_service()
    {
        $user_id        =   get_current_user_id();
        $user_idd       =   sanitize_text_field($_POST['uid']);
        $target_key     =   sanitize_text_field($_POST['target']);
        if ($user_id != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        $existing_data  =   get_user_meta($user_id, 'order_services', true);
        if (is_array($existing_data) && !empty($existing_data)) {
            unset($existing_data[$target_key]);
            update_user_meta($user_id, 'order_services', $existing_data);
            $return['status']   =   'success';
            die(json_encode($return));
        }
    }
}
add_action('wp_ajax_add_menu_group_cb', 'add_menu_group_cb');
if (!function_exists('add_menu_group_cb')) {
    function add_menu_group_cb()
    {
        $user_id        =   get_current_user_id();
        $user_idd       =   sanitize_text_field($_POST['user_id']);
        $type_data['group']    =   sanitize_text_field($_POST['group']);
        $data_arr[] =   $type_data;
        if ($user_id != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        $existing_data  =   get_user_meta($user_id, 'user_menu_groups', true);
        if (is_array($existing_data) && !empty($existing_data)) {
            $new_data   =   array_merge($existing_data, $data_arr);
            update_user_meta($user_id, 'user_menu_groups', $new_data);
            $var_key    =   array_search(sanitize_text_field($_POST['group']), $new_data);
            $return['status']   =   'success';
            $return['data_index']      =   $var_key;
            die(json_encode($return));
        } else {
            update_user_meta($user_id, 'user_menu_groups', $data_arr);
            $var_key = '';
            if (isset($_POST['type'])) {
                $var_key = array_search(sanitize_text_field($_POST['type']), $data_arr);
            }
            $return['status']   =   'success';
            $return['data_index']      =   $var_key;
            die(json_encode($return));
        }
    }
}
add_action('wp_ajax_add_menu_cb', 'add_menu_cb');
if (!function_exists('add_menu_cb')) {
    function add_menu_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $user_id        =   get_current_user_id();
        $user_idd       =   sanitize_text_field($_POST['user_id']);
        $data_arr       =   array();
        $menu_type_arr  =   array();
        if (isset($_POST['menuUp']) && $_POST['menuUp'] == 'yes') {
            $mTitle = isset($_POST['mTitle']) ? esc_html($_POST['mTitle']) : null;
            $mDetail = isset($_POST['mDetail']) ? htmlentities($_POST['mDetail']) : null;
            $mOldPrice = isset($_POST['mOldPrice']) ? sanitize_text_field($_POST['mOldPrice']) : null;
            $mNewPrice = isset($_POST['mNewPrice']) ? sanitize_text_field($_POST['mNewPrice']) : null;
            $mQuoteT = isset($_POST['mQuoteT']) ? sanitize_text_field($_POST['mQuoteT']) : null;
            $mQuoteL = isset($_POST['mQuoteL']) ? esc_url($_POST['mQuoteL']) : null;
            $mLink = isset($_POST['mLink']) && $_POST['mLink'] !== '' ? esc_url_raw(wp_unslash($_POST['mLink'])) : null;
            $menuID = isset($_POST['menuID']) ? sanitize_text_field($_POST['menuID']) : null;
            $LID = isset($_POST['LID']) ? sanitize_text_field($_POST['LID']) : null;
            $mImage = isset($_POST['mImage']) ? esc_url($_POST['mImage']) : null;
            $mType = isset($_POST['mType']) ? sanitize_text_field($_POST['mType']) : null;
            $mGroup = isset($_POST['mGroup']) ? sanitize_text_field($_POST['mGroup']) : null;
            $orderU = isset($_POST['orderU']) ? sanitize_text_field($_POST['orderU']) : null;
            $orderP = isset($_POST['orderP']) ? sanitize_text_field($_POST['orderP']) : null;
            $popularItem = isset($_POST['popularItem']) ? sanitize_text_field($_POST['popularItem']) : null;
            $spiceLVL = isset($_POST['spiceLVL']) ? sanitize_text_field($_POST['spiceLVL']) : null;

            $menuID_arr     =   explode('_', $menuID);
            $menu_type      =   str_replace('-', ' ', $menuID_arr[0]);
            $menu_group     =   str_replace('-', ' ', $menuID_arr[1]);
            $menu_indx      =   $menuID_arr[2];
        } else {
            $menu_data['mTitle'] = isset($_POST['mTitle']) ? sanitize_text_field($_POST['mTitle']) : null;
            $menu_data['mDetail'] = isset($_POST['mDetail']) ? htmlentities($_POST['mDetail']) : null;
            $menu_data['mOldPrice'] = isset($_POST['mOldPrice']) ? sanitize_text_field($_POST['mOldPrice']) : null;
            $menu_data['mNewPrice'] = isset($_POST['mNewPrice']) ? sanitize_text_field($_POST['mNewPrice']) : null;
            $menu_data['mQuoteL'] = isset($_POST['mQuoteL']) ? esc_url($_POST['mQuoteL']) : null;
            $menu_data['mQuoteT'] = isset($_POST['mQuoteT']) ? sanitize_text_field($_POST['mQuoteT']) : null;
            $menu_data['mListing'] = isset($_POST['mListing']) ? sanitize_text_field($_POST['mListing']) : null;
            $menu_data['mLink'] = isset($_POST['mLink']) && $_POST['mLink'] !== '' ? esc_url_raw(wp_unslash($_POST['mLink'])) : null;
            $menu_data['mImage'] = isset($_POST['mImage']) ? esc_url($_POST['mImage']) : null;
            $menu_data['mType'] = isset($_POST['mType']) ? sanitize_text_field($_POST['mType']) : null;
            $menu_data['mGroup'] = isset($_POST['mGroup']) ? sanitize_text_field($_POST['mGroup']) : null;
            $menu_data['orderU'] = isset($_POST['orderU']) ? sanitize_text_field($_POST['orderU']) : null;
            $menu_data['orderP'] = isset($_POST['orderP']) ? sanitize_text_field($_POST['orderP']) : null;
            $menu_data['popularItem'] = isset($_POST['popularItem']) ? sanitize_text_field($_POST['popularItem']) : null;
            $menu_data['spiceLVL'] = isset($_POST['spiceLVL']) ? sanitize_text_field($_POST['spiceLVL']) : null;
            $menu_data['showQute'] = isset($_POST['showQute']) ? sanitize_text_field($_POST['showQute']) : null;
        }
        if ($user_id != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        $listing_id = (isset($_POST['menuUp']) && $_POST['menuUp'] === 'yes')
            ? (isset($_POST['LID']) ? absint($_POST['LID']) : 0)
            : (isset($_POST['mListing']) ? absint($_POST['mListing']) : 0);
        if (!$listing_id || get_post_type($listing_id) !== 'listing' || !current_user_can('edit_post', $listing_id)) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('You do not have permission to edit this listing.', 'listingpro');
            die(json_encode($return));
        }
        if (isset($_POST['menuUp']) && $_POST['menuUp'] == 'yes') {
            $existing_menus     =   get_post_meta($LID, 'lp-listing-menu', true);
            $menu_with_type  =   $existing_menus[$menu_type];
            if (!is_array($menu_with_type)) {
                $menu_with_type =   $existing_menus[$menuID_arr[0]];
            }
            $menu_with_group    =   $menu_with_type[$menu_group];
            if (!is_array($menu_with_group)) {
                $menu_with_group    =   $menu_with_type[$menuID_arr[1]];
            }
            $target_arr =   $menu_with_group[$menu_indx];
            if (
                ($menu_type == $_POST['mType'] && $menu_group == $_POST['mGroup']) ||
                ($menuID_arr[0] == $_POST['mType'] && $menuID_arr[1] == $_POST['mGroup']) ||
                ($menu_type == $_POST['mType'] && $menuID_arr[1] == $_POST['mGroup']) ||
                ($menuID_arr[0] == $_POST['mType'] && $menu_group == $_POST['mGroup'])
            ) {
                $target_arr['mDetail']  = $mDetail;
                $target_arr['mTitle']   =   $mTitle;
                $target_arr['mNewPrice']    =   $mNewPrice;
                $target_arr['mOldPrice']    =   $mOldPrice;
                $target_arr['mQuoteT']    =   $mQuoteT;
                $target_arr['mQuoteL']    =   $mQuoteL;
                $target_arr['mLink']    =   $mLink;
                $target_arr['mImage']    =   $mImage;
                $target_arr['orderU']    =   $orderU;
                $target_arr['orderP']    =   $orderP;
                $target_arr['popularItem']    =   $popularItem;
                $target_arr['spiceLVL']    =   $spiceLVL;
                $return['status']   =   'success';
                $return['msg']      =   esc_html__('Menu item updated successfully', 'listingpro');
                if (is_array($existing_menus[$menu_type][$menu_group][$menu_indx])) {
                    $existing_menus[$menu_type][$menu_group][$menu_indx]    =   $target_arr;
                    update_post_meta($LID, 'lp-listing-menu', $existing_menus);
                } elseif ($existing_menus[$menuID_arr[0]][$menuID_arr[1]][$menu_indx]) {
                    $existing_menus[$menuID_arr[0]][$menuID_arr[1]][$menu_indx] =   $target_arr;
                    update_post_meta($LID, 'lp-listing-menu', $existing_menus);
                } elseif (is_array($existing_menus[$menu_type][$menuID_arr[1]][$menu_indx])) {
                    $existing_menus[$menuID_arr[0]][$menuID_arr[1]][$menu_indx] =   $target_arr;
                    update_post_meta($LID, 'lp-listing-menu', $existing_menus);
                } elseif (is_array($existing_menus[$menuID_arr[0]][$menu_group][$menu_indx])) {
                    $existing_menus[$menuID_arr[0]][$menu_group][$menu_indx]    =   $target_arr;
                    update_post_meta($LID, 'lp-listing-menu', $existing_menus);
                }
                die(json_encode($return));
            } else {
                unset($existing_menus[$menu_type][$menu_group][$menu_indx]);
                $group_count =   count($existing_menus[$menu_type][$menu_group]);
                if ($group_count == 0) {
                    unset($existing_menus[$menu_type][$menu_group]);
                }
                $type_count =  count($existing_menus[$menu_type]);
                if ($type_count == 0) {
                    unset($existing_menus[$menu_type]);
                }
                $new_menu_data_arr  =   array();
                $new_menu_data_arr['mDetail']  = $mDetail;
                $new_menu_data_arr['mTitle']   =   $mTitle;
                $new_menu_data_arr['mNewPrice']    =   $mNewPrice;
                $new_menu_data_arr['mOldPrice']    =   $mOldPrice;
                $new_menu_data_arr['mQuoteT']    =   $mQuoteT;
                $new_menu_data_arr['mQuoteL']    =   $mQuoteL;
                $new_menu_data_arr['mLink']    =   $mLink;
                $new_menu_data_arr['mImage']    =   $mImage;
                $new_menu_data_arr['mListing']    =   $LID;
                $new_menu_data_arr['mGroup']    =   $mGroup;
                $new_menu_data_arr['mType']    =   $mType;
                $new_menu_data_arr['orderU']    =   $orderU;
                $new_menu_data_arr['orderP']    =   $orderP;
                $new_menu_data_arr['popularItem']    =   $popularItem;
                $new_menu_data_arr['spiceLVL']    =   $spiceLVL;
                $existing_menus[$_POST['mType']][$_POST['mGroup']][]    =   $new_menu_data_arr;
                update_post_meta($LID, 'lp-listing-menu', $existing_menus);
                $return['status']   =   'success';
                $return['msg']      =   esc_html__('Menu item updated successfully', 'listingpro');
                die(json_encode($return));
            }
        } else {
            $checkStatus = lp_validate_listing_action($_POST['mListing'], 'menu');
            if (empty($checkStatus)) {
                $return['status']   =   'error';
                $return['msg']      =   'Event not   with this listing';
                die(json_encode($return));
            }
            $existing_menus     =   get_post_meta(sanitize_text_field($_POST['mListing']), 'lp-listing-menu', true);
            if (is_array($existing_menus) && !empty($existing_menus)) {
                $menu_type_arr[]    =   $_POST['mType'];
                if (is_array($menu_type_arr)) {
                    foreach ($menu_type_arr as $k => $v) {
                        if (is_array($_POST['mGroup'])) {
                            foreach ($_POST['mGroup'] as $mGroup) {
                                $data_arr[$v][$mGroup][]   =   $menu_data;
                            }
                        } else {
                            $data_arr[$v][$menu_data['mGroup']][]   =   $menu_data;
                        }
                    }
                }
                $new_data   =   array_merge_recursive($existing_menus, $data_arr);
                update_post_meta(sanitize_text_field($_POST['mListing']), 'lp-listing-menu', $new_data);
                $menu_after_update  =   get_post_meta(sanitize_text_field($_POST['mListing']), 'lp-listing-menu', true);
                $return['status']   =   'success';
                $return['msg']      =   esc_html__('Menu item added successfully', 'listingpro');
                $return['data'] =   $new_data;
                $return['menu_after_update']    =   $menu_after_update;
                die(json_encode($return));
            } else {
                $menu_type_arr[]    =   $_POST['mType'];
                if (is_array($menu_type_arr)) {
                    foreach ($menu_type_arr as $k => $v) {
                        if (is_array($_POST['mGroup'])) {
                            foreach ($_POST['mGroup'] as $mGroup) {
                                $data_arr[$v][$mGroup][]   =   $menu_data;
                            }
                        } else {
                            $data_arr[$v][$menu_data['mGroup']][]   =   $menu_data;
                        }
                    }
                }
                update_post_meta($_POST['mListing'], 'lp-listing-menu', $data_arr);
                $menu_after_update  =   get_post_meta($_POST['mListing'], 'lp-listing-menu', true);
                $return['status']       =   'success';
                $return['msg']          =   esc_html__('Menu item added successfully', 'listingpro');
                $return['data']         =   $data_arr;
                $return['menu_after_update']    =   $menu_after_update;
                die(json_encode($return));
            }
        }
    }
}
/* ============== ListingPro add Discount codes ============ */
add_action('wp_ajax_add_discount_cb', 'add_discount_cb');
if (!function_exists('add_discount_cb')) {
    function add_discount_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $timezone = get_option('timezone_string');
        if ($timezone) {
            date_default_timezone_set($timezone);
        } else {
            date_default_timezone_set('UTC'); // Set default timezone to UTC or any other preferred timezone
        }

        $return                 =   array();
        $listing_discount_data  =   array();
        $user_id    =   get_current_user_id();
        $user_idd   =   sanitize_text_field($_POST['user_id']);
        $listing_discount_data['disHea'] = isset($_POST['disHea']) ? sanitize_text_field($_POST['disHea']) : null;
        $listing_discount_data['disCod'] = isset($_POST['disCod']) ? sanitize_text_field($_POST['disCod']) : null;
        $listing_discount_data['disExpS'] = isset($_POST['disExpS']) ? strtotime(sanitize_text_field($_POST['disExpS'])) : null;
        $listing_discount_data['disExpE'] = isset($_POST['disExpE']) ? strtotime(sanitize_text_field($_POST['disExpE'])) : null;
        $listing_discount_data['disTimeS'] = isset($_POST['disTimeS']) ? sanitize_text_field($_POST['disTimeS']) : null;
        $listing_discount_data['disTimeE'] = isset($_POST['disTimeE']) ? sanitize_text_field($_POST['disTimeE']) : null;
        $listing_discount_data['disBT'] = isset($_POST['disBT']) ? sanitize_text_field($_POST['disBT']) : null;
        $listing_discount_data['disBL'] = isset($_POST['disBL']) ? sanitize_text_field($_POST['disBL']) : null;
        $listing_discount_data['disLI'] = isset($_POST['disLI']) ? sanitize_text_field($_POST['disLI']) : null;
        $listing_discount_data['disDes'] = isset($_POST['disDes']) ? htmlentities(sanitize_text_field($_POST['disDes'])) : null;
        $listing_discount_data['disID'] = isset($_POST['disID']) ? sanitize_text_field($_POST['disID']) : null;
        $listing_discount_data['disImg'] = isset($_POST['disImg']) ? sanitize_text_field($_POST['disImg']) : null;
        $listing_discount_data['disOff'] = isset($_POST['disOff']) ? sanitize_text_field($_POST['disOff']) : null;
        $listing_discount_data['disSta'] = isset($_POST['disSta']) ? sanitize_text_field($_POST['disSta']) : null;
        $listing_id    =   $listing_discount_data['disLI'];
        if ($listing_id == '' || $listing_id == null) {
            $listing_id =  $listing_discount_data['disID'];
        }
        if ($user_id != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        $disUp = isset($_POST['disUp']) ? sanitize_text_field($_POST['disUp']) : null;
        if ($disUp == 'yes') {
            $disID_arr  =   explode('-', sanitize_text_field($_POST['disID']));
            $data_index =   $disID_arr[1];
            $existing_data  =   get_post_meta($listing_id, 'listing_discount_data', true);
            $existing_data[$data_index]['disHea'] = isset($_POST['disHea']) ? sanitize_text_field($_POST['disHea']) : null;
            $existing_data[$data_index]['disCod'] = isset($_POST['disCod']) ? sanitize_text_field($_POST['disCod']) : null;
            $existing_data[$data_index]['disExpS'] = isset($_POST['disExpS']) ? strtotime(sanitize_text_field($_POST['disExpS'])) : null;
            $existing_data[$data_index]['disExpE'] = isset($_POST['disExpE']) ? strtotime(sanitize_text_field($_POST['disExpE'])) : null;
            $existing_data[$data_index]['disTimeS'] = isset($_POST['disTimeS']) ? sanitize_text_field($_POST['disTimeS']) : null;
            $existing_data[$data_index]['disTimeE'] = isset($_POST['disTimeE']) ? sanitize_text_field($_POST['disTimeE']) : null;
            $existing_data[$data_index]['disBT'] = isset($_POST['disBT']) ? sanitize_text_field($_POST['disBT']) : null;
            $existing_data[$data_index]['disBL'] = isset($_POST['disBL']) ? sanitize_text_field($_POST['disBL']) : null;
            $existing_data[$data_index]['disDes'] = isset($_POST['disDes']) ? htmlentities(sanitize_text_field($_POST['disDes'])) : null;
            $existing_data[$data_index]['disOff'] = isset($_POST['disOff']) ? sanitize_text_field($_POST['disOff']) : null;
            $existing_data[$data_index]['disImg'] = isset($_POST['disImg']) ? sanitize_text_field($_POST['disImg']) : null;
            update_post_meta($listing_id, 'listing_discount_data', $existing_data);
            $return['status']   =   'success';
            $return['msg']      =   esc_html__('Coupon Updated successfully', 'listingpro');
            die(json_encode($return));
        } else {
            $checkStatus = lp_validate_listing_action($listing_id, 'deals');
            if (empty($checkStatus)) {
                $return['status']   =   'error';
                $return['msg']      =   esc_html__('Coupons are not allowed with this listing', 'listingpro');
                die(json_encode($return));
            }
            $data_arr[]   = $listing_discount_data;
            $existing_data  =   get_post_meta($listing_id, 'listing_discount_data', true);
            if (is_array($existing_data) && !empty($existing_data)) {
                $new_data   =   array_merge($existing_data, $data_arr);
                update_post_meta($listing_id, 'listing_discount_data', $new_data);
                $return['status']   =   'success';
                $return['msg']      =   esc_html__('Coupon Created successfully', 'listingpro');
                die(json_encode($return));
            } else {
                update_post_meta($listing_id, 'listing_discount_data', $data_arr);
                $return['status']   =   'success';
                $return['msg']      =   esc_html__('Coupon Created successfully', 'listingpro');
                die(json_encode($return));
            }
        }
        $return['status']   =   'success';
        $return['msg']      =   esc_html__('Coupon Created successfully', 'listingpro');
        $return['discount_Data']      =   $listing_discount_data['disID'];
        die(json_encode($return));
    }
}
/* ============== Listingpro delete announcements/Discount Codes ============ */
add_action('wp_ajax_del_ann_dis_menu_cb', 'del_ann_dis_menu_cb');
if (!function_exists('del_ann_dis_menu_cb')) {
    function del_ann_dis_menu_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $return =   array();
        if (!is_user_logged_in()) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        $user_id        =   get_current_user_id();
        $user_idd       =   sanitize_text_field($_POST['user_id']);
        $delType        =   sanitize_text_field($_POST['delType']);
        $targetID       =   sanitize_text_field($_POST['targetID']);
        $delIDS         =   sanitize_text_field($_POST['delIDS']);
        if ($user_id != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        if ($delType == 'event') {
            if (!current_user_can('delete_post', $targetID)) {
                $return['status']   =   'error';
                $return['msg']      =   esc_html__('You are not allowed to delete this item', 'listingpro');
                die(json_encode($return));
            } else {
                $eLID   =   get_post_meta($targetID, 'event-lsiting-id', true);
                $listingEvents = get_post_meta($eLID, 'event_id', true);
                if (is_array($listingEvents) && !empty($listingEvents)) {
                    $targetKey = array_search($targetID, $listingEvents);
                    unset($listingEvents[$targetKey]);
                    if (count($listingEvents) > 0) {
                        update_post_meta($eLID, 'event_id', $listingEvents);
                    } else {
                        delete_post_meta($eLID, 'event_id');
                    }
                } else {
                    delete_post_meta($eLID, 'event_id');
                }
                wp_delete_post($targetID, true);
                $return['status'] = 'success';
                $return['msg'] = esc_html__('Data deleted', 'listingpro');
                die(json_encode($return));
            }
        }
        if ($delType == 'dis') {
            $targetID_arr       =   explode('-', $targetID);
            $target_index       =   $targetID_arr[1];
            $target_listing     =   $targetID_arr[0];
            $target_data    =   get_post_meta($target_listing, 'listing_discount_data', true);
            unset($target_data[$target_index]);
            if (count($target_data) == 0) {
                delete_post_meta($target_listing, 'listing_discount_data');
            } else {
                update_post_meta($target_listing, 'listing_discount_data', $target_data);
            }
            $return['status'] = 'success';
            $return['msg'] = esc_html__('Data deleted', 'listingpro');
            die(json_encode($return));
        }
        if ($delType == 'ann') {
            $targetID_arr       =   explode('-', $targetID);
            $target_index       =   $targetID_arr[1];
            $target_listing     =   $targetID_arr[0];
            $existing_data  =   get_post_meta($target_listing, 'lp_listing_announcements', true);
            unset($existing_data[$target_index]);
            $remaining  =   count($existing_data);
            if ($remaining == 0) {
                delete_post_meta($target_listing, 'lp_listing_announcements');
            } else {
                update_post_meta($target_listing, 'lp_listing_announcements', $existing_data);
            }
            $return['status'] = 'success';
            $return['msg'] = esc_html__('Data deleted', 'listingpro');
            die(json_encode($return));
        }
        if ($delType == 'offer') {
            $delIDS_Arr   =   explode(',', $delIDS);
            $LID    =   $delIDS_Arr[1];
            $INX    =   $delIDS_Arr[0];
            $lp_listing_offers  =   get_post_meta($LID, 'lp_listing_offers', true);
            unset($lp_listing_offers[$INX]);
            if (empty($lp_listing_offers)) {
                delete_post_meta($LID, 'lp_listing_offers');
            } else {
                update_post_meta($LID, 'lp_listing_offers', $lp_listing_offers);
            }
            $return['status'] = 'success';
            $return['msg'] = esc_html__('Data deleted', 'listingpro');
            die(json_encode($return));
        }
        if ($delType == 'menu') {
            $menuID_arr     =   explode('_', $targetID);
            $menu_type      =   str_replace('-', ' ', $menuID_arr[0]);
            $menu_group     =   str_replace('-', ' ', $menuID_arr[1]);
            $menu_indx      =   $menuID_arr[2];
            $existing_menus =   get_post_meta($delIDS, 'lp-listing-menu', true);
            $menu_with_type  =   $existing_menus[$menu_type];
            if (!is_array($menu_with_type)) {
                $menu_type  =   $menuID_arr[0];
                $menu_with_type =   $existing_menus[$menu_type];
            }
            $menu_with_group    =   $menu_with_type[$menu_group];
            if (!is_array($menu_with_group)) {
                $menu_group    =   $menuID_arr[1];
            }
            $array1 = count($existing_menus[$menu_type]);
            $array2 =   count($existing_menus[$menu_type][$menu_group]);
            unset($existing_menus[$menu_type][$menu_group][$menu_indx]);
            $count2 =   count($existing_menus[$menu_type][$menu_group]);
            if ($count2 == 0) {
                unset($existing_menus[$menu_type][$menu_group]);
            }
            $count1 = count($existing_menus[$menu_type]);
            if ($count1 == 0) {
                unset($existing_menus[$menu_type]);
            }
            if (count($existing_menus) > 0) {
                update_post_meta($delIDS, 'lp-listing-menu', $existing_menus);
            } else {
                delete_post_meta($delIDS, 'lp-listing-menu');
            }
            $return['status'] = 'success';
            $return['msg'] = esc_html__('Data deleted', 'listingpro');
            die(json_encode($return));
        }
        if ($delType == 'type') {
            $menu_types_data        =   get_user_meta($user_id, 'user_menu_types');
            $del_all                =   sanitize_text_field($_POST['dellAll']);
            $menu_types_data        =   $menu_types_data[0];
            $type_key               =   isset($menu_types_data[$targetID]) ? $menu_types_data[$targetID]['type'] : null;
            $del_res                =   '';
            if ($del_all == 1) {
                del_menu_data_by_user($user_id, $delType, $type_key);
            }
            unset($menu_types_data[$targetID]);
            update_user_meta($user_id, 'user_menu_types', $menu_types_data);
            $return['status'] = 'success';
            $return['msg'] = esc_html__('Data deleted', 'listingpro');
            die(json_encode($return));
        }
        if ($delType == 'group') {
            $menu_groups_data       =   get_user_meta($user_id, 'user_menu_groups');
            $del_all                =   sanitize_text_field($_POST['dellAll']);
            $menu_groups_data       =   $menu_groups_data[0];
            $group_Key              =   $menu_groups_data[$targetID]['group'];
            if ($del_all == 1) {
                del_menu_data_by_user($user_id, $delType, $group_Key);
            }
            unset($menu_groups_data[$targetID]);
            update_user_meta($user_id, 'user_menu_groups', $menu_groups_data);
            $return['status'] = 'success';
            $return['msg'] = esc_html__('Data deleted', 'listingpro');
            die(json_encode($return));
        }
        $return['status'] = 'fail';
        $return['msg'] = esc_html__('Bad Request', 'listingpro');
        die(json_encode($return));
    }
}
add_action('wp_ajax_del_all_menu_cb', 'del_all_menu_cb');
if (!function_exists('del_all_menu_cb')) {
    function del_all_menu_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $user_id        =   get_current_user_id();
        $user_idd       =   sanitize_text_field($_POST['user_id']);
        $lid            =   sanitize_text_field($_POST['lid']);
        if ($user_id != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        } else {
            delete_post_meta($lid, 'lp-listing-menu');
            $return['status']   =   'success';
            $return['msg']      =   esc_html__('Menu Items deleted successfully', 'listingpro');
            die(json_encode($return));
        }
    }
}
add_action('wp_ajax_discount_display_area', 'discount_display_area');
if (!function_exists('discount_display_area')) {
    function discount_display_area()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $user_idd   =   get_current_user_id();
        $userID       =  sanitize_text_field($_POST['userID']);
        $thisval        =   sanitize_text_field($_POST['thisval']);
        if ($userID != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        update_user_meta($userID, 'discount_display_area', $thisval);
        $return['status'] = 'success';
        $return['msg'] = $thisval . '-' . $userID;
        die(json_encode($return));
    }
}
add_action('wp_ajax_event_display_area', 'event_display_area');
if (!function_exists('event_display_area')) {
    function event_display_area()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $user_idd   =   get_current_user_id();
        $userID       =   sanitize_text_field($_POST['userID']);
        $thisval        =  sanitize_text_field($_POST['thisval']);
        if ($userID != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        }
        update_user_meta($userID, 'event_display_area', $thisval);
        $return['status'] = 'success';
        $return['msg'] = $thisval . '-' . $userID;
        die(json_encode($return));
    }
}
add_action('wp_ajax_author_review_tab_cb', 'author_review_tab_cb');
add_action('wp_ajax_nopriv_author_review_tab_cb', 'author_review_tab_cb');
//author_review_tab_cb
if (!function_exists('author_review_tab_cb')) {
    function author_review_tab_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $reviewStyle    =  sanitize_text_field($_POST['reviewStyle']);
        $listID         =   sanitize_text_field($_POST['listID']);
        $authorID       =  sanitize_text_field($_POST['authorID']);
        if ($reviewStyle == 'style1') {
        ?>
            <div id="submitreview" class="clearfix">
                <?php
                listingpro_get_all_reviews($listID);
                ?>
            </div>
        <?php
        } else {
        ?>
            <div class="lp-listing-reviews">
                <?php
                listingpro_get_all_reviews_v2($GLOBALS['listID']);
                ?>
            </div>
        <?php
        }
        ?>
        <?php
        die();
    }
}
add_action('wp_ajax_author_archive_tabs_cb', 'author_archive_tabs_cb');
add_action('wp_ajax_nopriv_author_archive_tabs_cb', 'author_archive_tabs_cb');
if (!function_exists('author_archive_tabs_cb')) {
    function author_archive_tabs_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        if (isset($_POST['authorPagin'])) {
            $GLOBALS['my_listing_views']    =  sanitize_text_field($_POST['listingLayout']);
            $GLOBALS['pageno']  =   sanitize_text_field($_POST['pageNo']);
            $GLOBALS['authorID']  =   sanitize_text_field($_POST['authorID']);
            get_template_part('templates/author/author-listings');
        } else {
            $tabType        =   sanitize_text_field($_POST['tabType']);
            $reviewStyle    =  sanitize_text_field($_POST['reviewStyle']);
            $authorID       =   sanitize_text_field($_POST['authorID']);
            $listingLayout       =   sanitize_text_field($_POST['listingLayout']);
            $GLOBALS['authorID']  =   $authorID;
            if ($tabType == 'reviews') {
                if ($reviewStyle == 'style1') {
                    get_template_part('templates/author/author-reviews-style1');
                } elseif ($reviewStyle == 'style2') {
                    get_template_part('templates/author/author-reviews-style2');
                }
            } elseif ($tabType == 'photos') {
                get_template_part('templates/author/author-photos');
            } elseif ($tabType == 'aboutme') {
                get_template_part('templates/author/author-about');
            } elseif ($tabType == 'contact') {
                get_template_part('templates/author/author-contact');
            } elseif ($tabType   ==  'mylistings') {
                $GLOBALS['my_listing_views']    =   $listingLayout;
                get_template_part('templates/author/author-listings');
            }
            //New Update 2.7.0
            elseif ($tabType   ==  'myevents') {

                include_once(EVENTPRO_PLUGIN_PATH . 'templates\author-events.php');
            } elseif ($tabType   ==  'mygroups') {

                include_once(EVENTPRO_PLUGIN_PATH . 'templates\author-groups.php');
            }
            //End New Update 2.7.0
        }
        die();
    }
}
if (!function_exists('lsitingpro_pagination_author')) {
    function lsitingpro_pagination_author($my_query, $pageno = null, $sKeyword = '')
    {
        $output = '';
        $pages = '';
        $pages = $my_query->max_num_pages;
        $totalpages = $pages;
        $ajax_pagin_classes =   'pagination lp-filter-pagination-ajx';
        if (is_author()) {
            $ajax_pagin_classes =   '';
        }
        if (!empty($pages) && $pages > 1) {
            $output .= '<div class="lp-pagination ' . $ajax_pagin_classes . '">';
            $output .= '<ul class="page-numbers">';
            $n = 1;
            $flagAt = 7;
            $flagAt2 = 7;
            $flagOn = 0;
            while ($pages > 0) {
                if (isset($pageno) && !empty($pageno)) {
                    if (!empty($totalpages) && $totalpages < 7) {
                        if ($pageno == $n) {
                            $output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers author-haspaglink current">' . $n . '</span></li>';
                        } else {
                            $output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers author-haspaglink">' . $n . '</span></li>';
                        }
                    } elseif (!empty($totalpages) && $totalpages > 6) {
                        $flagOn = $pageno - 5;
                        $flagOn2 = $pageno + 7;
                        if ($pageno == $n) {
                            $output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers author-haspaglink current">' . $n . '</span></li>';
                        } else {
                            if ($n <= 4) {
                                $output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers author-haspaglink">' . $n . '</span></li>';
                            } elseif ($n > 4 && $flagAt2 == 7) {
                                $output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers author-haspaglink">' . $n . '</span></li>';
                                $output .= '<li><span data-skeyword="' . $sKeyword . '"  class="page-numbers">...</span></li>';
                                $flagAt2 = 1;
                            } elseif ($n > 4  && $n >= $flagOn && $n < $flagOn2) {
                                $output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers author-haspaglink">' . $n . '</span></li>';
                            } elseif ($n == $totalpages) {
                                $output .= '<li><span data-skeyword="' . $sKeyword . '" class="page-numbers">...</span></li>';
                                $output .= '<li><span data-skeyword="' . $sKeyword . '" data-pageurl="' . $n . '"  class="page-numbers author-haspaglink">' . $n . '</span></li>';
                            }
                        }
                    }
                } else {
                    if ($n == 1) {
                        $output .= '<li><span data-pageurl="' . $n . '"  class="page-numbers  author-haspaglink current">' . $n . '</span></li>';
                    } else if ($n < 7) {
                        $output .= '<li><span data-pageurl="' . $n . '"  class="page-numbers author-haspaglink">' . $n . '</span></li>';
                    } else if ($n > 7 && $pages > 7 && $flagAt == 7) {
                        $output .= '<li><span  class="page-numbers">...</span></li>';
                        $flagAt = 1;
                    } else if ($n > 7 && $pages < 7 && $flagAt == 1) {
                        $output .= '<li><span data-pageurl="' . $n . '"  class="page-numbers author-haspaglink">' . $n . '</span></li>';
                    }
                }
                $pages--;
                $n++;
                $output .= '</li>';
            }
            $output .= '</ul>';
            $output .= '</div>';
        }
        return $output;
    }
}
add_action('wp_ajax_author_archive_listings_cb', 'author_archive_listings_cb');
add_action('wp_ajax_nopriv_author_archive_listings_cb', 'author_archive_listings_cb');
if (!function_exists('author_archive_listings_cb')) {
    function author_archive_listings_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $paged        =   sanitize_text_field($_POST['pageNum']);
        global $listingpro_options;
        $author_list_view   =   'grid_view_v2';
        if (isset($listingpro_options['my_listing_views'])) {
            $author_list_view   =   $listingpro_options['my_listing_views'];
        }
        $type = 'listing';
        $postsonpage = '';
        if (isset($listingpro_options['my_listing_per_page'])) {
            $postsonpage = $listingpro_options['my_listing_per_page'];
        } else {
            $postsonpage = 9;
        }
        $args = array(
            'post_type' => $type,
            'post_status' => 'publish',
            'posts_per_page' => $postsonpage,
            'order' => 'ASC',
            'paged'       => $paged,
            'author' => 1,
        );
        $my_query = null;
        $my_query = new WP_Query($args);
        if ($my_query->have_posts()) {
            while ($my_query->have_posts()) : $my_query->the_post();
                if ($author_list_view == 'grid_view_v2') {
                    get_template_part('layouts/loop-grid-view');
                } elseif ($author_list_view == 'list_view_v2') {
                    get_template_part('layouts/loop-list-view');
                } else {
                    get_template_part('listing-loop');
                }
            endwhile;
        }
        echo '<div class="md-overlay"></div>';
        die();
    }
}
/* ============== Listingpro compaigns ============ */
if (!function_exists('listingpro_get_campaigns_listing_v2')) {
    function listingpro_get_campaigns_listing_v2($ad_style, $campaign_type, $IDSonly, $taxQuery = array(), $searchQuery = array(), $priceQuery = array(), $s = null, $noOfListings = null, $posts_in = null)
    {
        $adsType = array(
            'lp_random_ads',
            'lp_detail_page_ads',
            'lp_top_in_search_page_ads'
        );
        global $listingpro_options;
        $listing_style  = isset($listingpro_options['listing_style']) ? $listingpro_options['listing_style'] : '';
        $postNumber = '';
        if ($listing_style == '3' && !is_front_page()) {
            if (empty($noOfListings)) {
                $postNumber = 2;
            } else {
                $postNumber = $noOfListings;
            }
        } else {
            if (empty($noOfListings)) {
                $postNumber = 3;
            } else {
                $postNumber = $noOfListings;
            }
        }
        if (!empty($campaign_type)) {
            if (in_array($campaign_type, $adsType, true)) {
                $TxQuery = array();
                if (!empty($taxQuery) && is_array($taxQuery)) {
                    $TxQuery = $taxQuery;
                } elseif (!empty($searchQuery) && is_array($searchQuery)) {
                    $TxQuery = $searchQuery;
                }
                $args = array(
                    'orderby' => 'rand',
                    'post_type' => 'listing',
                    'post_status' => 'publish',
                    'posts_per_page' => $postNumber,
                    'tax_query' => $TxQuery,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key'     => 'campaign_status',
                            'value'   => array('active'),
                            'compare' => 'IN',
                        ),
                        array(
                            'key'     => $campaign_type,
                            'value'   => array('active'),
                            'compare' => 'IN',
                        ),
                        $priceQuery,
                    ),
                );
                if (!empty($s)) {
                    $args = array(
                        'orderby' => 'rand',
                        'post_type' => 'listing',
                        'post_status' => 'publish',
                        's' => $s,
                        'posts_per_page' => $postNumber,
                        'tax_query' => $TxQuery,
                        'meta_query' => array(
                            'relation' => 'AND',
                            array(
                                'key'     => 'campaign_status',
                                'value'   => array('active'),
                                'compare' => 'IN',
                            ),
                            array(
                                'key'     => $campaign_type,
                                'value'   => array('active'),
                                'compare' => 'IN',
                            ),
                            $priceQuery,
                        ),
                    );
                }
                if (!empty($posts_in)) {
                    $args['post__in'] = $posts_in;
                }
                $idsArray = array();
                $the_query = new WP_Query($args);
                if ($the_query->have_posts()) {
                    while ($the_query->have_posts()) {
                        $the_query->the_post();
                        if ($IDSonly == true) {
                            $idsArray[] =  get_the_ID();
                        } else {
                            if (is_singular('listing')) {
                                get_template_part('templates/details-page-ads');
                            } elseif (is_page() && is_active_sidebar('default-sidebar')) {
                                get_template_part('templates/details-page-ads');
                            } else {
                                $listing_mobile_view  = isset($listingpro_options['single_listing_mobile_view']) ? $listingpro_options['single_listing_mobile_view'] : '';
                                if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                    get_template_part('mobile/listing-loop-app-view');
                                } else {
                                    if ($ad_style == 'list' || $ad_style == 'grid') {
                                        get_template_part('layouts/loop-list-view');
                                    }
                                    if ($ad_style == 'sidebar') {
                                        get_template_part('layouts/templates/sidebar-loop');
                                    }
                                }
                            }
                        }
                        wp_reset_postdata();
                    }
                    if ($IDSonly == true) {
                        if (!empty($idsArray)) {
                            return $idsArray;
                        }
                    }
                }
            }
        }
    }
}
/* ============== ListingPro category menu ============ */
if (!function_exists('listingpro_categoies_menu')) {
    function listingpro_categoies_menu()
    {
        $defaults = array(
            'theme_location'  => 'category_menu',
            'container'       => 'false',
            'menu_class'      => '',
            'menu_id'         => '',
            'echo'            => true,
            'fallback_cb'     => '',
            'items_wrap'      => '<ul id="menu-category" class="%2$s lp-user-menu list-style-none">%3$s</ul>',
        );
        return wp_nav_menu($defaults);
    }
}
/* ================ */
add_action('wp_ajax_select2_ajax_dashbaord_listing_booking', 'select2_ajax_dashbaord_listing_booking');
if (!function_exists('select2_ajax_dashbaord_listing_booking')) {
    function select2_ajax_dashbaord_listing_booking()
    {
        $return = array();
        if (is_user_logged_in()) {
            $user_id        =   get_current_user_id();
            $targetPlanMetaKey  =   'listingproc_plan_reservera';
            if (isset($_GET['target'])) {
                if (!empty($_GET['target']) && $_GET['target'] != 'false') {
                    $targetPlanMetaKey  =   'listingproc_plan_timekit';
                }
            }
            $search_results = new WP_Query(array(
                's' => esc_html($_GET['q']),
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => 50,
                'post_type' => 'listing',
                'author' => $user_id
            ));
            if ($search_results->have_posts()) :
                while ($search_results->have_posts()) : $search_results->the_post();
                    //$checkStatus = lp_validate_listing_action($search_results->post->ID, $targetPlanMetaKey);
                    $pLan_Id = listing_get_metabox_by_ID('Plan_id', $search_results->post->ID);
                    $checkStatus = false;
                    $disabled = 'no';
                    if (!empty($pLan_Id)) {
                        $plans_meta = get_post_meta($pLan_Id, $targetPlanMetaKey, true);
                        if (empty($plans_meta) || $plans_meta == "false") {
                            $checkStatus = false;
                            $disabled = 'yes';
                        } elseif (!empty($plans_meta)) {
                            $checkStatus = true;
                        }
                    }
                    if (!empty($checkStatus)) {
                        $title = (mb_strlen($search_results->post->post_title) > 50) ? mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
                        $return[] = array($search_results->post->ID, $title, $disabled); // array( Post ID, Post Title )
                    }
                // shorten the title a little
                endwhile;
            endif;
        }
        echo json_encode($return);
        die;
    }
}
/* ================ */
add_action('wp_ajax_select2_ajax_dashbaord_listing', 'select2_ajax_dashbaord_listing');
if (!function_exists('select2_ajax_dashbaord_listing')) {
    function select2_ajax_dashbaord_listing()
    {
        $return = array();
        if (is_user_logged_in()) {
            $user_id        =   get_current_user_id();
            $targetPlanMetaKey  =   'menu';
            if (isset($_GET['targetPlanMetaKey'])) {
                $targetPlanMetaKey  =   esc_html($_GET['targetPlanMetaKey']);
            }
            $search_results = new WP_Query(array(
                's' => esc_html($_GET['q']),
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => 50,
                'post_type' => 'listing',
                'author' => $user_id
            ));
            if ($search_results->have_posts()) :
                while ($search_results->have_posts()) : $search_results->the_post();
                    $checkStatus = lp_validate_listing_action($search_results->post->ID, $targetPlanMetaKey);
                    $disabled   =   'no';
                    if (empty($checkStatus)) {
                        $disabled   =   'yes';
                    }
                    // shorten the title a little
                    $title = (mb_strlen($search_results->post->post_title) > 50) ? mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
                    $return[] = array($search_results->post->ID, $title, $disabled); // array( Post ID, Post Title )
                endwhile;
            endif;
        }
        echo json_encode($return);
        die;
    }
}
add_action('wp_ajax_select2_ajax_dashbaord_listing_unique', 'select2_ajax_dashbaord_listing_unique');
if (!function_exists('select2_ajax_dashbaord_listing_unique')) {
    function select2_ajax_dashbaord_listing_unique()
    {
        $return = array();
        if (is_user_logged_in()) {
            $user_id        =   get_current_user_id();
            $uniqueMetaKey  =   'event_id';
            $planmetakey  =   'events';
            if (isset($_GET)) {
                $uniqueMetaKey  =   esc_html($_GET['uniqueMetaKey']);
                $planmetakey    =   esc_html($_GET['planmetakey']);
            }
            if ($uniqueMetaKey == 'event_id') {
                $search_results = new WP_Query(array(
                    's' => esc_html($_GET['q']),
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1,
                    'posts_per_page' => 50,
                    'post_type' => 'listing',
                    'author' => $user_id,
                ));
                if ($search_results->have_posts()) :
                    $timeNow    =   strtotime("-1 day");
                    while ($search_results->have_posts()) : $search_results->the_post();
                        $checkStatus = lp_validate_listing_action($search_results->post->ID, $planmetakey);
                        if (empty($checkStatus)) {
                            $disabled   =   'yes';
                        } else {
                            $event_id   =   get_post_meta(get_the_ID(), 'event_id', true);
                            if ($event_id) {
                                $event_date =   get_post_meta($event_id, 'event-date', true);
                                if ($timeNow > $event_date) {
                                    $disabled   =   'yes';
                                } else {
                                    $disabled   =   'yes';
                                }
                            } else {
                                $disabled   =   'no';
                            }
                        }
                        $title = (mb_strlen($search_results->post->post_title) > 50) ? mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
                        $return[] = array($search_results->post->ID, $title, $disabled); // array( Post ID, Post Title )
                    endwhile;
                endif;
            } else {
                $search_results = new WP_Query(array(
                    's' => esc_html($_GET['q']),
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => 1,
                    'posts_per_page' => 50,
                    'post_type' => 'listing',
                    'author' => $user_id,
                    'meta_key' => $uniqueMetaKey,
                    'meta_compare' => 'NOT EXISTS'
                ));
                if ($search_results->have_posts()) :
                    while ($search_results->have_posts()) : $search_results->the_post();
                        $disabled   =   'no';
                        $checkStatus = lp_validate_listing_action($search_results->post->ID, $planmetakey);
                        if (empty($checkStatus) && $uniqueMetaKey != 'menu_listing') {
                            $disabled   =   'yes';
                        }
                        $title = (mb_strlen($search_results->post->post_title) > 50) ? mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
                        $return[] = array($search_results->post->ID, $title, $disabled); // array( Post ID, Post Title )
                    endwhile;
                endif;
            }
        }
        echo json_encode($return);
        die;
    }
}
add_action('wp_ajax_add_events_cb', 'add_events_cb');
if (!function_exists('add_events_cb')) {
    function add_events_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $user_id        =   get_current_user_id();
        $user_idd       =   sanitize_text_field($_POST['eUID']);
        if ($user_id != $user_idd) {
            $return['status']   =   'error';
            $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
            die(json_encode($return));
        } else {
            if (sanitize_text_field($_POST['eUp']) == 'yes') {
                $eTitle = isset($_POST['eTitle']) ? sanitize_text_field($_POST['eTitle']) : null;
                $eDesc = isset($_POST['eDesc']) ? htmlentities(sanitize_text_field($_POST['eDesc'])) : null;
                $eDate = isset($_POST['eDate']) ? sanitize_text_field($_POST['eDate']) : null;
                $eDateE = isset($_POST['eDateE']) ? sanitize_text_field($_POST['eDateE']) : null;
                $eTime = isset($_POST['eTime']) ? sanitize_text_field($_POST['eTime']) : null;
                $eTimeE = isset($_POST['eTimeE']) ? sanitize_text_field($_POST['eTimeE']) : null;
                $eLoc = isset($_POST['eLoc']) ? sanitize_text_field($_POST['eLoc']) : null;
                $eLat = isset($_POST['eLat']) ? sanitize_text_field($_POST['eLat']) : null;
                $eLon = isset($_POST['eLon']) ? sanitize_text_field($_POST['eLon']) : null;
                $eTUrl = isset($_POST['eTUrl']) ? sanitize_text_field($_POST['eTUrl']) : null;
                $eID = isset($_POST['eID']) ? sanitize_text_field($_POST['eID']) : null;
                $eImg = isset($_POST['eImg']) ? sanitize_text_field($_POST['eImg']) : null;
                if (!empty($eDateE) && strtotime($eDateE . $eTimeE) <= strtotime($eDate . $eTime)) {
                    $return['status']   =   'error';
                    $return['msg']      =   esc_html__('Invalid Event Start And End Date', 'listingpro');
                    die(json_encode($return));
                }
                $event_data = array(
                    'ID'           => $eID,
                    'post_title'   => $eTitle,
                    'post_content' => $eDesc,
                );
                $event_id   =   wp_update_post($event_data);
                if (!is_wp_error($event_id)) {
                    $attachment_id  =   attachment_url_to_postid($eImg);
                    set_post_thumbnail($event_id, $attachment_id);
                    update_post_meta($event_id, 'event-date', strtotime($eDate));
                    update_post_meta($event_id, 'event-date-e', strtotime($eDateE));
                    update_post_meta($event_id, 'event-time', $eTime);
                    update_post_meta($event_id, 'event-time-e', $eTimeE);
                    update_post_meta($event_id, 'event-loc', $eLoc);
                    update_post_meta($event_id, 'event-lat', $eLat);
                    update_post_meta($event_id, 'event-lon', $eLon);
                    update_post_meta($event_id, 'ticket-url', $eTUrl);
                    update_post_meta($event_id, 'event-img', $eImg);
                    $return['status']   =   'success';
                    $return['msg']      =   esc_html__('event updated successfully', 'listingpro');
                    die(json_encode($return));
                } else {
                    $return['status']   =   'error';
                    $return['msg']      =   esc_html__('Error while updating event', 'listingpro');
                    die(json_encode($return));
                }
            } else {
                $eTitle = isset($_POST['eTitle']) ? sanitize_text_field($_POST['eTitle']) : null;
                $eDesc = isset($_POST['eDesc']) ? htmlentities(sanitize_text_field($_POST['eDesc'])) : null;
                $eDate = isset($_POST['eDate']) ? sanitize_text_field($_POST['eDate']) : null;
                $eDateE = isset($_POST['eDateE']) ? sanitize_text_field($_POST['eDateE']) : null;
                $eTime = isset($_POST['eTime']) ? sanitize_text_field($_POST['eTime']) : null;
                $eTimeE = isset($_POST['eTimeE']) ? sanitize_text_field($_POST['eTimeE']) : null;
                $eLoc = isset($_POST['eLoc']) ? sanitize_text_field($_POST['eLoc']) : null;
                $eLat = isset($_POST['eLat']) ? sanitize_text_field($_POST['eLat']) : null;
                $eLon = isset($_POST['eLon']) ? sanitize_text_field($_POST['eLon']) : null;
                $eTUrl = isset($_POST['eTUrl']) ? sanitize_text_field($_POST['eTUrl']) : null;
                $eLID = isset($_POST['eLID']) ? sanitize_text_field($_POST['eLID']) : null;
                $eUtils = isset($_POST['eUtils']) ? sanitize_text_field(stripslashes($_POST['eUtils'])) : null;
                $eImg = isset($_POST['eImg']) ? sanitize_text_field($_POST['eImg']) : null;
                $eImgID = isset($_POST['eImgID']) ? sanitize_text_field($_POST['eImgID']) : null;
                $eUtils_array   =   explode('*', $eUtils);
                $eUtils_array   =   array_filter($eUtils_array);
                $eUtils_ar      =   array();

                if (!empty($eDateE) && strtotime($eDateE . $eTimeE) <= strtotime($eDate . $eTime)) {
                    $return['status']   =   'error';
                    $return['msg']      =   esc_html__('Invalid Event Start And End Date', 'listingpro');
                    die(json_encode($return));
                }
                foreach ($eUtils_array as $item) {
                    $item_arr   =   explode('|', $item);
                    $eUtils_ar[$item_arr[0]] =   $item_arr[1];
                }
                $event_data =   array(
                    'post_title'    => $eTitle,
                    'post_content'  =>  $eDesc,
                    'post_author' =>    $user_id,
                    'post_status' =>    'publish',
                    'post_type' => 'events'
                );
                $checkStatus = lp_validate_listing_action($eLID, 'events');
                if (empty($checkStatus)) {
                    $return['status']   =   'error';
                    $return['msg']      =   'Event not allowed with this listing';
                    die(json_encode($return));
                }
                $event_id   =   wp_insert_post($event_data);
                if (!is_wp_error($event_id)) {
                    $attachment_id  =   attachment_url_to_postid($eImg);
                    set_post_thumbnail($event_id, $attachment_id);
                    update_post_meta($event_id, 'event-date', strtotime($eDate));
                    update_post_meta($event_id, 'event-date-e', strtotime($eDateE));
                    update_post_meta($event_id, 'event-time', $eTime);
                    update_post_meta($event_id, 'event-time-e', $eTimeE);
                    update_post_meta($event_id, 'event-loc', $eLoc);
                    update_post_meta($event_id, 'event-lat', $eLat);
                    update_post_meta($event_id, 'event-lon', $eLon);
                    update_post_meta($event_id, 'ticket-url', $eTUrl);
                    update_post_meta($event_id, 'event-utilities', $eUtils_ar);
                    update_post_meta($event_id, 'event-lsiting-id', $eLID);
                    update_post_meta($event_id, 'event-img', $eImg);
                    $attached_events    =   get_post_meta($eLID, 'event_id', true);
                    if ($attached_events && !is_array($attached_events)) {
                        $attached_events    =   (array) $attached_events;
                        array_push($attached_events, $event_id);
                    } elseif ($attached_events && is_array($attached_events)) {
                        array_push($attached_events, $event_id);
                    } else {
                        $attached_events    =   (array) $event_id;
                    }
                    update_post_meta($eLID, 'event_id', $attached_events);
                    $return['status']   =   'success';
                    $return['msg']      =   esc_html__('event created successfully', 'listingpro');
                    die(json_encode($return));
                } else {
                    $return['status']   =   'error';
                    $return['msg']      =   esc_html__('Error while creating event', 'listingpro');
                    die(json_encode($return));
                }
            }
        }
    }
}
add_action('wp_ajax_event_attending_cb', 'event_attending_cb');
if (!function_exists('event_attending_cb')) {
    function event_attending_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $eID        =   sanitize_text_field($_POST['eID']);
        $eUID       =   sanitize_text_field($_POST['eUID']);
        $notGoing   =   sanitize_text_field($_POST['notGoing']);
        $get_event_attending_user_ids  =   get_post_meta($eID, 'attending-users', true);
        $get_user_events                =   get_user_meta($eUID, 'user-events', true);
        if ($notGoing == 'yes') {
            if (($key = array_search($eUID, $get_event_attending_user_ids)) !== false) {
                unset($get_event_attending_user_ids[$key]);
            }
            if (($key = array_search($eUID, $get_user_events)) !== false) {
                unset($get_user_events[$key]);
            }
        } else {
            if (empty($get_event_attending_user_ids)) {
                $get_event_attending_user_ids   =   array($eUID);
            } else {
                $get_event_attending_user_ids[] =   $eUID;
            }
            if (empty($get_user_events)) {
                $get_user_events    =   array($eID);
            } else {
                $get_user_events[]  =   $eID;
            }
        }
        $attendee_data  =   get_userdata($eUID);
        $author_avatar_url = get_user_meta($eUID, "listingpro_author_img_url", true);
        if (!empty($author_avatar_url)) {
            $avatar =  $author_avatar_url;
        } else {
            $avatar_url = listingpro_get_avatar_url($eUID, $size = '90');
            $avatar =  $avatar_url;
        }
        $attendee_avatar_html   =   '<li id="lpec-attendee-avatar-' . $eUID . '-' . $eID . '"><img src="' . $avatar . '" alt="' . $attendee_data->user_nicename . '"></li>';
        update_post_meta($eID, 'attending-users', $get_event_attending_user_ids);
        update_user_meta($eUID, 'user-events', $get_user_events);
        $attendies_count    =   count(get_post_meta($eID, 'attending-users', true));
        $going_count    =   $attendies_count . ' ' . esc_html__('going', 'listingpro');
        $return['status'] = 'success';
        $return['total_attending'] = $going_count;
        $return['user_ids_arr'] = $get_event_attending_user_ids;
        $return['user_events_arr'] = $get_user_events;
        $return['attendee_avatar'] = $attendee_avatar_html;
        die(json_encode($return));
    }
}
if (!function_exists('show_map_pop_cb')) {
    function show_map_pop_cb()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_REQUEST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        if (isset($_REQUEST)) {
            $LPpostID    =   sanitize_text_field($_REQUEST['LPpostID']);
            $cats   =   get_the_terms($LPpostID, 'listing-category');
            $lp_cat_map_pin = get_template_directory_uri() . '/assets/images/pins/lp-logo.png';
            if ($cats) {
                $counter    =   1;
                foreach ($cats as $cat) {
                    if ($counter == 1) {
                        $cat_img = listing_get_tax_meta($cat->term_id, 'category', 'image');
                        if (!empty($cat_img)) {
                            $lp_cat_map_pin =   $cat_img;
                        }
                    }
                }
            }
        ?>
            <div id="quickmap<?php echo esc_attr($LPpostID); ?>" data-marker-src="<?php echo wp_kses_post($lp_cat_map_pin); ?>" class="quickmap"></div>
        <?php
        }
        die();
    }
}
add_action('wp_ajax_show_map_pop_cb', 'show_map_pop_cb');
add_action('wp_ajax_nopriv_show_map_pop_cb', 'show_map_pop_cb');
if (!function_exists('ajax_response_markup')) {
    function ajax_response_markup($returnData = false)
    {
        if (empty($returnData)) {
        ?>
            <div class="lp-notifaction-area lp-notifaction-error" data-error-msg="<?php echo esc_html__('Something went wrong!', 'listingpro'); ?>">
                <div class="lp-notifaction-area-outer">
                    <div class="lp-notifi-icons"><img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAE2SURBVFhH7dhBaoQwFMZxoZu5w5ygPc7AlF6gF5gLtbNpwVVn7LKQMG4b3c9ZCp1E3jdEEI1JnnGRP7h5Iv4wKmiRy+U8qkT7Wkn1VpblA43Yqn7abSWb+luqRxpNZ3D6oP+zUO+cSIPT57jqc/1p4I7G0xmUwXEibdxJ/j7T2D1OZDAOcSD7y9ruaexfTGR0HIqBZMOhECQ7DvkgF8OhOcjFccgFmQyHxpDJcWgIuRoc6iFl87kqHOqunFQfBtltQr3QrnVkLWsHxHLT7rTZ95y5cvflXgNy6IHo3ZNCHZMhx55WQh6TIV1eJcmQLji0OHIODi2G9MEhdmQIDrEhY+BQdGRMHIqG5MChYKSNC/puHSkIqQ+qOXGoh5TqQOPpvi7N06x/JQF1SI0TQmxolMvl3CuKG6LJpCW33jxQAAAAAElFTkSuQmCC"></div>
                    <div class="lp-notifaction-inner">
                        <h4></h4>
                        <p></p>
                    </div>
                </div>
            </div>
        <?php
        } else {
            /* data return */
            return '
				<div class="lp-notifaction-area lp-notifaction-error" data-error-msg="' . esc_html__('Something went wrong!', 'listingpro') . '">
				<div class="lp-notifaction-area-outer">
					<div class="lp-notifi-icons"><img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAE2SURBVFhH7dhBaoQwFMZxoZu5w5ygPc7AlF6gF5gLtbNpwVVn7LKQMG4b3c9ZCp1E3jdEEI1JnnGRP7h5Iv4wKmiRy+U8qkT7Wkn1VpblA43Yqn7abSWb+luqRxpNZ3D6oP+zUO+cSIPT57jqc/1p4I7G0xmUwXEibdxJ/j7T2D1OZDAOcSD7y9ruaexfTGR0HIqBZMOhECQ7DvkgF8OhOcjFccgFmQyHxpDJcWgIuRoc6iFl87kqHOqunFQfBtltQr3QrnVkLWsHxHLT7rTZ95y5cvflXgNy6IHo3ZNCHZMhx55WQh6TIV1eJcmQLji0OHIODi2G9MEhdmQIDrEhY+BQdGRMHIqG5MChYKSNC/puHSkIqQ+qOXGoh5TqQOPpvi7N06x/JQF1SI0TQmxolMvl3CuKG6LJpCW33jxQAAAAAElFTkSuQmCC"></div>
					<div class="lp-notifaction-inner">
						<h4></h4>
						<p></p>
					</div>
				</div>
			</div>
			';
        }
    }
}
/*  */
add_action('publish_to_trash', 'delete_events_permanently');
if (!function_exists('delete_events_permanently')) {
    function delete_events_permanently($post)
    {
        if ($post->post_type != 'events') {
            return false;
        }
        if (!current_user_can('delete_post', $post->ID)) {
            return false;
        }
        $listing_id =   get_post_meta($post->ID, 'event-lsiting-id', true);
        delete_post_meta($listing_id, 'event_id');
        wp_delete_post($post->ID, true);
    }
}
add_action('wp_ajax_select2_ajax_dashbaord_listing_camp', 'select2_ajax_dashbaord_listing_camp');
if (!function_exists('select2_ajax_dashbaord_listing_camp')) {
    function select2_ajax_dashbaord_listing_camp()
    {
        $return = array();
        if (is_user_logged_in()) {
            $user_id        =   get_current_user_id();
            $search_results = new WP_Query(array(
                's' => esc_html($_GET['q']),
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => 50,
                'post_type' => 'listing',
                'author' => $user_id,
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'campaign_status',
                        'value'   => 'active',
                        'compare' => '!=',
                    ),
                    array(
                        'key'     => 'campaign_status',
                        'compare' => 'NOT EXISTS',
                    ),
                )
            ));
            if ($search_results->have_posts()) :
                while ($search_results->have_posts()) : $search_results->the_post();
                    // shorten the title a little
                    $title = (mb_strlen($search_results->post->post_title) > 50) ? mb_substr($search_results->post->post_title, 0, 49) . '...' : $search_results->post->post_title;
                    $return[] = array($search_results->post->ID, $title); // array( Post ID, Post Title )
                endwhile;
            endif;
        }
        echo json_encode($return);
        die;
    }
}
/* Time format function  */
if (!function_exists('listing_time_format')) {
    function listing_time_format($displayTIme = null, $inputValue = null)
    {
        $newTimedisplay = '';
        $newTimeinput = '';
        global $listingpro_options;
        $format  = isset($listingpro_options['timing_option']) ? $listingpro_options['timing_option'] : '';
        if (!empty($displayTIme)) {
            $displayTIme = str_replace(' ', '', $displayTIme);
            $displayTIme = strtotime($displayTIme);
            if (!empty($format) && $format == '24') {
                $newTimedisplay = date("H:i", $displayTIme);
            } else {
                $newTimedisplay = date('h:i A', $displayTIme);
            }
            return $newTimedisplay;
        } elseif (!empty($inputValue)) {
            $inputValue = strtotime($inputValue);
            if (!empty($format) && $format == '24') {
                $newTimeinput = date("H:i", $inputValue);
            } else {
                $newTimeinput = date('h:ia', $inputValue);
            }
            return $newTimeinput;
        }
    }
}
if (!function_exists('del_menu_data_by_user')) {
    function del_menu_data_by_user($user_id, $del_type, $key)
    {
        $m_args =   array(
            'post_type' => 'listing',
            'fields' => 'ids',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'author' => $user_id,
            'meta_key' => 'lp-listing-menu',
            'meta_compare' => 'EXISTS'
        );
        $menus_array  = array();
        $m_listings             =   new WP_Query($m_args);
        if ($m_listings->have_posts()) {
            foreach ($m_listings->posts as $lid) {
                $lp_listing_menus   =   get_post_meta($lid, 'lp-listing-menu', true);
                if ($del_type == 'type') {
                    unset($lp_listing_menus[$key]);
                } elseif ($del_type == 'group') {
                    foreach ($lp_listing_menus as $menu_type => $menu_groups_arr) {
                        foreach ($menu_groups_arr as $k => $v) {
                            if ($k == $key) {
                                unset($lp_listing_menus[$menu_type][$key]);
                                if (count($lp_listing_menus[$menu_type]) == 0) {
                                    unset($lp_listing_menus[$menu_type]);
                                }
                            }
                        }
                    }
                }
                if (count($lp_listing_menus) == 0) {
                    delete_post_meta($lid, 'lp-listing-menu');
                } else {
                    update_post_meta($lid, 'lp-listing-menu', $lp_listing_menus);
                }
                $menus_array[$lid]   =   $lp_listing_menus;
            }
        }
    }
}
add_action('wp_ajax_ajax_search_child_cats', 'ajax_search_child_cats');
add_action('wp_ajax_nopriv_ajax_search_child_cats', 'ajax_search_child_cats');
if (!function_exists('ajax_search_child_cats')) {
    function ajax_search_child_cats()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        global $listingpro_options;
        $sub_cats_loc  = isset($listingpro_options['lp_listing_sub_cats_lcation']) ? $listingpro_options['lp_listing_sub_cats_lcation'] : '';
        $col_grid_class =   'col-grid-5';
        if ($sub_cats_loc == 'content') {
            $col_grid_class =   'col-grid-3';
        }
        $return =   array();
        $parent_id = sanitize_text_field($_POST['parent_id']);
        $output =   '';
        $parent_term    =   get_term($parent_id, 'listing-category');
        $term_name  =   $parent_term->name;
        $child_cats =   get_terms(
            'listing-category',
            array(
                'hide_empty' => false,
                'parent' => $parent_id
            )
        );
        if (empty($parent_id)) {
            $child_cats =   '';
        }
        if (empty($child_cats)) {
            $return['status']    =   'not';
            $return['term_name']   =   $term_name;
            die(json_encode($return));
        } else {
            require_once(THEME_PATH . "/include/aq_resizer.php");
            $output .=  '<div class="lp-child-cats-tax-slider" data-child-loc="' . $sub_cats_loc . '">';
            foreach ($child_cats as $child_cat) :
                $listings_label =   esc_html('Listing', 'listingpro');
                if ($child_cat->count > 1) {
                    $listings_label =   esc_html('Listings', 'listingpro');
                }
                $term_banner    =   get_term_meta($child_cat->term_id, 'lp_category_banner', true);
                $term_link  =   get_term_link($child_cat);
                if (empty($term_banner)) {
                    $banner_url =   'https://placehold.co/246x126';
                } else {
                    $banner_url =    aq_resize($term_banner, '246', '126', true, true, true);
                }
                $output .=  '<div class="' . $col_grid_class . ' lp-child-cats-tax-wrap">';
                $output .=  '   <div class="lp-child-cats-tax-inner">';
                $output .=  '       <div class="lp-child-cat-tax-thumb"><img src="' . $banner_url . '" alt="' . $child_cat->name . '"></div>';
                $output .=  '       <div class="lp-child-cat-tax-name">';
                $output .=  '           <a href="' . $term_link . '">' . $child_cat->name;
                $output .=  '                <span>' . $child_cat->count . ' ' . $listings_label . '</span>';
                $output .=  '           </a>';
                $output .=  '       </div>';
                $output .=  '   </div>';
                $output .=  '</div>';
            endforeach;
            $output .=  '</div>';

            if (empty($term_name)) {
                $term_name = esc_html__('All Categories', 'listingpro');
            }

            $return['status']   =   'found';
            $return['child_cats']   =   $output;
            $return['term_name']   =   $term_name;
            die(json_encode($return));
        }
    }
}
/* ================review_rating_color_class=============== */
if (!function_exists('review_rating_color_class')) {
    function review_rating_color_class($rating_val)
    {
        $rating_color_class =   '';
        if ($rating_val < 1) {
            $rating_color_class =   'lp-star-worst';
        } else if ($rating_val >= 1 && $rating_val < 2) {
            $rating_color_class =   'lp-star-bad';
        } else if ($rating_val >= 2 && $rating_val < 3.5) {
            $rating_color_class =   'lp-star-satisfactory';
        } else if ($rating_val >= 3.5 && $rating_val <= 5) {
            $rating_color_class =   'lp-star-good';
        }
        return $rating_color_class;
    }
}
add_action('wp_ajax_send_attendees_msg', 'send_attendees_msg');
if (!function_exists('send_attendees_msg')) {
    function send_attendees_msg()
    {
        if (isset($_REQUEST)) {
            $uid    =   sanitize_text_field($_POST['uid']);
            $msg    =   sanitize_text_field($_POST['msgContent']);
            $ids    =   $_POST['attendees'];
            $return =   array();
            if (is_user_logged_in()) {
                if ($uid == get_current_user_id()) {
                    $mail_headers   =   array();
                    $mail_headers[] = 'Content-Type: text/html; charset=UTF-8';
                    foreach ($ids as $attendee_id) {
                        $attendee_data  =   get_userdata($attendee_id);
                        $user_email =   $attendee_data->user_email;
                        $username   =   $attendee_data->user_login;
                        $emails_arr[$username]   =   $user_email;
                        $subject        =   esc_html__('Event Message', 'listingpro');
                        $to             =   $user_email;
                        lp_mail_headers_append();
                        LP_send_mail($to, $subject, $msg, $mail_headers);
                        lp_mail_headers_remove();
                    }
                    die(json_encode($return));
                }
            }
        }
    }
}
add_action('wp_ajax_get_event_calender_data', 'get_event_calender_data');
add_action('wp_ajax_nopriv_get_event_calender_data', 'get_event_calender_data');
if (!function_exists('get_event_calender_data')) {
    function get_event_calender_data()
    {
        if (isset($_POST)) {
            $return =   array();
            $targetDateM    =   sanitize_text_field($_POST['targetDateM']);
            $targetDateY    =   sanitize_text_field($_POST['targetDateY']);
            $targetAction   =   sanitize_text_field($_POST['targetAction']);
            $currentMN      =   sanitize_text_field($_POST['currentMN']);
            $currentYN      =   sanitize_text_field($_POST['currentYN']);
            $cTypee         =   sanitize_text_field($_POST['cTypee']);
            $per_page       =   sanitize_text_field($_POST['per_page']);
            if ($targetAction == 'nm') {
                $currentCY      =   date_i18n("Y", strtotime($currentYN . '-' . $currentMN . "-01" . " +1 month "));
            } else {
                $currentCY      =   date_i18n("Y", strtotime($currentYN . '-' . $currentMN . "-01" . " -1 month "));
            }
            $disabled_prev  =   '';
            $next_month_n       =   date_i18n("m", strtotime($targetDateY . '-' . $targetDateM . "-01" . " +1 month "));
            $next_month_na      =   get_month_name($next_month_n);
            $next_month_na_i    =   date_i18n("F", strtotime($targetDateY . '-' . $targetDateM . "-01" . " +1 month "));
            $prev_month_str     =   strtotime($targetDateY . '-' . $targetDateM . "-01" . " -1 month ");
            $prev_month_n       =   date_i18n("m", strtotime($targetDateY . '-' . $targetDateM . "-01" . " -1 month "));
            $prev_month_na      =   get_month_name($prev_month_n);
            $prev_month_na_i    =   date_i18n("F", strtotime($targetDateY . '-' . $targetDateM . "-01" . " -1 month "));
            $prev_month_y   =   date_i18n("Y", strtotime($targetDateY . '-' . $targetDateM . "-01" . " -1 month "));
            $new_year       =   date_i18n("Y", strtotime($targetDateY . '-' . $targetDateM . "-01" . " +1 month "));
            if ($prev_month_str < strtotime("now")) {
                //$disabled_prev  =   'disabled';
                $disabled_prev  =   '';
            }
            $next_month_markup  =   '<span class="get-npm btn-nm" data-npmnum="' . $next_month_n . ',' . $new_year . '">' . $next_month_na_i . '</span><i class="fa fa-angle-right" aria-hidden="true"></i>';
            $prev_month_markup  =   '<i class="fa fa-angle-left" aria-hidden="true"></i> <span class="get-npm btn-pm ' . $disabled_prev . '" data-npmnum="' . $prev_month_n . ',' . $prev_month_y . '">' . $prev_month_na_i . '</span>';
            $curr_month_markup  =   '<span class="cc-month" data-cmn="' . $targetDateM . ',' . $currentCY . '">' . date_i18n("F", strtotime($targetDateY . '-' . $targetDateM . "-01")) . ' ' . $currentCY . '</span>';
            $date_for_caldener  =   strtotime(sprintf('%s-%s-01', $targetDateY, $targetDateM));
            if ($cTypee == 'weekly') {
                $calender_markup    =   render_event_calender_weekly($date_for_caldener);
            } else {
                $calender_markup    =   render_event_calender_monthly($date_for_caldener, $cTypee, $per_page);
            }
            $return['status']               =   'success';
            $return['next_mon_markup']      =   $next_month_markup;
            $return['prev_mon_markup']      =   $prev_month_markup;
            $return['curr_mon_markup']      =   $curr_month_markup;
            $return['calender_markup']      =   $calender_markup;
            die(json_encode($return));
        }
    }
}
if (!function_exists('get_month_name')) {
    function get_month_name($number)
    {
        $dateObj   = DateTime::createFromFormat('!m', $number);
        $monthName = $dateObj->format('F');
        return $monthName;
    }
}
if (!function_exists('has_event_on_day')) {
    function has_event_on_day($timeStr, $check_arr)
    {
        $return =   'has-no-events';
        if (!empty($timeStr) && in_array($timeStr, $check_arr)) {
            $return =   'has-events';
        }
        return $return;
    }
}
if (!function_exists('render_event_calender_monthly')) {
    function render_event_calender_monthly($date, $calender_type, $per_page)
    {
        $month = date_i18n('m', $date);
        $year = date_i18n('Y', $date);
        $first_day_timestapm    =   strtotime('first day of ' . date("$year-$month"));
        $last_day_timestapm     =   strtotime('last day of ' . date("$year-$month"));
        $today_timestamp        =   strtotime("-1 day");
        $daysInMonth = cal_days_in_month(0, $month, $year);
        $timestamp = strtotime('next Sunday');
        $weekDays = array();
        for ($i = 0; $i < 7; $i++) {
            $weekDays[] = date_i18n('D', $timestamp);
            $timestamp = strtotime('+1 day', $timestamp);
        }
        $blank = date('w', strtotime("{$year}-{$month}-01"));
        $today_day  =   date_i18n('d');
        if ($per_page == 'all') {
            $per_page   =   -1;
        }
        $events_args    =   array(
            'post_type' => 'events',
            'post_status' => 'publish',
        );
        if ($calender_type == 'list') {
            $events_args['orderby'] =   'meta_value_num';
            $events_args['order']   =   'ASC';
        }
        /*
        if( $date > strtotime( 'first day of ' . date( 'F Y')) )
        {
            $events_args['meta_query']  =   array(
                'relation' => 'AND',
                array(
                    'key'     => 'event-date',
                    'value'   => array( $first_day_timestapm, $last_day_timestapm ),
                    'type'    => 'numeric',
                    'compare' => 'BETWEEN',
                ),
                array(
                    'key'     => 'event-date-e',
                    'value'   => $last_day_timestapm,
                    'type'    => 'numeric',
                    'compare' => '<=',
                ),
            );
        }
        else
        {
            $events_args['meta_query']  =   array(
                'relation' => 'OR',
                array(
                    'key'     => 'event-date',
                    'value'   => array( $today_timestamp, $last_day_timestapm ),
                    'type'    => 'numeric',
                    'compare' => 'BETWEEN',
                ),
                array(
                    'key'     => 'event-date-e',
                    'value'   => $last_day_timestapm,
                    'type'    => 'numeric',
                    'compare' => '<=',
                ),
            );
        }*/
        $events_args['meta_query']  =   array(
            array(
                'key'     => 'event-date',
                'value'   => array($first_day_timestapm, $last_day_timestapm),
                'type'    => 'numeric',
                'compare' => 'BETWEEN',
            ),
        );
        $events_array       =   array();
        $event_check_arr    =   array();
        $get_events =   new WP_Query($events_args);
        if ($get_events->have_posts()) : while ($get_events->have_posts()) : $get_events->the_post();
                global $post;
                $event_id   =   $post->ID;
                $timeNow = strtotime("-1 day");
                $eDate      =   get_post_meta($event_id, 'event-date', true);
                $event_date_end = get_post_meta($event_id, 'event-date-e', true);
                $date_of_event = date('d-m-Y', $eDate);
                $eDate = strtotime($date_of_event);
                $event_check_arr[] =  $eDate;
                $listing_id = get_post_meta($event_id, 'event-lsiting-id', true);
                $event_time =   get_post_meta($event_id, 'event-time', true);
                $event_loc =   get_post_meta($event_id, 'event-loc', true);
                $event_lat =   get_post_meta($event_id, 'event-lat', true);
                $event_lon =   get_post_meta($event_id, 'event-lon', true);
                $event_ticket_url =   get_post_meta($event_id, 'ticket-url', true);
                $event_img =   get_post_meta($event_id, 'event-img', true);
                $event_utilities =   get_post_meta($event_id, 'event-utilities', true);
                $attending_users    =   get_post_meta($event_id, 'attending-users', true);
                $lp_map_pin = lp_theme_option_url('lp_map_pin');
                if (empty($event_img)) {
                    if (has_post_thumbnail($event_id)) {
                        $event_img  =   get_the_post_thumbnail_url($event_id, 'thumbnail');
                    } else {
                        $event_img  =   'https://placehold.co/140';
                    }
                } else {
                    $event_img_id   =   get_image_id_by_url($event_img);
                    $event_img_arr = wp_get_attachment_image_src($event_img_id, 'thumbnail');
                    $event_img  =   $event_img_arr[0];
                }
                $totalposts  =   ceil($get_events->found_posts);
                if ($per_page == -1) {
                    $per_page   =  $totalposts;
                }
                $events_array[$eDate . '-' . $post->ID]   =   array(
                    'map_pin'    =>  $lp_map_pin,
                    'listing_id' => $listing_id,
                    'event_ticket_url' => $event_ticket_url,
                    'event_lon' => $event_lon,
                    'event_lat' => $event_lat,
                    'event_loc' => $event_loc,
                    'event_time' => $event_time,
                    'event_img' => $event_img,
                    'event_title' => get_the_title(),
                    'event_content' => $post->post_content,
                    'event_url' => get_permalink(),
                    'event_attendees' => $attending_users,
                    'event_id' => $event_id,
                    'event_utilities' => $event_utilities
                );
            endwhile;
            wp_reset_postdata();
        endif;
        ob_start();
        ?>
        <?php
        if ($calender_type == 'modern') {
        ?>
            <div class="col-md-6 ">
                <div class="modern-calender-inner-right">
                    <div class="row">
                        <div class="calender-header-moderen-date2 col-md-6">
                            <div class="today-event-date-container">
                                <span class="close-active-box"><i class="fa fa-times" aria-hidden="true"></i></span>
                                <p><?php echo date_i18n('F d'); ?> <span><?php echo date_i18n('l'); ?></span></p>
                            </div>
                        </div>
                        <!--                        <div class="calender-header-switcher2 col-md-6 text-right">-->
                        <!--                            <ul class="clearfix">-->
                        <!--                                <li><i class="fa fa-map-marker" aria-hidden="true"></i></li>-->
                        <!--                                <li class="active"><i class="fa fa-list-ul" aria-hidden="true"></i></li>-->
                        <!--                            </ul>-->
                        <!--                        </div>-->
                    </div>
                    <?php
                    foreach ($events_array as $k => $event_data) {
                        $keyArr     =   explode('-', $k);
                        $month_day  =   date('j', $keyArr[0]);
                    ?>
                        <div <?php event_calender_map_atts($event_data); ?> class="events-by-day-wrap" data-ebdt="ebdt-<?php echo esc_attr($month_day); ?>">
                            <div class="event-by-day-wrap clearfix">
                                <?php
                                if (!empty($event_data['event_img'])) {
                                ?>
                                    <div class="event-img">
                                        <img alt="image" src="<?php echo esc_url($event_data['event_img']); ?>">
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="event-by-day-content-wrap">
                                    <p class="event-by-day-content-wrap-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo esc_attr($event_data['event_time']); ?></p>
                                    <a href="<?php echo esc_url($event_data['event_url']); ?>" class="event-by-day-content-wrap-time-title"><?php echo esc_attr($event_data['event_title']); ?></a>
                                    <p><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_attr($event_data['event_loc']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="event-calander-moder-map">
                        <div id="map" class="event-calander-map"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 background-white ">
                <div class="modern-calender-inner-left">
                <?php
            }
                ?>
                <?php
                if ($calender_type != 'list') {
                ?>
                    <div class="week-days-wrap">
                        <?php foreach ($weekDays as $key => $weekDay) : ?>
                            <div class="week-day-box"><?php echo esc_attr($weekDay); ?></div>
                        <?php endforeach ?>
                        <div class="clearfix"></div>
                    </div>
                    <div class="month-dates-wrap" style="margin: 20px 0;">
                        <div class="week-days-dates-row">
                            <?php
                            $row_counter = 0;
                            ?>
                            <?php for ($i = 0; $i < $blank; $i++) : ?>
                                <div class="week-day-date-box"><span></span></div>
                            <?php endfor; ?>
                            <?php
                            for (
                                $i = 1;
                                $i <= $daysInMonth;
                                $i++
                            ) :
                                $dataStr = strtotime($year . '-' . $month . '-' . $i);
                                $has_event_class = has_event_on_day($dataStr, $event_check_arr);
                            ?>
                                <?php if ($today_day == $i) : ?>
                                    <div class="week-day-date-box <?php echo esc_attr($has_event_class); ?>" data-todaytime="<?php echo date_i18n('d-F-l', $dataStr); ?>" data-timestr="<?php echo esc_attr($dataStr); ?>" data-ewt="ebdt-<?php echo esc_attr($i); ?>">
                                        <span><?php echo esc_attr($i); ?></span>
                                    </div>
                                <?php else : ?>
                                    <div class="week-day-date-box <?php echo esc_attr($has_event_class); ?>" data-todaytime="<?php echo date_i18n('d-F-l', $dataStr); ?>" data-timestr="<?php echo esc_attr($dataStr); ?>" data-ewt="ebdt-<?php echo esc_attr($i); ?>"><span><?php echo esc_attr($i); ?></span></div>
                                <?php endif; ?>
                                <?php if (($i + $blank) % 7 == 0) :
                                    $row_counter++; ?>
                                    <div class="clearfix"></div>
                                    <?php
                                    if ($calender_type == 'classic') {
                                    ?>
                                        <div class="week-days-dates-events-row">
                                            <?php
                                            $week_end_date = $row_counter * 7 - $blank;
                                            $week_start_date = 1;
                                            if ($row_counter != 1) {
                                                $week_start_date = $week_end_date + 1 - 7;
                                            }
                                            for ($ed = $week_start_date; $ed <= $week_end_date; $ed++) {
                                                $dataStr2 = strtotime($year . '-' . $month . '-' . $ed);
                                            ?>
                                                <div class="events-by-day-wrap" data-ebdt="ebdt-<?php echo esc_attr($ed); ?>">
                                                    <div class="clearfix events-by-day-wrap-classic-con">
                                                        <div class="today-event-date-container col-md-6 padding-left-0">
                                                            <p class="margin-bottom-0 margin-top-10">Events For <span><?php echo date_i18n('F', $date) . ' ' . $ed; ?></span><?php //echo esc_attr( $dataStr2 ); 
                                                                                                                                                                                ?> </p>
                                                        </div>
                                                        <div class="calender-header-switcher col-md-5 padding-right-0 pull-left">
                                                            <ul>
                                                                <li class="col-md-6 text-center show-calander-map show-calander-map-ebdt-<?php echo esc_attr($ed); ?>" data-targetdate="<?php echo esc_attr($ed); ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_html__('Map View', 'listingpro'); ?></li>
                                                                <li class="col-md-6 text-center active show-calader-list show-calader-list-ebdt-<?php echo esc_attr($ed); ?>" data-targetdate="<?php echo esc_attr($ed); ?>"><i class="fa fa-list-ul" aria-hidden="true"></i> <?php echo esc_html__('List View', 'listingpro'); ?></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-1 text-center lp-close-active-box-inner pull-right  padding-right-0"><span class="close-active-box"><i class="fa fa-times" aria-hidden="true"></i> Close</span></div>
                                                    </div>
                                                    <?php
                                                    foreach ($events_array as $k => $event_data) {
                                                        if (strpos($k, $dataStr2 . '-') !== false) {
                                                    ?>
                                                            <div <?php event_calender_map_atts($event_data); ?> class="event-by-day-wrap clearfix">
                                                                <?php
                                                                if (!empty($event_data['event_img'])) {
                                                                ?>
                                                                    <div class="event-img">
                                                                        <img alt="image" src="<?php echo esc_url($event_data['event_img']); ?>">
                                                                    </div>
                                                                <?php
                                                                }
                                                                ?>
                                                                <div class="event-by-going-wrap pull-right text-right">
                                                                    <?php
                                                                    if (!isset($event_data['event_utilities']['guests']) || $event_data['event_utilities']['guests'] == 'yes') {
                                                                        display_event_calender_attendees($event_data['event_attendees'], $event_data['event_id']);
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <div class="event-by-day-content-wrap pull-right">
                                                                    <p class="event-by-day-content-wrap-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo esc_attr($event_data['event_time']); ?></p>
                                                                    <a href="<?php echo esc_url($event_data['event_url']); ?>" class="event-by-day-content-wrap-time-title margin-bottom-10 margin-top-0"><?php echo esc_attr($event_data['event_title']); ?></a>
                                                                    <p class="classic-event-by-day-content-wrap-time margin-bottom-10"><?php echo mb_substr($event_data['event_content'], 0, 150); ?></p>
                                                                    <p><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_attr($event_data['event_loc']); ?></p>
                                                                </div>
                                                            </div>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                    <div class="event-calender-classic-map classic-map-<?php echo esc_attr($ed); ?>"></div>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    <?php
                                    }
                                    ?>
                        </div>
                        <div class="week-days-dates-row">
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php for ($i = 0; ($i + $blank + $daysInMonth) % 7 != 0; $i++) : ?>
                        <div class="week-day-date-box"><span></span></div>
                    <?php endfor; ?>
                    <div class="clearfix"></div>
                    <?php
                    if ($calender_type == 'classic') {
                        $days_processed = $row_counter * 7 - $blank;
                        $days_remaining = $daysInMonth - $days_processed;
                        if ($days_remaining > 0) :
                    ?>
                            <div class="week-days-dates-events-row">
                                <?php
                                for ($xd = $days_processed + 1; $xd <= $daysInMonth; $xd++) {
                                    $dataStr3 = strtotime($year . '-' . $month . '-' . $xd);
                                ?>
                                    <div class="events-by-day-wrap" data-ebdt="ebdt-<?php echo esc_attr($xd); ?>">
                                        <div class="clearfix events-by-day-wrap-classic-con">
                                            <div class="today-event-date-container col-md-6 padding-left-0">
                                                <span class="close-active-box"><i class="fa fa-times" aria-hidden="true"></i></span>
                                                <p class="margin-bottom-0 margin-top-10"><?php echo esc_html__('Events For', 'listingpro'); ?> <span><?php echo date_i18n('F', $date) . ' ' . $xd; ?></span><?php //echo esc_attr( $dataStr2 ); 
                                                                                                                                                                                                            ?> </p>
                                            </div>
                                            <div class="calender-header-switcher col-md-6 padding-right-0">
                                                <ul>
                                                    <li class="col-md-6 text-center show-calander-map show-calander-map-ebdt-<?php echo esc_attr($ed); ?>" data-targetdate="<?php echo esc_attr($ed); ?>"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_html__('Map View', 'listingpro'); ?></li>
                                                    <li class="col-md-6 text-center active show-calader-list show-calader-list-ebdt-<?php echo esc_attr($ed); ?>" data-targetdate="<?php echo esc_attr($ed); ?>"><i class="fa fa-list-ul" aria-hidden="true"></i> <?php echo esc_html__('List View', 'listingpro'); ?></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <?php
                                        foreach ($events_array as $k => $event_data) {
                                            if (strpos($k, $dataStr3 . '-') !== false) {
                                        ?>
                                                <div <?php event_calender_map_atts($event_data); ?> class="event-by-day-wrap clearfix">
                                                    <?php
                                                    if (!empty($event_data['event_img'])) {
                                                    ?>
                                                        <div class="event-img">
                                                            <img alt="image" src="<?php echo esc_url($event_data['event_img']); ?>">
                                                        </div>
                                                    <?php
                                                    }
                                                    ?>
                                                    <div class="event-by-going-wrap pull-right text-right">
                                                        <?php
                                                        if (!isset($event_data['event_utilities']['guests']) || $event_data['event_utilities']['guests'] == 'yes') {
                                                            display_event_calender_attendees($event_data['event_attendees'], $event_data['event_id']);
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="event-by-day-content-wrap pull-right">
                                                        <p class="event-by-day-content-wrap-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo esc_attr($event_data['event_time']); ?></p>
                                                        <a href="<?php echo esc_attr($event_data['event_url']); ?>" class="event-by-day-content-wrap-time-title margin-bottom-10 margin-top-0"><?php echo esc_attr($event_data['event_title']); ?></a>
                                                        <p class="classic-event-by-day-content-wrap-time margin-bottom-10"><?php echo mb_substr($event_data['event_content'], 0, 150); ?>...</p>
                                                        <p><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_attr($event_data['event_loc']); ?></p>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <div class="event-calender-classic-map classic-map-<?php echo esc_attr($xd); ?>"></div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    <?php
                    }
                    ?>
                        </div>
                        <?php
                        if ($calender_type == 'classic2') {
                        ?>
                            <div class="event-calander-classic2">
                                <div class="clearfix events-by-day-wrap-classic-con">
                                    <div class="today-event-date-container col-md-6 padding-left-0">
                                        <p class="margin-bottom-0 margin-top-10"><?php echo esc_html__('Events For', 'listingpro'); ?> <span><?php echo date_i18n('F d'); ?></span> </p>
                                    </div>
                                    <div class="calender-header-switcher col-md-5 padding-right-0" style="background-color: #e1e1e1;">
                                        <ul>
                                            <li class="col-md-6 text-center show-calander-map"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_html__('Map View', 'listingpro'); ?></li>
                                            <li class="col-md-6 text-center active show-calader-list"><i class="fa fa-list-ul" aria-hidden="true"></i> <?php echo esc_html__('List View', 'listingpro'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-1 text-center lp-close-active-box-inner pull-right  padding-right-0"><span class="close-active-box"><i class="fa fa-times" aria-hidden="true"></i> Close</span></div>
                                </div>
                                <?php
                                foreach ($events_array as $k => $event_data) {
                                    $keyArr     =   explode('-', $k);
                                    $month_day  =   date('j', $keyArr[0]);
                                ?>
                                    <div <?php event_calender_map_atts($event_data); ?> data-ebdt="ebdt-<?php echo esc_attr($month_day); ?>" class="event-by-day-wrap clearfix">
                                        <?php
                                        if (!empty($event_data['event_img'])) {
                                        ?>
                                            <div class="event-img">
                                                <img alt="image" src="<?php echo esc_attr($event_data['event_img']); ?>">
                                            </div>
                                        <?php
                                        }
                                        ?>
                                        <div class="event-by-going-wrap pull-right text-right">
                                            <?php
                                            if (!isset($event_data['event_utilities']['guests']) || $event_data['event_utilities']['guests'] == 'yes') {
                                                display_event_calender_attendees($event_data['event_attendees'], $event_data['event_id']);
                                            }
                                            ?>
                                        </div>
                                        <div class="event-by-day-content-wrap pull-right">
                                            <p class="event-by-day-content-wrap-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo esc_attr($event_data['event_time']); ?></p>
                                            <a href="<?php echo esc_attr($event_data['event_url']); ?>" class="event-by-day-content-wrap-time-title margin-bottom-10 margin-top-0"><?php echo esc_attr($event_data['event_title']); ?></a>
                                            <p class="classic-event-by-day-content-wrap-time margin-bottom-10"><?php echo mb_substr($event_data['event_content'], 0, 150); ?>...</p>
                                            <p><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_attr($event_data['event_loc']); ?></p>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="event-calender-classic-map">
                                    <div id="map" class="event-calander-map"></div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                <?php
                }
                ?>
                <?php
                if ($calender_type == 'modern') {
                ?>
                </div>
            </div>
            <div class="clearfix"></div>
        <?php
                }
        ?>
        <?php
        if ($calender_type == 'list') {
        ?>
            <div class="list-calendar-inner-wrap">
                <div class="clearfix events-by-day-wrap-classic-con">
                    <div class="today-event-date-container col-md-6 padding-left-0">
                        <p class="margin-bottom-0 margin-top-10"><?php echo esc_html__('Events For', 'listingpro'); ?> <span><?php echo date_i18n('F', $date); ?></span><?php //echo esc_attr( $dataStr2 ); 
                                                                                                                                                                        ?> </p>
                    </div>
                    <div class="calender-header-switcher col-md-6 padding-right-0">
                        <ul>
                            <li class="col-md-6 text-center show-calander-map"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_html__('Map View', 'listingpro'); ?></li>
                            <li class="col-md-6 text-center active show-calader-list"><i class="fa fa-list-ul" aria-hidden="true"></i> <?php echo esc_html__('List View', 'listingpro'); ?></li>
                        </ul>
                    </div>
                </div>
                <div class="list-calendar-pager-wrap current-event-page-active">
                    <?php
                    $pager_counter  =   0;
                    foreach ($events_array as $k => $event_data) {
                        $active_event_for_map   =   '';
                        if ($pager_counter < $per_page) {
                            $active_event_for_map   =   'active-event-for-map';
                        }
                        $keyArr     =   explode('-', $k);
                        $month_day  =   date('d', $keyArr[0]);
                        //New Code for update
                        $eDate_start      =   get_post_meta($event_data['event_id'], 'event-date', true);

                    ?>
                        <div <?php event_calender_map_atts($event_data); ?> class="events-by-day-wrap <?php echo esc_attr($active_event_for_map); ?>" data-ebdt="ebdt-<?php echo esc_attr($month_day); ?>">
                            <div class="event-by-day-wrap clearfix">
                                <?php
                                if (!empty($event_data['event_img'])) {
                                ?>
                                    <div class="event-img">
                                        <img alt="image" src="<?php echo esc_attr($event_data['event_img']); ?>">
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="event-by-going-wrap pull-right text-right">
                                    <?php
                                    if (!isset($event_data['event_utilities']['guests']) || $event_data['event_utilities']['guests'] == 'yes') {
                                        display_event_calender_attendees($event_data['event_attendees'], $event_data['event_id']);
                                    }
                                    ?>
                                </div>
                                <div class="event-by-day-content-wrap pull-right">
                                    <p class="event-by-day-content-wrap-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo esc_attr(date_i18n(get_option('date_format'), $eDate_start) . ' ' . $event_data['event_time']); ?></p>
                                    <a href="<?php echo esc_attr($event_data['event_url']); ?>" class="event-by-day-content-wrap-time-title margin-bottom-10 margin-top-0"><?php echo esc_attr($event_data['event_title']); ?></a>
                                    <p class="classic-event-by-day-content-wrap-time margin-bottom-10"><?php echo mb_substr($event_data['event_content'], 0, 150); ?>...</p>
                                    <p><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_attr($event_data['event_loc']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php
                        $pager_counter++;
                        if ($pager_counter % $per_page == 0) {
                            echo '</div><div class="list-calendar-pager-wrap">';
                        }
                    }
                    ?>
                </div>
                <div class="event-calander-list-map">
                    <div id="map" class="event-calander-map"></div>
                </div>
                <?php
                $num_pages  =   ceil($get_events->found_posts / $per_page);
                if ($num_pages && $num_pages > 1) {
                    $pager_counter  =   0;
                ?>
                    <div class="lp-pagination event-pager">
                        <ul class="page-numbers">
                            <?php
                            for ($i = 1; $i <= $num_pages; $i++) {
                                $pager_counter++;
                                $active_class   =   '';
                                if ($pager_counter == 1) {
                                    $active_class   =   'current';
                                }
                                echo '<li><span data-pageno="' . ($i - 1) . '" class="page-numbers ' . $active_class . '">' . $i . '</span></li>';
                            }
                            ?>
                        </ul>
                    </div>
                <?php
                }
                ?>
            </div>
        <?php
        }
        ?>
    <?php
        return ob_get_clean();
    }
}
if (!function_exists('render_event_calender_weekly')) {
    function render_event_calender_weekly($date)
    {
        $month = date_i18n('m', $date);
        $year = date_i18n('Y', $date);
        $first_day_timestapm    =   strtotime('first day of ' . date("$year-$month"));
        $last_day_timestapm     =   strtotime('last day of ' . date("$year-$month"));
        $today_timestamp        =   strtotime("-1 day");
        $daysInMonth = cal_days_in_month(0, $month, $year);
        $timestamp = strtotime('next Sunday');
        $weekDays = array();
        for ($i = 1; $i <= 7; $i++) {
            $weekDays[$i] = date_i18n('D', $timestamp);
            $timestamp = strtotime('+1 day', $timestamp);
        }
        $blank = date('w', strtotime("{$year}-{$month}-01"));
        $today_day  =   date_i18n('d');
        $events_args    =   array(
            'post_type' => 'events',
            'post_status' => 'publish',
            'orderby' =>   'meta_value_num',
            'order'  =>   'ASC'
        );
        if ($date > strtotime('first day of ' . date('F Y'))) {
            $events_args['meta_query']  =   array(
                'relation' => 'OR',
                array(
                    'key'     => 'event-date',
                    'value'   => array($first_day_timestapm, $last_day_timestapm),
                    'type'    => 'numeric',
                    'compare' => 'BETWEEN',
                ),
                array(
                    'key'     => 'event-date-e',
                    'value'   => $last_day_timestapm,
                    'type'    => 'numeric',
                    'compare' => '<=',
                ),
            );
        } else {
            $events_args['meta_query']  =   array(
                'relation' => 'OR',
                array(
                    'key'     => 'event-date',
                    'value'   => array($today_timestamp, $last_day_timestapm),
                    'type'    => 'numeric',
                    'compare' => 'BETWEEN',
                ),
                array(
                    'key'     => 'event-date-e',
                    'value'   => $last_day_timestapm,
                    'type'    => 'numeric',
                    'compare' => '<=',
                ),
            );
        }
        $events_array       =   array();
        $event_check_arr    =   array();
        $get_events =   new WP_Query($events_args);
        if ($get_events->have_posts()) : while ($get_events->have_posts()) : $get_events->the_post();
                global $post;
                $event_id   =   $post->ID;
                $eDate      =   get_post_meta($event_id, 'event-date', true);
                $event_check_arr[]  =   $eDate;
                $listing_id = get_post_meta($event_id, 'event-lsiting-id', true);
                $event_time =   get_post_meta($event_id, 'event-time', true);
                $event_loc =   get_post_meta($event_id, 'event-loc', true);
                $event_lat =   get_post_meta($event_id, 'event-lat', true);
                $event_lon =   get_post_meta($event_id, 'event-lon', true);
                $event_ticket_url =   get_post_meta($event_id, 'ticket-url', true);
                $event_img =   get_post_meta($event_id, 'event-img', true);
                $event_utilities =   get_post_meta($event_id, 'event-utilities', true);
                $attending_users    =   get_post_meta($event_id, 'attending-users', true);
                $lp_map_pin = lp_theme_option_url('lp_map_pin');
                if (empty($event_img)) {
                    if (has_post_thumbnail($event_id)) {
                        $event_img  =   get_the_post_thumbnail_url($event_id, 'thumbnail');
                    } else {
                        $event_img  =   'https://placehold.co/100';
                    }
                } else {
                    $event_img_id   =   get_image_id_by_url($event_img);
                    $event_img_arr = wp_get_attachment_image_src($event_img_id, 'thumbnail');
                    $event_img  =   $event_img_arr[0];
                }
                $events_array[$eDate . '-' . $post->ID]   =   array(
                    'map_pin'    =>  $lp_map_pin,
                    'listing_id' => $listing_id,
                    'event_ticket_url' => $event_ticket_url,
                    'event_lon' => $event_lon,
                    'event_lat' => $event_lat,
                    'event_loc' => $event_loc,
                    'event_time' => $event_time,
                    'event_img' => $event_img,
                    'event_title' => get_the_title(),
                    'event_content' => $post->post_content,
                    'event_date' => $eDate,
                    'event_url' => get_the_permalink(),
                    'event_attendees' => $attending_users,
                    'event_id' => $event_id,
                    'event_utilities' => $event_utilities
                );
            endwhile;
            wp_reset_postdata();
        endif;
        ob_start();
        $weeksNum =   ceil(($blank + $daysInMonth) / 7);
    ?>
        <?php
        for ($i = 1; $i <= $weeksNum; $i++) {
            $next_week_end_date =   (($i + 1) * 7) - $blank;
            $next_week_sta_date =   (($i + 1) * 7) - $blank - 7 + 1;
            $prev_week_end_date =   $next_week_sta_date - 8;
            $prev_week_sta_date =   $prev_week_end_date - 6;
            $prev_week_num      =   $i - 1;
            if ($prev_week_num == 1) {
                $prev_week_sta_date =   1;
            }
            if ($i == $weeksNum - 1) {
                $next_week_end_date  =   $daysInMonth;
            }
            $next_week_end_timestamp =   strtotime($year . '-' . $month . '-' . $next_week_end_date);
            $next_week_sta_timestamp =   strtotime($year . '-' . $month . '-' . $next_week_sta_date);
            $prev_week_end_timestamp =   strtotime($year . '-' . $month . '-' . $prev_week_end_date);
            $prev_week_sta_timestamp =   strtotime($year . '-' . $month . '-' . $prev_week_sta_date);
            $active_week    =   '';
            if ($i == 1) {
                $active_week    =   'active-week';
            }
        ?>
            <div class="week-days-wrap event-week-<?php echo esc_attr($i); ?> <?php echo esc_attr($active_week); ?>">
                <h4 class="week-days-wrap-next-prv text-center">
                    <span class="event-npw" data-weeksta="<?php echo esc_attr($prev_week_sta_timestamp); ?>" data-weekend="<?php echo esc_attr($prev_week_end_timestamp); ?>" data-targetweek="<?php echo intval($i) - 1; ?>"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
                    <?php echo esc_html__('Week ', 'listingpro') . $i; ?>
                    <span class="event-npw" data-weeksta="<?php echo esc_attr($next_week_sta_timestamp); ?>" data-weekend="<?php echo esc_attr($next_week_end_timestamp); ?>" data-targetweek="<?php echo intval($i) + 1; ?>"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
                </h4>
                <div class="clearfix lp-week-days-wrape-inner-container">
                    <?php
                    foreach ($weekDays as $key => $weekDay) :
                        $week_day_date  =   $key + (7 * ($i - 1)) - $blank;
                        $data_timestamp =   '';
                        if ($key > $blank && $i == 1) {
                            $week_day_date  =  $key - $blank;
                        }
                        if ($week_day_date > $daysInMonth || $week_day_date == 0 || $week_day_date < 0) {
                            $week_day_date  =   '';
                        }
                        if (!empty($week_day_date)) {
                            $data_timestamp =   strtotime($year . '-' . $month . '-' . $week_day_date);
                        }
                        $has_event_class = has_event_on_day($data_timestamp, $event_check_arr);
                    ?>
                        <div class="week-day-box <?php echo esc_attr($has_event_class); ?>" data-timestamp="<?php echo esc_attr($data_timestamp); ?>" data-ewt="ebdt-<?php echo esc_attr($week_day_date); ?>">
                            <span class="week-day-name"><?php echo esc_attr($weekDay); ?></span>
                            <span class="week-day-date"><?php echo esc_attr($week_day_date); ?></span>
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php
        }
        ?>
        <div class="week-day-events-wrap">
            <div class="clearfix events-by-day-wrap-classic-con">
                <div class="today-event-date-container col-md-6 padding-left-0">
                    <p class="margin-bottom-0 margin-top-10"><?php echo esc_html__('Events For', 'listingpro'); ?> <span><?php echo date_i18n('F', $date); ?></span><?php //echo esc_attr( $dataStr2 ); 
                                                                                                                                                                    ?> </p>
                </div>
                <div class="calender-header-switcher col-md-6 padding-right-0">
                    <ul>
                        <li class="col-md-6 text-center show-calander-map"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_html__('Map View', 'listingpro'); ?></li>
                        <li class="col-md-6 text-center active show-calader-list"><i class="fa fa-list-ul" aria-hidden="true"></i> <?php echo esc_html__('List View', 'listingpro'); ?></li>
                    </ul>
                </div>
            </div>
            <?php
            foreach ($events_array as $key => $event_data) {
                $event_day  =   date_i18n('j', $event_data['event_date']);
            ?>
                <div <?php event_calender_map_atts($event_data); ?> class="event-by-day-wrap-inner clearfix events-for-day ebdt-<?php echo esc_attr($event_day); ?>" data-targetdate="<?php echo esc_attr($event_day); ?>">
                    <?php
                    if (!empty($event_data['event_img'])) {
                    ?>
                        <div class="event-img">
                            <img alt="image" src="<?php echo esc_attr($event_data['event_img']); ?>">
                            <div class="events-for-day-date ebdt-<?php echo esc_attr($event_day); ?>" data-timestamp="<?php echo esc_attr($event_data['event_date']); ?>">
                                <?php echo date_i18n('d', $event_data['event_date']); ?>
                                <span><?php echo date_i18n('F', $date); ?></span>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="event-by-going-wrap pull-right text-right">
                        <?php
                        if (!isset($event_data['event_utilities']['guests']) || $event_data['event_utilities']['guests'] == 'yes') {
                            display_event_calender_attendees($event_data['event_attendees'], $event_data['event_id']);
                        }
                        ?>
                    </div>
                    <div class="event-by-day-content-wrap pull-right">
                        <p class="event-by-day-content-wrap-time"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo esc_attr($event_data['event_time']); ?></p>
                        <a href="<?php echo esc_url($event_data['event_url']); ?>" class="event-by-day-content-wrap-time-title margin-bottom-10 margin-top-0"><?php echo esc_attr($event_data['event_title']); ?></a>
                        <p class="classic-event-by-day-content-wrap-time margin-bottom-10"><?php echo mb_substr($event_data['event_content'], 0, 150); ?></p>
                        <p><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_attr($event_data['event_loc']); ?></p>
                    </div>
                </div>
            <?php
            }
            ?>
            <div class="event-calender-weekly-map">
                <div id="map" class="event-calander-map"></div>
            </div>
        </div>
    <?php
        return ob_get_clean();
    }
}
if (!function_exists('display_event_calender_attendees')) {
    function display_event_calender_attendees($attendees, $event_id)
    {
        $attendees_count    =   0;
        if (!empty($attendees) && is_array($attendees)) {
            $attendees_count    =   count($attendees);
        }
    ?>
        <p><?php echo esc_attr__('Who is going?', 'listingpro'); ?></p>
        <p><span class="" id="lpec-attendee-count-<?php echo esc_attr($event_id); ?>"><?php echo esc_attr($attendees_count); ?></span> <?php echo esc_html__('people are going', 'listingpro'); ?> </p>
        <ul class="margin-top-10" id="lpec-attendees-avatar-<?php echo esc_attr($event_id); ?>">
            <?php
            if ($attendees_count > 0) {
                foreach ($attendees as $attendee) {
                    $attendee_data  =   get_userdata($attendee);
                    $author_avatar_url = get_user_meta($attendee, "listingpro_author_img_url", true);
                    if (!empty($author_avatar_url)) {
                        $avatar =  $author_avatar_url;
                    } else {
                        $avatar_url = listingpro_get_avatar_url($attendee, $size = '90');
                        $avatar =  $avatar_url;
                    }
            ?>
                    <li id="lpec-attendee-avatar-<?php echo esc_attr($attendee); ?>-<?php echo esc_attr($event_id); ?>">
                        <img src="<?php echo esc_attr($avatar); ?>" alt="<?php echo esc_attr($attendee_data->user_nicename); ?>">
                    </li>
                <?php
                }
            }
            if (!is_user_logged_in()) {
                global $listingpro_options;
                $popup_style  = isset($listingpro_options['login_popup_style']) ? $listingpro_options['login_popup_style'] : '';
                if (!isset($popup_style) || empty($popup_style) || !$popup_style || $popup_style == 'style1') {
                ?>
                    <li class="md-trigger" data-modal="modal-3"><button><i class="fa fa-plus" aria-hidden="true"></i></button></li>
                <?php
                } else {
                ?>
                    <li class="header-login-btn md-trigger app-view-popup-style" data-target="#app-view-login-popup"><button><i class="fa fa-plus" aria-hidden="true"></i></button></li>
                <?php
                }
                ?>
                <?php
            } else {
                $current_uid    =   get_current_user_id();
                if (is_array($attendees) && in_array($current_uid, $attendees)) {
                ?>
                    <li class="attend-event not-going from-lpec" data-uid="<?php echo esc_attr($current_uid); ?>" data-event="<?php echo esc_attr($event_id); ?>"><button><i class="fa fa-times" aria-hidden="true"></i></button></li>
                <?php
                } else {
                ?>
                    <li class="attend-event from-lpec" data-uid="<?php echo esc_attr($current_uid); ?>" data-event="<?php echo esc_attr($event_id); ?>"><button><i class="fa fa-plus" aria-hidden="true"></i></button></li>
            <?php
                }
            }
            ?>
            <ul>
                <?php
            }
        }
        if (!function_exists('event_calender_map_atts')) {
            function event_calender_map_atts($event_data)
            {
                echo 'data-address="' . $event_data['event_loc'] . '" data-posturl="' . $event_data['event_url'] . '" data-title="' . $event_data['event_title'] . '" data-postid="' . $event_data['event_id'] . '" data-lattitue="' . $event_data['event_lat'] . '" data-longitute="' . $event_data['event_lon'] . '"';
            }
        }
        if (!function_exists('get_image_id_by_url')) {
            function get_image_id_by_url($image_url)
            {
                global $wpdb;
                $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url));
                if ($attachment && isset($attachment[0])) {
                    return $attachment[0];
                } else {
                    return '';
                }
            }
        }
        add_action('wp_ajax_listingpro_dashboard_menu_of_listing',        'listingpro_dashboard_menu_of_listing');
        if (!function_exists('listingpro_dashboard_menu_of_listing')) {
            function listingpro_dashboard_menu_of_listing()
            {
                if (isset($_REQUEST)) {
                    $selectedListingID = sanitize_text_field($_REQUEST['selectedListingID']);
                    $current_user = wp_get_current_user();
                    $user_id = $current_user->ID;
                    $menu_types_data        =   get_user_meta($user_id, 'user_menu_types');
                    $menu_types_data        =   isset($menu_types_data[0]) ? $menu_types_data[0] : '';
                    global $listingpro_options;
                    $image_gallery   =   '';
                    $image_gallery_opt  = isset($listingpro_options['menu_gallery_dashoard']) ? $listingpro_options['menu_gallery_dashoard'] : '';
                    if ($image_gallery_opt == 1) {
                        $image_gallery  =   'data-multiple=true';
                    }
                ?>
                    <div class="tab-content lp-tab-content-outer clearfix">
                        <?php
                        $menu_type_counter  =   1;
                        foreach ($menu_types_data as $k => $item) {
                            $menu_type_active   =   '';
                            if ($menu_type_counter == 1) {
                                $menu_type_active   =   'in active';
                            }
                        ?>
                            <div class="tab-pane fade <?php echo esc_attr($menu_type_active); ?>" id="type-<?php echo str_replace(' ', '-', $item['type']); ?>">
                                <?php
                                $lp_listing_menus   =   get_post_meta($selectedListingID, 'lp-listing-menu', true);
                                foreach ($lp_listing_menus as $type => $type_menus) {
                                    if (isset($lp_listing_menus[$item['type']])) {
                                        if ($type == $item['type']) {
                                            foreach ($type_menus as $group => $lp_menuee) {
                                                $lp_menu_k  =   0;
                                                foreach ($lp_menuee as $lp_menu) {
                                                    $menu_id = str_replace(' ', '-', $type) . '_' . str_replace(' ', '-', $group) . '_' . $lp_menu_k . '_' . get_the_ID();
                                                    $lp_menu_k++;
                                                    $menu_price =   '';
                                                    if (isset($lp_menu['mNewPrice']) && !empty($lp_menu['mNewPrice'])) {
                                                        $menu_price =   $lp_menu['mNewPrice'];
                                                    }
                                                    if (!empty($menu_price) && isset($lp_menu['mOldPrice']) && !empty($lp_menu['mOldPrice'])) {
                                                        $menu_price =   $lp_menu['mOldPrice'];
                                                    }
                                ?>
                                                    <div class="lp-menu-close-outer">
                                                        <div class="lp-menu-closed clearfix ">
                                                            <div class="row">
                                                                <div class="col-md-6"><i class="fa fa-check-circle fa-check-circle2" aria-hidden="true"></i>
                                                                    <h4 class="lp-right-side-title"><span><?php echo esc_html__('Item Name', 'listingpro'); ?></span><br><?php echo esc_attr($lp_menu['mTitle']); ?></h4>
                                                                </div>
                                                                <div class="col-md-2"><span><?php echo esc_html__('Group', 'listingpro'); ?></span><br><?php echo esc_attr($group); ?></div>
                                                                <div class="col-md-2 price"><span><?php echo esc_html__('Price', 'listingpro'); ?></span><br><?php echo esc_attr($menu_price); ?></div>
                                                                <div class="col-md-2"><span class="lp-dot-extra-buttons"><i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                                        <ul class="lp-user-menu list-style-none">
                                                                            <li><a class="edit-menu-item" data-menuID="<?php echo esc_attr($menu_id); ?>" data-uid="<?php echo esc_attr($user_id); ?>" href=""><i class="fa fa-pencil" aria-hidden="true"></i><span><?php echo esc_html__('Edit', 'listingpro'); ?></span></a></li>
                                                                            <li><a href="" class="menu-del del-this" data-LID="<?php echo esc_attr($lp_menu['mListing']); ?>" data-targetid="<?php echo esc_attr($menu_id); ?>" data-uid="<?php echo esc_attr($user_id); ?>"><i class="fa fa-trash" aria-hidden="true"></i><span><?php echo esc_html__('Delete', 'listingpro'); ?></span></a></li>
                                                                        </ul>
                                                                    </span></div>
                                                            </div>
                                                        </div>
                                                        <div id="menu-update-<?php echo esc_attr($menu_id); ?>" class="lp-menu-form-outer background-white" style="display: none">
                                                            <div class="lp-menu-form-inner">
                                                                <form class="row">
                                                                    <input value="<?php echo esc_attr($type); ?>" type="hidden" id="menu-type-<?php echo esc_attr($menu_id); ?>" name="menu-type-<?php echo esc_attr($menu_id); ?>">
                                                                    <input type="hidden" value="<?php echo esc_attr($group); ?>" id="menu-group-<?php echo esc_attr($menu_id); ?>" name="menu-group-<?php echo esc_attr($menu_id); ?>">
                                                                    <div class="col-sm-12 margin-top-10">
                                                                        <div class="lp-menu-form-feilds">
                                                                            <div class="row clearfix">
                                                                                <div class="col-md-12">
                                                                                    <div class="row">
                                                                                        <div class="margin-bottom-20 col-md-8">
                                                                                            <label class="lp-dashboard-top-label" for="menu-title-<?php echo esc_attr($menu_id); ?>"><?php echo esc_html__('Menu Item', 'listingpro'); ?></label>
                                                                                            <input name="menu-title-<?php echo esc_attr($menu_id); ?>" id="menu-title-<?php echo esc_attr($menu_id); ?>" type="text" class="form-control lp-dashboard-text-field" placeholder="<?php echo esc_html__('Ex: Roasted Chicken', 'listingpro'); ?>" value="<?php echo esc_attr($lp_menu['mTitle']); ?>">
                                                                                        </div>
                                                                                        <?php
                                                                                        if ($lp_menu['showQute'] == 'false') :
                                                                                        ?>
                                                                                            <div class="menu-price-wrap">
                                                                                                <div class="col-sm-2 padding-left-0">
                                                                                                    <label class="lp-dashboard-top-label" for="menu-old-price-<?php echo esc_attr($menu_id); ?>"><?php echo esc_html__('Reg. Price', 'listingpro'); ?></label>
                                                                                                    <input name="menu-old-price-<?php echo esc_attr($menu_id); ?>" id="menu-old-price-<?php echo esc_attr($menu_id); ?>" type="text" class="form-control lp-dashboard-text-field" placeholder="<?php echo esc_html__('Ex: $10', 'listingpro'); ?>" value="<?php echo esc_attr($lp_menu['mOldPrice']); ?>">
                                                                                                </div>
                                                                                                <div class="col-sm-2 padding-left-0">
                                                                                                    <label class="lp-dashboard-top-label" for="menu-new-price-<?php echo esc_attr($menu_id); ?>"><?php echo esc_html__('Sale Price', 'listingpro'); ?></label>
                                                                                                    <input id="menu-new-price-<?php echo esc_attr($menu_id); ?>" name="menu-new-price-<?php echo esc_attr($menu_id); ?>" type="text" class="form-control lp-dashboard-text-field" placeholder="<?php echo esc_html__('Ex: $10', 'listingpro'); ?>" value="<?php echo esc_attr($lp_menu['mNewPrice']); ?>">
                                                                                                </div>
                                                                                            </div>
                                                                                        <?php
                                                                                        else :
                                                                                        ?>
                                                                                            <div class="menu-quote-wrap">
                                                                                                <div class="col-sm-6">
                                                                                                    <label class="lp-dashboard-top-label" for="menu-quote-text-<?php echo esc_attr($menu_id); ?>"><?php esc_html_e('Quote Text', 'listingpro'); ?></label>
                                                                                                    <input id="menu-quote-text-<?php echo esc_attr($menu_id); ?>" type="text" class="form-control lp-dashboard-text-field" value="<?php echo esc_attr($lp_menu['mQuoteT']); ?>" placeholder="<?php echo esc_html__('Ex: Quote', 'listingpro'); ?>">
                                                                                                </div>
                                                                                                <div class="col-sm-6">
                                                                                                    <label class="lp-dashboard-top-label" for="menu-quote-link-<?php echo esc_attr($menu_id); ?>"><?php esc_html_e('Quote Link', 'listingpro'); ?></label>
                                                                                                    <input id="menu-quote-link-<?php echo esc_attr($menu_id); ?>" type="text" class="form-control lp-dashboard-text-field" value="<?php echo esc_attr($lp_menu['mQuoteL']); ?>" placeholder="<?php echo esc_html__('Ex: hht://yourweb.com/page', 'listingpro'); ?>">
                                                                                                </div>
                                                                                            </div>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                    <div class="margin-bottom-20">
                                                                                        <label class="lp-dashboard-top-label" for="menu-detail-<?php echo esc_attr($menu_id); ?>"><?php echo esc_html__('Short Description', 'listingpro'); ?></label>
                                                                                        <textarea name="menu-detail-<?php echo esc_attr($menu_id); ?>" id="menu-detail-<?php echo esc_attr($menu_id); ?>" type="text" class="form-control lp-dashboard-des-field" rows="3" placeholder="<?php echo esc_html__('Ex: Roasted Chicken', 'listingpro'); ?>"><?php echo esc_attr($lp_menu['mDetail']); ?></textarea>
                                                                                    </div>
                                                                                    <div class="lp-invoices-all-stats-on-off clearfix margin-bottom-10 Popular_item_container">
                                                                                        <label class="switch">
                                                                                            <?php
                                                                                            $is_popular = $lp_menu['popularItem'];
                                                                                            if ($is_popular == 'mItemPopularTrue') {
                                                                                                $is_popular = 'checked';
                                                                                            }
                                                                                            ?>
                                                                                            <input value="Yes" <?php echo esc_attr($is_popular); ?> class="form-control switch-checkbox menu_Popular_Item-<?php echo esc_attr($menu_id); ?>" type="checkbox" name="lp_form_fields_inn[235]">
                                                                                            <div class="slider round"></div>
                                                                                        </label>
                                                                                        <span class="margin-left-10" style="font-size: 16px!important;font-weight: normal!important;"><?php esc_html_e('Popular Item', 'listingpro'); ?></span>
                                                                                    </div>
                                                                                    <div class="clearfix margin-bottom-10 menuSpice-control_containter">
                                                                                        <select style="width: 100%!important;margin-left: 0;" id="menuSpice-control" class="form-control menuSpice-control-<?php echo esc_attr($menu_id); ?>">
                                                                                            <?php
                                                                                            $selected_spicelvl1 = '';
                                                                                            $selected_spicelvl2 = '';
                                                                                            $selected_spicelvl3 = '';
                                                                                            $selected_spicelvl4 = '';
                                                                                            $spiceLVL   =  $lp_menu['spiceLVL'];
                                                                                            if ($spiceLVL == 'spicelvl1') {
                                                                                                $selected_spicelvl1 = 'selected';
                                                                                            } elseif ($spiceLVL == 'spicelvl2') {
                                                                                                $selected_spicelvl2 = 'selected';
                                                                                            } elseif ($spiceLVL == 'spicelvl3') {
                                                                                                $selected_spicelvl3 = 'selected';
                                                                                            } elseif ($spiceLVL == 'spicelvl4') {
                                                                                                $selected_spicelvl4 = 'selected';
                                                                                            }
                                                                                            ?>
                                                                                            <option><?php echo esc_html__('Spice Level', 'listingpro'); ?></option>
                                                                                            <option <?php echo esc_attr($selected_spicelvl1); ?> data-level="1">🌶</option>
                                                                                            <option <?php echo esc_attr($selected_spicelvl2); ?> data-level="2">🌶🌶</option>
                                                                                            <option <?php echo esc_attr($selected_spicelvl3); ?> data-level="3">🌶🌶🌶</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                    <div class="jFiler-input-dragDrop pos-relative event-featured-image-wrap-dash">
                                                                                        <div <?php echo esc_attr($image_gallery); ?> class="upload-field dashboard-upload-field edit-upload-<?php echo esc_attr($menu_id); ?>">
                                                                                            <input class="frontend-input-multiple" type="hidden" id="dis-old-img-<?php echo esc_attr($menu_id); ?>" value="<?php echo esc_attr($lp_menu['mImage']); ?>">
                                                                                            <?php echo do_shortcode('[frontend-button]'); ?>
                                                                                            <div class="menu-edit-imgs-wrap">
                                                                                                <?php
                                                                                                if (!empty($lp_menu['mImage'])) :
                                                                                                    if (strpos($lp_menu['mImage'], ',')) {
                                                                                                        $gallery_arr = explode(',', $lp_menu['mImage']);
                                                                                                        $gallery_arr = array_filter($gallery_arr);
                                                                                                        $gal_img_count = 0;
                                                                                                        foreach ($gallery_arr as $img_url) {
                                                                                                ?>
                                                                                                            <div class="menu-edit-img-wrap gal-img-count-<?php echo esc_attr($gal_img_count); ?>">
                                                                                                                <span data-src="<?php echo esc_attr($img_url); ?>" data-target="dis-old-img-<?php echo esc_attr($menu_id); ?>" class="remove-menu-img"><i class="fa fa-close"></i></span>
                                                                                                                <img class="gal-img-count-<?php echo esc_attr($gal_img_count); ?> lp-uploaded-img event-old-img-<?php echo esc_attr($menu_id); ?>" src="<?php echo esc_attr($img_url); ?>" alt="image">
                                                                                                            </div>
                                                                                                        <?php
                                                                                                            $gal_img_count++;
                                                                                                        }
                                                                                                    } else {
                                                                                                        ?>
                                                                                                        <div class="menu-edit-img-wrap gal-img-count-single">
                                                                                                            <span data-src="<?php echo esc_attr($lp_menu['mImage']); ?>" data-target="dis-old-img-<?php echo esc_attr($menu_id); ?>" class="remove-menu-img"><i class="fa fa-close"></i></span>
                                                                                                            <img class="gal-img-count-single lp-uploaded-img event-old-img-<?php echo esc_attr($menu_id); ?>" src="<?php echo esc_attr($lp_menu['mImage']); ?>" alt="image">
                                                                                                        </div>
                                                                                                    <?php
                                                                                                    }
                                                                                                    ?>
                                                                                                <?php endif; ?>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <div class="lp-menu-save-btns clearfix col-md-12 margin-bottom-20">
                                                                        <button class="lp-cancle-btn cancel-update-menu"><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                                                                        <button data-LID="<?php echo esc_attr($lp_menu['mListing']); ?>" data-menuID="<?php echo esc_attr($menu_id); ?>" data-uid="<?php echo esc_attr($user_id); ?>" class="lp-save-btn lp-edit-menu"><?php echo esc_html__('save', 'listingpro'); ?></button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                <?php
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>
                            </div>
                        <?php
                            $menu_type_counter++;
                        }
                        ?>
                    </div>
        <?php
                }
                die();
            }
        }
        add_action('wp_ajax_add_image_menu',        'add_image_menu');
        if (!function_exists('add_image_menu')) {
            function add_image_menu()
            {
                check_ajax_referer('lp_ajax_nonce', 'lpNonce');
                // Nonce is checked, get the POST data and sign user on
                if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
                    $res = json_encode(array('nonceerror' => 'yes'));
                    die($res);
                }
                $user_id        =   get_current_user_id();
                $userID       =   sanitize_text_field($_POST['userID']);
                $listID                 = sanitize_text_field($_POST['selected_list_Id']);
                $menu_img                 = sanitize_text_field($_POST['img_Url']);
                $menuArray = array('menulist_ID' => $listID, 'menu-img' => $menu_img);
                if (!empty($menu_img) && !empty($listID)) {
                    update_post_meta($listID, 'menu_listing', $menuArray);
                }
                $return['userID']   =   $user_id;
                $return['img_Url']   =   $menu_img;
                $return['selected_list_Id']   =   $listID;
                $return['selected_list_Title']   =   get_the_title($listID);
                $return['selected_list_Link']   =   get_permalink($listID);
                die(json_encode($return));
            }
        }
        add_action('wp_ajax_del_add_image_menu',        'del_add_image_menu');
        if (!function_exists('del_add_image_menu')) {
            function del_add_image_menu()
            {
                check_ajax_referer('lp_ajax_nonce', 'lpNonce');
                // Nonce is checked, get the POST data and sign user on
                if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
                    $res = json_encode(array('nonceerror' => 'yes'));
                    die($res);
                }
                $menu_remove_id = sanitize_text_field($_POST['target']);
                if (!empty($menu_remove_id)) {
                    delete_post_meta($menu_remove_id, 'menu_listing');
                }
            }
        }
        add_action('wp_ajax_del_assigned_menu_to_listing',        'del_assigned_menu_to_listing');
        if (!function_exists('del_assigned_menu_to_listing')) {
            function del_assigned_menu_to_listing()
            {
                $user_id        =   get_current_user_id();
                $user_idd       =   sanitize_text_field($_POST['user_id']);
                $lid            =   sanitize_text_field($_POST['lid']);
                if ($user_id != $user_idd) {
                    $return['status']   =   'error';
                    $return['msg']      =   esc_html__('Invalid User Session', 'listingpro');
                    die(json_encode($return));
                } else {
                    delete_post_meta($lid, 'lp-listing-menu');
                    $return['status']   =   'success';
                    $return['msg']      =   esc_html__('Menu Items deleted successfully', 'listingpro');
                    die(json_encode($return));
                }
            }
        }
        add_action('wp_ajax_delete_own_review',        'delete_own_review');
        if (!function_exists('delete_own_review')) {
            function delete_own_review()
            {
                $return =   array();
                if (isset($_REQUEST)) {
                    $user_id        =   get_current_user_id();
                    $reviewID       =   sanitize_text_field($_POST['reviewID']);
                    $review_author  =   get_post_field('post_author', $reviewID);
                    if ($user_id == $review_author && current_user_can('delete_post', $reviewID)) {
                        $listing_id             =   listing_get_metabox_by_ID('listing_id', $reviewID);
                        $review_rating          =   listing_get_metabox_by_ID('rating', $reviewID);
                        $review_rating = apply_filters('lp_rating_number_format', $review_rating);
                        $listing_reviews_num    =   get_post_meta($listing_id, 'listing_reviewed', true);
                        if ($listing_reviews_num != 0) {
                            $listing_reviews_num    =   $listing_reviews_num - 1;
                        }
                        update_post_meta($listing_id, 'listing_reviewed', $listing_reviews_num);
                        $reviews_ids        =   listing_get_metabox_by_ID('reviews_ids', $listing_id);
                        $reviews_ids_arr    =   explode(',', $reviews_ids);
                        if (($key = array_search($reviewID, $reviews_ids_arr)) !== false) {
                            unset($reviews_ids_arr[$key]);
                        }
                        $new_listing_rating_sum =   0;
                        $new_rating_avg         =   0;
                        if (is_array($reviews_ids_arr) && count($reviews_ids_arr) > 0) {
                            foreach ($reviews_ids_arr as $item) {
                                $review_rating  =  (int) get_post_meta($item, 'rating', true);
                                $new_listing_rating_sum +=  $review_rating;
                            }
                            $new_rating_avg =   number_format($new_listing_rating_sum / count($reviews_ids_arr), 1);
                        }
                        $reviews_ids    =   implode(',', $reviews_ids_arr);
                        listing_set_metabox('reviews_ids', $reviews_ids, $listing_id);
                        update_post_meta($listing_id, 'listing_rate', $new_rating_avg);
                        wp_delete_post($reviewID, true);
                        $return['id']                   =   $reviewID;
                        $return['listing_id']           =   $listing_id;
                        $return['new_num']              =   $listing_reviews_num;
                        $return['reviews_ids']          =   $reviews_ids;
                        $return['new_sum']              =   $new_listing_rating_sum;
                        $return['new_rating_avg']       =   $new_rating_avg;
                    }
                }
                die(json_encode($return));
            }
        }
        if (!function_exists('coupon_timestamp')) {
            function coupon_timestamp($date, $time = '12:00 AM')
            {
                $time = strtotime($time);
                $h = date('H', $time);
                $i = date('i', $time);
                $s = date('s', $time);
                $m = date('m', $date);
                $d = date('d', $date);
                $y = date('Y', $date);
                $couponExpiry   = mktime($h, $i, $s, $m, $d, $y);
                return $couponExpiry;
            }
        }
        if (!function_exists('lp_get_ad_status_callback')) {
            add_filter('lp_get_ad_status', 'lp_get_ad_status_callback', 10, 2);
            function lp_get_ad_status_callback($adStatus = '', $listing_id = 0)
            {
                $campaign_status = get_post_meta($listing_id, 'campaign_status', true);
                if ($campaign_status == 'inactive') {
                    return 'inactive';
                }
                if (wp_doing_ajax() && (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ajax_search_tags')) {
                    $adStatus = get_post_meta($listing_id, 'lp_top_in_search_page_ads', true);
                } else if (is_archive() || (isset($_GET['s']) && $_GET['s'] == 'home')) {
                    $adStatus = get_post_meta($listing_id, 'lp_top_in_search_page_ads', true);
                } else if (is_singular('listing')) {
                    $adStatus = get_post_meta($listing_id, 'lp_detail_page_ads', true);
                } else {
                    $adStatus = get_post_meta($listing_id, 'lp_random_ads', true);
                }
                return $adStatus;
            }
        }
        if (!function_exists('lp_cal_listing_rate_withcolor')) {
            function lp_cal_listing_rate_withcolor($listing_id, $post_type = 'listing', $is_reviewcall = false)
            {
                global $listingpro_options;
                $reviewEnabled  = isset($listingpro_options['lp_review_switch']) ? $listingpro_options['lp_review_switch'] : '';
                if ($post_type == 'lp_review') {
                    $rating = listing_get_metabox_by_ID('rating', $listing_id);
                } else {
                    $rating = get_post_meta($listing_id, 'listing_rate', true);
                }
                $rating = apply_filters('lp_rating_number_format', $rating);
                $ratingRes = '';
                if (!empty($rating) && $rating > 0) {
                    if ($rating < 1) {
                        $ratingRes = '<span class="rate lp-rate-worst">' . $rating;
                    } else if ($rating >= 1 && $rating < 2) {
                        $ratingRes = '<span class="rate lp-rate-bad">' . $rating;
                    } else if ($rating >= 2 && $rating < 3.5) {
                        $ratingRes = '<span class="rate lp-rate-satisfactory">' . $rating;
                    } else if ($rating >= 3.5 && $rating <= 5) {
                        $ratingRes = '<span class="rate lp-rate-good">' . $rating;
                    }
                } else {
                    if (class_exists('ListingReviews')) {
                        if (is_singular('listing')) {
                            if ($is_reviewcall == true) {
                                $ratingRes = '';
                            } else {
                                if (get_post_status($listing_id) != 'publish') {
                                    $ratingRes = '<span class="no-review">' . esc_html__("Rating only enabled on published listing", "listingpro") . '</span>';
                                } else {
                                    if ($reviewEnabled == "1") {
                                        $ratingRes = '<span class="no-review">' . esc_html__("Be the first one to rate!", "listingpro") . '</span>';
                                    }
                                }
                            }
                        }
                    }
                }
                return $ratingRes;
            }
        }

        if (!function_exists('lp_validated_sites')) {
            function lp_validated_sites()
            {
                $url = get_site_url();
                if (strpos($url, 'classic.listingprowp.com') !== false) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        if (!function_exists('lp_get_banner_style')) {
            function lp_get_banner_style()
            {
                if (lp_validated_sites() == 'true' && isset($_GET['top-banner-style']) && !empty($_GET['top-banner-style'])) {
                    return $_GET['top-banner-style'];
                } else {
                    return lp_theme_option('top_banner_styles');
                }
            }
        }
        if (!function_exists('lp_get_header_views')) {
            function lp_get_header_views()
            {
                if (lp_validated_sites() == 'true' && isset($_GET['top-header-style']) && !empty($_GET['top-header-style'])) {
                    return $_GET['top-header-style'];
                } else {
                    return lp_theme_option('header_views');
                }
            }
        }
        if (!function_exists('lp_get_search_views')) {
            function lp_get_search_views()
            {
                if (lp_validated_sites() == 'true' && isset($_GET['search-style']) && !empty($_GET['search-style'])) {
                    return $_GET['search-style'];
                } else {
                    return lp_theme_option('search_different_styles');
                }
            }
        }
        if (!function_exists('lp_get_cat_views')) {
            function lp_get_cat_views()
            {
                if (lp_validated_sites() == 'true' && isset($_GET['cat-style']) && !empty($_GET['cat-style'])) {
                    return $_GET['cat-style'];
                } else {
                    return lp_theme_option('categories_different_styles');
                }
            }
        }

        function lp_get_total_count($table_name, $userID)
        {
            global $wpdb;
            $table = $wpdb->prefix . $table_name;

            // Check if the table exists
            if ($wpdb->get_var("SHOW TABLES LIKE '$table'") !== $table) {
                return 0;
            }

            $totalCount = $wpdb->get_var($wpdb->prepare(
                'SELECT SUM(count) FROM ' . $table . ' WHERE user_id = %d',
                $userID
            ));

            return $totalCount ? $totalCount : 0;
        }
