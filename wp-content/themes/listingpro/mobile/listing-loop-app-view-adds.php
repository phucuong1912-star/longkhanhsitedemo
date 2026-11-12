<?php
global $post;
$thisCatsArray = array();
$thisAdCatArray = array();
$lpThisPost = $post->ID;
$lpThisPostPlan = listing_get_metabox_by_ID('Plan_id', $lpThisPost);
$restrictCampaign = get_post_meta($lpThisPostPlan, 'listingproc_plan_campaigns', true);

$showthisadinSidebar = true;

if (!empty($showthisadinSidebar)) {
    $output = null;
    global $listingpro_options;
    $lp_review_switch = $listingpro_options['lp_review_switch'];


    $postGridCount2 = '';
    if(!isset($postGridCount2)){
        $postGridCount2 = '0';
    }
    global $postGridCount2;
    $postGridCount2++;

    $deafaultFeatImg = lp_default_featured_image_listing();


    $listing_style = '';
    $listing_style = $listingpro_options['listing_style'];
    if(isset($_GET['list-style']) && !empty($_GET['list-style'])){
        $listing_style = esc_html($_GET['list-style']);
    }
    if(is_front_page()){
        $listing_style = 'col-md-4 col-sm-6';
        $postGridnumber = 3;
    }else{
        if($listing_style == '1'){
            $listing_style = 'col-md-4 col-sm-6';
            $postGridnumber = 3;
        }elseif($listing_style == '3' && !is_page()){
            $listing_style = 'col-md-6 col-sm-12';
            $postGridnumber = 2;
        }else{
            $listing_style = 'col-md-4 col-sm-6';
            $postGridnumber =3;
        }
    }
    if(is_page_template('template-favourites.php')){
        $listing_style = 'col-md-4 col-sm-6';
        $postGridnumber =3;
    }
    $latitude = listing_get_metabox('latitude');
    $longitude = listing_get_metabox('longitude');
    $gAddress = listing_get_metabox('gAddress');
    $isfavouriteicon = listingpro_is_favourite_grids(get_the_ID(),$onlyicon=true);
    $isfavouritetext = listingpro_is_favourite_grids(get_the_ID(),$onlyicon=false);

    $adStatus = apply_filters('lp_get_ad_status', '', get_the_ID());
    
    $CHeckAd = '';
    $adClass = '';
    if($adStatus == 'active'){
        $CHeckAd = '<span class="listing-pro">'.esc_html__('Ad','listingpro').'</span>';
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
    $phone = listing_get_metabox('phone');

    $priceRange = listing_get_metabox_by_ID('price_status', get_the_ID());
    $listingpTo = listing_get_metabox('list_price_to');
    $listingprice = listing_get_metabox_by_ID('list_price', get_the_ID());
    $lp_priceSymbol = $listingpro_options['listing_pricerange_symbol'];
    $title_pricy    =   '';
    if( ($priceRange != 'notsay' && !empty($priceRange)) || !empty($listingpTo) || !empty($listingprice) )
    {
        $dollars = '';
        if( $priceRange == 'notsay' ){
            $dollars = '';

        }elseif( $priceRange == 'inexpensive' ){
            $dollars = '1';

        }elseif( $priceRange == 'moderate' ){
            $dollars = '2';

        }elseif( $priceRange == 'pricey' ){
            $dollars = '3';

        }elseif( $priceRange == 'ultra_high_end' ){
            $dollars = '4';
        }
        $title_pricy    .=  '<span class="active">';
        for ($i=0; $i < $dollars ; $i++) {
            $title_pricy    .=  wp_kses_post( $lp_priceSymbol );
        }
        $title_pricy    .=  '</span>';

        $title_pricy    .=  '<span class="grayscale">';
        $greyDollar = 4 - $dollars;
        for($i=1;$i<=$greyDollar;$i++){
            $title_pricy    .= wp_kses_post($lp_priceSymbol);
        }
        $title_pricy    .=  '</span>';
    }

    $favrt = '';
    $favrt  =   listingpro_is_favourite_v2(get_the_ID());
    ?>

    <div data-counter="<?php echo esc_attr($postGridCount2); ?>" class="<?php echo esc_attr($adClass); ?> lp-grid-box-contianer clearfix view-toggle card1 lp-grid-box-contianer1 lp-grid-app-view" data-title="<?php echo get_the_title(); ?>" data-postid="<?php echo get_the_ID(); ?>"   data-lattitue="<?php echo esc_attr($latitude); ?>" data-longitute="<?php echo esc_attr($longitude); ?>" data-posturl="<?php echo get_the_permalink(); ?>">
        <div class="gaddress" style="display:none"><?php echo esc_attr($gAddress); ?></div>
        <div class="listing-app-view-new-wrap listing-app-view-new-wrap-add">
            <a href="#" data-post-id="<?php echo get_the_ID(); ?>" data-post-type="list" class="lp-listing-favrt <?php if($favrt == 'yes'){echo 'remove-fav-v2';}else{echo 'add-to-fav-v2';} ?>">
                <i class="fa fa-heart<?php if($favrt != 'yes'){echo '-o';} ?>" aria-hidden="true"></i>
            </a>
            <?php
            $NumberRating = listingpro_ratings_numbers($post->ID);
            if( $NumberRating != 0 )
            {
                $rating = get_post_meta( $post->ID, 'listing_rate', true );
                echo '<span class="listing-app-view-new-rating"><i class="fa fa-star" aria-hidden="true"></i> '. $rating .'</span>';
            }

            $listing_discount      =   array();
            $listing_discount_data  =   get_post_meta( get_the_ID(), 'listing_discount_data', true );
            if( is_array( $listing_discount_data ) && !empty( $listing_discount_data ) )
            {
                $strNow =   strtotime("NOW");
                foreach ( $listing_discount_data as $k => $v )
                {
                    if( $strNow < $v['disExpE'] )
                    {
                        $listing_discount   =   $v;
                        ?>
                        <div class="listing-app-view-new-discount <?php if( $NumberRating == 0 ){echo "no-rate-dis";} ?>">
                            <i class="fa fa-tags col-own-1"></i>
                            <span class="spantext col-own-2">
                        <?php
                        if( isset( $listing_discount['disOff'] ) ) {
                            echo wp_kses_post($listing_discount['disOff']);
                        } else {
                            echo wp_kses_post($listing_discount['disHea']);
                        }
                        ?>
                    </span>
                            <i class="fa fa-ellipsis-v col-own-3"></i>
                        </div>
                        <?php
                        break;
                    }
                }
            }
            ?>
            <div class="listing-app-view-new-thumb">
                <?php if( is_array( $listing_discount_data ) && !empty( $listing_discount_data ) ){ ?>
                    <div class="popover_overlay">
                        <div class="arrow <?php if( $NumberRating == 0 ){echo "no-rate-dis-arrow";} ?>"></div>
                        <div class="popover_overlay_head">
                            <?php echo wp_kses_post($listing_discount['disHea']); ?>
                        </div>
                        <hr>
                        <div class="popover_overlay_des">
                            <?php echo wp_kses_post($listing_discount['disDes']); ?>
                        </div>
                    </div>
                <?php } ?>
                <?php 
					$NumberRating = listingpro_ratings_numbers($post->ID);
				if( $NumberRating != 0 )
				{
					$rating = get_post_meta( $post->ID, 'listing_rate', true );
					echo '<span class="listing-app-view-new-rating listing-app-view-new-rating-bottom"><i class="fa fa-star" aria-hidden="true"></i> '. $rating .'</span>';
				}
				?>
                <div class="thumb-overlay"></div>
                <?php
                if ( has_post_thumbnail()) {

                    $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'listingpro-blog-grid' );
                    if(!empty($image[0])){
                        echo "<a href='".get_the_permalink()."' ><img alt='image' src='" . $image[0] . "' /></a>";
                    }else {
                        echo '<a href="'.get_the_permalink().'" ><img src="'.esc_html__('https://placehold.co/370x240', 'listingpro').'" alt="image"></a>';
                    }
                }elseif(!empty($deafaultFeatImg)){
                    echo "<a href='".get_the_permalink()."' ><img alt='image' src='".$deafaultFeatImg."' /></a>";
                }else {
                    echo '<a href="'.get_the_permalink().'" ><img src="'.esc_html__('https://placehold.co/370x240', 'listingpro').'" alt="image"></a>';
                }
                ?>
            </div>
            <div class="listing-app-view-new-details">
                <div class="details-inner-app-view-new">
                    <?php echo wp_kses_post($CHeckAd); ?>
                    <?php
                    $cats = get_the_terms( get_the_ID(), 'listing-category' );
                    if( !empty( $cats ) ){
                        $catCount = 1;
                        ?>
                        <span class="listing-app-view-new-details-cats">
                        <?php
                        global $listingpro_options;
                        $lp_default_map_pin = $listingpro_options['lp_map_pin'];
                        echo '<input type="hidden" id="def_map_marker_icon" value="' . $lp_default_map_pin['url'] . '">';
                        foreach ($cats as $cat) {
                            $cat_img = listing_get_tax_meta($cat->term_id, 'category', 'image');
                            if($catCount==1){
                                $term_link = get_term_link( $cat );
								if (!empty($cat_img)) {
									if( hasFontAwesomeIconClass($cat_img) ){
										echo '<span class="cat-icon" style="display:none;"><i class="icon icons-search-cat '.$cat_img.'"></i></span>';
									}else{
										echo '<span class="cat-icon" style="display:none;"><img class="icon icons8-Food" src="'.$cat_img.'" alt="cat-icon"></span>';
									}
								}
                               echo '<a href="'.$term_link.'">'.$cat->name.'</a>';
                            }
                            $catCount++;
                        }
                        ?>
                    </span>
                    <?php } ?>

                    <h4 class="lp-h4">
                        <a href="<?php echo get_the_permalink(); ?>">
                            <?php echo wp_kses_post($title_pricy); ?>
                            <?php echo mb_substr(get_the_title(), 0, 20);?>
                            <?php echo wp_kses_post($claim); ?>
                        </a>
                    </h4>
                    <div class="listing-app-view-new-details-bottom">
                        <?php
                        $locs = get_the_terms( get_the_ID(), 'location' );

                        if(!empty($locs))
                        {
                            $locCount = 1;
                            foreach ( $locs as $loc ) {
                                if($locCount==1){
                                    $term_link = get_term_link( $loc );
                                    echo '<a href="'.$term_link.'" class="listing-app-view-new-details-bottom-loc"><i class="fa fa-map-marker" aria-hidden="true"></i> '.$loc->name.'</a>';
                                }
                                $locCount++;
                            }
                        }
                        if( !empty( $phone ) )
                        {
                            echo '<a href="tel:'. $phone .'" class="listing-app-view-new-details-bottom-phone"><i class="fa fa-phone" aria-hidden="true"></i> '.esc_html__( 'Call Now', 'listingpro' ).'</a>';
                        }
                        ?>
                    </div>
                </div>

            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <?php

}
?>