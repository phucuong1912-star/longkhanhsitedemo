<?php


global $listingpro_options;
$lp_top_bar = $listingpro_options['top_bar_enable'];
$headerBgcolor = $listingpro_options['app_view_header_bgcolor'];
$listing_style = $listingpro_options['listing_style'];
$header_fullwidth = $listingpro_options['header_fullwidth'];
$headerSrch = $listingpro_options['search_switcher'];
$topBannerView = lp_get_banner_style();
$headerBginercolor = $listingpro_options['header_bgcolor_inner_pages'];


$headerWidth = '';
if ($header_fullwidth == 1) {
	$headerWidth = 'fullwidth-header app-view-header-container';
} else {
	$headerWidth = 'container app-view-header-container';
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

<script>
	jQuery(document).ready(function(e) {
		jQuery('.lp-search-toggle > a').click(function(e) {
			e.preventDefault();

			if (jQuery(this).hasClass('open-filter') || jQuery(this).hasClass('close-filter')) {
				jQuery('.lp-search-toggle').toggleClass('app-view-filter-open');
				jQuery('.header-right-panel').slideToggle('slow');
			}

			if (jQuery(this).hasClass('home-filter-open') || jQuery(this).hasClass('home-filter-close')) {
				jQuery('.lp-search-toggle').toggleClass('app-view-filter-close');
				jQuery('.lp-home-banner-contianer').slideToggle('slow');
			}

		})



	})
</script>
<!--================================full width with blue background====================================-->
<div class="modal fade" id="app-view-login-popup" role="dialog">
	<?php
	get_template_part('mobile/templates/login-reg-popup');
	?>
</div>

<header class="header-without-topbar <?php echo esc_attr($headerClass); ?> pos-relative lp-header-full-width app-view-header">
<?php if (is_front_page() && $topBannerView != 'map_view') { ?> <div class="lp-header-overlay"></div> <?php } ?>

	<div id="menu" class="menu-height-0 small-scrren small-scrren-app-view">
		<div class="mobile-menu-inner">
			<?php
			if (is_user_logged_in()) :
				$current_user = wp_get_current_user();
				$user_id = $current_user->ID;

				$user_address = get_user_meta($user_id, 'address', true);;


				$u_display_name = $current_user->display_name;
				if (empty($u_display_name)) {
					$u_display_name = $current_user->nickname;
				}
			?>
				<div class="user-detail-wrap">
					<div class="user-thumb">
						<a href="<?php echo listingpro_url('listing-author'); ?>"><img class="avatar-circle" alt="image" src="<?php echo listingpro_author_image(); ?>" /></a>
					</div>
					<div class="user-text">
						<h5 class="user-name margin-top-0"><?php echo esc_attr($u_display_name); ?></h5>
						<?php if (!empty($user_address)) :  ?><p><?php echo esc_html($user_address); ?></p><?php endif; ?>

					</div>
					<div class="clearfix"></div>
					<div class="sign-login-wrap">
						<a href="<?php echo wp_logout_url(esc_url(home_url('/'))); ?>"><?php esc_html_e('Sign out ', 'listingpro'); ?></a>
					</div>
				</div>


			<?php else : ?>
				<div class="user-detail-wrap">
					<div class="user-thumb enpty-thumb">
						<img class="avatar-circle" alt="image" src="<?php echo get_template_directory_uri(); ?>/assets/images/admin/avtar.jpg" />
					</div>
					<div class="user-text">
						<h5 class="user-name margin-top-0 empty-name"></h5>
						<p class="empty-addr"></p>
						<p class="empty-phone"></p>
					</div>
					<div class="clearfix"></div>
					<div class="sign-login-wrap">
						<a class="md-trigger" data-toggle="modal" data-target="#app-view-login-popup"><?php esc_html_e('Sign In', 'listingpro'); ?></a>
					</div>
				</div>

				<?php
			endif;
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
					<div class="text-left"><a href="<?php echo listingpro_url('add_listing_url_mode'); ?>" class="add-listing-btn"><i class="fa fa-plus-square-o" aria-hidden="true"></i> <?php esc_html_e('Add Listing', 'listingpro'); ?></a></div>
			<?php
				}
			}
			?>
			<?php echo listingpro_mobile_menu(); ?>
		</div>

	</div>
	<?php
	$menuColor = '';
	if (!is_front_page()) {
		$menuColor =  ' lp-menu-bar-color';
	}

	if (is_home()) {
		$menuColor =  ' lp-menu-bar-color';
	}

	//new code 2.6.15
	$style  = '';
	$style = 'style="background:' . $headerBgcolor . '"';

	//end new code 2.6.15
	?>
	<div <?php echo $style; ?> class="lp-menu-bar <?php echo esc_attr($menuColor);  ?>">
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
							$lp_logo = $listingpro_options['app_view_logo']['url'];
							if (!empty($lp_logo)) {
								echo '<img src="' . $lp_logo . '" alt="image" />';
							} else {
								echo get_option('blogname');
							}
							?>
						</a>
					</div>
				</div>
				<div class="lp-search-toggle col-md-2 col-xs-3 pull-right text-right padding-right-0">
					<?php

					if (is_user_logged_in()) :

						$dashURL = listingpro_url('listing-author');
						if (!empty($dashURL)) {
							$currentURL = $dashURL;
							$perma = '';
							$dashQuery = 'dashboard=';
							global $wp_rewrite;
							if ($wp_rewrite->permalink_structure == '') {
								$perma = "&";
							} else {
								$perma = "?";
							}
						}
					?>
						<a href="#" class="user-menu"><i class="fa fa-user-circle-o" aria-hidden="true"></i> <span><i class="fa fa-angle-down" aria-hidden="true"></i></span></a>

						<ul class="lp-user-menu list-style-none">
							<li><a href="<?php echo listingpro_url('listing-author'); ?>"><?php esc_html_e('Dashboard', 'listingpro'); ?> </a></li>
							<li><a href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'update-profile'); ?>"><?php esc_html_e('Update Profile', 'listingpro'); ?></a></li>
							<li><a href="<?php echo wp_logout_url(esc_url(home_url('/'))); ?>"><?php esc_html_e('Sign out ', 'listingpro'); ?></a></li>
						</ul>

					<?php else : ?>
						<a href="#" class="user-menu" data-toggle="modal" data-target="#app-view-login-popup"><i class="fa fa-user-circle-o" aria-hidden="true"></i></a>
					<?php endif; ?>
					<?php
					$app_view_home  =   $listingpro_options['app_view_home'];
					if (is_front_page() || (is_page($app_view_home) && !empty($app_view_home))) : ?>
						<a href="#" class="home-filter-close"><i class="fa fa-close" aria-hidden="true"></i></a>
						<a href="#" class="home-filter-open"><i class="fa fa-search" aria-hidden="true"></i></a>

						<?php else :
						$dashboard_url  =   listingpro_url('listing-author');
						$dashboardID    =   url_to_postid($dashboard_url);
						if (is_page($dashboardID)  && is_user_logged_in()) :
						?>
				</div>
				<div class="lp-onlylogut-appview col-md-2 col-xs-3 pull-right text-right padding-right-0">
					<a href="<?php echo wp_logout_url(esc_url(home_url('/'))); ?>" class="user-menu dashboard-redirect"><i class="fa fa-user-circle-o" aria-hidden="true"></i> <span class="dashboard-app-view-signout"> <?php esc_html_e('Signout', 'listingpro'); ?></span></a>
				<?php else : ?>

					<a href="#" class="open-filter"><i class="fa fa-search" aria-hidden="true"></i></a>
					<a href="#" class="close-filter"><i class="fa fa-times" aria-hidden="true"></i></a>
				<?php endif; ?>
			<?php endif; ?>
				</div>
				<div class="header-right-panel clearfix col-md-10 col-sm-10 col-xs-12" style="display: none;">
					<?php

					$app_view_home  =   $listingpro_options['app_view_home'];

					if ((!is_page($app_view_home) || empty($app_view_home)) && !is_front_page() && !is_page_template('template-dashboard.php')) {
						get_template_part('templates/search/top_search');
					}

					?>

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
	<?php

	$app_view_home  =   $listingpro_options['app_view_home'];
	if (is_home() || is_front_page() || is_page() || is_search('post') || is_archive('post') || is_author() || is_category() || is_tag() || (!empty($app_view_home) && is_page($app_view_home))) {
		get_template_part('mobile/templates/headers/banner-app-view');
	}
	?>
</header>
<!--==================================Header Close=================================-->