<?php
	
	if(!function_exists('save_lp-save_lpads_meta')){
		function save_lpads_meta( $post_id, $post ){
			global $listingpro_options;
			$post_type = get_post_type($post_id);
			$ads_mode = '';
			if(isset($_POST['ads_mode'])){
				$ads_mode = $_POST['ads_mode'];
			}
			
			$duration = '';
			if(isset($_POST['duration'])){
				$duration = $_POST['duration'];
			}
			$budget = '';
			if(isset($_POST['budget'])){
				$budget = $_POST['budget'];
			}
			$remaining_balance = '';
			if(isset($_POST['remaining_balance'])){
				$remaining_balance = $_POST['remaining_balance'];
			}
			$click_performed = '';
			if(isset($_POST['click_performed'])){
				$click_performed = $_POST['click_performed'];
			}
			$adsType = array(
				'lp_random_ads',
				'lp_detail_page_ads',
				'lp_top_in_search_page_ads'
			);
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
			if ( $post_type !== 'lp-ads' ) return $post_id;
			
			
			if(!empty($ads_mode)){
				listing_set_metabox('ads_mode', $ads_mode, $post_id);
			}
			if(!empty($duration)){
				listing_set_metabox('duration', $duration, $post_id);
			}
			if(!empty($budget)){
				listing_set_metabox('budget', $budget, $post_id);
			}
			if(!empty($remaining_balance)){
				listing_set_metabox('remaining_balance', $remaining_balance, $post_id);
			}
			if(!empty($click_performed)){
				listing_set_metabox('click_performed', $click_performed, $post_id);
			}
			
			
			if( !empty($_POST['ad_type']) && isset($_POST['ad_type']) ){
				listing_set_metabox('ad_status', 'active', $post_id);
			
				$ad_type = $_POST['ad_type'];
				
				$listingID = $_POST['ads_listing'];
				if(!empty($listingID)){
					listing_set_metabox('ads_listing', $listingID, $post_id);
					listing_set_metabox('campaign_id', $post_id, $listingID);
					update_post_meta( $listingID, 'campaign_status', 'active' );
				}
				
				
				if(!empty($ad_type)){
					foreach( $ad_type as $type ){
						//update_post_meta( $listingID, $type, 'active' );
					}
				}
				foreach( $adsType as $atype ){
					if (in_array($atype, $ad_type)) {
						update_post_meta( $listingID, $atype, 'active' );
					}else{
						delete_post_meta($listingID, $atype);
					}
				}
				
				$myAd = get_post($post_id);
				if( $myAd->post_modified_gmt == $myAd->post_date_gmt ){
					//New ad by admin so update value in db to support ads by admin
					$table = 'listing_campaigns';
					$insert_data = array(
						'user_id' => get_current_user_id(),
						'post_id' => $post_id,
						'payment_method' => '',
						'price' => '',
						'currency' => '',
						'status' => 'success',
						'transaction_id' => '',
						'mode' => $ads_mode,
						'duration' => $duration,
						'budget' => '',
					);
					lp_insert_data_in_db($table, $insert_data);
				}
				
			}
			else{
				return $post_id;
			}
			

			
		}
		add_action('save_post', 'save_lpads_meta', 10, 3);

	}
	
	/*========= delete listing post meta on trashing ad post =========*/
	add_action('wp_trash_post', 'delete_listing_meta_on_ad_trash', 10);
	if( !function_exists('delete_listing_meta_on_ad_trash') ){
		function delete_listing_meta_on_ad_trash($post_id){
			
			$ads_listing = listing_get_metabox_by_ID('ads_listing', $post_id );
			$ads_type = listing_get_metabox_by_ID('ad_type', $post_id );
			if( !empty($ads_listing) && !empty($ads_type) ){
				if(!empty($ads_type)){
					foreach( $ads_type as $type ){
						update_post_meta( $ads_listing, $type, 'expire' );
					}
				}
			}
			
		}
	}
	
	/* ============== ListingPro Custom post type columns ============ */
	
	if (!function_exists('lp_ads_columns')) {
		function lp_ads_columns($columns) {
			return array(
				'cb' => '<input type="checkbox" />',			
				'title' => esc_html__('Ad Title'),			
				'list_title' => esc_html__('Listing Title'),			
				'author' => esc_html__('Author'),
				'date' => esc_html__('Date')
			);
		}
		add_filter('manage_lp-ads_posts_columns' , 'lp_ads_columns');
	}
	/* ============== content for custom column ======================*/
	
	if (!function_exists('listingpro_ads_columns_content')) {
		function listingpro_ads_columns_content($column_name, $post_ID) {
			
			if ($column_name == 'list_title') {
				$metabox = get_post_meta($post_ID, 'lp_' . strtolower(THEMENAME) . '_options', true);
				if(isset($metabox['ads_listing']) && !empty($metabox['ads_listing'])){
					echo get_the_title($metabox['ads_listing']);
				}
			}
			
			
			
			
		}
		add_action('manage_lp-ads_posts_custom_column', 'listingpro_ads_columns_content', 10, 2);
	}
	
	/* ============== content for custom column ======================*/