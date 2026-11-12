<?php
global $listingpro_options;

$app_view_home  =   $listingpro_options['app_view_home'];
$listing_mobile_view            =   $listingpro_options['single_listing_mobile_view'];

if(
is_search() ||
is_tax( 'listing-category' ) ||
is_tax( 'features' ) ||
is_tax( 'list-tags' ) ||
is_tax( 'location' )
)
{
    ?>
    <div class="clearfix"></div>
    <footer class="footer-app-view-new">
        <div class="footer-btn-left">
            <div data-toggle="modal" data-target="#app-view-archive-login-popup">
                <i class="fa fa-tags"></i> <?php echo esc_html__( 'FILTERS', 'listingpro' ); ?>
            </div>
        </div>
        <div class="footer-center-btn">
            <div class="center-btn-icon">
                <?php
                $nearmeOPT = $listingpro_options['enable_nearme_search_filter'];
                if(!empty($nearmeOPT) && $nearmeOPT=='1' ){
                    if( is_ssl() || LP_if_localhost() == 'true'){

                        $units = $listingpro_options['lp_nearme_filter_param'];
                        if(empty($units)){
                            $units = 'km';
                        }
                        ?>
                        <div data-nearmeunit="<?php echo esc_attr($units); ?>" id="lp-find-near-me" class="search-filters form-group padding-right-0">
                            <ul>
                                <li class="lp-tooltip-outer">
                                    <a class="btn default near-me-btn"><img alt="image" src="<?php echo get_template_directory_uri(); ?>/assets/images/footer-near-me-btn.jpg"></a>
                                    <div class="lp-tooltip-div-hidden">
                                        <div class="lp-tooltip-arrow"></div>
                                        <div class="lp-tool-tip-content clearfix lp-tooltip-outer-responsive">
                                            <?php

                                            $minRange = $listingpro_options['enable_readious_search_filter_min'];
                                            $maxRange = $listingpro_options['enable_readious_search_filter_max'];
                                            $defVal = 100;
                                            if(isset($listingpro_options['enable_readious_search_filter_default'])){
                                                $defVal = $listingpro_options['enable_readious_search_filter_default'];
                                            }
                                            ?>
                                            <div class="location-filters location-filters-wrapper">

                                                <div id="pac-container" class="clearfix">
                                                    <div class="clearfix row">
                                                        <div class="lp-price-range-btnn col-md-1 text-right padding-0">
                                                            <?php echo esc_attr($minRange); ?>
                                                        </div>
                                                        <div class="col-md-9" id="distance_range_div">
                                                            <input id="distance_range" name="distance_range" type="text" data-slider-min="<?php echo esc_attr($minRange); ?>" data-slider-max="<?php echo esc_attr($maxRange); ?>" data-slider-step="1" data-slider-value="<?php echo esc_attr($defVal )?>"/>
                                                        </div>
                                                        <div class="col-md-2 padding-0 text-left lp-price-range-btnn">
                                                            <?php echo esc_attr($maxRange); ?>
                                                        </div>
                                                        <div style="display:none" class="col-md-4" id="distance_range_div_btn">
                                                            <a href=""><?php echo esc_html__('New Location', 'listingpro'); ?></a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 padding-top-10" style="display:none" >
                                                        <input id="pac-input" name="pac-input" type="text" placeholder="<?php echo esc_html__('Enter a location', 'listingpro'); ?>" data-lat="" data-lng="" data-center-lat="" data-center-lng="" data-ne-lat="" data-ne-lng="" data-sw-lat="" data-sw-lng="" data-zoom="">
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                </li>

                            </ul>
                        </div>
                    <?php } ?>
                <?php }
                ?>
            </div>
        </div>
        <div class="footer-btn-right map-view-btn" data-action="map_view">
            <i class="fa fa-map-marker"></i> <?php echo esc_html__( 'Map', 'listingpro' ); ?>
        </div>
        <div class="clearfix"></div>
    </footer>
    <?php
}
elseif ( is_singular( 'listing' ) )
{
    $latitude = listing_get_metabox('latitude');
    $longitude = listing_get_metabox('longitude');
    $phone = listing_get_metabox('phone');

    ?>
    <div class="clearfix"></div>
    <footer class="footer-app-view-new">
        <?php
        if( !empty( $phone ) ):
        ?>
        <div class="footer-center-btn">
            <div class="center-btn-icon"><a href="tel:<?php echo esc_attr($phone); ?>"><img alt="image" src="<?php echo get_template_directory_uri(); ?>/assets/images/appfooter2.png"></a></div>
        </div>
        <?php endif; ?>
        <div class="footer-btn-left">
            <div data-toggle="modal" data-target="#app-view-archive-login-popup">
                <a target="_blank" href="https://www.google.com/maps?daddr=<?php echo esc_attr($latitude); ?>,<?php echo esc_attr($longitude); ?>"><i class="fa fa-map-marker"></i> <?php echo esc_html__( 'DIRECTIONS', 'listingpro' ); ?></a>
            </div>
        </div>
        <div class="footer-btn-right">
            <a class="open-lead-form-app-view"><i class="fa fa-envelope"></i> <?php echo esc_html__( 'MESSAGE', 'listingpro' ); ?></a>
        </div>
        <div class="clearfix"></div>
    </footer>
    <?php
}
elseif( is_page( $app_view_home ) && $listing_mobile_view == 'app_view2' && wp_is_mobile() )
{
    $phone = listing_get_metabox('phone');
    ?>
    <div class="clearfix"></div>
    <footer class="footer-app-view-new">
        <div class="footer-center-btn">
            <div class="center-btn-icon lp-open-search-btn-icon"><img alt="image" src="<?php echo get_template_directory_uri(); ?>/assets/images/appfooter3.png"></div>
        </div>
    <?php
}
?>