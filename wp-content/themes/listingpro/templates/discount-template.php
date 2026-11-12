<?php

if( isset( $GLOBALS['listing_discount_data'] ) && !empty( $GLOBALS['listing_discount_data'] ) )

{

    $listing_discount_data  =   $GLOBALS['listing_discount_data'];

}

$img_url    =   'https://placehold.co/100x100';

if( !empty( $listing_discount_data['disImg'] ) )

{

    $img_url    =   $listing_discount_data['disImg'];

}

?>

<div class="col-md-4 col-sm-12">

    <div class="lp-widget lp-discount-widget">

        <div class="lp-discount-top">

            <?php

            if( !empty( $listing_discount_data['disHea'] ) ){ echo '<strong>'. $listing_discount_data['disHea'] .'</strong>'; }

            ?>

            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo wp_kses_post($listing_discount_data['disHea']); ?>">

        </div>

        <div class="lp-discount-bottom">

            <?php

            if( !empty( $listing_discount_data['disDes'] ) ){ echo wp_kses_post($listing_discount_data['disDes']); }

            ?>



            <p><?php echo esc_html__( 'Expires', 'listingpro' ); echo date( 'd/m/Y', $listing_discount_data['disExp'] ); ?></p>

            <div class="lp-discount-count-wrap">

                <div class="lp-discount-countdown"

                     data-label-hours="<?php esc_html__('hours', 'listingpro'); ?>"

                     data-label-mins="<?php esc_html__('mins', 'listingpro'); ?>"

                     data-label-secs="<?php esc_html__('secs', 'listingpro'); ?>"

                     data-day="<?php echo date('d', $listing_discount_data['disExp'] ); ?>"

                     data-month="<?php echo date('m', $listing_discount_data['disExp'] )-1; ?>"

                     data-year="<?php echo date('Y', $listing_discount_data['disExp'] ); ?>"></div>

            </div>

        </div>

        <?php

        if( isset( $listing_discount_data['disBT'] ) && !empty( $listing_discount_data['disBT'] ) ):

            ?>

            <a href="<?php if( isset( $listing_discount_data['disBT'] ) && !empty( $listing_discount_data['disBT'] ) ):  endif; ?>" class="lp-discount-btn"><?php echo wp_kses_post($listing_discount_data['disBT']); ?></a>

        <?php endif; ?>

    </div>

</div>