<?php
global $listingpro_options;
$banner_height      = isset($listingpro_options['banner_height']) ? $listingpro_options['banner_height'] : '';
$page_style_css = '';


if (isset($listingpro_options['ep_home_banner_switch'])) {
	$ep_home_banner_switch = $listingpro_options['ep_home_banner_switch'];
}
$curr_page_id = get_the_ID();

if (isset($listingpro_options['ep_page_select'])) {
	$ep_page_select = $listingpro_options['ep_page_select'];
}
if (isset($listingpro_options['ep_banner_style'])) {
	$ep_banner_style = $listingpro_options['ep_banner_style'];
}
if (empty($banner_height)) {
	$banner_height  =   'height:610px;';
} else {
	$banner_height  =   'height:' . $banner_height . 'px;';
}
if (is_front_page() && is_home()) {


?>
	<div class="page-heading listing-page">
		<div class="page-heading-inner-container text-center">
			<h1><?php _e('Home', 'listingpro') ?></h1>
		</div>
		<div class="page-header-overlay"></div>
	</div>
<?php
} elseif (is_home()) {
	$page_id = get_queried_object_id();

	$page_meta  =   get_post_meta($page_id, 'lp_listingpro_options', true);
	$page_banner = '';

	if (!empty($page_meta)) {

		$page_banner    =   $page_meta['lp_page_banner'];
		$page_banner_css    =   '';
	} else {
		$page_banner = $listingpro_options['page_header']['url'];
	}
	if (!empty($page_banner)) {
		$page_banner_css    =   "background-image: url($page_banner);";
		$page_style_css = 'background-position: center center;background-size: cover;';
	}


?>
	<div class="page-heading listing-page" style="<?php echo esc_attr($page_banner_css); ?> <?php echo esc_attr($page_style_css); ?>">
		<div class="page-heading-inner-container text-center">
			<h1>
				<?php
				$queried_object = get_queried_object();
				echo single_post_title('', FALSE);
				?>
			</h1>
			<ul class="breadcrumbs">
				<li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'listingpro') ?></a></li>
				<li><span><?php _e('Blog', 'listingpro') ?></span></li>
			</ul>
		</div>
		<div class="page-header-overlay"></div>
	</div>
	<?php
} elseif (is_archive()) {
	if (is_tax('location') || is_tax('listing-category') || is_tax('list-tags') || is_tax('features')) {
		$listing_style = $listingpro_options['listing_style'];
		if (isset($_GET['list-style']) && !empty($_GET['list-style'])) {
			$listing_style = esc_html($_GET['list-style']);
		}


		if ($listing_style == '1') {
	?>
			<?php if (is_tax('listing-category') || is_tax('list-tags')) { ?>
				<?php
				$queried_object = get_queried_object();
				$term_id = $queried_object->term_id;
				$category_banner = listing_get_tax_meta($term_id, 'category', 'banner');
				if ($category_banner) {
				?>
					<div class="page-heading listing-page" style="background:url(<?php echo esc_attr($category_banner); ?>); background-size: cover; background-position: center;">
					<?php
				} else {
					?>
						<div class="page-heading listing-page">
						<?php
					}
						?>

					<?php  } else {

					$queried_object = get_queried_object();
					$term_id = $queried_object->term_id;

					$banner_taxonomy = ' ';

					$meteKey_cat = get_term_meta($term_id, 'lp_category_banner', true);

					$meteKey_loc = get_term_meta($term_id, 'lp_location_image', true);

					if (isset($meteKey_cat) && !empty($meteKey_cat)) {
						$banner_taxonomy = $meteKey_cat;
					} elseif (isset($meteKey_loc) && !empty($meteKey_loc)) {
						$banner_taxonomy =  $meteKey_loc;
					} else {
						$banner_taxonomy = $listingpro_options['page_header']['url'];
					}

					?>
						<div class="page-heading listing-page " style="background:url(<?php echo esc_attr($banner_taxonomy); ?>);background-size: cover;background-position: center; ">
						<?php } ?>
						<div class="page-heading-inner-container cat-area">
							<div class="container">
								<div class="row">
									<div class="col-md-6 col-sm-6">
										<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
									</div>
									<div class="col-md-6 col-sm-6 text-right">
										<p class="view-on-map">
											<!-- Marker icon by Icons8 -->
											<?php echo listingpro_icons('whiteMapMarkerFill'); ?>
											<a class="md-trigger mobilelink all-list-map" data-modal="modal-listing"><?php echo esc_html_e('View on map', 'listingpro'); ?></a>
										</p>
										<?php
										$grid_list_switch   =   $listingpro_options['hide_grid_switcher'];
										$listing_view = $listingpro_options['listing_views'];
										if ($grid_list_switch == 'no') {
											if ($listing_view != 'lp-list-view-compact'  && $listing_view != 'grid_view_v3') {
										?>
												<div class="listing-view-layout">
													<ul>
														<li><a class="grid" href="#"><i class="fa fa-th"></i></a></li>
														<li><a class="list" href="#"><i class="fa fa-list-ul"></i></a></li>
													</ul>
												</div>
										<?php
											}
										}
										?>
									</div>
								</div>
							</div>
						</div>
						<div class="page-header-overlay"></div>
						</div>
					<?php
				}
			} elseif (is_author(get_queried_object_id())) {
					?>
					<div class="page-heading listing-page">
						<div class="page-heading-inner-container text-center">
							<h1><?php echo get_the_archive_title(get_queried_object_id()); ?></h1>
							<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
						</div>
						<div class="page-header-overlay"></div>
					</div>

					<?php
				} else {
					$showPageTitle = $listingpro_options['lp_showhide_pagetitle'];
					if ($showPageTitle == "1") {
					?>
						<div class="page-heading listing-page">
							<div class="page-heading-inner-container text-center">
								<h1><?php echo the_archive_title(); ?></h1>
								<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
							</div>
							<div class="page-header-overlay"></div>
						</div>
					<?php
					}
				}
			} elseif (is_front_page()) {

				$topBannerView = lp_get_banner_style();
				if ($topBannerView == 'banner_view2') {
					get_template_part('templates/headers/header-home');
				} elseif ($topBannerView == 'banner_view3') {
					get_template_part('templates/headers/banner-sidesearch');
				} elseif ($topBannerView == 'mp_banner_side_search_view1') {
					if (class_exists('MedicalPro')) {
						mp_get_template_part('templates/headers/banner-sidesearch-1');
					} else {
						get_template_part('templates/headers/banner-sidesearch');
					}
				}
				// WeddingPro
				elseif ($topBannerView == 'wpro_bannerStyle_1') {
					if (class_exists('WeddingPro')) {
						wpro_get_template_part('templates/banners/wpro_bannerStyle_1');
					} else {
						get_template_part('templates/headers/banner-sidesearch');
					}
				}
				// BlackPro
				elseif ($topBannerView == 'bpro_bannerStyle_1') {
					if (class_exists('BlackPro')) {
						bpro_get_template_part('templates/banners/bpro_bannerStyle_1');
					} else {
						get_template_part('templates/headers/banner-sidesearch');
					}
				}
				// PetPro
				elseif ($topBannerView == 'ppro_bannerStyle_1') {
					if (class_exists('PetFinder')) {
						ppro_get_template_part('templates/banners/ppro_bannerStyle_1');
					} else {
						get_template_part('templates/headers/banner-sidesearch');
					}
				}
				// LawyerPro
				elseif ($topBannerView == 'lpro_bannerStyle_1') {
					if (class_exists('LawyerPro')) {
						lpro_get_template_part('templates/banners/lpro_bannerStyle_1');
					} else {
						get_template_part('templates/headers/banner-sidesearch');
					}
				} elseif ($topBannerView == 'banner_view' ||  $topBannerView == 'banner_view_search_bottom' || $topBannerView == 'banner_view_search_classic' ||  $topBannerView == 'banner_view_category_upper' ||  $topBannerView == 'banner_view_search_inside' ||  $topBannerView == 'map_view' ||  $topBannerView == 'banner_view_search_classic') { //classic new style
					// search style
					$searchstyle = lp_get_search_views();
					$top_title = $listingpro_options['top_title'];
					$top_main_title = $listingpro_options['top_main_title'];
					$main_text = $listingpro_options['main_text'];
					$map_height = $listingpro_options['map_height'];
					$arrow_image = $listingpro_options['arrow_image'];
					$locationType = 'withip';

					$courtesySwitch = $listingpro_options['courtesy_switcher'];
					if ($courtesySwitch == 1) {
						$courtesyListing = $listingpro_options['courtesy_listing'];
					}
					$height = '';
					if (!empty($map_height)) {
						$height = ' style="height:' . $map_height . 'px;"';
					} else {
						$height = ' style="height:500px;"';
					}
					if ($topBannerView == 'map_view') {
					?>
						<div class="lp_home" id="homeMap" <?php echo wp_kses_post($height); ?>></div>
					<?php } elseif ($topBannerView == 'banner_view' || $topBannerView == 'banner_view_category_upper' || $topBannerView == 'banner_view_search_classic') { ?>
						<?php
						$videosearchlayout = $listingpro_options['video_search_layout'];
						$videoBanner = $listingpro_options['lp_video_banner_on'];
						$video_banner_img = $listingpro_options['video_banner_img']['url'];
						if ($videoBanner == "video_banner") {
							$video_src  =   $listingpro_options['vedio_type'];
							$vedio_url = $listingpro_options['vedio_url'];
							$vedio_url_yt = $listingpro_options['vedio_url_yt'];
							if (!empty($vedio_url) || !empty($vedio_url_yt)) {
								$outputEmbed =  preg_replace("/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "$2", $vedio_url_yt);
						?>

								<div class="video-lp" data-videoid="<?php echo wp_kses_post($outputEmbed); ?>">
									<?php
									if ($video_src == 'video_mp4'):
									?>
										<video id="lp_vedio" muted autoplay="autoplay" loop="loop" width="0" height="0" poster="<?php echo esc_url($video_banner_img); ?>">
											<source src="<?php echo esc_url($vedio_url); ?>" type="video/webm" />
											<source src="<?php echo esc_url($vedio_url); ?>" type="video/mp4" />
											<source src="<?php echo esc_url($vedio_url); ?>" type="video/ogg" />
										</video>
									<?php
									else:

										echo '<div id="player" style="width: 100%; height: 100%;"></div>';

									endif;
									?>
								</div>
						<?php
							}
						}


						?>

						<div class="lp-home-banner-contianer lp-home-banner-with-loc" style="<?php echo wp_kses_post($banner_height); ?>">

							<div class="page-header-overlay"></div>
							<?php if ($courtesySwitch == 1) {
								if ($topBannerView != 'banner_view_search_classic') {
							?>
									<div class="img-curtasy">
										<p><?php esc_html_e('Image courtesy of', 'listingpro'); ?> <span><a href="<?php echo get_the_permalink($courtesyListing); ?>"><?php echo get_the_title($courtesyListing); ?> <i class="fa fa-angle-right"></i></a></span></p>
									</div>
								<?php
								} else {
								?>
									<div class="img-curtasy-classic">
										<p><?php esc_html_e('Image courtesy of', 'listingpro'); ?> <span><a href="<?php echo get_the_permalink($courtesyListing); ?>"><?php echo get_the_title($courtesyListing); ?><i class="fa-solid fa-arrow-right"></i></a></span></p>
									</div>
							<?php
								}
							} ?>

							<?php if ($videosearchlayout == 'align_center') { ?>

								<?php

								//------------------ Banner view---------------------------//

								$top_banner_styles = lp_get_banner_style();
								$differentcats_views = lp_get_cat_views();

								$searchstyle_cat_inside = isset($listingpro_options['search_different_styles_banner_category_inside']) ? $listingpro_options['search_different_styles_banner_category_inside'] : '';
								$searchstyle_cat_inside_classic = isset($listingpro_options['search_different_styles_banner_classic']) ? $listingpro_options['search_different_styles_banner_classic'] : '';

								// Banner view category inside 

								$addclass_for_tranparent_cat_view = '';
								$addclass_for_tranparent_cat_view_classic = '';
								if ($top_banner_styles == 'banner_view_category_upper' && $differentcats_views == 'category_view' || $top_banner_styles == 'banner_view_category_upper' && $differentcats_views == 'category_view1' || $top_banner_styles == 'banner_view_category_upper' && $differentcats_views == 'category_view2' || $top_banner_styles == 'banner_view_category_upper' && $differentcats_views == 'category_view3' || $top_banner_styles == 'banner_view_search_classic' && $differentcats_views == 'category_view4') {
									$addclass_for_tranparent_cat_view = 'banner-view-cat-tranparent';
								}
								if ($top_banner_styles == 'banner_view_search_classic') {
									wp_enqueue_style('banner-classic', THEME_DIR . '/assets/css/classic/classic-banner-search-cats.css');
									$addclass_for_tranparent_cat_view_classic = 'banner-view-top-classic';
								}
								//classic new style
								?>

								<div class="lp-home-banner-contianer-inner <?php echo esc_attr($addclass_for_tranparent_cat_view . ' ' . $addclass_for_tranparent_cat_view_classic); ?>">
									<div class="container">
										<div class="row">
											<div class="col-md-12 col-sm-12 text-center lp_auto_loc_container">
												<?php if (!empty($top_main_title)) { ?>
													<h1 data-locnmethod="<?php echo wp_kses_post($locationType); ?>"><?php echo wp_kses_post($top_main_title); ?></h1>
												<?php } ?>
												<?php if (!empty($top_title)) { ?>
													<p class="lp-banner-browse-txt"><?php echo wp_kses_post($top_title); ?></p>
												<?php } ?>
											</div>
											<div class="col-md-8 col-xs-12 col-md-offset-2 col-sm-offset-0">
												<?php

												if ($top_banner_styles == 'banner_view') {

													if ($searchstyle == '' || $searchstyle == 'search_view') {

														get_template_part('templates/search/home-search');
													} elseif ($searchstyle == 'search_view1') {

														get_template_part('templates/search/home-search-view1');
													} elseif ($searchstyle == 'search_view2') {

														get_template_part('templates/search/home-search-view2');
													} elseif ($searchstyle == 'search_view3') {

														get_template_part('templates/search/home-search-view3');
													} elseif ($searchstyle == 'search_view4') {

														get_template_part('templates/search/home-search-view4');
													}
												}
												if ($top_banner_styles == 'banner_view_category_upper') {

													if ($searchstyle_cat_inside == '' || $searchstyle_cat_inside == 'search_view') {

														get_template_part('templates/search/home-search');
													} elseif ($searchstyle_cat_inside == 'search_view1') {

														get_template_part('templates/search/home-search-view1');
													} elseif ($searchstyle_cat_inside == 'search_view2') {

														get_template_part('templates/search/home-search-view2');
													} elseif ($searchstyle_cat_inside == 'search_view3') {

														get_template_part('templates/search/home-search-view3');
													} elseif ($searchstyle_cat_inside == 'search_view4') {

														get_template_part('templates/search/home-search-view4');
													}
												}

												if ($top_banner_styles == 'banner_view_search_classic') { //classic new style

													if ($searchstyle_cat_inside_classic == '' || $searchstyle_cat_inside_classic == 'search_view') {

														get_template_part('templates/search/home-search');
													} elseif ($searchstyle_cat_inside_classic == 'search_view1') {

														get_template_part('templates/search/home-search-view1');
													} elseif ($searchstyle_cat_inside_classic == 'search_view2') {

														get_template_part('templates/search/home-search-view2');
													} elseif ($searchstyle_cat_inside_classic == 'search_view3') {

														get_template_part('templates/search/home-search-view3');
													} elseif ($searchstyle_cat_inside_classic == 'search_view4') {

														get_template_part('templates/search/home-search-view4');
													}
												}
												?>

												<?php //get_template_part( 'templates/search/home-search'); 
												if ($top_banner_styles == 'banner_view_category_upper' || $top_banner_styles == 'banner_view') { //classic new style
												?>
													<div class="text-center lp-search-description">
														<?php if (!empty($main_text)) { ?>
															<p><?php echo wp_kses_post($main_text); ?></p>
														<?php } ?>
														<?php if ($arrow_image == 1) { ?>
															<img src="<?php echo get_template_directory_uri(); ?>/assets/images/banner-arrow.png" alt="banner-arrow" class="banner-arrow" />

														<?php } ?>
													</div>
												<?php } ?>
											</div>
										</div>
									</div>

								<?php } ?>

								</div><!-- ../Home Search Container -->
								<?php if ($videosearchlayout == 'align_bottom_video') { ?>
									<div class="lp-home-banner-contianer lp-home-banner-contianer-inner-video-outer" style="<?php echo wp_kses_post($banner_height); ?>">
										<div class="lp-home-banner-contianer-inner-video">
											<div class="clearfix">
												<div class="col-md-3 col-xs-12 padding-0">
													<div class="video-bottom-search-content">
														<?php if (!empty($top_main_title)) { ?>
															<h3 data-locnmethod="<?php echo wp_kses_post($locationType); ?>"><?php echo wp_kses_post($top_main_title); ?></h3>
														<?php } ?>

													</div>
												</div>
												<div class="col-md-9 col-xs-12 padding-0">
													<div class="video-bottom-search-container clearfix">
														<?php if (!empty($top_title)) { ?>
															<p class="lp-banner-browse-txt"><?php echo wp_kses_post($top_title); ?></p>
														<?php } ?>
														<?php get_template_part('templates/search/home-search'); ?>
													</div>
												</div>
											</div>
										</div>
									</div>

								<?php } ?>
							<?php } ?>

						<?php } else {
						do_action('listingpro_addon_banner');
					} ?>

						<?php
					} elseif (is_page()) {
						$showPageTitle = $listingpro_options['lp_showhide_pagetitle'];
						if (have_posts()) : while (have_posts()) : the_post();
								if (has_shortcode(get_the_content(), 'vc_row') && has_shortcode(get_the_content(), 'listingpro_promotional')) {
								} else {
									if ($showPageTitle == "1") {
										$page_style_css = '';
										$page_banner = '';
										$page_meta  =   get_post_meta(get_the_ID(), 'lp_listingpro_options', true);
										//print_r($page_meta);

										if (!empty($page_meta)) {


											$page_banner    =   $page_meta['lp_page_banner'];
											$page_banner_css    =   '';
										} else {
											$page_banner = $listingpro_options['page_header']['url'];
										}
										if (!empty($page_banner)) {
											$page_banner_css    =   "background-image: url($page_banner);";
											$page_style_css = 'background-position: center center;background-size: cover;';
										}
						?>
										<div class="page-heading listing-page" style="<?php echo esc_attr($page_banner_css); ?> <?php echo esc_attr($page_style_css); ?>">
											<div class="page-heading-inner-container text-center">
												<h1><?php echo get_the_title(); ?></h1>
												<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
											</div>
											<div class="page-header-overlay"></div>
										</div>
							<?php
									}
								}
							endwhile;
						endif;
					} elseif (is_search()) {
						$listing_style = $listingpro_options['listing_style'];
						if (isset($_GET['list-style']) && !empty($_GET['list-style'])) {
							$listing_style = esc_html($_GET['list-style']);
						}

						if ($listing_style == '1' && isset($_GET['post_type']) && $_GET['post_type'] == 'listing') {
							$sterm = '';
							$sloc = '';
							$termName = '';
							$locName = '';
							if (isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat'])) {
								$sterm = esc_html($_GET['lp_s_cat']);
								$termo = get_term_by('id', $sterm, 'listing-category');
								$termName = 'Best ' . $termo->name;
							}
							if (isset($_GET['lp_s_cat']) && empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])) {
								$sterm = esc_html($_GET['lp_s_tag']);
								$termo = get_term_by('id', $sterm, 'list-tags');
								$termName = 'Results For ' . $termo->name;
							}
							if (isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])) {
								$sloc = esc_html($_GET['lp_s_loc']);

								$checkTerm = listingpro_term_exist($sloc, 'location');
								if ($checkTerm == true) {
									$locTerm = get_term_by('name', $sloc, 'location');
									$locName = 'In ' . $locTerm->name;
								} else {
									$locName = 'In ' . $sloc;
								}
							}

							$page_meta  =   get_post_meta($page_id, 'lp_listingpro_options', true);
							$page_banner = '';

							if (!empty($page_meta)) {

								$page_banner    =   $page_meta['lp_page_banner'];
								$page_banner_css    =   '';
							} else {
								$page_banner = $listingpro_options['page_header']['url'];
							}
							if (!empty($page_banner)) {
								$page_banner_css    =   "background-image: url($page_banner);";
								$page_style_css = 'background-position: center center;background-size: cover;';
							}

							?>
							<div class="page-heading listing-page app_search_header" style="<?php echo esc_attr($page_banner_css); ?> <?php echo esc_attr($page_style_css); ?>">
								<div class="page-heading-inner-container search-page-header">
									<div class="container">
										<div class="row">
											<div class="col-md-6 col-sm-6">
												<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
											</div>
											<div class="col-md-6 col-sm-6 text-right">
												<p class="view-on-map">
													<!-- Marker icon by Icons8 -->
													<?php echo listingpro_icons('whiteMapMarkerFill'); ?>
													<a class="md-trigger mobilelink all-list-map" data-modal="modal-listing"><?php echo esc_html_e('View on map', 'listingpro'); ?></a>
												</p>
												<?php
												$grid_list_switch   =   $listingpro_options['hide_grid_switcher'];
												$listing_view = $listingpro_options['listing_views'];
												if ($grid_list_switch == 'no') {
													if ($listing_view != 'lp-list-view-compact'  && $listing_view != 'grid_view_v3') {
												?>
														<div class="listing-view-layout">
															<ul>
																<li><a class="grid" href="#"><i class="fa fa-th"></i></a></li>
																<li><a class="list" href="#"><i class="fa fa-list-ul"></i></a></li>
															</ul>
														</div>
												<?php
													}
												}
												?>
											</div>
										</div>
									</div>
								</div>
								<div class="page-header-overlay"></div>
							</div>
						<?php
						}
					} elseif (is_404()) {
						?>
						<div class="page-heading listing-page">
							<div class="page-heading-inner-container text-center">
								<h1>404</h1>
								<?php if (function_exists('listingpro_breadcrumbs')) listingpro_breadcrumbs(); ?>
							</div>
							<div class="page-header-overlay"></div>
						</div>
					<?php
					} else {
					?>

					<?php
					}
					?>