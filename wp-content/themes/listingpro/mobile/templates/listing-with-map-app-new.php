<?php

$type = 'listing';
$term_id = '';
$taxName = '';
$termID = '';
$term_ID = '';
global $paged, $listingpro_options;

$lporderby = 'date';
$lporders = 'DESC';
if( isset($listingpro_options['lp_archivepage_listingorder']) ){
    $lporders = $listingpro_options['lp_archivepage_listingorder'];
}
$mtQuery = array();
if( isset($listingpro_options['lp_archivepage_listingorderby']) ){
    $lporderby = $listingpro_options['lp_archivepage_listingorderby'];
}
if($lporderby=="post_views_count" || $lporderby=="listing_reviewed" || $lporderby=="listing_rate" || $lporderby=="claimed" ){

    //$lporderby = 'meta_value_num';
    $lporders = '';
    $mtQuery = array(
        $lporderby.'_clause'=>
            array(
                'key'=>$lporderby,
                'type'=>'numeric',
            ),
    );
    $lporderby = array($lporderby.'_clause'=>'DESC','title'=>'ASC');
}

if($lporderby=="rand"){
    $lporders = '';
}

$defSquery = '';
$lpDefaultSearchBy = 'title';
if( isset($listingpro_options['lp_default_search_by']) ){
    $lpDefaultSearchBy = $listingpro_options['lp_default_search_by'];
}

$includeChildren = true;
if(lp_theme_option('lp_children_in_tax')){
    if(lp_theme_option('lp_children_in_tax')=="no"){
        $includeChildren = false;
    }
}

$taxTaxDisplay = true;
$TxQuery = '';
$tagQuery = '';
$catQuery = '';
$locQuery = '';
$taxQuery = '';
$searchQuery = '';
$sKeyword = '';
$tagKeyword = '';
$priceQuery = '';
$postsonpage = '';
if(isset($listingpro_options['listing_per_page'])){
    $postsonpage = $listingpro_options['listing_per_page'];
}
else{
    $postsonpage = 10;
}
if( !empty($_GET['s']) && isset($_GET['s']) && $_GET['s']=="home" ){
    if( !empty($_GET['lp_s_tag']) && isset($_GET['lp_s_tag'])){
        $lpsTag = sanitize_text_field($_GET['lp_s_tag']);
        $tagQuery = array(
            'taxonomy' => 'list-tags',
            'field' => 'id',
            'terms' => $lpsTag,
            'operator'=> 'IN' //Or 'AND' or 'NOT IN'
        );
    }

    if( !empty($_GET['lp_s_cat']) && isset($_GET['lp_s_cat'])){
        $lpsCat = sanitize_text_field($_GET['lp_s_cat']);
        $catQuery = array(
            'taxonomy' => 'listing-category',
            'field' => 'id',
            'terms' => $lpsCat,
            'operator'=> 'IN', //Or 'AND' or 'NOT IN'
            'include_children ' => $includeChildren,
        );
        $taxName = 'listing-category';
    }

    if( !empty($_GET['lp_s_loc']) && isset($_GET['lp_s_loc'])){
        $lpsLoc = sanitize_text_field($_GET['lp_s_loc']);
        if(is_numeric($lpsLoc)){
            $lpsLoc = $lpsLoc;
        }
        else{
            $term = listingpro_term_exist($lpsLoc,'location');
            if(!empty($term)){
                $lpsLoc=$term['term_id'];
            }
            else{
                $lpsLoc = '';
            }
        }
        $locQuery = array(
            'taxonomy' => 'location',
            'field' => 'id',
            'terms' => $lpsLoc,
            'operator'=> 'IN', //Or 'AND' or 'NOT IN'
            'include_children ' => $includeChildren,
        );
    }
    /* on 3 april by zaheer */
    if( empty($_GET['lp_s_tag']) && empty($_GET['lp_s_cat']) && !empty($_GET['select']) ){

        if( $lpDefaultSearchBy=="title" ){
            $sKeyword = sanitize_text_field($_GET['select']);
            $defSquery = $sKeyword;
        }
        else{

            $sKeyword = sanitize_text_field($_GET['select']);

            $tagQuery = array(
                'taxonomy' => 'list-tags',
                'field' => 'name',
                'terms' => $sKeyword,
                'operator'=> 'IN' //Or 'AND' or 'NOT IN'
            );
            $sKeyword = '';
            $tagKeyword = sanitize_text_field($_GET['select']);
            $defSquery = $tagKeyword;
        }

    }
    if( empty($_GET['lp_s_loc']) && empty($_GET['lp_s_tag']) && empty($_GET['lp_s_cat']) && empty($_GET['select']) ){
        //$postsonpage = 25;
    }
    /* end on 3 april by zaheer */
    $TxQuery = array(
        'relation' => 'AND',
        $tagQuery,
        $catQuery,
        $locQuery,
    );
    $ad_campaignsIDS = listingpro_get_campaigns_listing( 'lp_top_in_search_page_ads', TRUE,$taxQuery,$TxQuery,$priceQuery,$sKeyword, null, null);
}
else{
    $queried_object = get_queried_object();
    $term_id = $queried_object->term_id;
    $taxName = $queried_object->taxonomy;
    if(!empty($term_id)){
        $termID = get_term_by('id', $term_id, $taxName);
        $termName = $termID->name;
        $term_ID = $termID->term_id;
    }

    $TxQuery = array(
        array(
            'taxonomy' => $taxName,
            'field' => 'id',
            'terms' => $termID->term_id,
            'operator'=> 'IN' //Or 'AND' or 'NOT IN'
        ),
    );
    $ad_campaignsIDS = listingpro_get_campaigns_listing( 'lp_top_in_search_page_ads', TRUE, $TxQuery,$searchQuery,$priceQuery,$sKeyword, null, null );
}

$args=array(
    'post_type' => $type,
    'post_status' => 'publish',
    'posts_per_page' => $postsonpage,
    's'	=> $sKeyword,
    'paged'  => $paged,
    'post__not_in' =>$ad_campaignsIDS,
    'tax_query' => $TxQuery,
    'orderby' => $lporderby,
    'order'   => $lporders,
);

$my_query = null;
$my_query = new WP_Query($args);
$found = $my_query->found_posts;
if(($found > 1)){
    $foundtext = esc_html__('Results', 'listingpro');
}else{
    $foundtext = esc_html__('Result', 'listingpro');
}
// Harry Code

$listing_layout = $listingpro_options['listing_views'];
$addClassListing = '';
$icon_markup    =   '';
if( $listing_layout == 'list_view' )
{
    $icon_markup    =   '<i class="fa fa-list" aria-hidden="true"></i>';
}
else
{
    $icon_markup    =   '<i class="fa fa-th-large" aria-hidden="true"></i>';
}
if($listing_layout == 'list_view') {
    $addClassListing = 'listing_list_view';
}
?>

<script>
    jQuery(document).ready(function(e){
        jQuery('.listing-app-view-bar .right-icons a, .footer-app-view-new .map-view-btn').click(function(e){
            e.preventDefault();
            var buttonAction    =   jQuery(this).data('action');
            if( buttonAction == 'map_view' )
            {
                if(jQuery(this).hasClass('close-map-view')){
                    jQuery('.map-view-list-container').hide();
                    jQuery('.sidemap-fixed').removeClass('open-map');
                    jQuery(this).removeClass('close-map-view');
                    if( jQuery(this).hasClass('map-view-btn') )
                    {

                    }
                    else
                    {
                        jQuery(this).html('<i class="fa fa-map-marker" aria-hidden="true"></i>');
                    }

                    jQuery('.map-view-list-container').slick('unslick');
                }else{
                    jQuery('.sidemap-fixed').addClass('open-map');

                    jQuery(this).addClass('close-map-view');
                    jQuery('.map-view-list-container').show();
                    jQuery('.map-view-list-container').slick({
                        centerMode: false,
                        autoplay: true,
                        centerPadding: '30px',
                        slidesToShow: 1,
                        arrows: false
                    });
                }

            }
        });

        jQuery('.open-app-view').click(function(e){
            e.preventDefault();
            jQuery('.map-view-list-container').hide();
            jQuery('.listing-app-view-bar .right-icons a').removeClass('close-map-view');
            jQuery('.map-view-list-container').slick('unslick');

        })

    })
</script>

<!--==================================Section Open=================================-->
<section class="page-container clearfix section-fixed listing-with-map pos-relative taxonomy" id="<?php echo esc_attr($taxName); ?>">
    <!--modal 7, for app view filters-->
    <?php
    $searchfilter = $listingpro_options['enable_search_filter'];
    if(!empty($searchfilter) && $searchfilter=='1'){
        ?>
        <!-- Modal -->
        <div class="modal fade app-view-filters app-view-filters2" id="app-view-archive-login-popup" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">

                        <h4 class="modal-title" id="myLargeModalLabel"><?php echo esc_html__( 'Filters', 'listingpro' );?></h4>
                    </div>
                    <div class="modal-body">
                        <?php get_template_part('mobile/templates/search/filter-app-view'); ?>
                    </div>
                    <div class="modal-footer">
                        <div class="text-center">
                            <a class="close" data-dismiss="modal" aria-label="Close"><?php echo esc_html__( 'Search', 'listingpro' );?></a>
                            <a class="close" data-dismiss="modal" aria-label="Close"><?php echo esc_html__( 'Cancel', 'listingpro' );?></a>
                        </div>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    <?php } ?>

<!--    <div class="listing-app-view-bar">-->
<!--        <div class="col-xs-1 search-filter">-->
<!--            <a href="#" data-toggle="modal" data-target="#app-view-archive-login-popup"><i class="fa fa-sliders" aria-hidden="true"></i></a>-->
<!--        </div>-->
<!--        <div class="col-xs-11 text-right right-icons">-->
<!--            <a href="#" data-action="map_view" class="map-view-icon"><i class="fa fa-map-marker" aria-hidden="true"></i></a>-->
<!--            --><?php
//            $searchfilter = $listingpro_options['enable_search_filter'];
//            if(!empty($searchfilter) && $searchfilter=='1'){
//                ?>
<!--                <div class="search-filter-attr-filter-inner">-->
<!--                    <div class="search-filter-attr-filter-outer">-->
<!--                        --><?php
//                        $priceOPT = $listingpro_options['enable_price_search_filter'];
//                        if(!empty($priceOPT) && $priceOPT=='1'){
//                            $lp_priceSymbol = $listingpro_options['listing_pricerange_symbol'];
//                            $lp_priceSymbol2 = $lp_priceSymbol.$lp_priceSymbol;
//                            $lp_priceSymbol3 = $lp_priceSymbol2.$lp_priceSymbol;
//                            $lp_priceSymbol4 = $lp_priceSymbol3.$lp_priceSymbol;
//                            ?>
<!--                            <div class="form-group pricy-form-group margin-right-0">-->
<!---->
<!--                                <div id="lp-find-near-me-outer" class="search-filters form-group margin-right-0">-->
<!--                                    <ul>-->
<!--                                        <li>-->
<!--                                            <a  class="margin-right-0" href="#" class="map-view-icon2"  id="map-view-icon2"><i class="fa fa-money"></i> --><?php //echo esc_html__('Price..', 'listingpro'); ?><!--</a>-->
<!--                                        </li>-->
<!--                                    </ul>-->
<!--                                </div>-->
<!--                                <div class="currency-signs search-filter-attr " id="search-filter-attr-filter">-->
<!--                                    <ul>-->
<!--                                        <li class="simptip-position-top simptip-movable" data-tooltip="--><?php //echo esc_html__( 'Inexpensive', 'listingpro' );?><!--" id="one"><a href="#" data-price="inexpensive">--><?php //echo $lp_priceSymbol; ?><!--</a></li>-->
<!--                                        <li class="simptip-position-top simptip-movable" data-tooltip="--><?php //echo esc_html__( 'Moderate', 'listingpro' );?><!--" id="two"><a href="#" data-price="moderate">--><?php //echo $lp_priceSymbol2; ?><!--</a></li>-->
<!--                                        <li class="simptip-position-top simptip-movable" data-tooltip="--><?php //echo esc_html__( 'Pricey', 'listingpro' );?><!--" id="three"><a href="#" data-price="pricey">--><?php //echo $lp_priceSymbol3; ?><!--</a></li>-->
<!--                                        <li class="simptip-position-top simptip-movable" data-tooltip="--><?php //echo esc_html__( 'Ultra High End', 'listingpro' );?><!--" id="four"><a href="#" data-price="ultra_high_end">--><?php //echo $lp_priceSymbol4; ?><!--</a></li>-->
<!--                                    </ul>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        --><?php //} ?>
<!---->
<!--                        <div class="search-filter-attr form-group margin-right-0">-->
<!--                            --><?php
//                            $nearmeOPT = $listingpro_options['enable_nearme_search_filter'];
//                            if(!empty($nearmeOPT) && $nearmeOPT=='1' ){
//                                if( is_ssl() || LP_if_localhost() == 'true'){
//
//                                    $units = $listingpro_options['lp_nearme_filter_param'];
//                                    if(empty($units)){
//                                        $units = 'km';
//                                    }
//                                    ?>
<!--                                    <div data-nearmeunit="--><?php //echo esc_attr($units); ?><!--" id="lp-find-near-me" class="search-filters form-group padding-right-0 margin-right-0 clearfix near-me-app-view-btn">-->
<!--                                        <ul>-->
<!--                                            <li class="lp-tooltip-outer">-->
<!--                                                <a  class="btn default near-me-btn"><i class="fa fa-map-marker" aria-hidden="true"></i> --><?php //echo esc_html__('Near Me', 'listingpro'); ?><!--</a>-->
<!--                                                <div class="lp-tooltip-div-hidden">-->
<!--                                                    <div class="lp-tooltip-arrow"></div>-->
<!--                                                    <div class="lp-tool-tip-content clearfix lp-tooltip-outer-responsive">-->
<!--                                                        --><?php
//
//                                                        $minRange = $listingpro_options['enable_readious_search_filter_min'];
//                                                        $maxRange = $listingpro_options['enable_readious_search_filter_max'];
//
//                                                        $defVal = 100;
//
//                                                        if(isset($listingpro_options['enable_readious_search_filter_default'])){
//
//                                                            $defVal = $listingpro_options['enable_readious_search_filter_default'];
//
//                                                        }
//
//                                                        ?>
<!--                                                        <div class="location-filters location-filters-wrapper">-->
<!---->
<!--                                                            <div id="pac-container" class="clearfix">-->
<!--                                                                <div class="clearfix row">-->
<!--                                                                    <div class="lp-price-range-btnn col-md-1 col-xs-1 text-right padding-0">--><?php //echo $minRange; ?><!--</div>-->
<!--                                                                    <div  class="col-md-9 col-xs-9" id="distance_range_div">-->
<!--                                                                        <input id="distance_range" name="distance_range" type="text" data-slider-min="--><?php //echo $minRange; ?><!--" data-slider-max="--><?php //echo $maxRange; ?><!--" data-slider-step="1" data-slider-value="--><?php //echo $defVal ?><!--"/>-->
<!---->
<!--                                                                    </div>-->
<!--                                                                    <div class="col-md-2 col-xs-2 padding-0 text-left lp-price-range-btnn">--><?php //echo $maxRange; ?><!--</div>-->
<!--                                                                    <div style="display:none" class="col-md-4" id="distance_range_div_btn">-->
<!--                                                                        <a href="">--><?php //echo esc_html__('New Location', 'listingpro'); ?><!--</a>-->
<!--                                                                    </div>-->
<!--                                                                </div>-->
<!--                                                                <div class="col-md-12 padding-top-10" style="display:none" >-->
<!---->
<!--                                                                    <input id="pac-input" name="pac-input" type="text" placeholder="--><?php //echo esc_html__('Enter a location', 'listingpro'); ?><!--" data-lat="" data-lng="" data-center-lat="" data-center-lng="" data-ne-lat="" data-ne-lng="" data-sw-lat="" data-sw-lng="" data-zoom="">-->
<!--                                                                </div>-->
<!--                                                            </div>-->
<!---->
<!--                                                        </div>-->
<!---->
<!--                                                    </div>-->
<!---->
<!--                                                </div>-->
<!--                                            </li>-->
<!---->
<!--                                        </ul>-->
<!--                                    </div>-->
<!--                                --><?php //} ?>
<!--                            --><?php //} ?>
<!--                        </div>-->
<!---->
<!---->
<!--                        --><?php
//                        $openTimeOPT = $listingpro_options['enable_opentime_search_filter'];
//                        if(!empty($openTimeOPT) && $openTimeOPT=='1'){
//                            ?>
<!--                            <div class="search-filters form-group margin-right-0">-->
<!--                                <ul>-->
<!--                                    <li class="listing_openTime"><a href="#" data-value="close"><i class="fa fa-clock-o"></i>--><?php //echo esc_html__( 'Open Now', 'listingpro' );?><!--</a></li>-->
<!--                                </ul>-->
<!--                            </div>-->
<!--                        --><?php //} ?>
<!---->
<!--                    </div>-->
<!--                </div>-->
<!--            --><?php //} ?>
<!--        </div>-->
<!--        <div class="clearfix"></div>-->
<!--    </div>-->
<!--    <div class="lp-title2 clearfix">-->
<!--<!--        <div class="pull-left"><a href="">Restaurant</a> <span>NewYork</span></div>-->
<!--<!--        <div class="pull-right">1-20 of 200</div>-->
<!--    </div>-->
    <div class="sidemap-container pull-right sidemap-fixed">
        <div class="map-pop map-container3" id="map-section">
            <div id='map' class="mapSidebar"></div>
        </div>
        <a href="#" class="open-img-view open-app-view">

            <?php echo wp_kses_post($icon_markup); ?>

        </a>
    </div>
    <div class="all-list-map"></div>
    <div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<?php echo apply_filters('listingpro_show_google_ads', 'archive', get_the_ID()); ?>
			</div>
		</div>
    <?php
    if( $my_query->have_posts() ) {
        echo '<div class="map-view-list-container">';
        while ($my_query->have_posts()) : $my_query->the_post();
            get_template_part( 'mobile/listing-loop-app-view2' );
        endwhile;
        wp_reset_query();
        echo '</div>';
    } ?>
    <div class="post-with-map-container-right">
        <div class="post-with-map-container">

            <div class="content-grids-wraps">
                <div class="clearfix lp-list-page-grid listing-loop-app-view-new" id="content-grids" >
                    <?php
                    $array['features'] = '';
                    ?>
                    <div class="promoted-listings">
                        <h3><?php echo esc_html__( 'EXCLUSIVE', 'listingpro' ); ?></h3>
                        <div class="app-view-new-ads-slider">
                            <?php
                            if( !empty($_GET['s']) && isset($_GET['s']) && $_GET['s']=="home" ){
                                echo listingpro_get_campaigns_listing( 'lp_top_in_search_page_ads', false,$taxQuery,$TxQuery,$priceQuery,$sKeyword, null, $ad_campaignsIDS);
                            }else{
                                echo listingpro_get_campaigns_listing( 'lp_top_in_search_page_ads', false, $TxQuery,$searchQuery,$priceQuery,$sKeyword, null, $ad_campaignsIDS);
                            }
                            ?>
                        </div>

                        <div class="md-overlay"></div>
                    </div>
                    <?php if(!empty($ad_campaignsIDS)){ ?>
                        <div class="lp-archive-page-border"></div>
                    <?php }?>
                    <?php
                    if( $my_query->have_posts() ) {
                        while ($my_query->have_posts()) : $my_query->the_post();
                            get_template_part( 'mobile/listing-loop-app-view-new' );
                        endwhile;
                        wp_reset_query();
                    }elseif(empty($ad_campaignsIDS)){
                        ?>
                        <div class="text-center margin-top-80 margin-bottom-80">
                            <h2><?php esc_html_e('No Results','listingpro'); ?></h2>
                            <p><?php esc_html_e('Sorry! There are no listings matching your search.','listingpro'); ?></p>
                            <p><?php esc_html_e('Try changing your search filters or','listingpro'); ?>
                                <?php
                                $currentURL = LP_current_URL();
                                ?>
                                <a href=""><?php esc_html_e('Reset Filter','listingpro'); ?></a>
                            </p>
                        </div>
                        <?php
                    }

                    ?>
                    <div class="md-overlay"></div>
                </div>
            </div>

            <?php
            echo '<div id="lp-pages-in-cats">';
            echo listingpro_load_more_filter($my_query, '1', $defSquery);
            echo '</div>';
            ?>
            <div class="lp-pagination pagination lp-filter-pagination-ajx"></div>
        </div>
    </div>
</section>