<?php
	global $listingpro_options;
	
	$listing_style = $listingpro_options['listing_style'];
	$header_fullwidth = $listingpro_options['header_fullwidth'];
	$headerSrch = $listingpro_options['search_switcher'];
	$topBannerView = lp_get_banner_style();

	$headerWidth = '';
	if($header_fullwidth == 1){
		$headerWidth = 'fullwidth-header';
	}else{
		$headerWidth = 'container';
	}

	$listing_style = '';
	$listing_styledata = '';
	$headerClass = 'header-normal';
	$listing_style = $listingpro_options['listing_style'];
	if(isset($_GET['list-style']) && !empty($_GET['list-style'])){
		$listing_style = esc_html($_GET['list-style']);
	}
	if(is_tax('location') || is_tax('listing-category') || is_tax('list-tags') || is_tax('features') || is_search()){
		if($listing_style == '2' || $listing_style == '3'  || $listing_style == '6'){
			$headerClass = 'header-fixed';
		}
	}
	$menuColor= '';
	if(!is_front_page() || is_home()){
		$menuColor =  ' lp-menu-bar-color';
	}elseif ( $topBannerView == 'map_view' && is_front_page() ) {
		$menuColor =  ' lp-menu-bar-color';
	}
	?>
<!--================================full width with blck icon background====================================-->

	<header class="header-menu-dropdown <?php echo esc_attr($headerClass); ?> lp-header-full-width lp-header-bg-black">
		<?php if(is_front_page()){ ?> <div class="lp-header-overlay"></div> <?php } ?>	
			<div id="menu" class="menu-height-0 small-screen">
				<?php
					$listing_access_only_users = $listingpro_options['lp_allow_vistor_submit'];
					$showAddListing = true;
					if( isset($listing_access_only_users)&& $listing_access_only_users==1 ){
						$showAddListing = false;
						if(is_user_logged_in()){
							$showAddListing = true;
						}
					}
					if($showAddListing==true) {
							$addURL = listingpro_url('add_listing_url_mode');
							if(!empty($addURL)) {
                                $lpl_add_listing    =   'lpl-add-listing-loggedout';
                                if(is_user_logged_in()) {
                                    $lpl_add_listing = 'lpl-add-listing-logedin';
                                }
							?>
							<a href="<?php echo listingpro_url('add_listing_url_mode'); ?>" class="lpl-button <?php echo esc_attr($lpl_add_listing); ?>"><?php echo esc_html__('Add Listing', 'listingpro'); ?></a>
							<?php 
						}
					}
				?>
				<?php
					if (!is_user_logged_in()) {
                        $login_popup_style  =   $listingpro_options['login_popup_style'];
                        if($login_popup_style == 'style2') {
                            ?>
                            <a class="lpl-button md-trigger app-view-popup-style" data-target="#app-view-login-popup"><?php esc_html_e('Sign In', 'listingpro');?></a>
                            <?php
                        } else {
                            ?>
                            <a class="lpl-button md-trigger" data-modal="modal-3"><?php esc_html_e('Sign In', 'listingpro');?></a>
                            <?php
                        }
						?>
					<?php } else { ?>					<a href="<?php echo wp_logout_url( esc_url(home_url('/')) ); ?>" class="lpl-button lpl-signout" style="right: 10px;"><?php esc_html_e('Sign out ','listingpro'); ?></a>					<?php } ?>
				<?php
					echo listingpro_mobile_menu();
				?>
			</div>
		<?php
			$menuColor= '';
			if(!is_front_page()){
				$menuColor =  ' lp-menu-bar-color';
			}
			if(is_home()){
				$menuColor =  ' lp-menu-bar-color';
			}
		?>
		<div class="lp-menu-bar <?php echo esc_attr($menuColor);  ?>">
			<div class="<?php echo esc_attr($headerWidth); ?>">
				<div class="row">
					<div class="col-md-2 col-xs-6 lp-logo-container">
						<div class="lp-logo">
							<a href="<?php echo esc_url(home_url('/')); ?>">
								<?php
								if(is_front_page()){
								    listingpro_primary_logo(); 
								}
								else{
									listingpro_secondary_logo(); 
								}
								?>
							</a>
						</div>
					</div>
					<div class="header-right-panel clearfix col-md-10 col-sm-10 col-xs-12">
						
						<!--<div class="navbar navbar-inverse navbar-fixed-top" role="navigation" id="slide-nav">
							<div class="container">
								<div class="navbar-header">
									<a class="navbar-toggle"> 
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
									</a>
								</div>
								<div id="slidemenu">   
									<?php echo listingpro_primary_logo(); ?> 
									<div class="lp-listing-adlisting">
										<a href="<?php echo listingpro_url('add_listing_url_mode'); ?>" class="lpl-button">
											<?php esc_html_e('Add Listing', 'listingpro'); ?>
										</a>
										<a href="#" class="lpl-button md-trigger" data-modal="modal-3">
											<?php esc_html_e('Sign In', 'listingpro'); ?>
										</a>
									</div>
									<?php echo listingpro_mobile_menu(); ?>    
								</div>
							</div>
						</div>-->
						
						<div class="col-xs-6 mobile-nav-icon">
							<a href="#menu" class="nav-icon">
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</a>
						</div>
						<?php 
							if($headerSrch == 1) {
								if(!is_front_page() && !is_page_template('template-dashboard.php')){
									get_template_part('templates/search/top_search');
								}
							}
						?>
						<div class="col-md-6 col-xs-12 lp-menu-container pull-right">
							<div class="lp-without-icon-bar-right">
								<div class="lp-joinus-icon">
									<?php get_template_part( 'templates/join-now'); ?>
								</div>
								<?php
									$listing_access_only_users = $listingpro_options['lp_allow_vistor_submit'];
									$showAddListing = true;
									if( isset($listing_access_only_users)&& $listing_access_only_users==1 ){
										$showAddListing = false;
										if(is_user_logged_in()){
											$showAddListing = true;
										}
									}
									
									if($showAddListing==true){
										$addURL = listingpro_url('add_listing_url_mode');
										if(!empty($addURL)){
											?>
											<div class="lp-add-listing-btn">
												<ul>
													<li>
														<a href="<?php echo listingpro_url('add_listing_url_mode'); ?>">
															<i class="fa fa-plus"></i>
															<?php esc_html_e('Add Listing', 'listingpro') ?>
														</a>
													</li>
												</ul>
											</div>
											<?php 
										}
									}
								?>
								<div class="lp-dropdown-menu dropdown">
									<button id="main-nav" data-toggle="dropdown" class="navbar-toggle dropdown-toggle" type="button">
										<i class="fa fa-bars"></i>
									</button>
									<div id="menu">
										<?php
											if(is_front_page()) {
												echo listingpro_primary_menu();
											}else {
												echo listingpro_inner_menu();
											}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- ../menu-bar -->
		<?php //get_template_part( 'templates/banner'); ?>
	</header>
	
	

	<!--==================================Header Close=================================-->