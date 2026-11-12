<?php
/**
 * Search Filters.
 *
 */
/* ============== ListingPro get child term (tags) in search ============ */
?>
<?php
if (!function_exists('listingpro_search_term_method')) {
    function listingpro_search_term_method()
    {
        wp_register_script('search-ajax-script', get_template_directory_uri() . '/assets/js/search-ajax.js', array(
            'jquery'
        ));
        wp_enqueue_script('search-ajax-script');
        wp_localize_script('search-ajax-script', 'ajax_search_term_object', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'noresult' => esc_html__('Most recent ', 'listingpro'),
            'listing' => esc_html__( ' Listings', 'listingpro' ),
            'resultfor' => esc_html__( 'Results For', 'listingpro' )
        ));
    }

    if (!is_admin()) {
        add_action('init', 'listingpro_search_term_method');
    }
}
/* ============== ListingPro Search Term ============ */
add_action('wp_ajax_ajax_search_term', 'ajax_search_term');
add_action('wp_ajax_nopriv_ajax_search_term', 'ajax_search_term');
if (!function_exists('ajax_search_term')) {
    function ajax_search_term()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $term_id = sanitize_text_field($_POST['term_id']);
        $count = 1;
        $tagsHTML = '';
        $featureName = '';
        $termparent = '';
        $parent = '';
        $hasfeature = false;
        $showdivwrap = true;
        $sortedFeatuers = array();
        if (!empty($term_id)) {
            $termparent = get_term_by('id', $term_id, 'listing-category');
            $parent = $termparent->parent;
        }
        $features = listingpro_get_term_meta($term_id, 'lp_category_tags');
        if (empty($features)) {
            $features = listingpro_get_term_meta($parent, 'lp_category_tags');
        }
        global $listingpro_options;
        $listing_style = $listingpro_options['listing_style'];
        $ULClass = '';
        $FetTitle = '';


        /* for sorting  creating array*/
        if (!empty($features)) {
            foreach ($features as $feature) {
                $terms = get_term_by('id', $feature, 'features');
                if (!empty($terms)) {
                    $sortedFeatuers[$feature] = $terms->name;
                }

            }
        }

        if (!empty($sortedFeatuers)) {
            sort($sortedFeatuers);

            foreach ($sortedFeatuers as $featureName) {
                $terms = get_term_by('name', $featureName, 'features');
                if (!empty($terms)) {
                    $featurCount = lp_count_postcount_taxonomy_term_byID('listing', 'features', $terms->term_id);

                    if (!empty($featurCount)) {
                        $hasfeature = true;
                    }
                    if ($hasfeature == true && $showdivwrap == true) {
                        if ($listing_style == '4') {
                            $tagsHTML = '<div class="form-inline lp-features-filter tags-area add-border">
														<div class="form-group">
														<div class="input-group margin-right-0">
														<strong class="col-sm-2">' . esc_html__("Features", "listingpro") . '</strong>
														<div class="col-sm-10">
														<ul class="lp-features-inner-container">';
                        } else {
                            $tagsHTML = '<div class="form-inline lp-features-filter tags-area"><div class="form-group"><div class="input-group margin-right-0"><ul>';
                        }

                        $showdivwrap = false;
                    }

                    if (!empty($featurCount)) {
                        $tagsHTML .= '<li>';
                        $tagsHTML .= '<div class="pad-bottom-10 checkbox ">';
                        $tagsHTML .= '<input type="checkbox" name="searchtags[' . $count . ']" id="check_' . $count . '" class="searchtags" value="' . $terms->term_id . '">';
                        $tagsHTML .= '<label for="' . $terms->term_id . '">' . $terms->name . '</label>';
                        $tagsHTML .= '</div>';
                        $tagsHTML .= '</li>';
                    }

                    $count++;

                }
            }
            if ($hasfeature == true) {
                $tagsHTML .= '</ul>';
                $tagsHTML .= '</div>';
                $tagsHTML .= '</div>';
                $tagsHTML .= '</div>';
                if ($listing_style == '4') {
                    $tagsHTML .= '</div>';
                }
            }

        }
        if (lp_theme_option('listing_style') == "4" && !wp_is_mobile()) {
            /* style 4 */
            $htmlFilter = '<div class="lp-features-filter lp-head-withfilter4 row display-flex">';
            $htmlFilter .= lp_get_extrafield_in_filterAjax($term_id);
            $htmlFilter .= '</div>';

        } else {
            /* style 1 and 2 */
            $htmlFilter = lp_get_extrafield_in_filterAjax($term_id);
        }

        $term_group_result = json_encode(array(
            'html' => $tagsHTML,
            'htmlfilter' => $htmlFilter
        ));
        die($term_group_result);
    }
}
/* ============== ListingPro Search Filter========== */
add_action('wp_ajax_ajax_search_tags', 'ajax_search_tags');
add_action('wp_ajax_nopriv_ajax_search_tags', 'ajax_search_tags');
if (!function_exists('ajax_search_tags')) {
    function ajax_search_tags()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        global $listingpro_options;
        $info = array('');
        $metakeyOrderBy = 'date';
        $lporders = 'DESC';
        if (isset($listingpro_options['lp_archivepage_listingorder'])) {
            $lporders = $listingpro_options['lp_archivepage_listingorder'];
        }
        if (isset($listingpro_options['lp_archivepage_listingorderby'])) {
            $metakeyOrderBy = $listingpro_options['lp_archivepage_listingorderby'];
        }

        $includeChildren = true;
        if (lp_theme_option('lp_children_in_tax')) {
            if (lp_theme_option('lp_children_in_tax') == "no") {
                $includeChildren = false;
            }
        }


        $defSquery = '';
        $lpDefaultSearchBy = 'title';
        if (isset($listingpro_options['lp_default_search_by'])) {
            $lpDefaultSearchBy = $listingpro_options['lp_default_search_by'];
        }

        $pageno = '';
        if (isset($_POST['pageno'])) {
            $pageno = $_POST['pageno'];
        }
        /* for version 2.0 */
        $formFieldsMetaArray = array();
        $formFieldsMetaArray['relation'] = 'AND';
        $lp_formFIelds = array();
        if (isset($_POST['formfields'])) {
            $lp_formFIelds = $_POST['formfields'];
        }

        /* for radious filter */
        $sloc_address = (isset($_POST['sloc_address'])) ? $_POST['sloc_address'] : '';
        $my_bounds_ne_lat = (isset($_POST['my_bounds_ne_lat'])) ? $_POST['my_bounds_ne_lat'] : '';
        $my_bounds_ne_lng = (isset($_POST['my_bounds_ne_lng'])) ? $_POST['my_bounds_ne_lng'] : '';
        $my_bounds_sw_lat = (isset($_POST['my_bounds_sw_lat'])) ? $_POST['my_bounds_sw_lat'] : '';
        $my_bounds_sw_lng = (isset($_POST['my_bounds_sw_lng'])) ? $_POST['my_bounds_sw_lng'] : '';

        $units = $listingpro_options['lp_nearme_filter_param'];
        if (empty($units)) {
            $units = 'km';
        }


        $squery = '';

        $latlongfilter = false;
        $latlongArray = array();
        $openNowArray = array();
        $clat = '';
        $clong = '';
        if (isset($_POST['clat'])) {
            $clat = sanitize_text_field($_POST['clat']);
        }
        if (isset($_POST['clong'])) {
            $clong = sanitize_text_field($_POST['clong']);
        }

        if (isset($_POST['tag_name'])) {
            $info['tag_name'] = $_POST['tag_name'];
        }
        $info['cat_id'] = sanitize_text_field($_POST['cat_id']);

        /* for dynamic search result title */

        $searchtitles = array();
        if (isset($_POST['cat_id'])) {
            if (!empty($_POST['cat_id'])) {
                $categoryID = $_POST['cat_id'];
                if (is_numeric($categoryID)) {
                    $categoryTerm = get_term_by('id', $categoryID, 'listing-category');
                    $term_Name = $categoryTerm->name;
                    $searchtitles['category'] = $term_Name;
                } else {
                    $searchtitles['category'] = $categoryID;
                }
            }
        }

	    if (empty($searchtitles['category'])) {
		    $searchtitles['category'] = esc_html__('All Categories', 'listingpro');
	    }

        if (isset($_POST['loc_id'])) {
            if (!empty($_POST['loc_id'])) {
                $locationID = $_POST['loc_id'];
                if (is_numeric($locationID)) {
                    if ($listingpro_options['lp_listing_search_locations_type'] == 'auto_loc') {
                        $lp_file_content = 'file_';
                        $lp_file_content .= 'get_';
                        $lp_file_content .= 'contents';
                        $url = $lp_file_content('https://maps.googleapis.com/maps/api/geocode/json?address=' . $locationID . '&key=' . $listingpro_options['google_map_api']);
                        $array = json_decode($url, TRUE);
                        if (isset($array['results'][0]['address_components'][1]['long_name'])) {
                            if (!empty($array['results'][0]['address_components'][1]['long_name'])) {
                                $loc_name = $array['results'][0]['address_components'][1]['long_name'];
                                $term = listingpro_term_exist($loc_name, 'location');
                                if (!empty($term)) {
                                    $locationID = $term['term_id'];
                                    $locationTerm = get_term_by('id', $locationID, 'location');
                                    $term_Name = $locationTerm->name;
                                    $searchtitles['location'] = $term_Name;
                                } else {
                                    $locationTerm = get_term_by('id', $locationID, 'location');
                                    $term_Name = $locationTerm->name;
                                    $searchtitles['location'] = $term_Name;
                                }
                            } else {
                                $locationTerm = get_term_by('id', $locationID, 'location');
                                $term_Name = $locationTerm->name;
                                $searchtitles['location'] = $term_Name;
                            }
                        } else {
                            $locationTerm = get_term_by('id', $locationID, 'location');
                            $term_Name = $locationTerm->name;
                            $searchtitles['location'] = $term_Name;
                        }
                    } else {
                        $locationTerm = get_term_by('id', $locationID, 'location');
                        $term_Name = $locationTerm->name;
                        $searchtitles['location'] = $term_Name;
                    }
                } else {
                    $searchtitles['location'] = $locationID;
                }
            }
        }

        if (!empty($searchtitles)) {
            $searchtitles['website'] = get_option('blogname');
            if (count($searchtitles) > 2) {
                $searchtitles['in'] = esc_html__('in', 'listingpro');
            }
            $searchtitles['for'] = esc_html__('for', 'listingpro');
        }

        /* end for dynamic search result title */
        if (isset($_POST['loc_id'])) {
            if (is_numeric($_POST['loc_id'])) {
                if ($listingpro_options['lp_listing_search_locations_type'] == 'auto_loc') {
                    $lp_file_content = 'file_';
                    $lp_file_content .= 'get_';
                    $lp_file_content .= 'contents';
                    $url = $lp_file_content('https://maps.googleapis.com/maps/api/geocode/json?address=' . $_POST['loc_id'] . '&key=' . $listingpro_options['google_map_api']);
                    $array = json_decode($url, TRUE);
                    if (isset($array['results'][0]['address_components'][1]['long_name'])) {
                        if (!empty($array['results'][0]['address_components'][1]['long_name'])) {
                            $loc_name = $array['results'][0]['address_components'][1]['long_name'];
                            $term = listingpro_term_exist($loc_name, 'location');
                            if (!empty($term)) {
                                $locationID = $term['term_id'];
                                $info['loc_id'] = ($sloc_address != '' || $my_bounds_ne_lat != '') ? '' : sanitize_text_field($locationID);
                            } else {
                                $locationID = $_POST['loc_id'];
                                $info['loc_id'] = ($sloc_address != '' || $my_bounds_ne_lat != '') ? '' : sanitize_text_field($locationID);
                            }
                        } else {
                            $locationID = $_POST['loc_id'];
                            $info['loc_id'] = ($sloc_address != '' || $my_bounds_ne_lat != '') ? '' : sanitize_text_field($locationID);
                        }
                    } else {
                        $locationID = $_POST['loc_id'];
                        $info['loc_id'] = ($sloc_address != '' || $my_bounds_ne_lat != '') ? '' : sanitize_text_field($locationID);
                    }
                } else {
                    $locationID = $_POST['loc_id'];
                    $info['loc_id'] = ($sloc_address != '' || $my_bounds_ne_lat != '') ? '' : sanitize_text_field($locationID);
                }
            } else {
                $locTerm = get_term_by('name', $_POST['loc_id'], 'location');
                if (!empty($locTerm)) {
                    $loc_ID = $locTerm->term_id;
                    $info['loc_id'] = ($sloc_address != '' || $my_bounds_ne_lat != '') ? '' : $loc_ID;
                } elseif (!empty($_POST['loc_id'])) {
                    $info['loc_id'] = 1;
                }
            }
        }

        $info['listStyle'] = isset($_POST['list_style']) ? sanitize_text_field($_POST['list_style']) : null;
		$info['inexpensive'] = isset($_POST['inexpensive']) ? sanitize_text_field($_POST['inexpensive']) : null;
		$info['moderate'] = isset($_POST['moderate']) ? sanitize_text_field($_POST['moderate']) : null;
		$info['pricey'] = isset($_POST['pricey']) ? sanitize_text_field($_POST['pricey']) : null;
		$info['ultra'] = isset($_POST['ultra']) ? sanitize_text_field($_POST['ultra']) : null;
		$info['averageRate'] = isset($_POST['averageRate']) ? sanitize_text_field($_POST['averageRate']) : null;
		$info['mostRewvied'] = isset($_POST['mostRewvied']) ? sanitize_text_field($_POST['mostRewvied']) : null;
		$info['listing_openTime'] = isset($_POST['listing_openTime']) ? sanitize_text_field($_POST['listing_openTime']) : null;
		$info['mostviewed'] = isset($_POST['mostviewed']) ? sanitize_text_field($_POST['mostviewed']) : null;
		$info['lp_s_tag'] = isset($_POST['lpstag']) ? sanitize_text_field($_POST['lpstag']) : null;
        if (isset($_POST['coupons'])) {
            $info['coupons'] = sanitize_text_field($_POST['coupons']);
        }
        $tagQuery = '';
        $catQuery = '';
        $searchtagQuery = '';
        $listing_time = '';
        $opentimeswitch = false;
        $opentimefilter = false;
        $sFeatures = '';
        if (isset($_POST['tag_name'])) {
            $sFeatures = $_POST['tag_name'];
        }
        if (!empty($info['listing_openTime'])) {
            $listing_time = $info['listing_openTime'];
            $opentimeswitch = true;
        }
        global $paged;
        if (!empty($pageno)) {
            $paged = $pageno;
        }
        $priceQuery = array();
        $categoryName = '';
        $LocationName = '';
        $locQuery = '';
        $currentTax = '';
        if (!empty($info['tag_name'])) {
            $tagQuery = array(
                'taxonomy' => 'features',
                'field' => 'id',
                'terms' => $info['tag_name'],
                'operator' => 'IN',
            );
        }


        if (!empty($info['cat_id'])) {
            $categoryName = get_term_by('id', $info['cat_id'], 'listing-category');
            $categoryName = $categoryName->name;
            $catQuery = array(
                'taxonomy' => 'listing-category',
                'field' => 'id',
                'terms' => $info['cat_id'],
            );
            if ($includeChildren == false) {
                $catQuery['include_children'] = $includeChildren;
            }
        }
        if (!empty($info['loc_id'])) {
            $LocationName = get_term_by('id', $info['loc_id'], 'location');
            $LocationName = $LocationName->name;
            $locQuery = array(
                'taxonomy' => 'location',
                'field' => 'id',
                'terms' => $info['loc_id'],
            );
            if ($includeChildren == false) {
                $locQuery['include_children'] = $includeChildren;
            }
        }
        if (!empty($info['lp_s_tag']) && isset($info['lp_s_tag'])) {
            $lpsTag = $info['lp_s_tag'];
            $searchtagQuery = array(
                'taxonomy' => 'list-tags',
                'field' => 'id',
                'terms' => $lpsTag,
                'operator' => 'IN' //Or 'AND' or 'NOT IN'
            );
        }

        if (isset($_POST['skeyword'])) {
            if ((empty($info['lp_s_tag']) || !isset($info['lp_s_tag'])) && (empty($info['cat_id']) || !isset($info['cat_id']))) {
                $squery = esc_attr($_POST['skeyword']);
                if (!empty($squery)) {
                    $defSquery = $squery;
                    $termExist = term_exists($squery, 'list-tags');

                    if ($termExist !== 0 && $termExist !== null) {
                        $tagQuery = array(
                            'taxonomy' => 'list-tags',
                            'field' => 'name',
                            'terms' => $squery,
                            'operator' => 'IN' //Or 'AND' or 'NOT IN'
                        );
                        $squery = '';
                        $squeryp = esc_attr($_POST['skeyword']);
                        $defSquery = $squeryp;
                    }

                }
            }
        }

        $rateArray = array();
        $reviewedArray = array();
        $viewedArray = array();
        $orderBy = $metakeyOrderBy;
        $sortBy = '';
        if (!empty($info['averageRate'])) {
            $lporders = '';
            $sortBy = array(
                'listing_rate_clause' =>
                    array(
                        'key' => 'listing_rate',
                        'type' => 'numeric',
                    ),
            );
            $orderBy = array('listing_rate_clause' => 'DESC', 'title' => 'ASC');
        } elseif (!empty($info['mostRewvied'])) {
            $lporders = '';
            $sortBy = array(
                'listing_reviewed_clause' =>
                    array(
                        'key' => 'listing_reviewed',
                        'type' => 'numeric',
                    ),
            );
            $orderBy = array('listing_reviewed_clause' => 'DESC', 'title' => 'ASC');
        } elseif (!empty($info['mostviewed'])) {
            $sortBy = array(
                'key' => 'post_views_count',
                'compare' => 'IN'
            );
            $orderBy = 'meta_value_num';
            $lporders = '';
            $sortBy = array(
                'post_views_count_clause' =>
                    array(
                        'key' => 'post_views_count',
                        'type' => 'numeric',
                    ),
            );
            $orderBy = array('post_views_count_clause' => 'DESC', 'title' => 'ASC');
        } elseif ($metakeyOrderBy == "post_views_count" || $metakeyOrderBy == "listing_reviewed" || $metakeyOrderBy == "listing_rate" || $metakeyOrderBy == "claimed") {
            $lporders = '';
            $sortBy = array(
                $metakeyOrderBy . '_clause' =>
                    array(
                        'key' => $metakeyOrderBy,
                        'type' => 'numeric',
                    ),
            );
            $orderBy = array($metakeyOrderBy . '_clause' => 'DESC', 'title' => 'ASC');
        } elseif ($metakeyOrderBy == "rand") {
            $lporders = '';
            $orderBy = 'rand';
        }

        if ($info['listing_openTime'] == 'open' && $_POST['averageRate'] == 'listing_rate') {
            $lporders = '';
            $sortBy = array(
                'listing_rate_clause' =>
                    array(
                        'key' => 'listing_rate',
                        'type' => 'numeric',
                    ),
            );
            $orderBy = array('listing_rate_clause' => 'DESC', 'title' => 'ASC','post__in'=>'ASC');
        }


        $statusArray = array();
        $optenTimeArray = array();
        $couponsArray = array();
        $inexArray = array();
        $moderArray = array();
        $pricyArray = array();
        $ultrArray = array();
        $lpcountwhile = 1;
        $relation = 'OR';

        if (!empty($info['inexpensive'])) {
            $inexArray = array(
                'key' => 'lp_listingpro_options',
                'value' => 'inexpensive',
                'compare' => 'LIKE'
            );
        }
        if (!empty($info['moderate'])) {
            $moderArray = array(
                'key' => 'lp_listingpro_options',
                'value' => 'moderate',
                'compare' => 'LIKE'
            );
        }
        if (!empty($info['pricey'])) {
            $pricyArray = array(
                'key' => 'lp_listingpro_options',
                'value' => 'pricey',
                'compare' => 'LIKE'
            );
        }
        if (!empty($info['ultra'])) {
            $ultrArray = array(
                'key' => 'lp_listingpro_options',
                'value' => 'ultra_high_end',
                'compare' => 'LIKE'
            );
        }
        if (!empty($info['coupons'])) {
            $couponsArray = array(
                'key' => 'listing_discount_data',
                'compare' => 'EXISTS'
            );
        }


        $formFieldsMetaArray = array();
        $fieldsArry = array();

        $lp_formFIelds = array();
        if (isset($_POST['formfields'])) {
            $lp_formFIelds = $_POST['formfields'];
        }
        $fieldsArryy = array();
        if (!empty($lp_formFIelds)) {
            foreach ($lp_formFIelds as $lp_singleField) {
                foreach ($lp_singleField as $k => $v) {

                    $kn = $k . '-mfilter';
                    $v = $k . '-' . $v;

                    if (!empty($v) && !empty($k)) {
                        $fieldsArryy[] = array('key' => 'lp_listingpro_options_fields', 'value' => $kn, 'compare' => 'LIKE');
                        $fieldsArryy2[] = array('key' => 'lp_listingpro_options_fields', 'value' => $v, 'compare' => 'LIKE');

                    }

                }


            }
        }

        $fieldsArry['relation'] = 'AND';
        $fieldsArry2['relation'] = 'AND';
        $n = 0;
        $n2 = 0;
        if (!empty($fieldsArryy)) {
            foreach ($fieldsArryy as $val) {
                $fieldsArry[$n] = $val;
                $n++;
            }
            foreach ($fieldsArryy2 as $val2) {
                $fieldsArry2[$n2] = $val2;
                $n2++;
            }
            $relation = "AND";
        }


        if (!empty($info['inexpensive']) || !empty($info['moderate']) || !empty($info['pricey']) || !empty($info['ultra'])) {
            $statusArray = array(
                'key' => 'lp_listingpro_options',
                'value' => 'price_status',
                'compare' => 'LIKE'
            );
            $relation = "AND";
        }
        if (!empty($info['inexpensive']) || !empty($info['moderate']) || !empty($info['pricey']) || !empty($info['ultra']) || !empty($info['averageRate']) || !empty($info['mostRewvied']) || !empty($info['mostviewed']) || !empty($_POST['formfields']) || !empty($info['coupons']) || !empty($sortBy)) {
            $priceQuery = array(
                'relation' => $relation, // Optional, defaults to "AND"
                $statusArray,
                array(
                    'relation' => 'OR',
                    $inexArray,
                    $moderArray,
                    $pricyArray,
                    $ultrArray
                ),
                $sortBy,
                $couponsArray,
                $fieldsArry,
                $fieldsArry2,
            );
        }

        $listingperpage = '';
        if (isset($listingpro_options['listing_per_page']) && !empty($listingpro_options['listing_per_page'])) {
            $listingperpage = $listingpro_options['listing_per_page'];
        } else {
            $listingperpage = 10;
        }

        /* if nearme is on */
        $listingperpageMain = '';
        if ((!empty($clat) && !empty($clong)) || $listing_time == "open") {

            $listingperpageMain = -1;

        } else {
            $listingperpageMain = $listingperpage;
        }
        /* end if nearme is on */

        /* added by zaheer on 13 march */
        $searchQuery = '';
        $TxQuery = array(
            $searchtagQuery,
            $tagQuery,
            $catQuery,
            $locQuery
        );
        if (empty($TxQuery)) {
            $TxQuery = array();
        }
        $ad_campaignsIDS = listingpro_get_campaigns_listing('lp_top_in_search_page_ads', true, $TxQuery, $searchQuery, $priceQuery, null, null, null);
        $type = 'listing';

        $args = array(
            'post_type' => $type,
            'post_status' => 'publish',
            'posts_per_page' => $listingperpageMain,
            'paged' => $paged,
            's' => $squery,
            'post__not_in' => $ad_campaignsIDS,
            'tax_query' => $TxQuery,
            'meta_query' => $priceQuery,
            'orderby' => $orderBy,
            'order' => $lporders,
        );


        //die(json_encode($_POST['formfields']));

        $lp_lat = '';
        $lp_lng = '';

        $my_query = null;
        $output = null;
        $result = null;
        $found = null;
        $htmlOutput = null;
        $my_query = new WP_Query($args);
        $found = $my_query->found_posts;
        $output .= '<div class="promoted-listings">';
        $listing_mobile_view = $listingpro_options['single_listing_mobile_view'];
        if ($listing_mobile_view == 'app_view2' && wp_is_mobile()) {
            $output .= '<div class="app-view-new-ads-slider">';
        }
        ob_start();
        $output .= listingpro_get_campaigns_listing('lp_top_in_search_page_ads', false, $TxQuery, $searchQuery, false, $s = null, $noOFListing = null, $ad_campaignsIDS);
        $output .= ob_get_contents();
        ob_end_clean();
        ob_flush();
        if ($listing_mobile_view == 'app_view2' && wp_is_mobile()) {
            $output .= '</div>';
        }
        $output .= '</div>';
        if ($my_query->have_posts()) {

            while ($my_query->have_posts()):
                $my_query->the_post();
                $proceeditnow = true;

                /* ///////radious filter starts//////// */
                $flag = true;
                if ((isset($_POST['sloc_address']) && $_POST['sloc_address'] != '') || (!empty($clat) && !empty($clong))) {
                    if (!empty($clat) && !empty($clong)) {
                        $my_bounds_ne_lat = $clat;
                        $my_bounds_ne_lng = $clong;

                        $my_bounds_sw_lat = $clat;
                        $my_bounds_sw_lng = $clong;
                    }

                    $lp_lat = listing_get_metabox_by_ID('latitude', get_the_ID());
                    $lp_lng = listing_get_metabox_by_ID('longitude', get_the_ID());
                    if (empty($lp_lat) || empty($lp_lng)) {
                        continue;
                    }
                    $lp_my_distance_range = (int)$_POST['distance_range'];
                    if ($units == 'mil') {
                        $lp_my_distance_range = $lp_my_distance_range * 1.6023;
                    }
                    $ne_distance = haversineGreatCircleDistance($lp_lat, $lp_lng, $my_bounds_ne_lat, $my_bounds_ne_lng);
                    $sw_distance = haversineGreatCircleDistance($lp_lat, $lp_lng, $my_bounds_sw_lat, $my_bounds_sw_lng);

                    $flag1 = ($lp_my_distance_range >= $ne_distance || $lp_my_distance_range >= $sw_distance) ? true : false;
                    if (!$flag1) {
                        $flag = $flag1;
                    }

                    //$latlongfilter = true;

                }

                //Zoom Search
                $lp_data_zoom = (isset($_POST['data_zoom'])) ? $_POST['data_zoom'] : '';
                if (!$flag || $lp_data_zoom == 'yes') {
                    $flag2 = listingproc_inBounds($lp_lat, $lp_lng, $my_bounds_ne_lat, $my_bounds_ne_lng, $my_bounds_sw_lat, $my_bounds_sw_lng);
                    if (!$flag2) {
                        continue;
                    }
                }
                /* ///////radious filter end//////// */

                if ($listing_time == 'open') {
                    $openStatus = listingpro_check_time(get_the_ID(), true);
                    if ($openStatus == 'open') {

                        $this_lat = listing_get_metabox_by_ID('latitude', get_the_ID());
                        $this_long = listing_get_metabox_by_ID('longitude', get_the_ID());
                        if (!empty($clat) && !empty($clong)) {
                            if (!empty($this_lat) && !empty($this_long)) {
                                $latlongfilter = true;
                                $calDistance = GetDrivingDistance($clat, $this_lat, $clong, $this_long, $units);
                                if (!empty($calDistance['distance']) && !empty($proceeditnow)) {
                                    $latlongArray[get_the_ID()] = $calDistance['distance'];
                                }
                            }

                        }

                        if ($latlongfilter == false && !empty($proceeditnow)) {
                            $optenTimeArray[get_the_ID()] = get_the_ID();
                        }

                    }
                } else {
                    $this_lat = listing_get_metabox_by_ID('latitude', get_the_ID());
                    $this_long = listing_get_metabox_by_ID('longitude', get_the_ID());
                    if (!empty($clat) && !empty($clong)) {
                        if (!empty($this_lat) && !empty($this_long)) {
                            $latlongfilter = true;
                            $calDistance = GetDrivingDistance($clat, $this_lat, $clong, $this_long, $units);
                            if (!empty($calDistance['distance']) && !empty($proceeditnow)) {
                                $latlongArray[get_the_ID()] = $calDistance['distance'];
                            }
                        }

                    }
                    if ($latlongfilter == false) {
                        ob_start();
                        global $listingpro_options;
                        $listing_mobile_view = $listingpro_options['single_listing_mobile_view'];
                        if ($listing_mobile_view == 'app_view' && wp_is_mobile() && !empty($proceeditnow)) {
                            get_template_part('mobile/listing-loop-app-view');
                        } elseif ($listing_mobile_view == 'app_view2' && wp_is_mobile() && !empty($proceeditnow)) {
                            get_template_part('mobile/listing-loop-app-view-new');
                        } else {
                            if (!empty($proceeditnow)) {
                                get_template_part('listing-loop');
                            }
                        }
                        $htmlOutput .= ob_get_contents();
                        ob_end_clean();
                        ob_flush();
                    }
                }
            endwhile;
            wp_reset_query();


            if ($latlongfilter == true) {
                $keysArrray = array();
                if (!empty($latlongArray)) {
                    asort($latlongArray);
                    foreach ($latlongArray as $key => $val) {
                        $keysArrray [] = $key;
                    }
                }


                $argss = array(
                    'post_type' => $type,
                    'posts_per_page' => $listingperpage,
                    'paged' => $paged,
                    'post__in' => $keysArrray,
                    'orderby' => 'post__in',
                    'order' => 'ASC'

                );
                $my_query = null;
                $my_query = new WP_Query($argss);
                $found = $my_query->found_posts;
                if ($my_query->have_posts()) {
                    $listing_mobile_view = $listingpro_options['single_listing_mobile_view'];
                    if (($listing_mobile_view == 'app_view' || $listing_mobile_view == 'app_view2') && wp_is_mobile()) {
                        $htmlOutput .= '<div class="map-view-list-container">';
                        while ($my_query->have_posts()):
                            $my_query->the_post();
                            ob_start();
                            if ($listing_mobile_view == 'app_view2') {
                                get_template_part('mobile/listing-loop-app-view-new');
                            } else {
                                get_template_part('mobile/listing-loop-app-view');
                            }
                            $htmlOutput .= ob_get_contents();
                            ob_end_clean();
                        endwhile;
                        wp_reset_query();
                        $htmlOutput .= '</div>';
                    }

                    while ($my_query->have_posts()):

                        $my_query->the_post();
                        $this_lat = listing_get_metabox_by_ID('latitude', get_the_ID());
                        $this_long = listing_get_metabox_by_ID('longitude', get_the_ID());

                        $calDistance = GetDrivingDistance($clat, $this_lat, $clong, $this_long, $units);

                        if (isset($_POST['sloc_address']) && $_POST['sloc_address'] != '') {
                        } else {
                            if (!empty($calDistance['distance'])) {
                                $nearbydata = $calDistance['distance'] . ' ' . $units;
                                $htmlOutput .= '<div class="lp-nearby-dist-data" data-lpnearbydist = "' . $nearbydata . '"></div>';
                            }
                        }
                        ob_start();
                        global $listingpro_options;
                        $listing_mobile_view = $listingpro_options['single_listing_mobile_view'];
                        if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                            get_template_part('mobile/listing-loop-app-view');
                        } elseif ($listing_mobile_view == 'app_view2' && wp_is_mobile()) {
                            get_template_part('mobile/listing-loop-app-view2');
                        } else {
                            get_template_part('listing-loop');
                        }
                        $htmlOutput .= ob_get_contents();
                        ob_end_clean();
                        ob_flush();
                        if (!empty($calDistance['distance'])) {
                            //$htmlOutput.='</div>';
                        }

                    endwhile;
                    wp_reset_query();
                }
            }
            if (!empty($optenTimeArray)) {
                $keysArrray = array();
                if (!empty($optenTimeArray)) {
                    asort($optenTimeArray);
                    foreach ($optenTimeArray as $key => $val) {
                        $keysArrray [] = $key;
                    }
                }


                $argss = array(
                    'post_type' => $type,
                    'posts_per_page' => $listingperpage,
                    'paged' => $paged,
                    'post__in' => $keysArrray,
                    'orderby' => 'post__in',
                    'order' => 'ASC'

                );
                $my_query = null;
                $my_query = new WP_Query($argss);
                $found = $my_query->found_posts;
                if ($my_query->have_posts()) {
                    $listing_mobile_view = $listingpro_options['single_listing_mobile_view'];
                    if (($listing_mobile_view == 'app_view' || $listing_mobile_view == 'app_view2') && wp_is_mobile()) {
                        $htmlOutput .= '<div class="map-view-list-container">';
                        while ($my_query->have_posts()):
                            $my_query->the_post();
                            ob_start();
                            global $listingpro_options;
                            $listing_mobile_view = $listingpro_options['single_listing_mobile_view'];
                            if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                                get_template_part('mobile/listing-loop-app-view');
                            } elseif ($listing_mobile_view == 'app_view2' && wp_is_mobile()) {
                                get_template_part('mobile/listing-loop-app-view-new');
                            } else {
                                get_template_part('listing-loop');
                            }
                            $htmlOutput .= ob_get_contents();
                            ob_end_clean();
                        endwhile;
                        wp_reset_query();
                        $htmlOutput .= '</div>';
                    }

                    while ($my_query->have_posts()):

                        $my_query->the_post();

                        ob_start();
                        global $listingpro_options;
                        $listing_mobile_view = $listingpro_options['single_listing_mobile_view'];
                        if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
                            get_template_part('mobile/listing-loop-app-view');
                        } elseif ($listing_mobile_view == 'app_view2' && wp_is_mobile()) {
                            get_template_part('mobile/listing-loop-app-view-new');
                        } else {
                            get_template_part('listing-loop');
                        }
                        $htmlOutput .= ob_get_contents();
                        ob_end_clean();
                        ob_flush();


                    endwhile;
                    wp_reset_query();
                }
            }


            if (empty($htmlOutput)) {
                $currentURL = '';

                $output .= '


						<div class="text-center margin-top-80 margin-bottom-80">


							<h2>' . esc_html__('No Results', 'listingpro') . '</h2>


							<p>' . esc_html__('Sorry! There are no listings matching your search.', 'listingpro') . '</p>


							<p>' . esc_html__('Try changing your search filters or ', 'listingpro') . '<a href="">' . esc_html__('Reset Filter', 'listingpro') . '</a></p>


						</div>


						';
            } else {
                $output .= $htmlOutput;
            }
        } elseif (empty($ad_campaignsIDS)) {
            $currentURL = '';
            $output .= '


						<div class="text-center margin-top-80 margin-bottom-80">


							<h2>' . esc_html__('No Results', 'listingpro') . '</h2>


							<p>' . esc_html__('Sorry! There are no listings matching your search.', 'listingpro') . '</p>


							<p>' . esc_html__('Try changing your search filters or ', 'listingpro') . '<a href="">' . esc_html__('Reset Filter', 'listingpro') . '</a></p>


						</div>


						';
        }
        if (($found > 0)) {
            $foundtext = 'Results';
        } else {
            $foundtext = 'Result';
        }
        if (!empty($htmlOutput)) {
            $output .= listingpro_load_more_filter($my_query, $pageno, $defSquery);
        }
        $output = mb_convert_encoding($output, 'UTF-8', 'auto');

        $showingResult = null;
        if (!empty($pageno)) {
            $fromPosts = (($pageno - 1) * $listingperpage) + 1;
        }else {
            $fromPosts = 1;
        }
        if (!empty($pageno)) {
            $toPosts = $listingperpage * $pageno;
        }else {
            $toPosts = $listingperpage;
        }
        if ($found == 0) {
            $showingResult = esc_html__('No Result Found', 'listingpro');
        }else {
            if ($toPosts > $found){
                $toPosts = $found;
            }
            $showingResult = esc_html__('Showing', 'listingpro') . ' ' . $fromPosts . '-' . $toPosts . ' ' . esc_html__('of', 'listingpro') . ' ' . $found;
        }
		
		$info['tag_name'] = isset($_POST['tag_name']) ? sanitize_text_field($_POST['tag_name']) : '';
		
        $term_group_result = json_encode(array(
            "foundtext" => $foundtext,
            "found" => $found,
            "tags" => $info['tag_name'],
            "cat" => $categoryName,
            "city" => $LocationName,
            "html" => $output,
            "opentime" => $listing_time,
            "dfdfdfdf" => $latlongArray,
            "latlongfilter" => $latlongfilter,
            "opentimefilter" => $opentimefilter,
            "searchtitles" => $searchtitles,
            "showingString" => $showingResult,
        ));
        die($term_group_result);
    }
}
/* ddfd */
/* ============== ListingPro Home page========== */
add_action('wp_ajax_listingpro_suggested_search', 'listingpro_suggested_search');
add_action('wp_ajax_nopriv_listingpro_suggested_search', 'listingpro_suggested_search');
if (!function_exists('listingpro_suggested_search')) {
    function listingpro_suggested_search()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        global $listingpro_options;
        $qString = '';
        $qString = sanitize_text_field($_POST['tagID']);
        $qString = strtolower($qString);
        $output = null;
        $TAGOutput = null;
        $CATOutput = null;
        $TagCatOutput = null;
        $TitleOutput = null;
        $lpsearchMode = "titlematch";
        if (isset($listingpro_options['lp_what_field_algo'])) {
            if (!empty($listingpro_options['lp_what_field_algo']) && $listingpro_options['lp_what_field_algo'] == "keyword") {
                $lpsearchMode = "keyword";
            }
        }

        $includeCas = array();
        $defCats = lp_theme_option('default_search_cats');
        if (!empty($defCats)) {
            $includeCas = $defCats;
        }

        if (empty($qString)) {
            global $listingpro_options;
            $cats;
            $ucat = array(
                'post_type' => 'listing',
                'hide_empty' => false,
                'orderby' => 'count',
                'order' => 'ASC',
                'include' => $includeCas,
//                'parent' => 0,
            );
            $catIcon = '';
            $categories = get_terms('listing-category', $ucat);
            foreach ($categories as $cat) {
                $catIcon = listingpro_get_term_meta($cat->term_id, 'lp_category_image');
                if (!empty($catIcon)) {
					if( hasFontAwesomeIconClass($catIcon) ){
						$catIcon = '<i class="icon icons-search-cat '.$catIcon.'"></i>'; 
					}else{
						$catIcon = '<img class="d-icon" alt="image" src="' . $catIcon . '" />';
					}
                }
                $cats[$cat->term_id] = '<li class="lp-default-cats" data-catid="' . $cat->term_id . '">' . $catIcon . '<span class="lp-s-cat">' . $cat->name . '</span></li>';
            }
            $output = array(
                'tag' => '',
                'cats' => $cats,
                'tagsncats' => '',
                'titles' => '',
                'more' => ''
            );
            $query_suggestion = json_encode(array(
                "tagID" => $qString,
                "suggestions" => $output
            ));
            die($query_suggestion);

        } else {


            $excludeTitleFromSearch = 'no';
            if (isset($listingpro_options['lp_exclude_listingtitle_switcher'])) {
                if (!empty($listingpro_options['lp_exclude_listingtitle_switcher']) && $listingpro_options['lp_exclude_listingtitle_switcher'] == 1) {
                    $excludeTitleFromSearch = 'yes';
                }
            }

            if ($excludeTitleFromSearch == "yes") {

                /* for low servers resources */
                $argsTaxs = array(
                    'orderby' => 'count',
                    'order' => 'ASC',
                    'hide_empty' => false,
                    'name__like' => $qString
                );
                $tagTerms = get_terms('list-tags', $argsTaxs);
                $catTerms = get_terms('listing-category', $argsTaxs);

                if (!empty($tagTerms)) {
                    foreach ($tagTerms as $tag) {

                        $tagTermMatch = false;
                        $tagTernName = strtolower($tag->name);

                        if ($lpsearchMode == "keyword") {
                            preg_match("/[$qString]/", "$tagTernName", $lpMatches, PREG_OFFSET_CAPTURE);
                            $lpresCnt = count($lpMatches);
                            if ($lpresCnt > 0) {
                                $tagTermMatch = true;
                            }

                        } else {
                            $tagTermMatch = strpos($tagTernName, $qString);
                        }

                        if ($tagTermMatch !== false) {
                            $TAGOutput[$tag->term_id] = '<li class="lp-wrap-tags" data-tagid="' . $tag->term_id . '"><span class="lp-s-tag">' . $tag->name . '</span></li>';
                        }
                    }
                }

                if (!empty($catTerms)) {

                    foreach ($catTerms as $cat) {
                        $catIcon = listingpro_get_term_meta($cat->term_id, 'lp_category_image');
                        if (!empty($catIcon)) {
							if( hasFontAwesomeIconClass($catIcon) ){
								$catIcon = '<i class="icon icons-search-cat '.$catIcon.'"></i>'; 
							}else{
								$catIcon = '<img class="d-icon" alt="image" src="' . $catIcon . '" />';
							}
						}

                        $catTermMatch = false;

                        $catTernName = $cat->name;
                        $catTernName = strtolower($catTernName);
                        if ($lpsearchMode == "keyword") {
                            preg_match("/[$qString]/", "$catTernName", $lpMatches, PREG_OFFSET_CAPTURE);
                            $lpresCnt = count($lpMatches);
                            if ($lpresCnt > 0) {
                                $catTermMatch = true;
                            }

                        } else {
                            $catTermMatch = strpos($catTernName, $qString);
                        }

                        if ($catTermMatch !== false) {
                            $CATOutput[$cat->term_id] = '<li class="lp-wrap-cats" data-catid="' . $cat->term_id . '">' . $catIcon . '<span class="lp-s-cat">' . $cat->name . '</span></li>';
                        }

                    }

                }


            } else {

                /* for server with good resources */
                $args = array(
                    'posts_per_page' => 5, // Number of related posts to display.
                    'post_type' => 'listing',
                    'post_status' => 'publish',
                    's' => $qString,
                );


                $my_query = new wp_query($args);
                if ($my_query->have_posts()) {
                    while ($my_query->have_posts()):
                        $my_query->the_post();

                        $locTerms = get_the_terms(get_the_ID(), 'location');
                        $tagTerms = get_the_terms(get_the_ID(), 'list-tags');
                        $catTerms = get_the_terms(get_the_ID(), 'listing-category');

                        $listThumb = '';
                        if (has_post_thumbnail()) {
                            $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'thumbnail');
                            if (!empty($image[0])) {
                                $listThumb = "<img alt='image' src='" . $image[0] . "' />
												";
                            } else {
                                $listThumb = '<img src="' . esc_html__('https://placehold.co/50x50', 'listingpro') . '" alt="image">';
                            }
                        }

                        $machTitles = false;
                        $listTitle = get_the_title();
                        $listTitle = strtolower($listTitle);
                        if ($lpsearchMode == "keyword") {
                            preg_match("/[$qString]/", "$listTitle", $lpMatches, PREG_OFFSET_CAPTURE);
                            $lpresCnt = count($lpMatches);
                            if ($lpresCnt > 0) {
                                $machTitles = true;
                            }

                        } else {
                            $machTitles = strpos($listTitle, $qString);
                        }
                        if ($machTitles !== false) {
                            $TitleOutput[] = '<li class="lp-wrap-title" data-url="' . get_the_permalink() . '">' . $listThumb . '<span class="lp-s-title"><a href="' . get_the_permalink() . '">' . $listTitle . ' <span class="lp-loc">' . $locTerms[0]->name . '</span></a></span></li>';
                        }
                    endwhile;
                    wp_reset_postdata();
                }

                /* for category and tags */

                $argsTaxs = array(
                    'orderby' => 'count',
                    'order' => 'ASC',
                    'hide_empty' => true,
                    'name__like' => $qString
                );
                $tagTerms = get_terms('list-tags', $argsTaxs);
                $catTerms = get_terms('listing-category', $argsTaxs);

                if (!empty($tagTerms)) {
                    foreach ($tagTerms as $tag) {

                        $tagTermMatch = false;
                        $tagTernName = strtolower($tag->name);

                        if ($lpsearchMode == "keyword") {
                            preg_match("/[$qString]/", "$tagTernName", $lpMatches, PREG_OFFSET_CAPTURE);
                            $lpresCnt = count($lpMatches);
                            if ($lpresCnt > 0) {
                                $tagTermMatch = true;
                            }

                        } else {
                            $tagTermMatch = strpos($tagTernName, $qString);
                        }

                        if ($tagTermMatch !== false) {
                            $TAGOutput[$tag->term_id] = '<li class="lp-wrap-tags" data-tagid="' . $tag->term_id . '"><span class="lp-s-tag">' . $tag->name . '</span></li>';
                        }
                    }
                }

                if (!empty($catTerms)) {

                    foreach ($catTerms as $cat) {
                        $catIcon = listingpro_get_term_meta($cat->term_id, 'lp_category_image');
						if (!empty($catIcon)) {
							if( hasFontAwesomeIconClass($catIcon) ){
								$catIcon = '<i class="icon icons-search-cat '.$catIcon.'"></i>'; 
							}else{
								$catIcon = '<img class="d-icon" alt="image" src="' . $catIcon . '" />';
							}
						}

                        $catTermMatch = false;

                        $catTernName = $cat->name;
                        $catTernName = strtolower($catTernName);
                        if ($lpsearchMode == "keyword") {
                            preg_match("/[$qString]/", "$catTernName", $lpMatches, PREG_OFFSET_CAPTURE);
                            $lpresCnt = count($lpMatches);
                            if ($lpresCnt > 0) {
                                $catTermMatch = true;
                            }

                        } else {
                            $catTermMatch = strpos($catTernName, $qString);
                        }

                        if ($catTermMatch !== false) {
                            $CATOutput[$cat->term_id] = '<li class="lp-wrap-cats" data-catid="' . $cat->term_id . '">' . $catIcon . '<span class="lp-s-cat">' . $cat->name . '</span></li>';
                        }

                    }

                }

                /* **********for tags in cat********* */
//Old

//                $TagstermIds = array_map(function (\WP_Term $term) {
//                    return $term->term_id;
//                }, get_terms([
//                    'name__like' => $qString,
//                ]));

//New
                $args = array(
                    'taxonomy' => array('list-tags'),
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'hide_empty' => false,
                    'fields' => 'all',
                    'name__like' => $qString
                );
                $Tagsterm = get_terms($args);
                $TagstermIds = array();

                foreach( $Tagsterm as $category ) {
                    $TagstermIds[$category->term_id] = $category->term_id;
                }

                if (!empty($TagstermIds)) {

                    $args = array(
                        'posts_per_page' => 5, // Number of related posts to display.
                        'post_type' => 'listing',
                        'post_status' => 'publish',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'list-tags',
                                'field' => 'id',
                                'terms' => $TagstermIds,
                            ),
                        ),
                    );
                    $my_query = null;
                    $my_query = new wp_query($args);
                    if ($my_query->have_posts()) {
                        while ($my_query->have_posts()):
                            $my_query->the_post();

                            $tagTerms = get_the_terms(get_the_ID(), 'list-tags');
                            $catTerms = get_the_terms(get_the_ID(), 'listing-category');

                            if (!empty($catTerms) && !empty($tagTerms)) {
                                $catName = $catTerms[0];
                                $term_id = $catName->term_id;
                                $parent = '';
                                if (!empty($term_id)) {
                                    $termparent = get_term_by('id', $term_id, 'listing-category');
                                    $parent = $termparent->parent;
                                }
                                $catIcon = listingpro_get_term_meta($catName->term_id, 'lp_category_image');
                                if (empty($catIcon)) {
                                    $catIcon = listingpro_get_term_meta($parent, 'lp_category_image');
                                }
                                if (!empty($catIcon)) {
									if( hasFontAwesomeIconClass($catIcon) ){
										$catIcon = '<i class="icon icons-search-cat '.$catIcon.'"></i>'; 
									}else{
										$catIcon = '<img class="d-icon" alt="image" src="' . $catIcon . '" />';
									}
								}
                                foreach ($tagTerms as $tag) {

                                    $tagTermMatch = false;
                                    $tagTernName = strtolower($tag->name);

                                    if ($lpsearchMode == "keyword") {
                                        preg_match("/[$qString]/", "$tagTernName", $lpMatches, PREG_OFFSET_CAPTURE);
                                        $lpresCnt = count($lpMatches);
                                        if ($lpresCnt > 0) {
                                            $tagTermMatch = true;
                                        }

                                    } else {
                                        $tagTermMatch = strpos($tagTernName, $qString);
                                    }

                                    if ($tagTermMatch !== false) {
                                        $TAGOutput[$tag->term_id] = '<li class="lp-wrap-tags" data-tagid="' . $tag->term_id . '"><span class="lp-s-tag">' . $tag->name . '</span></li>';
                                        $TagCatOutput[] = '<li class="lp-wrap-catsntags" data-tagid="' . $tag->term_id . '" data-catid="' . $catName->term_id . '">' . $catIcon . '<span class="lp-s-tag">' . $tag->name . '</span><span> ' . esc_html__('in', 'listingpro') . ' </span><span class="lp-s-cat">' . $catName->name . '</span></li>';
                                    }
                                }

                            }


                        endwhile;
                        wp_reset_postdata();
                    }
                }

                /* **********for tags in cat ends********* */


            }

            
            if(is_array($TAGOutput)) $TAGOutput = array_unique($TAGOutput);
            if(is_array($CATOutput)) $CATOutput = array_unique($CATOutput);
            if(is_array($TagCatOutput)) $TagCatOutput = array_unique($TagCatOutput);
            if(is_array($TitleOutput)){
                $TitleOutput = array_unique($TitleOutput);
            }
            if ((!empty($TAGOutput) && count($TAGOutput) > 0) || (!empty($CATOutput) && count($CATOutput) > 0) || (!empty($TagCatOutput) && count($TagCatOutput) > 0) || (!empty($TitleOutput) && count($TitleOutput) > 0)) {
                $output = array(
                    'tag' => $TAGOutput,
                    'cats' => $CATOutput,
                    'tagsncats' => $TagCatOutput,
                    'titles' => $TitleOutput,
                    'more' => '',
                    'matches' => $machTitles
                );
            } else {
                $moreResult = array();
                $mResults = '<strong>' . esc_html__('More results for ', 'listingpro') . '</strong>';
                $mResults .= $qString;
                $moreResult[] = '<li class="lp-wrap-more-results" data-moreval="' . $qString . '">' . $mResults . '</li>';
                $output = array(
                    'tag' => '',
                    'cats' => '',
                    'tagsncats' => '',
                    'titles' => '',
                    'more' => $moreResult
                );
            }
            $query_suggestion = json_encode(array(
                "tagID" => $qString,
                "suggestions" => $output
            ));
            die($query_suggestion);
        }
    }
}
/* ======================show cateogries on focus================ */
add_action('wp_ajax_listingpro_suggested_cats', 'listingpro_suggested_cats');
add_action('wp_ajax_nopriv_listingpro_suggested_cats', 'listingpro_suggested_cats');
if (!function_exists('listingpro_suggested_cats')) {
    function listingpro_suggested_cats()
    {
        global $listingpro_options;
        $cats;
        $homeSearchCategory = $listingpro_options['home_banner_search_cats'];
        $ucat = array(
            'post_type' => 'listing',
            'hide_empty' => false,
            'orderby' => 'count',
            'order' => 'ASC',
            'include' => $homeSearchCategory
        );
        $categories = get_terms('listing-category', $ucat);
        foreach ($categories as $category) {
            $cats[] = $category->name;
        }
        $query_suggestion = json_encode(array(
            "cats" => $cats
        ));
        die($query_suggestion);
    }
}
/* ======================show cateogries on focus================ */

/* ============== ListingPro website visits ============ */

add_action('wp_ajax_listingpro_website_visit', 'listingpro_website_visit');
add_action('wp_ajax_nopriv_listingpro_website_visit', 'listingpro_website_visit');

if (!function_exists('listingpro_website_visit')) {
    function listingpro_website_visit()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $lpCountry = '';
        $lpCity = '';
        $lpZip = '';
        $lp_id = '';
        $lp_id = $_POST['lp-id'];
        $lpCountry = $_POST['lp-country'];
        $lpCity = $_POST['lp-city'];
        $lpZip = $_POST['lp-zip'];
        $actor = esc_html__('Someone', 'listingpro');
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $actor = $current_user->user_login;
        }

        $visitorInfo = esc_html__('Country : ', 'listingpro');
        $visitorInfo .= $lpCountry;
        $visitorInfo .= esc_html__(' City : ', 'listingpro');
        $visitorInfo .= $lpCity;
        $visitorInfo .= esc_html__(' Zip : ', 'listingpro');
        $visitorInfo .= $lpZip;

        $listing_id = $lp_id;
        $listingData = get_post($listing_id);
        $authID = $listingData->post_author;
        //$currentdate = date("Y-m-d h:i:a");
        //$currentdate = date("d-m-Y h:i:a");
        $currentdate = current_time('mysql');

        /* updating lead */
        $leadsCount = '';
        $leadsCount = get_user_meta($authID, 'leads_count', true);
        if (isset($leadsCount)) {
            $leadsCount = (int)$leadsCount + 1;
            update_user_meta($authID, 'leads_count', $leadsCount);
        } else {
            $leadsCount = 0;
            update_user_meta($authID, 'leads_count', $leadsCount);
        }
        /*  */

        /* for stats chart */
        lp_set_this_stats_for_chart($authID, $listing_id, 'leads');
        /* stats chart ends */
        $activityData = array();
        $activityData = array(array(
            'type' => 'visit',
            'actor' => $actor,
            'reviewer' => $visitorInfo,
            'listing' => $listing_id,
            'rating' => '',
            'time' => $currentdate
        ));

        $updatedActivitiesData = array();
        $lp_recent_activities = get_option('lp_recent_activities');
        if ($lp_recent_activities != false) {

            $existingActivitiesData = get_option('lp_recent_activities');
            if (array_key_exists($authID, $existingActivitiesData)) {
                $currenctActivityData = $existingActivitiesData[$authID];
                if (!empty($currenctActivityData)) {
                    if (count($currenctActivityData) >= 20) {
                        $currenctActivityData = array_slice($currenctActivityData, 1, 20);
                        $updatedActivityData = array_merge($currenctActivityData, $activityData);
                    } else {
                        $updatedActivityData = array_merge($currenctActivityData, $activityData);
                    }
                }
                $existingActivitiesData[$authID] = $updatedActivityData;
            } else {
                $existingActivitiesData[$authID] = $activityData;
            }
            $updatedActivitiesData = $existingActivitiesData;
        } else {
            $updatedActivitiesData[$authID] = $activityData;
        }
        update_option('lp_recent_activities', $updatedActivitiesData);


        $lp_response = json_encode(array("success" => "ok"));
        die($lp_response);
    }
}
/* ============== ListingPro phone number clicked ============ */

add_action('wp_ajax_listingpro_phone_clicked', 'listingpro_phone_clicked');
add_action('wp_ajax_nopriv_listingpro_phone_clicked', 'listingpro_phone_clicked');

if (!function_exists('listingpro_phone_clicked')) {
    function listingpro_phone_clicked()
    {

        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        $lpCountry = '';
        $lpCity = '';
        $lpZip = '';
        $lp_id = '';
        $lp_id = $_POST['lp-id'];
        $lpCountry = $_POST['lp-country'];
        $lpCity = $_POST['lp-city'];
        $lpZip = $_POST['lp-zip'];
        $actor = esc_html__('Someone', 'listingpro');
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $actor = $current_user->user_login;
        }

        $visitorInfo = esc_html__('Country : ', 'listingpro');
        $visitorInfo .= $lpCountry;
        $visitorInfo .= esc_html__(' City : ', 'listingpro');
        $visitorInfo .= $lpCity;
        $visitorInfo .= esc_html__(' Zip : ', 'listingpro');
        $visitorInfo .= $lpZip;

        $listing_id = $lp_id;
        $listingData = get_post($listing_id);
        $authID = $listingData->post_author;
        //$currentdate = date("Y-m-d h:i:a");
        //$currentdate = date("d-m-Y h:i:a");
        $currentdate = current_time('mysql');


        /* updating lead */
        $leadsCount = '';
        $leadsCount = get_user_meta($authID, 'leads_count', true);
        if (isset($leadsCount)) {
            $leadsCount = (int)$leadsCount + 1;
            update_user_meta($authID, 'leads_count', $leadsCount);
        } else {
            $leadsCount = 0;
            update_user_meta($authID, 'leads_count', $leadsCount);
        }
        /*  */

        /* for stats chart */
        lp_set_this_stats_for_chart($authID, $listing_id, 'leads');
        /* stats chart ends */

        $activityData = array();
        $activityData = array(array(
            'type' => 'phone',
            'actor' => $actor,
            'reviewer' => $visitorInfo,
            'listing' => $listing_id,
            'rating' => '',
            'time' => $currentdate
        ));

        $updatedActivitiesData = array();
        $lp_recent_activities = get_option('lp_recent_activities');
        if ($lp_recent_activities != false) {

            $existingActivitiesData = get_option('lp_recent_activities');
            if (array_key_exists($authID, $existingActivitiesData)) {
                $currenctActivityData = $existingActivitiesData[$authID];
                if (!empty($currenctActivityData)) {
                    if (count($currenctActivityData) >= 20) {
                        $currenctActivityData = array_slice($currenctActivityData, 1, 20);
                        $updatedActivityData = array_merge($currenctActivityData, $activityData);
                    } else {
                        $updatedActivityData = array_merge($currenctActivityData, $activityData);
                    }
                }
                $existingActivitiesData[$authID] = $updatedActivityData;
            } else {
                $existingActivitiesData[$authID] = $activityData;
            }
            $updatedActivitiesData = $existingActivitiesData;
        } else {
            $updatedActivitiesData[$authID] = $activityData;
        }
        update_option('lp_recent_activities', $updatedActivitiesData);


        $lp_response = json_encode(array("success" => "ok"));
        die($lp_response);
    }
}


if (!function_exists('listingproc_inBounds')) {
    function listingproc_inBounds($pointLat, $pointLong, $boundsNElat, $boundsNElong, $boundsSWlat, $boundsSWlong)
    {
        $eastBound = $pointLong < $boundsNElong;
        $westBound = $pointLong > $boundsSWlong;

        if ($boundsNElong < $boundsSWlong) {
            $inLong = $eastBound || $westBound;
        } else {
            $inLong = $eastBound && $westBound;
        }

        $inLat = $pointLat > $boundsSWlat && $pointLat < $boundsNElat;
        return $inLat && $inLong;
    }
}// listingproc_inBounds

if (!function_exists('haversineGreatCircleDistance')) {

    function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        $latitudeFrom = (float)$latitudeFrom;
        $longitudeFrom = (float)$longitudeFrom;
        $latitudeTo = (float)$latitudeTo;
        $longitudeTo = (float)$longitudeTo;

        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}