<?php


global $listingpro_options;
$lp_top_bar = $listingpro_options['top_bar_enable'];
$headerBgcolor = $listingpro_options['header_bgcolor'];
$listing_style = $listingpro_options['listing_style'];
$header_fullwidth = $listingpro_options['header_fullwidth'];
$headerSrch = $listingpro_options['search_switcher'];
$topBannerView = lp_get_banner_style();


$headerWidth = '';
if ($header_fullwidth == 1) {
	$headerWidth = 'fullwidth-header';
} else {
	$headerWidth = 'container';
}


$listing_styledata = '';
$headerClass = 'header-normal';
$listing_style = '';
$listing_style = $listingpro_options['listing_style'];
if (isset($_GET['list-style']) && !empty($_GET['list-style'])) {
	$listing_style = esc_html($_GET['list-style']);
}
if (is_tax('location') || is_tax('listing-category')  || is_tax('list-tags') || is_tax('features') || is_search()) {
	if ($listing_style == '2' || $listing_style == '3' || $listing_style == '6') {
		$headerClass = 'header-fixed';
	}
}
$menuColor = '';
if (!is_front_page() || is_home()) {
	$menuColor =  ' lp-menu-bar-color';
} elseif ($topBannerView == 'map_view' && is_front_page()) {
	$menuColor =  ' lp-menu-bar-color';
}

$menuClass = '';
if (!is_front_page() && $headerSrch == 1) {
	$menuClass = 'col-md-6';
} elseif (!is_front_page() && $headerSrch != 1) {
	$menuClass = 'col-md-9';
} else {
	$menuClass = 'col-md-9';
}
?>
<style>
	.lp-logo-container {
		float: left;
	}

	.lp-menu-bar .mobile-nav-icon {
		position: static;
		margin-top: 7px;
	}

	.lp-logo {
		float: left;
		text-align: left;
		margin-left: 10px;
	}

	.lp-logo img {
		max-width: 175px;
	}

	.open-filter,
	.close-filter {
		color: #fff;
		font-size: 20px;
		margin: 10px;
		display: inline-block;
	}

	.lp-search-toggle .close-filter {
		display: none;
	}

	.lp-search-toggle.app-view-filter-open .close-filter {
		display: inline-block;
	}

	.lp-search-toggle.app-view-filter-open .open-filter {
		display: none;
	}

	.listing-app-view .col-xs-12,
	.listing-app-view .col-sm-12 {
		padding: 0;
	}

	.lp-search-toggle {

		<?php if (wp_is_mobile()) { ?>width: 30%;
		<?php } ?>
	}
</style>
<script>
	jQuery(document).ready(function(e) {
		jQuery('.lp-search-toggle a').click(function(e) {
			e.preventDefault();

			jQuery('.lp-search-toggle').toggleClass('app-view-filter-open');
			jQuery('.header-right-panel').slideToggle('slow', function() {

			})
		})
	})
</script>
<!--================================full width with blue background====================================-->

<header class="header-without-topbar <?php echo esc_attr($headerClass); ?> pos-relative lp-header-full-width app-view-header">
	<?php if (is_front_page() && $topBannerView != 'map_view') { ?> <div class="lp-header-overlay"></div> <?php } ?>

	<div id="menu" class="menu-height-0  small-screen">
		<?php
		$listing_access_only_users = $listingpro_options['lp_allow_vistor_submit'];
		$showAddListing = true;
		if (isset($listing_access_only_users) && $listing_access_only_users == 1) {
			$showAddListing = false;
			if (is_user_logged_in()) {
				$showAddListing = true;
			}
		}
		if ($showAddListing == true) {
			$addURL = listingpro_url('add_listing_url_mode');
			if (!empty($addURL)) {

				$lpl_add_listing    =   'lpl-add-listing-loggedout';
				if (is_user_logged_in()) {
					$lpl_add_listing = 'lpl-add-listing-logedin';
				}

		?>
				<a href="<?php echo listingpro_url('add_listing_url_mode'); ?>" class="lpl-button <?php echo esc_attr($lpl_add_listing); ?>"><?php esc_html_e('Add Listing', 'listingpro'); ?></a>
		<?php
			}
		}
		?>
		<?php
		if (!is_user_logged_in()) {
		?>
			<a class="lpl-button md-trigger" data-modal="modal-3"><?php esc_html_e('Sign In', 'listingpro'); ?></a>
		<?php } else { ?> <a href="<?php echo wp_logout_url(esc_url(home_url('/'))); ?>" class="lpl-button" style="right: 10px;"><?php esc_html_e('Sign out ', 'listingpro'); ?></a> <?php } ?>
		<?php
		echo listingpro_mobile_menu();
		?>
	</div>
	<?php
	$menuColor = '';
	if (!is_front_page()) {
		$menuColor =  ' lp-menu-bar-color';
	}
	if (is_home()) {
		$menuColor =  ' lp-menu-bar-color';
	}
	?>
	<div class="lp-menu-bar <?php echo esc_attr($menuColor);  ?>">
		<div class="<?php echo esc_attr($headerWidth); ?>">
			<div class="row">
				<div class="col-md-2 col-xs-9 lp-logo-container">
					<div class="mobile-nav-icon">
						<a href="#menu" class="nav-icon">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</a>
					</div>
					<div class="lp-logo">
						<a href="<?php echo esc_url(home_url('/')); ?>">
							<?php
							if (is_front_page()) {
								listingpro_primary_logo();
							} else {
								listingpro_secondary_logo();
							}
							?>
						</a>
					</div>
				</div>
				<div class="lp-search-toggle col-md-2 col-xs-3 pull-right text-right">
					<a href="#" class="open-filter"><i class="fa fa-search" aria-hidden="true"></i></a>
					<a href="#" class="close-filter"><i class="fa fa-times" aria-hidden="true"></i></a>
				</div>
				<div class="header-right-panel clearfix col-md-10 col-sm-10 col-xs-12" style="display: none;">
					<?php

					if (!is_front_page() && !is_page_template('template-dashboard.php')) {
						get_template_part('templates/search/top_search');
					}

					?>
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
					<!--						<div class="col-xs-6 mobile-nav-icon">-->
					<!---->
					<!---->
					<!--							--><?php ////echo listingpro_mobile_menu(); 
														?>
					<!--							<a href="#menu" class="nav-icon">-->
					<!--								<span class="icon-bar"></span>-->
					<!--								<span class="icon-bar"></span>-->
					<!--								<span class="icon-bar"></span>-->
					<!--							</a>-->
					<!--						</div>-->
					<div class="<?php echo esc_attr($menuClass); ?> col-xs-12 lp-menu-container clearfix pull-right">
						<div class="pull-right">
							<div class="lp-joinus-icon">
								<?php get_template_part('templates/join-now'); ?>
							</div>
							<?php
							$listing_access_only_users = $listingpro_options['lp_allow_vistor_submit'];
							$showAddListing = true;
							if (isset($listing_access_only_users) && $listing_access_only_users == 1) {
								$showAddListing = false;
								if (is_user_logged_in()) {
									$showAddListing = true;
								}
							}

							if ($showAddListing == true) {
								$addURL = listingpro_url('add_listing_url_mode');
								if (!empty($addURL)) {
							?>
									<div class="pull-right lp-add-listing-btn">
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
						</div>
						<div class="pull-right padding-right-10">
							<div class="lp-menu menu">
								<?php
								if (is_front_page()) {
									echo listingpro_primary_menu();
								} else {
									echo listingpro_inner_menu();
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- ../menu-bar -->
	<?php //get_template_part( 'templates/banner'); 
	?>
</header>
<!--==================================Header Close=================================-->