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
$feature_ID = '';
$lpstag = '';
$locationID = '';
$catsOPT = '';
$searchfilter = '';
global $paged;
$taxTaxDisplay = true;

if( !isset($_GET['s'])){
    $queried_object = get_queried_object();
    $term_id = $queried_object->term_id;
    $taxName = $queried_object->taxonomy;
    if(!empty($term_id)){
        $termID = get_term_by('id', $term_id, $taxName);
        $termName = $termID->name;
        $parent = $termID->parent;
        $term_ID = $termID->term_id;
        if(is_tax('location')){
            $loc_ID = $termID->term_id;
        }
        elseif(is_tax('features')){
            $feature_ID = $termID->term_id;
        }

        elseif(is_tax('list-tags')){
            $lpstag = $termID->term_id;
        }


    }
}elseif(isset($_GET['lp_s_cat']) || isset($_GET['lp_s_tag']) || isset($_GET['lp_s_loc'])){

    if(isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat'])){
        $sterm = wp_kses_post($_GET['lp_s_cat']);
        $term_ID = wp_kses_post($_GET['lp_s_cat']);
        $termo = get_term_by('id', $sterm, 'listing-category');
        $termName = esc_html__('Results For','listingpro').' <strong class="term-name">'.$termo->name.'</strong>';
        $parent = $termo->parent;
    }
    if(isset($_GET['lp_s_cat']) && empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])){
        $sterm = wp_kses_post($_GET['lp_s_tag']);
        $lpstag = $sterm;
        $termo = get_term_by('id', $sterm, 'list-tags');
        $termName = esc_html__('Results For','listingpro').' <strong>'.$termo->name.'</strong>';
    }

    if(isset($_GET['lp_s_cat']) && !empty($_GET['lp_s_cat']) && isset($_GET['lp_s_tag']) && !empty($_GET['lp_s_tag'])){
        $sterm = wp_kses_post($_GET['lp_s_tag']);
        $lpstag = $sterm;

        $termo = get_term_by('id', $sterm, 'list-tags');
        $termName = esc_html__('Results For','listingpro').' <strong>'.$termo->name.'</strong>';
    }

    if(isset($_GET['lp_s_loc']) && !empty($_GET['lp_s_loc'])){
        $sloc = wp_kses_post($_GET['lp_s_loc']);
        $loc_ID = wp_kses_post($_GET['lp_s_loc']);
        if(is_numeric($sloc)){
            $sloc = $sloc;
            $termo = get_term_by('id', $sloc, 'location');
            if(!empty($termo)){
                $locName = esc_html__('In ','listingpro').$termo->name;
            }
        }
        else{
            $checkTerm = listingpro_term_exist($sloc,'location');
            if(!empty($checkTerm)){
                $locTerm = get_term_by('name', $sloc, 'location');
                if( !empty($locTerm) ){
                    $loc_ID = $locTerm->term_id;
                    $locName = esc_html__('In ', 'listingpro').'<strong class="termloc-name">'.$locTerm->name.'<strong>';
                }

            }
            else{
                $locName = esc_html__('In ', 'listingpro').'<strong class="termloc-name">'.$sloc.'</strong>';
            }
        }

    }
}

$emptySearchTitle = '';
if( empty($_GET['lp_s_tag']) && isset($_GET['lp_s_tag']) && empty($_GET['lp_s_cat']) && isset($_GET['lp_s_cat']) && empty($_GET['lp_s_loc']) && isset($_GET['lp_s_loc']) ){
    $emptySearchTitle = esc_html__('Most recent ', 'listingpro');
}

$lparchiveBGCLass = '';
$emptyFIlter = '';

$searchfilter = $listingpro_options['enable_search_filter'];
if( $searchfilter != '1' ){
	$emptyFIlter = 'empty-filter';
}


$meteKey_cat = get_term_meta($term_id, 'lp_category_banner', true );
$meteKey_loc = get_term_meta($term_id, 'lp_location_image', true );

if(isset($meteKey_cat) && !empty($meteKey_cat)){
    $lp_archive_bg= $meteKey_cat;
}elseif (isset($meteKey_loc) && !empty($meteKey_loc)) {
    $lp_archive_bg =  $meteKey_loc;
}
elseif( isset($listingpro_options['lp_archive_bg']['url']) && !empty( $listingpro_options['lp_archive_bg']['url'] ) ) {
    $lp_archive_bg = $listingpro_options['lp_archive_bg']['url'];
}else{
    $lp_archive_bg  =   get_template_directory_uri().'/assets/images/home-banner.jpg';
}

if( isset( $lp_archive_bg ) && !empty( $lp_archive_bg ) )
{
    $lparchiveBGCLass = 'colorWhite';
}
?>
<div class="lp-archive-banner <?php echo esc_attr($emptyFIlter); ?>" style="background-image: url(<?php echo esc_url($lp_archive_bg); ?>);background-size:
cover; background-position: center; ">
	<?php if(!empty($lp_archive_bg)){ ?>
    <div class="lp-header-overlay"></div>
	<?php } ?>
    <div class="lp-header-search archive-search">
        <div class="text-center lp-filter-top-text">
            <?php if(is_search()){ ?>
                <h4 class="lp-title <?php echo esc_attr($lparchiveBGCLass); ?>" data-rstring = "<?php esc_html_e( 'Results For', 'listingpro' ); ?>" data-lstring="<?php esc_html_e( 'Listings', 'listingpro' ); ?>"><?php echo wp_kses_post($termName); ?> <?php echo esc_attr($emptySearchTitle); ?><?php echo esc_html__( ' Listings', 'listingpro' );?> <?php echo wp_kses_post($locName); ?></h4>

            <?php }else{ ?>
                <h4 class="lp-title <?php echo esc_attr($lparchiveBGCLass); ?>" data-rstring = "<?php esc_html_e( 'Results For', 'listingpro' ); ?>" data-lstring="<?php esc_html_e( 'Listings', 'listingpro' ); ?>"><?php echo esc_html__( 'Results For ', 'listingpro' );?> <em><?php echo esc_attr($termName); ?></em><?php echo esc_html__( ' Listings', 'listingpro' );?> </h4>
            <?php } ?>
        </div>
		<?php
        $searchFilter = $listingpro_options['enable_cats_search_filter'];

        if( !empty( $searchfilter ) && $searchfilter == '1' ){
	?>
        <div class="container">
            <div class="row">
                <?php
				
				get_template_part( 'templates/headers/header-search' );
                ?>
            </div>
        </div>
        <?php
        get_template_part('templates/headers/filters-in-header');
        ?>
		<?php }elseif ($searchFilter == 1){
            ?>
            <div style="display: none; height: 0px; padding: 0; margin: 0;" id="searchform">
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
            </div>
            <?php
        } ?>
    </div>
</div>
