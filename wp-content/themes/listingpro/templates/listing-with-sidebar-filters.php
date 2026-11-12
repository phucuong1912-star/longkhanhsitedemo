<?php

$type = 'listing';
$term_id = '';
$taxName = '';
$termID = '';
$term_ID = '';
global $paged, $listingpro_options;

$lporderby = 'date';
$lporders = 'DESC';
if (isset($listingpro_options['lp_archivepage_listingorder'])) {
    $lporders = $listingpro_options['lp_archivepage_listingorder'];
}
$mtQuery = array();

if (!empty(lp_theme_option('lp_archivepage_listingorderby'))) {
    $lporderby = lp_theme_option('lp_archivepage_listingorderby');
}
if ($lporderby == "post_views_count" || $lporderby == "listing_reviewed" || $lporderby == "listing_rate" || $lporderby == "claimed") {
    //$lporderby = 'meta_value_num';
    $lporders = '';
    $mtQuery = array(
        $lporderby . '_clause' =>
        array(
            'key' => $lporderby,
            'type' => 'numeric',
        ),
    );
    $lporderby = array($lporderby . '_clause' => 'DESC', 'title' => 'ASC');
}

$defSquery = '';

if ($lporderby == "rand") {
    $lporders = '';
}

$includeChildren = true;
if (lp_theme_option('lp_children_in_tax')) {
    if (lp_theme_option('lp_children_in_tax') == "no") {
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
if (isset($listingpro_options['listing_per_page'])) {
    $postsonpage = $listingpro_options['listing_per_page'];
} else {
    $postsonpage = 10;
}


if (!empty($_GET['s']) && isset($_GET['s']) && $_GET['s'] == "home") {
    if (!empty($_GET['lp_s_tag']) && isset($_GET['lp_s_tag'])) {
        $lpsTag = sanitize_text_field($_GET['lp_s_tag']);
        $tagQuery = array(
            'taxonomy' => 'list-tags',
            'field' => 'id',
            'terms' => $lpsTag,
            'operator' => 'IN' //Or 'AND' or 'NOT IN'
        );
    }

    if (!empty($_GET['lp_s_cat']) && isset($_GET['lp_s_cat'])) {
        $lpsCat = sanitize_text_field($_GET['lp_s_cat']);
        $catQuery = array(
            'taxonomy' => 'listing-category',
            'field' => 'id',
            'terms' => $lpsCat,
            'operator' => 'IN', //Or 'AND' or 'NOT IN'
        );
        if ($includeChildren == false) {
            $catQuery['include_children'] = $includeChildren;
        }
        $taxName = 'listing-category';
    }

    if (!empty($_GET['lp_s_loc']) && isset($_GET['lp_s_loc'])) {
        $lpsLoc = sanitize_text_field($_GET['lp_s_loc']);
        if (is_numeric($lpsLoc)) {
            $lpsLoc = $lpsLoc;
            if ($listingpro_options['lp_listing_search_locations_type'] == 'auto_loc') :
                $lp_file_content = 'file_';
                $lp_file_content .= 'get_';
                $lp_file_content .= 'contents';
                $url = $lp_file_content('https://maps.googleapis.com/maps/api/geocode/json?address=' . $lpsLoc . '&key=' . $listingpro_options['google_map_api']);
                $array = json_decode($url, TRUE);
                if (isset($array['results'][0]['address_components'][1]['long_name'])) :
                    if (!empty($array['results'][0]['address_components'][1]['long_name'])) :
                        $loc_name = $array['results'][0]['address_components'][1]['long_name'];
                        $term = listingpro_term_exist($loc_name, 'location');
                        if (!empty($term)) {
                            $lpsLoc = $term['term_id'];
                        } else {
                            $lpsLoc = $lpsLoc;
                        }
                    else :
                        $lpsLoc = $lpsLoc;
                    endif;
                else :
                    $lpsLoc = $lpsLoc;
                endif;
            endif;
        } else {
            $term = listingpro_term_exist($lpsLoc, 'location');
            if (!empty($term)) {
                $lpsLoc = $term['term_id'];
            } else {
                $lpsLoc = '';
            }
        }
        $locQuery = array(
            'taxonomy' => 'location',
            'field' => 'id',
            'terms' => $lpsLoc,
            'operator' => 'IN', //Or 'AND' or 'NOT IN'
        );
        if ($includeChildren == false) {
            $locQuery['include_children'] = $includeChildren;
        }
    }
    /* Search default result priority- Keyword then title */
    if (empty($_GET['lp_s_tag']) && empty($_GET['lp_s_cat']) && !empty($_GET['select'])) {

        $sKeyword = sanitize_text_field($_GET['select']);
        $defSquery = $sKeyword;
        $termExist = term_exists($sKeyword, 'list-tags');

        if ($termExist !== 0 && $termExist !== null) {
            $tagQuery = array(
                'taxonomy' => 'list-tags',
                'field' => 'name',
                'terms' => $sKeyword,
                'operator' => 'IN' //Or 'AND' or 'NOT IN'
            );
            $sKeyword = '';
            $tagKeyword = sanitize_text_field($_GET['select']);
            $defSquery = $tagKeyword;
        }
    }

    $TxQuery = array(
        'relation' => 'AND',
        $tagQuery,
        $catQuery,
        $locQuery,
    );

    $ad_campaignsIDS = listingpro_get_campaigns_listing('lp_top_in_search_page_ads', TRUE, $taxQuery, $TxQuery, $priceQuery, $sKeyword, null, null);
} else {
    $queried_object = get_queried_object();
    $term_id = $queried_object->term_id;
    $taxName = $queried_object->taxonomy;
    if (!empty($term_id)) {
        $termID = get_term_by('id', $term_id, $taxName);
        $termName = $termID->name;
        $term_ID = $termID->term_id;
    }

    $TxQuery = array(
        array(
            'taxonomy' => $taxName,
            'field' => 'id',
            'terms' => $termID->term_id,
            'operator' => 'IN' //Or 'AND' or 'NOT IN'
        ),
    );

    if ($includeChildren == false) {
        $TxQuery[0]['include_children'] = $includeChildren;
    } else {
        $TxQuery[0]['include_children'] = true;
    }

    $ad_campaignsIDS = listingpro_get_campaigns_listing('lp_top_in_search_page_ads', TRUE, $TxQuery, $searchQuery, $priceQuery, $sKeyword, null, null);
}

$args = array(
    'post_type' => $type,
    'post_status' => 'publish',
    'posts_per_page' => $postsonpage,
    's'    => $sKeyword,
    'paged'  => $paged,
    'post__not_in' => $ad_campaignsIDS,
    'tax_query' => $TxQuery,
    'meta_query' => array($mtQuery),
    'orderby' => $lporderby,
    'order'   => $lporders,
);

$my_query = null;
$my_query = new WP_Query($args);
$found = $my_query->found_posts;

if (($found > 1)) {
    $foundtext = esc_html__('Results', 'listingpro');
} else {
    $foundtext = esc_html__('Result', 'listingpro');
}

$listing_layout = $listingpro_options['listing_views'];
$addClassListing = '';
if ($listing_layout == 'list_view' || $listing_layout == 'list_view3') {
    $addClassListing = 'listing_list_view';
}
$addClasscompact = '';
if ($listing_layout == 'lp-list-view-compact') {
    $addClasscompact = 'lp-compact-view-outer clearfix';
}



$listing_style = $listingpro_options['listing_style'];
$listing_style_class    =   '';
if ($listing_style == 1) {
    $listing_style_class    =   'listing-simple';
}
if ($listing_style == 2) {
    $listing_style_class    =   'listing-with-sidebar';
}
if ($listing_style == 3) {
    $listing_style_class    =   'listing-with-map';
}


$taxTaxDisplay = true;

if (!isset($_GET['s'])) {

    $queried_object = get_queried_object();

    $term_id = $queried_object->term_id;

    $taxName = $queried_object->taxonomy;

    if (!empty($term_id)) {

        $termID = get_term_by('id', $term_id, $taxName);

        $termName = $termID->name;

        $parent = $termID->parent;

        $term_ID = $termID->term_id;

        if (is_tax('location')) {

            $loc_ID = $termID->term_id;
        } elseif (is_tax('features')) {

            $feature_ID = $termID->term_id;
        } elseif (is_tax('list-tags')) {
            $lpstag = $termID->name;
        }
    }
} elseif (isset($_GET['lp_s_cat']) || isset($_GET['lp_s_tag']) || isset($_GET['lp_s_loc'])) {

    if (isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat'])) {
        $sterm = wp_kses_post($_GET['lp_s_cat']);

        $term_ID = wp_kses_post($_GET['lp_s_cat']);

        $termo = get_term_by('id', $sterm, 'listing-category');

        $termName = esc_html__('Results For', 'listingpro') . ' ' . $termo->name;

        $parent = $termo->parent;
    }

    if (isset($_GET['lp_s_cat']) && empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])) {

        $sterm = wp_kses_post($_GET['lp_s_tag']);

        $lpstag = $sterm;

        $termo = get_term_by('id', $sterm, 'list-tags');

        $termName = esc_html__('Results For', 'listingpro') . ' ' . $termo->name;
    }

    if (isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])) {

        $sterm = wp_kses_post($_GET['lp_s_tag']);

        $lpstag = $sterm;
        $termo = get_term_by('id', $sterm, 'list-tags');

        $termName = esc_html__('Results For', 'listingpro') . ' ' . $termo->name;
    }

    if (isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])) {

        $sloc = wp_kses_post($_GET['lp_s_loc']);

        $loc_ID = wp_kses_post($_GET['lp_s_loc']);

        if (is_numeric($sloc)) {

            $sloc = $sloc;
            if ($listingpro_options['lp_listing_search_locations_type'] == 'auto_loc') :
                $lp_file_content = 'file_';
                $lp_file_content .= 'get_';
                $lp_file_content .= 'contents';
                $url = $lp_file_content('https://maps.googleapis.com/maps/api/geocode/json?address=' . $sloc . '&key=' . $listingpro_options['google_map_api']);
                $array = json_decode($url, TRUE);
                if (isset($array['results'][0]['address_components'][1]['long_name'])) :
                    if (!empty($array['results'][0]['address_components'][1]['long_name'])) :
                        $loc_name = $array['results'][0]['address_components'][1]['long_name'];
                        $term = listingpro_term_exist($loc_name, 'location');
                        if (!empty($term)) {
                            $sloc = $term['term_id'];
                        } else {
                            $sloc = $sloc;
                        }
                    else :
                        $sloc = $sloc;
                    endif;
                else :
                    $sloc = $sloc;
                endif;
            endif;

            $termo = get_term_by('id', $sloc, 'location');

            if (!empty($termo)) {

                $locName = esc_html__('In ', 'listingpro') . $termo->name;
            }
        } else {

            $checkTerm = listingpro_term_exist($sloc, 'location');

            if (!empty($checkTerm)) {

                $locTerm = get_term_by('name', $sloc, 'location');

                if (!empty($locTerm)) {

                    $loc_ID = $locTerm->term_id;

                    $locName = esc_html__('In ', 'listingpro') . ' ' . $locTerm->name;
                }
            } else {
                $locName = esc_html__('In ', 'listingpro') . ' ' . $sloc;
            }
        }
    }
}
// Harry Code


$listing_layout = $listingpro_options['listing_views'];
$addClassListing = '';
if ($listing_layout == 'list_view' || $listing_layout == 'list_view3') {
    $addClassListing = 'listing_list_view';
}

$cat_name   =   '';
$loc_name   =   '';
if (isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat'])) {
    $term_obj   =   get_term_by('id', $_GET['lp_s_cat'], 'listing-category');
    $cat_name   =   $term_obj->name;
}
if (isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])) {
    if (is_numeric($_GET['lp_s_loc'])) {
        $sloc = $_GET['lp_s_loc'];
        if ($listingpro_options['lp_listing_search_locations_type'] == 'auto_loc') :
            $lp_file_content = 'file_';
            $lp_file_content .= 'get_';
            $lp_file_content .= 'contents';
            $url = $lp_file_content('https://maps.googleapis.com/maps/api/geocode/json?address=' . $sloc . '&key=' . $listingpro_options['google_map_api']);
            $array = json_decode($url, TRUE);
            if (isset($array['results'][0]['address_components'][1]['long_name'])) :
                if (!empty($array['results'][0]['address_components'][1]['long_name'])) :
                    $loc_name = $array['results'][0]['address_components'][1]['long_name'];
                    $term = listingpro_term_exist($loc_name, 'location');
                    if (!empty($term)) {
                        $sloc = $term['term_id'];
                    } else {
                        $sloc = $_GET['lp_s_loc'];
                    }
                else :
                    $sloc = $_GET['lp_s_loc'];
                endif;
            else :
                $sloc = $_GET['lp_s_loc'];
            endif;
        endif;
        $termo = get_term_by('id', $sloc, 'location');
        if (!empty($termo)) {
            $locName = esc_html__('In ', 'listingpro') . $termo->name;
        }
    } else {
        $term_obj   =   get_term_by('name', $_GET['lp_s_loc'], 'location');
        $loc_name   =   ' ' . $term_obj->name;
    }
}

$current_search_text    =   esc_html__('Recent Listings', 'listingpro');
if (!empty($cat_name) || !empty($loc_name)) {
    $current_search_text    =   $cat_name . '' . $loc_name;
}
$showing_results    =   '';
if ($found != 0) {
    if ($found > $postsonpage) {
        $showing_results    =   esc_html__('Showing', 'listingpro') . ' 1-' . $postsonpage . ' ' . esc_html__('of', 'listingpro') . ' ' . $found;
    } else {
        $showing_results    =   esc_html__('Showing', 'listingpro') . ' ' . $found . ' ' . esc_html__('of', 'listingpro') . ' ' . $found;
    }
}
?>

<!--==================================Section Open=================================-->

<section class="lp-sidebar-filters-style sidebar-filters page-container clearfix section-fixed listing-with-map pos-relative taxonomy lp-grid-width1 " id="<?php echo esc_attr($taxName); ?>">
    <p class="view-on-map">
        <!-- Marker icon by Icons8 -->
        <?php echo listingpro_icons('whiteMapMarkerFill'); ?>
        <a class="md-trigger sidebar-filters-map-pop" data-close-map="<?php echo esc_html_e('Close Map', 'listingpro'); ?>" data-full-map="<?php echo esc_html_e('View on map', 'listingpro'); ?>"><?php echo esc_html_e('View on map', 'listingpro'); ?></a>
    </p>
    <?php
    $v2_map_class   =   '';
    if ($listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2') :
        $header_style_v2   =    '';
        $v2_map_class       =   'v2-map-load';
        $layout_class      =    '';
        $listing_style = $listingpro_options['listing_style'];
        if ($listing_style == 4) {
            $header_style_v2    =   'header-style-v2';
        }
        if ($listing_layout == 'list_view_v2') {
            $layout_class   =   'list';
        }
        if ($listing_layout == 'grid_view_v2') {
            $layout_class   =   'grid';
        }
    ?>
        <div data-layout-class="<?php echo esc_attr($layout_class); ?>" id="list-grid-view-v2" class=" <?php echo esc_attr($header_style_v2); ?> <?php echo esc_attr($v2_map_class); ?> <?php echo esc_attr($listing_layout); ?>"></div>
    <?php endif; ?>

    <div class="sidemap-container pull-right sidemap-fixed">
        <div class="overlay_on_map_for_filter"></div>
        <div class="map-pop map-container3" id="map-section">
            <div id='map' class="mapSidebar"></div>
        </div>
        <a href="#" class="open-img-view"><i class="fa fa-file-image-o"></i></a>
    </div>
    <div class="all-list-map"></div>


    <div class=" pull-left post-with-map-container-right">
        <div class="post-with-map-container pull-left">


            <!-- archive adsense space before filter -->
            <?php
            //show google ads
            echo apply_filters('listingpro_show_google_ads', 'archive', '');
            ?>

            <div class="sidebar-filters-wrap">

                <?php get_template_part('templates/search/sidebar-filter'); ?>
            </div>
            <div class="mobile-map-space">

                <!-- Popup Open -->

                <div class="md-modal md-effect-3 mobilemap" id="modal-listing">
                    <div class="md-content mapbilemap-content">
                        <div class="map-pop">
                            <div id='map' class="listingmap"></div>
                        </div>
                        <a class="md-close mapbilemap-close"><i class="fa fa-close"></i></a>
                    </div>
                </div>
                <!-- Popup Close -->
                <div class="md-overlay md-overlayi"></div> <!-- Overlay for Popup -->
            </div>
            <div class="md-overlay md-overlayi"></div> <!-- Overlay for Popup -->
            <div class="content-grids-wraps">
                <div class="lp-title clearfix lp-title-new-style">
                    <div class="pull-left">
                        <div class="lp-filter-name sidebar-filter-process">
                            <span><?php echo esc_attr($current_search_text); ?></span>
                            <span><?php echo esc_attr($showing_results); ?></span>

                        </div>

                    </div>
                    <div class="pull-right clearfix">
                        <div class="listing-view-layout">
                            <ul>
                                <li><a class="active lp-grid-one" href="#"><i class="fa fa-stop" aria-hidden="true"></i></a></li>
                                <li><a class="lp-grid-two" href="#"><i class="fa fa-th-large" aria-hidden="true"></i></a></li>
                                <li><a class="lp-grid-three" href="#"><i class="fa fa-th" aria-hidden="true"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
				<?php 
					$description_switch =   isset($listingpro_options['listingpro_show_terms_description_switch']) ? $listingpro_options['listingpro_show_terms_description_switch'] : '';
					if( $description_switch == 'yes' ){
						echo '<div class="lp_term_archive_description">'.term_description( get_queried_object_id(), $taxName ). '</div>';
					}
				?>
                <div class="clearfix lp-list-page-grid" id="content-grids">
                    <?php
                    if ($listing_layout == 'list_view_v2') {
                        echo '<div class="lp-listings list-style active-view">
                                    <div class="search-filter-response">
                                        <div class="lp-listings-inner-wrap">';
                    }
                    if ($listing_layout == 'grid_view_v2' || $listing_layout == 'grid_view_v3' || $listing_layout == 'grid_view2' || $listing_layout == 'grid_view') {
                        echo '<div class="lp-listings grid-style active-view">
                                    <div class="search-filter-response">
                                        <div class="lp-listings-inner-wrap">';
                    }
                    ?>
                    <?php
                    $array['features'] = '';
                    ?>
                    <div class="promoted-listings">
                        <?php
                        if (!empty($_GET['s']) && isset($_GET['s']) && $_GET['s'] == "home") {
                            echo listingpro_get_campaigns_listing('lp_top_in_search_page_ads', false, $taxQuery, $TxQuery, $priceQuery, $sKeyword, null, $ad_campaignsIDS);
                        } else {
                            echo listingpro_get_campaigns_listing('lp_top_in_search_page_ads', false, $TxQuery, $searchQuery, $priceQuery, $sKeyword, null, $ad_campaignsIDS);
                        }
                        ?>
                        <div class="md-overlay"></div>
                    </div>
                    <?php
                    if ($my_query->have_posts()) {
                        while ($my_query->have_posts()) : $my_query->the_post();
                            get_template_part('listing-loop');
                        endwhile;
                        wp_reset_query();
                    } elseif (empty($ad_campaignsIDS)) {
                    ?>
                        <div class="text-center margin-top-80 margin-bottom-80">
                            <h2><?php esc_html_e('No Results', 'listingpro'); ?></h2>
                            <p><?php esc_html_e('Sorry! There are no listings matching your search.', 'listingpro'); ?></p>
                            <p><?php esc_html_e('Try changing your search filters or', 'listingpro'); ?>
                                <?php
                                $currentURL = LP_current_URL();
                                ?>
                                <a href=""><?php esc_html_e('Reset Filter', 'listingpro'); ?></a>
                            </p>
                        </div>
                    <?php
                    }

                    ?>
                    <div class="md-overlay"></div>
                    <?php
                    if ($listing_layout == 'list_view_v2' || $listing_layout == 'grid_view' || $listing_layout == 'grid_view2' || $listing_layout == 'grid_view_v2' || $listing_layout == 'grid_view_v3') {
                        echo '   <div class="clearfix"></div> <div>
                                <div>
                              <div><div class="clearfix"></div>';
                    }
                    ?>

                    <?php
                    echo '<div id="lp-pages-in-cats">';
                    echo listingpro_load_more_filter($my_query, '1', $defSquery);
                    echo '</div>';
                    ?>
                </div>
            </div>

            <div class="lp-pagination pagination lp-filter-pagination-ajx"></div>
        </div>
        <input type="hidden" id="lp_current_query" value="<?php echo esc_attr($defSquery); ?>">
    </div>

</section>