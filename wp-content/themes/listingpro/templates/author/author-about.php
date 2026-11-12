<?php

global $listingpro_options;


$authorID   =   get_queried_object_id();
$user_data  =   get_userdata($authorID);


$fb_show    =   '';
$tw_show    =   '';
$link_show    =   '';
$insta_show    =   '';
$pin_show    =   '';

$author_fb    =   get_the_author_meta('facebook', $authorID);
$author_tw    =   get_the_author_meta('twitter', $authorID);
$author_pin    =   get_the_author_meta('pinterest', $authorID);
$author_insta    =   get_the_author_meta('instagram', $authorID);
$author_link    =   get_the_author_meta('linkedin', $authorID);
$author_phone    =   get_the_author_meta('phone', $authorID);
$author_website    =   get_the_author_meta('url', $authorID);
$author_email    =   get_the_author_meta('email', $authorID);
$author_address    =   get_the_author_meta('address', $authorID);

if (isset($listingpro_options['about_me_social_icons_fb']))
    $fb_show    =   $listingpro_options['about_me_social_icons_fb'];

if (isset($listingpro_options['about_me_social_icons_tw']))
    $tw_show    =   $listingpro_options['about_me_social_icons_tw'];

if (isset($listingpro_options['about_me_social_icons_linkedin']))
    $link_show  =   $listingpro_options['about_me_social_icons_linkedin'];

if (isset($listingpro_options['about_me_social_icons_inst']))
    $insta_show =   $listingpro_options['about_me_social_icons_inst'];

if (isset($listingpro_options['about_me_social_icons_pin']))
    $pin_show   =   $listingpro_options['about_me_social_icons_pin'];


$cpLat = $listingpro_options['cp-lat'];
$cpLan = $listingpro_options['cp-lan'];
$lp_map_pin = $listingpro_options['lp_map_pin']['url'];

$about_activities       =   $listingpro_options['about_activities'];
$about_activities_title =   $listingpro_options['about_activities_title'];

?>

<div class="author-about-wrap">
    <div class="row">
        <div class="col-md-7">
            <?php
            if (!empty($user_data->description)) :
            ?>
                <?php echo wp_kses_post($user_data->description); ?>
            <?php else : ?>
                <p><?php echo esc_html__('No info available', 'listingpro'); ?></p>
            <?php endif; ?>
        </div>
        <div class="col-md-5">
            <?php
            if (!empty($author_website)) :
            ?>
                <div class="info-row">
                    <p>
                        <label><?php echo esc_html__('website', 'listingpro'); ?></label>
                        <strong><?php echo esc_attr($author_website); ?></strong>
                    </p>
                </div>
            <?php endif; ?>
            <?php
            if (!empty($author_phone)) :
            ?>
                <div class="info-row">
                    <p>
                        <label><?php echo esc_html__('Phone', 'listingpro'); ?></label>
                        <strong><?php echo esc_attr($author_phone); ?></strong>
                    </p>
                </div>
            <?php endif; ?>
            <?php
            if (!empty($author_address)) :
            ?>
                <div class="info-row">
                    <p>
                        <label><?php echo esc_html__('Address', 'listingpro'); ?></label>
                        <strong><?php echo esc_attr($author_address); ?></strong>
                    </p>
                </div>
            <?php endif; ?>
            <?php
            $social_show = $listingpro_options['about_me_social_icons'];
            if ($social_show == 1) :
                if ($author_fb != '' || $author_tw != '' || $author_pin != '' || $author_insta || $author_link != '') :
            ?>
                    <div class="info-row">
                        <h5 class="author-contact-second-heading"><?php echo esc_html__('Social', 'listingpro'); ?></h5>
                        <div class="lp-blog-grid-shares-contact-page">

                            <?php
                            if ($fb_show == 1 && $author_fb != '') :
                            ?>
                                <a href="<?php echo esc_url($author_fb); ?>" class="lp-blog-grid-shares-icon icon-fb"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                            <?php endif; ?>
                            <?php
                            if ($tw_show == 1 && $author_tw != '') :
                            ?>
                                <a href="<?php echo esc_url($author_tw); ?>" class="lp-blog-grid-shares-icon icon-tw"><i class="fa-brands fa-square-x-twitter" aria-hidden="true"></i></a>
                            <?php endif; ?>
                            <?php
                            if ($pin_show == 1 && $author_pin != '') :
                            ?>
                                <a href="<?php echo esc_url($author_pin); ?>" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a>
                            <?php endif; ?>
                            <?php
                            if ($insta_show == 1 && $author_insta) :
                            ?>
                                <a href="<?php echo esc_url($author_insta); ?>" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                            <?php endif; ?>

                            <?php
                            if ($link_show == 1 && esc_url($author_link)) :
                            ?>
                                <a href="<?php echo esc_url($author_link); ?>" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-linkedin" aria-hidden="true"></i></a>
                            <?php endif; ?>


                            <div class="clearfix"></div>
                        </div>
                    </div>
            <?php endif;
            endif;
            ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if ($about_activities == 1) :
    ?>
        <div class="about-activities">
            <?php
            if (!empty($about_activities_title)) {
                echo '<h3>' . $about_activities_title . '</h3>';
            }
            ?>

            <div class="about-activities-inner">
                <?php
                $args = array(
                    'post_type' => 'lp-reviews',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'order' => 'ASC',
                    'author' => $authorID,
                );

                $my_query = null;
                $my_query = new WP_Query($args);
                $total_pages    =   ceil($my_query->found_posts / 20);
                if ($my_query->have_posts()) :

                    echo '<div class="lp-listing-reviews">';
                    echo '<div class="reviews-pagin-wrap reviews-pagin-wrap-1">';
                    $i = 0;
                    $pagin_wrap =   1;
                    while ($my_query->have_posts()) : $my_query->the_post();
                        $review_id  =   get_the_ID();
                        $listing_id = '';
                        if (class_exists('Elementor_Listingpro')) {
                            $listing_id = listing_get_metabox_by_ID('listing_id', $review_id);
                        }
                        activity_reviews($review_id, $authorID);
                        $i++;
                        if ($i % 20 == 0) {
                            $pagin_wrap++;
                            echo '</div><div class="reviews-pagin-wrap reviews-pagin-wrap-' . $pagin_wrap . '">';
                        }
                    endwhile;
                    wp_reset_postdata();
                    echo '</div>';
                ?>
                    <div class="clearfix"></div>
                    <?php
                    if ($total_pages > 1) {
                    ?>
                        <div class="lp-pagination author-reviews-pagination">
                            <ul class="page-numbers">
                                <?php
                                for ($x = 1; $x <= $total_pages; $x++) {
                                ?>
                                    <li><span data-skeyword="" data-pageurl="<?php echo esc_attr($x); ?>" class="page-numbers <?php if ($x == 1) {
                                                                                                                                    echo 'current';
                                                                                                                                } ?>"><?php echo esc_attr($x); ?></span></li>
                                <?php
                                }
                                ?>
                            </ul>
                        </div>
                    <?php
                    }
                    ?>

                <?php
                    echo '</div>';
                else :
                    echo esc_html__('No Review Found', 'listingpro');
                endif;
                ?>
            </div>


        </div>
    <?php
    endif;
    ?>
</div>