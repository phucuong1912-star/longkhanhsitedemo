<?php

$latitude = listing_get_metabox('latitude');
$longitude = listing_get_metabox('longitude');
$gAddress = listing_get_metabox('gAddress');

$adStatus = apply_filters('lp_get_ad_status', '', get_the_ID());


$CHeckAd = '';
$adClass = '';
if ($adStatus == 'active') {
    $CHeckAd = '<span class="listing-pro">' . esc_html__('Ad', 'listingpro') . '</span>';
    $adClass = 'promoted';
}
$claimed_section = listing_get_metabox('claimed_section');

$claim = '';
$claimStatus = '';

if ($claimed_section == 'claimed') {
    if (is_singular('listing')) {
        $claimStatus = esc_html__('Claimed', 'listingpro');
    }
    $claim = '<span class="verified simptip-position-top simptip-movable" data-tooltip="' . esc_html__('Claimed', 'listingpro') . '"><i class="fa fa-check"></i> ' . $claimStatus . '</span>';
} elseif ($claimed_section == 'not_claimed') {
    $claim = '';
}
$openStatus = listingpro_check_time(get_the_ID());


$deafaultFeatImg = lp_default_featured_image_listing();
$img_url    =   'https://placehold.co/190x170';

// New update 2.6.10
global $listingpro_options;
if (has_post_thumbnail())  {
    $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid');
    if (!empty($image[0])) {
        $img_url = $image[0];
    } elseif (!empty($deafaultFeatImg)) {

        $img_url = $deafaultFeatImg;
    } else {
        $img_url = 'https://placehold.co/372x240';
    }

    //$featureImg = $image[0];
} else if ($listingpro_options['lp_def_featured_image_from_gallery'] == 'enable') {
    
    //  echo "yes";
    $IDs = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
    

    $IDs = explode(',', $IDs);
    $imgurl_bgd = '';

    if (is_array($IDs)) {
        shuffle($IDs);

        $img_url = wp_get_attachment_image_src($IDs[0], 'listingpro-blog-grid');
       
      
        if(!empty( $img_url )){
            $imgurl = $img_url[0];
            $imgurl_bgd = $img_url[0];
        }

        if (!empty($imgurl)) {
            $img_url = $imgurl;
        } elseif (!empty($deafaultFeatImg)) {
            $img_url = $deafaultFeatImg;
        } else {
            $img_url = 'https://placehold.co/372x240';
        }
       
    }
} elseif (!empty($deafaultFeatImg)) {
    $img_url = $deafaultFeatImg;
} else {
    $img_url = 'https://placehold.co/372x240';
}

$trending_el =   false;
if (isset($GLOBALS['trending_el']) && $GLOBALS['trending_el'] != '' && $GLOBALS['trending_el'] == true) {
    $trending_el =   true;
}

$img_url_g    =   'https://placehold.co/360x210';
if (has_post_thumbnail()) {
    if ($trending_el == true) {
        $img_url_g = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid2');
	    $img_url_g  = $img_url_g[0] ?? '';
    } else {
        $img_url_g = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid');
        $img_url_g  = $img_url_g[0] ?? '';
    }
    if(empty($img_url_g)){
	    $img_url_g = $img_url;
    }
} elseif (!empty($deafaultFeatImg)) {
    if ($listingpro_options['lp_def_featured_image_from_gallery'] == 'enable' && !empty($imgurl_bgd) ) {
        $img_url_g = $imgurl_bgd;
    }else{
        $img_url_g = $deafaultFeatImg;
    }
}

// End New update 2.6.10

$favrt  =   listingpro_is_favourite_v2(get_the_ID());

$tags   =   get_the_terms(get_the_ID(), 'list-tags');
$cats   =   get_the_terms(get_the_ID(), 'listing-category');
$location   =   get_the_terms(get_the_ID(), 'location');

$listing_discount       =   array();
$listing_discount_data  =   get_post_meta(get_the_ID(), 'listing_discount_data', true);

if (is_array($listing_discount_data) && !empty($listing_discount_data)) {
    $timezone_string = get_option('timezone_string');
    if (isset($timezone_string) && !empty($timezone_string)) {
        date_default_timezone_set($timezone_string);
    }
    $strNow =   strtotime("NOW");
    foreach ($listing_discount_data as $k => $v) {
        $exETime = strtotime('12:00 AM');
        if (!empty($v['disTimeE'])) {
            $exETime = strtotime($v['disTimeE']);
        }
        $exEDate = $v['disExpE'];
        $exTime = strtotime('12:00 AM');
        $expiry_date  = coupon_timestamp($v['disExpE'], $exTime);
        $stTime = strtotime('12:00 AM');
        if (!empty($v['disTimeE'])) {
            $stTime = strtotime($v['disTimeS']);
        }
        $start_date = coupon_timestamp($v['disExpS'], $stTime);
        if ($strNow < $expiry_date  &&  $strNow > $start_date) {
            $listing_discount   =   $v;
            break;
        }
    }
}
$listing_discount_count  =   count($listing_discount);
$user_id    =   get_current_user_id();
$lp_listing_announcements_initial  =   get_post_meta(get_the_ID(), 'lp_listing_announcements', true);
$lp_listing_announcements  =   array();
if (isset($lp_listing_announcements_initial) && is_array($lp_listing_announcements_initial)) {

    foreach ($lp_listing_announcements_initial as $lp_listing_announcement) {
        if (!isset($lp_listing_announcement['annStatus']) || $lp_listing_announcement['annStatus'] == 1) {
            $lp_listing_announcements[] =   $lp_listing_announcement;
        }
    }
}

global $listing_counter, $found, $listingpro_options;
$listing_layout = $listingpro_options['listing_views'];
if (isset($GLOBALS['my_listing_views']) && $GLOBALS['my_listing_views'] != '') {
    $listing_layout =   $GLOBALS['my_listing_views'];
}

$listing_col_class  =   'col-md-12';
$listing_style = $listingpro_options['listing_style'];
if ($listing_layout == 'grid_view_v2') {
    if ($listing_style == '1') {
        $listing_col_class = 'col-md-4 col-sm-12';
    } elseif ($listing_style == '5') {
        $listing_style = 'col-md-12 col-sm-12';
        $postGridnumber = 2;
    } else {
        $listing_col_class  =   'col-md-6';
    }
}
$lp_title   =   get_the_title();
if ($listing_discount_count != 0 && strlen($lp_title) > 25) {
    $lp_title   =   mb_substr(get_the_title(), 0, 25) . '...';
} else if ($listing_discount_count == 0 && strlen($lp_title) > 50) {
    $lp_title   =   mb_substr(get_the_title(), 0, 50) . '...';
}
$lp_title_grid   =   get_the_title();
if (strlen($lp_title_grid) > 25) {
    $lp_title_grid   =   mb_substr(get_the_title(), 0, 25) . '...';
}

$discount_data  =   get_post_meta(get_the_ID(), 'listing_discount_data', true);

$menu_check     =   get_post_meta(get_the_ID(), 'lp-listing-menu', true);
$phone          =   listing_get_metabox('phone');
$gAddress       =   listing_get_metabox('gAddress');
$lp_lat         =   listing_get_metabox_by_ID('latitude', get_the_ID());
$lp_lng         =   listing_get_metabox_by_ID('longitude', get_the_ID());

$PM_btns_all    =   '';
$is_phone       =   '';
$is_menu        =   '';
$is_gAddress    =   '';

if (!empty($phone) && !empty($menu_check) && is_array($menu_check) && !empty($gAddress)) {
    $PMG_btns_both   =   'ok';
}
if (!empty($phone)) {
    $is_phone   =   'ok';
}
if (!empty($menu_check) && is_array($menu_check)) {
    $is_menu    =   'ok';
}
if (!empty($gAddress)) {
    $is_gAddress    =   'ok';
}
?>
<div class="<?php echo esc_attr($listing_col_class); ?> <?php echo esc_attr($adClass); ?> <?php echo 'listing-style-' . esc_attr($listing_style); ?> <?php echo esc_attr($listing_layout); ?> loop-switch-class grid_view_s4 lp-grid-box-contianer" data-title="<?php echo get_the_title(); ?>" data-postid="<?php echo get_the_ID(); ?>" data-lattitue="<?php echo esc_attr($latitude); ?>" data-longitute="<?php echo esc_attr($longitude); ?>" data-posturl="<?php echo get_the_permalink(); ?>">
    <div class="lp-listing">
        <div class="grid-style-container">
            <div class="lp-listing-top">
                <a href="#" data-post-id="<?php echo get_the_ID(); ?>" data-post-type="list" class="lp-listing-favrt <?php if ($favrt == 'yes') {
                                                                                                                            echo 'remove-fav-v2';
                                                                                                                        } else {
                                                                                                                            echo 'add-to-fav-v2';
                                                                                                                        } ?>">
                    <i class="fa fa-heart<?php if ($favrt != 'yes') {
                                                echo '-o';
                                            } ?>" aria-hidden="true"></i>
                </a>
                <div class="clearfix lp-listing-discount-outer">

                    <?php echo listingpro_price_dynesty($post->ID); ?>
                    <?php
                    if (is_array($discount_data) && !empty($discount_data)) :
                    ?>
                        <div class="lp-listing-discount-range">
                            <span class="lp-listing-price-range-currency"><?php echo wp_kses_post($discount_data[0]['disOff']); ?></span>
                        </div>
                    <?php
                    endif;
                    ?>

                </div>

                <div class="lp-listing-top-thumb">
                    <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($img_url_g); ?>" alt="<?php the_title(); ?>"></a>
                </div>
            </div>
            <div class="lp-listing-bottom">
                <?php
                if ($cats) :
                    $counter    =   1;
                    foreach ($cats as $cat) :
                        $cat_link   =   get_term_link($cat);
                        if ($counter == 1) :
                            $cat_img = listing_get_tax_meta($cat->term_id, 'category', 'image');
                            $lp_default_map_pin = get_template_directory_uri() . '/assets/images/pins/lp-logo.png';
                            if (empty($cat_img)) {
                                $cat_img =   $lp_default_map_pin;
                            }
                            if (!empty($cat_img)) :
                                if( hasFontAwesomeIconClass($cat_img) ){
									echo '<span class="cat-icon" style="display:none;"><i class="icon icons-search-cat '.$cat_img.'"></i></span>'; 
								}else{
									echo '<span class="cat-icon" style="display:none;"><img class="icon icons8-Food" src="' . $cat_img . '" alt="cat-icon"></span>';
								}
                ?>
                                <a href="<?php echo esc_url($cat_link); ?>" class="lp-listing-cat"><?php echo esc_attr($cat->name); ?></a>
                            <?php else : ?><a href="<?php echo esc_url($cat_link); ?>" class="lp-listing-cat"><?php echo esc_attr($cat->name); ?></a>
                        <?php endif;
                        endif; ?>
                    <?php $counter++;
                    endforeach; ?>
                <?php endif; ?>
                <?php echo wp_kses_post($openStatus); ?>
                <div class="clearfix"></div>
                <h4><?php echo wp_kses_post($CHeckAd); ?> <a href="<?php the_permalink(); ?>"><?php echo esc_attr($lp_title_grid); ?> <?php echo wp_kses_post($claim); ?></a></h4>
                <div class="lp-listing-cats">
                    <?php
                    if ($tags) :
                        $total_tags =   count($tags);
                        foreach ($tags as $tag) :
                            $tag_link   =   get_term_link($tag);
                            $tag_name  =   $tag->name;
                    ?>
                            <a href="<?php echo esc_url($tag_link); ?>">
                                <?php echo esc_attr($tag_name); ?><?php if ($counter != $total_tags) {
                                                                        echo ',';
                                                                    }; ?>
                            </a>
                    <?php $counter++;
                        endforeach;
                    else : echo '<a href=""></a>';
                    endif; ?>

                </div>

                <div class="lp-listing-stars">
                    <?php
                    $NumberRating = listingpro_ratings_numbers($post->ID);
                    if ($NumberRating == 0) {

                    ?>

                        <span class="lp-ann-btn lp-no-review-btn"><?php echo esc_html__('Be the first to review!', 'listingpro'); ?></span>

                    <?php

                    } else {
                        $rating = get_post_meta(get_the_ID(), 'listing_rate', true);
                        $rating_num_bg = '';
                        $rating_num_clr = '';

                        if ($rating < 3) {
                            $rating_num_bg = 'num-level1';
                            $rating_num_clr = 'level1';
                        }
                        if ($rating < 4) {
                            $rating_num_bg = 'num-level2';
                            $rating_num_clr = 'level2';
                        }
                        if ($rating < 5) {
                            $rating_num_bg = 'num-level3';
                            $rating_num_clr = 'level3';
                        }
                        if ($rating >= 5) {
                            $rating_num_bg = 'num-level4';
                            $rating_num_clr = 'level4';
                        }
                    ?>
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
                        <span class="lp-rating-num"><?php echo esc_attr($rating); ?></span>
                    <?php
                    }
                    ?>

                </div>
                <?php
                if ($location) :
                    $loc        =   $location[0];
                    $loc_link   =   get_term_link($loc);
                    echo '<div class="lp-listing-location"><i class="fa fa-map-marker" aria-hidden="true"></i> <a href="' . $loc_link . '">' . $loc->name . '</a></div>';
                endif;
                ?>
                <div class="clearfix"></div>
            </div>
            <?php
            if ((!empty($menu_check) && is_array($menu_check)) || (!empty($phone)) || (!empty($gAddress))) :
            ?>
                <div class="lp-new-grid-bottom-button">
                    <ul class="clearfix">

                        <?php
                        if ($is_phone == 'ok') :
                        ?>
                            <li onclick="myFuction(this)" class="show-number-wrap" style="<?php //if( $is_menu == '' ){ echo 'width:100%;'; } 
                                                                                            ?>">
                                <p><i class="fa fa-phone" aria-hidden="true"></i> <span class="show-number"><?php esc_html_e('call', 'listingpro'); ?></span><a href="tel:<?php echo esc_attr($phone); ?>" class="grind-number"><?php echo esc_attr($phone); ?></a></p>
                            </li>
                        <?php endif; ?>
                        <?php
                        if ($is_gAddress == 'ok') :
                        ?>
                            <li style="<?php //if( $is_phone == '' ){ echo 'width:50%;'; } 
                                        ?>">
                                <a href="" data-lid="<?php echo get_the_ID(); ?>" data-lat="<?php echo esc_attr($lp_lat); ?>" data-lng="<?php echo esc_attr($lp_lng); ?>" class="show-loop-map-popup"><i class="fa fa-map-pin" aria-hidden="true"></i> <?php esc_html_e('Show Map', 'listingpro'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php
                        if ($is_menu == 'ok') :
                        ?>
                            <li style="<?php //if( $is_phone == '' ){ echo 'width:50%;'; } 
                                        ?>">
                                <a href="<?php echo get_permalink(get_the_ID()); ?>/#lp-listing-menuu-wrap"><i class="fa fa-cutlery" aria-hidden="true"></i> <?php esc_html_e('View Menu', 'listingpro'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <div class="list-style-cotainer">
            <div class="lp-listing-top">
                <a href="#" data-post-id="<?php echo get_the_ID(); ?>" data-post-type="list" class="lp-listing-favrt <?php if ($favrt == 'yes') {
                                                                                                                            echo 'remove-fav-v2';
                                                                                                                        } else {
                                                                                                                            echo 'add-to-fav-v2';
                                                                                                                        } ?>">
                    <i class="fa fa-heart<?php if ($favrt != 'yes') {
                                                echo '-o';
                                            } ?>" aria-hidden="true"></i>
                </a>


                <div class="lp-listing-top-thumb">
                    <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>"></a>
                </div>
            </div>
            <div class="lp-listing-bottom">
                <div class="<?php echo esc_attr($listing_discount_count); ?> clearfix lp-listing-bottom-left-full <?php if ($listing_discount_count == 0) {
                                                                                                                        echo '';
                                                                                                                    } ?>">
                    <?php
                    if ($cats) :
                        $counter    =   1;
                        foreach ($cats as $cat) :
                            $cat_link   =   get_term_link($cat);
                            if ($counter == 1) :
                                $cat_img = listing_get_tax_meta($cat->term_id, 'category', 'image');
                                $lp_default_map_pin = get_template_directory_uri() . '/assets/images/pins/lp-logo.png';
                                if (empty($cat_img)) {
                                    $cat_img =   $lp_default_map_pin;
                                }
                                if (!empty($cat_img)) :
                                    if( hasFontAwesomeIconClass($cat_img) ){
										echo '<span class="cat-icon" style="display:none;"><i class="icon icons-search-cat '.$cat_img.'"></i></span>'; 
									}else{
										echo '<span class="cat-icon" style="display:none;"><img class="icon icons8-Food" src="' . $cat_img . '" alt="cat-icon"></span>';
									}
                    ?>
                                    <a href="<?php echo esc_url($cat_link); ?>" class="lp-listing-cat"><?php echo esc_attr($cat->name); ?></a>
                                <?php else : ?><a href="<?php echo esc_url($cat_link); ?>" class="lp-listing-cat"><?php echo esc_attr($cat->name); ?></a>
                            <?php endif;
                            endif; ?>
                        <?php $counter++;
                        endforeach; ?>
                    <?php endif; ?>
                    <?php echo wp_kses_post($openStatus); ?>
                    <div class="clearfix"></div>
                    <div class="lp-bottom-left-full-outer">
                        <h4><?php echo wp_kses_post($CHeckAd); ?> <a href="<?php the_permalink(); ?>"><?php echo esc_attr($lp_title); ?> <?php echo wp_kses_post($claim); ?></a></h4>

                        <?php
                        if ($tags) :
                            $total_tags =   count($tags);
                            $counter    =   1;
                        ?>
                            <div class="lp-listing-cats">
                                <?php
                                foreach ($tags as $tag) :
                                    $tag_link   =   get_term_link($tag);
                                    $tag_name  =   $tag->name;
                                ?>
                                    <a href="<?php echo esc_url($tag_link); ?>">
                                        <?php echo esc_attr($tag_name); ?><?php if ($counter != $total_tags) {
                                                                                echo ',';
                                                                            }; ?>
                                    </a>
                                <?php $counter++;
                                endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="lp-listing-stars">
                            <?php
                            $NumberRating = listingpro_ratings_numbers($post->ID);
                            if ($NumberRating == 0) {

                            ?>

                                <span class="lp-ann-btn lp-no-review-btn"><?php echo esc_html__('Be the first to review!', 'listingpro'); ?></span>

                            <?php

                            } else {
                                $rating = get_post_meta(get_the_ID(), 'listing_rate', true);
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
                                <span class="lp-rating-num"><?php echo esc_attr($rating); ?></span>
                            <?php
                            }

                            ?>

                        </div>
                        <div class="clearfix"></div>
                        <?php
                        if (is_array($lp_listing_announcements) && !empty($lp_listing_announcements)) :
                        ?>
                            <div class="code-overlay"></div>
                            <div class="clearfix"></div>
                            <div class="lp-listing-announcements" id="ann-<?php echo get_the_ID(); ?>">
                                <div class="lp-listing-announcement">
                                    <span class="close-ann"><i class="fa fa-times" aria-hidden="true"></i></span>
                                    <div class="popup-header">
                                        <strong><?php echo esc_html__('Announcements', 'listingpro'); ?></strong>
                                    </div>
                                    <?php
                                    $ann_counter    =   1;
                                    $ann_total     =   count($lp_listing_announcements);
                                    foreach ($lp_listing_announcements as $listing_announcement) :
                                    ?>
                                        <div class="announcement-wrap <?php if ($ann_counter == $ann_total) {
                                                                            echo 'last';
                                                                        } ?>">
                                            <i class="fa fa-bullhorn" aria-hidden="true"></i> <span><?php echo wp_kses_post($listing_announcement['annMsg']); ?></span>
                                            <?php
                                            if (!empty($listing_announcement['annBT']) && !empty($listing_announcement['annBL'])) :
                                            ?>
                                                <a target="_blank" href="<?php echo wp_kses_post($listing_announcement['annBL']); ?>" class="announcement-btn"><?php echo wp_kses_post($listing_announcement['annBT']); ?></a>
                                            <?php endif; ?>
                                            <div class="clearfix"></div>
                                        </div>
                                    <?php $ann_counter++;
                                    endforeach; ?>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="clearfix">
                            <?php
                            if (is_array($lp_listing_announcements) && !empty($lp_listing_announcements)) :
                            ?>
                                <a href="#" data-ann="ann-<?php echo get_the_ID(); ?>" class="lp-ann-btn"><i class="fa fa-bullhorn" aria-hidden="true"></i> <?php echo esc_html__('Announcements', 'listingpro'); ?></a>
                            <?php endif; ?>
                            <?php echo listingpro_price_dynesty($post->ID); ?>
                        </div>
                        <?php
                        if ($gAddress) :
                        ?>
                            <div class="lp-listing-location">
                                <p class="margin-bottom-0"><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_attr($gAddress); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="clearfix"></div>

                    </div>
                    <?php
                    $coupon_mobile =   '';
                    if (is_array($listing_discount) && !empty($listing_discount)) :

                        if (wp_is_mobile()) :
                    ?>
                            <a href="" class="lp-coupon-btn" data-coupon="coup-<?php echo get_the_ID(); ?>"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo esc_html__('Coupon Code', 'listingpro'); ?></a>
                        <?php
                            $coupon_mobile  =   'lp-listing-bottom-right-mobile';
                        endif;

                        $btn_href   =   '';
                        $btn_class  =   'lp-copy-code';
                        if ($listing_discount['disBL'] && !empty($listing_discount['disBL'])) {
                            $btn_href   =   'href="' . $listing_discount['disBL'] . '"';
                            $btn_class  =   '';
                        }
                        ?>
                        <div class="code-overlay"></div>
                        <div id="coup-<?php echo get_the_ID(); ?>" class="lp-listing-bottom-right <?php echo wp_kses_post($coupon_mobile); ?>">
                            <?php
                            if (!empty($listing_discount['disOff']) || !empty($listing_discount['disHea'])) :
                                $hea_off    =   $listing_discount['disOff'] . ' ' . $listing_discount['disHea'];
                                $off_    =   $listing_discount['disOff'];
                                if (strlen($listing_discount['disOff']) > 18) {
                                    $off_   =   mb_substr($listing_discount['disOff'], 0, 20) . '...';
                                }

                            ?>
                                <div class="discount-bar">
                                    <i class="fa fa-tags pull-left"></i>
                                    <?php if ($listing_discount['disOff']) echo wp_kses_post($listing_discount['disOff']);; ?>
                                    <?php
                                    if (strlen($listing_discount['disOff']) < 18) {
                                        $new_len    =   15 - strlen($listing_discount['disOff']);
                                        if (strlen($listing_discount['disHea']) > $new_len) {
                                            echo mb_substr($listing_discount['disHea'], 0, $new_len) . '...';
                                        } else {
                                            $listing_discount['disHea'];
                                        }
                                    }
                                    ?>
                                    <i class="fa fa-chevron-down pull-right"></i>
                                </div>
                                <?php
                                $exTime = strtotime('12:00 AM');
                                if (!empty($listing_discount['disTimeE'])) {
                                    $exTime = strtotime($listing_discount['disTimeE']);
                                }
                                $couponExpiry  = coupon_timestamp($listing_discount['disExpE'], $exTime);
                                ?>
                                <div class="coupons-bottom-content-wrap">
                                    <div class="archive-countdown-wrap">
                                        <div id="lp-deals-countdown<?php echo get_the_ID(); ?>" class="lp-countdown lp-deals-countdown<?php echo get_the_ID(); ?>" data-label-hours="<?php echo esc_html__('hours', 'listingpro'); ?>" data-label-mins="<?php echo esc_html__('min', 'listingpro'); ?>" data-label-secs="<?php echo esc_html__('sec', 'listingpro'); ?>" data-label-days="<?php echo esc_html__('days', 'listingpro'); ?>" data-minute="<?php echo date('i', $couponExpiry); ?>" data-hour="<?php echo date('H', $couponExpiry); ?>" data-day="<?php echo date('d', $couponExpiry); ?>" data-month="<?php echo date('m', $couponExpiry) - 1; ?>" data-year="<?php echo date('Y', $couponExpiry); ?>"></div>
                                    </div>

                                    <a target="_blank" data-target-code="deal-copy-<?php echo get_the_ID(); ?>" <?php echo wp_kses_post($btn_href); ?> class="deal-button <?php echo esc_attr($btn_class); ?>"><i class="fa fa-gavel" aria-hidden="true"></i> <?php echo wp_kses_post($listing_discount['disBT']); ?></a>

                                    <div class="dis-code-copy-pop deal-copy-<?php echo get_the_ID(); ?>" id="dicount-copy-<?php echo get_the_ID(); ?>">
                                        <span class="close-right-icon" data-target="deal-copy-<?php echo get_the_ID(); ?>"><i class="fa fa-times"></i></span>
                                        <div class="dis-code-copy-pop-inner">
                                            <div class="dis-code-copy-pop-inner-cell">
                                                <p><?php echo esc_html__('Copy to clipboard', 'listingpro'); ?></p>
                                                <p class="dis-code-copy-wrap"><input class="code-top-copy-<?php echo get_the_ID(); ?>" type="text" value="<?php echo wp_kses_post($listing_discount['disCod']); ?>"> <a data-target-code="dicount-copy-<?php echo get_the_ID(); ?>" href="#" class="copy-now" data-coppied-label="<?php echo esc_html__('Copied', 'listingpro'); ?>"><?php echo esc_html__('Copy', 'listingpro'); ?></a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>

                <div class="clearfix"></div>
            </div>

            <div class="clearfix"></div>
            <?php
            if ((!empty($menu_check) && is_array($menu_check)) || (!empty($phone)) || (!empty($gAddress))) :
            ?>
                <div class="lp-new-grid-bottom-button">
                    <ul class="clearfix">

                        <?php
                        if ($is_phone == 'ok') :
                        ?>
                            <li onclick="myFuction(this)" class="show-number-wrap" style="<?php //if( $is_menu == '' ){ echo 'width:100%;'; } 
                                                                                            ?>">
                                <p><i class="fa fa-phone" aria-hidden="true"></i> <span class="show-number"><?php esc_html_e('call', 'listingpro'); ?></span><a href="tel:<?php echo esc_attr($phone); ?>" class="grind-number"><?php echo esc_attr($phone); ?></a></p>
                            </li>
                        <?php endif; ?>
                        <?php
                        if ($is_gAddress == 'ok') :
                        ?>
                            <li style="<?php //if( $is_phone == '' ){ echo 'width:50%;'; } 
                                        ?>">
                                <a href="" data-lid="<?php echo get_the_ID(); ?>" data-lat="<?php echo esc_attr($lp_lat); ?>" data-lng="<?php echo esc_attr($lp_lng); ?>" class="show-loop-map-popup"><i class="fa fa-map-pin" aria-hidden="true"></i> <?php esc_html_e('Show Map', 'listingpro'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php
                        if ($is_menu == 'ok') :
                        ?>
                            <li style="<?php //if( $is_phone == '' ){ echo 'width:50%;'; } 
                                        ?>">
                                <a href="<?php echo get_permalink(get_the_ID()); ?>/#lp-listing-menuu-wrap"><i class="fa fa-cutlery" aria-hidden="true"></i> <?php esc_html_e('View Menu', 'listingpro'); ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>