
<?php
$listing_discount_data  =   get_post_meta( get_the_ID(), 'listing_discount_data', true );
if( is_array( $listing_discount_data ) && !empty( $listing_discount_data ) ):
    $strNow =   strtotime("NOW");
    require_once (THEME_PATH . "/include/aq_resizer.php");

    $offer_counter  =   1;
    $offer_total    =   count( $listing_discount_data );
    ?>
    <div class="container">
        <div class="lp-listing-detail-page-content lp-listing-offers" id="detail5-offers">
            <div class="row">
                <div class="pull-left lp-left-title">
                    <h2><?php echo esc_html__('Special Offers', 'listingpro'); ?></h2>
                </div>
                <div class="col-md-12 lp-right-content-box">
                    <?php
                    foreach ( $listing_discount_data as $k => $discount_data ):
                        if( $strNow < $discount_data['disExp'] || empty( $discount_data['disExp'] ) ):
                            $img_url    =   'https://placehold.co/150x150';
                            if( !empty( $discount_data['disImg'] ) )
                            {
                                $img_url  = aq_resize( $discount_data['disImg'], '150', '150', true, true, true);
                            }
                            ?>
                            <div class="lp-listing-offer">
                                <div class="offer-top">
                                    <?php
                                    if( $discount_data['disOff'] ) echo '<span class="offer-tagline">'. $discount_data['disOff'] .'</span>';
                                    ?>
                                    <?php
                                    if( !empty( $discount_data['disExp'] ) ):
                                        ?>
                                        <div class="offer-expiry">
                                            <strong><?php echo esc_html__( 'Validity:', 'listingpro' ); ?></strong>
                                            <div id="offer-countdown-<?php echo esc_attr( $offer_counter ); ?>" class="offer-countdown-<?php echo esc_attr( $offer_counter ); ?> lp-countdown"
                                                 data-label-hours="<?php esc_html__('hours', 'listingpro'); ?>"
                                                 data-label-mins="<?php esc_html__('mins', 'listingpro'); ?>"
                                                 data-label-secs="<?php esc_html__('secs', 'listingpro'); ?>"
                                                 data-day="<?php echo date( 'd', $discount_data['disExp'] ); ?>"
                                                 data-month="<?php echo date( 'm', $discount_data['disExp'] )-1; ?>"
                                                 data-year="<?php echo date( 'Y', $discount_data['disExp'] ); ?>"></div>
                                            <div class="clearfix"></div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="offer-bottom">
                                    <div class="offer-thumb">
                                        <img src="<?php echo esc_attr( $img_url ); ?>" alt="<?php echo esc_attr( $discount_data['disHea'] ); ?>">
                                    </div>
                                    <?php
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
                                                    <i data-target-code="deal-<?php echo esc_attr( $deal_counter ); ?>" class="fa fa-times close-copy-code" aria-hidden="true"></i>
                                                </div>
                                                <i data-target-code="<?php echo 'offer-'.$offer_counter; ?>" class="fa fa-times close-copy-code" aria-hidden="true"></i>
                                                <input type="text" value="" class="codtopcopy">
                                                <strong><?php echo esc_attr( $discount_data['disCod'] ); ?></strong>
                                                <span data-target-code="<?php echo 'offer-'.$offer_counter; ?>"><i class="fa fa-files-o" aria-hidden="true"></i> <?php echo esc_html__( 'copy to clipboard', 'listingpro' ); ?></span>
                                            </div>
                                            <a data-target-code="<?php echo 'offer-'.$offer_counter; ?>" <?php echo wp_kses_post( $btn_link ); ?> class="<?php echo esc_attr( $btn_class ); ?>"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo esc_attr( $discount_data['disBT'] ); ?></a>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="offer-title"><?php echo esc_attr( $discount_data['disHea'] ); ?></h3>
                                    <div class="offer-description">
                                        <?php if( $discount_data['disDes'] ): ?>
                                            <p><?php echo esc_attr( $discount_data['disDes'] ); ?></p>
                                        <?php endif; ?>
                                        <?php
                                        if( $discount_data['disValid'] ):
                                            ?>
                                            <p><strong><?php echo esc_html__( 'Validity Details:', 'listingpro' ); ?></strong> <?php echo esc_attr( $discount_data['disValid'] ); ?></p>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                            <?php $offer_counter++; endif;  endforeach; ?>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>