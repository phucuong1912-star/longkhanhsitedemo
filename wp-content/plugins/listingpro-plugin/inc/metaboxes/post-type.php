<?php
add_action('admin_print_scripts', 'postsettings_admin_scripts');
add_action('admin_print_styles', 'postsettings_admin_styles');
if (!function_exists('postsettings_admin_scripts')) {
    function postsettings_admin_scripts(){
        global $post,$pagenow;

        if (current_user_can('edit_posts') && ($pagenow == 'post-new.php' || $pagenow == 'post.php' )) {
			    if( isset($post) ) {
					wp_localize_script( 'jquery', 'script_data', array(
						'post_id' => $post->ID,
						'nonce' => wp_create_nonce( 'lp-ajax' ),
						'image_ids' => get_post_meta( $post->ID, 'gallery_image_ids', true ),
						'label_create' => __("Create Featured Gallery", "listingpro-plugin"),
						'label_edit' => __("Edit Featured Gallery", "listingpro-plugin"),
						'label_save' => __("Save Featured Gallery", "listingpro-plugin"),
						'label_saving' => __("Saving...", "listingpro-plugin")
					));
				}
			
	 
            wp_register_script('post-metaboxes', plugins_url( 'assets/js/metaboxes.js', dirname(dirname(__FILE__) )));
		
       
            wp_enqueue_script('post-metaboxes');
			global $listingpro_options;
			$mapAPI = '';
			$mapAPI = $listingpro_options['google_map_api'];
			if(empty($mapAPI)){
				$mapAPI = 'AIzaSyDQIbsz2wFeL42Dp9KaL4o4cJKJu4r8Tvg';
			}
			wp_enqueue_script('maps', 'https://maps.googleapis.com/maps/api/js?key='.$mapAPI.'&libraries=places', 'jquery', '', false);
			
			if (current_user_can('edit_posts') && ($pagenow == 'edit.php' && isset($_GET['page']) && $_GET['page'] == 'listing-claims')) {
				wp_enqueue_script('bootstrapadmin', get_template_directory_uri() . '/assets/lib/bootstrap/js/bootstrap.min.js', 'jquery', '', true);
			}
			if (current_user_can('edit_posts') && ($pagenow == 'post-new.php' || $pagenow == 'post.php')) {
				if ( 'listing' === $post->post_type || 'lp-reviews' === $post->post_type   || 'lp-ads' === $post->post_type || 'lp-claims' === $post->post_type || 'events' == $post->post_type ) {
					wp_enqueue_script('accorianui', plugins_url( 'assets/js/jqueryuiaccordian.js', dirname(dirname(__FILE__) )), 'jquery', '', true);
					wp_enqueue_script('jquery-ui', get_template_directory_uri() . '/assets/js/jquery-ui.js', 'jquery', '', true);
                    wp_enqueue_script('jquery-ui-trigger', get_template_directory_uri() . '/assets/js/jquery-ui-trigger.js', 'jquery', '', true);
                    wp_localize_script(	'jquery-ui-trigger','global',array('ajax' => admin_url( 'admin-ajax.php' ),));
					wp_enqueue_script('jquery-droppin', get_template_directory_uri() . '/assets/js/drop-pin.js', 'jquery', '', true);
				}
				
			}
        }
		if (current_user_can('edit_posts') && ($pagenow == 'edit-tags.php'|| $pagenow == 'term.php' )) {
			        
			 wp_register_script('select2-metaboxes', plugins_url( 'assets/js/select2.full.min.js', dirname(dirname(__FILE__) )));    		 
	 
            wp_register_script('prettify-metaboxes', plugins_url( 'assets/js/prettify.min.js', dirname(dirname(__FILE__) )));  
            wp_register_script('post-metaboxes', plugins_url( 'assets/js/metaboxes.js', dirname(dirname(__FILE__) )));
			wp_enqueue_script('select2-metaboxes');
            wp_enqueue_script('prettify-metaboxes');        
            wp_enqueue_script('post-metaboxes');
		}
    }
}

if (!function_exists('postsettings_admin_styles')) {
    function postsettings_admin_styles(){
        global $pagenow;
        if (current_user_can('edit_posts') && ($pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit-tags.php'|| $pagenow == 'term.php' )) {
            wp_register_style('post-metaboxes', plugins_url( 'assets/css/metaboxes.css', dirname(dirname(__FILE__) )), false, '1.00', 'screen');
			
            
			 wp_register_style('select2-metaboxes', plugins_url( 'assets/css/select2.css', dirname(dirname(__FILE__) )), false, '1.00', 'screen');
            wp_register_style('prettify-metaboxes', plugins_url( 'assets/css/prettify.css', dirname(dirname(__FILE__) )), false, '1.00', 'screen');
            wp_enqueue_style('post-metaboxes');
			 wp_enqueue_style('select2-metaboxes');
            wp_enqueue_style('prettify-metaboxes');
			
        }
		
		if (current_user_can('edit_posts') && ($pagenow == 'edit.php' && isset($_GET['page']) && $_GET['page'] == 'listing-claims')) {
			wp_enqueue_style('bootstrapcss', get_template_directory_uri() . '/assets/lib/bootstrap/css/bootstrap.min.css');
		}
		if (current_user_can('edit_posts') && ($pagenow == 'post-new.php' || $pagenow == 'post.php')) {
					wp_enqueue_style('jquery-ui');
				
		}
    }
}
require_once ( WP_PLUGIN_DIR.'/listingpro-plugin/inc/metaboxes/metaboxes.php');
require_once ( WP_PLUGIN_DIR.'/listingpro-plugin/inc/metaboxes/post-options.php');   

if(!function_exists('listingpro_metabox_render')) {
    function listingpro_metabox_render($post, $metabox) {
        global $post;
        $options = get_post_meta($post->ID, 'lp_'.strtolower(THEMENAME).'_options', true);
        ?>
        <input type="hidden" name="lp_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__));?>" />
        <table class="form-table lp-metaboxes">
            <tbody>
            <?php
            foreach ($metabox['args'] as $settings) {
                $settings['value'] = isset($options[$settings['id']]) ? $options[$settings['id']] : (isset($settings['std']) ? $settings['std'] : '');
                call_user_func('settings_'.$settings['type'], $settings);
            }
            ?>
            </tbody>
        </table>
        <?php
    }
}

if(!function_exists('reviews_metabox_render')) {
    function reviews_metabox_render($post, $metabox) {
        global $post, $listingpro_options;
        $options = get_post_meta($post->ID, 'lp_'.strtolower(THEMENAME).'_options', true);?>
        <?php

        $lp_multi_rating_state    	=   $listingpro_options['lp_multirating_switch'];
        if( $lp_multi_rating_state == 1 && !empty( $lp_multi_rating_state ) ) {
            $unsetKey = '';
            foreach ($metabox['args'] as $arrkey => $mybox) {
                if ($mybox['id'] == 'rating' && $mybox['name'] == 'Rating') {
                    $unsetKey = $arrkey;
                }
            }
            unset($metabox['args'][$unsetKey]);
        }
        ?>

        <input type="hidden" name="lp_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__));?>" />
        <table class="form-table lp-metaboxes">
            <tbody>
            <?php
            foreach ($metabox['args'] as $settings) {
                $settings['value'] = isset($options[$settings['id']]) ? $options[$settings['id']] : (isset($settings['std']) ? $settings['std'] : '');
                call_user_func('settings_'.$settings['type'], $settings);
            }
            ?>
            </tbody>
        </table>
        <?php
    }
}

if(!function_exists('claims_metabox_render')) {
    function claims_metabox_render($post, $metabox) {
        global $post;
        $options = get_post_meta($post->ID, 'lp_'.strtolower(THEMENAME).'_options', true);
        ?>
        <input type="hidden" name="lp_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__));?>" />
        <table class="form-table lp-metaboxes">
            <tbody>

            <?php
            foreach ($metabox['args'] as $settings) {
                $settings['value'] = isset($options[$settings['id']]) ? $options[$settings['id']] : (isset($settings['std']) ? $settings['std'] : '');
                call_user_func('settings_'.$settings['type'], $settings);
            }
            ?>
            </tbody>
        </table>
        <?php
    }
}

/* ads_metabox_render */

if(!function_exists('ads_metabox_render')){
    function ads_metabox_render($post, $metabox) {
        global $post;
        $options = get_post_meta($post->ID, 'lp_'.strtolower(THEMENAME).'_options', true);?>
        <input type="hidden" name="lp_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__));?>" />
        <table class="form-table lp-metaboxes">
            <tbody>
            <?php
            foreach ($metabox['args'] as $settings) {
                $settings['value'] = isset($options[$settings['id']]) ? $options[$settings['id']] : (isset($settings['std']) ? $settings['std'] : '');
                call_user_func('settings_'.$settings['type'], $settings);
            }
            ?>
            </tbody>
        </table>
        <?php
    }
}

/* ads_metabox_render */
/* pages_metabox_render */
if(!function_exists('lp_pages_metabox_render')) {
    function lp_pages_metabox_render($post, $metabox) {
        global $post;
        $options = get_post_meta($post->ID, 'lp_'.strtolower(THEMENAME).'_options', true);?>
        <input type="hidden" name="lp_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__));?>" />
        <table class="form-table lp-metaboxes">
            <tbody>
            <?php
            foreach ($metabox['args'] as $settings) {
                $settings['value'] = isset($options[$settings['id']]) ? $options[$settings['id']] : (isset($settings['std']) ? $settings['std'] : '');
                call_user_func('settings_'.$settings['type'], $settings);
            }
            ?>
            </tbody>
        </table>
        <?php
    }
}

/* pages_metabox_render */

/* posts_metabox_render */

if(!function_exists('lp_post_metabox_render')) {
    function lp_post_metabox_render($post, $metabox) {
        global $post;
        $options = get_post_meta($post->ID, 'lp_'.strtolower(THEMENAME).'_options', true);?>
        <input type="hidden" name="lp_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__));?>" />
        <table class="form-table lp-metaboxes">
            <tbody>
            <?php
            foreach ($metabox['args'] as $settings) {
                $settings['value'] = isset($options[$settings['id']]) ? $options[$settings['id']] : (isset($settings['std']) ? $settings['std'] : '');
                call_user_func('settings_'.$settings['type'], $settings);
            }
            ?>
            </tbody>
        </table>
        <?php
    }
}

/* posts_metabox_render */

/*price plan metabox render starts*/
if(!function_exists('lp_price_plans_metabox_render')) {
    function lp_price_plans_metabox_render($post, $metabox) {
        global $post;
        $options = get_post_meta($post->ID, 'lp_'.strtolower(THEMENAME).'_options', true);?>
        <input type="hidden" name="lp_meta_box_nonce" value="<?php echo wp_create_nonce(basename(__FILE__));?>" />
        <table class="form-table lp-metaboxes">
            <tbody>
            <?php
            foreach ($metabox['args'] as $settings) {
                $settings['value'] = isset($options[$settings['id']]) ? $options[$settings['id']] : (isset($settings['std']) ? $settings['std'] : '');
                call_user_func('settings_'.$settings['type'], $settings);
            }
            ?>
            </tbody>
        </table>
        <?php
    }
}

/*price plan metabox render ends*/

add_action('save_post', 'savePostMeta');
if(!function_exists('savePostMeta')){
	function savePostMeta($post_id) {
		global $listingpro_settings, $reviews_options, $listingpro_formFields, $claim_options, $ads_options, $page_options, $post_options, $price_plans_options;

		$meta = 'lp_'.strtolower(THEMENAME).'_options';
		
		// verify nonce
		if (!isset($_POST['lp_meta_box_nonce']) || !wp_verify_nonce($_POST['lp_meta_box_nonce'], basename(__FILE__))) {
				return $post_id;
		}
		
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}
		// check permissions
		if ('page' == $_POST['post_type']) {
				if (!current_user_can('edit_page', $post_id)) {
						return $post_id;
				}
		} elseif (!current_user_can('edit_post', $post_id)) {
				return $post_id;
		}
		
		if($_POST['post_type']=='lp-reviews'){
			$metaboxes_reviews = $reviews_options;
		}
	   
		if($_POST['post_type']=='lp-ads'){
			$metaboxes = $ads_options;
		}
		
		if($_POST['post_type']=='listing'){
			$metaboxes = $listingpro_settings;
		}
		
		if($_POST['post_type']=='form-fields'){
			$metaboxes = $listingpro_formFields;
		}
		if($_POST['post_type']=='lp-claims'){
			$metaboxes = $claim_options;
		}
		if($_POST['post_type']=='page'){
			$metaboxes = $page_options;
		}
		if($_POST['post_type']=='post'){
			$metaboxes = $post_options;
		}
		if($_POST['post_type']=='price_plan'){
			$metaboxes = $price_plans_options;
		}
		if(!empty($metaboxes_reviews)) {
			$myMeta = array();

			foreach ($metaboxes_reviews as $metabox) {
				$myMeta[$metabox['id']] = isset($_POST[$metabox['id']]) ? $_POST[$metabox['id']] : "";
			}

			update_post_meta($post_id, $meta, $myMeta);        

		}
		
		if(!empty($metaboxes)) {
			$myMeta = array();

			foreach ($metaboxes as $metabox) {
				$myMeta[$metabox['id']] = isset($_POST[$metabox['id']]) ? $_POST[$metabox['id']] : "";
			}

			update_post_meta($post_id, $meta, $myMeta);        
			if(isset($_POST['lp_form_fields_inn'])){
				$metaFields = 'lp_'.strtolower(THEMENAME).'_options_fields';
				$fields = $_POST['lp_form_fields_inn'];
				$filterArray = lp_save_extra_fields_in_listing($fields, $post_id);
				$fields = array_merge($fields,$filterArray);
				
				update_post_meta($post_id, $metaFields, $fields);
			}else{
				$metaFields = 'lp_'.strtolower(THEMENAME).'_options_fields';
				update_post_meta($post_id, $metaFields, '');
			}        
		}
	}
}

/* ================================================================================== */
/*      Save gallery images
/* ================================================================================== */

if(!function_exists('lp_save_images')) {
    function lp_save_images() {

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        if ( !isset($_POST['ids']) || !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'lp-ajax' ) )
            return;

        if ( !current_user_can( 'edit_posts' ) ) return;

        $ids = strip_tags(rtrim($_POST['ids'], ','));
        update_post_meta($_POST['post_id'], 'gallery_image_ids', $ids);

        // update thumbs
        $thumbs = explode(',', $ids);
        $gallery_thumbs = '';
        foreach( $thumbs as $thumb ) {
            $gallery_thumbs .= '<li>' . wp_get_attachment_image( $thumb, array(32,32) ) . '</li>';
        }

        print $gallery_thumbs;

        die();
    }
}
add_action('wp_ajax_lp_save_images', 'lp_save_images');
?>