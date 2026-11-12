<?php

					$type = 'listing';
					$term_id = '';
					$taxName = '';
					$termName = '';
					$locName = '';
					$termID = '';
					$term_ID = '';
					global $paged, $listingpro_options;
					
					$lporderby = 'date';
					$lporders = 'DESC';
					if( isset($listingpro_options['lp_archivepage_listingorder']) ){
						$lporders = $listingpro_options['lp_archivepage_listingorder'];
					}
					if( isset($listingpro_options['lp_archivepage_listingorderby']) ){
						$lporderby = $listingpro_options['lp_archivepage_listingorderby'];
					}
					
					if($lporderby=="rand"){
						$lporders = '';
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
					
					$includeChildren = true;
					if(lp_theme_option('lp_children_in_tax')){
						if(lp_theme_option('lp_children_in_tax')=="no"){
							$includeChildren = false;
						}
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
								'include_children ' => $includeChildren,
							);
						}
						/* on 3 april by zaheer */
						if( empty($_GET['lp_s_tag']) && empty($_GET['lp_s_cat']) && !empty($_GET['select']) ){
							
							$sKeyword = sanitize_text_field($_GET['select']);
							
							$tagQuery = array(
								'taxonomy' => 'list-tags',
								'field' => 'name',
								'terms' => $sKeyword,
								'operator'=> 'IN' //Or 'AND' or 'NOT IN'
							);
							$sKeyword = '';
							$tagKeyword = sanitize_text_field($_GET['select']);
							
							
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

                    if($lpDefaultSearchBy == 'keyword') {
                        $args['s']  =   $sKeyword = sanitize_text_field($_GET['select']);
                    }
                    $my_query = null;
                    $my_query = new WP_Query($args);
                    $found = $my_query->found_posts;
                    if($found == 0){
                        unset($args['tax_query']);
                        $my_query = null;
                        $my_query = new WP_Query($args);
                        $found = $my_query->found_posts;
                    }
					if(($found > 1)){
						$foundtext = esc_html__('Results', 'listingpro');
					}else{
						$foundtext = esc_html__('Result', 'listingpro');
					}
					// Harry Code

					$listing_layout = $listingpro_options['listing_views'];
					$addClassListing = '';
					if($listing_layout == 'list_view') {
						$addClassListing = 'listing_list_view';
					}
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
					
					
					
				}
			}elseif(isset($_GET['lp_s_cat']) || isset($_GET['lp_s_tag']) || isset($_GET['lp_s_loc'])){
				
				if(isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat'])){
					$sterm = $_GET['lp_s_cat'];
					$term_ID = $_GET['lp_s_cat'];
					$termo = get_term_by('id', $sterm, 'listing-category');
					$termName = esc_html__('Results For','listingpro').' <span class="font-bold term-name">'.$termo->name.'</span>';
					$parent = $termo->parent;
				}	
				if(isset($_GET['lp_s_cat']) && empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])){
					$sterm = $_GET['lp_s_tag'];
					$lpstag = $sterm;
					$termo = get_term_by('id', $sterm, 'list-tags');
					$termName = esc_html__('Results For','listingpro').' <span class="font-bold">'.$termo->name.'</span>';
				}
				/* if(isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])){
					$sloc = $_GET['lp_s_loc'];
					$termo = get_term_by('id', $sloc, 'location');
					$locName = 'In <span class="font-bold">'.$termo->name.'</span>';
				} */
				if(isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])){
					$sloc = $_GET['lp_s_loc'];
					$loc_ID = $_GET['lp_s_loc'];
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
								$locName = esc_html__('In ', 'listingpro').'<span class="font-bold">'.$locTerm->name.'</span>';
							}
							
						}
						else{
							$locName = esc_html__('In ', 'listingpro').'<span class="font-bold">'.$sloc.'</span>';
						}
					}
					
				}
			}
			
			$emptySearchTitle = '';
			if( empty($_GET['lp_s_tag']) && isset($_GET['lp_s_tag']) && empty($_GET['lp_s_cat']) && isset($_GET['lp_s_cat']) && empty($_GET['lp_s_loc']) && isset($_GET['lp_s_loc']) ){
						$emptySearchTitle = esc_html__('Most recent ', 'listingpro');
					}
					$GridClass='';
					$ListClass='';
					$listingView = $listingpro_options['listing_views'];
					if($listingView == 'grid_view' || $listingView == 'grid_view2' || $listingView == 'grid_view3'){
						$GridClass= 'active';
					}elseif($listingView == 'list_view' || $listing_layout == 'list_view3'){
						$ListClass= 'active';
					}
			$headerSrch = $listingpro_options['search_switcher'];		
?>

	<!--==================================Section Open=================================-->
	<section class="page-container clearfix section-fixed listing-with-sidebar listing-with-map pos-relative taxonomy" id="<?php echo esc_attr($taxName); ?>">
        <?php
        $listing_layoutt = $listingpro_options['listing_views'];
        if( $listing_layoutt == 'list_view_v2' || $listing_layoutt == 'grid_view_v2' ):
            $header_style_v2   =    '';
            $layout_class      =    '';
            $listing_style = $listingpro_options['listing_style'];
            if( $listing_style == 4 )
            {
                $header_style_v2    =   'header-style-v2';
            }
            if( $listing_layoutt == 'list_view_v2' )
            {
                $layout_class   =   'list';
            }
            if( $listing_layoutt == 'grid_view_v2' )
            {
                $layout_class   =   'grid';
            }
            ?>
            <div data-layout-class="<?php echo esc_attr($layout_class); ?>" id="list-grid-view-v2" class=" <?php echo esc_attr($header_style_v2); ?>"></div>
        <?php endif; ?>
				<div class="lp-left-filter pull-left padding-top-30">
					<h3 class="col-md-12 margin-top-0"><?php echo esc_html__( 'Search', 'listingpro' );?></h3>
					<?php
							if($headerSrch == 1) {

									get_template_part('templates/search/top_search');

							}
						?>
					<div class="margin-bottom-20 margin-top-30">
							<?php get_template_part( 'templates/search/filter2'); ?>
					</div>
				</div>
				<div class="lp-center-content pull-left">
					<div class="">				
						<div class="filter-top-section pos-relative clearfix">							
							<div class="lp-title col-md-10 col-sm-10">
								<?php if(is_search()){ ?>
								<h3><?php echo wp_kses_post($termName); ?> <span class="dename"><?php echo esc_attr($emptySearchTitle); ?></span><span class="font-bold"><?php echo esc_html__( ' Listings', 'listingpro' );?></span> <?php echo esc_attr($locName); ?></h3>
								<?php }else{ ?>
								<h3><?php echo esc_html__( 'Results For ', 'listingpro' );?> <span class="font-bold term-name"><?php echo wp_kses_post($termName); ?></span><span class="font-bold"><?php echo esc_html__( ' Listings', 'listingpro' );?></span> </h3>
								<?php } ?>
							</div>
							<div class="pull-right margin-right-0 col-md-2 col-sm-2 clearfix">
								<div class="listing-view-layout">
									<ul>
										<li><a class="grid <?php echo esc_attr($GridClass); ?>" href="#"><i class="fa fa-th-large"></i></a></li>
										<li><a class="list <?php echo esc_attr($ListClass); ?>" href="#"><i class="fa fa-list-ul"></i></a></li>
									</ul>
								</div>
							</div>
						</div>

						<div class="content-grids-wraps">
							<div class="clearfix lp-list-page-grid" id="content-grids" >
                                <?php
                                $listing_layoutt = $listingpro_options['listing_views'];
                                $campaign_layout    =   'grid';
                                if( $listing_layoutt == 'list_view_v2' )
                                {
                                    $campaign_layout    =   'list';
                                    echo '<div class="lp-listings list-style active-view">
                                    <div class="search-filter-response">
                                        <div class="lp-listings-inner-wrap lp-listings-inner-wrap-with-sidebar">';
                                }
                                if( $listing_layoutt == 'grid_view_v2' )
                                {
                                    echo '<div class="lp-listings grid-style active-view">
                                    <div class="search-filter-response">
                                        <div class="lp-listings-inner-wrap lp-listings-inner-wrap-with-sidebar">';
                                }
                                if( $listing_layoutt != 'list_view_v2' && $listing_layoutt != 'grid_view_v2' ):
                                ?>
                                    <div class="promoted-listings">
                                        <?php
                                        if( !empty($_GET['s']) && isset($_GET['s']) && $_GET['s']=="home" ){
                                            echo listingpro_get_campaigns_listing( 'lp_top_in_search_page_ads', false,$taxQuery,$TxQuery,$priceQuery,$sKeyword, null, null);
                                        }else{
                                            echo listingpro_get_campaigns_listing( 'lp_top_in_search_page_ads', false, $TxQuery,$searchQuery,$priceQuery,$sKeyword, null, null);
                                        }
                                        ?>
                                        <div class="md-overlay"></div>
                                    </div>
                                <?php
                                else:
                                $array['features'] = '';
                                if( !empty($_GET['s']) && isset($_GET['s']) && $_GET['s']=="home" ){
                                    echo listingpro_get_campaigns_listing_v2( $campaign_layout, 'lp_top_in_search_page_ads', false,$taxQuery,$TxQuery,$priceQuery,$sKeyword, null, $ad_campaignsIDS);
                                }else{
                                    echo listingpro_get_campaigns_listing_v2( $campaign_layout,'lp_top_in_search_page_ads', false, $TxQuery,$searchQuery,$priceQuery,$sKeyword, null, $ad_campaignsIDS);
                                }
                                endif;
                                ?>
									<?php
									if( $my_query->have_posts() ) {
										while ($my_query->have_posts()) : $my_query->the_post();  
											get_template_part( 'listing-loop' );							
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
                                <?php
                                if( $listing_layoutt == 'list_view_v2' || $listing_layoutt == 'grid_view_v2' )
                                {
                                    echo '    <div class="clearfix"></div></div>
                                </div>
                              </div>';
                                }
                                ?>
							<div class="md-overlay"></div>
							</div>
						</div>
					
					<?php 
							echo '<div id="lp-pages-in-cats">';
							echo listingpro_load_more_filter($my_query, '1', $sKeyword);
							echo '</div>';
							
					 ?>
					<div class="lp-pagination pagination lp-filter-pagination-ajx"></div>
					</div>
					<input type="hidden" id="lp_current_query" value="<?php echo esc_attr($tagKeyword); ?>">
				</div>
				<div class="lp-right-map pull-left">
					<div class="sidemap-container sidemap-fixed">
						<div class="map-pop map-container3" id="map-section">
							<div id='map' class="mapSidebar"></div>
						</div>
						<a href="#" class="open-img-view"><i class="fa fa-file-image-o"></i></a>
					</div>
					<div class="all-list-map"></div>
				</div>
				
				
				
	</section>