<?php

$latitude = listing_get_metabox('latitude');

$longitude = listing_get_metabox('longitude');

$gAddress = listing_get_metabox('gAddress');


$adStatus = apply_filters('lp_get_ad_status', '', get_the_ID());


$CHeckAd = '';



$adClass = '';



if($adStatus == 'active'){



    $CHeckAd = '<span>'.esc_html__('Ad','listingpro').'</span>';



    $adClass = 'promoted';



}



$claimed_section = listing_get_metabox('claimed_section');







$claim = '';



$claimStatus = '';







if($claimed_section == 'claimed') {



    if(is_singular( 'listing' ) ){

        $claimStatus = esc_html__('Claimed', 'listingpro');

    }

    $claim = '<span class="verified simptip-position-top simptip-movable" data-tooltip="'. esc_html__('Claimed', 'listingpro').'"><i class="fa fa-check"></i> '.$claimStatus.'</span>';



}elseif($claimed_section == 'not_claimed') {



    $claim = '';



}

$openStatus = '';

$favrt = '';

$openStatus = listingpro_check_time(get_the_ID());



$favrt  =   listingpro_is_favourite_v2(get_the_ID());





require_once (THEME_PATH . "/include/aq_resizer.php");



$img_url    =   'https://placehold.co/360x210';

$trending_el =   false;

if( isset( $GLOBALS['trending_el'] ) && $GLOBALS['trending_el'] != '' && $GLOBALS['trending_el'] == true )

{

    $trending_el =   true;

    $img_url    =   'https://placehold.co/360x420';

}
$deafaultFeatImg = lp_default_featured_image_listing();
// New update 2.6.10
global $listingpro_options;
if (has_post_thumbnail()) {

    $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid');

    if(!empty($image[0])){
        $img_url = $image[0];
    }
    elseif (!empty($deafaultFeatImg)) {

        $img_url = $deafaultFeatImg;
    }

    else {
        $img_url = 'https://placehold.co/372x240';
    }

    //$featureImg = $image[0];
}

else if ($listingpro_options['lp_def_featured_image_from_gallery'] == 'enable') {

    //  echo "yes";
    $IDs = get_post_meta(get_the_ID(), 'gallery_image_ids', true);

    $IDs = explode(',', $IDs);

    if (is_array($IDs)) {
        shuffle($IDs);

        $img_url = wp_get_attachment_image_src($IDs[0], 'listingpro-blog-grid');

        if(!empty( $img_url )){
            $imgurl = $img_url[0];
        }
        
        if(!empty($imgurl)){
            $img_url = $imgurl;
        }
        elseif (!empty($deafaultFeatImg)) {

            $img_url = $deafaultFeatImg;
        }

        else {
            $img_url = 'https://placehold.co/372x240';
        }


    }
}
elseif (!empty($deafaultFeatImg)) {

    $img_url=$deafaultFeatImg;


} else {

    $img_url = 'https://placehold.co/372x240';
}

// End New update 2.6.10

$tags       =   get_the_terms( get_the_ID(), 'list-tags' );



$cats       =   get_the_terms( get_the_ID(), 'listing-category' );



$location   =   get_the_terms( get_the_ID(), 'location' );



$grid_class =   6;







if( isset( $GLOBALS['grid_col_class'] ) && $GLOBALS['grid_col_class'] != '' )



{



    $grid_class =   4;



}







$lp_title   =   get_the_title();



if(strlen( get_the_title() ) > 25)



{

    $lp_title   =   mb_substr(get_the_title(), 0, 25).'...';

}



$discount_data  =   get_post_meta( get_the_ID(), 'listing_discount_data', true);



$menu_check     =   get_post_meta( get_the_ID(), 'lp-listing-menu', true );

$phone          =   listing_get_metabox('phone');

$gAddress       =   listing_get_metabox('gAddress');

$lp_lat         =   listing_get_metabox_by_ID('latitude', get_the_ID());

$lp_lng         =   listing_get_metabox_by_ID('longitude', get_the_ID());



$PM_btns_all    =   '';

$is_phone       =   '';

$is_menu        =   '';

$is_gAddress    =   '';



if( !empty( $phone ) && !empty( $menu_check ) && is_array( $menu_check ) && !empty( $gAddress ) )

{

    $PMG_btns_both   =   'ok';

}

if( !empty( $phone ) )

{

    $is_phone   =   'ok';

}

if( !empty( $menu_check ) && is_array( $menu_check ) )

{

    $is_menu    =   'ok';

}

if( !empty( $gAddress ) )

{

    $is_gAddress    =   'ok';

}

?>



<div class="col-md-<?php echo esc_attr($grid_class); ?> margin-bottom-class grid_view_s4 loop-grid-clearfix" data-title="<?php echo get_the_title(); ?>" data-postid="<?php echo get_the_ID(); ?>"   data-lattitue="<?php echo esc_attr($latitude); ?>" data-longitute="<?php echo esc_attr($longitude); ?>" data-posturl="<?php echo get_the_permalink(); ?>">



    <div class="lp-listing">



        <div class="lp-listing-top">



            <a href="#" data-post-id="<?php echo get_the_ID(); ?>" data-post-type="list" class="lp-listing-favrt <?php if($favrt == 'yes'){echo 'remove-fav-v2';}else{echo 'add-to-fav-v2';} ?>">



                <i class="fa fa-heart<?php if($favrt != 'yes'){echo '-o';} ?>" aria-hidden="true"></i>



            </a>







            <?php



            if($cats):



                $counter    =   1;



                foreach ( $cats as $cat ):



                    $cat_link   =   get_term_link( $cat );



                    if( $counter == 1 ):



                        $cat_img = listing_get_tax_meta($cat->term_id,'category','image2');

                        $lp_default_map_pin = get_template_directory_uri() . '/assets/images/pins/lp-logo.png';

                        if( empty( $cat_img ) )

                        {

                            $cat_img =   $lp_default_map_pin;

                        }

                        if( !empty( $cat_img ) ):

                            echo '<span class="cat-icon" style="display:none;"><img class="icon icons8-Food" src="'.$cat_img.'" alt="cat-icon"></span>';



                            ?>



                            <a href="<?php echo esc_url($cat_link); ?>" class="lp-listing-cat simptip-position-top simptip-movable" data-tooltip="<?php echo esc_attr($cat->name); ?>"><img src="<?php echo esc_url($cat_img); ?>" alt="<?php echo esc_attr($cat->name); ?>"></a>



                        <?php endif; endif; ?>



                    <?php $counter++; endforeach; ?>



            <?php endif; ?>



            <div class="clearfix lp-listing-discount-outer">



                <?php echo listingpro_price_dynesty($post->ID); ?>

                <?php

                if( is_array( $discount_data ) && !empty( $discount_data ) ):

                    ?>

                    <div class="lp-listing-discount-range">

                        <span class="lp-listing-price-range-currency"><?php echo wp_kses_post($discount_data[0]['disOff']); ?></span>

                    </div>

                    <?php

                endif;

                ?>





            </div>



            <div class="lp-listing-top-thumb">



                <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>"></a>



            </div>



        </div>



        <div class="lp-listing-bottom">



            <?php

            if( $trending_el == true ) echo '<div class="lp-listing-bottom-inner">';

            ?>

            <?php

            if($cats):

                $counter    =   1;

                foreach ( $cats as $cat ):

                    $cat_link   =   get_term_link( $cat );

                    if( $counter == 1 ):

                        $cat_img = listing_get_tax_meta($cat->term_id,'category','image');

                        $lp_default_map_pin = get_template_directory_uri() . '/assets/images/pins/lp-logo.png';

                        if( empty( $cat_img ) )

                        {

                            $cat_img =   $lp_default_map_pin;

                        }

                        if( !empty( $cat_img ) ):
						
							if( hasFontAwesomeIconClass($cat_img) ){
								echo '<span class="cat-icon" style="display:none;"><i class="icon icons-search-cat '.$cat_img.'"></i></span>'; 
							}else{
								echo '<span class="cat-icon" style="display:none;"><img class="icon icons8-Food" src="' . $cat_img . '" alt="cat-icon"></span>';
							}
                            ?>

                            <a href="<?php echo esc_url($cat_link); ?>" class="lp-listing-cat"><?php echo esc_attr($cat->name); ?></a>

                        <?php else: ?><a href="<?php echo esc_url($cat_link); ?>" class="lp-listing-cat"><?php echo esc_attr($cat->name); ?></a>

                        <?php endif; endif; ?>

                    <?php $counter++; endforeach; ?>

            <?php endif; ?>

            <?php

            $tooltip_class    =     'red-tooltip';

            if( $openStatus != 'Close' )

            {

                $tooltip_class  =   'green-tooltip';

            }

            ?>

            <?php echo wp_kses_post($openStatus); ?>

            <div class="clearfix"></div>

            <h4><?php echo wp_kses_post($CHeckAd); ?> <a href="<?php the_permalink(); ?>"><?php echo esc_attr($lp_title); ?> <?php echo wp_kses_post($claim); ?></a></h4>



            <div class="lp-listing-cats">



                <?php



                if( $tags ):



                    $total_tags =   count($tags);



                    $counter    =   1;



                    foreach ( $tags as $tag ):



                        $tag_link   =   get_term_link( $tag );



                        $tag_name  =   $tag->name;



                        ?>



                        <a href="<?php echo esc_url($tag_link); ?>">



                            <?php echo esc_attr($tag_name); ?><?php if($counter != $total_tags){ echo ',';}; ?>



                        </a>



                        <?php $counter++; endforeach; else: echo '<a href=""></a>'; endif; ?>







            </div>



            <div class="lp-listing-stars">



                <?php



                $NumberRating = listingpro_ratings_numbers($post->ID);

                if( $NumberRating == 0 )

                {

                    ?>

                    <span class="lp-ann-btn lp-no-review-btn"><?php echo esc_html__( 'Be the first to review!', 'listingpro' ); ?></span>

                    <?php

                }

                else

                {

                    $rating = get_post_meta( get_the_ID(), 'listing_rate', true );

                    $rating_num_bg  =   '';

                    $rating_num_clr  =   '';



                    if( $rating < 3 ){ $rating_num_bg  =   'num-level1'; $rating_num_clr  =   'level1'; }

                    if( $rating < 4 ){ $rating_num_bg  =   'num-level2'; $rating_num_clr  =   'level2'; }

                    if( $rating < 5 ){ $rating_num_bg  =   'num-level3'; $rating_num_clr  =   'level3'; }

                    if( $rating >= 5 ){ $rating_num_bg  =   'num-level4'; $rating_num_clr  =   'level4'; }

                    ?>



                    <div class="lp-rating-stars-outer">

                        <span class="lp-star-box <?php if($rating >= 1){echo 'filled'.' '.$rating_num_clr;}?>"><i class="fa fa-star" aria-hidden="true"></i></span>



                        <span class="lp-star-box <?php if($rating >= 2){echo 'filled'.' '.$rating_num_clr;}?>"><i class="fa fa-star" aria-hidden="true"></i></span>



                        <span class="lp-star-box <?php if($rating >= 3){echo 'filled'.' '.$rating_num_clr;}?>"><i class="fa fa-star" aria-hidden="true"></i></span>



                        <span class="lp-star-box <?php if($rating >= 4){echo 'filled'.' '.$rating_num_clr;}?>"><i class="fa fa-star" aria-hidden="true"></i></span>



                        <span class="lp-star-box <?php if($rating >= 5){echo 'filled'.' '.$rating_num_clr;}?>"><i class="fa fa-star" aria-hidden="true"></i></span>

                    </div>

                    <span class="lp-rating-num"><?php echo esc_attr($rating); ?></span>

                    <?php

                }



                ?>



            </div>



            <?php



            if( $trending_el == true )



            {
            echo '<div class="lp-listing-location"><span>'. mb_substr($gAddress, 0, 60).'...</span></div>';
            }



            else



            {



                if($location):



                    $loc        =   $location[0];



                    $loc_link   =   get_term_link( $loc );



                    echo '<div class="lp-listing-location"><i class="fa fa-map-marker" aria-hidden="true"></i> <a href="'. $loc_link .'">'. $loc->name .'</a></div>';



                endif;



            }







            ?>



            <div class="clearfix"></div>



            <?php



            if( $trending_el == true ) echo '</div>';



            ?>



        </div>

        <?php

        if( ( !empty( $menu_check ) && is_array( $menu_check ) ) || ( !empty( $phone )  ) || ( !empty( $gAddress )  ) ):

            ?>

            <div class="lp-new-grid-bottom-button">

                <ul class="clearfix">



                    <?php

                    if( $is_phone == 'ok' ):

                        ?>

                        <li onclick="myFuction(this)" class="show-number-wrap hereIam" style="<?php //if( $is_menu == '' ){ echo 'width:100%;'; } ?>">

                            <p><i class="fa fa-phone" aria-hidden="true"></i> <span class="show-number"><?php esc_html_e('call','listingpro'); ?></span><a href="tel:<?php echo esc_attr($phone); ?>" class="grind-number"><?php echo esc_attr($phone); ?></a></p>

                        </li>

                    <?php endif; ?>

                    <?php

                    if( $is_gAddress == 'ok' ):

                        ?>

                        <li style="<?php //if( $is_phone == '' ){ echo 'width:50%;'; } ?>">

                            <a href="" data-lid="<?php echo get_the_ID(); ?>" data-lat="<?php echo esc_attr($lp_lat); ?>" data-lng="<?php echo esc_attr($lp_lng); ?>" class="show-loop-map-popup"><i class="fa fa-map-pin" aria-hidden="true"></i> <?php esc_html_e('Show Map','listingpro'); ?></a>

                        </li>

                    <?php endif; ?>

                    <?php

                    if( $is_menu == 'ok' ):

                        ?>

                        <li style="<?php //if( $is_phone == '' ){ echo 'width:50%;'; } ?>">

                            <a href="<?php echo get_permalink( get_the_ID() ); ?>/#lp-listing-menuu-wrap"><i class="fa fa-cutlery" aria-hidden="true"></i> <?php esc_html_e('View Menu','listingpro'); ?></a>

                        </li>

                    <?php endif; ?>

                </ul>

            </div>

        <?php endif; ?>

    </div>



</div>



