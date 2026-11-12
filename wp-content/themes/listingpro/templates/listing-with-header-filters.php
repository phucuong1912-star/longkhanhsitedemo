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
					if( !empty(lp_theme_option('lp_archivepage_listingorderby')) ){
						$lporderby = lp_theme_option('lp_archivepage_listingorderby');
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
					
					$defSquery = '';
					
					if($lporderby=="rand"){
						$lporders = '';
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
							);
							if( $includeChildren == false ){
                               $catQuery['include_children'] = $includeChildren;
							}
							$taxName = 'listing-category';
						}
						
						if( !empty($_GET['lp_s_loc']) && isset($_GET['lp_s_loc'])){							
							$lpsLoc = sanitize_text_field($_GET['lp_s_loc']);
							if(is_numeric($lpsLoc)){
                                $lpsLoc = $lpsLoc;
								if ($listingpro_options['lp_listing_search_locations_type'] == 'auto_loc') :
                                    $lp_file_content = 'file_';
                                    $lp_file_content .= 'get_';
                                    $lp_file_content .= 'contents';                                
                                    $url = $lp_file_content('https://maps.googleapis.com/maps/api/geocode/json?address='.$lpsLoc.'&key='.$listingpro_options['google_map_api']);
                                    $array = json_decode($url,TRUE);
                                    if (isset($array['results'][0]['address_components'][1]['long_name'])) :
                                        if (!empty($array['results'][0]['address_components'][1]['long_name'])) :
                                            $loc_name = $array['results'][0]['address_components'][1]['long_name'];
                                            $term = listingpro_term_exist($loc_name,'location');
                                            if(!empty($term)){
                                                $lpsLoc=$term['term_id'];
                                            }else{
                                                $lpsLoc = $lpsLoc;
                                            }
                                        else :
                                            $lpsLoc = $lpsLoc;
                                        endif;
                                    else :
                                        $lpsLoc = $lpsLoc;
                                    endif;
                                endif;
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
							);
							if( $includeChildren == false ){
                               $locQuery['include_children'] = $includeChildren;
							}
						}
						/* Search default result priority- Keyword then title */
						if( empty($_GET['lp_s_tag']) && empty($_GET['lp_s_cat']) && !empty($_GET['select']) ){                        
                                
                            $sKeyword = sanitize_text_field($_GET['select']);
                            $defSquery = $sKeyword;
                            $termExist = term_exists( $sKeyword, 'list-tags' );

                            if ( $termExist !== 0 && $termExist !== null ) {
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
						
						if( $includeChildren == false ){
                           $TxQuery[0]['include_children'] = $includeChildren;
						}else{
							$TxQuery[0]['include_children'] = true;
						}
						
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
                        'meta_query' => array($mtQuery),
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

					$listing_layout = $listingpro_options['listing_views'];
					$addClassListing = '';
                    if($listing_layout == 'list_view' || $listing_layout == 'list_view3') {
						$addClassListing = 'listing_list_view';
					}
                    $addClasscompact = '';
					if($listing_layout == 'lp-list-view-compact') {
						$addClasscompact = 'lp-compact-view-outer clearfix';
					}



                    $listing_style = $listingpro_options['listing_style'];
                    $listing_style_class    =   '';
                    if( $listing_style == 1 )
                    {
                        $listing_style_class    =   'listing-simple';
                    }
                    if( $listing_style == 2 )
                    {
                        $listing_style_class    =   'listing-with-sidebar';
                    }
                    if( $listing_style == 3 )
                    {
                        $listing_style_class    =   'listing-with-map';
                    }


                    $taxTaxDisplay = true;

                    if( !isset($_GET['s'])){

                        $queried_object = get_queried_object();

                        $term_id = $queried_object->term_id;

                        $taxName = $queried_object->taxonomy;

                        if(!empty($term_id)){

                            $termID = get_term_by('id', $term_id, $taxName);

                            $termName = $termID->name;

                            $parent = $termID->parent;

                            $term_ID = $termID->term_id;

                            if(is_tax('location')){

                                $loc_ID = $termID->term_id;
                            }
                            elseif(is_tax('features')){

                                $feature_ID = $termID->term_id;
                            }
                            elseif(is_tax('list-tags')){
                                $lpstag = $termID->name;
                            }
                        }

                    }elseif(isset($_GET['lp_s_cat']) || isset($_GET['lp_s_tag']) || isset($_GET['lp_s_loc'])){

                        if(isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat'])){
                            $sterm = wp_kses_post($_GET['lp_s_cat']);

                            $term_ID = wp_kses_post($_GET['lp_s_cat']);

                            $termo = get_term_by('id', $sterm, 'listing-category');

                            $termName = esc_html__('Results For','listingpro').' '.$termo->name;

                            $parent = $termo->parent;
                        }

                        if(isset($_GET['lp_s_cat']) && empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])){

                            $sterm = wp_kses_post($_GET['lp_s_tag']);

                            $lpstag = $sterm;

                            $termo = get_term_by('id', $sterm, 'list-tags');

                            $termName = esc_html__('Results For','listingpro').' '.$termo->name;
                        }
                        
                        if(isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])){

                            $sterm = wp_kses_post($_GET['lp_s_tag']);

                            $lpstag = $sterm;
                            $termo = get_term_by('id', $sterm, 'list-tags');

                            $termName = esc_html__('Results For','listingpro').' '.$termo->name;

                        }
                        
                        if(isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])){

                            $sloc = wp_kses_post($_GET['lp_s_loc']);

                            $loc_ID = wp_kses_post($_GET['lp_s_loc']);

                            if(is_numeric($sloc)){

                                $sloc = $sloc;
								if ($listingpro_options['lp_listing_search_locations_type'] == 'auto_loc') :
                                    $lp_file_content = 'file_';
                                    $lp_file_content .= 'get_';
                                    $lp_file_content .= 'contents';
                                    $url = $lp_file_content('https://maps.googleapis.com/maps/api/geocode/json?address='.$sloc.'&key='.$listingpro_options['google_map_api']);
                                    $array = json_decode($url,TRUE);
                                    if (isset($array['results'][0]['address_components'][1]['long_name'])) :
                                        if (!empty($array['results'][0]['address_components'][1]['long_name'])) :
                                            $loc_name = $array['results'][0]['address_components'][1]['long_name'];
                                            $term = listingpro_term_exist($loc_name,'location');
                                            if(!empty($term)){
                                                $sloc=$term['term_id'];
                                            }else{
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

                                if(!empty($termo)){

                                    $locName = esc_html__('In ','listingpro').$termo->name;
                                }
                            }
                            else{

                                $checkTerm = listingpro_term_exist($sloc,'location');

                                if(!empty($checkTerm)){

                                    $locTerm = get_term_by('name', $sloc, 'location');

                                    if( !empty($locTerm) ){

                                        $loc_ID = $locTerm->term_id;

                                        $locName = esc_html__('In ', 'listingpro').' '.$locTerm->name;
                                    }
                                }
                                else{
                                    $locName = esc_html__('In ', 'listingpro').' '.$sloc;
                                }
                            }
                        }

                    }

$emptySearchTitle = '';

if( empty($_GET['lp_s_tag']) && isset($_GET['lp_s_tag']) && empty($_GET['lp_s_cat']) && isset($_GET['lp_s_cat']) && empty($_GET['lp_s_loc']) && isset($_GET['lp_s_loc']) ){

    $emptySearchTitle = esc_html__('Most recent ', 'listingpro');

}

$sub_cats_loc   =   $listingpro_options['lp_listing_sub_cats_lcation'];
$sub_cats       =   $listingpro_options['lp_listing_sub_cats'];
?>

<!--==================================Section Open=================================-->

<section class="lp-section listing-style4" data-childcat="<?php echo esc_attr($sub_cats_loc); ?>" data-childcatshow="<?php echo esc_attr($sub_cats); ?>">

    <?php

    $v2_toggle  =   '';

    $listing_style = $listingpro_options['listing_style'];

    $listing_layoutt = $listingpro_options['listing_views'];

    if( $listing_layoutt == 'list_view_v2' || $listing_layoutt == 'grid_view_v2' || $listing_style == 4 ):

        $layout_class      =    '';

        if( $listing_layoutt == 'list_view_v2' || $listing_layoutt == 'grid_view_v2' )

        {

            $v2_toggle  =   'v2-toggle';

            if( $listing_layoutt == 'list_view_v2' )

            {

                $layout_class   =   'list';

            }

            if( $listing_layoutt == 'grid_view_v2' )

            {

                $layout_class   =   'grid';

            }

        }

        ?>

        <div data-layout-class="<?php echo esc_attr($layout_class); ?>" id="list-grid-view-v2" class="header-style-v2 <?php echo esc_attr($listing_layoutt); ?>"></div>

    <?php endif; ?>

    <div class="container">



        <div class="row">
			
            <?php if($sub_cats_loc == 'fullwidth'): ?>

                <?php get_template_part( 'templates/child-cats' );  ?>

            <?php endif; ?>
			
            <div class="col-md-8">

                <div class="lp-header-title">



                    <div class="row">

						

                        <div class="col-md-9">

                            <?php
                            $searchfilter = $listingpro_options['enable_search_filter'];
                            if (!empty($searchfilter) && $searchfilter == '1') {
                                ?>
                                <div class="form-inline lp-filter-inner" id="pop" style="padding-top: 1.5px">
                                    <a id="see_filter"><?php echo esc_html__('See Filters', 'listingpro'); ?></a>
                                    <div class="more-filter lp-filter-inner-wrapper" id="more_filters"
                                         style="display: block !important;">
                                        <div class="more-filter-left-col">
                                            <?php
                                            $nearmeOPT = $listingpro_options['enable_nearme_search_filter'];
                                            if (!empty($nearmeOPT) && $nearmeOPT == '1') {
                                                if( is_ssl() || LP_if_localhost() == 'true'){

                                                    $units = $listingpro_options['lp_nearme_filter_param'];
                                                    if (empty($units)) {
                                                        $units = 'km';
                                                    }
                                                    ?>
                                                    <div data-nearmeunit="<?php echo esc_attr($units); ?>"
                                                         id="lp-find-near-me"
                                                         class="search-filters  padding-right-0">
                                                        <ul>
                                                            <li class="lp-tooltip-outer">
                                                                <a class="btn default near-me-btn-style-3"><i
                                                                            class="fa fa-map-marker"
                                                                            aria-hidden="true"></i> <?php echo esc_html__('Near Me', 'listingpro'); ?>
                                                                </a>
                                                                <div class="lp-tooltip-div">
                                                                    <div class="lp-tooltip-arrow"></div>
                                                                    <div class="lp-tool-tip-content clearfix lp-tooltip-outer-responsive">
                                                                        <p class="margin-0">
                                                                            <?php echo esc_html__('Click To GET', 'listingpro'); ?>
                                                                        </p>

                                                                    </div>

                                                                </div>
                                                                <div class="lp-tooltip-div-hidden">
                                                                    <div class="lp-tooltip-arrow"></div>
                                                                    <div class="lp-tool-tip-content clearfix lp-tooltip-outer-responsive">
                                                                        <?php

                                                                        $minRange = $listingpro_options['enable_readious_search_filter_min'];
                                                                        $maxRange = $listingpro_options['enable_readious_search_filter_max'];
                                                                        $defVal = 100;
                                                                        if (isset($listingpro_options['enable_readious_search_filter_default'])) {
                                                                            $defVal = $listingpro_options['enable_readious_search_filter_default'];
                                                                        }
                                                                        ?>
                                                                        <div class="location-filters location-filters-wrapper">

                                                                            <div id="pac-container" class="clearfix">
                                                                                <div class="clearfix row">
                                                                                    <div class="lp-price-range-btnn col-md-1 text-right padding-0">
                                                                                        <?php echo esc_attr($minRange); ?>
                                                                                    </div>
                                                                                    <div class="col-md-9"
                                                                                         id="distance_range_div">
                                                                                        <input id="distance_range"
                                                                                               name="distance_range"
                                                                                               type="text"
                                                                                               data-slider-min="<?php echo esc_attr($minRange); ?>"
                                                                                               data-slider-max="<?php echo esc_attr($maxRange); ?>"
                                                                                               data-slider-step="1"
                                                                                               data-slider-value="<?php echo esc_attr($defVal); ?>"/>
                                                                                    </div>
                                                                                    <div class="col-md-2 padding-0 text-left lp-price-range-btnn">
                                                                                        <?php echo esc_attr($maxRange); ?>
                                                                                    </div>
                                                                                    <div style="display:none"
                                                                                         class="col-md-4"
                                                                                         id="distance_range_div_btn">
                                                                                        <a href=""><?php echo esc_html__('New Location', 'listingpro'); ?></a>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12 padding-top-10"
                                                                                     style="display:none">
                                                                                    <input id="pac-input"
                                                                                           name="pac-input" type="text"
                                                                                           placeholder="<?php echo esc_html__('Enter a location', 'listingpro'); ?>"
                                                                                           data-lat="" data-lng=""
                                                                                           data-center-lat=""
                                                                                           data-center-lng=""
                                                                                           data-ne-lat="" data-ne-lng=""
                                                                                           data-sw-lat="" data-sw-lng=""
                                                                                           data-zoom="">
                                                                                </div>
                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </li>

                                                        </ul>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>

                                            <?php
                                            $lp_bestmatch_on = $listingpro_options['enable_best_changed_search_filter'];
                                            if (!empty($lp_bestmatch_on) && $lp_bestmatch_on == '1') {
                                                ?>

                                                <div class="search-filters  padding-right-0">
                                                    <ul>
                                                        <li data-best="bestmatch"
                                                            class="lp-tooltip-outer lp-search-best-matches">
                                                            <a class="btn default"><i class="fa fa-random"
                                                                                      aria-hidden="true"></i> <?php echo esc_html__('Best Match', 'listingpro'); ?>
                                                            </a>
                                                            <div class="lp-tooltip-div">
                                                                <div class="lp-tooltip-arrow"></div>
                                                                <div class="lp-tool-tip-content clearfix">
                                                                    <p class="margin-0">
                                                                        <?php echo esc_html__('Click To See Your Best Match', 'listingpro'); ?>
                                                                    </p>

                                                                </div>

                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                            <div class="clearfix lp-show-on-mobile"></div>
                                            <?php
                                            //$radiusOPT = $listingpro_options['enable_readious_search_filter'];
                                            $radiusOPT = false;
                                            if (!empty($radiusOPT) && $radiusOPT == '1') {
                                                if( is_ssl() || LP_if_localhost() == 'true'){
                                                    ?>
                                                    <div class="search-filters  padding-right-0 lp-radus-filter-wrap">
                                                        <ul>
                                                            <li id="lp-filter-radius-wraper" class="lp-tooltip-outer">
                                                                <a class="btn default lp-distancesearchbtn"><?php echo esc_html__('Distance', 'listingpro'); ?></a>
                                                                <div class="lp-tooltip-div">
                                                                    <div class="lp-tooltip-arrow"></div>
                                                                    <div class="lp-tool-tip-content clearfix">
                                                                        <?php

                                                                        $minRange = $listingpro_options['enable_readious_search_filter_min'];
                                                                        $maxRange = $listingpro_options['enable_readious_search_filter_max'];
                                                                        ?>
                                                                        <div class="location-filters location-filters-wrapper">

                                                                            <div id="pac-container" class="clearfix">
                                                                                <div>
                                                                                    <div id="distance_range_div">
                                                                                        <input id="distance_range"
                                                                                               name="distance_range"
                                                                                               type="text"
                                                                                               data-slider-min="<?php echo esc_attr($minRange); ?>"
                                                                                               data-slider-max="<?php echo esc_attr($maxRange); ?>"
                                                                                               data-slider-step="1"
                                                                                               data-slider-value="100"/>

                                                                                    </div>
                                                                                    <div style="display:none"
                                                                                         class="col-md-4"
                                                                                         id="distance_range_div_btn">
                                                                                        <a href=""><?php echo esc_html__('New Location', 'listingpro'); ?></a>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12 padding-top-10"
                                                                                     style="display:none">

                                                                                    <input id="pac-input"
                                                                                           name="pac-input" type="text"
                                                                                           placeholder="<?php echo esc_html__('Enter a location', 'listingpro'); ?>"
                                                                                           data-lat="" data-lng=""
                                                                                           data-center-lat=""
                                                                                           data-center-lng=""
                                                                                           data-ne-lat="" data-ne-lng=""
                                                                                           data-sw-lat="" data-sw-lng=""
                                                                                           data-zoom="">
                                                                                </div>
                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php
                                            $highRatOPT = $listingpro_options['enable_high_rated_search_filter'];
                                            $highRewOPT = $listingpro_options['enable_most_reviewed_search_filter'];
                                            $highViewOPT = $listingpro_options['enable_most_viewed_search_filter'];
                                            if ((!empty($highRatOPT) && $highRatOPT == '1') || (!empty($highRewOPT) && $highRewOPT == '1') || (!empty($highViewOPT) && $highViewOPT == '1')) {
                                                ?>
                                                <div class="search-filters  padding-right-0">
                                                    <ul>
                                                        <li class="lp-tooltip-outer">
                                                            <a class="btn default"><i class="fa fa-sort"
                                                                                      aria-hidden="true"></i> <?php echo esc_html__('Sort By', 'listingpro'); ?>
                                                            </a>
                                                            <div class="lp-tooltip-div">
                                                                <div class="lp-tooltip-arrow"></div>
                                                                <div class="lp-tool-tip-content clearfix">
                                                                    <div class="sortbyrated-outer">
                                                                        <div class="border-dropdown sortbyrated">

                                                                            <ul class="comboboxCategory clearfix"
                                                                                id="select-lp-more-filter">
                                                                                <?php if ($highRewOPT == "1") { ?>
                                                                                    <li id="listingReviewed"
                                                                                        class="sortbyfilter"><a href=""
                                                                                                                data-value="listing_reviewed"><?php echo esc_html__('Most Reviewed', 'listingpro'); ?></a>
                                                                                    </li>
                                                                                <?php } ?>
                                                                                <?php if ($highViewOPT == "1") { ?>
                                                                                    <li id="mostviewed"
                                                                                        class="sortbyfilter"><a href=""
                                                                                                                data-value="mostviewed"><?php echo esc_html__('Most Viewed', 'listingpro'); ?></a>
                                                                                    </li>
                                                                                <?php } ?>
                                                                                <?php if ($highRatOPT == "1") { ?>
                                                                                    <li id="listingRate"
                                                                                        class="sortbyfilter"><a href="#"
                                                                                                                data-value="listing_rate"><?php echo esc_html__('Highest Rated', 'listingpro'); ?></a>
                                                                                    </li>
                                                                                <?php } ?>
                                                                            </ul>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                            <!-- end shebi-->
                                        </div>
                                    </div>


                                </div>
                            <?php } ?>

                        </div>

						

                        <div class="col-md-3">

                            <?php

                            $listing_view = $listingpro_options['listing_views'];

                            $active_list    =   '';

                            $active_grid    =   '';

                            if( $listing_view == 'list_view' || $listing_view == 'list_view3' || $listing_view == 'list_view_v2' )

                            {

                                $active_list    =   'active';

                            }

                            if( $listing_view != 'list_view' && $listing_view != 'list_view3' && $listing_view != 'list_view_v2' )

                            {

                                $active_grid    =   'active';

                            }

                           $grid_list_switch   =   $listingpro_options['hide_grid_switcher'];

                            if( $grid_list_switch == 'no' )

                            {

                                if($listing_view != 'lp-list-view-compact'  && $listing_view != 'grid_view_v3') {

                                    ?>

                                    <div class="lp-header-togglesa text-right">

                                        <div class="listing-view-layout listing-view-layout-v2">

                                            <ul>

                                                <li><a class="grid <?php echo esc_attr($active_grid); ?>" href="#"><i class="fa fa-th-large"></i></a></li>

                                                <li><a class="list <?php echo esc_attr($active_list); ?>" href="#"><i class="fa fa-list-ul"></i></a></li>

                                            </ul>

                                        </div>

                                    </div>

                                    <?php

                                }

                            }

                            ?>

                        </div>

                        <div class="clearfix"></div>

                    </div>
				<?php 
					$description_switch =   isset($listingpro_options['listingpro_show_terms_description_switch']) ? $listingpro_options['listingpro_show_terms_description_switch'] : '';
					if( $description_switch == 'yes' ){
						echo '<div class="lp_term_archive_description">'.term_description( get_queried_object_id(), $taxName ). '</div>';
					}
				?>
				<?php echo apply_filters('listingpro_show_google_ads', 'archive', ''); ?>

                </div>

                <div class="row">

                    <?php if($sub_cats_loc == 'content'): ?>

                        <?php get_template_part( 'templates/child-cats' );  ?>

                    <?php endif; ?>

                    <div class="listing-simple">



                        <div id="content-grids" class="listing-with-header-filters-wrap <?php echo esc_attr($v2_toggle).' '.esc_attr($addClasscompact); ?>">

                            <?php

                            $campaign_layout    =   'grid';

                            if( $listing_layout == 'list_view_v2' )

                            {

                                $campaign_layout    =   'list';

                                echo '<div class="lp-listings list-style active-view">

                                    <div class="search-filter-response">

                                        <div class="lp-listings-inner-wrap">';

                            }

                            if( $listing_layout == 'grid_view_v2' )

                            {

                                echo '<div class="lp-listings grid-style active-view">

                                    <div class="search-filter-response">

                                        <div class="lp-listings-inner-wrap">';

                            }

                            ?>

							<div class="promoted-listings">

                            <?php

                           

                                $array['features'] = '';

                                if( !empty($_GET['s']) && isset($_GET['s']) && $_GET['s']=="home" ){

                                    echo listingpro_get_campaigns_listing( 'lp_top_in_search_page_ads', false,$taxQuery,$TxQuery,$priceQuery,$sKeyword, 2, $ad_campaignsIDS);

                                }else{

                                    echo listingpro_get_campaigns_listing( 'lp_top_in_search_page_ads', false, $TxQuery,$searchQuery,$priceQuery,$sKeyword, null, $ad_campaignsIDS);

                                }

                           



                            ?>

							</div>

                            <?php

                            if( $my_query->have_posts() ):

                                global $listing_counter;

                                $listing_counter    =   1;

                                while ( $my_query->have_posts() ): $my_query->the_post();

                                    ?>

                                    <?php get_template_part( 'listing-loop' ); ?>

                                    <?php $listing_counter++; endwhile; wp_reset_postdata(); else: ?>

                                <div class="text-center margin-top-80 margin-bottom-80">

                                    <h2><?php esc_html_e('No Results','listingpro'); ?></h2>

                                    <p><?php esc_html_e('Sorry! There are no listings matching your search.','listingpro'); ?></p>

                                    <p><?php esc_html_e('Try changing your search filters or','listingpro'); ?>

                                        <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Visit Here','listingpro'); ?></a>

                                    </p>

                                </div>

                            <?php endif; ?>

                            <?php

                            if( $listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2' )

                            {

                                echo '    <div class="clearfix"></div></div>

                                </div>

                              </div>';

                            }

                            ?>





                        </div>

                    </div>

                    <?php

                    echo '<div id="lp-pages-in-cats">';

                    echo listingpro_load_more_filter($my_query, '1', $defSquery);

                    echo '</div>';

                    ?>

                    <input type="hidden" id="lp_current_query" value="<?php echo esc_attr($defSquery) ?>">

                </div>

            </div>

            <div class="col-md-4">

                <div class="lp-sidebar">

                    <div class="widget lp-widget map-widget map-no-btns">

                        <?php

                        $v2_map_load_old    =   '';

                        if( $listing_view != 'list_view_v2' && $listing_view != 'grid_view_v2' )

                        {

                            $v2_map_load_old    =   'v2_map_load_old';

                        }

                        ?>

                        <div class="v2-map-load <?php echo esc_attr($v2_map_load_old); ?>"></div>

                        <div class="map-pop">

                            <div id="map" class="mapSidebar"></div>

                        </div>

                    </div>

                    <?php

                    if( is_active_sidebar( 'listing_archive_sidebar' ) )

                    {

                        dynamic_sidebar( 'listing_archive_sidebar' );

                    }

                    ?>

                </div>

            </div>

        </div>

    </div>

</section>
<?php 
if( !empty($feature_ID) ){
    echo '<input type="checkbox" name="searchtags[]" id="check_featuretax" class="searchtags" value="'.$feature_ID.'" checked>';
}