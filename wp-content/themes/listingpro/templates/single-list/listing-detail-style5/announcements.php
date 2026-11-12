<?php
                global $listingpro_options;
                $announcements_show =   true;
                if( isset( $listingpro_options['announcements_dashoard'] ) && $listingpro_options['announcements_dashoard'] == 0 )
                {
                    $announcements_show =   false;
                }
                if( $announcements_show == false ) return false;

                $lp_listing_announcements  =   get_post_meta( get_the_ID(), 'lp_listing_announcements', true );
                if( $lp_listing_announcements != '' && is_array( $lp_listing_announcements ) && count($lp_listing_announcements) > 0 ):
                    ?>
                <div class="container">
                    <div class="lp-listing-detail-page-content margin-bottom-40">
                        <div class="row">
                            <div class="col-md-12 lp-right-content-box pull-right">
                    <div class="lp-listing-announcement" id="detail5-announcements">
                        <div class="">
                            <h3><?php echo esc_html__('Announcements', 'listingpro'); ?></h3>							</div>
                        <?php
                        foreach ( $lp_listing_announcements as $k => $v ):
                            if( $v['annLI'] == get_the_ID() ):
                                ?>
                                <div class="announcement-wrap">
                                    <i class="fa fa-bullhorn" aria-hidden="true"></i> <span><?php echo esc_attr( $v['annMsg'] ); ?></span>
                                    <?php
                                    if( !empty( $v['annBT'] ) && !empty( $v['annBL'] ) ):
                                        ?>
                                        <a target="_blank" href="<?php echo esc_attr( $v['annBL'] ); ?>" class="announcement-btn"><?php echo esc_attr( $v['annBT'] ); ?></a>
                                    <?php endif; ?>
                                    <div class="clearfix"></div>
                                </div>
                            <?php endif; endforeach; ?>
                        <div class="clearfix"></div>
                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
