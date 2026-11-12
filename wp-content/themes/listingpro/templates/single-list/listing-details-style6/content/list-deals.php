<?php
global $listingpro_options;
$lp_detail_page_styles  =   $listingpro_options['lp_detail_page_styles'];
$listing_discount_data_init  =   get_post_meta( get_the_ID(), 'listing_discount_data', true );

$listing_discount_data_final    =   array();

$timezone_string = get_option('timezone_string');
if(isset($timezone_string) && !empty($timezone_string)){
    date_default_timezone_set($timezone_string);
}
$strNow =  strtotime("NOW");


if( !empty( $listing_discount_data_init ) ):
    foreach ( $listing_discount_data_init as $key => $val )
    {
        if( isset($val['disExpE'] ) && isset($val['disTimeE']) && isset($val['disTimeS']) ) :

                $couponExpiryE  = coupon_timestamp($val['disExpE'],$val['disTimeE']);

                $couponExpiryS  = coupon_timestamp($val['disExpS'],$val['disTimeS']);

            if( ( $strNow < $couponExpiryE || empty( $val['disExpE'] ) ) && ( $strNow > $couponExpiryS || empty( $val['disExpS'] ) ) ) :
                $listing_discount_data_final[]  =   $val;
            endif;
        endif;
    }

    if( is_array( $listing_discount_data_final ) && !empty( $listing_discount_data_final ) ):

        require_once (THEME_PATH . "/include/aq_resizer.php");
        ?>



        <?php

        $deal_counter   =   1;
        $total_deals    =   count( $listing_discount_data_final );

        $col_class      =   'col-md-6';

        $post_author_id = get_post_field( 'post_author', get_the_ID() );
        $discount_displayin =   get_user_meta( $post_author_id, 'discount_display_area', true );
        if( $discount_displayin == 'sidebar' )
        {
            $col_class   =   'col-md-12';
        }
        foreach ( $listing_discount_data_final as $key => $discount_data ):
            if( $deal_counter == 1 ):
                ?>

                <h4 class="lp-detail-section-title-classic"><?php echo esc_html__( 'Deals', 'listingpro' ); ?></h4>
                <div class="lp-deals-wrap">
                <div class="row">
            <?php endif; ?>
            <?php

            $img_url    =   'https://placehold.co/360x260';

            if( $discount_data['disImg'] )

            {

                $img_url    =   $discount_data['disImg'];

                $img_url  = aq_resize( $img_url, '360', '260', true, true, true);

            }



            ?>

            <div class="<?php echo esc_attr( $col_class ); ?>">

                <div class="lp-deal lp-classic-discount-grids">

                    <div class="deal-thumb">

                        <img alt="image" src="<?php echo esc_attr( $img_url ); ?>">

                    </div>

                    <div class="deal-details">

                        <?php
                        if( !empty( $discount_data['disExpE'] ) ):
                            $exTime = strtotime('12:00 AM');
                            if (!empty($discount_data['disTimeE'])) {
                                $exTime = strtotime($discount_data['disTimeE']);
                            }
                            $couponExpiry  = coupon_timestamp($discount_data['disExpE'],$exTime);
                            ?>
                            <div class="deal-countdown-wrap">
                                <div id="lp-deals-countdown<?php echo esc_attr( $deal_counter ); ?>" class="lp-countdown lp-deals-countdown<?php echo esc_attr( $deal_counter ); ?>"
                                     data-label-days="<?php echo esc_html__('days', 'listingpro'); ?>"
                                    data-label-hours="<?php echo esc_html__('hours', 'listingpro'); ?>"
                                    data-label-mints="<?php echo esc_html__('min', 'listingpro') ?>"
                                     data-minute = "<?php echo date( 'i', $couponExpiry ); ?>"
                                     data-hour = "<?php echo date( 'H', $couponExpiry ); ?>"
                                     data-day="<?php echo date( 'd', $couponExpiry ); ?>"
                                     data-month="<?php echo date( 'm', $couponExpiry )-1; ?>"
                                     data-year="<?php echo date( 'Y', $couponExpiry ); ?>"></div>
                            </div>

                        <?php endif; ?>

                        <?php

                        if( $discount_data['disOff'] ) echo '<span class="lp-deal-off">'. $discount_data['disOff'] .'</span>';

                        ?>

                        <div class="deal-content">

                            <?php

                            if( $discount_data['disHea'] ) echo '<strong>'. $discount_data['disHea'] .'</strong>';

                            html_entity_decode(mb_substr( $discount_data['disDes'], 0, 35 ));

                            global $listingpro_options;
                            $listing_mobile_view            =   $listingpro_options['single_listing_mobile_view'];
                            //if( $discount_data['disDes'] ) echo '<p>'. html_entity_decode(mb_substr( $discount_data['disDes'], 0, 35 )) .'</p>';

                            if( $listing_mobile_view == 'app_view' && wp_is_mobile() ){

                            }
                            else{

                                if( $discount_data['disDes'] ) echo '<p>'. html_entity_decode($discount_data['disDes']) .'</p>';
                            }


                            $btn_href   =   '';

                            $btn_class  =   'lp-copy-code';
							$bnt_text_def   =   esc_html__( 'Click Here', 'listingpro' );
                           if( empty( $discount_data['disBT'] ) )
                           {
                               $discount_data['disBT'] =   $bnt_text_def;
                           }

                            if( $discount_data['disBL'] && !empty( $discount_data['disBL'] ) )

                            {

                                $btn_href   =   'href="'. $discount_data['disBL'] .'"';

                                $btn_class  =   '';

                            }

                            ?>

                            <a target="_blank" data-html="<?php echo esc_attr( $discount_data['disBT'] ); ?>" data-target-code="deal-copy-<?php echo esc_attr( $deal_counter ); ?>" <?php echo wp_kses_post( $btn_href ); ?> class="deal-button <?php echo esc_attr( $btn_class ); ?>"><i class="fa fa-gavel" aria-hidden="true"></i> <?php echo esc_attr( $discount_data['disBT'] ); ?></a>

                        </div>

                    </div>
                    <?php
                    if( empty( $discount_data['disBL'] ) ):
                        ?>
                        <div class="dis-code-copy-pop deal-copy-<?php echo esc_attr( $deal_counter ); ?>" id="dicount-copy-<?php echo esc_attr( $deal_counter ); ?>">
                            <span class="close-right-icon" data-target="deal-copy-<?php echo esc_attr( $deal_counter ); ?>"><i class="fa fa-times"></i></span>
                            <div class="dis-code-copy-pop-inner">
                                <div class="dis-code-copy-pop-inner-cell">
                                    <p><?php echo esc_html__( 'Copy to clipboard', 'listingpro' ); ?></p>
                                    <p class="dis-code-copy-wrap"><input class="code-top-copy-<?php echo esc_attr( $deal_counter ); ?>" type="text" value="<?php echo esc_attr( $discount_data['disCod'] ); ?>"> <a data-target-code="dicount-copy-<?php echo esc_attr( $deal_counter ); ?>" href="#" class="copy-now" data-coppied-label="<?php echo esc_html__( 'Copied', 'listingpro' ); ?>"><?php echo esc_html__( 'Copy', 'listingpro' ); ?></a></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php $deal_counter++; ?>

            <?php

            if( $deal_counter == $total_deals+1 )

            {

                echo '<div class="clearfix"></div></div>

                            </div>';

            }

            ?>

        <?php endforeach;; ?>

        <?php

    endif; endif;
?>