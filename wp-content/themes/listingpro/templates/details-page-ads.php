<?php

global $wp_query;
$thisCatsArray = array();
$thisAdCatArray = array();
$lpThisPost = $wp_query->post->ID;
$lpThisPostPlan = listing_get_metabox_by_ID('Plan_id', $lpThisPost);
$restrictCampaign = get_post_meta($lpThisPostPlan, 'listingproc_plan_campaigns', true);
$showthisadinSidebar = true;
if(!empty($restrictCampaign)){
	if($restrictCampaign!="false"){
		$thisCats = get_the_terms( $lpThisPost, 'listing-category' );
		if(!empty($thisCats)){
			foreach($thisCats as $thisCat){
				$thisCatsArray[] = $thisCat->term_id;
			}
		}

		/* for campagins cats */
		$thisCatss = get_the_terms( get_the_ID(), 'listing-category' );
		if(!empty($thisCatss)){
			foreach($thisCatss as $thisCat){
				$thisAdCatArray[] = $thisCat->term_id;
			}
		}


		if(!empty($thisCatsArray) && !empty($thisAdCatArray)){
			$checkCommon = array_intersect($thisCatsArray, $thisAdCatArray);
			if (count($checkCommon) > 0) {
				$showthisadinSidebar = false;
			}
		}
	}

}

if(!empty($showthisadinSidebar)){
	global $listingpro_options;



	$gAddress = listing_get_metabox_by_ID('gAddress',$post->ID);
	$rating = get_post_meta( get_the_ID(), 'listing_rate', true );
	$rate = $rating;

	$priceRange = listing_get_metabox_by_ID('price_status', $post->ID);
	$listingpTo = listing_get_metabox_by_ID('list_price_to', $post->ID);
	$listingprice = listing_get_metabox_by_ID('list_price', $post->ID);

	$deafaultFeatImg = lp_default_featured_image_listing();

	if(has_post_thumbnail()) {
		$imageAlt = lp_get_the_post_thumbnail_alt(get_the_ID());
		$images = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'listingpro-gallery-thumb2' );
		$image = '<img src="'.$images[0].'" alt="'.$imageAlt.'">';
	}elseif(!empty($deafaultFeatImg)){
		$image = $deafaultFeatImg;
		$image = '<img src="'.esc_url($image).'" alt="image">';

	}else {
		$image = '<img src="'.esc_url('https://placehold.co/360x198').'" alt="Listing Pro Placeholder">';
	}
        
        $current_id             = get_current_user_id();
        $listindUserID          = get_post_field( 'post_author', get_the_ID() );
        $default_typeofcampaign = lp_theme_option('listingpro_ads_campaign_style');
        $adID                   = listing_get_metabox_by_ID('campaign_id', $post->ID);
        $typeofcampaign         = listing_get_metabox_by_ID('ads_mode', $adID);
        if(isset($typeofcampaign) && !empty($typeofcampaign)){
            $typeofcampaign = 'ads'.$typeofcampaign;
        }else{
            $typeofcampaign = $default_typeofcampaign;
        }
        
        $front_page_adStatus   = get_post_meta( get_the_ID(), 'lp_random_ads', true );
        $search_page_adStatus  = get_post_meta( get_the_ID(), 'lp_top_in_search_page_ads', true );
        $detail_page_adStatus  = get_post_meta( get_the_ID(), 'lp_detail_page_ads', true );
        $campaign_status       = get_post_meta( get_the_ID(), 'campaign_status', true );
        
        $ad_listing_url = esc_url(get_the_permalink());
        if (strstr($ad_listing_url, "?")) {
            $ad_listing_url = strstr($ad_listing_url, "?", true);
        }
        
        if( $campaign_status == 'active' && $typeofcampaign == 'adsperclick' && $listindUserID != $current_id ){
            if($detail_page_adStatus == 'active'){
                $ad_listing_url = esc_url(add_query_arg (array( 'ad_type' => 'lp_detail_page_ads_pc' ), get_the_permalink()));
            }
        }
        ?>
    <article class="<?php echo esc_attr($restrictCampaign); ?>">
        <figure>
            <a href="<?php echo get_the_permalink(); ?>">
				<?php echo wp_kses_post($image); ?>
            </a>
            <figcaption>
                <a href="<?php echo $ad_listing_url; ?>" class="overlay-link"></a>
                <div class="listing-price">
					<?php
					if(!empty($listingprice)){
						echo esc_html($listingprice);
						if(!empty($listingpTo)){
							echo ' - ';
							echo esc_html($listingpTo);
						}
					}
					?>
                </div>
				<?php
				$adStatus = apply_filters('lp_get_ad_status', '', get_the_ID());
				$CHeckAd = '';
				if($campaign_status == 'active' && $detail_page_adStatus == 'active'){
                                    $CHeckAd = '<span class="listing-pro">'.esc_html__('Ad','listingpro').'</span>';
                                    echo wp_kses_post($CHeckAd);
				}
				?>
                <div class="bottom-area">
                    <div class="listing-cats">
						<?php
						$cats = get_the_terms( get_the_ID(), 'listing-category' );
						if(!empty($cats)){
							$catCount = 1;
							foreach($cats as $cat) {
								if($catCount==1 && $cat->parent==0){
									?>
                                    <a href="<?php echo get_term_link($cat); ?>" class="cat"><?php echo esc_attr($cat->name); ?></a>
									<?php
									$catCount++;
								}
							}
						} ?>
                    </div>
					<?php if( !empty($rate) && $rate > 0 ) { ?>
                        <span class="rate"><?php echo esc_attr($rate); ?></span>
					<?php } ?>
                    <h4><a href="<?php echo $ad_listing_url; ?>"><?php echo mb_substr(get_the_title(), 0, 45); ?></a></h4>
					<?php if(!empty($gAddress)) { ?>
                        <div class="listing-location">
                            <p><?php echo wp_kses_post($gAddress) ?></p>
                        </div>
					<?php } ?>
                </div>
            </figcaption>
        </figure>
    </article>

	<?php
}
?>
