<?php 
add_action( 'init', 'create_post_type_ads' );

if(!function_exists('create_post_type_ads')) {
    function create_post_type_ads() {
        $labels = array(
            'name'               => _x( 'Ads', 'post type general name', 'listingpro' ),
            'singular_name'      => _x( 'Ad', 'post type singular name', 'listingpro' ),
            'menu_name'          => _x( 'Ads', 'admin menu', 'listingpro' ),
            'name_admin_bar'     => _x( 'Ad', 'add new on admin bar', 'listingpro' ),
            'add_new'            => _x( 'Add New', 'ad', 'listingpro' ),
            'add_new_item'       => __( 'Add New Ad', 'listingpro' ),
            'new_item'           => __( 'New Ad', 'listingpro' ),
            'edit_item'          => __( 'Edit Ad', 'listingpro' ),
            'view_item'          => __( 'View Ad', 'listingpro' ),
            'all_items'          => __( 'All Ads', 'listingpro' ),
            'search_items'       => __( 'Search Ads', 'listingpro' ),
            'parent_item_colon'  => __( 'Parent Ads:', 'listingpro' ),
            'not_found'          => __( 'No ads found.', 'listingpro' ),
            'not_found_in_trash' => __( 'No ads found in Trash.', 'listingpro' )
        );

        $args = array(
            'labels'             => $labels,
            'menu_icon'          => 'dashicons-media-spreadsheet',
            'description'        => __( 'Description.', 'listingpro' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'lp-ads' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => true,
            'menu_position'      => 29,
            'supports'           => array( 'title')
        );

        register_post_type( 'lp-ads', $args );
    }
}


/* ================registering inactive ad status================= */
if(!function_exists('lp_ad_inactive_post_status')){
	function lp_ad_inactive_post_status(){
		register_post_status( 'Inactive', array(
			'label'                     => _x( 'Inactive', 'lp-ads' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'post_type'                 => array( 'lp-ads' ),
			'label_count'               => _n_noop( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>' ),
		) );
	}
	add_action( 'init', 'lp_ad_inactive_post_status' );
}
//
if(!function_exists('lp_append_ad_status_in_list')){
	function lp_append_ad_status_in_list(){
		 global $post;
		 if(!empty($post)){
			 $complete = '';
			 $label = '';
			 if($post->post_type == 'lp-ads'){
				  if($post->post_status == 'inactive'){
					   $complete = ' selected="selected"';
					   $label = '<span id="post-status-display"> Inactive</span>';
				  }
				  ?>
				  <script>
				  jQuery(document).ready(function($){
					   var misLable = '<?php echo $label; ?>';
					   jQuery("select#post_status").append('<option value="inactive" <?php echo $complete; ?>>Inactive</option>');
					   jQuery(".misc-pub-section label").append(misLable);
				  });
				  </script>
				  <?php
			}
		}
	}
}

add_action('admin_footer-post.php', 'lp_append_ad_status_in_list');
add_action('admin_footer-edit.php', 'lp_append_ad_status_in_list');
add_action('admin_footer-post-new.php', 'lp_append_ad_status_in_list');

//
if(!function_exists('lp_display_archive_state_ads')){
	function lp_display_archive_state_ads( $states ) {
		 global $post;
		 if(isset($post) && $post->post_type == 'lp-ads'){
			 $arg = get_query_var( 'post_status' );
			 if($arg == 'inactive'){
				  if($post->post_status == 'inactive'){
					   return array('Inactive');
				  }
			 }
		 }
        return $states;
	}
}
add_filter( 'display_post_states', 'lp_display_archive_state_ads' );