<?php
$listing_discount_data_init  =   get_post_meta( get_the_ID(), 'listing_discount_data', true );

$listing_discount_data_final    =   array();
$strNow =   strtotime("NOW");
if( !empty( $listing_discount_data_init ) ):
foreach ( $listing_discount_data_init as $key => $val )
{
    if( $strNow < $val['disExp'] || empty( $val['disExp'] ) ) :
        $listing_discount_data_final[]  =   $val;
    endif;
}
if( is_array( $listing_discount_data_final ) && !empty( $listing_discount_data_final ) ):

    require_once (THEME_PATH . "/include/aq_resizer.php");
    ?>
    <div class="container">
    <div class="lp-listing-detail-page-content" id="detail5-offers">
    <div class="row">
    <div class="pull-left lp-left-title">
        <h2><?php echo esc_html__('Special Deals', 'listingpro'); ?></h2>
    </div>
    <div class="col-md-12 lp-right-content-box">
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
                    <div class="lp-deals-wrap">
                    <div class="row">
                <?php endif; ?>
            <?php
                $img_url    =   'https://placehold.co/490x370';
                if( $discount_data['disImg'] )
                {
                    $img_url    =   $discount_data['disImg'];
                    $img_url  = aq_resize( $img_url, '490', '370', true, true, true);
                }

                ?>
                <div class="col-md-6">
                    <div class="lp-deal">
                        <div class="deal-thumb">
                            <img alt="image" src="<?php echo esc_attr( $img_url ); ?>">
                        </div>
                        <div class="deal-details">
                            <?php
                            if( !empty( $discount_data['disExp'] ) ):
                            ?>
                            <div class="deal-countdown-wrap">
                                <div id="lp-deals-countdown<?php echo esc_attr( $deal_counter ); ?>" class="lp-countdown lp-deals-countdown<?php echo esc_attr( $deal_counter ); ?>"
                                     data-label-hours="<?php echo esc_html__( 'hours', 'listingpro' ); ?>"
                                         data-label-mins="<?php echo esc_html__( 'min', 'listingpro' ); ?>"
                                         data-label-secs="<?php echo esc_html__( 'sec', 'listingpro' ); ?>"
                                         data-label-days="<?php echo esc_html__( 'days', 'listingpro' ); ?>"
                                     data-day="<?php echo date( 'd', $discount_data['disExp'] ); ?>"
                                     data-month="<?php echo date( 'm', $discount_data['disExp'] )-1; ?>"
                                     data-year="<?php echo date( 'Y', $discount_data['disExp'] ); ?>"></div>
                            </div>
                            <?php endif; ?>
                            <?php
                            if( $discount_data['disOff'] ) echo '<span class="lp-deal-off">'. $discount_data['disOff'] .'</span>';
                            ?>
                            <div class="deal-content">
                                <?php
                                if( $discount_data['disHea'] ) echo '<strong>'. $discount_data['disHea'] .'</strong>';
                                if( $discount_data['disDes'] ) echo '<p>'. $discount_data['disDes'] .'</p>';
                                if( $discount_data['disValid'] )
                                {
                                    echo '<p class="lp-deal-validity">
                                <strong>'.esc_html__( 'Validity: ', 'listingpro' ).'</strong>
                                <span class="lp-deal-valid-detail">'. $discount_data['disValid'] .'</span>
                            </p>';
                                }
                                ?>
                                <div class="lp-dis-code-copy deal-<?php echo esc_attr( $deal_counter ); ?>">
                                    <div class="popup-header"><strong><?php echo esc_html__( 'Coupon Code', 'listingpro' ); ?></strong><i data-target-code="deal-<?php echo esc_attr( $deal_counter ); ?>" class="fa fa-times close-copy-code" aria-hidden="true"></i></div>
                                    <i data-target-code="deal-<?php echo esc_attr( $deal_counter ); ?>" class="fa fa-times close-copy-code" aria-hidden="true"></i>
                                    <input type="text" value="" class="codtopcopy">
                                    <strong class="copycode"><?php echo esc_attr( $discount_data['disCod'] ); ?></strong>
                                    <span data-target-code="deal-<?php echo esc_attr( $deal_counter ); ?>"><i class="fa fa-files-o" aria-hidden="true"></i> <?php echo esc_html__( 'copy to clipboard', 'listingpro' ); ?></span>
                                </div>

                                <?php
                                $btn_href   =   '';
                                $btn_class  =   'lp-copy-code';
                                if( $discount_data['disBL'] && !empty( $discount_data['disBL'] ) )
                                {
                                    $btn_href   =   'href="'. $discount_data['disBL'] .'"';
                                    $btn_class  =   '';
                                }
                                ?>
                                <a target="_blank" data-target-code="deal-<?php echo esc_attr( $deal_counter ); ?>" <?php echo wp_kses_post( $btn_href ); ?> class="deal-button <?php echo esc_attr( $btn_class ); ?>"><i class="fa fa-gavel" aria-hidden="true"></i> <?php echo esc_attr( $discount_data['disBT'] ); ?></a>
                            </div>
                        </div>
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
endif; ?>
    </div>
    </div>
    </div>
    </div>

<?php endif;
?>