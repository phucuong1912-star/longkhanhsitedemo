		<?php
								
			$listCats=array();
			$catIcon = '';
			$defaultCats = null;
			global $listingpro_options;
			$search_placeholder = $listingpro_options['search_placeholder'];
			$location_default_text = $listingpro_options['location_default_text'];
			$home_srch_loc_switchr = $listingpro_options['home_search_loc_switcher'];
			$search_loc_option = $listingpro_options['search_loc_option'];
			$lp_what_field_switcher = $listingpro_options['lp_what_field_switcher'];
			$search_loc_field_hide = $listingpro_options['lp_location_loc_switcher'];
			$hideWhereClass = '';
			$searchHide = '';
			if(isset($search_loc_field_hide) && $search_loc_field_hide==1){
				$hideWhereClass = "hide-where";
				$searchHide = "search-hide";
			} else {
				$searchHide = "";
			}
			$hideWhatClass = '';
			$whatHide = '';
			if(isset($lp_what_field_switcher) && $lp_what_field_switcher==1){
				$hideWhatClass = "hide-what";
				$whatHide = "what-hide";
			} else {
				$whatHide = "";
			}
			

			$srchBr = '';
			$slct = '';
			if ( $home_srch_loc_switchr == true ) {
				$srchBr = 'ui-widget';
				$slct = 'select2';
			}else {
				$srchBr = 'ui-widget border-dropdown';
				$slct = 'chosen-select chosen-select5';
			}

			$locOption = '';
			if ($search_loc_option == 'yes') {
				$locOption = 'yes';
			}elseif ($search_loc_option == 'no') {
				$locOption = 'no';
			}
			
			$locations_search_type = $listingpro_options['lp_listing_search_locations_type'];
			$locArea = '';
			if(!empty($locations_search_type) && $locations_search_type=="auto_loc"){
				$locArea = $listingpro_options['lp_listing_search_locations_range'];
			}
		?>
		<div class="lp-search-bar app-view-search-bar">
			<form autocomplete="off" class="form-inline" action="<?php echo home_url(); ?>" method="get" accept-charset="UTF-8">
				
				
					<?php 
						if( isset($lp_what_field_switcher) && $lp_what_field_switcher==0 ){
					?>
					<div class="form-group lp-suggested-search <?php echo esc_attr($hideWhereClass); ?> <?php echo esc_attr($hideWhatClass); ?>">
					<div class="input-group-addon lp-border"><?php esc_html_e('What','listingpro'); ?></div>
						<div class="pos-relative">
							<div class="what-placeholder pos-relative" data-holder="">
							<input autocomplete="off" type="text" class="lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields" name="select" id="select" placeholder="<?php echo esc_attr($search_placeholder); ?>" data-prev-value='0' data-noresult = "<?php echo esc_html__('More results for', 'listingpro'); ?>">
							<i class="cross-search-q fa fa-times-circle" aria-hidden="true"></i>
							<img class='loadinerSearch' width="100px" alt="image" src="<?php echo get_template_directory_uri().'/assets/images/search-load.gif' ?>"/>
							
							</div>
							<div id="input-dropdown">
								<ul>
									<?php
										
										$args = array(
											'post_type' => 'listing',
											'order' => 'ASC',
											'hide_empty' => false,
											'parent' => 0,
										);
										$default_search_cats = '';
										if(isset($listingpro_options['default_search_cats'])){
											$default_search_cats = $listingpro_options['default_search_cats'];
										}
										if(empty($default_search_cats)){
											$listCatTerms = get_terms( 'listing-category',$args);
											if ( ! empty( $listCatTerms ) && ! is_wp_error( $listCatTerms ) ){
												foreach ( $listCatTerms as $term ) {
													$catIcon = listingpro_get_term_meta( $term->term_id,'lp_category_image' );
													if (!empty($catIcon)) {
														if( hasFontAwesomeIconClass($catIcon) ){
															$catIcon = '<i class="icon icons-search-cat '.$catIcon.'"></i>'; 
														}else{
															$catIcon = '<img class="d-icon" alt="image" src="'.$catIcon.'" />';
														}
													}
													echo '<li class="lp-wrap-cats" data-catid="'.$term->term_id.'">'.$catIcon.'<span class="lp-s-cat">'.$term->name.'</span></li>';
													
													$defaultCats .='<li class="lp-wrap-cats" data-catid="'.$term->term_id.'">'.$catIcon.'<span class="lp-s-cat">'.$term->name.'</span></li>';
													
													
												}
											}
										}
										else{
												foreach ( $default_search_cats as $catTermID ) {
													$term = get_term_by('id', $catTermID, 'listing-category');
													$catIcon = listingpro_get_term_meta( $term->term_id,'lp_category_image' );
													if (!empty($catIcon)) {
														if( hasFontAwesomeIconClass($catIcon) ){
															$catIcon = '<i class="icon icons-search-cat '.$catIcon.'"></i>'; 
														}else{
															$catIcon = '<img class="d-icon" alt="image" src="'.$catIcon.'" />';
														}
													}
													echo '<li class="lp-wrap-cats" data-catid="'.$term->term_id.'">'.$catIcon.'<span class="lp-s-cat">'.$term->name.'</span></li>';
													
													$defaultCats .='<li class="lp-wrap-cats" data-catid="'.$term->term_id.'">'.$catIcon.'<span class="lp-s-cat">'.$term->name.'</span></li>';
													
													
												}
										}
									?>
									
								</ul>
								<!-- New Update 2.7.0 -->
								<div style="display:none" id="def-cats"><?php echo esc_attr($defaultCats);?></div>
								<!-- End New Update 2.7.0 -->
							</div>
						</div>
				
				</div>
				<?php } ?>
				
				<?php 
					if( isset($search_loc_field_hide) && $search_loc_field_hide==0 ){
						if( !empty($locations_search_type) && $locations_search_type=="auto_loc" ){
				?>
							<div class="form-group lp-location-search <?php echo esc_attr($hideWhatClass); ?>">
								<div class="input-group-addon lp-border lp-where"><?php esc_html_e('Where','listingpro'); ?></div>
								<div data-option="<?php echo esc_attr($locOption); ?>" class="<?php echo esc_attr($srchBr); ?>">
									<i class="fa fa-crosshairs"></i>
									<input autocomplete="off" id="cities" name="lp_s_loc" class="form-control" data-country="<?php echo esc_attr($locArea); ?>" placeholder="<?php echo esc_attr($location_default_text); ?>">
								</div>
							</div>
				<?php
						}
						elseif( !empty($locations_search_type) && $locations_search_type=="manual_loc" ){ ?>
						
							<div class="form-group lp-location-search <?php echo esc_attr($hideWhatClass); ?>">
								<div class="input-group-addon lp-border lp-where"><?php esc_html_e('Where','listingpro'); ?></div>
								<div data-option="<?php echo esc_attr($locOption); ?>" class="<?php echo esc_attr($srchBr); ?>">
									<i class="fa fa-crosshairs"></i>
								  <select class="<?php echo esc_attr($slct); ?>" name="lp_s_loc" id="searchlocation">
									<option id="def_location" value=""><?php echo esc_attr($location_default_text); ?></option>
									<?php 
										$args = array(
										'post_type' => 'listing',
										'order' => 'ASC',
										'hide_empty' => false,
										'parent' => 0,
										);
										$locations = get_terms( 'location',$args);
										if ( ! empty( $locations ) && ! is_wp_error( $locations ) ){
											foreach($locations as $location) {
												if(is_tax('location') && $term_ID == $location->term_id){
													$selected = 'selected';
												}else{
													$selected = '';
												}
												echo '<option '.$selected.' value="'.$location->term_id.'">'.$location->name.'</option>';
												$sub = get_term_children( $location->term_id, 'location' );
														foreach ( $sub as $subID ) {								
															$locationTerm = get_term_by( 'id', $subID, 'location' );
															echo  '<option '.$selected.' class="sub_cat" value="'.$locationTerm->term_id.'">-&nbsp;&nbsp;'.$locationTerm->name.'</option>';
														}
											}		
										}		
									?>	
								  </select>
								</div>
							</div>
							
				<?php	}
					}

					if( (isset($lp_what_field_switcher) && $lp_what_field_switcher==0) ||(isset($search_loc_field_hide) && $search_loc_field_hide==0) ){
				?>
							<div class="hide form-group pull-right <?php echo esc_attr($searchHide); ?>">
								<div class="lp-search-bar-right">
									<input value="<?php echo esc_html__( 'Search', 'listingpro' );?>" class="lp-search-btn" type="submit">
									<i class="fa-solid fa-magnifying-glass lp-search-icon"></i>
									<img alt="image" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/ellipsis.gif" class="searchloading">
									<!--
										<img alt="image" src="<?php //echo get_stylesheet_directory_uri(); ?>/assets/images/searchloader.gif" class="searchloading">
									-->
								</div>
							</div>
					<?php } ?>
				<input type="hidden" name="lp_s_tag" id="lp_s_tag">
				<input type="hidden" name="lp_s_cat" id="lp_s_cat">
				<input type="hidden" name="s" value="home">
				<input type="hidden" name="post_type" value="listing">	
				
			</form>
			
		</div>