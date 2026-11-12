<?php
global $listingpro_options;
$type = 'listing';
$term_id = '';
$taxName = '';
$termID = '';
$term_ID = '';
$termName = '';
$sterm = '';
$sloc = '';
$termName = '';
$locName = '';
$catterm = '';
$parent = '';
$loc_ID = '';
$locationID = '';
global $paged;
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

	}
}elseif(isset($_GET['lp_s_cat']) || isset($_GET['lp_s_tag']) || isset($_GET['lp_s_loc'])){

	if(isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat'])){
		$sterm = wp_kses_post($_GET['lp_s_cat']);
		$term_ID = wp_kses_post($_GET['lp_s_cat']);
		$termID = wp_kses_post($_GET['lp_s_cat']);
		$termo = get_term_by('id', $sterm, 'listing-category');
		$termName = esc_html__('Results For','listingpro').' <span class="font-bold term-name">'.$termo->name.'</span>';
		$parent = $termo->parent;
	}
	if(isset($_GET['lp_s_cat']) && empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])){
		$sterm = wp_kses_post($_GET['lp_s_tag']);
		$termo = get_term_by('id', $sterm, 'list-tags');
		$termName = esc_html__('Results For','listingpro').' <span class="font-bold">'.$termo->name.'</span>';
	}
	/* if(isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])){
		$sloc = $_GET['lp_s_loc'];
		$termo = get_term_by('id', $sloc, 'location');
		$locName = 'In <span class="font-bold">'.$termo->name.'</span>';
	} */
	if(isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])){
		$sloc =wp_kses_post($_GET['lp_s_loc']);
		$loc_ID = wp_kses_post($_GET['lp_s_loc']);
		if(is_numeric($sloc)){
			$sloc = $sloc;
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


$listing_style ='1';
$listingView ='grid_view';
$GridClass='';
$ListClass='';
$listing_style = $listingpro_options['listing_style'];
if(isset($_GET['list-style']) && !empty($_GET['list-style'])){
	$listing_style = $_GET['list-style'];
}
$listingView = $listingpro_options['listing_views'];
if($listingView == 'grid_view'){
	$GridClass= 'active';
}elseif($listingView == 'list_view'){
	$ListClass= 'active';
}


$searchfilter = $listingpro_options['enable_search_filter'];
if( !empty( $searchfilter ) && $searchfilter == '1' ) :
	?>

    <div class="lp-header-search-filters" id="filter-in-header">
        <div class="">

			<?php
			if(wp_is_mobile()):
				?>
                <div class="mobile-toggle-filters"><i class="fa fa-filter" aria-hidden="true"></i> <?php echo esc_html__( 'Show Filters', 'listingpro' ); ?></div>
			<?php endif; ?>



			<?php if(wp_is_mobile()): ?>
                <div class="clearfix"></div>
			<?php endif; ?>
            <div class="filters-wrap-for-mobile clearfix">

				<?php
				$priceOPT = $listingpro_options['enable_price_search_filter'];
				if( !empty( $priceOPT ) && $priceOPT == '1' ):
					$lp_priceSymbol = $listingpro_options['listing_pricerange_symbol'];
					$lp_priceSymbol2 = $lp_priceSymbol.$lp_priceSymbol;
					$lp_priceSymbol3 = $lp_priceSymbol2.$lp_priceSymbol;
					$lp_priceSymbol4 = $lp_priceSymbol3.$lp_priceSymbol;
					?>
                    <div class="price-filter clearfix">
                        <ul class="clearfix priceRangeFilter">
                            <li class="simptip-position-top simptip-movable" data-tooltip="<?php echo esc_html__( 'Inexpensive', 'listingpro' );?>" id="one"><a href="#" data-price="inexpensive"><?php echo wp_kses_post($lp_priceSymbol); ?></a></li>
                            <li class="simptip-position-top simptip-movable" data-tooltip="<?php echo esc_html__( 'Moderate', 'listingpro' );?>" id="two"><a href="#" data-price="moderate"><?php echo wp_kses_post($lp_priceSymbol2); ?></a></li>
                            <li class="simptip-position-top simptip-movable" data-tooltip="<?php echo esc_html__( 'Pricey', 'listingpro' );?>" id="three"><a href="#" data-price="pricey"><?php echo wp_kses_post($lp_priceSymbol3); ?></a></li>
                            <li class="simptip-position-top simptip-movable" data-tooltip="<?php echo esc_html__( 'Ultra High End', 'listingpro' );?>" id="four"><a href="#" data-price="ultra_high_end"><?php echo wp_kses_post($lp_priceSymbol4); ?></a></li>
                        </ul>
                    </div>
				<?php endif; ?>
				<?php
				$openTimeOPT = $listingpro_options['enable_opentime_search_filter'];
				if( !empty($openTimeOPT ) && $openTimeOPT == '1' ):
					?>
                    <div class="sort-filters-wrap listing_openTime">
                        <div class="sort-by-filter open-now-filter header-filter-wrap">
							<a data-time="close"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo esc_html__( 'Open Now', 'listingpro' );?></a>
                            <div class="sort-filter-inner">
                                <p><?php echo esc_html__( 'Click To See What Open Now', 'listingpro' );?></p>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>



            </div>
			<?php
			$showAdditionalFilter = lp_theme_option('enable_extrafields_filter');
			$countExtFilter = lp_get_extrafields_filter(false, $term_ID, true);
			if (!empty($showAdditionalFilter)) {
				// Start Advance filters add class for color
				$advance_filter_color = '';
				if( isset( $listingpro_options['lp_archive_bg']['url']) && !empty( $listingpro_options['lp_archive_bg']['url'] ) ){
					$advance_filter_color = 'lp-advence-filtr-colr';
				}
				// Start Advance filters add class for color
				?>

                <div class="more-filters <?php echo esc_attr($advance_filter_color); ?>"><i class="fa fa-sliders" aria-hidden="true"></i> <?php echo esc_html__( 'Advanced Filters', 'listingpro' ); ?></div>
			<?php } ?>
            <div class="clearfix  padding-bottom-20"></div>


        </div>

		<?php
		$showAdditionalFilter = lp_theme_option('enable_extrafields_filter');
		$features = listingpro_get_term_meta($term_ID,'lp_category_tags');
		if(empty($features)){
			$features = listingpro_get_term_meta($parent,'lp_category_tags');
		}
		if (!empty($showAdditionalFilter) || !empty($features)) {
			?>

            <div class="header-more-filters form-inline more-filters-container">
                <div class="">
					<?php if($taxTaxDisplay == true){ ?>
						<?php
						$count = 1;
						$featureName;
						$hasfeature = false;
						$showdivwrap = true;
						

						if(!empty($features)){ ?>

							<?php
							foreach($features as $feature){
								$terms = get_term_by('id', $feature, 'features');
								if(!empty($terms)){

									$featurCount = lp_count_postcount_taxonomy_term_byID('listing','features', $terms->term_id);

									if(!empty($featurCount)){
										$hasfeature = true;
									}
									if($hasfeature==true && $showdivwrap == true){
										?>
                                        <div class="form-inline lp-features-filter tags-area">
                                        <div class="form-group">
                                        <div class="input-group margin-right-0">
                                        <strong class="col-sm-2"><?php echo esc_html__( 'Features', 'listingpro' ); ?></strong>
                                        <div class="col-sm-10">
                                        <ul class="lp-features-inner-container">
										<?php
										$showdivwrap = false;
									}

									if(!empty($featurCount)){

										echo '<li>';
										echo '<div class="pad-bottom-10 checkbox ">';
										echo '<input type="checkbox" name="searchtags[]'.$count.'" id="check_'.$count.'" class="searchtags" value="'.$terms->term_id.'">';
										echo '<label for="'.$terms->term_id.'">'.$terms->name.'</label>';
										echo '</div>';
										echo '</li>';
									}
									$count++;
								}
							}


							?>
							<?php
							if($hasfeature==true){
								?>
                                </ul>
                                </div>
                                </div>
                                </div>
                                <div class="clearfix"></div>
                                </div>

								<?php
							}
							?>
							<?php
						}
						?>

					<?php } ?>

                   <?php
                    $showAdditionalFilter = lp_theme_option('enable_extrafields_filter');
                    if (!empty($showAdditionalFilter)) {
                        get_template_part('templates/search/more-filter');
                    }
				    ?>			



                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
			<?php
		}
		?>

    </div>

<?php endif; ?>