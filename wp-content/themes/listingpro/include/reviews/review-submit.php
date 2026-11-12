<?php

	if (!function_exists('Listingpro_review_submit_init')) {
		function Listingpro_review_submit_init(){

			global $listingpro_options;
			$dashURL    = isset($listingpro_options['listing-author']) ? $listingpro_options['listing-author'] : '';
			$D_pageID = url_to_postid($dashURL);
			if ($D_pageID) {
				$dashURL = get_permalink($D_pageID);
			}

			if(!$dashURL){
				$dashURL = home_url();
			}

			wp_register_script('review-submit-ajax', get_template_directory_uri() . '/assets/js/review-submit.js', array('jquery') );
			wp_enqueue_script('review-submit-ajax');

			wp_localize_script( 'review-submit-ajax', 'ajax_review_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));



		}
		if(!is_admin()){
			if(!is_singular('listing')){
				add_action('wp_enqueue_scripts', 'Listingpro_review_submit_init');
			}
		}
	}




	add_action('wp_ajax_ajax_review_submit',        'ajax_review_submit');
	add_action('wp_ajax_nopriv_ajax_review_submit', 'ajax_review_submit');

if(!function_exists('ajax_review_submit')){
	function ajax_review_submit(){

        check_ajax_referer( 'lp_ajax_nonce', 'lpNonce' );
        // Nonce is checked, get the POST data and sign user on
        if( !wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror'=>'yes'));
            die($res);
        }
		global $listingpro_options;
		$enableCaptcha = false;
		$description = '';
		$user_name = '';
		$listing_author_name = '';
		$processReview = true;
		$userNameExists = false;

		$enableUsernameField = false;
		if( isset($listingpro_options['lp_register_username']) ){
			if($listingpro_options['lp_register_username']==true){
				$enableUsernameField = true;
			}
		}


		$listing_author_email = '';
		if(isset($_POST['token'])){
			if(!empty($_POST['token'])){
				$enableCaptcha = true;
			}
			else{
				$processReview = false;
			}
		}
		else{
			$enableCaptcha = false;
			$processReview = true;
		}

		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$headers2[] = 'Content-Type: text/html; charset=UTF-8';

		$keyResponse = '';
		$user_email = '';

		if($enableCaptcha == true){
			if ( class_exists( 'cridio_Recaptcha' ) ){
				$keyResponse = cridio_Recaptcha_Logic::is_recaptcha_valid(sanitize_text_field($_POST['token']), sanitize_text_field($_POST['recaptha-action']));
				if($keyResponse == false){
					$processReview = false;
				}
				else{
					$processReview = true;
				}
			}
		}

		if($processReview==true){
			$listing_id = sanitize_text_field($_POST['comment_post_ID']);
			$title = sanitize_text_field($_POST['post_title']);
			$description = sanitize_text_field($_POST['post_description']);
			$rating = sanitize_text_field($_POST['rating']);
			$user_email = '';
			$postID = '';
			$post_information = array();

			$listing_author_id =  get_post_field('post_author', $listing_id );
			$listing_author_info = get_user_by('id', $listing_author_id);
			$listing_author_email = $listing_author_info->user_email;
			$listing_author_name = $listing_author_info->user_login;

			if ( is_user_logged_in() ) {

				$allowed_mimes = array('image/webp', 'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/x-icon');
				$files = $_FILES['post_gallery'];
				if( !empty($files) ){
					foreach ($files['name'] as $key => $value) {
						$file_type = $files['type'][$key];
						if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
							$res = json_encode(array('reviewID'=>$postID, 'status'=>esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro'), 'error'=>'notallowed'));
							die($res);
						}
					}
				}
				
				$uid = get_current_user_id();
				$author_obj = get_user_by('id', $uid);
				$user_email = $author_obj->user_email;
				$hasReview = lp_check_user_reviews_for_listing($uid, $listing_id);
				if( $listing_author_id==$uid ){
					$res = json_encode(array('reviewID'=>$postID, 'status'=>esc_html__('Sorry! Listing owners can not post review on their listings!', 'listingpro'), 'error'=>'already'));
					die($res);
				}
				elseif($hasReview !="1"){
					$post_information = array(
						'post_title' => esc_attr(strip_tags( $title )),
						'post_content' => $description,
						'post_type' => 'lp-reviews',
						'post_status' => 'publish'
					);
					$postID = wp_insert_post( $post_information );

				}
				else{
					$res = json_encode(array('reviewID'=>$postID, 'status'=>esc_html__('Sorry! already posted on this listing!', 'listingpro'), 'error'=>'already'));
					die($res);
				}

			}
			else{
				$user_email = sanitize_email($_POST['u_mail']);
				if( email_exists($user_email)==true ){
					$res = json_encode(array('reviewID'=>$postID, 'status'=>esc_html__('Email already exists! Please login or change email', 'listingpro'), 'error'=>'email'));
					die($res);
				}
				else{
					$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
					if($enableUsernameField==true){
						$user_name = sanitize_text_field($_POST['lp_custom_username']);
					}else{
						list($user_name) = explode('@', $user_email);
						$user_name .=rand(1,100);
					}

					$existinUserId = username_exists( $user_name );
					if(!empty($existinUserId)){
						$res = json_encode(array('reviewID'=>$postID, 'status'=>esc_html__('User name already exists! Please login or try a different one', 'listingpro'), 'error'=>'email'));
						die($res);
					}

					$user_id = wp_create_user( $user_name, $random_password, $user_email );
					$creds['user_login'] = $user_name;
					$creds['user_password'] = $random_password;
					$creds['remember'] = true;
					$user = wp_signon( $creds, true );


					$post_information = array(
						'post_author'=> $user_id,
						'post_title' => esc_attr(strip_tags( $title )),
						'post_content' => $description,
						'post_type' => 'lp-reviews',
						'post_status' => 'publish'
					);
					$postID = wp_insert_post( $post_information );

					$current_user = wp_get_current_user();
					//$user_email = $current_user->user_email;
					$admin_email = '';
					$admin_email = get_option( 'admin_email' );
					$website_url = site_url();
					$website_name = get_option('blogname');
					/* for user */
					$subject  = isset($listingpro_options['listingpro_subject_new_user_register']) ? $listingpro_options['listingpro_subject_new_user_register'] : '';
					$website_url = site_url();

					$formated_subject = lp_sprintf2("$subject", array(
						'website_url' => "$website_url",
						'website_name' => "$website_name",
						'user_name' => "$user_name",
					));

					$mail_content  = isset($listingpro_options['listingpro_new_user_register']) ? $listingpro_options['listingpro_new_user_register'] : '';
					$formated_mail_content = lp_sprintf2("$mail_content", array(
						'website_url' => "$website_url",
						'user_login_register' => "$user_name",
						'user_pass_register' => "$random_password",
						'website_name' => "$website_name",
						'user_name' => "$user_name",

					));

					/* for admin */
					$subject2  = isset($listingpro_options['listingpro_subject_admin_new_user_register']) ? $listingpro_options['listingpro_subject_admin_new_user_register'] : '';
					$mail_content2  = isset($listingpro_options['listingpro_admin_new_user_register']) ? $listingpro_options['listingpro_admin_new_user_register'] : '';

					$formated_mail_content2 = lp_sprintf2("$mail_content2", array(
						'website_url' => "$website_url",
						'user_login_register' => "$user_name",
						'user_email_register' => "$user_email",
						'website_name' => "$website_name",
						'user_name' => "$user_name",

					));

					$admin_email = get_option( 'admin_email' );
					lp_mail_headers_append();
                    LP_send_mail( $user_email, $formated_subject, $formated_mail_content, $headers );
                    LP_send_mail( $admin_email, $subject2, $formated_mail_content2, $headers2 );
					lp_mail_headers_remove();

				}
			}


			listing_set_metabox('rating', $rating, $postID);
			listing_set_metabox('listing_id', $listing_id, $postID);
			global $current_user;
			$user_name = $current_user->user_login;
			if( sanitize_text_field($_POST['multiState']) == '1' )
			{
				$multirating	=	sanitize_text_field($_POST['multirating']);
				$rating_meta_arr	=	explode( ',', $multirating );
				if( is_array( $rating_meta_arr ) )
				{
					$total_fields	=	count($rating_meta_arr);
					$rating_vals	=	array();
					foreach( $rating_meta_arr as $rating_meta )
					{
						$rating_meta_ar	=	explode( ':',$rating_meta );
						$rating_vals[]	=	$rating_meta_ar[1];

						listing_set_metabox($rating_meta_ar[0], $rating_meta_ar[1], $postID);
					}
					$total_multi_rating_sum	    =	array_sum($rating_vals);
					$total_multi_rating         =   $total_multi_rating_sum/$total_fields;
				}
				listing_set_metabox('rating', $total_multi_rating, $postID);
				$action = 'add';
				listingpro_set_listing_ratings($postID, $listing_id , $total_multi_rating , $action);
			}
			else
			{
				listing_set_metabox('rating', $rating, $postID);
				$action = 'add';
				listingpro_set_listing_ratings($postID, $listing_id , $rating , $action);
			}



			listingpro_total_reviews_add($listing_id);

			if ( !empty($_FILES['post_gallery']) ) {
				$ids = array();
				$files = $_FILES['post_gallery'];
				foreach ($files['name'] as $key => $value) {
					if ($files['name'][$key]) {
						$file = array(
							'name' => $files['name'][$key],
							'type' => $files['type'][$key],
							'tmp_name' => $files['tmp_name'][$key],
							'error' => $files['error'][$key],
							'size' => $files['size'][$key]
						);
						$_FILES = array ("post_gallery" => $file);
						$count = 0;
						foreach ($_FILES as $file => $array) {
							$newupload = listingpro_handle_attachment($file,$postID,$set_thu=false); 									 $ids[] =$newupload;									  $count++;
						}
					}
				}
				if( is_array($ids) && count($ids)>0 ){
					$img_ids = implode(",", $ids);
					update_post_meta($postID, 'gallery_image_ids', $img_ids);

				}
			}
			$dataRes = array();
			$dataRes['reviewid']= $postID;
			$dataRes['status']= esc_html__('Review has been submitted successfully', 'listingpro');
			$res = json_encode(array('reviewID'=>$postID, 'status'=>esc_html__('Review has been submitted successfully', 'listingpro')));


			/* for stats chart */
			lp_set_this_stats_for_chart($listing_author_id, $listing_id, 'reviews');
			/* stats chart ends */


			$listing_title = get_the_title($listing_id);
			$listing_url = get_the_permalink($listing_id);

			/* email for listing author */
			$authorEmailSubject  = isset($listingpro_options['listingpro_subject_review_author']) ? $listingpro_options['listingpro_subject_review_author'] : '';
			$authorEmailContent  = isset($listingpro_options['listingpro_content_review_author']) ? $listingpro_options['listingpro_content_review_author'] : '';
			

			$website_url = site_url();
			$website_name = get_option('blogname');

			$authorEmailContent = lp_sprintf2("$authorEmailContent", array(
				'listing_title' => "$listing_title",
				'listing_url' => "$listing_url",
				'reviewtext' => "$description",
				'reviewer_email' => "$user_email",
				'website_url' => "$website_url",
				'website_name' => "$website_name",
				'user_name' => "$listing_author_name",
			));
			lp_mail_headers_append();
            LP_send_mail( $listing_author_email, $authorEmailSubject, $authorEmailContent, $headers );

			/* email for reviewer */
			$reviewerEmailSubject  = isset($listingpro_options['listingpro_subject_reviewer']) ? $listingpro_options['listingpro_subject_reviewer'] : '';
			$reviewerEmailContent  = isset($listingpro_options['listingpro_content_reviewer']) ? $listingpro_options['listingpro_content_reviewer'] : '';


			$reviewerEmailContent = lp_sprintf2("$reviewerEmailContent", array(
				'listing_title' => "$listing_title",
				'listing_url' => "$listing_url",
				'reviewtext' => "$description",
				'reviewer_email' => "$user_email",
				'website_url' => "$website_url",
				'website_name' => "$website_name",
				'user_name' => "$user_name",
			));
			lp_mail_headers_append();
            LP_send_mail( $user_email, $reviewerEmailSubject, $reviewerEmailContent, $headers );
			lp_mail_headers_remove();
			/* saving activity in wp_options */

			$reviewData = get_post($postID);
			$reviewerID = $reviewData->post_author;

			$listingData = get_post($listing_id);
			$authID = $listingData->post_author;
			//$currentdate = date("Y-m-d h:i:a");
			//$currentdate = date("d-m-Y h:i:a");
			if( isset($_POST['multiState']) ){
				if( sanitize_text_field($_POST['multiState']) == '1' ){
				   $rating =   $total_multi_rating;
				}
			}
			$currentdate =  current_time('mysql');
			$activityData = array();
			$activityData = array( array(
				'type'	=>	'review',
				'actor'	=>	get_the_author_meta('user_login',$reviewerID),
				'reviewer'	=>	$reviewerID,
				'listing'	=>	$listing_id,
				'rating'	=>	$rating,
				'time'	=>	$currentdate
			));

			$updatedActivitiesData = array();

			$lp_recent_activities = get_option( 'lp_recent_activities' );
			if( $lp_recent_activities!=false ){

				$existingActivitiesData = get_option( 'lp_recent_activities' );
				if (array_key_exists($authID, $existingActivitiesData)) {
					$currenctActivityData = $existingActivitiesData[$authID];

					if(!empty($currenctActivityData)){
						if(count($currenctActivityData)>=20){
							$currenctActivityData =	array_slice($currenctActivityData,1,20);
							$updatedActivityData = array_merge($currenctActivityData,$activityData);
						}
						else{
							$updatedActivityData = array_merge($currenctActivityData,$activityData);
						}
					}

					$updatedActivityData = array_merge($currenctActivityData,$activityData);
					$existingActivitiesData[$authID] = $updatedActivityData;
				}
				else{
					$existingActivitiesData[$authID] = $activityData;
				}
				$updatedActivitiesData = $existingActivitiesData;
			}
			else{
				$updatedActivitiesData[$authID] = $activityData;
			}
			update_option( 'lp_recent_activities', $updatedActivitiesData );
		}
		else{
			$res = json_encode(array('reviewID'=>$postID, 'status'=>esc_html__('Please check captcha', 'listingpro'), 'error'=>'email', 'processReview'=>$processReview, 'keyResponse'=>$keyResponse));
		}

		die($res);
	}
}


	/* by zaheer on 16 march */
	add_action('wp_ajax_lp_reviews_interests',        'lp_reviews_interests');
	add_action('wp_ajax_nopriv_lp_reviews_interests', 'lp_reviews_interests');

	if(!function_exists('lp_reviews_interests')){
		function lp_reviews_interests(){
            check_ajax_referer( 'lp_ajax_nonce', 'lpNonce' );
            // Nonce is checked, get the POST data and sign user on
            if( !wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
                $res = json_encode(array('nonceerror'=>'yes'));
                die($res);
            }
			$newScore = '';
			$currentScore = '';
			$reviewID = '';
			$reviewID = sanitize_text_field($_POST['id']);
			$currentScore = sanitize_text_field($_POST['interest']);
			$restype = sanitize_text_field($_POST['restype']);
			$oldScore = $currentScore;
			if(!empty($currentScore)){
				$currentScore++;
			}
			else{
				$currentScore = 1;
			}

			$cookie_name = $restype.$reviewID;
			$cookie_name = preg_replace('/[=,; \t\r\n\013\014]/', '', $cookie_name);
			$cookie_value = true;
			$reactionCookie = (isset($_COOKIE[$cookie_name])) ? $_COOKIE[$cookie_name] : array();
			$response ='';

			if(empty($reactionCookie)){
				//setcookie($cookie_name , $cookie_value , time() + (3600 * 24 * 30), "/");
				setcookie($cookie_name , $cookie_value , time() + (3600 * 24 * 30));
				$key = 'review_'.$restype;
				listing_set_metabox($key, $currentScore, $reviewID);
				$newScore = listing_get_metabox_by_ID($key, $reviewID);

				/* saving activity in wp_options */
				$reviewData = get_post($reviewID);
				$reviewerID = $reviewData->post_author;

				$listing_id = listing_get_metabox_by_ID('listing_id', $reviewID);
				$listingData = get_post($listing_id);
				$authID = $listingData->post_author;
				//$currentdate = date("Y-m-d h:i:a");
				//$currentdate = date("d-m-Y h:i:a");
				$currentdate =  current_time('mysql');
				$activityData = array();
				$activityData = array( array(
					'type'	=>	'reaction',
					'actor'	=>	'',
					'reviewer'	=>	$reviewerID,
					'listing'	=>	$listing_id,
					'rating'	=>	$restype,
					'time'	=>	$currentdate
				));

				$updatedActivitiesData = array();

				$lp_recent_activities = get_option( 'lp_recent_activities' );
				if( $lp_recent_activities!=false ){

					$existingActivitiesData = get_option( 'lp_recent_activities' );
					if (array_key_exists($authID, $existingActivitiesData)) {
						$currenctActivityData = $existingActivitiesData[$authID];
						if(!empty($currenctActivityData)){
							if(count($currenctActivityData)>=20){
							$currenctActivityData =	array_slice($currenctActivityData,1,20);
								$updatedActivityData = array_merge($currenctActivityData,$activityData);
							}
							else{
								$updatedActivityData = array_merge($currenctActivityData,$activityData);
							}
						}
						$existingActivitiesData[$authID] = $updatedActivityData;
					}
					else{
						$existingActivitiesData[$authID] = $activityData;
					}
					$updatedActivitiesData = $existingActivitiesData;
				}
				else{
					$updatedActivitiesData[$authID] = $activityData;
				}
				update_option( 'lp_recent_activities', $updatedActivitiesData );

				$status = esc_html__('Thanks for reacting', 'listingpro');

				$response = json_encode(array('id'=>$reviewID , 'newScore'=>$newScore, 'errors'=> 'no', 'cookieArray'=>$reactionCookie, 'statuss'=> $status, 'recttype'=>$restype));
			}
			else{
				$status = esc_html__('You already reacted', 'listingpro');
				$response = json_encode(array('id'=>$reviewID , 'newScore'=>$oldScore, 'errors'=> 'yes', 'cookieArray'=>$reactionCookie, 'statuss'=> $status, 'recttype'=>$restype));
			}
			die($response);
		}
	}
	/* end by zaheer on 16 march */

	if(!function_exists('lp_has_review_reaction')){
		function lp_has_review_reaction($reactionQuery){
			$reactionCookie = (isset($_COOKIE[$reactionQuery])) ? $_COOKIE[$reactionQuery] : array();
			if(empty($reactionCookie)){
				return 0;
			}
			else{
				return 1;
			}
		}
	}

?>