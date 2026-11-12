<?php
if(!function_exists('lp_custom_post_status')){
	function lp_custom_post_status(){
		 register_post_status( 'expired', array(
			  'label'                     => _x( 'Expired', 'listingpro-plugin' ),
			  'public'                    => true,
			  'show_in_admin_all_list'    => true,
			  'show_in_admin_status_list' => true,
			  'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>' )
		 ) );
	}
}
add_action( 'init', 'lp_custom_post_status' );

/* ========================================================================================================= */
if(!function_exists('lp_append_post_status_list')){
	function lp_append_post_status_list(){
		 global $post;
		 if(!empty($post)){
			 $complete = '';
			 $label = '';
			 if($post->post_type == 'listing'){
				  if($post->post_status == 'expired'){
					   $complete = ' selected="selected"';
					   $label = '<span id="post-status-display"> Expired</span>';
				  }
				  ?>
				  <script>
				  jQuery(document).ready(function($){
					   var misLable = '<?php echo $label; ?>';
					   jQuery("select#post_status").append('<option value="expired" <?php echo $complete; ?>>Expired</option>');
					   jQuery(".misc-pub-section label").append(misLable);
				  });
				  </script>
				  <?php
			}
		}
	}
}

add_action('admin_footer-post.php', 'lp_append_post_status_list');
add_action('admin_footer-edit.php', 'lp_append_post_status_list');
add_action('admin_footer-post-new.php', 'lp_append_post_status_list');

/* ========================================================================================================== */
if(!function_exists('lp_display_archive_state')){
	function lp_display_archive_state( $states ) {
		 global $post;
		 $arg = get_query_var( 'post_status' );
		 if($arg != 'expired'){
             if (is_object($post)){
                 if($post->post_status == 'expired'){
                     return array('Expired');
                 }
             }
		 }
		return $states;
	}
}
add_filter( 'display_post_states', 'lp_display_archive_state' );