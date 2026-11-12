<?php
global $listingpro_options;
$listing_style = $listingpro_options['listing_style'];
$sQuery = '';
$sLocation = '';
$sLocationName = '';
$lp_tag = '';
$lp_cat = '';
$sLoc = '';
$sLocName = '';
$defaultCats = null;

$listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];
if ((isset($_GET['select']) || isset($_GET['lp_s_loc']) || is_archive()) && $listing_style == 4 && (!wp_is_mobile() && ($listing_mobile_view == 'app_view' || $listing_mobile_view == 'app_view2'))) {
    return;
}

if (isset($_GET['select']) && !empty($_GET['select'])) {

    $sQuery = sanitize_text_field($_GET['select']);
}

if (isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])) {

    $sLocation = sanitize_text_field($_GET['lp_s_loc']);

    if (is_numeric($sLocation)) {

        $locTerm = get_term_by('id', $sLocation, 'location');
        if (!empty($locTerm)) {
            $sLoc = $locTerm->name;
        }
    } else {

        $checkTerm = listingpro_term_exist($sLocation, 'location');

        if ($checkTerm == true) {

            $locTerm = get_term_by('name', $sLocation, 'location');
            if (!empty($locTerm)) {
                $sLocationName = $locTerm->name;
                $sLocation = $locTerm->name;
                $sLoc = $sLocation ? $sLocationName : esc_html__('City...', 'listingpro');
            }
        } else {

            $sLoc = $sLocation;
        }
    }
}

if (is_tax('location')) {
    $queried_object = get_queried_object();
    $sLocation = $queried_object->name;
    $sLoc = $queried_object->term_id;
    $sLocName = $queried_object->name;
?>
    <input id="lp_search_loc" type="hidden" autocomplete="off" name="lp_s_loc" value="<?php echo esc_html($sLocName); ?>"><?php
                                                                                                                        } elseif (is_tax('listing-category')) {
                                                                                                                            $queried_object = get_queried_object();
                                                                                                                            $sQuery = $queried_object->name;
                                                                                                                            $lp_cat = $queried_object->term_id;
                                                                                                                        }


                                                                                                                        if (!empty($_GET['lp_s_tag']) && isset($_GET['lp_s_tag'])) {

                                                                                                                            $lp_tag = sanitize_text_field($_GET['lp_s_tag']);
                                                                                                                        }

                                                                                                                        if (!empty($_GET['lp_s_cat']) && isset($_GET['lp_s_cat'])) {

                                                                                                                            $lp_cat = sanitize_text_field($_GET['lp_s_cat']);
                                                                                                                        }

                                                                                                                        global $listingpro_options;

                                                                                                                        $search_placeholder = $listingpro_options['search_placeholder'];

                                                                                                                        $location_default_text = $listingpro_options['location_default_text'];

                                                                                                                        $inner_srch_loc_switchr = $listingpro_options['inner_search_loc_switcher'];

                                                                                                                        $hideWhereClass = '';

                                                                                                                        $search_loc_field_hide = $listingpro_options['lp_location_loc_switcher'];

                                                                                                                        if (isset($search_loc_field_hide) && $search_loc_field_hide == 1) {

                                                                                                                            $hideWhereClass = "hide-where";

                                                                                                                            $searchHide = "hide-search";
                                                                                                                        } else {

                                                                                                                            $searchHide = '';
                                                                                                                        }

                                                                                                                        $lp_what_field_switcher = $listingpro_options['lp_what_field_switcher'];
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

                                                                                                                        if ($inner_srch_loc_switchr == true) {

                                                                                                                            $srchBr = 'ui-widget';

                                                                                                                            $slct = 'select2';
                                                                                                                        } else {

                                                                                                                            $srchBr = 'ui-widget border-dropdown';

                                                                                                                            $slct = 'chosen-select chosen-select5';
                                                                                                                        }

                                                                                                                        $locations_search_type = $listingpro_options['lp_listing_search_locations_type'];
                                                                                                                        $locArea = '';
                                                                                                                        if (!empty($locations_search_type) && $locations_search_type == "auto_loc") {
                                                                                                                            $locArea = $listingpro_options['lp_listing_search_locations_range'];
                                                                                                                        }

                                                                                                                            ?>

<div class="header-filter pos-relative form-group margin-bottom-0 col-md-6 <?php echo esc_attr($searchHide); ?>">

    <form autocomplete="off" class="form-inline top-search-form" action="<?php echo home_url(); ?>" method="get"
        accept-charset="UTF-8">

        <?php
        if (isset($lp_what_field_switcher) && $lp_what_field_switcher == 0) {
        ?>

            <div class="search-form-field input-group width-49-percent margin-right-15 <?php echo esc_attr($hideWhereClass); ?>">

                <div class="input-group-addon lp-border"><?php esc_html_e('What', 'listingpro'); ?></div>

                <div class="pos-relative">

                    <div class="what-placeholder pos-relative" data-holder="">

                        <input autocomplete="off" type="text"
                            class="lp-suggested-search js-typeahead-input lp-search-input form-control ui-autocomplete-input dropdown_fields"
                            name="select" id="select" placeholder="<?php echo esc_attr($search_placeholder); ?>"
                            value="<?php echo esc_attr($sQuery); ?>" data-prev-value='0'
                            data-noresult="<?php echo esc_html__('More results for', 'listingpro'); ?>">

                        <i class="cross-search-q fa fa-times-circle" aria-hidden="true"></i>

                        <img class='loadinerSearch' width="100px"
                            alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/search-load.gif' ?>" />

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
                                            if (hasFontAwesomeIconClass($catIcon)) {
                                                $catIcon = '<i class="icon icons-search-cat ' . $catIcon . '"></i>';
                                            } else {
                                                $catIcon = '<img alt="image" src="' . $catIcon . '" />';
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
                                        if (hasFontAwesomeIconClass($catIcon)) {
                                            $catIcon = '<i class="icon icons-search-cat ' . $catIcon . '"></i>';
                                        } else {
                                            $catIcon = '<img class="d-icon" alt="image" src="' . $catIcon . '" />';
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

            </div>
        <?php
        }
        ?>
        <?php

        if (isset($search_loc_field_hide) && $search_loc_field_hide == 0) {
            if (!empty($locations_search_type) && $locations_search_type == "auto_loc") {
        ?>

                <div class="input-group width-49-percent <?php echo esc_attr($hideWhatClass); ?>">

                    <div class="input-group-addon lp-border"><?php esc_html_e('Where', 'listingpro'); ?></div>

                    <div class="<?php echo esc_attr($srchBr); ?>">

                        <input autocomplete="off" id="cities" class="form-control"
                            data-country="<?php echo esc_attr($locArea); ?>"
                            value="<?php echo esc_attr($sLocation); ?>"
                            placeholder="<?php echo esc_attr($location_default_text); ?>">
                        <input type="hidden" autocomplete="off" id="lp_search_loc" name="lp_s_loc"
                            value="<?php if (isset($_GET['lp_s_loc'])) : echo esc_attr($_GET['lp_s_loc']);
                                    endif; ?>">
                    </div>

                </div>
            <?php
            } elseif (!empty($locations_search_type) && $locations_search_type == "manual_loc") {
            ?>

                <div class="input-group width-49-percent <?php echo esc_attr($hideWhatClass); ?>">
                    <div class="input-group-addon lp-border"><?php esc_html_e('Where', 'listingpro'); ?></div>
                    <div class="<?php echo esc_attr($srchBr); ?>">
                        <select class="<?php echo esc_attr($slct); ?>" name="lp_s_loc" id="searchlocation">
                            <option id="def_location" value=""><?php echo esc_attr($location_default_text); ?></option>
                            <?php

                            $args = array(

                                'post_type' => 'listing',

                                'order' => 'ASC',

                                'hide_empty' => false,

                                'parent' => 0,

                            );

                            $locations = get_terms('location', $args);

                            foreach ($locations as $location) {

                                if ($sLocation == $location->term_id) {

                                    $selected = 'selected';
                                } else {

                                    $selected = '';
                                }

                                echo '<option ' . $selected . ' value="' . $location->term_id . '">' . $location->name . '</option>';

                                $argsChild = array(
                                    'order' => 'ASC',
                                    'hide_empty' => false,
                                    'hierarchical' => false,
                                    'parent' => $location->term_id,


                                );
                                $childLocs = get_terms('location', $argsChild);
                                if (!empty($childLocs)) {
                                    foreach ($childLocs as $childLoc) {

                                        if ($sLocation == $childLoc->term_id) {

                                            $selected = 'selected';
                                        } else {

                                            $selected = '';
                                        }

                                        echo '<option ' . $selected . ' value="' . $childLoc->term_id . '">-&nbsp;' . $childLoc->name . '</option>';

                                        $argsChildof = array(
                                            'order' => 'ASC',
                                            'hide_empty' => false,
                                            'hierarchical' => false,
                                            'parent' => $childLoc->term_id,
                                        );
                                        $childLocsof = get_terms('location', $argsChildof);
                                        if (!empty($childLocsof)) {
                                            foreach ($childLocsof as $childLocof) {

                                                if ($sLocation == $childLocof->term_id) {

                                                    $selected = 'selected';
                                                } else {

                                                    $selected = '';
                                                }

                                                echo '<option ' . $selected . ' value="' . $childLocof->term_id . '">--&nbsp;' . $childLocof->name . '</option>';
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
            ?>

        <?php

        }
        if ((isset($lp_what_field_switcher) && $lp_what_field_switcher == 0) || (isset($search_loc_field_hide) && $search_loc_field_hide == 0)) {
        ?>

            <div class="lp-search-btn-header pos-relative">

                <input value="" class="lp-search-btn lp-search-icon" type="submit">
                <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
                <img alt="image" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/ellipsis.gif"
                    class="searchloading loader-inner-header">

            </div>
        <?php
        }
        ?>

        <input type="hidden" name="lp_s_tag" id="lp_s_tag" value="<?php echo esc_attr($lp_tag); ?>">

        <input type="hidden" name="lp_s_cat" id="lp_s_cat" value="<?php echo esc_attr($lp_cat); ?>">

        <input type="hidden" name="s" value="home">

        <input type="hidden" name="post_type" value="listing">

    </form>

</div>