<?php

/*
 * Submit Listing ajax
 *
*/

if (!function_exists('Listingpro_listing_submit_init')) {
	function Listingpro_listing_submit_init($hook)
	{
		wp_register_script('listingpro-submit-listing', plugins_url('/assets/js/submit-listing.js', plugin_dir_path(__FILE__)), '', '', true);

		wp_enqueue_script('listingpro-submit-listing');

		wp_localize_script('listingpro-submit-listing', 'ajax_listingpro_submit_object', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
		));
	}
}
if (!is_admin()) {
	add_action('init', 'Listingpro_listing_submit_init');
}

/* ================================================================================= */
add_action('wp_ajax_listingpro_submit_listing_ajax', 'listingpro_submit_listing_ajax');
add_action('wp_ajax_nopriv_listingpro_submit_listing_ajax', 'listingpro_submit_listing_ajax');
if (!function_exists('listingpro_submit_listing_ajax')) {
	function listingpro_submit_listing_ajax()
	{
		lp_mail_headers_append();
		global $listingpro_options;
		$enableCaptcha = false;
		$gCaptcha;
		$count = 0;
		$keyResponse = '';
		$processSubmission = true;
		$listingptext = '';
		$listingprice = '';
		$price_status = '';
		$allowed_mimes = array('image/webp', 'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/tiff', 'image/x-icon');


		$featImgFromGal = false;
		if (isset($listingpro_options['lp_def_featured_image_from_gallery']) && !empty($listingpro_options['lp_def_featured_image_from_gallery'])) {
			if ($listingpro_options['lp_def_featured_image_from_gallery'] == "enable") {
				$featImgFromGal = true;
			}
		}

		if (isset($_POST['token'])) {
			if (!empty($_POST['token'])) {
				$enableCaptcha = true;
			} else {
				$processSubmission = false;
			}
		} else {
			$enableCaptcha = false;
			$processSubmission = true;
		}

		if ($enableCaptcha == true) {
			if (class_exists('cridio_Recaptcha')) {
				$keyResponse = cridio_Recaptcha_Logic::is_recaptcha_valid($_POST['token'], $_POST['recaptha-action']);
				if ($keyResponse == false) {
					$processSubmission = false;
				} else {
					$processSubmission = true;
				}
			}
		}

		if ($processSubmission == true) {
			if (isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

				$response = '';
				$preselctedCat = false;
				if (isset($_POST['lppre_plan_cats'])) {
					if (!empty($_POST['lppre_plan_cats'])) {
						$preselctedCat = true;
					}
				}
				$errors = array();
				if (empty($_POST['postTitle'])) {
					$errors['postTitle'] = 'postTitle';
				}
				/* if(empty($_POST['gAddress'])){
						$errors['gAddress'] = 'gAddress';
					} */
				if (empty($_POST['category']) || !array_filter($_POST['category'])) {
					$errors['category'] = 'category';
				}

				if (empty($_POST['postContent'])) {
					$errors['postContent'] = 'postContent';
				}
				if (isset($_POST['processLogin'])) {
					if ($_POST['processLogin'] == 'yes') {
						if (empty($_POST['inputUsername'])) {
							$errors['inputUsername'] = 'inputUsername';
						}
						if (empty($_POST['inputUserpass'])) {
							$errors['inputUserpass'] = 'inputUserpass';
						}
					}
				}

				/* for size validations */
				$counterLimit = null;
				$sizeLimit = null;

				$totalGalSize = null;
				$totalGalCount = null;
				$msg2 = '';
				$counterSwitch = lp_theme_option('lp_listing_images_count_switch');
				$sizeSwitch = lp_theme_option('lp_listing_images_size_switch');
				if ($counterSwitch == "yes" || $sizeSwitch == "yes") {
					if ($counterSwitch == "yes") {
						$counterLimit = (int) lp_theme_option('lp_listing_images_counter');
					}
					if ($sizeSwitch == "yes") {
						$sizeLimit = lp_theme_option('lp_listing_images_size_switch');
						$sizeLimit = (int) $sizeLimit * 1000000;
					}


					if (isset($_FILES["listingfiles"])) {

						if ($_FILES['listingfiles']['size'] != 0) {

							if (isset($_FILES["listingfiles"])) {
								if ($_FILES['listingfiles']['size'] != 0) {
									$files = $_FILES["listingfiles"];
									$totalGalCount = 0;
									foreach ($files['name'] as $key => $value) {
										$file_type = $files['type'][$key];
										if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
											$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
											die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
										}
										if ($files['name'][$key]) {
											$singleGal = $files['size'][$key];
											$totalGalSize = $totalGalSize + $singleGal;
											$totalGalCount++;
										}
									}
								}
							}


							if (!empty($counterLimit) && !empty($totalGalCount)) {
								if ($totalGalCount > $counterLimit) {
									$errors['listingfiles'] = 'listingfiles';
									$msg2 = esc_html__('Max. allowed images are ', 'listingpro-plugin');
									$msg2 .= $counterLimit;
								}
							}
							if (!empty($sizeLimit) && !empty($totalGalSize)) {
								if ($totalGalSize > $sizeLimit) {
									$errors['listingfiles'] = 'listingfiles';
									$msg2 = esc_html__('Max. allowed images size is ', 'listingpro-plugin');
									$msg2 .= $sizeLimit . '';
									$msg2 .= 'Mb';
								}
							}
						}
					}
				}

				if (isset($_FILES["business_logo"])) {
					if ($_FILES['business_logo']['size'] != 0) {
						$files2 = $_FILES["listingfiles"];
						$files3 = $_FILES["lp-featuredimage"];
						$files = $_FILES["business_logo"];
						foreach ($files['name'] as $key => $value) {
							if ($files['name'][$key]) {
								$file = array(
									'name' => $files['name'][$key],
									'type' => $files['type'][$key],
									'tmp_name' => $files['tmp_name'][$key],
									'error' => $files['error'][$key],
									'size' => $files['size'][$key]
								);
								$file_type = wp_check_filetype($file['name']);
								$file_type = $file_type['type'];
								if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
									$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
									die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
								}
							}
						}
					}
				}

				/* end for size validation */

				/* PLAN ID */
				$lp_paid_mode = $listingpro_options['enable_paid_submission'];
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
				if (!empty($errors) && count($errors) > 0) {
					$msg = esc_html__('Please Fill The Required Fields', 'listingpro-plugin');

					die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
				} else {

					$gaddpress = '';
					if (isset($_POST['gAddress']) && !empty($_POST['gAddress'])) {
						$gaddpress = sanitize_text_field($_POST['gAddress']);
					}
					if (isset($_POST['gAddresscustom']) && !empty($_POST['gAddresscustom'])) {
						$gaddpress = sanitize_text_field($_POST['gAddresscustom']);
					}
					$postID = '';
					$featuresID = '';
					if (isset($_POST['lp_form_fields_inn'])) {
						if (isset($_POST['lp_form_fields_inn']['lp_feature']) && !empty($_POST['lp_form_fields_inn']['lp_feature'])) {
							$featuresID = $_POST['lp_form_fields_inn']['lp_feature'];
						}
					}

					$tags = '';
					if (isset($_POST['tags']) && !empty($_POST['tags'])) {
						$tags = sanitize_text_field($_POST['tags']);
						$tags = explode(',', $tags);
					}

					/* locations */

					$listLocation = '';
					$lpsLoc = '';
					$locations_type = $listingpro_options['lp_listing_locations_options'];
					if ($locations_type == "auto_loc") {
						if (isset($_POST['location'])) {
							if (!empty($_POST['location'])) {
								if (is_array($_POST['location'])) {
									$lpsLoc = array();
									$listLocation = $_POST['location'];

									$locCount = 0;
									foreach ($_POST['location'] as $locnames) {
										$insertLoc = listingpro_insert_term($locnames, 'location');
										if (!empty($insertLoc)) {
											$lpsLoc[$locCount] = $insertLoc['term_id'];
										} else {
											$lpLocObj = get_term_by('name', $locnames, 'location');
											if (!empty($lpLocObj)) {
												$lpsLoc[$locCount] = $lpLocObj->term_id;
											} else {
												$lpsLoc[$locCount] = '';
											}
										}

										$locCount++;
									}
								} else {
									$listLocation = sanitize_text_field($_POST['location']);
									$insertLoc = listingpro_insert_term($listLocation, 'location');
									if (!empty($insertLoc)) {
										$lpsLoc = $insertLoc['term_id'];
									} else {
										$lpLocObj = get_term_by('name', $listLocation, 'location');
										if (!empty($lpLocObj)) {
											$lpsLoc = $lpLocObj->term_id;
										} else {
											$lpsLoc = '';
										}
									}
								}
							}
						}
					} else {
						$lpsLoc = $_POST['location'];
					}
					/* ends locations */
					$lp_listing_locations;
					$lp_listing_cats;
					if (!empty($lpsLoc) && is_array($lpsLoc)) {
						$lp_listing_locations = $lpsLoc;
					} else {
						$lp_listing_locations = array($lpsLoc);
					}

					if (isset($_POST['category'])) {
						$lpcatss = $_POST['category'];
						if (!empty($lpcatss) && is_array($lpcatss)) {
							if (is_numeric($lpcatss[0])) {
								$lp_listing_cats = $lpcatss;
							} else {
								$_POST['category'] = array();
								foreach ($lpcatss as $k => $term) {
									if (!term_exists($term, 'listing-category')) {
										$newTerm = wp_insert_term(
											$term,
											'listing-category'
										);
										$newTerm = $newTerm['term_id'];
										$_POST['category'][] = $newTerm;
									} else {
										$eterm = term_exists($term, 'listing-category');
										$eterm = $eterm['term_id'];
										$_POST['category'][] = $eterm;
									}
								}
								$lpcatss = $_POST['category'];
								$lp_listing_cats = $lpcatss;
							}
						} else {
							$lpcatss = $_POST['category'];
							if (is_numeric($lpcatss)) {
								$lp_listing_cats = $lpcatss;
							} else {
								$_POST['category'] = null;
								if (!term_exists($_POST['category'], 'listing-category')) {
									$newTerm = wp_insert_term(
										$_POST['category'],
										'listing-category'
									);
									$newTerm = $newTerm['term_id'];
									$_POST['category'] = $newTerm;
								} else {
									$eterm = term_exists($_POST['category'], 'listing-category');
									$eterm = $eterm['term_id'];
									$_POST['category'] = $eterm;
								}
								$lpcatss = $_POST['category'];
								$lp_listing_cats = $lpcatss;
							}
						}
					}
					$featuresArr = array();
					if (!empty($featuresID) && is_array($featuresID)) {
						foreach ($featuresID as $k => $termID) {
							$termID = (int) $termID;
							if (term_exists($termID, 'features')) $featuresArr[] = $termID;
						}
					}
					if (is_user_logged_in()) {
						$post_information = array(
							'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
							'post_content' => wp_kses_post($_POST['postContent']),
							'post_type' => 'listing',
							'tax_input' => array(
								'location' => $lp_listing_locations,
								'listing-category' => $lp_listing_cats,
								'features' => $featuresArr,
								'list-tags' => $tags,
							),
							'post_status' => 'pending'
						);
						$postID = wp_insert_post($post_information);
						update_post_meta($postID, 'claimed', 0);
						/* plan data save in meta */
						lp_listing_save_additional_metas($plan_id, $postID);

						$current_user = wp_get_current_user();
						$useremail = $current_user->user_email;
						$user_name = $current_user->user_login;
						$admin_email = '';
						$admin_email = get_option('admin_email');
						$website_url = site_url();
						$website_name = get_option('blogname');
						$listing_title = get_the_title($postID);
						$listing_url = get_the_permalink($postID);

						$headers[] = 'Content-Type: text/html; charset=UTF-8';


						$business_hours = $_POST['business_hours'];

						$faqs;
						if (isset($_POST['faq']) && isset($_POST['faqans'])) {
							$faqQuestion = $_POST['faq'];
							$faqAns = $_POST['faqans'];
							$faqs = array('faq' => $faqQuestion, 'faqans' => $faqAns);
						}
						if (isset($_POST['lp_form_fields_inn'])) {

							$fields = $_POST['lp_form_fields_inn'];
							$filterArray = lp_save_extra_fields_in_listing($fields, $postID);
							if( is_array($fields) && is_array($filterArray) ){
								$fields = array_merge($fields,$filterArray);
							}
							$metaFields = 'lp_' . strtolower(THEMENAME) . '_options_fields';
							update_post_meta($postID, $metaFields, $fields);
						} else {
							$metaFields = 'lp_' . strtolower(THEMENAME) . '_options_fields';
							$fields = get_post_meta($postID, $metaFields, true);
							if (is_array($fields)) {
								if (array_key_exists('lp_feature', $fields)) {
									unset($fields['lp_feature']);
									update_post_meta($postID, $metaFields, $fields);
								}
							}
						}

						$priceStatus = isset($_POST['price_status']) ? sanitize_text_field($_POST['price_status']) : '';
						$listTagline = isset($_POST['tagline_text']) ? sanitize_text_field($_POST['tagline_text']) : '';
						$listLatitude = isset($_POST['latitude']) ? sanitize_text_field($_POST['latitude']) : '';
						$listLongitude = isset($_POST['longitude']) ? sanitize_text_field($_POST['longitude']) : '';
						$listPhone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
						$listWebsite = isset($_POST['website']) ? sanitize_text_field($_POST['website']) : '';
						$listWhatsapp = isset($_POST['whatsapp']) ? sanitize_text_field($_POST['whatsapp']) : '';
						$listTwitter = isset($_POST['twitter']) ? sanitize_text_field($_POST['twitter']) : '';
						$listFacebook = isset($_POST['facebook']) ? sanitize_text_field($_POST['facebook']) : '';
						$listLinkedin = isset($_POST['linkedin']) ? sanitize_text_field($_POST['linkedin']) : '';
						$listYoutube = isset($_POST['youtube']) ? sanitize_text_field($_POST['youtube']) : '';
						$listInstagram = isset($_POST['instagram']) ? sanitize_text_field($_POST['instagram']) : '';
						$listPrice = isset($_POST['listingprice']) ? sanitize_text_field($_POST['listingprice']) : '';
						$listPText = isset($_POST['listingptext']) ? sanitize_text_field($_POST['listingptext']) : '';
						$listPostVideo = isset($_POST['postVideo']) ? sanitize_text_field($_POST['postVideo']) : '';

						listing_set_metabox('business_hours', $business_hours, $postID);
						listing_set_metabox('price_status', $priceStatus, $postID);
						listing_set_metabox('faqs', $faqs, $postID);
						listing_set_metabox('tagline_text', $listTagline, $postID);
						listing_set_metabox('gAddress', $gaddpress, $postID);
						listing_set_metabox('latitude', $listLatitude, $postID);
						listing_set_metabox('longitude', $listLongitude, $postID);
						listing_set_metabox('phone', $listPhone, $postID);
						listing_set_metabox('whatsapp', $listWhatsapp, $postID);
						listing_set_metabox('email', $useremail, $postID);
						listing_set_metabox('website', $listWebsite, $postID);
						listing_set_metabox('twitter', $listTwitter, $postID);
						listing_set_metabox('facebook', $listFacebook, $postID);
						listing_set_metabox('linkedin', $listLinkedin, $postID);
						listing_set_metabox('youtube', $listYoutube, $postID);
						listing_set_metabox('instagram', $listInstagram, $postID);
						listing_set_metabox('Plan_id', $plan_id, $postID);
						listing_set_metabox('list_price', $listPrice, $postID);
						add_post_meta($postID, 'claimed', '0');
						listing_set_metabox('list_price_to', $listPText, $postID);
						listing_set_metabox('video', $listPostVideo, $postID);
						listing_set_metabox('claimed_section', 'not_claimed', $postID);
						add_post_meta($postID, 'listing_rate', '');
						add_post_meta($postID, 'listing_reviewed', '');

						update_post_meta($postID, 'plan_id', $plan_id);

						if (!empty($preselctedCat)) {
							//for pre selected category
							update_post_meta($postID, 'preselected', true);
						}
						if ($plan_id != 'none' && $plan_days) {
							listing_set_metabox('lp_purchase_days', $plan_days, $postID);
						}

						if (isset($_FILES["business_logo"])) {
							if ($_FILES['business_logo']['size'] != 0) {
								$files2 = $_FILES["listingfiles"];
								$files3 = $_FILES["lp-featuredimage"];
								$files = $_FILES["business_logo"];
								foreach ($files['name'] as $key => $value) {
									if ($files['name'][$key]) {
										$file = array(
											'name' => $files['name'][$key],
											'type' => $files['type'][$key],
											'tmp_name' => $files['tmp_name'][$key],
											'error' => $files['error'][$key],
											'size' => $files['size'][$key]
										);
										$file_type = wp_check_filetype($file['name']);
										$file_type = $file_type['type'];
										if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
											$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
											die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
										}
										$_FILES = array("business_logo" => $file);
										$count = 0;
										foreach ($_FILES as $file => $array) {
											//$newupload = listingpro_handle_attachment_featured($file,$postID);
											$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
											$b_logo_url = wp_get_attachment_url($newupload);

											listing_set_metabox('business_logo', $b_logo_url, $postID);
										}
									}
									$_FILES["listingfiles"] = '';
									$_FILES["listingfiles"] = $files2;
									$_FILES["lp-featuredimage"] = $files3;
								}
							}
						}
						$featuredimageset = false;
						if (isset($_FILES["lp-featuredimage"])) {
							if ($_FILES['lp-featuredimage']['size'] != 0) {
								$featuredimageset = true;
								$files2 = $_FILES["listingfiles"];
								$files = $_FILES["lp-featuredimage"];
								$b_logo = $_FILES["business_logo"];
								foreach ($files['name'] as $key => $value) {
									if ($files['name'][$key]) {
										$file = array(
											'name' => $files['name'][$key],
											'type' => $files['type'][$key],
											'tmp_name' => $files['tmp_name'][$key],
											'error' => $files['error'][$key],
											'size' => $files['size'][$key]
										);
										$file_type = wp_check_filetype($file['name']);
										$file_type = $file_type['type'];
										if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
											$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
											die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
										}
										$_FILES = array("lp-featuredimage" => $file);
										$count = 0;
										foreach ($_FILES as $file => $array) {
											$newupload = listingpro_handle_attachment_featured($file, $postID);
										}
									}
									$_FILES["listingfiles"] = '';
									$_FILES["business_logo"] = '';

									$_FILES["listingfiles"] = $files2;
									$_FILES["business_logo"] = $b_logo;
								}
							}
						}


						if (isset($_FILES["listingfiles"])) {
							if ($_FILES['listingfiles']['size'] != 0) {
								$files = isset($_FILES["listingfiles"]) ? $_FILES["listingfiles"] : null;
								$b_logo = isset($_FILES["business_logo"]) ? $_FILES["business_logo"] : null;
								//$files2 = $_FILES["lp-featuredimage"];
								foreach ($files['name'] as $key => $value) {
									if ($files['name'][$key]) {
										$file = array(
											'name' => $files['name'][$key],
											'type' => $files['type'][$key],
											'tmp_name' => $files['tmp_name'][$key],
											'error' => $files['error'][$key],
											'size' => $files['size'][$key]
										);
										$file_type = wp_check_filetype($file['name']);
										$file_type = $file_type['type'];
										if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
											$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
											die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
										}
										$_FILES = array("listingfiles" => $file);
										$count = 0;
										foreach ($_FILES as $file => $array) {

											if (empty($featuredimageset) && $featImgFromGal == true && !isset($_FILES["lp-featuredimage"])) {
												if (!has_post_thumbnail($postID)) {
													$newupload = listingpro_handle_attachment($file, $postID, $set_thu = true);
												} else {
													$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
												}
											} else {
												$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
											}


											$ids[] = $newupload;
											$count++;
										}
									}
									$_FILES["business_logo"] = '';
									$_FILES["business_logo"] = $b_logo;
								}
								if (!empty($ids) && is_array($ids)) {
									$img_ids = implode(",", $ids);
									update_post_meta($postID, 'gallery_image_ids', $img_ids);
									//$_FILES['lp-featuredimage'] = $files2;

								}
							}
						}

						/* mail for user */
						$u_mail_subject_a = '';
						$u_mail_body_a = '';
						$u_mail_subject = $listingpro_options['listingpro_subject_new_submit_listing'];
						$u_mail_body = $listingpro_options['listingpro_new_submit_listing_content'];

						$u_mail_subject_a = lp_sprintf2("$u_mail_subject", array(
							'website_url' => "$website_url",
							'listing_title' => "$listing_title",
							'listing_url' => "$listing_url",
							'website_name' => "$website_name",
							'user_name' => "$user_name",
						));

						$u_mail_body_a = lp_sprintf2("$u_mail_body", array(
							'website_url' => "$website_url",
							'listing_title' => "$listing_title",
							'listing_url' => "$listing_url",
							'website_name' => "$website_name",
							'user_name' => "$user_name",
						));

						wp_mail($useremail, $u_mail_subject_a, $u_mail_body_a, $headers);

						/* mail for admin */
						$a_mail_subject_a = '';
						$a_mail_body_a = '';
						$a_mail_subject = $listingpro_options['listingpro_subject_new_submit_listing_admin'];
						$a_mail_body = $listingpro_options['listingpro_new_submit_listing_content_admin'];

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

						wp_mail($admin_email, $a_mail_subject_a, $a_mail_body_a, $headers);


						if ($lp_paid_mode == "yes" && $planHavePrice == true) {
							listing_draft_save($postID, null);
						}

						$response = get_the_permalink($postID);
						$msg = esc_html__('Success! Submission is successful', 'listingpro-plugin');
						die(json_encode(array('response' => 'success', 'status' => $response, 'msg' => $msg)));
					} else {

						if ($_POST['processLogin'] == 'no' || !isset($_POST['processLogin'])) {
							$enableUsernameField = false;
							if (isset($listingpro_options['lp_register_username'])) {
								if ($listingpro_options['lp_register_username'] == true) {
									$enableUsernameField = true;
								}
							}
							$user_email = '';
							$user_name = '';
							$email = sanitize_email($_POST['email']);
							if (empty($email)) {
								$errors['email'] = $email;
							}


							$user_name = sanitize_email($_POST['email']);
							list($user_name) = explode('@', $email);
							$user_name .= rand(1, 10000);
							if (!empty($enableUsernameField)) {
								$user_name = sanitize_text_field($_POST['customUname']);
							}

							if (empty($user_name)) {
								$errors['customUname'] = $user_name;
							}
							if (!empty($errors) && count($errors) > 0) {
								$msg = esc_html__('Please Fill The Required Fields', 'listingpro-plugin');
								die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
							}
							$existinUserId = username_exists($user_name);
							if (!empty($existinUserId)) {
								$response = '<span class="email-exist-error">' . esc_html__("UserName already exists", "listingpro-plugin") . '</span>';
								$msg = esc_html__('Please Fill The Required Fields', 'listingpro-plugin');
								die(json_encode(array('response' => 'failure', 'status' => $response, 'msg' => $msg)));
							}


							if (email_exists($email) == true || username_exists($user_name) == true) {
								$response = '<span class="email-exist-error">' . esc_html__("Email already exists", "listingpro-plugin") . '</span>';
								$msg = esc_html__('Please Fill The Required Fields', 'listingpro-plugin');
								die(json_encode(array('response' => 'failure', 'status' => $response, 'msg' => $msg)));
							} else {

								$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
								$user_id = wp_create_user($user_name, $random_password, $email);
								$creds['user_login'] = $user_name;
								$creds['user_password'] = $random_password;
								$creds['remember'] = true;
								$user = wp_signon($creds, true);

								/* for user */
								$subject = $listingpro_options['listingpro_subject_new_user_register'];
								$website_url = site_url();

								$formated_subject = lp_sprintf2("$subject", array(
									'website_url' => "$website_url"
								));

								$mail_content = $listingpro_options['listingpro_new_user_register'];
								$formated_mail_content = lp_sprintf2("$mail_content", array(
									'website_url' => "$website_url",
									'user_login_register' => "$user_name",
									'user_pass_register' => "$random_password",
									'user_name' => "$user_name",
								));

								/* for admin */

								$subject2 = $listingpro_options['listingpro_subject_admin_new_user_register'];
								$mail_content2 = $listingpro_options['listingpro_admin_new_user_register'];
								$formated_mail_content2 = lp_sprintf2("$mail_content2", array(
									'website_url' => "$website_url",
									'user_login_register' => "$user_name",
									'user_email_register' => "$email",
									'user_name' => "$user_name",
								));

								$from = get_option('admin_email');
								$headers[] = 'Content-Type: text/html; charset=UTF-8';

								$headers2[] = 'Content-Type: text/html; charset=UTF-8';

								wp_mail($email, $formated_subject, $formated_mail_content, $headers);
								wp_mail($from, $subject2, $formated_mail_content2, $headers2);

								/* locations */
								$listLocation = '';
								$lpsLoc = '';
								$insertLoc = '';
								$locations_type = $listingpro_options['lp_listing_locations_options'];
								if ($locations_type == "auto_loc") {
									if (isset($_POST['location'])) {
										if (!empty($_POST['location'])) {
											//$listLocation = sanitize_text_field($_POST['location']);
											$listLocation = $_POST['location'];
											if (is_array($listLocation)) {
												foreach ($listLocation as $singlelistLoc) {
													$insertLoc[$singlelistLoc] = listingpro_insert_term($singlelistLoc, 'location');
												}
											} else {
												$insertLoc = listingpro_insert_term($listLocation, 'location');
											}
											if (!empty($insertLoc)) {
												if (is_array($insertLoc)) {
													foreach ($insertLoc as $singleinsertLoc) {
														$slidd = $singleinsertLoc['term_id'];
														$lpsLoc[$slidd] = $slidd;
													}
												} else {
													$lpsLoc = $insertLoc['term_id'];
												}
											} else {
												if (is_array($listLocation)) {

													foreach ($listLocation as $singlelistLoc) {
														$lpLocObj = get_term_by('name', $singlelistLoc, 'location');
														if (!empty($lpLocObj)) {
															$obtID = $lpLocObj->term_id;
															$lpsLoc[$obtID] = $obtID;
														}
													}
												} else {
													$lpLocObj = get_term_by('name', $listLocation, 'location');
													if (!empty($lpLocObj)) {
														$lpsLoc = $lpLocObj->term_id;
													} else {
														$lpsLoc = '';
													}
												}
											}
										}
									}
								} else {
									$lpsLoc = $_POST['location'];
								}
								/* ends locations */


								$post_information = array(
									'post_author' => $user_id,
									'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
									'post_content' => $_POST['postContent'],
									'post_type' => 'listing',
									'post_status' => 'pending'
								);
								$postID = wp_insert_post($post_information);
								update_post_meta($postID, 'claimed', 0);
								/* plan data save in meta */
								lp_listing_save_additional_metas($plan_id, $postID);

								wp_set_post_terms($postID, $_POST['category'], 'listing-category');
								wp_set_post_terms($postID, $lpsLoc, 'location');
								$featuresArr = array();
								if (!empty($featuresID) && is_array($featuresID)) {
									foreach ($featuresID as $k => $termID) {
										$termID = (int) $termID;
										if (term_exists($termID, 'features')) $featuresArr[] = $termID;
									}
								}
								wp_set_post_terms($postID, $featuresArr, 'features');
								wp_set_post_terms($postID, $tags, 'list-tags');



								$current_user = wp_get_current_user();
								$useremail = $current_user->user_email;
								$user_name = $current_user->user_login;
								$admin_email = '';
								$admin_email = get_option('admin_email');
								$website_url = site_url();
								$website_name = get_option('blogname');
								$listing_title = get_the_title($postID);
								$listing_url = get_the_permalink($postID);

								$headers[] = 'Content-Type: text/html; charset=UTF-8';


								$business_hours = $_POST['business_hours'];

								$faqs = '';
								if (isset($_POST['faq']) && isset($_POST['faqans'])) {
									$faqQuestion = $_POST['faq'];
									$faqAns = $_POST['faqans'];
									$faqs = array('faq' => $faqQuestion, 'faqans' => $faqAns);
								}
								if (isset($_POST['lp_form_fields_inn'])) {

									$fields = $_POST['lp_form_fields_inn'];
									$filterArray = lp_save_extra_fields_in_listing($fields, $postID);
									if( is_array($fields) && is_array($filterArray) ){
										$fields = array_merge($fields,$filterArray);
									}
									$metaFields = 'lp_' . strtolower(THEMENAME) . '_options_fields';
									update_post_meta($postID, $metaFields, $fields);
								} else {
									$metaFields = 'lp_' . strtolower(THEMENAME) . '_options_fields';
									$fields = get_post_meta($postID, $metaFields, true);
									if (is_array($fields) && array_key_exists('lp_feature', $fields)) {
										unset($fields['lp_feature']);
										update_post_meta($postID, $metaFields, $fields);
									}
								}

								$priceStatus = isset($_POST['price_status']) ? sanitize_text_field($_POST['price_status']) : null;
								$listTagline = isset($_POST['tagline_text']) ? sanitize_text_field($_POST['tagline_text']) : null;
								$listLatitude = isset($_POST['latitude']) ? sanitize_text_field($_POST['latitude']) : null;
								$listLongitude = isset($_POST['longitude']) ? sanitize_text_field($_POST['longitude']) : null;
								$listPhone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : null;
								$listWhatsapp = isset($_POST['whatsapp']) ? sanitize_text_field($_POST['whatsapp']) : null;
								$listWebsite = isset($_POST['website']) ? sanitize_text_field($_POST['website']) : null;
								$listTwitter = isset($_POST['twitter']) ? sanitize_text_field($_POST['twitter']) : null;
								$listFacebook = isset($_POST['facebook']) ? sanitize_text_field($_POST['facebook']) : null;
								$listLinkedin = isset($_POST['linkedin']) ? sanitize_text_field($_POST['linkedin']) : null;
								$listYoutube = isset($_POST['youtube']) ? sanitize_text_field($_POST['youtube']) : null;
								$listInstagram = isset($_POST['instagram']) ? sanitize_text_field($_POST['instagram']) : null;
								$listPrice = isset($_POST['listingprice']) ? sanitize_text_field($_POST['listingprice']) : null;
								$listPText = isset($_POST['listingptext']) ? sanitize_text_field($_POST['listingptext']) : null;
								$listPostVideo = isset($_POST['postVideo']) ? sanitize_text_field($_POST['postVideo']) : null;

								listing_set_metabox('business_hours', $business_hours, $postID);
								listing_set_metabox('price_status', $priceStatus, $postID);
								listing_set_metabox('faqs', $faqs, $postID);
								listing_set_metabox('tagline_text', $listTagline, $postID);
								listing_set_metabox('gAddress', $gaddpress, $postID);
								listing_set_metabox('latitude', $listLatitude, $postID);
								listing_set_metabox('longitude', $listLongitude, $postID);
								listing_set_metabox('phone', $listPhone, $postID);
								listing_set_metabox('whatsapp', $listWhatsapp, $postID);
								listing_set_metabox('email', $email, $postID);
								listing_set_metabox('website', $listWebsite, $postID);
								listing_set_metabox('twitter', $listTwitter, $postID);
								listing_set_metabox('facebook', $listFacebook, $postID);
								listing_set_metabox('linkedin', $listLinkedin, $postID);
								listing_set_metabox('youtube', $listYoutube, $postID);
								listing_set_metabox('instagram', $listInstagram, $postID);
								listing_set_metabox('Plan_id', $plan_id, $postID);
								listing_set_metabox('list_price', $listPrice, $postID);
								listing_set_metabox('list_price_to', $listPText, $postID);
								listing_set_metabox('video', $listPostVideo, $postID);
								listing_set_metabox('claimed_section', 'not_claimed', $postID);
								add_post_meta($postID, 'listing_rate', '');
								add_post_meta($postID, 'listing_reviewed', '');

								update_post_meta($postID, 'plan_id', $plan_id);

								if ($plan_id != 'none' && $plan_days) {
									listing_set_metabox('lp_purchase_days', $plan_days, $postID);
								}


								if (isset($_FILES["business_logo"])) {
									if ($_FILES['business_logo']['size'] != 0) {
										$files2 = $_FILES["listingfiles"];
										$files3 = $_FILES["lp-featuredimage"];
										$files = $_FILES["business_logo"];
										foreach ($files['name'] as $key => $value) {
											if ($files['name'][$key]) {
												$file = array(
													'name' => $files['name'][$key],
													'type' => $files['type'][$key],
													'tmp_name' => $files['tmp_name'][$key],
													'error' => $files['error'][$key],
													'size' => $files['size'][$key]
												);
												$_FILES = array("business_logo" => $file);
												$file_type = wp_check_filetype($file['name']);
												$file_type = $file_type['type'];
												if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
													$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
													die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
												}
												$count = 0;
												foreach ($_FILES as $file => $array) {
													//$newupload = listingpro_handle_attachment_featured($file,$postID);
													$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
													$b_logo_url = wp_get_attachment_url($newupload);

													listing_set_metabox('business_logo', $b_logo_url, $postID);
												}
											}
											$_FILES["listingfiles"] = '';
											$_FILES["listingfiles"] = $files2;
											$_FILES["lp-featuredimage"] = $files3;
										}
									}
								}
								$featuredimageset = false;
								if (isset($_FILES["lp-featuredimage"])) {
									if ($_FILES['lp-featuredimage']['size'] != 0) {
										$featuredimageset = true;
										$files2 = $_FILES["listingfiles"];
										$files = $_FILES["lp-featuredimage"];
										foreach ($files['name'] as $key => $value) {
											if ($files['name'][$key]) {
												$file = array(
													'name' => $files['name'][$key],
													'type' => $files['type'][$key],
													'tmp_name' => $files['tmp_name'][$key],
													'error' => $files['error'][$key],
													'size' => $files['size'][$key]
												);
												$file_type = wp_check_filetype($file['name']);
												$file_type = $file_type['type'];
												if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
													$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
													die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
												}
												$_FILES = array("lp-featuredimage" => $file);
												$count = 0;
												foreach ($_FILES as $file => $array) {
													$newupload = listingpro_handle_attachment_featured($file, $postID);
												}
											}
											$_FILES["listingfiles"] = '';
											$_FILES["listingfiles"] = $files2;
										}
									}
								}


								if (isset($_FILES["listingfiles"])) {
									if ($_FILES['listingfiles']['size'] != 0) {
										$files = $_FILES["listingfiles"];
										//$files2 = $_FILES["lp-featuredimage"];  			
										foreach ($files['name'] as $key => $value) {
											if ($files['name'][$key]) {
												$file = array(
													'name' => $files['name'][$key],
													'type' => $files['type'][$key],
													'tmp_name' => $files['tmp_name'][$key],
													'error' => $files['error'][$key],
													'size' => $files['size'][$key]
												);
												$file_type = wp_check_filetype($file['name']);
												$file_type = $file_type['type'];
												if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
													$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
													die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
												}
												$_FILES = array("listingfiles" => $file);
												$count = 0;
												foreach ($_FILES as $file => $array) {

													if (empty($featuredimageset) && $featImgFromGal == true && !isset($_FILES["lp-featuredimage"])) {
														if (!has_post_thumbnail($postID)) {
															$newupload = listingpro_handle_attachment($file, $postID, $set_thu = true);
														} else {
															$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
														}
													} else {
														$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
													}
													$ids[] = $newupload;
													$count++;
												}
											}
										}
										if (!empty($ids) && is_array($ids)) {
											$img_ids = implode(",", $ids);
											update_post_meta($postID, 'gallery_image_ids', $img_ids);
											//$_FILES['lp-featuredimage'] = $files2;

										}
									}
								}

								/* mail for user */
								$u_mail_subject_a = '';
								$u_mail_body_a = '';
								$u_mail_subject = $listingpro_options['listingpro_subject_new_submit_listing'];
								$u_mail_body = $listingpro_options['listingpro_new_submit_listing_content'];

								$u_mail_subject_a = lp_sprintf2("$u_mail_subject", array(
									'website_url' => "$website_url",
									'listing_title' => "$listing_title",
									'listing_url' => "$listing_url",
									'website_name' => "$website_name",
									'user_name' => "$user_name",
								));

								$u_mail_body_a = lp_sprintf2("$u_mail_body", array(
									'website_url' => "$website_url",
									'listing_title' => "$listing_title",
									'listing_url' => "$listing_url",
									'website_name' => "$website_name",
									'user_name' => "$user_name",
								));

								wp_mail($email, $u_mail_subject_a, $u_mail_body_a, $headers);

								/* mail for admin */
								$a_mail_subject_a = '';
								$a_mail_body_a = '';
								$a_mail_subject = $listingpro_options['listingpro_subject_new_submit_listing_admin'];
								$a_mail_body = $listingpro_options['listingpro_new_submit_listing_content_admin'];

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

								wp_mail($admin_email, $a_mail_subject_a, $a_mail_body_a, $headers);


								if ($lp_paid_mode == "yes" && $planHavePrice == true) {
									listing_draft_save($postID, $user_id);
								}

								$response = get_the_permalink($postID);
								$msg = esc_html__('Success! Submission is successful', 'listingpro-plugin');
								die(json_encode(array('response' => 'success', 'status' => $response, 'newuser' => $user_id, 'msg' => $msg)));
							}
						} else if ($_POST['processLogin'] == 'yes' && isset($_POST['processLogin'])) {
							$user_pass = sanitize_text_field($_POST['inputUserpass']);
							$user_name = sanitize_text_field($_POST['inputUsername']);

							$creds = array();
							$creds['user_login'] = $user_name;
							$creds['user_password'] = $user_pass;
							$creds['remember'] = true;

							$user = wp_signon($creds, true);
							if (is_wp_error($user)) {
								$response = '<span class="invalid-cred-error">' . esc_html__("Invalid username or password", "listingpro-plugin") . '</span>';
								$msg = esc_html__('Please Fill The Required Fields', 'listingpro-plugin');
								die(json_encode(array('response' => 'failure', 'status' => $response, 'msg' => $msg)));
							} else {

								//Get current user ID
								$user_id = $user->ID;
								$email = $user->user_email;
								/* locations */
								$listLocation = '';
								$lpsLoc = '';
								$locations_type = $listingpro_options['lp_listing_locations_options'];
								if ($locations_type == "auto_loc") {
									if (isset($_POST['location'])) {
										if (!empty($_POST['location'])) {
											$listLocation = sanitize_text_field($_POST['location']);
											$insertLoc = listingpro_insert_term($listLocation, 'location');
											if (!empty($insertLoc)) {
												$lpsLoc = $insertLoc['term_id'];
											} else {
												$lpLocObj = get_term_by('name', $listLocation, 'location');
												if (!empty($lpLocObj)) {
													$lpsLoc = $lpLocObj->term_id;
												} else {
													$lpsLoc = '';
												}
											}
										}
									}
								} else {
									$lpsLoc = $_POST['location'];
								}
								/* ends locations */


								$post_information = array(
									'post_author' => $user_id,
									'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
									'post_content' => wp_kses_post($_POST['postContent']),
									'post_type' => 'listing',
									'post_status' => 'pending'
								);
								$postID = wp_insert_post($post_information);
								update_post_meta($postID, 'claimed', 0);
								/* plan data save in meta */
								lp_listing_save_additional_metas($plan_id, $postID);

								wp_set_post_terms($postID, $_POST['category'], 'listing-category');
								wp_set_post_terms($postID, $lpsLoc, 'location');
								$featuresArr = array();
								if (!empty($featuresID) && is_array($featuresID)) {
									foreach ($featuresID as $k => $termID) {
										$termID = (int) $termID;
										if (term_exists($termID, 'features')) $featuresArr[] = $termID;
									}
								}
								wp_set_post_terms($postID, $featuresArr, 'features');
								wp_set_post_terms($postID, $tags, 'list-tags');



								$current_user = wp_get_current_user();
								$useremail = $current_user->user_email;
								$admin_email = '';
								$admin_email = get_option('admin_email');
								$website_url = site_url();
								$website_name = get_option('blogname');
								$listing_title = get_the_title($postID);
								$listing_url = get_the_permalink($postID);

								$headers[] = 'Content-Type: text/html; charset=UTF-8';

								$business_hours = $_POST['business_hours'];

								$faqs = '';
								if (isset($_POST['faq']) && isset($_POST['faqans'])) {
									$faqQuestion = $_POST['faq'];
									$faqAns = $_POST['faqans'];
									$faqs = array('faq' => $faqQuestion, 'faqans' => $faqAns);
								}
								if (isset($_POST['lp_form_fields_inn'])) {
									$fields = $_POST['lp_form_fields_inn'];
									$filterArray = lp_save_extra_fields_in_listing($fields, $postID);
									if( is_array($fields) && is_array($filterArray) ){
										$fields = array_merge($fields,$filterArray);
									}
									$metaFields = 'lp_' . strtolower(THEMENAME) . '_options_fields';
									update_post_meta($postID, $metaFields, $fields);
								} else {
									$metaFields = 'lp_' . strtolower(THEMENAME) . '_options_fields';
									$fields = get_post_meta($postID, $metaFields, true);
									if (is_array($fields) && array_key_exists('lp_feature', $fields)) {
										unset($fields['lp_feature']);
										update_post_meta($postID, $metaFields, $fields);
									}
								}

								$priceStatus = isset($_POST['price_status']) ? sanitize_text_field($_POST['price_status']) : null;
								$listTagline = isset($_POST['tagline_text']) ? sanitize_text_field($_POST['tagline_text']) : null;
								$listLatitude = isset($_POST['latitude']) ? sanitize_text_field($_POST['latitude']) : null;
								$listLongitude = isset($_POST['longitude']) ? sanitize_text_field($_POST['longitude']) : null;
								$listPhone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : null;
								$listWhatsapp = isset($_POST['whatsapp']) ? sanitize_text_field($_POST['whatsapp']) : null;
								$listWebsite = isset($_POST['website']) ? sanitize_text_field($_POST['website']) : null;
								$listTwitter = isset($_POST['twitter']) ? sanitize_text_field($_POST['twitter']) : null;
								$listFacebook = isset($_POST['facebook']) ? sanitize_text_field($_POST['facebook']) : null;
								$listLinkedin = isset($_POST['linkedin']) ? sanitize_text_field($_POST['linkedin']) : null;
								$listYoutube = isset($_POST['youtube']) ? sanitize_text_field($_POST['youtube']) : null;
								$listInstagram = isset($_POST['instagram']) ? sanitize_text_field($_POST['instagram']) : null;
								$listPrice = isset($_POST['listingprice']) ? sanitize_text_field($_POST['listingprice']) : null;
								$listPText = isset($_POST['listingptext']) ? sanitize_text_field($_POST['listingptext']) : null;
								$listPostVideo = isset($_POST['postVideo']) ? sanitize_text_field($_POST['postVideo']) : null;

								listing_set_metabox('business_hours', $business_hours, $postID);
								listing_set_metabox('price_status', $priceStatus, $postID);
								listing_set_metabox('faqs', $faqs, $postID);
								listing_set_metabox('tagline_text', $listTagline, $postID);
								listing_set_metabox('gAddress', $gaddpress, $postID);
								listing_set_metabox('latitude', $listLatitude, $postID);
								listing_set_metabox('longitude', $listLongitude, $postID);
								listing_set_metabox('phone', $listPhone, $postID);
								listing_set_metabox('whatsapp', $listWhatsapp, $postID);
								listing_set_metabox('email', $email, $postID);
								listing_set_metabox('website', $listWebsite, $postID);
								listing_set_metabox('twitter', $listTwitter, $postID);
								listing_set_metabox('facebook', $listFacebook, $postID);
								listing_set_metabox('linkedin', $listLinkedin, $postID);
								listing_set_metabox('youtube', $listYoutube, $postID);
								listing_set_metabox('instagram', $listInstagram, $postID);
								listing_set_metabox('Plan_id', $plan_id, $postID);
								listing_set_metabox('list_price', $listPrice, $postID);
								listing_set_metabox('list_price_to', $listPText, $postID);
								listing_set_metabox('video', $listPostVideo, $postID);
								listing_set_metabox('claimed_section', 'not_claimed', $postID);
								add_post_meta($postID, 'listing_rate', '');
								add_post_meta($postID, 'listing_reviewed', '');

								update_post_meta($postID, 'plan_id', $plan_id);

								if (!empty($preselctedCat)) {
									//for pre selected category
									update_post_meta($postID, 'preselected', true);
								}

								if (isset($_FILES["business_logo"])) {
									if ($_FILES['business_logo']['size'] != 0) {
										$files2 = $_FILES["listingfiles"];
										$files3 = $_FILES["lp-featuredimage"];
										$files = $_FILES["business_logo"];
										foreach ($files['name'] as $key => $value) {
											if ($files['name'][$key]) {
												$file = array(
													'name' => $files['name'][$key],
													'type' => $files['type'][$key],
													'tmp_name' => $files['tmp_name'][$key],
													'error' => $files['error'][$key],
													'size' => $files['size'][$key]
												);
												$_FILES = array("business_logo" => $file);
												$file_type = wp_check_filetype($file['name']);
												$file_type = $file_type['type'];
												if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
													$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
													die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
												}
												$count = 0;
												foreach ($_FILES as $file => $array) {
													//$newupload = listingpro_handle_attachment_featured($file,$postID);
													$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
													$b_logo_url = wp_get_attachment_url($newupload);

													listing_set_metabox('business_logo', $b_logo_url, $postID);
												}
											}
											$_FILES["listingfiles"] = '';
											$_FILES["listingfiles"] = $files2;
											$_FILES["lp-featuredimage"] = $files3;
										}
									}
								}
								$featuredimageset = false;
								if (isset($_FILES["lp-featuredimage"])) {
									if ($_FILES['lp-featuredimage']['size'] != 0) {
										$featuredimageset = true;
										$files2 = $_FILES["listingfiles"];
										$files = $_FILES["lp-featuredimage"];
										foreach ($files['name'] as $key => $value) {
											if ($files['name'][$key]) {
												$file = array(
													'name' => $files['name'][$key],
													'type' => $files['type'][$key],
													'tmp_name' => $files['tmp_name'][$key],
													'error' => $files['error'][$key],
													'size' => $files['size'][$key]
												);
												$file_type = wp_check_filetype($file['name']);
												$file_type = $file_type['type'];
												if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
													$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
													die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
												}
												$_FILES = array("lp-featuredimage" => $file);
												$count = 0;
												foreach ($_FILES as $file => $array) {
													$newupload = listingpro_handle_attachment_featured($file, $postID);
												}
											}
											$_FILES["listingfiles"] = '';
											$_FILES["listingfiles"] = $files2;
										}
									}
								}


								if (isset($_FILES["listingfiles"])) {
									if ($_FILES['listingfiles']['size'] != 0) {
										$files = $_FILES["listingfiles"];
										//$files2 = $_FILES["lp-featuredimage"];  			
										foreach ($files['name'] as $key => $value) {
											if ($files['name'][$key]) {
												$file = array(
													'name' => $files['name'][$key],
													'type' => $files['type'][$key],
													'tmp_name' => $files['tmp_name'][$key],
													'error' => $files['error'][$key],
													'size' => $files['size'][$key]
												);
												$file_type = wp_check_filetype($file['name']);
												$file_type = $file_type['type'];
												if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
													$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
													die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
												}
												$_FILES = array("listingfiles" => $file);
												$count = 0;
												foreach ($_FILES as $file => $array) {

													if (empty($featuredimageset) && $featImgFromGal == true && !isset($_FILES["lp-featuredimage"])) {
														if (!has_post_thumbnail($postID)) {
															$newupload = listingpro_handle_attachment($file, $postID, $set_thu = true);
														} else {
															$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
														}
													} else {
														$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
													}
													$ids[] = $newupload;
													$count++;
												}
											}
										}
										if (!empty($ids) && is_array($ids)) {
											$img_ids = implode(",", $ids);
											update_post_meta($postID, 'gallery_image_ids', $img_ids);
											//$_FILES['lp-featuredimage'] = $files2;

										}
									}
								}


								/* mail for user */
								$u_mail_subject_a = '';
								$u_mail_body_a = '';
								$u_mail_subject = $listingpro_options['listingpro_subject_new_submit_listing'];
								$u_mail_body = $listingpro_options['listingpro_new_submit_listing_content'];

								$u_mail_subject_a = lp_sprintf2("$u_mail_subject", array(
									'website_url' => "$website_url",
									'listing_title' => "$listing_title",
									'listing_url' => "$listing_url",
									'website_name' => "$website_name",
									'user_name' => "$user_name",
								));

								$u_mail_body_a = lp_sprintf2("$u_mail_body", array(
									'website_url' => "$website_url",
									'listing_title' => "$listing_title",
									'listing_url' => "$listing_url",
									'website_name' => "$website_name",
									'user_name' => "$user_name",
								));


								wp_mail($email, $u_mail_subject_a, $u_mail_body_a, $headers);

								/* mail for admin */
								$a_mail_subject_a = '';
								$a_mail_body_a = '';
								$a_mail_subject = $listingpro_options['listingpro_subject_new_submit_listing_admin'];
								$a_mail_body = $listingpro_options['listingpro_new_submit_listing_content_admin'];

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
								wp_mail($admin_email, $a_mail_subject_a, $a_mail_body_a, $headers);




								if ($lp_paid_mode == "yes" && $planHavePrice == true) {
									listing_draft_save($postID, $user_id);
								}

								$response = get_the_permalink($postID);
								$msg = esc_html__('Success! Submission is successful', 'listingpro-plugin');
								die(json_encode(array('response' => 'success', 'status' => $response, 'newuser' => $user_id, 'msg' => $msg)));
							}
							//}
						}
					}
					/* not loggedin ends */
				}
			}


			/* edit post */
			if (isset($_POST['edit_nonce_field']) && wp_verify_nonce($_POST['edit_nonce_field'], 'edit_nonce')) {

				global $listingpro_options;

				$lp_post = '';
				$form_field = '';
				$faqs = '';
				$faq = '';
				$faqans = '';
				$gAddress = '';
				$latitude = '';
				$longitude = '';
				$phone = '';
				$email = '';
				$website = '';
				$twitter = '';
				$facebook = '';
				$linkedin = '';
				$listingprice = '';
				$listingptext = '';
				$video = '';
				$lp_post = sanitize_text_field($_POST['lp_post']);
				$prevAddress = listing_get_metabox_by_ID('gAddress', $lp_post);
				$exGalIds = '';
				if (isset($_POST['listingeditfiles'])) {
					$exGalIds = $_POST['listingeditfiles'];
				}
				$total_new_images   = '';



				/* for validations */
				$errors = array();
				if (empty($_POST['postTitle'])) {
					$errors['postTitle'] = 'postTitle';
				}
				/* if(empty($_POST['gAddress'])){
						$errors['gAddress'] = 'gAddress';
					} */
				if (empty($_POST['category']) || !array_filter($_POST['category'])) {
					$errors['category'] = 'category';
				}

				if (empty($_POST['postContent'])) {
					$errors['postContent'] = 'postContent';
				}
				/* for size validations */
				$counterLimit = null;
				$sizeLimit = null;

				$totalGalSize = null;
				$totalGalCount = null;
				$msg2 = '';
				$counterSwitch = lp_theme_option('lp_listing_images_count_switch');
				$sizeSwitch = lp_theme_option('lp_listing_images_size_switch');
				if ($counterSwitch == "yes" || $sizeSwitch == "yes") {
					if ($counterSwitch == "yes") {
						$counterLimit = (int) lp_theme_option('lp_listing_images_counter');
					}
					if ($sizeSwitch == "yes") {
						$sizeLimit = lp_theme_option('lp_listing_images_size_switch');
						$sizeLimit =  (int) $sizeLimit * 1000000;
					}


					if (isset($_FILES["listingfiles"])) {

						if ($_FILES['listingfiles']['size'] != 0) {

							if (isset($_FILES["listingfiles"])) {
								if ($_FILES['listingfiles']['size'] != 0) {
									$files = $_FILES["listingfiles"];
									$totalGalCount = 0;
									foreach ($files['name'] as $key => $value) {
										$file_type = $files['type'][$key];
										if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
											$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
											die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
										}
										if ($files['name'][$key]) {
											$singleGal = $files['size'][$key];
											$totalGalSize = $totalGalSize + $singleGal;
											$totalGalCount++;
										}
									}
								}
							}


							if (!empty($counterLimit) && !empty($totalGalCount)) {
								$galleryImagesIDS = get_post_meta($lp_post, 'gallery_image_ids', true);
								if (!empty($galleryImagesIDS)) {
									$galleryImagesIDS = explode(',', $galleryImagesIDS);
									$galleryImagesIDS_count =   count($galleryImagesIDS);

									$total_new_images   =   $_POST['imageCount'];
									if ($total_new_images > $counterLimit) {
										$errors['listingfiles'] = 'listingfiles';
										$msg2 = esc_html__('Max. allowed images are ', 'listingpro-plugin');
										$msg2 .= $counterLimit;
									}
								}

								if ($totalGalCount > $counterLimit) {
									$errors['listingfiles'] = 'listingfiles';
									$msg2 = esc_html__('Max. allowed images are ', 'listingpro-plugin');
									$msg2 .= $counterLimit;
								}
							}
							if (!empty($sizeLimit) && !empty($totalGalSize)) {
								if ($totalGalSize > $sizeLimit) {
									$errors['listingfiles'] = 'listingfiles';
									$msg2 = esc_html__('Max. allowed images size is ', 'listingpro-plugin');
									$msg2 .= $sizeLimit . '';
									$msg2 .= 'Mb';
								}
							}
						}
					}
				}
				/* end for size validation */

				if (!empty($errors) && count($errors) > 0) {
					$msg = esc_html__('Sorry! There is problem in listing submission', 'listingpro-plugin');
					die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
				} else {

					$gaddpress = '';
					$gaddpress = $prevAddress;
					if (isset($_POST['gAddress']) && !empty($_POST['gAddress'])) {
						$gaddpressA = sanitize_text_field($_POST['gAddress']);
						if ($prevAddress != $gaddpressA) {
							$gaddpress = $gaddpressA;
						}
					}
					if (isset($_POST['gAddresscustom']) && !empty($_POST['gAddresscustom'])) {
						$gaddpressC = sanitize_text_field($_POST['gAddresscustom']);
						if ($prevAddress != $gaddpressC) {
							$gaddpress = $gaddpressC;
						}
					}

					$featuresID = '';
					if (isset($_POST['lp_form_fields_inn'])) {
						if (isset($_POST['lp_form_fields_inn']['lp_feature']) && !empty($_POST['lp_form_fields_inn']['lp_feature'])) {
							$featuresID = $_POST['lp_form_fields_inn']['lp_feature'];
						}
					}
					$tags = '';
					if (isset($_POST['tags']) && !empty($_POST['tags'])) {
						$tags = $_POST['tags'];
						$tags = explode(',', $tags);
					}
					/* locations */

					$listLocation = '';
					$lpsLoc = '';
					$locations_type = $listingpro_options['lp_listing_locations_options'];
					if ($locations_type == "auto_loc") {
						if (isset($_POST['location'])) {
							if (!empty($_POST['location'])) {
								if (is_array($_POST['location'])) {
									$lpsLoc = array();
									$listLocation = $_POST['location'];
									$locCount = 0;
									foreach ($_POST['location'] as $locnames) {
										$insertLoc = listingpro_insert_term($locnames, 'location');
										if (!empty($insertLoc)) {
											$lpsLoc[$locCount] = $insertLoc['term_id'];
										} else {
											$lpLocObj = get_term_by('name', $locnames, 'location');
											if (!empty($lpLocObj)) {
												$lpsLoc[$locCount] = $lpLocObj->term_id;
											} else {
												$lpsLoc[$locCount] = '';
											}
										}

										$locCount++;
									}
								} else {
									$listLocation = sanitize_text_field($_POST['location']);
									$insertLoc = listingpro_insert_term($listLocation, 'location');
									if (!empty($insertLoc)) {
										$lpsLoc = $insertLoc['term_id'];
									} else {
										$lpLocObj = get_term_by('name', $listLocation, 'location');
										if (!empty($lpLocObj)) {
											$lpsLoc = $lpLocObj->term_id;
										} else {
											$lpsLoc = '';
										}
									}
								}
							}
						}
					} else {
						$lpsLoc = $_POST['location'];
					}

					$lp_listing_locations;
					$lp_listing_cats;
					if (!empty($lpsLoc) && is_array($lpsLoc)) {
						$lp_listing_locations = $lpsLoc;
					} else {
						$lp_listing_locations = array($lpsLoc);
					}
					/* ends locations */

					$lp_listing_cats;

					if (isset($_POST['category'])) {
						$lpcatss = $_POST['category'];
						if (!empty($lpcatss) && is_array($lpcatss)) {
							$lp_listing_cats = $lpcatss;
						} else {
							$lp_listing_cats = array($lpcatss);
						}
					}


					$listingEditStatus = 'publish';
					if (get_post_status($lp_post) == 'publish') {
						$listingEditStatus = 'publish';
						if (isset($listingpro_options['edit_listing_pending_switch'])) {
							if ($listingpro_options['edit_listing_pending_switch'] == true) {
								$listingEditStatus = 'pending';
							}
						}
					} elseif (get_post_status($lp_post) == 'pending' ||  get_post_status($lp_post) == 'draft') {
						$listingEditStatus = 'pending';
					} elseif (get_post_status($lp_post) == 'expired') {
						$listingEditStatus = 'expired';
					}
					$featuresArr = array();
					if (!empty($featuresID) && is_array($featuresID)) {
						foreach ($featuresID as $k => $termID) {
							$termID = (int) $termID;
							if (term_exists($termID, 'features')) $featuresArr[] = $termID;
						}
					}
					$post_information = array(
						'ID'           => $lp_post,
						'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
						'post_name' => '',
						'post_content' => wp_kses_post($_POST['postContent']),
						'post_type' => 'listing',
						'tax_input' => array(
							'location' => $lp_listing_locations,
							'listing-category' => $lp_listing_cats,
							'features' => $featuresArr,
							'list-tags' => $tags,
						),
						'post_status' => $listingEditStatus
					);

					wp_update_post($post_information);

					$postID = $lp_post;
					$author_id = get_post_field('post_author', $postID);
					$author_info = get_userdata($author_id);

					$business_hours = $_POST['business_hours'];
					if (isset($_POST['faq']) && isset($_POST['faqans'])) {
						$oldKeyQ = 1;
						$arrQ = array();
						foreach ($_POST['faq'] as $keyQ => $valQ) {
							if ($valQ != '') {
								$arrQ[$oldKeyQ] = $valQ;
								$oldKeyQ++;
							}
						}
						$oldKeyA = 1;
						$arrA = array();
						foreach ($_POST['faqans'] as $keyA => $valA) {
							if ($valA != '') {
								$arrA[$oldKeyA] = $valA;
								$oldKeyA++;
							}
						}
						$faqs;
						$faqs = array('faq' => $arrQ, 'faqans' => $arrA);
					}

					if (isset($_POST['lp_form_fields_inn'])) {
						$fields = $_POST['lp_form_fields_inn'];
						$filterArray = lp_save_extra_fields_in_listing($fields, $postID);
						if( is_array($fields) && is_array($filterArray) ){
							$fields = array_merge($fields,$filterArray);
						}
						$metaFields = 'lp_' . strtolower(THEMENAME) . '_options_fields';
						update_post_meta($postID, $metaFields, $fields);
					} else {
						$metaFields = 'lp_' . strtolower(THEMENAME) . '_options_fields';
						$fields = get_post_meta($postID, $metaFields, true);
						if (is_array($fields) && array_key_exists('lp_feature', $fields)) {
							unset($fields['lp_feature']);
							update_post_meta($postID, $metaFields, $fields);
						}
					}

					$priceStatus = isset($_POST['price_status']) ? sanitize_text_field($_POST['price_status']) : null;
					$listTagline = isset($_POST['tagline_text']) ? sanitize_text_field($_POST['tagline_text']) : null;
					$listLatitude = isset($_POST['latitude']) ? sanitize_text_field($_POST['latitude']) : null;
					$listLongitude = isset($_POST['longitude']) ? sanitize_text_field($_POST['longitude']) : null;
					$listPhone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : null;
					$listWhatsapp = isset($_POST['whatsapp']) ? sanitize_text_field($_POST['whatsapp']) : null;
					$listEmail = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
					$listWebsite = isset($_POST['website']) ? sanitize_text_field($_POST['website']) : null;
					$listTwitter = isset($_POST['twitter']) ? sanitize_text_field($_POST['twitter']) : null;
					$listFacebook = isset($_POST['facebook']) ? sanitize_text_field($_POST['facebook']) : null;
					$listLinkedin = isset($_POST['linkedin']) ? sanitize_text_field($_POST['linkedin']) : null;
					$listYoutube = isset($_POST['youtube']) ? sanitize_text_field($_POST['youtube']) : null;
					$listInstagram = isset($_POST['instagram']) ? sanitize_text_field($_POST['instagram']) : null;
					$listPlanid = isset($_POST['plan_id']) ? sanitize_text_field($_POST['plan_id']) : null;
					lp_listing_save_additional_metas($listPlanid, $postID);
					$listPrice = isset($_POST['listingprice']) ? sanitize_text_field($_POST['listingprice']) : null;
					$listPText = isset($_POST['listingptext']) ? sanitize_text_field($_POST['listingptext']) : null;
					$listPostVideo = isset($_POST['postVideo']) ? sanitize_text_field($_POST['postVideo']) : null;
					$listClaimedsec = isset($_POST['claimed_section']) ? sanitize_text_field($_POST['claimed_section']) : null;
					if (empty($listEmail)) {
						$listEmail = $author_info->user_email;
					}
					listing_set_metabox('business_hours', $business_hours, $postID);
					listing_set_metabox('price_status', $priceStatus, $postID);
					listing_set_metabox('faqs', $faqs, $postID);
					listing_set_metabox('tagline_text', $listTagline, $postID);
					listing_set_metabox('gAddress', $gaddpress, $postID);
					listing_set_metabox('latitude', $listLatitude, $postID);
					listing_set_metabox('longitude', $listLongitude, $postID);
					listing_set_metabox('phone', $listPhone, $postID);
					listing_set_metabox('whatsapp', $listWhatsapp, $postID);
					listing_set_metabox('email', $listEmail, $postID);
					listing_set_metabox('website', $listWebsite, $postID);
					listing_set_metabox('twitter', $listTwitter, $postID);
					listing_set_metabox('facebook', $listFacebook, $postID);
					listing_set_metabox('linkedin', $listLinkedin, $postID);
					listing_set_metabox('youtube', $listYoutube, $postID);
					listing_set_metabox('instagram', $listInstagram, $postID);
					listing_set_metabox('list_price', $listPrice, $postID);
					listing_set_metabox('list_price_to', $listPText, $postID);
					listing_set_metabox('video', $listPostVideo, $postID);
					listing_set_metabox('claimed_section', $listClaimedsec, $postID);
					//update_post_meta($postID, 'gallery_image_ids', '');

					$ids = array();

					/* print_r($_FILES["listingfiles"]);
						exit; */
					if (isset($_FILES["business_logo"])) {
						if ($_FILES['business_logo']['size'] != 0) {
							$files = $_FILES["business_logo"];
							$files1 = $_FILES["lp-featuredimage"];
							$files2 = $_FILES["listingfiles"];
							foreach ($files['name'] as $key => $value) {
								if ($files['name'][$key]) {
									$file = array(
										'name' => $files['name'][$key],
										'type' => $files['type'][$key],
										'tmp_name' => $files['tmp_name'][$key],
										'error' => $files['error'][$key],
										'size' => $files['size'][$key]
									);
									$file_type = wp_check_filetype($file['name']);
									$file_type = $file_type['type'];
									if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
										$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
										die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
									}
									$_FILES = array("business_logo" => $file);
									$count = 0;
									foreach ($_FILES as $file => $array) {
										//$newupload = listingpro_handle_attachment_featured($file,$postID);
										$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
										$b_logo_url = wp_get_attachment_url($newupload);
										listing_set_metabox('business_logo', $b_logo_url, $postID);
									}
								}
								$_FILES["lp-featuredimage"] = $files1;
								$_FILES["listingfiles"] = $files2;
							}
						}
					}
					$featuredimageset = false;
					if (isset($_FILES["lp-featuredimage"])) {
						if ($_FILES['lp-featuredimage']['size'] != 0) {
							$featuredimageset = true;
							$files = $_FILES["lp-featuredimage"];
							$files2 = $_FILES["listingfiles"];
							foreach ($files['name'] as $key => $value) {
								if ($files['name'][$key]) {
									$file = array(
										'name' => $files['name'][$key],
										'type' => $files['type'][$key],
										'tmp_name' => $files['tmp_name'][$key],
										'error' => $files['error'][$key],
										'size' => $files['size'][$key]
									);
									$file_type = wp_check_filetype($file['name']);
									$file_type = $file_type['type'];
									if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
										$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
										die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
									}
									$_FILES = array("lp-featuredimage" => $file);
									$count = 0;
									foreach ($_FILES as $file => $array) {
										$newupload = listingpro_handle_attachment_featured($file, $postID);
									}
								}
								$_FILES["listingfiles"] = '';
								$_FILES["listingfiles"] = $files2;
							}
						}
					}

					//previous images in galleryImagesIDS
					$galleryImagesIDS = get_post_meta($postID, 'gallery_image_ids', true);
					if (!empty($exGalIds)) {
						$gallIDArr = explode(",", $galleryImagesIDS);
						$gallIDArr2 = array_intersect($exGalIds, $gallIDArr);
						if (!empty($gallIDArr2)) {
							$img_ids = implode(",", $gallIDArr2);

							update_post_meta($postID, 'gallery_image_ids', $img_ids);
						}
					} else {
						//remove all images from gallery
						delete_post_meta($postID, 'gallery_image_ids');
					}

					$result;
					if (isset($_FILES["listingfiles"])) {
						if ($_FILES['listingfiles']['size'] != 0) {
							$files = $_FILES["listingfiles"];
							foreach ($files['name'] as $key => $value) {
								if ($files['name'][$key]) {
									$file = array(
										'name' => $files['name'][$key],
										'type' => $files['type'][$key],
										'tmp_name' => $files['tmp_name'][$key],
										'error' => $files['error'][$key],
										'size' => $files['size'][$key]
									);
									$file_type = wp_check_filetype($file['name']);
									$file_type = $file_type['type'];
									if (!empty($file_type) && !in_array($file_type, $allowed_mimes)) {
										$msg = esc_html__('Sorry! Your are not allowed to upload this type of file!', 'listingpro-plugin');
										die(json_encode(array('response' => 'fail', 'status' => $errors, 'msg' => $msg)));
									}
									$_FILES = array("listingfiles" => $file);
									$count = 0;
									if (!empty($_FILES)) {
										foreach ($_FILES as $file => $array) {

											if (empty($featuredimageset) && $featImgFromGal == true && !isset($_FILES["lp-featuredimage"])) {
												if (!has_post_thumbnail($postID)) {
													$newupload = listingpro_handle_attachment($file, $postID, $set_thu = true);
												} else {
													$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
												}
											} else {
												$newupload = listingpro_handle_attachment($file, $postID, $set_thu = false);
											}
											array_push($ids, $newupload);
											$count++;
										}
									}
								}
							}



							if (!empty($ids)) {
								if (!empty($gallIDArr2)) {
									$result = array_merge($gallIDArr2, $ids);
									$img_ids = implode(",", $result);
								} else {
									$img_ids = implode(",", $ids);
									$result = $ids;
								}
								update_post_meta($postID, 'gallery_image_ids', $img_ids);
							}
						}
					}



					/* print_r($result);
							exit; */



					if (isset($_POST['listImg_remove']) && !empty($_POST['listImg_remove'])) {
						$removeIDS = $_POST['listImg_remove'];
						$galleryImagesIDS = get_post_meta($postID, 'gallery_image_ids', true);
						$galleryImagesIDS = explode(',', $galleryImagesIDS);
						foreach ($removeIDS as $key) {
							$index = array_search($key, $galleryImagesIDS);
							if ($index !== false) {
								unset($galleryImagesIDS[$index]);
							}
						}
						$fIDS = implode(",", $galleryImagesIDS);
						update_post_meta($postID, 'gallery_image_ids', $fIDS);
					}
					$response = get_the_permalink($postID);
					$msg = esc_html__('Success! Submission is successful', 'listingpro-plugin');
					die(json_encode(array('llllc' => $lp_listing_locations, 'response' => 'success', 'status' => $response, 'responsed' => $count, 'responsedds' => $galleryImagesIDS, 'msg' => $msg)));
				}
			}
		} else {
			$msg = esc_html__('Please Fill The Required Fields', 'listingpro-plugin');
			$response = '<span class="email-exist-error error-msg">' . esc_html__("Please check captcha", "listingpro-plugin") . '</span>';
			die(json_encode(array('response' => 'failure', 'status' => $response, 'dff' => $processSubmission, 'msg' => $msg)));
		}

		lp_mail_headers_remove();
	}
}
