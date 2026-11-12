<?php
	global $listingpro_options;

	$search_view = $listingpro_options['search_views'];
	$search_layout = $listingpro_options['search_vertical_layout'];
	$alignment = $listingpro_options['search_vertical_layout'];

	$layoutClass = '';
	if ( $alignment == 'right_side' ) {
		$layoutClass = 'lp-right-grid';
	}elseif ( $alignment == 'left_side' ) {
		$layoutClass = 'lp-left-grid';
	}

	$searchViewClass = '';
	if ( $search_view == 'light' ) {
		$searchViewClass = 'lp-right-with-white-bg';
	}elseif ( $search_view == 'dark' ) {
		$searchViewClass = 'lp-bg-black';
	}elseif ( $search_view == 'grey' ) {
		$searchViewClass = 'lp-bg-grey';
	}

?>
<div class="absolute vertical-view">
	<div class="container">
		<div class="lp-search-bar-all-demo <?php echo esc_attr($layoutClass); ?> <?php echo esc_attr($searchViewClass); ?>">
			<span class="lp-search-title"><?php esc_html_e('Brwose Anything!', 'listingpro'); ?></span>
			<div class="lp-search-bar">	
				<form action="" method="get" accept-charset="UTF-8">
					<div class="lp-interest-bar">
						<div class="input-group input-group-sm">
							<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
							<div class="icon-addon addon-sm">
								<input type="text" placeholder="What is your interest" class="form-control" id="email">
							</div>
						</div>
					</div>
					<div class="lp-location-bar lp-search-bar-left">
						<div class="input-group input-group-sm">
							<span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
							<div class="ui-widget border-dropdown">
							  <select class="chosen-select chosen-select5" name="searchlocation" id="searchlocation">
								<option value=""><?php esc_html_e('Location', 'listingpro'); ?></option>
								<option value=""><?php esc_html_e('All Locations', 'listingpro'); ?></option>
							  </select>
							</div>
						</div>
					</div>
					<div class="lp-catagory-bar lp-search-bar-left">
						<div class="input-group input-group-sm">
							<span class="input-group-addon" style="width: 40px; height: 38px;">								
								<img class="icon icons8-Define-Location" alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAC6UlEQVRoQ+2ZjW0UQQyFXyogHQQqIHQQKgAqCFSQpALoAFJBoAJIBaEDoAKSDpIKgj5pLI3M7Ix39ucIWUun1el2ZvzsZ4/t29Nycu+23lviqEU2TYpuACIe2zxQsdJGobUp9FTSsSSeh+mT6/BTEp9rSV/SM6Jj9Z2pMbAv6ZWk04LCLeUA8ymBab07+PsUAK8lfUwW71YgeeJM0reeTXoAYPULSQDw8kvS50SV2/TkHSjFOp5vJT0vrAXAO0msC8tYAPD7a4Eu54kO8Dsi7APtTtzL0OrNmPgYA4BDfyRL2rmXSZGo4h4cexIHxJEJHngRBREFgPuvnOWxOlacQwCRewNPvIzQKQoA2uSch6twfU4hNogtE2ICOlUlAgDFAWAyp+W9ct4TAKhmpxYAqAPv4SoC50vZp2WoMb+jsMUEsfWstrgFwLuVzXoDNgoCY/3OXq7StQUA65O7kSWpU6MSAU1WKkoNAIoDwGQN69tZ3guDZ9cAfJD0Pu3IDWueiFJh6ntY3m5sSg0C/C+pASBNUl0igxtM1bKynjuGWguheiUeRwHI+c+l8n1BZUtbH6XLk98G4wAP+M6ptBlBxCZrio/B4tlRAK1stRSwpnEfDYB/mkJD7s/T2K6DeDCN/9dpNL/Iqtf5QhGcp/Gui+zBlxJ2gdh1vqtirlrGtPL7gy+n8QL1/0HieajNmxgTeft605o7tTyALrtsKZvpOwIAEHmbx/c1mvpQ+xoFQG9MNZpP1KjPSW9zCGVzPqIhcKlGm1O6KACUpEviPniSaYxnANHbJ7MnyueDgrt0VlN59BgDwECgtJ9t4g3SbBQIijPI8oOxsOXNiGMBsA460a3l40DbDw/ZcBdLWg/BpYjnbLhbak/hPGk7ZPkpAGwtbsfylmJ7Y4FUiSdWG697RbEaCpRG5jVQ0AUDTBpR9lBoSCl4jVfs7yUPCIXtbyasHY2XqmfnBOAP2v6ljATV5oGKlTYKPQoK/QHpWqYtD0X8igAAAABJRU5ErkJggg==" width="48" height="48">								
							</span>
							<div class="ui-widget border-dropdown">
							  <select class="chosen-select chosen-select5" name="searchlocation" id="searchlocation">
								<option value=""><?php esc_html_e('Catagory', 'listingpro'); ?></option>
								<option value=""><?php esc_html_e('All Locations', 'listingpro'); ?></option>
							  </select>
							</div>
						</div>
					</div>
					<div class="lp-search-bar-price lp-search-bar-left">
						<div class="lp-interest-bar">
							<div class="input-group input-group-sm">
								<div class="icon-addon addon-sm">
									<input type="text" placeholder="Price" class="form-control" id="email">
								</div>
							</div>
						</div>
					</div>
					<div class="lp-interest-bar text-center">
						<div class="lp-feature-event" id="lp_feature_panel">
							<div class="lp-range-slider">
								<!--Range slider strt-->
								<input type="range" value="10,80" />
								<!--Range slider end-->
							</div>
							<div class="lp-feature-title"><?php esc_html_e('Features', 'listingpro'); ?>:</div>
							<div class="form-group">
								<div class="checkbox pad-bottom-10">
									<input id="chk2" type="checkbox" name="remember" value="price-on-call">
									<label for="chk2"><?php esc_html_e('Wifi', 'listingpro'); ?></label>
								</div>
							</div>
							<div class="form-group">
								<div class="checkbox pad-bottom-10">
									<input id="chk3" type="checkbox" name="remember" value="price-on-call">
									<label for="chk3"><?php esc_html_e('Event', 'listingpro'); ?></label>
								</div>
							</div>
							<div class="form-group">
								<div class="checkbox pad-bottom-10">
									<input id="chk4" type="checkbox" name="remember" value="price-on-call">
									<label for="chk4"><?php esc_html_e('Hotel', 'listingpro'); ?></label>
								</div>
							</div>
							<div class="form-group">
								<div class="checkbox pad-bottom-10">
									<input id="chk5" type="checkbox" name="remember" value="price-on-call">
									<label for="chk5"><?php esc_html_e('Trip', 'listingpro'); ?></label>
								</div>
							</div>
							<div class="form-group">
								<div class="checkbox pad-bottom-10">
									<input id="chk6" type="checkbox" name="remember" value="price-on-call">
									<label for="chk6"><?php esc_html_e('Bar', 'listingpro'); ?></label>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
						<a class="add-more" id="flip">
							<span><?php esc_html_e('Advance', 'listingpro'); ?></span>
							<i class="fa fa-plus-square-o" aria-hidden="true"></i>
						</a>
						<div class="lp-search-bar-right">
							<input type="submit"  value="Search" class="lp-search-btn" />
							<i class="fa-solid fa-magnifying-glass lp-search-icon"></i>
						</div>
					</div>
					<div class="clearfix"></div>
				</form>
			</div><!-- ../search-bar -->
		</div>
		<div class="clearfix"></div>
	</div>
</div>