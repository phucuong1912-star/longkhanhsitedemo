<?php

global $listingpro_options;

$lp_detail_page_styles = $listingpro_options['lp_detail_page_styles'];



$plan_id = listing_get_metabox_by_ID('Plan_id',get_the_ID());
$gallery_show = get_post_meta( $plan_id, 'gallery_show', true );

if(!empty($plan_id)){

    $plan_id = $plan_id;

}else{

    $plan_id = 'none';

}

if( $plan_id == 'none' )

{
    $gallery_show   =   'true';
}

if( $gallery_show == 'false' ) return false;



$IDs = get_post_meta( $post->ID, 'gallery_image_ids', true );



if( !empty( $IDs )):


	
	$imgIDs = array();
    $numImages = 0;
    $ximgIDs = explode(',',$IDs);
    if(!empty($ximgIDs)){
        foreach ($ximgIDs as $value) {
            if (!empty(get_post_type($value)) && get_post_type($value) == 'attachment') {
                $imgIDs[] = $value;
            }
        }

        if(!empty($imgIDs)){
           $numImages = count($imgIDs);  
        }
    }

    require_once (THEME_PATH . "/include/aq_resizer.php");



    ?>



    <div class="lp-listing-slider" data-totalSlides="<?php echo esc_attr( $numImages ); ?>">

        <?php

        if( $numImages == 1 )

        {

            $img_url    = wp_get_attachment_image_src( $imgIDs[0], 'full');

            $img_thumb  = aq_resize( $img_url[0], '780', '270', true, true, true);

            if( !filter_var($img_thumb, FILTER_VALIDATE_URL) ){
              $img_thumb = $img_url[0];
            }

            ?>

            <div class="col-md-12 lp-listing-slide-wrap">

                <div class="lp-listing-slide">

                    <a href="<?php echo esc_attr( $img_url[0] ); ?>" rel="prettyPhoto[gallery1]">

                        <img src="<?php echo esc_attr( $img_thumb ); ?>" alt="<?php the_title(); ?>">

                    </a>

                </div>

            </div>

            <?php

        }

        elseif( $numImages == 2 )

        {

            foreach ($imgIDs as $imgID):

                $img_url    = wp_get_attachment_image_src( $imgID, 'full');

                $img_thumb  = aq_resize( $img_url[0], '370', '270', true, true, true);
                if($img_url):

                    ?>

                    <div class="col-md-6 lp-listing-slide-wrap">

                        <div class="lp-listing-slide">

                            <a href="<?php echo esc_attr( $img_url[0] ); ?>" rel="prettyPhoto[gallery1]">

                                <img src="<?php echo esc_attr( $img_thumb ); ?>" alt="<?php the_title(); ?>">

                            </a>

                        </div>

                    </div>

                <?php endif; ?>

            <?php endforeach;

        }

        else

        {

            foreach ($imgIDs as $imgID):

                $img_url    = wp_get_attachment_image_src( $imgID, 'full');

                $img_thumb  = aq_resize( $img_url[0], '245', '270', true, true, true);

                if($img_url):

                    ?>

                    <div class="col-md-4 lp-listing-slide-wrap">

                        <div class="lp-listing-slide">

                            <a href="<?php echo esc_attr( $img_url[0] ); ?>" rel="prettyPhoto[gallery1]">

                                <img src="<?php echo esc_attr( $img_thumb ); ?>" alt="<?php the_title(); ?>">

                            </a>

                        </div>

                    </div>

                <?php endif; ?>

            <?php endforeach;

        }

        ?>

    </div>

<?php

else:



endif;


