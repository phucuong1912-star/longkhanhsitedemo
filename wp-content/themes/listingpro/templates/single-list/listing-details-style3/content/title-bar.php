<div class="lp-listing-title">
    <?php
    $b_logo =   $listingpro_options['business_logo_switch'];
    $plan_id = listing_get_metabox_by_ID('Plan_id',get_the_ID());
    $tagline_show = get_post_meta( $plan_id, 'listingproc_tagline', true );
    if(!empty($plan_id)){
        $plan_id = $plan_id;
    }else{
        $plan_id = 'none';
    }
    if( $plan_id == 'none' )
    {
        $tagline_show   =   true;
    }
    if( $b_logo ==true):
        $business_logo_url  =   '';
        $page_style =   $listingpro_options['listing_submit_page_style'];
        
        $b_logo_default =   $listingpro_options['business_logo_default']['url'];

        $business_logo = listing_get_metabox_by_ID('business_logo',get_the_ID());
        $image_thumb = '';
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $business_logo ));
        if(!empty($attachment)){
            $image_thumb = wp_get_attachment_image_src($attachment[0], 'thumbnail');
        }
        if( empty( $business_logo ) )
        {
            $business_logo_url  =   $b_logo_default;
        }
        else
        {
            $business_logo_url  =   $business_logo;
        }
        if( !empty( $business_logo_url ) ):
            ?>
            <div class="lp-listing-logo">
                <img src="<?php echo esc_attr( $business_logo_url ); ?>" alt="Listing Logo">
            </div>
        <?php endif; endif; ?>
    <div class="lp-listing-name">
        <h1><?php the_title(); ?> <?php echo wp_kses_post( $claim ); ?></h1>
        <?php
        if( !empty( $tagline_show ) && $tagline_show == 'true' )
        {
            ?>
            <p class="lp-listing-name-tagline"><?php echo esc_attr( $tagline_text ); ?></p>
            <?php
        }
        ?>
    </div>
    <?php
    $allowedReviews = $listingpro_options['lp_review_switch'];
    if( !empty( $allowedReviews ) && $allowedReviews == 1 && get_post_status( $post->ID )== "publish" ):
        ?>
        <div class="lp-listing-title-rating">
            <?php if( $NumberRating > 0 ): ?><span class="lp-rating-avg <?php echo esc_attr( $rating_num_bg ); ?>"><?php echo esc_attr( $rating ); ?>/<sub>5</sub></span><span class="lp-rating-count"><?php echo esc_attr( $NumberRating ); ?> <?php echo esc_html__( 'Reviews', 'listingpro' ); ?></span><?php endif; ?>
            <?php
            if( $NumberRating == 0 ):
                ?>
                <span class="lp-rating-count zero-with-top-margin"><?php echo esc_html__( 'Be the first to review', 'listingpro' ); ?></span>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="clearfix"></div>
</div>