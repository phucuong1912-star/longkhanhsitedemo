<?php

/**
 * Claim List
 *
 */

/* ============== ListingPro single ajax Init ============ */

if (!function_exists('Listingpro_single_ajax_init')) {
	function Listingpro_single_ajax_init()
	{


		wp_register_script('ajax-single-ajax', get_template_directory_uri() . '/assets/js/single-ajax.js', array('jquery'));
		wp_enqueue_script('ajax-single-ajax');



		wp_localize_script('ajax-single-ajax', 'single_ajax_object', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
		));
	}
	if (!is_admin()) {
		if (!is_singular('listing')) {
			add_action('init', 'Listingpro_single_ajax_init');
		}
	}
}




/* ============== ListingPro Claim Ajax Process ============ */
add_action('wp_ajax_listingpro_claim_list', 'listingpro_claim_list');
add_action('wp_ajax_nopriv_listingpro_claim_list', 'listingpro_claim_list');
if (!function_exists('listingpro_claim_list')) {
	function listingpro_claim_list()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}

		global $listingpro_options;
		$post_title = '';
		$post_url = '';
		$email1 = '';
		$message = '';
		$author_nicename = '';
		$author_url = '';
		$emailexist = false;
		$userID = '';
		$claim_plan = '';
		$claim_type = '';

		$successMsg = '
				<h3>' . esc_html__('Thank You!', 'listingpro') . '</h3>
				<p>' . esc_html__('Your claim request has been submitted. Please sit back and relax it may take up to few business days for approval.', 'listingpro') . '</p>
			';


		$errorMsg = '
				<h3>' . esc_html__('Sorry!', 'listingpro') . '</h3>
				<p>' . esc_html__('You have already submitted claim for this listing', 'listingpro') . '</p>
			';


		$loginErrorMsg = '
				<h3>' . esc_html__('Login error!', 'listingpro') . '</h3>
				<p>' . esc_html__('There is problem in login', 'listingpro') . '</p>
			';


		$registerErrorMsg = '
				<h3>' . esc_html__('Registeration error!', 'listingpro') . '</h3>
				<p>' . esc_html__('There is problem in user registeration', 'listingpro') . '</p>
			';


		if (!is_user_logged_in()) {
			/* signin or signup attempt */
			if (isset($_POST['lp-signin-on-claim'])) {
				/* signin attempt */
				$username = sanitize_text_field($_POST['claim_username']);
				$userpass = sanitize_text_field($_POST['claim_userpass']);
				$userID = lp_do_user_sign_in($userpass, $username);
				if (empty($userID)) {
					//login error
					exit(json_encode(array('state' => $loginErrorMsg, 'result' => 'error')));
				}
			} else {
				/* signup attempt */
				$useremail = sanitize_email($_POST['claim_new_user_email']);

				$user_name = null;
				list($user_name) = explode('@', $useremail);
				$user_name .= rand(1, 10000);


				$userInfo = lp_create_new_user(null, $user_name, $useremail);
				if (!empty($userInfo)) {
					$userID = $userInfo['id'];
					$upassword = $userInfo['password'];
				} else {
					//user creating error
					exit(json_encode(array('state' => $registerErrorMsg, 'result' => 'error')));
				}
				$userID = lp_do_user_sign_in($upassword, $user_name);

				if (empty($userID)) {
					//login error
					exit(json_encode(array('state' => $loginErrorMsg, 'result' => 'error')));
				}
			}
		} else {
			/* already loggedin */
			$userID = get_current_user_id();
		}

		$allowed_mimes = array('image/webp', 'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/x-icon');
		$filescla = $_FILES['claim_attachment'];
		if (!empty($filescla)) {
			foreach ($filescla['name'] as $key => $value) {
				$file_type = $filescla['type'][$key];
				if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
					exit(json_encode(array('state' => esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro'), 'result' => 'error')));
				}
			}
		}

		$userData = get_user_by('id', $userID);
		$email1 = $userData->user_email;

		$posttitle = '';

		$firstname = sanitize_text_field($_POST['firstname']);
		$lastname = sanitize_text_field($_POST['lastname']);
		$bEmail = sanitize_email($_POST['bemail']);

		$post_title = sanitize_text_field($_POST['post_title']);
		$post_URL = sanitize_text_field($_POST['post_url']);
		$posttitle = esc_html__('Claim for', 'listingpro') . ' ';
		$posttitle .= $post_title;

		$author_email = sanitize_email($_POST['author_email']);
		$message = sanitize_text_field($_POST['message']);
		$claimerphone = sanitize_text_field($_POST['phone']);

		if (isset($_POST['lp_claimed_plan'])) {
			if (!empty($_POST['lp_claimed_plan'])) {
				$claim_plan = sanitize_text_field($_POST['lp_claimed_plan']);
			}
		}

		if (isset($_POST['claim_type'])) {
			if (!empty($_POST['claim_type'])) {
				$claim_type = sanitize_text_field($_POST['claim_type']);
			}
		}

		$post_id = sanitize_text_field($_POST['post_id']);
		$post_author = get_post_field('post_author', $post_id);
		$user = get_user_by('id', $post_author);
		$usersname = $user->user_login;
		$authorEmail = $user->user_email;

		$claimerdetails = '';
		$claimerdetails .= $firstname . ' ' . $lastname . ' : ';
		$claimerdetails .= $email1 . ' : ';
		$claimerdetails .= $message;

		$status = 'pending';
		$result = array();
		$claim_post = array(
			'post_title'    => wp_strip_all_tags($posttitle),
			'post_type'   => 'lp-claims',
			'post_status'   => 'publish',
		);

		$getClaimerIDS = get_post_meta($post_id, 'claimers', true);
		if (!empty($getClaimerIDS)) {
			//already added claim by this user on the listing
			$is_user_exist = in_array($userID, $getClaimerIDS);
			if (!empty($is_user_exist)) {
				echo json_encode(array('state' => $errorMsg, '</span>', 'result' => $result));
				exit();
			}
		}

		$id = wp_insert_post($claim_post);
		if (!empty($getClaimerIDS)) {
			array_push($getClaimerIDS, $userID);
			update_post_meta($post_id, 'claimers', $getClaimerIDS);
		} else {
			update_post_meta($post_id, 'claimers', array($userID));
		}

		$claimer_email = $userData->user_email;
		$user_name     = $userData->user_login;

		listing_set_metabox('details', $claimerdetails, $id);
		listing_set_metabox('claimer', $userID, $id);
		listing_set_metabox('owner', $usersname, $id);
		listing_set_metabox('claim_status', $status, $id);
		listing_set_metabox('claimed_listing', $post_id, $id);
		listing_set_metabox('claimer_phone', $claimerphone, $id);
		listing_set_metabox('claimer_bemail', $bEmail, $id);
		listing_set_metabox('claimer_fname', $firstname, $id);
		listing_set_metabox('claimer_lname', $lastname, $id);

		//file attachment

		$files = $_FILES["claim_attachment"];
		if ($files['name']) {
			$file = array(
				'name' => $files['name'],
				'type' => $files['type'],
				'tmp_name' => $files['tmp_name'],
				'error' => $files['error'],
				'size' => $files['size']
			);
			$_FILES = array("claim_attachment" => $file);
			$count = 0;
			foreach ($_FILES as $file => $array) {
				$newupload = listingpro_handle_attachment($file, $id, $set_thu = false);
				$attachment_id = wp_get_attachment_url($newupload);
				listing_set_metabox('claimer_attachment', $attachment_id, $id);
			}
		}

		if (!empty($claim_plan)) {
			listing_set_metabox('claim_plan', $claim_plan, $id);
		}

		if (!empty($claim_plan)) {
			listing_set_metabox('claim_type', $claim_type, $id);
		}

		$admin_email = '';
		$admin_email = get_option('admin_email');
		$website_url = site_url();
		$website_name = get_option('blogname');
		$listing_title = $post_title;
		$listing_url = $post_URL;
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		/* ====for claimer=== */
		$c_mail_subject = $listingpro_options['listingpro_subject_listing_claimer'];
		$c_mail_body = $listingpro_options['listingpro_content_listing_claimer'];

		$c_mail_subject_a = lp_sprintf2("$c_mail_subject", array(
			'website_url' => "$website_url",
			'listing_title' => "$listing_title",
			'listing_url' => "$listing_url",
			'website_name' => "$website_name",
			'user_name' => "$user_name",
		));

		$c_mail_body_a = lp_sprintf2("$c_mail_body", array(
			'website_url' => "$website_url",
			'listing_title' => "$listing_title",
			'listing_url' => "$listing_url",
			'website_name' => "$website_name",
			'user_name' => "$user_name",
		));
		lp_mail_headers_append();
		LP_send_mail($claimer_email, $c_mail_subject_a, $c_mail_body_a, $headers);

		/* ====for Admin=== */
		$a_mail_subject = $listingpro_options['listingpro_subject_listing_claim_admin'];
		$a_mail_body = $listingpro_options['listingpro_content_listing_claim_admin'];

		$a_mail_subject_a = lp_sprintf2("$a_mail_subject", array(
			'website_url' => "$website_url",
			'listing_title' => "$listing_title",
			'listing_url' => "$listing_url",
			'website_name' => "$website_name",
			'user_name' => "$user_name",
		));

		$a_mail_body_a = lp_sprintf2("$a_mail_body", array(
			'website_url' => "$website_url",
			'listing_title' => "$listing_title",
			'listing_url' => "$listing_url",
			'website_name' => "$website_name",
			'user_name' => "$user_name",
		));
		LP_send_mail($admin_email, $a_mail_subject_a, $a_mail_body_a, $headers);
		/* ====for Author=== */
		$athr_mail_subject = $listingpro_options['listingpro_subject_listing_author'];
		$athr_mail_body = $listingpro_options['listingpro_content_listing_author'];

		$athr_mail_subject_a = lp_sprintf2("$athr_mail_subject", array(
			'website_url' => "$website_url",
			'listing_title' => "$listing_title",
			'listing_url' => "$listing_url",
			'website_name' => "$website_name",
			'user_name' => "$user_name",
		));

		$athr_mail_body_a = lp_sprintf2("$athr_mail_body", array(
			'website_url' => "$website_url",
			'listing_title' => "$listing_title",
			'listing_url' => "$listing_url",
			'website_name' => "$website_name",
			'user_name' => "$user_name",
		));
		LP_send_mail($authorEmail, $athr_mail_subject_a, $athr_mail_body_a, $headers);
		/* ====end for Author mail=== */
		lp_mail_headers_remove();
		$result = $id;

		echo json_encode(array('state' => $successMsg, 'result' => $result));
		exit();
	}
}

/* ============== ListingPro Contact author Process ============ */


add_action('wp_ajax_listingpro_contactowner', 'listingpro_contactowner');
add_action('wp_ajax_nopriv_listingpro_contactowner', 'listingpro_contactowner');
if (!function_exists('listingpro_contactowner')) {
	function listingpro_contactowner()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		global $listingpro_options;
		$post_id = '';
		$post_title = '';
		$post_url = '';
		$name = '';
		$email = '';
		$phone = '';
		$message = '';
		$authoremail = '';
		$authorID = '';
		$result = '';
		$error = array();

		if (isset($_POST['formData'])) {

			parse_str($_POST['formData'], $formData);
			$post_id = sanitize_text_field($formData['post_id']);
			$post_title = get_the_title($post_id);
			$post_url = get_the_permalink($post_id);
			$name1 = sanitize_text_field($formData['name7']);
			$email = sanitize_email($formData['email7']);
			$phone = sanitize_text_field($formData['phone7']);
			$message = sanitize_text_field($formData['message7']);
			$authorID = sanitize_text_field($formData['author_id']);

			$enableCaptcha = false;
			$processLead = true;
			$gCaptcha;

			if (isset($_POST['token']) && isset($_POST['recaptha-action'])) {
				if (!empty($_POST['token']) && !empty($_POST['recaptha-action'])) {
					$enableCaptcha = true;
				} else {
					$processLead = false;
				}
			} else {
				$enableCaptcha = false;
				$processLead = true;
			}


			$keyResponse = '';

			if ($enableCaptcha == true) {
				if (class_exists('cridio_Recaptcha')) {
					$keyResponse = cridio_Recaptcha_Logic::is_recaptcha_valid(sanitize_text_field($_POST['token']), sanitize_text_field($_POST['recaptha-action']));
					if ($keyResponse == false) {
						$processLead = false;
					} else {
						$processLead = true;
					}
				}
			}

			/* excluding default keys */
			$removeData = array('post_id', 'name7', 'email7', 'phone7', 'message7', 'author_id', 'token', 'recaptha-action');
			foreach ($removeData as $removKey) {
				unset($formData[$removKey]);
			}

			$newFormData = array();
			$newFormDataContent =   '';
			if (!empty($formData)) {
				foreach ($formData as $fkey => $fvalue) {
					if (strpos($fkey, '_label') !== false) {
						$extra_data_key =   str_replace('_label', '', $fkey);
						$newFormData[$fvalue] =   $formData[$extra_data_key];
					}
				}
			}
			$orgmsgs = $message;
			$message = null;
			if (!empty($newFormData)) {
				foreach ($newFormData as $label => $value) {
					$message .= $label . ":" . $value . '<br>';
				}
				$message .= '<br>';
			}
			$message .= $orgmsgs;


			if ($processLead == true) {
				$listing_id = $post_id;
				$listingData = get_post($listing_id);
				$authID = $listingData->post_author;
				if (empty($authorID)) {
					$authorID = $authID;
				}
				$user_info = get_user_by('ID', $authorID);
				$toauthor = $user_info->user_email;
				$authObj = get_user_by('email', $toauthor);
				$user_name = $authObj->user_login;
				$subject =  $listingpro_options['listingpro_subject_lead_form'];
				$body =  $listingpro_options['listingpro_content_lead_form'];
				$user_name = $name1;
				$website_url = site_url();
				$website_name = get_option('blogname');

				$formated_mail_content = lp_sprintf2("$body", array(
					'listing_title' => "$post_title",
					'sender_name' => "$name1",
					'sender_email' => "$email",
					'sender_phone' => "$phone",
					'sender_message' => "$message",
					'user_name' => "$user_name",
					'website_url' => "$website_url",
					'website_name' => "$website_name",
				));

				$headers[] = "Content-Type: text/html; charset=UTF-8";

				if (empty($email) || empty($message) || empty($name1)) {
					$result = 'fail';
					if (empty($email)) {
						$error['email'] = $email;
					}
					if (empty($message)) {
						$error['message'] = $message;
					}
					if (empty($name1)) {
						$error['name7'] = $name1;
					}
					echo json_encode(array('state' => '<span class="alert alert-danger">' . esc_html__('Something is missing! Please fill out all fields highlighted in red.', 'listingpro') . '</span>', 'result' => $result, 'errors' => $error));
				} else {
					$lpdatetodaycus = date('Y-m-d H:i:s');
					$lpdatetodaycus = strtotime($lpdatetodaycus);
					lp_mail_headers_append();

					$headers[] = 'From: ' . $name1 . ' <' . $email . '>';
					$result = LP_send_mail($toauthor, $subject, $formated_mail_content, $headers);

					lp_mail_headers_remove();

					$result =   true;
					/* need to be remove of comment */ /* $result = true; */

					if ($result == true) {
						$leadsCount = '';
						$leadsCount = get_user_meta($authorID, 'leads_count', true);
						if (isset($leadsCount)) {
							$leadsCount = (int)$leadsCount + 1;
							update_user_meta($authorID, 'leads_count', $leadsCount);
						} else {
							$leadsCount = 0;
							update_user_meta($authorID, 'leads_count', $leadsCount);
						}

						/* saving activity in wp_options */

						$listing_id = $post_id;
						$listingData = get_post($listing_id);
						$authID = $listingData->post_author;
						//$currentdate = date("d-m-Y h:i:a");
						$currentdate =  current_time('mysql');
						$activityData = array();
						$activityData = array(array(
							'type'	=>	'lead',
							'actor'	=>	$name1,
							'reviewer'	=>	'',
							'listing'	=>	$listing_id,
							'rating'	=>	'',
							'time'	=>	$currentdate
						));

						$updatedActivitiesData = array();

						$lp_recent_activities = get_option('lp_recent_activities');
						if ($lp_recent_activities != false) {

							$existingActivitiesData = get_option('lp_recent_activities');
							if (array_key_exists($authID, $existingActivitiesData)) {
								$currenctActivityData = $existingActivitiesData[$authID];
								if (count($currenctActivityData) >= 20) {
									$currenctActivityData = array_slice($currenctActivityData, 1, 20);
									$updatedActivityData = array_merge($currenctActivityData, $activityData);
								} else {
									$updatedActivityData = array_merge($currenctActivityData, $activityData);
								}
								$existingActivitiesData[$authID] = $updatedActivityData;
							} else {
								$existingActivitiesData[$authID] = $activityData;
							}
							$updatedActivitiesData = $existingActivitiesData;
						} else {
							$updatedActivitiesData[$authID] = $activityData;
						}
						update_option('lp_recent_activities', $updatedActivitiesData);

						/* saving data for internal messages */
						$newMessagesArray = array();
						$newTimeArray = array();
						$dataArray = array();
						$lpdatetoday = date(get_option('date_format'));
						$dataArray['name'] = $name1;
						$dataArray['phone'] = $phone;
						$dataArray['message'][$lpdatetodaycus] = $orgmsgs;
						$dataArray['time'] = array($lpdatetoday);
						$dataArray['extras'] = $newFormData;
						$lpAllPrevMessages = get_user_meta($authorID, 'lead_messages', true);
						if (!empty($lpAllPrevMessages)) {
							/* already have messages */
							if (array_key_exists("$post_id", $lpAllPrevMessages)) {
								$PrevMessges = $lpAllPrevMessages[$post_id];
								if (array_key_exists("$email", $PrevMessges)) {
									$PrevMessges = $lpAllPrevMessages[$post_id][$email];
									$newMessagesArray = $PrevMessges['leads']['message'];
									$newTimeArray = $PrevMessges['leads']['time'];
									array_push($newMessagesArray, $orgmsgs);
									array_push($newTimeArray, $lpdatetoday);
									$dataArray['message'] = $newMessagesArray;
									$dataArray['time'] = $newTimeArray;
								} else {

									$lpAllPrevMessages[$post_id][$email]['leads'] = $dataArray;
								}
								$lpAllPrevMessages[$post_id][$email]['leads'] = $dataArray;
							} else {
								/* no key exists */

								$lpAllPrevMessages[$post_id][$email]['leads'] = $dataArray;
							}
						} else {
							/* first message */
							$lpAllPrevMessages = array();
							$lpAllPrevMessages[$post_id][$email]['leads'] = $dataArray;
						}
						$lpAllPrevMessages[$post_id][$email]['status'] = 'unread';
						$latestLead = array(
							$post_id => $email,
						);
						update_user_meta($authorID, 'lead_messages', $lpAllPrevMessages);
						update_user_meta($authorID, 'latest_lead', $latestLead);

						/* for registered user leads sent */
						if (email_exists($email)) {
							$rUser = get_user_by('email', $email);
							$rUserID = $rUser->ID;
							update_user_meta($rUserID, 'leads_sent', $lpAllPrevMessages);
						}

						/* saving data for internal messages */
						/* for stats chart */
						lp_set_this_stats_for_chart($authorID, $listing_id, 'leads');
						/* stats chart ends */
						echo json_encode(array('state' => '<span class="alert alert-success">' . esc_html__('Email has been sent.', 'listingpro') . '</span>', 'result' => $result, 'errors' => $error));
					} else {
						echo json_encode(array('state' => '<span class="alert alert-danger">' . esc_html__('Something went wrong.', 'listingpro') . '</span>', 'result' => $result, 'errors' => $error));
					}
				}
			} else {
				$result = 'fail';
				$error['email'] = $email;
				$error['message'] = $message;
				$error['name7'] = $name1;
				echo json_encode(array('state' => '<span class="alert alert-danger">' . esc_html__('Please check captcha', 'listingpro') . '</span>', 'result' => $result, 'errors' => $error));
			}
		}
		exit();
	}
}

/* =========================================change price plan============== */
add_action('wp_ajax_listingpro_change_plan', 'listingpro_change_plan');
if (!function_exists('listingpro_change_plan')) {
	function listingpro_change_plan()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		global $listingpro_options;
		$plan_id = sanitize_text_field($_POST['ch_plan_id']);
		$listing_id = sanitize_text_field($_POST['ch_listing_id']);
		$listing_status = get_post_status($listing_id);
		$checkout = $listingpro_options['payment-checkout'];
		$checkout_url = get_permalink($checkout);
		$status = '';
		$ex_plan_price = '';
		$new_plan_price = '';
		$action = '';
		$subsc_status = '';
		$alertmsg = '';

		$ex_plan = listing_get_metabox_by_ID('Plan_id', $listing_id);
		if (!empty($ex_plan)) {
			$ex_plan_price = get_post_meta($ex_plan, 'plan_price', true);
		}
		if (!empty($plan_id)) {
			$new_plan_price = get_post_meta($plan_id, 'plan_price', true);
		}

		/* if this new plan is already purchased as package */
		$check_plan_credit = null;
		$plan_type = get_post_meta($plan_id, 'plan_package_type', true);
		if (!empty($plan_type) && $plan_type == "Package") {
			$check_plan_credit = lp_check_package_has_credit($plan_id);
		}

		listing_set_metabox('changed_planid', $plan_id, $listing_id);

		if (!empty($check_plan_credit) && $check_plan_credit == true) {
			$action = '		
								<form method="post"  action="' . $checkout_url . '">
								<input type="hidden" name="planid" value="' . $plan_id . '">
								<input type="hidden" name="listingid" value="' . $listing_id . '">
								<a href="" class="lp_change_plan_action" data-planid="' . $plan_id . '" data-listingid="' . $listing_id . '">' . esc_html__('Proceed', 'listingpro') . '</a>
								<form>
								';
		} elseif ($listing_status == "publish") {
			if ($new_plan_price <= $ex_plan_price) {

				$action = '		
								<form method="post"  action="' . $checkout_url . '">
								<input type="hidden" name="planid" value="' . $plan_id . '">
								<input type="hidden" name="listingid" value="' . $listing_id . '">
								<a href="" class="lp_change_plan_action" data-planid="' . $plan_id . '" data-listingid="' . $listing_id . '">' . esc_html__('Proceed', 'listingpro') . '</a>
								<form>
								';
			} else {
				$action = '
							<form method="post"  action="' . $checkout_url . '">
							<input type="hidden" name="planid" value="' . $plan_id . '">
							<input type="hidden" name="listingid" value="' . $listing_id . '">
							<input type="submit" value="' . esc_html__('Pay & Proceed', 'listingpro') . '"/>
							</form>
							';
			}
			/* check if subscribed already */
			$alreadySubscribed = false;
			$uid = get_current_user_id();
			$userSubscriptions = get_user_meta($uid, 'listingpro_user_sbscr', true);
			if (!empty($userSubscriptions)) {
				foreach ($userSubscriptions as $key => $subscription) {
					$subscr_plan_id = $subscription['plan_id'];
					$subscr_listing_id = $subscription['listing_id'];
					if (($subscr_plan_id == $ex_plan) && ($subscr_listing_id == $listing_id)) {
						$alreadySubscribed = true;
						break;
					}
				}
			}

			if ($alreadySubscribed == true) {
				$subsc_status = 'yes';
				$alertmsg = esc_html__('Alert! your existing plan attached with this listing has an active subscription, changing plan will cancel your subscription, make sure to change before proceed', 'listingpro');
			}
			/* end check if subscribed already */
		} else {

			$payment_status = lp_get_payment_status_by_ID($listing_id);
			if (($payment_status == "success") && ($new_plan_price >= $ex_plan_price)) {
				$action = '
								<form method="post" action="' . $checkout_url . '">
								<input type="hidden" name="planid" value="' . $plan_id . '">
								<input type="hidden" name="listingid" value="' . $listing_id . '">
								<input type="submit" value="' . esc_html__('Pay & Proceed', 'listingpro') . '"/>
								</form>
								';
			} elseif (!empty($new_plan_price)) {
				$action = '
							<form method="post"  action="' . $checkout_url . '">
							<input type="hidden" name="planid" value="' . $plan_id . '">
							<input type="hidden" name="listingid" value="' . $listing_id . '">
							<input type="submit" value="' . esc_html__('Pay & Proceed', 'listingpro') . '"/>
							</form>
							
						';
			} else {
				$action = '
								<form method="post" action="' . $checkout_url . '">
								<input type="hidden" name="planid" value="' . $plan_id . '">
								<input type="hidden" name="listingid" value="' . $listing_id . '">
								<a href="" class="lp_change_plan_action" data-planid="' . $plan_id . '" data-listingid="' . $listing_id . '">' . esc_html__('Proceed', 'listingpro') . '</a>
								</form>
								';
			}
		}


		$planHavePrice = false;
		$plan_days  =   false;
		$plan_id = 'none';
		if (isset($_POST['plan_id'])) {
			$plan_id = sanitize_text_field($_POST['plan_id']);
			$plan_price = get_post_meta($plan_id, 'plan_price', true);
			$plan_days  =   get_post_meta($plan_id, 'plan_time', true);
			if (!empty($plan_price)) {
				$planHavePrice = true;
			}
		} else {
			$plan_id = 'none';
		}

		if ($plan_id != 'none' && $plan_days) {
			listing_set_metabox('lp_purchase_days', $plan_days, $listing_id);
		}

		$data = array('planid' => $plan_id, 'listing' => $listing_id, 'status' => $status, 'error' => '', 'subscribed' => $subsc_status, 'action' => $action, 'actionurl' => $checkout_url, 'listing_status' => $listing_status, 'alertmsg' => $alertmsg);
		die(json_encode($data));
	}
}

/* =========================================change plan proceeding============== */
add_action('wp_ajax_listingpro_change_plan_proceeding', 'listingpro_change_plan_proceeding');
if (!function_exists('listingpro_change_plan_proceeding')) {
	function listingpro_change_plan_proceeding()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		global $listingpro_options;
		$res = array();
		$plan_id = sanitize_text_field($_POST['plan_iddd']);


		$listing_id = sanitize_text_field($_POST['listing_iddd']);
		$plan_days  =   get_post_meta($plan_id, 'plan_time', true);
		/* if package is purchased */
		$check_plan_credit = null;
		$plan_type = get_post_meta($plan_id, 'plan_package_type', true);
		if (!empty($plan_type) && $plan_type == "Package") {
			$check_plan_credit = lp_check_package_has_credit($plan_id);
		}
		if (!empty($check_plan_credit) && $check_plan_credit == true) {
			lp_update_credit_package($listing_id, $plan_id);
		}
		/* end if package is purchased */


		if (!empty($plan_id) && !empty($listing_id)) {
			/* if expired.. publish it */
			$listing_status = get_post_status($listing_id);
			if ($listing_status == "expired") {
				$my_listing_post = array();
				$my_listing_post['ID'] = $listing_id;
				$my_listing_post['post_date'] = date("Y-m-d H:i:s");
				$my_listing_post['post_status'] = 'publish';
				wp_update_post($my_listing_post);
			}
			/* end if expired.. publish it */
			/* check if subscribed already */
			$ex_plan = listing_get_metabox_by_ID('Plan_id', $listing_id);
			if (!empty($ex_plan)) {
				lp_cancel_stripe_subscription($listing_id, $ex_plan);
				listing_set_metabox('Plan_id', $plan_id, $listing_id);
				listing_set_metabox('changed_planid', '', $listing_id);
			}
			/* end check if subscribed already */
			if (!empty($plan_days)) {
				listing_set_metabox('lp_purchase_days', $plan_days, $listing_id);
			} else {
				listing_set_metabox('lp_purchase_days', '', $listing_id);
			}
			listing_set_metabox('Plan_id', $plan_id, $listing_id);
			$msg = '<span class="alert alert-success">' . esc_html__('Plan has been changed', 'listingpro') . '</span>';
			$res = array('status' => 'success', 'message' => $msg);
		}

		die(json_encode($res));
	}
}

/* =========================================cancel subscription proceeding============== */
add_action('wp_ajax_listingpro_cancel_subscription_proceeding', 'listingpro_cancel_subscription_proceeding');
if (!function_exists('listingpro_cancel_subscription_proceeding')) {
	function listingpro_cancel_subscription_proceeding()
	{

		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		$response = array();
		global $listingpro_options;

		$secritKey = '';
		$secritKey = $listingpro_options['stripe_secrit_key'];
		if (isset($_POST['subscript_id'])) {
			$subscrip_id = sanitize_text_field($_POST['subscript_id']);
			if (strpos($subscrip_id, 'sub_') !== false) {
				/* stripe */
				include_once(LISTINGPRO_PLUGIN_PATH . "inc/stripe/stripe-php/init.php");
				\Stripe\Stripe::setApiKey("$secritKey");
				try {
					$subscription = \Stripe\Subscription::retrieve($subscrip_id);
					$subscription->cancel();
				} catch (Exception $e) {
				}
			} else {
				/* paypal */
				lp_cancel_recurring_profile($subscrip_id);
			}


			$uid = get_current_user_id();
			$userSubscriptions = get_user_meta($uid, 'listingpro_user_sbscr', true);
			if (!empty($userSubscriptions)) {
				foreach ($userSubscriptions as $key => $subscription) {
					$subscr_id = $subscription['subscr_id'];
					$subscr_listing_id = $subscription['listing_id'];

					if ($subscr_id == $subscrip_id) {
						$table = 'listing_orders';
						$summary = 'expired';
						$data = array('summary' => $summary);
						$where = array('post_id' => $subscr_listing_id);
						lp_update_data_in_db($table, $data, $where);

						$website_url = site_url();
						$website_name = get_option('blogname');
						$listing_title = get_the_title($subscr_listing_id);
						$listing_url = get_the_permalink($subscr_listing_id);


						unset($userSubscriptions[$key]);
						lp_mail_headers_append();
						$headers[] = 'Content-Type: text/html; charset=UTF-8';
						/* user email */
						$author_obj = get_user_by('id', $uid);
						$user_email = $author_obj->user_email;
						$usubject = $listingpro_options['listingpro_subject_cancel_subscription'];
						$usubject = lp_sprintf2("$usubject", array(
							'website_url' => "$website_url",
							'listing_title' => "$listing_title",
							'listing_url' => "$listing_url",
							'website_name' => "$website_name",
						));
						$ucontent = $listingpro_options['listingpro_content_cancel_subscription'];
						$ucontent = lp_sprintf2("$ucontent", array(
							'website_url' => "$website_url",
							'listing_title' => "$listing_title",
							'listing_url' => "$listing_url",
							'website_name' => "$website_name",
						));
						LP_send_mail($user_email, $usubject, $ucontent, $headers);
						/* admin email */
						$adminemail = get_option('admin_email');
						$asubject = $listingpro_options['listingpro_subject_cancel_subscription_admin'];
						$asubject = lp_sprintf2("$asubject", array(
							'website_url' => "$website_url",
							'listing_title' => "$listing_title",
							'listing_url' => "$listing_url",
							'website_name' => "$website_name",
						));
						$acontent = $listingpro_options['listingpro_content_cancel_subscription_admin'];
						$acontent = lp_sprintf2("$acontent", array(
							'website_url' => "$website_url",
							'listing_title' => "$listing_title",
							'listing_url' => "$listing_url",
							'website_name' => "$website_name",
						));
						LP_send_mail($adminemail, $asubject, $acontent, $headers);
						lp_mail_headers_remove();
					}
				}
			}
			/* removing user meta */
			if (!empty($userSubscriptions)) {
				update_user_meta($uid, 'listingpro_user_sbscr', $userSubscriptions);
			} else {
				delete_user_meta($uid, 'listingpro_user_sbscr');
			}
			/* end removing user meta */
			$response = array('status' => 'success', 'msg' => esc_html__('you have unsubscribed successfully', 'listingpro'));
		} else {
			$response = array('status' => 'fail', 'msg' => esc_html__('something went wrong', 'listingpro'));
		}
		die(json_encode($response));
	}
}

/* ===========================Report Listing OR Review============== */
add_action('wp_ajax_listingpro_report_this_post', 'listingpro_report_this_post');
add_action('wp_ajax_nopriv_listingpro_report_this_post', 'listingpro_report_this_post');
if (!function_exists('listingpro_report_this_post')) {
	function listingpro_report_this_post()
	{

		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		$response = array();
		$resp = '';
		global $listingpro_options;
		$postType = sanitize_text_field($_POST['posttype']);
		$postid = sanitize_text_field($_POST['postid']);
		$reporterID = sanitize_text_field($_POST['reportedby']);

		//new code 2.6.15
		$reportreason = sanitize_text_field($_POST['reportreason']);
		if (empty($reportreason)) {
			$resp = '<span><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>' . esc_html__('Please enter reason!', 'listingpro');
			$response = array('status' => 'fail', 'msg' => $resp);
			die(json_encode($response));
		}
		//end new code 2.6.15

		if (isset($postType) && isset($postid)) {
			if ($postType == "listing") {
				/* for listing post type */
				$Reportedby = array();
				$postReportedby = listing_get_metabox_by_ID('listing_reported_by', $postid);
				if (!empty($postReportedby)) {
					if (strpos($postReportedby, ',') !== false) {
						/* found */
						$Reportedby = explode(",", $postReportedby);
					} else {
						$Reportedby[] = $postReportedby;
					}

					$resSearch = in_array($reporterID, $Reportedby);
					if (!empty($resSearch)) {
						$resp = '<span><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>' . esc_html__('Already reported by you !', 'listingpro');
						$response = array('status' => 'fail', 'msg' => $resp);
						die(json_encode($response));
					} else {
						/* update metaboxes */
						$postReportedcount = listing_get_metabox_by_ID('listing_reported', $postid);
						if (!empty($postReportedcount)) {
							$postReportedcount = $postReportedcount + 1;
						} else {
							$postReportedcount = 1;
						}
						listing_set_metabox('listing_reported', $postReportedcount, $postid);
						$Reportedby[] = $reporterID;
						$newpostReportedby = implode(",", $Reportedby);
						listing_set_metabox('listing_reported_by', $newpostReportedby, $postid);

						//new code 2.6.15
						$postReportedreason = listing_get_metabox_by_ID('listing_reported_reason', $postid);
						$reportreason_arr[] = $reportreason;

						if (!empty($postReportedreason)) {
							$newdata = array_merge($postReportedreason, $reportreason_arr);
							listing_set_metabox('listing_reported_reason', $newdata, $postid);
						} else {
							listing_set_metabox('listing_reported_reason', $reportreason_arr, $postid);
						}

						//end new code 2.6.15

						$resp = '<span><i class="fa fa-check" aria-hidden="true"></i></span>' . esc_html__('Reported Successfully', 'listingpro');
						$response = array('status' => 'success', 'msg' => $resp);

						/* store data in options  */
						if (get_option('lp_reported_listings') !== false) {
							$reportedLisingsArray = array();
							$reportedLisings = get_option('lp_reported_listings');
							if (strpos($reportedLisings, ',') !== false) {
								$reportedLisingsArray = explode(",", $reportedLisings);
							} else {
								$reportedLisingsArray[] = $reportedLisings;
							}
							$reportedLisingsArray[] = $postid;
							$reportedLisings = implode(",", $reportedLisingsArray);
							update_option('lp_reported_listings', $reportedLisings);
						} else {
							$deprecated = null;
							$autoload = 'no';
							$reportedLisings = $postid;
							add_option('lp_reported_listings', $reportedLisings, $deprecated, $autoload);
						}

						/* end store data in options  */

						die(json_encode($response));
					}
				}
				$Reportedby[] = $reporterID;
				$postReportedby = implode(",", $Reportedby);
				listing_set_metabox('listing_reported_by', $postReportedby, $postid);
				listing_set_metabox('listing_reported', '1', $postid);

				//new code 2.6.15
				$postReportedreason = listing_get_metabox_by_ID('listing_reported_reason', $postid);
				$reportreason_arr[] = $reportreason;

				if (!empty($postReportedreason)) {
					$newdata = array_merge($postReportedreason, $reportreason_arr);
					listing_set_metabox('listing_reported_reason', $newdata, $postid);
				} else {
					listing_set_metabox('listing_reported_reason', $reportreason_arr, $postid);
				}

				//end new code 2.6.15

				$resp = esc_html__('Reported Successfully', 'listingpro');
				$response = array('status' => 'success', 'msg' => $resp);
				$have_unread_report = get_option('lp_unread_listing_report');
				if (empty($have_unread_report)) $have_unread_report = 0;
				update_option('lp_unread_listing_report', ($have_unread_report + 1));

				/* store data in options  */
				if (get_option('lp_reported_listings') !== false) {
					$reportedLisingsArray = array();
					$reportedLisings = get_option('lp_reported_listings');
					if (strpos($reportedLisings, ',') !== false) {
						$reportedLisingsArray = explode(",", $reportedLisings);
					} else {
						$reportedLisingsArray[] = $reportedLisings;
					}
					$reportedLisingsArray[] = $postid;
					$reportedLisings = implode(",", $reportedLisingsArray);
					update_option('lp_reported_listings', $reportedLisings);
				} else {
					$deprecated = null;
					$autoload = 'no';
					$reportedLisings = $postid;
					add_option('lp_reported_listings', $reportedLisings, $deprecated, $autoload);
				}

				/* end store data in options  */

				die(json_encode($response));
			}

			if ($postType == "reviews") {
				/* for listing reviews */

				$Reportedby;
				$postReportedby = listing_get_metabox_by_ID('review_reported_by', $postid);
				if (!empty($postReportedby)) {
					if (strpos($postReportedby, ',') !== false) {
						/* found */
						$Reportedby = explode(",", $postReportedby);
					} else {
						$Reportedby[] = $postReportedby;
					}

					$resSearch = in_array($reporterID, $Reportedby);
					if (!empty($resSearch)) {
						$resp = esc_html__('Already reported by you', 'listingpro');
						$response = array('status' => 'fail', 'msg' => $resp);
						die(json_encode($response));
					} else {
						/* update metaboxes */
						$postReportedcount = listing_get_metabox_by_ID('review_reported', $postid);
						if (!empty($postReportedcount)) {
							$postReportedcount = $postReportedcount + 1;
						} else {
							$postReportedcount = 1;
						}
						listing_set_metabox('review_reported', $postReportedcount, $postid);

						$Reportedby[] = $reporterID;
						$newpostReportedby = implode(",", $Reportedby);
						listing_set_metabox('review_reported_by', $newpostReportedby, $postid);

						//new code 2.6.15
						$postReportedreason = listing_get_metabox_by_ID('review_reported_reason', $postid);
						$reportreason_arr[] = $reportreason;

						if (!empty($postReportedreason)) {
							$newdata = array_merge($postReportedreason, $reportreason_arr);
							listing_set_metabox('review_reported_reason', $newdata, $postid);
						} else {
							listing_set_metabox('review_reported_reason', $reportreason_arr, $postid);
						}

						//end new code 2.6.15

						$resp = esc_html__('Reported Successfully', 'listingpro');
						$response = array('status' => 'success', 'msg' => $resp);

						/* store data in options  */
						if (get_option('lp_reported_reviews') !== false) {
							$reportedLisingsArray = array();
							$reportedLisings = get_option('lp_reported_reviews');
							if (strpos($reportedLisings, ',') !== false) {
								$reportedLisingsArray = explode(",", $reportedLisings);
							} else {
								$reportedLisingsArray[] = $reportedLisings;
							}
							$reportedLisingsArray[] = $postid;
							$reportedLisings = implode(",", $reportedLisingsArray);
							update_option('lp_reported_reviews', $reportedLisings);
						} else {
							$deprecated = null;
							$autoload = 'no';
							$reportedLisings = $postid;
							add_option('lp_reported_reviews', $reportedLisings, $deprecated, $autoload);
						}

						/* end store data in options  */
						die(json_encode($response));
					}
				}
				$Reportedby[] = $reporterID;
				$postReportedby = implode(",", $Reportedby);
				listing_set_metabox('review_reported_by', $postReportedby, $postid);
				listing_set_metabox('review_reported', '1', $postid);

				//new code 2.6.15
				$postReportedreason = listing_get_metabox_by_ID('review_reported_reason', $postid);
				$reportreason_arr[] = $reportreason;

				if (!empty($postReportedreason)) {
					$newdata = array_merge($postReportedreason, $reportreason_arr);
					listing_set_metabox('review_reported_reason', $newdata, $postid);
				} else {
					listing_set_metabox('review_reported_reason', $reportreason_arr, $postid);
				}

				//end new code 2.6.15

				$resp = esc_html__('Reported Successfully', 'listingpro');
				$response = array('status' => 'success', 'msg' => $resp);
				$have_unread_report = get_option('lp_unread_review_report');
				if (empty($have_unread_report)) $have_unread_report = 0;
				update_option('lp_unread_review_report', ($have_unread_report + 1));

				/* store data in options  */
				if (get_option('lp_reported_reviews') !== false) {
					$reportedLisingsArray = array();
					$reportedLisings = get_option('lp_reported_reviews');
					if (strpos($reportedLisings, ',') !== false) {
						$reportedLisingsArray = explode(",", $reportedLisings);
					} else {
						$reportedLisingsArray[] = $reportedLisings;
					}
					$reportedLisingsArray[] = $postid;
					$reportedLisings = implode(",", $reportedLisingsArray);
					update_option('lp_reported_reviews', $reportedLisings);
				} else {
					$deprecated = null;
					$autoload = 'no';
					$reportedLisings = $postid;
					add_option('lp_reported_reviews', $reportedLisings, $deprecated, $autoload);
				}

				/* end store data in options  */

				die(json_encode($response));
			}
		} else {
			$resp = esc_html__('Something Wrong', 'listingpro');
			$response = array('status' => 'fail', 'msg' => $resp);
		}
		die(json_encode($response));
	}
}

/* ================= for preview popu================= */

if (!function_exists('quick_preivew_cb')) {
	function quick_preivew_cb()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_REQUEST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}

		$output = '';

		$LPpostID    =   sanitize_text_field($_REQUEST['LPpostID']);
		$data_return    =   array();

		$post_content = get_post($LPpostID);
		$post_content = $post_content->post_content;

		$post_content = preg_replace('/\[[^\]]*\]/', '', $post_content);
		$post_content = wp_filter_nohtml_kses($post_content);
		$post_content   =   mb_substr($post_content, 0, 230);


		$deafaultFeatImg = lp_default_featured_image_listing();
		if (has_post_thumbnail($LPpostID)) {
			require_once(THEME_PATH . "/include/aq_resizer.php");
			$img_url = wp_get_attachment_image_src(get_post_thumbnail_id($LPpostID), 'full');
			if (!empty($img_url[0])) {
				$imgurl = aq_resize($img_url[0], '650', '300', true, true, true);
				$imgSrc = $imgurl;
			} else {
				$imgSrc =   'https://placehold.co/650x300';
			}
		} elseif (!empty($deafaultFeatImg)) {
			$imgSrc = $deafaultFeatImg;
		} else {
			$imgSrc =   'https://placehold.co/650x300';
		}
		$permalink  =   get_the_permalink($LPpostID);
		$ptitle     =   mb_substr(get_the_title($LPpostID), 0, 40);
		$phone = listing_get_metabox_by_ID('phone', $LPpostID);
		$gAddress = listing_get_metabox_by_ID('gAddress', $LPpostID);
		$adress_markup  =   '';
		if (!empty($gAddress)) {
			$adress_markup  =   '<span class="cat-icon">
                   <img class="icon icons8-Food" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAABv0lEQVRoge1ZUbGDMBA8CUhAAhKQUAlIqILsOkBCJSABCUhAAhJ4Hw1vGB4tB82R9A07c7+Z3dxmuQSRCxcuBAPJwjlHAC2ADsDgqwfQOOdIsojN8w9Ilp7wqKyOZBWbt5AsdhJfExKnI37Xhw/ITzWQLL+V/G+dJoJkHpr8rBP2dsIzYUKTn6o3JU/yZkh+xNNKlZkAfJY42hpMyPvI1JJoSZYkC5K571y7owt5cAH+C6shUJPM1tYAUGvWcM4xuAAAjXLnV8nP1tF0orUQ0Ctaf9taRxkE4c+BUkCuEJDFErD58QooYLQQsBmhSguVCgGdhQBNgmwePijDILgAbYy+i0CSd80aAOrgAnaOEd00XZLMvG00Oz8CRuOE9vCFKJMvsYgIgMcJAhoT8iIiJKsTdr+yFGBto8HMPhNga6OHKXkR0y6cc6UUMetC+Ox/BYMu2Ht/CSgvJ8nt/gTfhT4A+X7rEmQpQjNZvi3NBGsK7JhxkrDOEt5KR17q4llniSNWim6dJbAvleJbZw1QPpkkY50lFNGaju9fwT+/r5E//0fGUawd6uQO7Rbmd2iS99h8DsG/QqSZOBcu/BP8AL+XHO7G8elbAAAAAElFTkSuQmCC" alt="cat-icon">
                  </span>
                  <span class="text gaddress">' . $gAddress . '</span>';
		}
		$rating = get_post_meta($LPpostID, 'listing_rate', true);
		$rate = $rating;
		$adStatus = apply_filters('lp_get_ad_status', '', $LPpostID);

		$CHeckAd = '';
		if ($adStatus == 'active') {
			$CHeckAd = '<span class="listing-pro">' . esc_html__('Ad', 'listingpro') . '</span>';
		}
		$claimed_section = listing_get_metabox_by_ID('claimed_section', $LPpostID);
		$claim = '';
		if ($claimed_section == 'claimed') {
			$claim = '<span class="verified simptip-position-top simptip-movable" data-tooltip="' . esc_html__('Claimed', 'listingpro') . '"><i class="fa fa-check"></i> ' . esc_html__('Claimed', 'listingpro') . '</span>';
		} elseif ($claimed_section == 'not_claimed') {
			$claim = '';
		}
		$pricey =   listingpro_price_dynesty_text($LPpostID);
		$cats_markup    =   '';
		$cats = get_the_terms($LPpostID, 'listing-category');
		if (!empty($cats)) {
			$catCount = 1;
			foreach ($cats as $cat) {
				if ($catCount == 1) {
					$category_image = listing_get_tax_meta($cat->term_id, 'category', 'image');
					if (!empty($category_image)) {
						if (hasFontAwesomeIconClass($category_image)) {
							$cats_markup  .= '<span class="cat-icon"><i class="icon icons-search-cat ' . $category_image . '"></i></span>';
						} else {
							$cats_markup  .= '<span class="cat-icon"><img class="icon icons8-Food" src="' . $category_image . '" alt="cat-icon"></span>';
						}
					}
					$term_link = get_term_link($cat);
					$cats_markup    .=  '<a href="' . $term_link . '">
                       ' . $cat->name . '
                      </a>';
				}
				$catCount++;
			}
		}

		$feaImg =   '';

		$openStatus = listingpro_check_time($LPpostID);
		$post_content   =   '<div class="this">' . $post_content . '</div>';
		$data_return['noreview'] = esc_html__('0 Reviews', 'listingpro');
		$data_return['ad'] = esc_html__('Ad', 'listingpro');
		$data_return['phone'] = $phone;
		$data_return['gAddress'] = $gAddress;
		$data_return['post_content'] = $post_content;
		$data_return['post_thumb'] = $imgSrc;
		$data_return['permalink'] = $permalink;
		$data_return['ptitle'] = $ptitle;
		$data_return['adStatus'] = $adStatus;
		$data_return['rate'] = $rate;
		$data_return['pricey'] = $pricey;
		$data_return['cats_markup'] = $cats_markup;
		$data_return['adress_markup'] = $adress_markup;
		$data_return['openStatus'] = $openStatus;
		$data_return['postid']  =   $LPpostID;
		$data_return['claim']  =   $claim;
		$data_return['post_thumb'] = $imgSrc;
		$output .= '
            <div class="col-md-6 lp-insert-data">
                <div class="lp-grid-box-thumb">
                    <div class="slide">
                        <img src="' . $data_return['post_thumb'] . '" alt="' . $data_return['ptitle'] . '">
                    </div>
                </div>
                <div class="lp-grid-desc-container lp-border clearfix">
                    <div class="lp-grid-box-description ">
                        <div class="lp-grid-box-left pull-left">
                            <h4 class="lp-h4">
                                <a href="' . $data_return['permalink'] . '">';
		if ($data_return['adStatus'] == 'active') {
			$output .= $CHeckAd;
		}
		$output .= $data_return['ptitle'];
		$output .= $claim;
		$output .= '           </a>
                            </h4>
                            <ul>
                                <li>';

		if ($data_return['rate'] == '') {
			$output .= $data_return['noreview'];
		} else {
			$output .= '<span class="rate">' . $data_return['rate'] . '<sup>/5</sup></span>';
		}

		$output .= '      </li>
                                <li class="middle">' . $data_return['pricey'] . '</li>
                                <li>' . $data_return['cats_markup'] . '</li>
                                <li><a href="tel:' . $data_return['phone'] . '">' . $data_return['phone'] . '</a></li>
                            </ul>
                            <div class="lp-grid-desc">
                                <p>' . $data_return['post_content'] . '</p>
                            </div>
                        </div>
                        <div class="lp-grid-box-right pull-right"></div>
                    </div>
                    <div class="lp-grid-box-bottom">
                        <div class="pull-left">
                            ' . $data_return['adress_markup'] . '
                        </div>
                        <div class="pull-right">
                            <a href="#" class="status-btn">' . $data_return['openStatus'] . '</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="quickmap' . $LPpostID . '" class="quickmap"></div>
            </div>';


		die(json_encode($output));
	}
}

add_action('wp_ajax_quick_preivew_cb', 'quick_preivew_cb');
add_action('wp_ajax_nopriv_quick_preivew_cb', 'quick_preivew_cb');


/* ================================ajax callback for chart======================================= */
/* ================================ajax callback for chart======================================= */
if (!function_exists('listingpro_show_bar_chart')) {
	function listingpro_show_bar_chart()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		$dataResponseSend = array();
		$dataResponse = array();
		$currentUserId = get_current_user_id();
		$type = sanitize_text_field($_POST['type']);
		$duration = sanitize_text_field($_POST['duration']);
		$dayNow = date("l");
		$yearNow = date("Y");
		$monthNow = date("F");
		//Updated by abbas
		$lpTodayDate = date(get_option('date_format'));
		//End Updated by abbas
		$lpTodayTime = strtotime($lpTodayDate);
		$opt_name = $currentUserId . '_' . $type . '_' . $yearNow;
		$lpUserOpt = get_option($opt_name);
		if ($type == "view") {
			$table = "listing_stats_views";
		} elseif ($type == "reviews") {
			$table = "listing_stats_reviews";
		} elseif ($type == "leads") {
			$table = "listing_stats_leads";
		}
		$lpdataoverAllcount = 0;

		if (!empty($type)) {
			/* weekly */
			$condition = "user_id='$currentUserId' AND action_type='$type'";
			$getRow = lp_get_data_from_db($table, '*', $condition);
			if ($duration == "weekly") {

				$lpWeekeArray = lp_get_days_of_week($lpTodayDate);
				if (!empty($lpWeekeArray)) {
					foreach ($lpWeekeArray as $singleDay) {
						$lpdatacount = 0;
						if (!empty($getRow)) {

							foreach ($getRow as $indx => $val) {
								$datDta  = $val->month;
								$datDta = unserialize($datDta);
								$ndatDta = $datDta;
								if (!empty($datDta)) {
									foreach ($datDta as $ind => $singleData) {
										$savedDate = $singleData['date'];
										$savedcount = $singleData['count'];
										if (date('Y-m-d', $savedDate) == date('Y-m-d', $singleDay)) {
											$lpdatacount = $lpdatacount + $savedcount;
										}
									}
								}
							}

							if (!empty($lpdatacount)) {
								$lpdataoverAllcount = $lpdataoverAllcount + $lpdatacount;
							}

							/* foreach($getRow as $singleRow){
                                if(isset($singleRow->date)){
                                    $singleRowDate = $singleRow->date;
                                    if($singleRowDate==$singleDay){
                                        $singleRowCount = $singleRow->counts;
                                        $lpdatacount = $lpdatacount+$singleRowCount;
                                        if(!empty($lpdatacount)){
                                            $lpdataoverAllcount = $lpdataoverAllcount + $lpdatacount;
                                        }
                                    }
                                }
                            } */
						}

						$dataResponse[] = array(
							'x' => date_i18n("l", $singleDay),
							'y' => $lpdatacount,
						);
					}
				}
			}
			/* montly */
			if ($duration == "monthly") {
				$monthNo = date("m");
				$yearNo = date("Y");

				//$condition = "user_id='$currentUserId' AND action_type='$type' AND MONTH(FROM_UNIXTIME(date))='$monthNo'";
				$condition = "user_id='$currentUserId' AND action_type='$type'";
				$getRow = lp_get_data_from_db($table, '*', $condition);
				$daysOfMonth = lp_get_days_of_month($monthNo, $yearNo);
				if (!empty($daysOfMonth)) {
					$count = 1;
					foreach ($daysOfMonth as $singleDay) {
						$lpdatacount = 0;
						if (!empty($getRow)) {

							foreach ($getRow as $indx => $val) {
								$datDta  = $val->month;
								$datDta = unserialize($datDta);
								$ndatDta = $datDta;
								if (!empty($datDta)) {
									foreach ($datDta as $ind => $singleData) {
										$savedDate = $singleData['date'];
										$savedcount = $singleData['count'];
										if (date('Y-m-d', $savedDate) == date('Y-m-d', $singleDay)) {
											$lpdatacount = $lpdatacount + $savedcount;
										}
									}
								}
							}
							if (!empty($lpdatacount)) {
								$lpdataoverAllcount = $lpdataoverAllcount + $lpdatacount;
							}

							/* foreach($getRow as $singleRow){
                                if(isset($singleRow->date)){
                                    $singleRowDate = $singleRow->date;
                                    if($singleRowDate==$singleDay){
                                        $singleRowCount = $singleRow->counts;
                                        $lpdatacount = $lpdatacount+$singleRowCount;
                                        if(!empty($lpdatacount)){
                                            $lpdataoverAllcount = $lpdataoverAllcount + $lpdatacount;
                                        }
                                    }
                                }
                            } */
						}
						$dataResponse[] = array(
							'x' => $count,
							'y' => $lpdatacount,
						);

						$count++;
					}
				}
			}

			/* yearly */
			if ($duration == "yearly") {
				$monthNo = date("m");
				$yearNo = date("Y");
				$condition = "user_id='$currentUserId' AND action_type='$type' AND YEAR(FROM_UNIXTIME(date))='$yearNo'";
				$getRow = lp_get_data_from_db($table, '*', $condition);
				$allMonths = lp_get_all_months();
				if (!empty($allMonths)) {
					foreach ($allMonths as $singleMonth) {
						$lpdatacount = 0;
						if (!empty($getRow)) {
							foreach ($getRow as $singleRow) {
								if (isset($singleRow->date)) {
									$singleRowDate = $singleRow->date;
									$thisMonth = date('F', $singleRowDate);
									if ($thisMonth == $singleMonth) {
										$singleRowCount = $singleRow->counts;
										$lpdatacount = $lpdatacount + $singleRowCount;
										if (!empty($lpdatacount)) {
											$lpdataoverAllcount = $lpdataoverAllcount + $lpdatacount;
										}
									}
								}
							}
						}


						if (empty($lpdatacount)) {
							$lpdatacount = 0;
						}
						$dataResponse[] = array(
							'x' => $singleMonth,
							'y' => $lpdatacount,
						);
					}
				}
			}
		}
		$resp = '';
		if ($duration == 'weekly') {
			$resp = esc_html__('In Last Week', 'listingpro');
		} elseif ($duration == 'monthly') {
			$resp = esc_html__('In Last Month', 'listingpro');
		} elseif ($duration == 'yearly') {
			$resp = esc_html__('In Last Year', 'listingpro');
		}
		$dataResponseSend['resp'] = $resp;
		$dataResponseSend['counts'] = $lpdataoverAllcount;
		$dataResponseSend['data'] = $dataResponse;
		exit(json_encode($dataResponseSend));
	}
}

add_action('wp_ajax_listingpro_show_bar_chart', 'listingpro_show_bar_chart');


/* ===================== function lp_count_clicks_for_campaigns======================= */

if (!function_exists('lp_count_clicks_for_campaigns')) {

	function lp_count_clicks_for_campaigns($listing_id, $adID, $type, $redirectURl = '')
	{

		$typeofcampaign = listing_get_metabox_by_ID('ads_mode', $adID);
		if (empty($typeofcampaign)) {
			$typeofcampaign = lp_theme_option('listingpro_ads_campaign_style');
		} else {
			$typeofcampaign = 'ads' . $typeofcampaign;
		}

		if ($typeofcampaign == 'adsperclick') {

			$listingTID = listing_get_metabox_by_ID('campaign_id', $adID);
			if (empty($listingTID)) {
				$listingTID = listing_get_metabox_by_ID('ads_listing', $adID);
			}
			if ($listingTID == $listing_id) {

				$spotLightAd = null;
				$sideAd = null;
				$detailAd = null;
				$ad_types = listing_get_metabox_by_ID('ad_type', $adID);

				$min_ad_price = 0;
				if (!empty($ad_types)) {
					foreach ($ad_types as $advalue) {
						if ($advalue == "lp_random_ads") {
							$spotLightAd = lp_theme_option('lp_random_ads_pc');
							if ($min_ad_price == 0 || $spotLightAd < $min_ad_price) {
								$min_ad_price = $spotLightAd;
							}
						} elseif ($advalue == "lp_detail_page_ads") {
							$detailAd = lp_theme_option('lp_detail_page_ads_pc');
							if ($min_ad_price == 0 || $detailAd < $min_ad_price) {
								$min_ad_price = $detailAd;
							}
						} elseif ($advalue == "lp_top_in_search_page_ads") {
							$sideAd = lp_theme_option('lp_top_in_search_page_ads_pc');
							if ($min_ad_price == 0 || $sideAd < $min_ad_price) {
								$min_ad_price = $sideAd;
							}
						}
					}
				}

				$get_remaining_credits = listing_get_metabox_by_ID('remaining_balance', $adID);

				$get_clicks_credits = listing_get_metabox_by_ID('click_performed', $adID);
				$thisCharge = lp_theme_option($type);
				if (floatval($get_clicks_credits) == intval($get_clicks_credits)) {
					$get_clicks_credits = intval($get_clicks_credits);
				} else {
					$get_clicks_credits = floatval($get_clicks_credits);
				}

				if (floatval($thisCharge) == intval($thisCharge)) {
					$thisCharge = intval($thisCharge);
				} else {
					$thisCharge = floatval($thisCharge);
				}

				if ($get_remaining_credits >= $thisCharge) {

					$remingCredits = $get_remaining_credits - $thisCharge;

					listing_set_metabox('remaining_balance', $remingCredits, $adID);

					if (empty($remingCredits)) {
						//means credit finished
						update_post_meta($listing_id, 'campaign_status', 'inactive');
						$this_post = array(
							'ID'           => $adID,
							'post_status'   => 'inactive',
						);
						wp_update_post($this_post);
						$currentdate = date("d-m-Y");
						update_post_meta($adID, 'campain_expire_date', $currentdate);

						if (!empty($ad_types)) {
							foreach ($ad_types as $key => $value) {
								$spotPrice = lp_theme_option($value . '_pc');
								if ($remingCredits < $spotPrice) {
									unset($ad_types[$key]);
									listing_set_metabox('ad_type', $ad_types, $adID);
									delete_post_meta($listing_id, $value, 'active');
								}
							}
						}
					} else {
						if (!empty($ad_types)) {
							foreach ($ad_types as $key => $value) {
								$spotPrice = lp_theme_option($value . '_pc');
								if ($remingCredits < $spotPrice && $spotPrice != $min_ad_price) {
									unset($ad_types[$key]);
									listing_set_metabox('ad_type', $ad_types, $adID);
									delete_post_meta($listing_id, $value, 'active');
								}
							}
						}
					}
					if (!empty($get_clicks_credits)) {
						$get_clicks_credits++;
					} else {
						$get_clicks_credits = 1;
					}
					listing_set_metabox('click_performed', $get_clicks_credits, $adID);
				} else {
					if ($get_remaining_credits > 0) {
						if (!empty($get_clicks_credits)) {
							$get_clicks_credits++;
						} else {
							$get_clicks_credits = 1;
						}
						listing_set_metabox('click_performed', $get_clicks_credits, $adID);
					}

					if (!empty($ad_types)) {
						foreach ($ad_types as $key => $value) {
							$spotPrice = lp_theme_option($value . '_pc');
							$remingCredits = listing_get_metabox_by_ID('remaining_balance', $adID);
							if ($remingCredits < $spotPrice) {
								unset($ad_types[$key]);
								listing_set_metabox('ad_type', $ad_types, $adID);
								delete_post_meta($listing_id, $value, 'active');
							}
						}
					}
					update_post_meta($listing_id, 'campaign_status', 'inactive');
					$this_post = array(
						'ID'           => $adID,
						'post_status'   => 'inactive',
					);
					wp_update_post($this_post);
					$currentdate = date("d-m-Y");
					update_post_meta($adID, 'campain_expire_date', $currentdate);
					listing_set_metabox('remaining_balance', '0', $adID);
				}
			}

			if ($redirectURl != '') {
				wp_redirect($redirectURl);
				exit;
			}
		}
	}
}


/* ==========================for contact form=========================== */
if (!function_exists('lp_ajax_contact_submit')) {
	function lp_ajax_contact_submit()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		$cpFailedMessage = lp_theme_option('cp-failed-message');
		$cpsuccessMessage = lp_theme_option('cp-success-message');
		$uname = sanitize_text_field($_POST['uname']);
		$uemail = sanitize_email($_POST['uemail']);
		$usubject = sanitize_text_field($_POST['usubject']);
		$umessage = sanitize_text_field($_POST['umessage']);

		$errorMSG = '';
		$successMSG = '';

		$response = array();

		$enableCaptcha = false;
		$processContact = true;
		if (isset($_POST['token'])) {
			if (!empty($_POST['token'])) {
				$enableCaptcha = true;
			} else {
				$processContact = false;
			}
		} else {
			$enableCaptcha = false;
			$processContact = true;
		}

		$keyResponse = '';

		if ($enableCaptcha == true) {
			if (class_exists('cridio_Recaptcha')) {
				$keyResponse = cridio_Recaptcha_Logic::is_recaptcha_valid(sanitize_text_field($_POST['token']), sanitize_text_field($_POST['recaptha-action']));
				if ($keyResponse == false) {
					$processContact = false;
				} else {
					$processContact = true;
				}
			}
		}
		if ($processContact == true) {

			if (empty($uname) || empty($uemail) || empty($umessage)) {
				if (empty($uname) || empty($uemail) || empty($umessage)) {
					$errorMSG .= $cpFailedMessage;
					$response = array(
						'status' => 'error',
						'msg' => $errorMSG
					);
				}
			} else {
				$successMSG = $cpsuccessMessage;

				$admin_email = '';
				$admin_email = get_option('admin_email');
				if (empty($usubject)) {
					$usubject = esc_html__('Contact Us Email', 'listingpro');
				}
				$formated_mail_content = '<h3>' . esc_html__("Details : ", "listingpro") . '</h3>';
				$formated_mail_content .= '<p>' . esc_html__("Name : ", "listingpro") . $uname . '</p>';
				$formated_mail_content .= '<p>' . esc_html__("Email : ", "listingpro") . $uemail . '</p>';
				$formated_mail_content .= '<p>' . esc_html__("Message : ", "listingpro") . $umessage . '</p>';
				lp_mail_headers_append();
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				//$headers[] = 'From: '.$uname.' <'.$uemail.'>';
				LP_send_mail($admin_email, $usubject, $formated_mail_content, $headers);
				lp_mail_headers_remove();
				$response = array(
					'status' => 'success',
					'msg' => $successMSG
				);
			}
		} else {
			$errorMSG .= esc_html__('Please check captcha.', 'listingpro');
			$response = array(
				'status' => 'error',
				'msg' => $errorMSG
			);
		}

		exit(json_encode($response));
	}
}
add_action('wp_ajax_lp_ajax_contact_submit', 'lp_ajax_contact_submit');
add_action('wp_ajax_nopriv_lp_ajax_contact_submit', 'lp_ajax_contact_submit');

/* ==================show sorted review============== */
if (!function_exists('lp_show_sorted_reivews')) {
	function lp_show_sorted_reivews()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		$returnHTML = null;
		$orderby = 'date';
		$metaQuery = array();
		$order = sanitize_text_field($_POST['order_by']);
		$listing_id = sanitize_text_field($_POST['listing_id']);
		$detailStyle = lp_theme_option('lp_detail_page_styles');

		if ($order == 'listing_rate' || $order == 'listing_rate_lowest') {

			$metaQuery = array(
				array(
					'key' => 'rating',
					'compare' => 'EXIST'
				)
			);

			if ($order == 'listing_rate_lowest') {

				$orderby = array(
					'rating' => 'ASC',
				);
			} else {
				$orderby = array(
					'rating' => 'DESC',
				);
			}
			$order = '';
		}

		$review_idss = listing_get_metabox_by_ID('reviews_ids', $listing_id);
		$review_ids = '';
		if (!empty($review_idss)) {
			$review_ids = explode(",", $review_idss);
		}
		if (!empty($review_ids) && is_array($review_ids)) {
			$review_ids = array_unique($review_ids);
		}

		if ($detailStyle == 'lp_detail_page_styles1' || $detailStyle == 'lp_detail_page_styles2') {
			//detail style 1

			ob_start();

			if (!empty($review_ids) && count($review_ids) > 0) {
				$review_ids = array_reverse($review_ids, true);
				//foreach( $review_ids as $key=>$review_id ){
				$args = array(
					'post_type'  => 'lp-reviews',
					'orderby'    => $orderby,
					'order'      => $order,
					'meta_query' => $metaQuery,
					'post__in'	 => $review_ids,
					'post_status'	=> 'publish',
					'posts_per_page'	=> -1
				);

				$query = new WP_Query($args);
				if ($query->have_posts()) {
					echo '';
					while ($query->have_posts()) {
						$query->the_post();
						global $post;
						echo '<article class="review-post">';
						// moin here strt
						$review_reply = '';
						$review_reply = listing_get_metabox_by_ID('review_reply', get_the_ID());
						$review_reply_time = listing_get_metabox_by_ID('review_reply_time', get_the_ID());

						// moin here ends

						$rating = listing_get_metabox_by_ID('rating', get_the_ID());
						$rating = apply_filters('lp_rating_number_format', $rating);
						$rate = $rating;
						$gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);
						$author_id = $post->post_author;

						$author_avatar_url = get_user_meta($author_id, "listingpro_author_img_url", true);
						$avatar;
						if (!empty($author_avatar_url)) {
							$avatar =  $author_avatar_url;
						} else {
							$avatar_url = listingpro_get_avatar_url($author_id, $size = '94');
							$avatar =  $avatar_url;
						}
						$user_reviews_count = count_user_posts($author_id, 'lp-reviews');
?>
						<figure>
							<div class="review-thumbnail">
								<a href="<?php echo get_author_posts_url($author_id); ?>">
									<img src="<?php echo esc_attr($avatar); ?>" alt="image">
								</a>
							</div>
							<figcaption>
								<h4><a href="<?php echo get_author_posts_url($author_id); ?>"><?php the_author(); ?></a></h4>
								<p><i class="fa fa-star"></i> <?php echo esc_attr($user_reviews_count); ?> <?php esc_html_e('Reviews', 'listingpro'); ?></p>


							</figcaption>
						</figure>
						<section class="details">
							<div class="top-section">
								<h3><?php the_title(); ?></h3>
								<time><?php echo get_the_time('F j, Y g:i a'); ?></time>
								<div class="review-count">
									<?php
									if ($lp_multi_rating_state == 1 && !empty($lp_multi_rating_state)) {
										echo '<a href="#" data-rate-box="multi-box-' . $post->ID . '" class="open-multi-rate-box"><i class="fa fa-chevron-down" aria-hidden="true"></i>' . esc_html__('View All', 'listingpro') . '</a>';
										$post_rating_data   =   get_post_meta($post->ID, 'lp_listingpro_options', true);
									?>
										<div class="lp-multi-star-wrap" id="multi-box-<?php echo esc_attr($post->ID); ?>">
											<?php
											foreach ($lp_multi_rating_fields as $k => $v) {
												$field_rating_val   =   '';
												if (isset($post_rating_data[$k])) {
													$field_rating_val   =   $post_rating_data[$k];
												}
											?>
												<div class="lp-multi-star-field rating-with-colors <?php echo review_rating_color_class($field_rating_val); ?>">
													<label><?php echo esc_attr($v['label']);  ?></label>
													<p>
														<i class="fa <?php if ($field_rating_val > 0) {
																			echo 'fa-star';
																		} else {
																			echo 'fa-star-o';
																		} ?>" aria-hidden="true"></i>
														<i class="fa <?php if ($field_rating_val > 1) {
																			echo 'fa-star';
																		} else {
																			echo 'fa-star-o';
																		} ?>" aria-hidden="true"></i>
														<i class="fa <?php if ($field_rating_val > 2) {
																			echo 'fa-star';
																		} else {
																			echo 'fa-star-o';
																		} ?>" aria-hidden="true"></i>
														<i class="fa <?php if ($field_rating_val > 3) {
																			echo 'fa-star';
																		} else {
																			echo 'fa-star-o';
																		} ?>" aria-hidden="true"></i>
														<i class="fa <?php if ($field_rating_val > 4) {
																			echo 'fa-star';
																		} else {
																			echo 'fa-star-o';
																		} ?>" aria-hidden="true"></i>
													</p>
												</div>
											<?php
											}
											?>
										</div>
									<?php
									}
									?>
									<?php

									$review_rating = listing_get_metabox_by_ID('rating', get_the_ID());
									$review_rating = apply_filters('lp_rating_number_format', $review_rating);

									?>
									<div class="rating rating-with-colors <?php echo review_rating_color_class($review_rating); ?>">
										<?php
										listingpro_ratings_stars('rating', get_the_ID());
										?>
									</div>
									<?php echo lp_cal_listing_rate(get_the_ID(), 'lp_review', true); ?>
								</div>
							</div>
							<div class="content-section">
								<p><?php the_content(); ?></p>
								<?php if (!empty($gallery)) { ?>
									<div class="images-gal-section">
										<div class="row">
											<div class="img-col review-img-slider">
												<?php
												//image gallery
												$imagearray = explode(',', $gallery);
												foreach ($imagearray as $image) {
													$imgGal = wp_get_attachment_image($image, 'listingpro-review-gallery-thumb', '', '');
													$imgGalFull = wp_get_attachment_image_src($image,  'full');
													echo '<a class="galImgFull" href="' . $imgGalFull[0] . '">' . $imgGal . '</a>';
												}
												?>
											</div>
										</div>
									</div>
								<?php } ?>
								<?php
								$interests = '';
								$Lols = '';
								$loves = '';
								$interVal = esc_html__('Interesting', 'listingpro');
								$lolVal = esc_html__('Lol', 'listingpro');
								$loveVal = esc_html__('Love', 'listingpro');

								$interests = listing_get_metabox_by_ID('review_' . $interVal . '', get_the_ID());
								$Lols = listing_get_metabox_by_ID('review_' . $lolVal . '', get_the_ID());
								$loves = listing_get_metabox_by_ID('review_' . $loveVal . '', get_the_ID());


								if (empty($interests)) {
									$interests = 0;
								}
								if (empty($Lols)) {
									$Lols = 0;
								}
								if (empty($loves)) {
									$loves = 0;
								}
								?>
								<div class="bottom-section">
									<form action="#">
										<span><?php echo esc_html__('Was this review ...?', 'listingpro'); ?></span>
										<ul>
											<li>
												<a class="instresting reviewRes" href="#" data-reacted="<?php echo esc_html__('You already reacted', 'listingpro'); ?>" data-restype='<?php echo esc_attr($interVal); ?>' data-id='<?php the_ID(); ?>' data-score='<?php echo esc_attr($interests); ?>'>
													<i class="fa fa-thumbs-o-up"></i><?php echo esc_html__('Interesting', 'listingpro'); ?><span class="interests-score"><?php if (!empty($interests)) echo esc_attr($interests); ?></span>
													<span class="lp_state"></span>
												</a>

											</li>
											<li>
												<a class="lol reviewRes" href="#" data-reacted="<?php echo esc_html__('You already reacted', 'listingpro'); ?>" data-restype='<?php echo esc_attr($lolVal); ?>' data-id='<?php the_ID(); ?>' data-score='<?php echo esc_attr($Lols); ?>'>
													<i class="fa fa-smile-o"></i><?php echo esc_html__('Lol', 'listingpro'); ?><span class="interests-score"><?php if (!empty($Lols)) echo esc_attr($Lols); ?></span>
													<span class="lp_state"></span>
												</a>

											</li>
											<li>
												<a class="love reviewRes" href="#" data-reacted="<?php echo esc_html__('You already reacted', 'listingpro'); ?>" data-restype='<?php echo esc_attr($loveVal); ?>' data-id='<?php the_ID(); ?>' data-id='<?php the_ID(); ?>' data-score='<?php echo esc_attr($loves); ?>'>
													<i class="fa fa-heart-o"></i><?php echo esc_html__('Love', 'listingpro'); ?><span class="interests-score"><?php if (!empty($loves)) echo esc_attr($loves); ?></span>
													<span class="lp_state"></span>
												</a>

											</li>
											<?php
											if ($showReport == true && is_user_logged_in()) { ?>
												<li id="lp-report-review">
													<a data-postid="<?php echo get_the_ID(); ?>" data-reportedby="<?php echo esc_attr($currentUserId); ?>" data-posttype="reviews" href="#" id="lp-report-this-review" class="report"><i class="fa fa-flag" aria-hidden="true"></i><?php esc_html_e('Report', 'listingpro'); ?></a>
												</li>
											<?php } ?>
										</ul>
									</form>
								</div>
							</div>
						</section>

						<?php if (!empty($review_reply)) { ?>
							<section class="details detail-sec">
								<div class="owner-response">
									<h3><?php esc_html_e('Owner Response', 'listingpro'); ?></h3>
									<?php
									if (!empty($review_reply_time)) { ?>
										<time><?php echo date_i18n("F j, Y h:i:s a", strtotime($review_reply_time)); ?></time>
									<?php } ?>
									<p><?php echo esc_attr($review_reply); ?></p>

								</div>
							</section>
						<?php } ?>
						<!-- moin here ends-->
					<?php
						echo '</article>';
					}
					echo '';
					wp_reset_postdata();
				} else {
				}
				//}

			}
			$returnHTML = ob_get_contents();
			ob_end_clean();
		} else {
			//for other styles

			ob_start();

			if (!empty($review_ids) && count($review_ids) > 0) {
				$review_ids = array_reverse($review_ids, true);
				//foreach( $review_ids as $key=>$review_id ){
				$args = array(
					'post_type'  => 'lp-reviews',
					'orderby'    => $orderby,
					'order'      => $order,
					'meta_query' => $metaQuery,
					'post__in'	 => $review_ids,
					'post_status'	=> 'publish',
					'posts_per_page'	=> -1
				);

				$query = new WP_Query($args);
				if ($query->have_posts()) {
					echo '';
					while ($query->have_posts()) {
						$query->the_post();
						global $post;


						$review_reply = '';

						$review_reply = listing_get_metabox_by_ID('review_reply', get_the_ID());


						$review_reply_time = listing_get_metabox_by_ID('review_reply_time', get_the_ID());

						// moin here ends



						$rating = listing_get_metabox_by_ID('rating', get_the_ID());
						$rating = apply_filters('lp_rating_number_format', $rating);
						update_post_meta(get_the_ID(), 'rating', $rating);

						$rate = $rating;

						$gallery = get_post_meta(get_the_ID(), 'gallery_image_ids', true);

						$author_id = $post->post_author;



						$author_avatar_url = get_user_meta($author_id, "listingpro_author_img_url", true);

						$avatar = '';

						if (!empty($author_avatar_url)) {

							$avatar =  $author_avatar_url;
						} else {

							$avatar_url = listingpro_get_avatar_url($author_id, $size = '94');

							$avatar =  $avatar_url;
						}

						$user_reviews_count = count_user_posts($author_id, 'lp-reviews');





						$interests = '';

						$Lols = '';

						$loves = '';

						$interVal = esc_html__('Interesting', 'listingpro');

						$lolVal = esc_html__('Lol', 'listingpro');

						$loveVal = esc_html__('Love', 'listingpro');



						$interests = listing_get_metabox_by_ID('review_' . $interVal . '', get_the_ID());

						$Lols = listing_get_metabox_by_ID('review_' . $lolVal . '', get_the_ID());

						$loves = listing_get_metabox_by_ID('review_' . $loveVal . '', get_the_ID());





						if (empty($interests)) {

							$interests = 0;
						}

						if (empty($Lols)) {

							$Lols = 0;
						}

						if (empty($loves)) {

							$loves = 0;
						}

						$reacted_msg    =   esc_html__('You already reacted', 'listingpro');

						$rating_num_bg  =   '';

						$rating_num_clr  =   '';



						if ($rating < 2) {
							$rating_num_bg  =   'num-level1';
							$rating_num_clr  =   'level1';
						}
						if ($rating < 3) {
							$rating_num_bg  =   'num-level2';
							$rating_num_clr  =   'level2';
						}
						if ($rating < 4) {
							$rating_num_bg  =   'num-level3';
							$rating_num_clr  =   'level3';
						}
						if ($rating >= 4) {
							$rating_num_bg  =   'num-level4';
							$rating_num_clr  =   'level4';
						}



					?>

						<div class="lp-listing-review">

							<div class="lp-review-left">

								<div class="lp-review-thumb">
									<a href="<?php echo get_author_posts_url($author_id); ?>">
										<img src="<?php echo esc_attr($avatar); ?>" alt="image">
									</a>
								</div>

								<span class="lp-review-name"><a href="<?php echo get_author_posts_url($author_id); ?>"><?php the_author(); ?></a></span>

								<span class="lp-review-count"><i class="fa fa-star" aria-hidden="true"></i> <?php echo esc_attr($user_reviews_count); ?> <?php esc_html_e('Reviews', 'listingpro'); ?></span>



							</div>

							<div class="lp-review-right">

								<div class="lp-review-right-top">

									<h3><?php the_title(); ?></h3>

									<time><?php echo get_the_time('F j, Y g:i a'); ?></time>

									<div class="lp-review-stars">

										<?php

										if ($lp_multi_rating_state == 1 && !empty($lp_multi_rating_state)) {

											echo '<a href="#" data-rate-box="multi-box-' . $post->ID . '" class="open-multi-rate-box"><i class="fa fa-chevron-down" aria-hidden="true"></i> ' . esc_html__('View All', 'listingpro') . '</a>';

											$post_rating_data   =   get_post_meta($post->ID, 'lp_listingpro_options', true);

										?>

											<div class="lp-multi-star-wrap" id="multi-box-<?php echo esc_attr($post->ID);; ?>">

												<?php

												foreach ($lp_multi_rating_fields as $k => $v) {
													$field_rating_val   =   '';
													if (isset($post_rating_data[$k])) {
														$field_rating_val   =   $post_rating_data[$k];
													}

												?>
													<div class="lp-multi-star-field">
														<label><?php echo esc_attr($v);  ?></label>
														<p>
															<i class="fa <?php if ($field_rating_val > 0) {
																				echo 'fa-star';
																			} else {
																				echo 'fa-star-o';
																			} ?>" aria-hidden="true"></i>
															<i class="fa <?php if ($field_rating_val > 1) {
																				echo 'fa-star';
																			} else {
																				echo 'fa-star-o';
																			} ?>" aria-hidden="true"></i>
															<i class="fa <?php if ($field_rating_val > 2) {
																				echo 'fa-star';
																			} else {
																				echo 'fa-star-o';
																			} ?>" aria-hidden="true"></i>
															<i class="fa <?php if ($field_rating_val > 3) {
																				echo 'fa-star';
																			} else {
																				echo 'fa-star-o';
																			} ?>" aria-hidden="true"></i>
															<i class="fa <?php if ($field_rating_val > 4) {
																				echo 'fa-star';
																			} else {
																				echo 'fa-star-o';
																			} ?>" aria-hidden="true"></i>
														</p>
													</div>

												<?php

												}

												?>

											</div>

										<?php

										}

										?>

										<div class="lp-listing-stars">
											<div class="lp-rating-stars-outer">
												<span class="lp-star-box <?php if ($rating >= 1) {
																				echo 'filled' . ' ' . $rating_num_clr;
																			} ?>"><i class="fa fa-star" aria-hidden="true"></i></span>

												<span class="lp-star-box <?php if ($rating >= 2) {
																				echo 'filled' . ' ' . $rating_num_clr;
																			} ?>"><i class="fa fa-star" aria-hidden="true"></i></span>

												<span class="lp-star-box <?php if ($rating >= 3) {
																				echo 'filled' . ' ' . $rating_num_clr;
																			} ?>"><i class="fa fa-star" aria-hidden="true"></i></span>

												<span class="lp-star-box <?php if ($rating >= 4) {
																				echo 'filled' . ' ' . $rating_num_clr;
																			} ?>"><i class="fa fa-star" aria-hidden="true"></i></span>

												<span class="lp-star-box <?php if ($rating >= 5) {
																				echo 'filled' . ' ' . $rating_num_clr;
																			} ?>"><i class="fa fa-star" aria-hidden="true"></i></span>
											</div>
											<?php

											if ($rating != 0) :

											?>

												<span class="lp-rating-num <?php echo esc_attr($rating_num_bg); ?>"><?php echo esc_attr($rating); ?><?php if ($rating == 1 || $rating == 2 || $rating == 3 || $rating == 4 || $rating == 5) {
																																						echo '.0';
																																					} ?> </span>

											<?php endif; ?>

										</div>

									</div>

								</div>

								<div class="lp-review-right-content">

									<?php the_content(); ?>

									<?php

									if (!empty($gallery)) {

										$imagearray = explode(',', $gallery);

										$imagearray_count = count($imagearray);

										if ($imagearray_count > 0) {

											require_once(THEME_PATH . "/include/aq_resizer.php");

									?>

											<div class="lp-reivew-gallery">

												<div class="row">

													<div class="listing-review-slider" data-review-thumbs="<?php echo esc_attr($imagearray_count); ?>">

														<?php

														foreach ($imagearray as $image) {

															$imgGalFull = wp_get_attachment_image_src($image, 'full');

															$imgGalThum  = aq_resize($imgGalFull[0], '150', '115', true, true, true);
															echo '<div class="col-md-3"><a href="' . $imgGalFull[0] . '" class="galImgFull" rel="prettyPhoto[gallery2]"><img src="' . $imgGalThum . '" alt="image"></a></div>';
														}

														?>

													</div>

												</div>

											</div>

									<?php

										}
									}

									?>

									<div class="lp-review-right-bottom">

										<span id="lp-review-text-align"><?php echo esc_html__('Was this review ...?', 'listingpro'); ?></span>

										<a href="#" data-restype="<?php echo esc_attr($interVal); ?>" data-reacted="<?php echo esc_attr($reacted_msg); ?>" data-id="<?php the_ID(); ?>" data-score="<?php echo esc_attr($interests); ?>" class="review-interesting review-reaction"><i class="fa fa-thumbs-o-up"></i><span class="react-msg"></span> <?php esc_html_e('Interesting', 'listingpro'); ?> <span class="react-count"><?php echo esc_attr($interests); ?></span></a>

										<a href="#" data-restype="<?php echo esc_attr($lolVal); ?>" data-reacted="<?php echo esc_attr($reacted_msg); ?>" data-id="<?php the_ID(); ?>" data-score="<?php echo esc_attr($Lols); ?>" class="review-lol review-reaction"><i class="fa fa-smile-o"></i> <span class="react-msg"></span> <span class="react-msg"></span><?php esc_html_e('LOL', 'listingpro'); ?> <span class="react-count"><?php echo esc_attr($Lols); ?></span></a>

										<a href="#" data-restype="<?php echo esc_attr($loveVal); ?>" data-reacted="<?php echo esc_attr($reacted_msg); ?>" data-id="<?php the_ID(); ?>" data-score="<?php echo esc_attr($loves); ?>" class="review-love review-reaction"><i class="fa fa-heart-o"></i><span class="react-msg"></span> <span class="react-msg"></span><?php esc_html_e('Love', 'listingpro'); ?> <span class="react-count"><?php echo esc_attr($loves); ?></span></a>

										<?php

										if ($showReport == true && is_user_logged_in()) :

										?>

											<a id="lp-report-this-review" href="#" class="review-love" data-postid="<?php echo get_the_ID(); ?>" data-reportedby="<?php echo esc_attr($currentUserId); ?>" data-posttype="reviews"><i class="fa fa-flag" aria-hidden="true"></i> <?php esc_html_e('Report', 'listingpro'); ?></a>

										<?php

										endif;

										?>

									</div>

								</div>
								<?php if (!empty($review_reply)) { ?>
									<div class="lp-deatil-reply-review-area">
										<div class="owner-response">
											<h3><?php esc_html_e('Author Response', 'listingpro'); ?></h3>
											<?php
											if (!empty($review_reply_time)) { ?>
												<time><?php echo date_i18n("F j, Y h:i:s a", strtotime($review_reply_time)); ?></time>
											<?php } ?>
											<p><?php echo esc_attr($review_reply); ?></p>
										</div>
									</div>
								<?php } ?>
							</div>

							<div class="clearfix"></div>

						</div>

		<?php




					}
					echo '';
					wp_reset_postdata();
				} else {
				}
			}


			$returnHTML = ob_get_contents();
			ob_end_clean();
		}
		exit(json_encode($returnHTML));
	}
}
add_action('wp_ajax_lp_show_sorted_reivews', 'lp_show_sorted_reivews');
add_action('wp_ajax_nopriv_lp_show_sorted_reivews', 'lp_show_sorted_reivews');


/* =======================for change price plan popup================== */
add_action('wp_ajax_listingpro_change_plan_data', 'listingpro_change_plan_data');
if (!function_exists('listingpro_change_plan_data')) {
	function listingpro_change_plan_data()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		$listingID = sanitize_text_field($_POST['listing_id']);
		$plan_title = sanitize_text_field($_POST['plan_title']);
		$plan_price = sanitize_text_field($_POST['plan_price']);
		$haveplan = sanitize_text_field($_POST['haveplan']);
		$listing_status = sanitize_text_field($_POST['listing_status']);
		global $listingpro_options;
		ob_start();
		?>
		<div class="lp-existing-plane-container lp-existing-plane-container-new">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h2 class="modal-title"><?php echo esc_html__('Current Pricing Plan', 'listingpro'); ?></h2>
				<p class="modal-small-title"><?php echo esc_html__('We recommend you to review all the Pricing Plans before changing.', 'listingpro'); ?>
					<?php
					$pricingURL = $listingpro_options['pricing-plan'];
					$PP_pageID = url_to_postid($pricingURL);
					if ($PP_pageID) {
						$pricingURL = get_permalink($PP_pageID);
					}
					if (!empty($pricingURL)) {
					?>
						<a href="<?php echo esc_url($pricingURL); ?>" target="_blank"><?php echo esc_html__('View all pricing plan', 'listingpro'); ?></a>
					<?php
					}
					?>
				</p>
			</div>
			<div class="lp-selected-plan-features">
				<p class="modal-big-title"><?php echo esc_html__('Currently active plan in use', 'listingpro'); ?></p>
				<div class="clearfix lp-active-plan-outer-box">
					<p class="text-center lp-active-plan-outer-box-title"><?php echo esc_html__('active plan', 'listingpro'); ?></p>
					<div class="lp-selected-plan-price pull-left">
						<h4><?php echo esc_attr($plan_title); ?></h4>
					</div>
					<div class="lp-selected-plan-price pull-right">
						<h4><?php echo esc_attr($plan_price); ?></h4>
					</div>
				</div>
			</div>
		</div>
		<div class="lp-new-plane-container lp-existing-plane-container-new lp-existing-all-plane-container-new" style="display:block;">
			<p class="modal-big-title"><?php echo esc_html__('Other available plans to switch', 'listingpro'); ?>
				<?php
				$n = 0;
				$currency_position = '';
				$currency_position = $listingpro_options['pricingplan_currency_position'];
				$currency = listingpro_currency_sign();
				$checkout = $listingpro_options['payment-checkout'];
				$checkout_url = get_permalink($checkout);
				$taxOn = $listingpro_options['lp_tax_swtich'];
				$withtaxprice = false;
				if ($taxOn == "1") {
					$showtaxwithprice = $listingpro_options['lp_tax_with_plan_swtich'];
					if ($showtaxwithprice == "1") {
						$withtaxprice = true;
					}
				}
				$args = array(
					'posts_per_page' => -1,
					'post_status' => 'publish',
					'post_type' => array('price_plan'),
				);
				if ($listing_status == 'expired') {
					$args = array(
						'posts_per_page' => -1,
						'post_status' => 'publish',
						'post_type' => array('price_plan'),
						'meta_query' => array(
							'relation' => 'OR',
							array(
								'key' => 'f_plan_continue',
								'value' => 'true',
								'compare' => '=',
							),
							array(
								'key' => 'plan_price',
								'value' => '0',
								'compare' => '>'
							),
						),
					);
				}
				$the_query = new WP_Query($args);

				if ($the_query->have_posts()) {
					echo '<form name="select-plan-form"  id="select-plan-form" method="post" class="select-plan-form">';
					while ($the_query->have_posts()) {
						$the_query->the_post();
						$plan_id = get_the_ID();
						$forListings = listing_get_metabox_by_ID('plan_for', $plan_id);
						if ($forListings != "claimonly") {
							$planPrice = get_post_meta($plan_id, 'plan_price', true);
							if (!empty($planPrice)) {
								if ($withtaxprice == "1") {
									$taxrate = $listingpro_options['lp_tax_amount'];
									$taxprice = (float) (($taxrate / 100) * $planPrice);
									$planPrice = (float) $planPrice + (float) $taxprice;
								}
								if (!empty($currency_position)) {
									if ($currency_position == "left") {
										$planPrice = $currency . $planPrice;
									} else {
										$planPrice = $planPrice . $currency;
									}
								} else {
									$planPrice = $currency . $planPrice;
								}
							} else {
								$planPrice = esc_html__('Free', 'listingpro');
							}

							$planType = get_post_meta($plan_id, 'plan_package_type', true);
							$planType = trim($planType);
							$planpkgtype = '';
							if (!empty($planType) && $planType == "Package") {
								$planpkgtype = esc_html__('Package', 'listingpro');
							} else {
								$planpkgtype = esc_html__('Per Listing', 'listingpro');
							}
							$planDays = '';
							$planDays = get_post_meta($plan_id, 'plan_time', true);
							$planListing = '';
							if (!empty($planType) && $planType == "Package") {
								$planListing = get_post_meta($plan_id, 'plan_text', true);
								if (!empty($planListing)) {
									$planListing = $planListing . ' ' . esc_html__('Listing', 'listingpro');
								} else {
									$planListing = esc_html__('Unlimited Listing', 'listingpro');
								}
							}

							if (!empty($planDays)) {
								if ($planDays == "1") {
									$planDays .= ' ' . esc_html__('Day', 'listingpro');
								} else {
									$planDays .= ' ' . esc_html__('Days', 'listingpro');
								}
							} else {
								$planDays .= esc_html__('Unlimited Days', 'listingpro');
							}
							echo '<div class="lp-selected-plan-features select-plan-form clearfix">
                            <div class="lp-selected-plan-price pull-left">
                                <label class="plan-options">
                                    <div class="radio radio-danger">
                                    <input id="' . get_the_ID() . '" type="radio" name="plans-posts" value="' . get_the_ID() . '">
                                    <label for="' . get_the_ID() . '"></label>
                                    </div>
                                     ' . get_the_title() . '
                                </label>
                            </div>
                            <div class="per_user_per_listing_price per_user_per_listing_price-hv2"><span>' . $planPrice . '</span> <p>/ ' . $planpkgtype . '</p></div>
                        </div>';
							$n++;
						}
					}
					if ($n > 0) {
						echo '<div class="clearfix margin-top-10 margin-bottom-10"><div class="plane_change_btn plane_change_btn_new">';
						echo '<input type="hidden" value="' . $listingID . '" name="listing-id" id="listing_id">';
						echo '<input type="hidden" value="" name="listing_statuss" id="listing_statuss">';
						echo '<input type="submit" class="btn btn-default" value="' . esc_html__('Change MY Plan', 'listingpro') . '" name="submit-change">';
						echo '</div>';
						echo '</div>';
					} else {
						echo '<p>' . esc_html__('Sorry! There is no plan available', 'listingpro') . '</p>';
					}
					echo '</form>';
					echo '<div class="lp-change-plane-status"><div class="lp-action-div"></div><div class="lp-expire-update-status"></div></div><div class="clearfix"></div>';
					echo '<div class="clearfix pull-left plane_change_btn change-plan-btn-holder" style="display:none;">';
					echo '<a href="javascript:void(0);" class="lp-back-to-change-plan">' . esc_html__('Go Back', 'listingpro') . '</a>';
					echo '</div><div class="clearfix"></div>';
					wp_reset_postdata();
				} else {
				}
				?>
		</div>
<?php
		$returnHTML = ob_get_contents();
		ob_end_clean();
		exit(json_encode($returnHTML));
	}
}
/* =======================for google ads filters================== */
add_filter('listingpro_show_google_ads', 'listingpro_listing_google_ads_callback', 10, 2);
if (!function_exists('listingpro_listing_google_ads_callback')) {
	function listingpro_listing_google_ads_callback($type, $listing_id)
	{
		$returnData = null;
		$adsCode = null;
		$showAds = true;
		$plan_id = listing_get_metabox_by_ID('Plan_id', $listing_id);
		if (!empty($plan_id)) {
			$gAds = get_post_meta($plan_id, 'lp_hidegooglead', true);
			if (empty($gAds) || $gAds != "false") {
				$showAds = false;
			}
		}

		if ($type == "listing") {
			$adsCode = lp_theme_option('lp-gads-editor');
		} elseif ($type == "archive") {
			$adsCode = lp_theme_option('lp-archive-gads-editor');
		}
		if (!empty($adsCode)) {
			$returnData = '<div class="row">
									   <div class="col-md-12 col-sm-12 col-xs-12">
										   ' . $adsCode . '
									   </div>
								   </div>';
		}
		if (!empty($showAds)) {
			return $returnData;
		}
	}
}

/* ===================lp_make_listing_published============ */
add_action('wp_ajax_lp_make_listing_published', 'lp_make_listing_published');
if (!function_exists('lp_make_listing_published')) {
	function lp_make_listing_published()
	{
		check_ajax_referer('lp_ajax_nonce', 'lpNonce');
		// Nonce is checked, get the POST data and sign user on
		if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
			$res = json_encode(array('nonceerror' => 'yes'));
			die($res);
		}
		if (!current_user_can('edit_post', intval($_POST['listing_id']))) {
			$res = json_encode(array('permissionerror' => 'yes'));
			die($res);
		}
		$listing_id = sanitize_text_field($_POST['listing_id']);
		$coupon = sanitize_text_field($_POST['coupon']);
		$taxrate = sanitize_text_field($_POST['taxrate']);
		$planprice = sanitize_text_field($_POST['plan_price']);
		$plan_id = sanitize_text_field($_POST['plan_id']);
		$payment_method = sanitize_text_field($_POST['payment_method']);
		$currency = sanitize_text_field($_POST['currency']);
		$plan_type = get_post_meta($plan_id, 'plan_package_type', true);
		global $listingpro_options;
		$moderation = $listingpro_options['listings_admin_approved'];
		if ($moderation == 'no') {
			$pstatus = 'publish';
		} else {
			$pstatus = 'pending';
		}
		if (!empty($listing_id)) {
			wp_update_post(array(
				'ID'            =>  $listing_id,
				'post_status'   =>  $pstatus,
				'post_date'	=> date('Y-m-d H:i:s'),
			));
			update_post_meta($listing_id, 'discounted', 'yes');
			if (!empty($plan_id)) {
				listing_set_metabox('Plan_id', $plan_id, $listing_id);
				$plan_time = get_post_meta($plan_id, 'plan_time', true);
				listing_set_metabox('lp_purchase_days', $plan_time, $listing_id);
			}
			global $wpdb;
			$prefix = $wpdb->prefix;
			$table = $prefix . 'listing_orders';
			$new_author = wp_get_current_user()->ID;
			$fname = wp_get_current_user()->user_login;
			$lname = wp_get_current_user()->user_login;
			$usermail = wp_get_current_user()->user_email;
			$plan_title = get_the_title($plan_id);
			$plan_duration = get_post_meta($plan_id, 'plan_time', true);

			/* creating invoice */
			$start = 11111111;
			$end = 999999999;
			$ord_num = random_int($start, $end);
			if (lp_theme_option('listingpro_invoice_start_switch') == "yes") {
				$ord_num = lp_theme_option('listingpro_invoiceno_no_start');
				$ord_num++;
				if (class_exists('Redux')) {
					$opt_name = 'listingpro_options';
					Redux::set_option($opt_name, 'listingpro_invoiceno_no_start', "$ord_num");
				}
			}

			$order_data = array(
				'user_id'	=> $new_author,
				'post_id'	=> $listing_id,
				'plan_id'	=> $plan_id,
				'plan_name' => $plan_title,
				'plan_type' => $plan_type,
				'payment_method' => $payment_method,
				'price' => '0',
				'currency'	=> $currency,
				'days'	=> $plan_duration,
				'date'	=> date('d-m-Y'),
				'status'	=> 'success',
				'firstname'	=> $fname,
				'lastname'	=> $lname,
				'email'	=> $usermail,
				'description'	=> 'listing has been purchased',
				'summary' => 'success',
				'order_id'	=> $ord_num
			);
			$wpdb->insert($table, $order_data);
			listingpro_apply_coupon_code_at_payment($coupon, $listing_id, $taxrate, $planprice);
			$listing_url = get_the_permalink($listing_id);
			$status = array(
				'status' => 'success',
				'url' => $listing_url,
			);
		} else {
			$status = array(
				'status' => 'error',
				'url' => esc_html__('Sorry! there is some error', 'listingpro'),
			);
		}
		exit(json_encode($status));
	}
}


/* ===================lp_save_user_analytics============ */
add_action('wp_ajax_lp_save_user_analytics', 'lp_save_user_analytics');
if (!function_exists('lp_save_user_analytics')) {
	function lp_save_user_analytics()
	{
		$data = array();
		parse_str(sanitize_text_field($_POST['data']), $data);
		$g_a_id = null;
		if (isset($data['user_g_analytics'])) {
			$g_a_id = $data['user_g_analytics'];
		}
		if (!empty($g_a_id)) {
			$uid = get_current_user_id();
			update_user_meta($uid, 'g_analytics_id', $g_a_id);
			wp_die(json_encode('success'));
		} else {
			wp_die(json_encode('error'));
		}
	}
}
