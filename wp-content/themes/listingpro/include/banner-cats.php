<?php
/**
 * Banner Categories.
 *
 */

/* ============== ListingPro Banner Categories ============ */

if (!function_exists('listingpro_banner_categories')) {
    
    function listingpro_banner_categories()
    {
        global $listingpro_options;
        $home_banner_taxonomy = '';
        if (isset($listingpro_options['home_banner_taxonomy'])) {
            $home_banner_taxonomy = $listingpro_options['home_banner_taxonomy'];
        }
        $banner_tax      = 'home_banner_cats';
        $banner_tax_slug = 'listing-category';
        if ($home_banner_taxonomy == 'tax_locs') {
            $banner_tax      = 'home_banner_locs';
            $banner_tax_slug = 'location';
        }
        if ($home_banner_taxonomy == 'tax_feats') {
            $banner_tax      = 'home_banner_feats';
            $banner_tax_slug = 'features';
        }
        
        if (isset($listingpro_options[$banner_tax])) {
            $homeBannerCategory = $listingpro_options[$banner_tax];
        } else {
            $homeBannerCategory = '';
        }
        
        
        $search_view                      = isset($listingpro_options['search_views']) ? $listingpro_options['search_views'] : '';
        $appview                          = isset($listingpro_options['single_listing_mobile_view']) ? $listingpro_options['single_listing_mobile_view'] : '';
        $search_layout                    = isset($listingpro_options['search_layout']) ? $listingpro_options['search_layout'] : '';
        $alignment                        = isset($listingpro_options['search_alignment']) ? $listingpro_options['search_alignment'] : '';
        $cat_txt                          = isset($listingpro_options['cat_txt']) ? $listingpro_options['cat_txt'] : '';
        $top_banner_styles                = lp_get_banner_style();
        $differentcats_views              = lp_get_cat_views();
        $bannercats_search_overlap        = isset($listingpro_options['categories_different_styles_search_overlap']) ? $listingpro_options['categories_different_styles_search_overlap'] : '';
        $bannercat_category_inside        = isset($listingpro_options['categories_different_styles_category_inside']) ? $listingpro_options['categories_different_styles_category_inside'] : '';
        $bannercat_search_inside          = isset($listingpro_options['categories_different_styles_search_inside']) ? $listingpro_options['categories_different_styles_search_inside'] : '';
		$bannercat_search_inside_classic  = isset($listingpro_options['categories_different_styles_search_inside_classic']) ? $listingpro_options['categories_different_styles_search_inside_classic'] : '';//classic new style
          $addclass_for_tranparent_cat_view = '';
        $newbannercatClass                = '';
        $class_for_cat_uper               = '';
        $class_for_search_inside          = '';
        if ($top_banner_styles == 'banner_view') {
            if ($differentcats_views == 'category_view1') {
                $addclass_for_tranparent_cat_view = 'banner-default-view-category1';
            } elseif ($differentcats_views == 'category_view2') {
                $addclass_for_tranparent_cat_view = 'banner-default-view-category2';
            } elseif ($differentcats_views == 'category_view3') {
                $addclass_for_tranparent_cat_view = 'banner-default-view-category3';
            }elseif ($bannercats_search_overlap == 'category_view') {
                $newbannercatClass = 'new-banner-category-view4';
            }
        } elseif ($top_banner_styles == 'banner_view_search_bottom') {
            if ($bannercats_search_overlap == 'category_view') {
                $newbannercatClass = 'new-banner-category-view';
            } elseif ($bannercats_search_overlap == 'category_view1') {
                $newbannercatClass = 'new-banner-category-view1';
            } elseif ($bannercats_search_overlap == 'category_view2') {
                $newbannercatClass = 'new-banner-category-view2';
            } elseif ($bannercats_search_overlap == 'category_view3') {
                $newbannercatClass = 'new-banner-category-view3';
            }
        } elseif ($top_banner_styles == 'banner_view_category_upper') {
            if ($bannercat_category_inside == 'category_view') {
                $class_for_cat_uper = 'lp-upper-cat-view1';
            } elseif ($bannercat_category_inside == 'category_view1') {
                $class_for_cat_uper = 'lp-upper-cat-view4';
            } elseif ($bannercat_category_inside == 'category_view2') {
                $class_for_cat_uper = 'lp-upper-cat-view3';
            } elseif ($bannercat_category_inside == 'category_view3') {
                $class_for_cat_uper = 'lp-upper-cat-view2';
                $addclass_for_tranparent_cat_view   =   'category-view-bg-transparent';
            }
        } elseif ($top_banner_styles == 'banner_view_search_inside') {
            if ($bannercat_search_inside == 'category_view') {
                $class_for_search_inside = 'lp-inside-search-view';
            } elseif ($bannercat_search_inside == 'category_view1') {
                $class_for_search_inside = 'lp-inside-search-view1';
            } elseif ($bannercat_search_inside == 'category_view2') {
                $class_for_search_inside = 'lp-inside-search-view2';
            } elseif ($bannercat_search_inside == 'category_view3') {
                $class_for_search_inside = 'lp-inside-search-view3';
            }
        }elseif ($top_banner_styles == 'banner_view_search_classic') { //classic new style
            if ($bannercat_search_inside_classic == 'category_view') {
                $class_for_search_inside = 'lp-inside-search-view';
            } elseif ($bannercat_search_inside_classic == 'category_view1') {
                $class_for_search_inside = 'lp-inside-search-view1';
            } elseif ($bannercat_search_inside_classic == 'category_view2') {
                $class_for_search_inside = 'lp-inside-search-view2';
            } elseif ($bannercat_search_inside_classic == 'category_view3') {
                $class_for_search_inside = 'lp-inside-search-view3';
            }elseif ($bannercat_search_inside_classic == 'category_view4') {
                $class_for_search_inside = 'lp-inside-search-view4';
            }
        }
		
        if (wp_is_mobile() && $appview == 'app_view') {
            $newbannercatClass = 'new-banner-category-view';
            $class_for_cat_uper = 'lp-upper-cat-view1';
            $class_for_search_inside = 'lp-inside-search-view';
            $bannercats_search_overlap = 'category_view1';
            $bannercat_search_inside = 'category_view';
            $bannercat_category_inside = 'category_view';
        }

        $alignClass = '';
        if ($alignment == 'align_top') {
            $alignClass = 'lp-align-top';
        } elseif ($alignment == 'align_middle') {
            $alignClass = 'lp-align-underBanner';
        } elseif ($alignment == 'align_bottom') {
            $alignClass = 'lp-align-bottom';
        }
        
        
        $output = '';
        if (isset($homeBannerCategory) && !empty($homeBannerCategory)) {
            $output .= '<div class="lp-section-row margin-bottom-60">';
            $output .= '<div class="container">';
            $output .= '<div class="row">';
            $output .= '<div class="col-md-12">';
            if ($top_banner_styles == 'map_view') {
                if ($alignment == 'align_middle' || $alignment == 'align_bottom') {
                    $output .= '<div class="col-md-8 col-xs-12 col-md-offset-2 col-sm-offset-0"><div class="text-center lp-search-description"><p>' . $cat_txt . '</p>';
                    $arrow_image = $listingpro_options['arrow_image'];
                    if($arrow_image == 1) {
                        $output .=    '<img src="' . get_template_directory_uri() . '/assets/images/banner-arrow-dark.png" alt="banner-arrow" class="banner-arrow">';
                    }
                    $output .=      '</div></div>';
                }
            }
            $output .= '<ul class="lp-home-categoires padding-left-0 ' . $newbannercatClass . ' ' . $class_for_cat_uper . ' ' . $class_for_search_inside . ' ' . $addclass_for_tranparent_cat_view . '">';
            $ucat       = array(
                'post_type' => 'listing',
                'hide_empty' => false,
                'include' => $homeBannerCategory
            );
            $categories = get_terms($banner_tax_slug, $ucat);
			if (!is_wp_error($categories)) {
				foreach ($categories as $category) {
					$category_image = listing_get_tax_meta($category->term_id, 'category', 'image2');
					if (empty($category_image)) {
						$category_image = listing_get_tax_meta($category->term_id, 'category', 'image');
					}
					$output .= '<li>';
					$output .= '<a href="' . get_term_link($category, $banner_tax_slug) . '" class="lp-border-radius-5">';
					$output .= '<span>';
					if (!empty($category_image) && $banner_tax_slug == 'listing-category') {
						if ($top_banner_styles == 'banner_view_search_bottom' && $bannercats_search_overlap == 'category_view2') {
							if( hasFontAwesomeIconClass($category_image) ){
								$output .= '<p id="cat-img-bg"><i class="icon icons-banner-cat '.$category_image.'"></i></p><br>'; 
							}else{
								$output .= '<p id="cat-img-bg"><img class="icon icons-banner-cat" src="' . $category_image . '" alt="Food" /></p><br>';
							}
						} elseif ($top_banner_styles == 'banner_view_category_upper' && $bannercat_category_inside == 'category_view2') {
							if( hasFontAwesomeIconClass($category_image) ){
								$output .= '<p id="cat-img-bg"><i class="icon icons-banner-cat '.$category_image.'"></i></p><br>'; 
							}else{
								$output .= '<p id="cat-img-bg"><img class="icon icons-banner-cat" src="' . $category_image . '" alt="Food" /></p><br>';
							}
						} elseif ($top_banner_styles == 'banner_view_search_inside' && $bannercat_search_inside == 'category_view2') {
							if( hasFontAwesomeIconClass($category_image) ){
								$output .= '<p id="cat-img-bg"><i class="icon icons-banner-cat '.$category_image.'"></i></p><br>'; 
							}else{
								$output .= '<p id="cat-img-bg"><img class="icon icons-banner-cat" src="' . $category_image . '" alt="Food" /></p><br>';
							}
						} elseif ($top_banner_styles == 'banner_view' && $differentcats_views == 'category_view2') {
							if( hasFontAwesomeIconClass($category_image) ){
								$output .= '<p id="cat-img-bg"><i class="icon icons-banner-cat '.$category_image.'"></i></p><br>'; 
							}else{
								$output .= '<p id="cat-img-bg"><img class="icon icons-banner-cat" src="' . $category_image . '" alt="Food" /></p><br>';
							}
						} else {
							if( hasFontAwesomeIconClass($category_image) ){
								$output .= '<i class="icon icons-banner-cat '.$category_image.'"></i><br>'; 
							}else{
								$output .= '<img class="icon icons-banner-cat" src="' . $category_image . '" alt="Food" /><br>';
							}
						}
					} elseif ($banner_tax_slug == 'location') {
						$output .= '<p id="cat-img-bg"><img class="icon icons-banner-cat" alt="image" src="data:image/png;base64,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"></p><br>';
					} elseif ($banner_tax_slug == 'features') {
						$icon = listingpro_get_term_meta( $category->term_id ,'lp_features_icon');
						if(!empty($icon)) {
							$output .= '<p id="cat-img-bg"><i class="fa '.esc_attr($icon).'"></i></p>';
						}else {
							$output .= '<p id="cat-img-bg"><i class="fa fa-check"></i></p>';
						}
					}
					$output .= $category->name;
					$output .= '</span>';
					$output .= '</a>';
					$output .= '</li>';
				}
			}
            $output .= '</ul>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
        }
        return $output;
    }
    
}