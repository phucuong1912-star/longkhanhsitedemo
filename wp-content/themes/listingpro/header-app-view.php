<!DOCTYPE html>
<!--[if IE 7 ]>    <html class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8"> <![endif]-->
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<!-- Mobile Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8"> <!-- Favicon -->
	<?php listingpro_favicon();
	global $listingpro_options;
	$listing_detail_slider_style = $listingpro_options['lp_detail_slider_styles'];
	$lp_default_map_location_lat = 0;
	$lp_default_map_location_long = -0;
	if ((isset($listingpro_options['lp_default_map_location_lat'])) && (isset($listingpro_options['lp_default_map_location_long']))) {
		if ((!empty($listingpro_options['lp_default_map_location_lat'])) && (!empty($listingpro_options['lp_default_map_location_long']))) {
			$lp_default_map_location_lat   =  $listingpro_options['lp_default_map_location_lat'];
			$lp_default_map_location_long   =  $listingpro_options['lp_default_map_location_long'];
		}
	}

	$maplistingby = '';
	if(isset($listingpro_options['map_listing_by'])){
		$maplistingby = $listingpro_options['map_listing_by'];
	}

	$lpsearchMode = "titlematch";
	if (isset($listingpro_options['lp_what_field_algo'])) {
		if (!empty($listingpro_options['lp_what_field_algo']) && $listingpro_options['lp_what_field_algo'] == "keyword") {
			$lpsearchMode = "keyword";
		}
	}
	?>
	<?php wp_head(); ?>
</head>

<?php
$lpAtts = '';
if (is_search() || is_tax()) {
	if (lp_theme_option('enable_search_filter') == 1) {
		if (lp_theme_option('enable_nearme_search_filter') == 1) {
			if (lp_theme_option('disable_location_in_nearme_search_filter') == 1) {
				$lpAtts = "data-locdisablefilter='yes'" . ' ';
			}
		}
	}
}

if (is_single()) {
	$lp_map_zoom = $listingpro_options['map_zoom']; //new code 2.6.15
}
?>

<body <?php body_class() ?> data-submitlink="<?php echo listingpro_url('submit-listing'); ?>" data-maplistingby="<?php echo $maplistingby; ?>" data-sliderstyle="<?php echo esc_attr($listing_detail_slider_style); ?>" data-defaultmaplat="<?php echo esc_attr($lp_default_map_location_lat); ?>" data-defaultmaplot="<?php echo esc_attr($lp_default_map_location_long); ?>" data-lpsearchmode="<?php echo esc_attr($lpsearchMode); ?>" data-mapzoom="<?php echo $lp_map_zoom; ?>" <?php echo wp_kses_post($lpAtts); ?>>
	<?php wp_nonce_field('lp_ajax_nonce', 'lpNonce'); ?>
	<?php
	$mapbox_token = '';
	$map_style = '';

	$mapOption = $listingpro_options['map_option'];

	$search_view = $listingpro_options['search_views'];
	$search_layout = $listingpro_options['search_layout'];
	$alignment = $listingpro_options['search_alignment'];
	$top_banner_styles = lp_get_banner_style();

	$alignClass = '';
	if ($top_banner_styles == 'map_view') {
		if ($alignment == 'align_top') {
			$alignClass = 'lp-align-top';
		} elseif ($alignment == 'align_middle') {
			$alignClass = 'lp-align-underBanner';
		} elseif ($alignment == 'align_bottom') {
			$alignClass = 'lp-align-bottom';
		}
	}


	if ($mapOption == 'mapbox') {
		$mapbox_token = $listingpro_options['mapbox_token'];
		$map_style = $listingpro_options['map_style'];
	}


	$primary_logo = $listingpro_options['primary_logo']['url'];
	$listing_style = '';
	$listing_styledata = '';
	$listing_style = $listingpro_options['listing_style'];
	if (isset($_GET['list-style']) && !empty($_GET['list-style'])) {
		$listing_styledata = 'data-list-style="' . esc_attr($_GET['list-style']) . '"';
		$listing_style = esc_html($_GET['list-style']);
	}

	$lpCurrLocOnHome = '';
	if (isset($listingpro_options['lp_auto_current_locations_switch'])) {
		$lpCurrLocOnHome  = $listingpro_options['lp_auto_current_locations_switch'];
	}


	$header_views =     lp_get_header_views();
	$topBannerView = lp_get_banner_style();
	$ipAPI = $listingpro_options['lp_current_ip_type'];
	$videoBanner = '';
	$imgClass = '';
	if ($topBannerView == 'map_view') {
		$imgClass = '';
	} else {

		$videoBanner = $listingpro_options['lp_video_banner_on'];
		if ($videoBanner == "video_banner") {
			$imgClass = 'lp-vedio-bg';
		} else {
			$imgClass = 'lp-header-bg';
		}
	}

	$app_view_home  =   $listingpro_options['app_view_home'];
	$lpAtts = "data-lpattern=" . lp_theme_option("lp_listing_locations_field_options") . " ";
	?>

	<div id="page" class="clearfix" <?php echo esc_attr($listing_styledata); ?> data-mtoken="<?php echo esc_attr($mapbox_token); ?>" data-mtype="<?php echo esc_attr($mapOption); ?>" data-mstyle="<?php echo esc_attr($map_style); ?>" data-sitelogo="<?php echo esc_attr($primary_logo); ?>" data-site-url="<?php echo esc_url(home_url('/')); ?>" data-ipapi="<?php echo esc_attr($ipAPI); ?>" data-lpcurrentloconhome="<?php echo esc_attr($lpCurrLocOnHome) ?>" <?php echo wp_kses_post($lpAtts); ?>>

		<!--==================================Header Open=================================-->
		<div class="pos-relative">
			<?php
			get_template_part('templates/popups');
			?>
			<div class="header-container <?php if (is_front_page() || (!empty($app_view_home) && is_page($app_view_home))) {
												echo esc_attr($imgClass);
											} ?> ">
				<?php
				get_template_part('mobile/templates/headers/header-app-view-template');
				get_template_part('mobile/templates/headers/banner/banner-app-view');
				?>
			</div>
			<?php
			$app_view_option    =   $listingpro_options['single_listing_mobile_view'];

			if (is_front_page() || (!empty($app_view_home) && is_page($app_view_home))) {
				if ($app_view_option == 'app_view2') {
					$home_banner_cats = '';
					if (isset($listingpro_options['home_banner_cats'])) {
						$home_banner_cats = $listingpro_options['home_banner_cats'];
					}

					if (!empty($home_banner_cats)) {
			?>
						<div class="app-view2-banner-cat-slider">
							<?php
							foreach ($home_banner_cats as $banner_cat) {
								$cat_item = get_term_by('id', $banner_cat, 'listing-category');
								$term_link  =   get_term_link($cat_item);
								$category_image = listing_get_tax_meta($cat_item->term_id, 'category', 'image2');
								if (empty($category_image)) {
									$category_image = listing_get_tax_meta($cat_item->term_id, 'category', 'image');
								}
							?>
								<div class="app-view2-banner-cat">
									<?php
									if (!empty($category_image)) {
									?>
										<span class="app-view2-banner-cat-img">
											<a href="<?php echo ($term_link); ?>"> <?php

																					if (!empty($category_image)) {
																						if (hasFontAwesomeIconClass($category_image)) {
																							echo '<i class="icon icons-search-cat ' . $category_image . '"></i>';
																						} else {
																							echo '<img alt="image" src="' . $category_image . '" />';
																						}
																					}
																					?>
											</a>
										</span>
									<?php
									} else {
									?>
										<a href="<?php echo ($term_link); ?>">
											<span class="app-view2-banner-cat-img"><img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAIDAAACAwBDO6GDAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAb9SURBVHic7Z1LqJVVGIbfpallWZpmpmlJNQgRQygiU0siuhFBt0EQhEGjQBpEt2kNjKBZdiMHkk3CaCJlhRWBWmFWmljqqUjJS3mpzOvTYB/leDpn77X+71v/Onu7n9GB/a/v3f96z7fWf1nr21JNAOOBW4EngDcG+Pw0Bvh8MfAwMBsYYfgedwE/AVuBZ4Dzq8ZqO4CxwJPAWuB4iw5vZUhfDgDvAQ8BIxO/0/Z+sfYCzwKjLOc65AEWAvv6d7STIX3ZCTya8L0GYzMwz3reQxbgYJOT9zQE4O+E79WME8BLwFnW8x9SAONanLi3IQAXRH63GFYDF3v0RSrDMsW9NFPcZlziGGu+pHXAlY4xo8hlyJRMcZvh/R89TdIqYLpz3KbkMmRqprjN8MyQk1wu6WNgYobYA5LLkMmZ4jYj15g/XdLyuib67hwSxwJJz2WMf4ruHBLP08CszBrdOSSBkZKWACGnSCdlyKQaNK6XdG9OAXe3gXMk/dNSOITTtPvfDLb6fAB+DyG0NCUiTis2SpoZQrDGGZAcGXJRhpgxTACG16AzQ9LduYJ3kiHDJY2vSevxXIFzGDIhQ8yhpn0TkOXSvpMypE7t4ZIezBG40zKkriFLku7MEbSbIdW5sfeK0pVOy5A6tUdIusY7aKcZUueQJUnXegfMYciYDDFjqfuf4TrvgDkMcR9XE7iwZr0rvAMWMwSY0ufv/z2MbPX5IJwbIx0ZK4ZpjrEklc2QV4HJvR2/pMLnA3FexDEnImPFMInEdWGtyPFwcbsarz5LsDmEcHWzA4DDajxK92JiCGG3V7AcGTI6Q8xYYoYszwyRnM83hyFnZ4gZS4whx501XUeZbobY2ecZLIchxzLEjCWmsz0z5LikA47xshiyN0PMWPZEHOOZIT+GEFwzLochMZ2Si7oN2eQYS1IeQ77JEDOWDRHHeBqy1jGWpDyGfJ4hpqe2pyEfOMaSlMeQlZKOZojbimOSVkUc52XIDknfOcU6hbshIYQdkt71jhvBihDCrxHHeV1lve09oUv5Fsotlv8NWDNO9GrGHuvBO05xTiOLISGE9ZJeyRF7EF4PIXwVeayHIZ+FEL52iFMfwChgTeQWMgvrUt5t09gSbeW2XP2Wa8hSCOGwpPslbc2lIekXSQ+EEA4ltLFmyEZluLo6STZDJKl3kp2nPPcmP0iaF0LoSWxnNeTFXOt6a4PG8PWaw1BxkreASk+VgU0G3e+pZ/1wPQCLgKOGDjkGPIVhj0Zvp1Yl61aEIgA3A7srdMYuYIGD/oaKZqwh82adYgBzK3TIDU7a6ysaMt9Df0hCY05JxeU9ONUy5H0P7RiyXmU1ocpiOq8FeDErU/pyRNIiJ+2WdA1pzdIQwjYn7ZaUMqRK0TCvQmMpxh6V9IKTbhSlDKmy5NO8TJRGNYaUpa5LQwg/W3VTKGVIlUXRHns/Uoerlx00kyhlSJViLh6GjEs4dk0Iwf2deSvaKUM8thqkGLLcQS+ZUoZU+W/3yJCUeegjB71k2ilD6h6ytjvoJdNOhngMWSkZUuS51ZlmSEqGXOWgl0w7GeKxoTMlQ+Y46CVzpk3qKRlyj4NeMrUbAoxRtR1Mo4DUG7v+pBgynwJ14UtkiGUusA5bKUPWSEl3GPWSaTdDrMNW6vOw+4x6ybSbIdYrrZQhS5JuB2J2ZblRwhDLsGN94js28fjRkuYaNZMoYYhl2KnclsaPwFSZpG+pqlmFdssQS9vU7DiJeaVLCiUMSR3HS7edReRPYXhQwhDLyVnaVp1/hkmaadBNFqsby82Wpa0lu2YY2ibRbhnSNSQD7ZghtT357RoSR22F0UoYYilOY6lWZzGko6+yLGt0LW0tKx+r3sMkcyYZYnkm1c2QDG0tZaNq2zXVboZYfq/WkiEpm0pNlHqFWwJLhrT8gRovShhiKXBmaWt5/dvRGWLpVEtRm26GDIKlUy1tu3PIIFgy5IihbTdDBsHSqaUy5DdD2yRKGGKp4hn9Q/YDYLmX6DG0TaKEIX8a2loqnh42tO3oTZ+lDLEMlT2GtkmUMMRSCfoPQ9uuIYNgqetbwpAD6nBDLAXNYopcDkZVQ74IIdRWPr2EIVsMbTca2lad1D8xaCbTbhmy2dD2YMV2tRaGLmHINlW7F/lLtiFrR4U2uyTVWn20dkN6x+PVFZp+aqx1WOVue1md84dU7n3IhxXarDBqxhTq78sRFSitUQRgCvBvQgGxY8Ako+bMxKJlb3qdb1sALEnonGUOesOALZF6O4GSPyFbP8BUYE9E5+wDXH7AEXgsQu8gUGRLdHGAOcD+Jp2zH4dKpH30ArCsiV4PMNtLry2hMZ8sBr4EDgCHgG+B54HLMugNAxYCq2gUVd4ErAQeoWJxZk/+AywT275CyP49AAAAAElFTkSuQmCC"></span>
										</a>
									<?php
									}
									?>
									<?php echo esc_attr($cat_item->name); ?>
								</div>
							<?php
							}
							?>
						</div>
			<?php
					}
				}
			}
			?>
			<!--==================================Header Close=================================-->

			<!--================================== Search Close =================================-->
			<?php
			if (is_front_page() && !is_home() || (!empty($app_view_home) && is_page($app_view_home))) {
				$topBannerView = lp_get_banner_style();
				if ($topBannerView == 'map_view') {
					echo '<div class="listing-app-view">';
					echo '<div class="pos-relative ">';
					echo '<div class="lp-home-banner-contianer map-banner-search-container" style="height: 245px !important;">';
					get_template_part('templates/search/template_search1');
					echo '</div>';
					echo '</div>';
					echo '</div>';
				}
			}
			?>
			<div class="lp-top-notification-bar"></div>
			<!--================================== Search Close =================================-->
		</div>

		<?php
		if (is_front_page() || (!empty($app_view_home) && is_page($app_view_home))) {
			if ($topBannerView != 'map_view') { ?>
				<div class="home-categories-area <?php echo esc_attr($alignClass); ?>">
					<?php echo listingpro_banner_categories(); ?>
				</div>
		<?php
			}
		}
		?>