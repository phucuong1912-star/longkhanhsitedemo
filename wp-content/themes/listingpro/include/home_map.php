<?php
/* ============== ListingPro Get Home map content ============ */
/* ============== ListingPro get child term (tags) in search ============ */

if (!function_exists('listingpro_home_map')) {

    function listingpro_home_map(){
        wp_register_script('listingpro_home_map', get_template_directory_uri() . '/assets/js/home-map.js', array('jquery') );
        wp_enqueue_script('listingpro_home_map');

        wp_localize_script( 'listingpro_home_map', 'listingpro_home_map_object', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ));


    }
    if(!is_admin()){
        if(!is_front_page()){
            add_action('init', 'listingpro_home_map');
        }
    }
}

if (!function_exists('listingpro_home_map_content')) {


    add_action('wp_ajax_listingpro_home_map_content',        'listingpro_home_map_content');
    add_action('wp_ajax_nopriv_listingpro_home_map_content', 'listingpro_home_map_content');
    function listingpro_home_map_content() {
        global $listingpro_options;

        $defaultImg = $listingpro_options['lp_def_featured_image']['url'];

        $output = null;
        $listingby = trim($_POST['listingby']);
        $listingbycat = lp_theme_option('lp_listing_cat_on_map');
        $lpcity = trim($_POST['lpcity']);
        $taxQuery = array();
        switch($listingby){
            case 'by_category':
                $taxQuery = array(
                    array(
                        'taxonomy' => 'listing-category',
                        'field'    => 'term_id',
                        'terms'    => $listingbycat,
                    )
                );
                break;


            case 'geolocaion':
                $taxQuery = array(
                    array(
                        'taxonomy' => 'location',
                        'field'    => 'name',
                        'terms'    => $lpcity,
                    )
                );
                break;
        }

        $final;
        $lat;
        $long;
        $action = $_POST['trig'];
        if($action == 'home_map'){
            $type = 'listing';
            $args=array(
                'post_type' => $type,
                'post_status' => 'publish',
                'tax_query' =>$taxQuery,
                'posts_per_page' => -1,
            );
            $my_query = new WP_Query($args);
            if( $my_query->have_posts() ) {
                while ($my_query->have_posts()) : $my_query->the_post();
                    if ( has_post_thumbnail()) {
                        $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'listingpro-blog-grid' );
                        if(!empty($image[0])){
                            $image = "<a href='".get_the_permalink()."' >
										<img alt='image' src='" . $image[0] . "' />
									</a>";
                        }

                    }elseif(!empty($defaultImg)){
                        $image = "<a href='".get_the_permalink()."' >
                                        <img alt='image' src='" . $defaultImg . "' />
                                    </a>";
                    }else {
                        $image = '<img src="'.esc_html__('https://placehold.co/372x284', 'listingpro').'" alt="image">';
                    }


                    $ids = get_the_ID();
                    $cats = get_the_terms( get_the_ID(), 'listing-category' );
                    foreach ( $cats as $cat ) {
                        $category_image = listing_get_tax_meta($cat->term_id,'category','image');
                    }
                    $gAddress = listing_get_metabox_by_ID('gAddress', get_the_ID());
                    $url = get_the_permalink();
                    $lat = listing_get_metabox_by_ID('latitude', get_the_ID());
                    $long = listing_get_metabox_by_ID('longitude', get_the_ID());
                    $output[$ids] = array("latitude"=>$lat,"longitude"=>$long,"title"=>get_the_title(),"icon"=>$category_image,"address"=>$gAddress,"url"=>$url,"image"=>$image);
                endwhile;

            }else{
                $lat = lp_theme_option('lp_default_map_location_lat');
                $long= lp_theme_option('lp_default_map_location_long');

                $output[] = array("noresult" => true, "latitude"=>$lat,"longitude"=>$long,"title"=>'',"icon"=>'',"address"=>'',"url"=>'',"image"=>'');
            }
        }
        $final = json_encode($output);
        die($final);
    }

}