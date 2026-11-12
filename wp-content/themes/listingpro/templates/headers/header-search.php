<?php
global $listingpro_options;
$type = 'listing';
$term_id = '';
$taxName = '';
$termID = '';
$term_ID = '';
$termName = '';
$sterm = '';
$sloc = '';
$termName = '';
$locName = '';
$catterm = '';
$parent = '';
$loc_ID = '';
$lpstag =   '';
$locationID = '';
$sQuery = '';
global $paged;
$taxTaxDisplay = true;



$listCats = array();
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
if (isset($search_loc_field_hide) && $search_loc_field_hide == 1) {
    $hideWhereClass = "hide-where";
    $searchHide = "search-hide";
} else {
    $searchHide = "";
}
$hideWhatClass = '';
$whatHide = '';
if (isset($lp_what_field_switcher) && $lp_what_field_switcher == 1) {
    $hideWhatClass = "hide-what";
    $whatHide = "what-hide";
} else {
    $whatHide = "";
}


$srchBr = '';
$slct = '';
if ($home_srch_loc_switchr == true) {
    $srchBr = 'ui-widget';
    $slct = 'select2';
} else {
    $srchBr = 'ui-widget border-dropdown';
    $slct = 'chosen-select5';
}

$locOption = '';
if ($search_loc_option == 'yes' && is_front_page()) {
    $locOption = 'yes';
} elseif ($search_loc_option == 'no') {
    $locOption = 'no';
}

$locations_search_type = $listingpro_options['lp_listing_search_locations_type'];
$locArea = '';
if (!empty($locations_search_type) && $locations_search_type == "auto_loc") {
    $locArea = $listingpro_options['lp_listing_search_locations_range'];
}

if (!isset($_GET['s'])) {
    $queried_object = get_queried_object();
    $term_id = $queried_object->term_id;
    $taxName = $queried_object->taxonomy;
    if (!empty($term_id)) {
        $termID = get_term_by('id', $term_id, $taxName);
        $termName = $termID->name;
        $parent = $termID->parent;
        $term_ID = $termID->term_id;
        if (is_tax('location')) {
            $loc_ID = $termID->term_id;
            $locName = $termName;
        } elseif (is_tax('features')) {
            $feature_ID = $termID->term_id;
        } elseif (is_tax('list-tags')) {
            $lpstag = $termID->term_id;
        }
    }
} elseif (isset($_GET['lp_s_cat']) || isset($_GET['lp_s_tag']) || isset($_GET['lp_s_loc'])) {
    if (isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat'])) {
        $sterm = wp_kses_post($_GET['lp_s_cat']);
        $term_ID = wp_kses_post($_GET['lp_s_cat']);
        $termo = get_term_by('id', $sterm, 'listing-category');
        $parent = $termo->parent;
    }
    if (isset($_GET['lp_s_cat']) && empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])) {
        $sterm = wp_kses_post($_GET['lp_s_tag']);
        $lpstag = wp_kses_post($_GET['lp_s_tag']);
    }

    if (isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])) {
        $sterm = wp_kses_post($_GET['lp_s_tag']);
        $lpstag = wp_kses_post($_GET['lp_s_tag']);
    }

    if (isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])) {
        $loc_ID = wp_kses_post($_GET['lp_s_loc']);
    }
}
if (isset($_GET['select']) && !empty($_GET['select'])) {
    $sQuery = $_GET['select'];
}



?>
<div class="col-md-12">
    <div class="lp-search-bar lp-header-search-form text-center">
        <form autocomplete="off" class="form-inline" action="<?php echo home_url(); ?>" method="get" accept-charset="UTF-8">
            <?php
            $searchFilter = $listingpro_options['enable_cats_search_filter'];

            if (is_archive() || is_search()) :
            ?>
                <div class="select-filter" id="searchform">
                    <i class="fa fa-list" aria-hidden="true"></i>
                    <?php if ($searchFilter == 1) { ?>
                        <select class="chosen-select2" id="searchcategory">
                            <option value=""><?php echo esc_html__('All Categories', 'listingpro'); ?></option>

                            <?php
                            $args = array(
                                'post_type' => 'listing',
                                'order' => 'ASC',
                                'hide_empty' => false,
                                'parent' => 0,
                            );
                            $locations = get_terms('listing-category', $args);
                            foreach ($locations as $location) {
                                if ($term_ID == $location->term_id) {
                                    $selected = 'selected';
                                } else {
                                    $selected = '';
                                }
                                echo '<option ' . $selected . ' value="' . $location->term_id . '">' . $location->name . '</option>';

                                $args1 = array(
                                    'post_type' => 'listing',
                                    'order' => 'ASC',
                                    'hide_empty' => false,
                                    'parent' => $location->term_id,
                                );
                                $locations1 = get_terms('listing-category', $args1);
                                foreach ($locations1 as $location1) {
                                    if ($term_ID == $location1->term_id) {
                                        $selected = 'selected';
                                    } else {
                                        $selected = '';
                                    }
                                    echo '<option ' . $selected . ' value="' . $location1->term_id . '">-' . $location1->name . '</option>';

                                    $args2 = array(
                                        'post_type' => 'listing',
                                        'order' => 'ASC',
                                        'hide_empty' => false,
                                        'parent' => $location1->term_id,
                                    );
                                    $locations2 = get_terms('listing-category', $args2);
                                    foreach ($locations2 as $location2) {
                                        if ($term_ID == $location2->term_id) {
                                            $selected = 'selected';
                                        } else {
                                            $selected = '';
                                        }
                                        echo '<option ' . $selected . ' value="' . $location2->term_id . '">--' . $location2->name . '</option>';

                                        $args3 = array(
                                            'post_type' => 'listing',
                                            'order' => 'ASC',
                                            'hide_empty' => false,
                                            'parent' => $location2->term_id,
                                        );
                                        $locations3 = get_terms('listing-category', $args3);
                                        foreach ($locations3 as $location3) {
                                            if ($term_ID == $location3->term_id) {
                                                $selected = 'selected';
                                            } else {
                                                $selected = '';
                                            }
                                            echo '<option ' . $selected . ' value="' . $location3->term_id . '">---' . $location3->name . '</option>';
                                        }
                                    }
                                }
                            }
                            ?>
                        </select>
                    <?php } ?>
                    <input type="hidden" name="clat">
                    <input type="hidden" name="clong">


                </div>
            <?php endif; ?>
            <?php
            if (isset($lp_what_field_switcher) && $lp_what_field_switcher == 0) {
            ?>
                <div class="<?php if (is_archive() || is_search()) : ?>right-margin-20<?php endif; ?> form-group lp-suggested-search <?php echo esc_attr($hideWhereClass); ?> <?php echo esc_attr($hideWhatClass); ?>">
                    <?php
                    if ((isset($lp_what_field_switcher) && $lp_what_field_switcher == 0) || (isset($search_loc_field_hide) && $search_loc_field_hide == 0)) {
                        if ((is_archive() || is_search()) && !wp_is_mobile()) {
                    ?>
                            <div class="form-group <?php echo esc_attr($searchHide); ?>">
                                <div class="lp-search-bar-right">
                                    <a href="#" class="keyword-ajax">Go</a>
                                </div>
                            </div>
                    <?php }
                    } ?>

                    <?php
                    if ((is_archive() || is_search()) && !wp_is_mobile()) {
                    ?>
                        <div class="input-group-addon lp-border input-group-addon-keyword"><?php esc_html_e('Keyword', 'listingpro'); ?></div>
                        <div class="pos-relative">
                            <div class="what-placeholder pos-relative lp-search-form-what" data-holder="">
                                <input autocomplete="off" type="text" class="lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields" name="select" id="skeyword-filter" value="<?php echo esc_attr($sQuery); ?>" placeholder="<?php echo esc_attr($search_placeholder); ?>" data-prev-value='0' data-noresult="<?php echo esc_html__('More results for', 'listingpro'); ?>">
                                <i class="cross-search-q fa fa-times-circle" aria-hidden="true"></i>

                            </div>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="input-group-addon lp-border"><?php esc_html_e('What', 'listingpro'); ?></div>
                        <div class="pos-relative">
                            <div class="what-placeholder pos-relative lp-search-form-what" data-holder="">
                                <input autocomplete="off" type="text" class="lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields" name="select" id="select" placeholder="<?php echo esc_attr($search_placeholder); ?>" data-prev-value='0' data-noresult="<?php echo esc_html__('More results for', 'listingpro'); ?>">
                                <i class="cross-search-q fa fa-times-circle" aria-hidden="true"></i>
                                <img class='loadinerSearch' width="100px" alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/search-load.gif' ?>" />

                            </div>
                            <div id="input-dropdown">
                                <ul>
                                    <?php

                                    $args = array(
                                        'post_type' => 'listing',
                                        'order' => 'ASC',
                                        'hide_empty' => false,
                                    );
                                    $default_search_cats = '';
                                    if (isset($listingpro_options['default_search_cats'])) {
                                        $default_search_cats = $listingpro_options['default_search_cats'];
                                    }
                                    if (empty($default_search_cats)) {
                                        $listCatTerms = get_terms('listing-category', $args);
                                        if (!empty($listCatTerms) && !is_wp_error($listCatTerms)) {
                                            foreach ($listCatTerms as $term) {
                                                $catIcon = listingpro_get_term_meta($term->term_id, 'lp_category_image');
                                                if (!empty($catIcon)) {
													if( hasFontAwesomeIconClass($catIcon) ){
														$catIcon = '<i class="icon icons-search-cat '.$catIcon.'"></i>'; 
													}else{
														$catIcon = '<img class="d-icon" alt="image" src="'.$catIcon.'" />';
													}
												}
                                                echo '<li class="lp-wrap-cats" data-catid="' . $term->term_id . '">' . $catIcon . '<span class="lp-s-cat">' . $term->name . '</span></li>';

                                                $defaultCats .= '<li class="lp-wrap-cats" data-catid="' . $term->term_id . '">' . $catIcon . '<span class="lp-s-cat">' . $term->name . '</span></li>';
                                            }
                                        }
                                    } else {
                                        foreach ($default_search_cats as $catTermID) {
                                            $term = get_term_by('id', $catTermID, 'listing-category');
                                            $catIcon = listingpro_get_term_meta($term->term_id, 'lp_category_image');
                                            if (!empty($catIcon)) {
												if( hasFontAwesomeIconClass($catIcon) ){
													$catIcon = '<i class="icon icons-search-cat '.$catIcon.'"></i>'; 
												}else{
													$catIcon = '<img class="d-icon" alt="image" src="'.$catIcon.'" />';
												}
											}
                                            echo '<li class="lp-wrap-cats" data-catid="' . $term->term_id . '">' . $catIcon . '<span class="lp-s-cat">' . $term->name . '</span></li>';

                                            $defaultCats .= '<li class="lp-wrap-cats" data-catid="' . $term->term_id . '">' . $catIcon . '<span class="lp-s-cat">' . $term->name . '</span></li>';
                                        }
                                    }
                                    ?>

                                </ul>
                                <!-- New Update 2.7.0 -->
                                <div style="display:none" id="def-cats"><?php echo esc_attr($defaultCats); ?></div>
                                <!-- End New Update 2.7.0 -->
                            </div>
                        </div>
                    <?php
                    }
                    ?>

                </div>
            <?php } ?>

            <?php
            if (isset($search_loc_field_hide) && $search_loc_field_hide == 0) {
                if (!empty($locations_search_type) && $locations_search_type == "auto_loc") {

                    $sLocID = '';
                    if (!empty($loc_ID)) {
                        $sLocID = $loc_ID;
                    }

            ?>
                    <div class="form-group lp-location-auto-fix lp-location-search <?php echo esc_attr($hideWhatClass); ?>">
                        <?php
                        if ((is_archive() || is_search()) && !wp_is_mobile()) {
                        ?>
                            <div class="input-group-addon lp-border lp-where"><?php esc_html_e('Location', 'listingpro'); ?></div>
                        <?php } else { ?>
                            <div class="input-group-addon lp-border lp-where"><?php esc_html_e('Where', 'listingpro'); ?></div>
                        <?php } ?>
                        <div data-option="<?php echo esc_attr($locOption); ?>" class="<?php echo esc_attr($srchBr); ?>">
                            <i class="fa fa-crosshairs"></i>
                            <input autocomplete="off" id="cities" class="form-control" data-country="<?php echo esc_attr($locArea); ?>" placeholder="<?php echo esc_attr($location_default_text); ?>" value="<?php echo esc_attr($locName); ?>">
                            <input type="hidden" autocomplete="off" id="lp_search_loc" name="lp_s_loc" value="<?php if (isset($_GET['lp_s_loc'])) : echo esc_attr($_GET['lp_s_loc']);
                                                                                                                endif; ?>">
                        </div>
                    </div>
                <?php
                } elseif (!empty($locations_search_type) && $locations_search_type == "manual_loc") {
                ?>
                    <div class="form-group lp-location-search <?php echo esc_attr($hideWhatClass); ?>">
                        <?php
                        if ((is_archive() || is_search()) && !wp_is_mobile()) {
                        ?>
                            <div class="input-group-addon lp-border lp-where"><?php esc_html_e('Location', 'listingpro'); ?></div>
                        <?php } else { ?>
                            <div class="input-group-addon lp-border lp-where"><?php esc_html_e('Where', 'listingpro'); ?></div>
                        <?php } ?>
                        <div data-option="<?php echo esc_attr($locOption); ?>" class="<?php echo esc_attr($srchBr); ?>">
                            <i class="fa fa-crosshairs"></i>
                            <?php
                            if (is_front_page()) {
                            ?>
                                <select class="<?php echo esc_attr($slct); ?>" name="lp_s_loc" id="searchlocation">
                                <?php
                            } else {
                                ?>
                                    <select class="<?php echo esc_attr($slct); ?>" name="lp_s_loc" id="searchlocation">
                                    <?php
                                }
                                    ?>
                                    <option id="def_location" value=""><?php echo esc_attr($location_default_text); ?></option>
                                    <?php
                                    $args = array(
                                        'post_type' => 'listing',
                                        'order' => 'ASC',
                                        'hide_empty' => false,
                                        'parent' => 0,
                                    );
                                    $locations = get_terms('location', $args);
                                    if (!empty($locations) && !is_wp_error($locations)) {
                                        foreach ($locations as $location) {
                                            if ($loc_ID == $location->term_id) {
                                                $selected = 'selected';
                                            } else {
                                                $selected = '';
                                            }
                                            echo '<option ' . $selected . ' value="' . $location->term_id . '">' . $location->name . '</option>';

                                            $args1 = array(
                                                'post_type' => 'listing',
                                                'order' => 'ASC',
                                                'hide_empty' => false,
                                                'parent' => $location->term_id,
                                            );

                                            //$sub = get_term_children( $location->term_id, 'location' );
                                            $locations1 = get_terms('location', $args1);
                                            if (!empty($locations1) && !is_wp_error($locations1)) {
                                                foreach ($locations1 as $location1) {
                                                    if ($loc_ID == $location1->term_id) {
                                                        $selected = 'selected';
                                                    } else {
                                                        $selected = '';
                                                    }
                                                    echo '<option ' . $selected . ' value="' . $location1->term_id . '">-&nbsp;&nbsp;' . $location1->name . '</option>';

                                                    //2nd
                                                    $args2 = array(
                                                        'post_type' => 'listing',
                                                        'order' => 'ASC',
                                                        'hide_empty' => false,
                                                        'parent' => $location1->term_id,
                                                    );

                                                    //$sub = get_term_children( $location->term_id, 'location' );
                                                    $locations2 = get_terms('location', $args2);
                                                    if (!empty($locations2) && !is_wp_error($locations2)) {
                                                        foreach ($locations2 as $location2) {
                                                            if ($loc_ID == $location2->term_id) {
                                                                $selected = 'selected';
                                                            } else {
                                                                $selected = '';
                                                            }
                                                            echo '<option ' . $selected . ' value="' . $location2->term_id . '">--&nbsp;&nbsp;' . $location2->name . '</option>';

                                                            //3rd

                                                            $args3 = array(
                                                                'post_type' => 'listing',
                                                                'order' => 'ASC',
                                                                'hide_empty' => false,
                                                                'parent' => $location2->term_id,
                                                            );

                                                            //$sub = get_term_children( $location->term_id, 'location' );
                                                            $locations3 = get_terms('location', $args3);
                                                            if (!empty($locations3) && !is_wp_error($locations3)) {
                                                                foreach ($locations3 as $location3) {
                                                                    if ($loc_ID == $location3->term_id) {
                                                                        $selected = 'selected';
                                                                    } else {
                                                                        $selected = '';
                                                                    }
                                                                    echo '<option ' . $selected . ' value="' . $location3->term_id . '">---&nbsp;&nbsp;' . $location3->name . '</option>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    </select>
                        </div>
                    </div>
                <?php
                }
            }

            if ((isset($lp_what_field_switcher) && $lp_what_field_switcher == 0) || (isset($search_loc_field_hide) && $search_loc_field_hide == 0)) {
                if (is_home() || is_front_page() || wp_is_mobile()) {

                ?>
                    <div class="form-group <?php echo esc_attr($searchHide); ?>">
                        <div class="lp-search-bar-right">
                            <input value="" class="lp-search-btn lp-search-form-submit" type="submit">
                            <i class="fa-solid fa-magnifying-glass lp-search-icon"></i>
                            <img alt="image" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/ellipsis.gif" class="searchloading">
                        </div>
                    </div>
            <?php }
            } ?>
            <input type="hidden" name="lp_s_tag" id="lpstag" value="<?php echo esc_attr($lpstag); ?>">
            <input type="hidden" name="lp_s_cat" id="lp_s_cat" value="<?php echo esc_attr($term_ID); ?>">
            <input type="hidden" name="s" value="home">
            <input type="hidden" name="post_type" value="listing">

        </form>

    </div>
    <div class="clearfix"></div>
</div>