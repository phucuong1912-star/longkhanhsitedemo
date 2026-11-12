<?php
if(!function_exists('lp_listing_custom_post_r_status')){
	function lp_listing_custom_post_r_status(){
		 register_post_status( 'reported', array(
			  'label'                     => _x( 'Reported', 'listingpro-plugin' ),
			  'public'                    => true,
			  'show_in_admin_all_list'    => false,
			  'show_in_admin_status_list' => true,
			  'label_count'               => _n_noop( 'Reported <span class="count">(%s)</span>', 'Reported <span class="count">(%s)</span>' )
		 ) );
	}
}
add_action( 'init', 'lp_listing_custom_post_r_status' );

/* ========================================================================================================= */
if(!function_exists('lp_append_post_status_reported_list')){
	function lp_append_post_status_reported_list(){
		 global $post;
		 if(!empty($post)){
			 $complete = '';
			 $label = '';
			 if($post->post_type == 'listing' || $post->post_type == 'lp-reviews'){
				  if($post->post_status == 'reported'){
					   $complete = ' selected="selected"';
					   $label = '<span id="post-status-display"> Reported</span>';
				  }
				  ?>
				  <script>
				  jQuery(document).ready(function($){
					   var misLable = '<?php echo $label; ?>';
					   jQuery("select#post_status").append('<option value="reported" <?php echo $complete; ?>>Reported</option>');
					   jQuery(".misc-pub-section label").append(misLable);
				  });
				  </script>
				  <?php
			}
		}
	}
}

add_action('admin_footer-post.php', 'lp_append_post_status_reported_list');
add_action('admin_footer-edit.php', 'lp_append_post_status_reported_list');
add_action('admin_footer-post-new.php', 'lp_append_post_status_reported_list');

/* ========================================================================================================== */
if(!function_exists('lp_display_archive_report_state')){
	function lp_display_archive_report_state( $states ) {
		 global $post;
		 $arg = get_query_var( 'post_status' );
		 if($arg != 'reported'){
		     if (is_object($post)){
                 if($post->post_status == 'reported'){
                     return array('Reported');
                 }
             }
		 }
		return $states;
	}
}
add_filter( 'display_post_states', 'lp_display_archive_report_state' );