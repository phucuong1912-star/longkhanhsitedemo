<?php 
add_action( 'init', 'create_post_type_reviews' );

if(!function_exists('create_post_type_reviews')) {
    function create_post_type_reviews() {
        $labels = array(
            'name'               => _x( 'Reviews', 'post type general name', 'listingpro' ),
            'singular_name'      => _x( 'Review', 'post type singular name', 'listingpro' ),
            'menu_name'          => _x( 'Reviews', 'admin menu', 'listingpro' ),
            'name_admin_bar'     => _x( 'Review', 'add new on admin bar', 'listingpro' ),
            'add_new'            => _x( 'Add New', 'review', 'listingpro' ),
            'add_new_item'       => __( 'Add New Review', 'listingpro' ),
            'new_item'           => __( 'New Review', 'listingpro' ),
            'edit_item'          => __( 'Edit Review', 'listingpro' ),
            'view_item'          => __( 'View Review', 'listingpro' ),
            'all_items'          => __( 'All Reviews', 'listingpro' ),
            'search_items'       => __( 'Search Reviews', 'listingpro' ),
            'parent_item_colon'  => __( 'Parent Reviews:', 'listingpro' ),
            'not_found'          => __( 'No reviews found.', 'listingpro' ),
            'not_found_in_trash' => __( 'No reviews found in Trash.', 'listingpro' )
        );

        $args = array(
            'labels'             => $labels,
            'menu_icon'          => 'dashicons-testimonial',
            'description'        => __( 'Description.', 'listingpro' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'lp-reviews' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => true,
            'menu_position'      => 29,
            'supports'           => array( 'title', 'editor')
        );

        register_post_type( 'lp-reviews', $args );
    }
}


	 