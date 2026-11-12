<!DOCTYPE html>
<!--[if IE 7 ]>
<html class="ie7"> <![endif]-->
<!--[if IE 8 ]>
<html class="ie8"> <![endif]-->
<?php

$mobile_view = lp_theme_option('single_listing_mobile_view');
if (wp_is_mobile() && ($mobile_view == 'app_view' || $mobile_view == 'app_view2') && ! is_page_template('template-dashboard.php')) {
	get_template_part('templates/headers/header-app-view');
} else {

?>
	<html <?php language_attributes(); ?>>

	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<!-- Mobile Meta -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<meta HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE" />
		<?php
		listingpro_favicon();
		$listing_detail_slider_style = lp_theme_option('lp_detail_slider_styles');
		?>
		<?php wp_head(); ?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('select.form-control').removeClass('form-control').addClass('custom-form-control');
			})
		</script>
		<style type="text/css">
			.custom-form-control {
				width: 100%;
				padding: 10px;
				line-height: 24px;
				-webkit-appearance: textfield;
			}
		</style>
	</head>

	<body <?php body_class();
			lp_header_data_atts('body'); ?>>
		<?php wp_body_open(); ?>
		<?php wp_nonce_field('lp_ajax_nonce', 'lpNonce'); ?>
		<input type="hidden" id="start_of_weekk" value="<?php echo get_option('start_of_week'); ?>">
		<?php

		$listing_styledata     = '';
		$topBannerView         = lp_get_banner_style();
		$lp_detail_page_styles = lp_theme_option('lp_detail_page_styles');
		$search_alignment      = lp_theme_option('search_alignment');

		// New
		$addclass_banner_upper_cat = '';
		$differentcats_views       = lp_get_cat_views();
		$bannercat_category_inside = lp_theme_option('categories_different_styles_category_inside');

		$alignClass = '';
		switch ($topBannerView) {
			case 'map_view':
				switch ($search_alignment) {
					case 'align_top':
						$alignClass = 'lp-align-top';
						break;
					case 'align_middle':
						$alignClass = 'lp-align-underBanner';
						break;
					case 'align_bottom':
						$alignClass = 'lp-align-bottom';
						break;
				}
				break;
		}

		//-------------Start New view style banner add class------------------//
		$class_cat_banner_view = '';
		if ($topBannerView == "banner_view_search_bottom" && $differentcats_views == 'category_view2') {
			$class_cat_banner_view = 'banner-category-mix-view2';
		}

		if ($topBannerView == 'banner_view_category_upper') {
			// banner_view_category_upper
			$addclass_banner_upper_cat = '';
			if ($topBannerView == 'banner_view_category_upper' && $bannercat_category_inside == 'category_view') {
				$addclass_banner_upper_cat = 'banner-view-cat-tranparent-category';
			} else if ($topBannerView == 'banner_view_category_upper' && $bannercat_category_inside == 'category_view1') {
				$addclass_banner_upper_cat = 'banner-view-cat-tranparent-category';
			} else if ($topBannerView == 'banner_view_category_upper' && $bannercat_category_inside == 'category_view2') {
				$addclass_banner_upper_cat = 'banner-view-cat-tranparent-category';
			} else if ($topBannerView == 'banner_view_category_upper' && $bannercat_category_inside == 'category_view3') {
				$addclass_banner_upper_cat = 'banner-view-cat-tranparent-category';
			}
		}

		$newbannerstyleClass = '';
		if ($topBannerView == 'banner_view_search_bottom') {
			$newbannerstyleClass = 'new-banner-view-category';
		} elseif ($topBannerView == 'banner_view_search_inside') {
			$newbannerstyleClass = 'new-banner-view-category';
		} elseif ($topBannerView == 'banner_view_search_classic') {
			$newbannerstyleClass = 'banner-view-classic';
		}

		$maintxtstyleClass = '';
		if (empty($main_txt)) {
			$maintxtstyleClass = 'new-banner-view-category-st';
		}
		//-------------End New view style banner add class------------------//

		$lp_theme_loader_switch     = lp_theme_option('lp_theme_loader_switch');
		$lp_theme_loader      		= lp_theme_option('lp_theme_loader');

		if ($lp_theme_loader_switch && !empty($lp_theme_loader)) {
		?>
			<div class="lp-theme-loader"><img src="<?php echo esc_url($lp_theme_loader['url']); ?>" alt="loaderimg">
			</div>
		<?php
		}
		?>

		<div id="page" <?php lp_header_data_atts('page'); ?> class="clearfix <?php echo esc_attr($lp_detail_page_styles); ?>">

			<!--===========================header-views========================-->
			<?php get_template_part('templates/headers/header-views'); ?>
			<?php
			if (is_front_page() && $topBannerView != 'map_view' && $topBannerView != 'banner_view2' && $topBannerView != 'banner_view3' && $topBannerView != 'wpro_bannerStyle_1' && $topBannerView != 'bpro_bannerStyle_1' && $topBannerView != 'ppro_bannerStyle_1' && $topBannerView != 'lpro_bannerStyle_1' && $topBannerView != 'cbdpro_bannerStyle_1') { ?>
				<div class="home-categories-area <?php echo esc_attr($alignClass); ?>
						<?php echo esc_attr($newbannerstyleClass); ?> 
						<?php echo esc_attr($maintxtstyleClass); ?>
						<?php echo esc_attr($class_cat_banner_view); ?> 
						<?php echo esc_attr($addclass_banner_upper_cat); ?>">
					<?php echo listingpro_banner_categories(); ?>
				</div>
			<?php
			}
			?>
		<?php
	}
		?>