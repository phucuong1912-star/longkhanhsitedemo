<?php

$latitude = listing_get_metabox('latitude');

$longitude = listing_get_metabox('longitude');

$gAddress = listing_get_metabox('gAddress');

$isfavouriteicon = listingpro_is_favourite_grids(get_the_ID(),$onlyicon=true);

$isfavouritetext = listingpro_is_favourite_grids(get_the_ID(),$onlyicon=false);

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



if( $claimed_section == 'claimed') {

    if(is_singular( 'listing' ) ){

        $claimStatus = esc_html__('Claimed', 'listingpro');

    }

    $claim = '<i class="fa fa-check-circle" aria-hidden="true"></i>';



}elseif($claimed_section == 'not_claimed') {

    $claim = '';



}

$img_url    =   'https://placehold.co/84x84';

if ( has_post_thumbnail()) {

    $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'lp-sidebar-thumb-v2');

    if (!empty($image[0])) {

        $img_url = $image[0];

    }

}

?>

<div class="lp-listing">

    <div class="lp-listing-thumb">

        <a href="<?php the_permalink(); ?>"><img src="<?php echo esc_url($img_url); ?>" alt="<?php the_title(); ?>"></a>

    </div>

    <div class="lp-listing-detail">

        <?php

        $l_title    =   get_the_title();

        if( strlen( $l_title ) > 25 )

        {

            $l_title    =   mb_substr( $l_title, 0, 25 ).'...';

        }

        ?>

        <h6><?php echo wp_kses_post($CHeckAd); ?> <a href="<?php the_permalink(); ?>"><?php echo esc_attr($l_title); ?></a> <?php echo wp_kses_post($claim); ?></h6>

		
        <?php
        if( isset( $gAddress ) && !empty( $gAddress ) ):
        ?>
        <div class="lp-listing-location clearfix"><i class="fa fa-map-marker" aria-hidden="true"></i> <a href="#"><?php echo mb_substr( $gAddress, 0, 32 ).'...'; ?></a>
		
		 <?php
            if( isset( $calDistance ) && isset( $calParam ) ):
            ?>
            <span class="lp-listing-miles"><?php echo wp_kses_post($calDistance['distance'].' '.$calParam); ?></span>
            <?php endif; ?>
		
		</div>
        <?php endif; ?>
		
    </div>
    <div class="clearfix"></div>
</div>