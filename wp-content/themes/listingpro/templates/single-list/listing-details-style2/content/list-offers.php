<?php
global $listingpro_options;
$listing_discount_data_init  =   get_post_meta( get_the_ID(), 'listing_discount_data', true );
$lp_detail_page_styles  =   $listingpro_options['lp_detail_page_styles'];

$listing_discount_data_final    =   array();
$timezone_string = get_option('timezone_string');
if(isset($timezone_string) && !empty($timezone_string)){
    date_default_timezone_set($timezone_string);
}
$strNow =   strtotime("NOW");


if( !empty( $listing_discount_data_init ) ):


    foreach ( $listing_discount_data_init as $key => $val )
    {
        if( isset($val['disExpE'] ) && isset($val['disTimeE']) && isset($val['disTimeS']) ) :
            
            $couponExpiryE  = coupon_timestamp($val['disExpE'],$val['disTimeE']);
            $couponExpiryS  = coupon_timestamp($val['disExpS'],$val['disTimeS']);

            if( ( $strNow < $val['disExpE'] || empty( $couponExpiryE  ) ) && ( $strNow > $couponExpiryS || empty( $val['disExpS'] ) ) ) :
                $listing_discount_data_final[]  =   $val;
            endif;
        endif;
    }

    if( is_array( $listing_discount_data_final ) && !empty( $listing_discount_data_final ) ):
    require_once (THEME_PATH . "/include/aq_resizer.php");
    ?>
    <?php
    $offer_counter  =   1;
    $offer_total    =   count( $listing_discount_data_final );

    $post_author_id = get_post_field( 'post_author', get_the_ID() );
    $discount_displayin =   get_user_meta( $post_author_id, 'discount_display_area', true );
    $offer_sidebar  =   '';
    if( $discount_displayin == 'sidebar' )
    {
        $offer_sidebar  =   'offer-sidebar';
    }
    foreach ( $listing_discount_data_final as $key => $discount_data )
    {
        if( ( $strNow < $discount_data['disExpE'] || empty( $discount_data['disExpE'] ) ) && ( $strNow > $discount_data['disExpS'] || empty( $discount_data['disExpS'] ) ) ) :
            if( $offer_counter == 1 ):
                ?>
                <div class="lp-listing-offers">
            <?php endif; ?>
            <?php
            $img_url    =   'https://placehold.co/150x150';
            if( !empty( $discount_data['disImg'] ) )
            {
                $img_url  = aq_resize( $discount_data['disImg'], '150', '150', true, true, true);
            }
            ?>
            <div class="lp-listing-offer <?php echo esc_attr( $offer_sidebar ); ?>">

                <div class="offer-top">
                   
                    <?php
                    if( !empty( $discount_data['disExpE'] ) ):
                        $couponExpiry  = coupon_timestamp($discount_data['disExpE'],$discount_data['disTimeE']);

                        ?>
                        <div class="offer-expiry">
                            <strong><?php echo esc_html__( 'Validity:', 'listingpro' ); ?></strong>
                            <div id="offer-countdown-<?php echo esc_attr( $offer_counter ); ?>" class="offer-countdown-<?php echo esc_attr( $offer_counter ); ?> lp-countdown"
                                 data-label-days="<?php echo esc_html__('days', 'listingpro'); ?>"
                                 data-label-hours="<?php echo esc_html__('hours', 'listingpro'); ?>"
                                 data-label-mints="<?php echo esc_html__('min', 'listingpro') ?>"
                                 data-minute = "<?php echo date( 'i', $couponExpiry ); ?>"
                                 data-hour = "<?php echo date( 'H', $couponExpiry ); ?>"
                                 data-day="<?php echo date( 'd', $couponExpiry); ?>"
                                 data-month="<?php echo date( 'm', $couponExpiry)-1; ?>"
                                 data-year="<?php echo date( 'Y', $couponExpiry); ?>"></div>
                            <div class="clearfix"></div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="offer-bottom">
                    <div class="offer-thumb">
                        <img src="<?php echo esc_attr( $img_url ); ?>" alt="<?php echo esc_attr( $discount_data['disHea'] ); ?>">
                    </div>
                    <?php
					$bnt_text_def   =   esc_html__( 'Click Here', 'listingpro' );
                   if( empty( $discount_data['disBT']  ) )
                   {
                       $discount_data['disBT'] =   $bnt_text_def;
                   }
                    if( !empty( $discount_data['disBT'] ) ):
                        $btn_link   =   '';
                        $btn_class  =   'lp-copy-code';
                        if( $discount_data['disBL'] )
                        {
                            $btn_link   =   'href="'. $discount_data['disBL'] .'" target="_blank"';
                            $btn_class  =   '';
                        }
                        ?>
                        <div class="offer-btn">
                            <div class="lp-dis-code-copy <?php echo 'offer-'.$offer_counter; ?>">
                                <div class="popup-header">
                                    <strong><?php echo esc_html__( 'Coupon Code', 'listingpro' ); ?></strong>
                                    <i data-target-code="deal-<?php echo esc_attr( $offer_counter ); ?>" class="fa fa-times close-copy-code" aria-hidden="true"></i>
                                </div>
                                <i data-target-code="<?php echo 'offer-'.$offer_counter; ?>" class="fa fa-times close-copy-code" aria-hidden="true"></i>
                                <input type="text" value="" class="codtopcopy">
                                <strong class="copycode"><?php echo esc_attr( $discount_data['disCod'] ); ?></strong>
                                <span data-target-code="<?php echo 'offer-'.$offer_counter; ?>"><i class="fa fa-files-o" aria-hidden="true"></i> <?php echo esc_html__( 'copy to clipboard', 'listingpro' ); ?></span>
                            </div>
                           
							
                        </div>
                    <?php endif; ?>
                    
					<div class="clearfix">
						 
					<div class="pull-left">
						<strong class="offer-title"><?php echo esc_attr( $discount_data['disHea'] ); ?></strong>
					</div>	
					<?php
					$tagline_margin =   '';
					if( empty( $discount_data['disExpE'] ) ){
						$tagline_margin =   'tagline-margin';
					}
					if( $discount_data['disOff'] ) echo '<span class="pull-right offer-tagline ' . $tagline_margin . '">'. $discount_data['disOff'] .'</span>';
					?>	
					</div>
                    <div class="offer-description">
                        <?php if( $discount_data['disDes'] ): ?>
                            <p><?php echo html_entity_decode($discount_data['disDes']); ?></p>
                        <?php endif; ?>
						
						 <a data-target-code="<?php echo 'offer-copy-'.$offer_counter; ?>" <?php echo wp_kses_post( $btn_link ); ?> class="<?php echo esc_attr( $btn_class ); ?>"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo esc_attr( $discount_data['disBT'] ); ?></a>
                    </div>
                </div>
            <?php
            if( empty( $discount_data['disBL'] ) ):
                ?>
                <div class="dis-code-copy-pop offer-copy-<?php echo esc_attr( $offer_counter ); ?>" id="dicount-copy-<?php echo esc_attr( $offer_counter ); ?>">
                    <span class="close-right-icon" data-target="offer-copy-<?php echo esc_attr( $offer_counter ); ?>"><i class="fa fa-times"></i></span>
                    <div class="dis-code-copy-pop-inner">
                        <div class="dis-code-copy-pop-inner-cell">
                            <p><?php echo esc_html__( 'Copy to clipboard', 'listingpro' ); ?></p>
                            <p class="dis-code-copy-wrap"><input class="code-top-copy-<?php echo esc_attr( $offer_counter ); ?>" type="text" value="<?php echo esc_attr( $discount_data['disCod'] ); ?>"> <a data-target-code="dicount-copy-<?php echo esc_attr( $offer_counter ); ?>" href="#" class="copy-now" data-coppied-label="<?php echo esc_html__( 'Copied', 'listingpro' ); ?>"><?php echo esc_html__( 'Copy', 'listingpro' ); ?></a></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php

            if( $offer_counter == $offer_total ){
                echo '</div>';
            }
            $offer_counter++;
        endif;
    }
    ?>


<?php
    endif;
endif;
?>