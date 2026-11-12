<?php
/**
 * Listingpro Functions.
 *
 */
	/* ============== ListingPro Add to favorite ============ */
	
	add_action('wp_ajax_listingpro_add_favorite',        'listingpro_add_favorite');
	add_action('wp_ajax_nopriv_listingpro_add_favorite', 'listingpro_add_favorite');
	
	if(!function_exists('listingpro_add_favorite')){
		function listingpro_add_favorite(){
            check_ajax_referer( 'lp_ajax_nonce', 'lpNonce' );
            // Nonce is checked, get the POST data and sign user on
            if( !wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
                $res = json_encode(array('nonceerror'=>'yes'));
                die($res);
            }

            // Load current favourite posts from cookie
			$favposts = (isset($_COOKIE['newco'])) ? explode(',', (string) $_COOKIE['newco']) : array();
			$favposts = array_map('absint', $favposts); // Clean cookie input, it's user input!

			// Add (or remove) favourite post IDs
			$favposts[] = $_POST['post-id'];
			$type = $_POST['type'];
			 
			
			if(is_user_logged_in()){
				$uid = get_current_user_id();
				$savedListing = get_user_meta($uid, 'lp_saved_user_posts', true);
				if(!empty($savedListing)){
					$savedListing = get_user_meta($uid, 'lp_saved_user_posts', true);
				}else{
					$savedListing = array();
				}
				$savedListing[]=$_POST['post-id'];
				update_user_meta($uid, 'lp_saved_user_posts', array_unique($savedListing));
			}else{
				$time_to_live = 3600 * 24 * 30; // 30 days
				setcookie('newco', implode(',', array_unique($favposts)), time() + $time_to_live ,"/");
			}
			
			$done = json_encode(array("type"=>$type,"active"=>'yes',"id"=>$favposts));
			die($done);
					
		}
	}
	
	
	/* ============== ListingPro Remove from favorite ============ */
	
	add_action('wp_ajax_listingpro_remove_favorite',        'listingpro_remove_favorite');
	add_action('wp_ajax_nopriv_listingpro_remove_favorite', 'listingpro_remove_favorite');
	
	if(!function_exists('listingpro_remove_favorite')){
		function listingpro_remove_favorite(){
            check_ajax_referer( 'lp_ajax_nonce', 'lpNonce' );
            // Nonce is checked, get the POST data and sign user on
            if( !wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
                $res = json_encode(array('nonceerror'=>'yes'));
                die($res);
            }
			// Load current favourite posts from cookie
			$favpostsd = $_POST['post-id'];
			$favposts = (isset($_COOKIE['newco'])) ? explode(',', (string) $_COOKIE['newco']) : array();
			$favposts = array_map('absint', $favposts); // Clean cookie input, it's user input!
			
			if(is_user_logged_in()){
				$uid = get_current_user_id();
				$savedinMeta = get_user_meta($uid, 'lp_saved_user_posts', true);
				if(!empty($savedinMeta)){
					foreach($savedinMeta as $index => $value){

						if($value == $favpostsd)

						{

							unset($savedinMeta[$index]);

						}

					}
				}
				update_user_meta($uid, 'lp_saved_user_posts', $savedinMeta);
			}

			// Add (or remove) favourite post IDs
			else{		
				foreach($favposts as $index => $value)

				{

					if($value == $favpostsd)

					{

						unset($favposts[$index]);

					}

				}
				 
				$time_to_live = 3600 * 24 * 30; // 30 days
				setcookie('newco', implode(',', array_unique($favposts)), time() + $time_to_live ,"/");
			}
			
			$done = json_encode(array("remove"=>'yes',"id"=>$favposts, 'remove_text' =>esc_html__('Save', 'listingpro')));
			die($done);
					
		}
	}

	
	add_action('init', 'listingpro_fav_ids');

	if(!function_exists('listingpro_fav_ids')){
		function listingpro_fav_ids(){
		 $favposts = (isset($_COOKIE['newco'])) ? explode(',', (string) $_COOKIE['newco']) : array();
		 $favposts = array_map('absint', $favposts); // Clean cookie input, it's user input!
		 return $favposts;
		}
	}
	
	
	/* ============== is favourite DETAIL ============ */
	
	if (!function_exists('listingpro_is_favourite')) {
		
		function listingpro_is_favourite($postid,$onlyicon=true){
			$favposts = (isset($_COOKIE['newco'])) ? explode(',', (string) $_COOKIE['newco']) : array();
			$favposts = array_map('absint', $favposts); // Clean cookie input, it's user input!
			
			if($onlyicon == true){
				if (in_array($postid,$favposts )) {
					return 'fa-bookmark';
				}else{
					return 'fa-bookmark-o';
				}
			}else{
				global $listingpro_options;	
				if (in_array($postid,$favposts )) {
					echo esc_html__('Saved', 'listingpro');
				}else{
					echo esc_html__('Save', 'listingpro');
				}
			}
		
		}
		
	}
	
	
	/* ============== is favourite GRID ============ */

if (!function_exists('listingpro_is_favourite_grids')) {

    function listingpro_is_favourite_grids($postid,$onlyicon=true){
        if(is_user_logged_in()){
            $uid = get_current_user_id();
            $favposts = get_user_meta($uid, 'lp_saved_user_posts', true);
            if( !is_array( $favposts ) )
            {
                $favposts   =   (array) $favposts;
            }
        }else{
            $favposts = (isset($_COOKIE['newco'])) ? explode(',', (string) $_COOKIE['newco']) : array();
            $favposts = array_map('absint', $favposts); // Clean cookie input, it's user input!
        }
        if($onlyicon == true){
            if (in_array($postid,$favposts )) {
                return 'fa fa-bookmark';
            }else{
                return 'fa-regular fa-bookmark-o';
            }
        }else{
            if (in_array($postid,$favposts )) {
                return esc_html__('Saved', 'listingpro');
            }else{
                return esc_html__('Save', 'listingpro');
            }
        }

    }

}
	
	/* ============ fav function to get fav ====================== */
	if(!function_exists('getSaved')){
		function getSaved(){
			$favPPosts = array();
			if(is_user_logged_in()){
				$uid = get_current_user_id();
				$favposts = get_user_meta($uid, 'lp_saved_user_posts', true);
				if(!empty($favposts)){
					foreach($favposts as $spost){
						if ( FALSE === get_post_status( $spost ) ) {
						}else{
							$favPPosts[] = $spost;
						}
					}
					update_user_meta($uid, 'lp_saved_user_posts', $favPPosts);
					$favposts = $favPPosts;
				}
			}else{
				$favposts = (isset($_COOKIE['newco'])) ? explode(',', (string) $_COOKIE['newco']) : array();
				$favposts = array_map('absint', $favposts); // Clean cookie input, it's user input!
				
				if(!empty($favposts)){
					foreach($favposts as $spost){
						if ( FALSE === get_post_status( $spost ) ) {
						}else{
							$favPPosts[] = $spost;
						}
					}
					
					$favposts = $favPPosts;
					
				}
				
			}
			return $favposts;
		}
	}