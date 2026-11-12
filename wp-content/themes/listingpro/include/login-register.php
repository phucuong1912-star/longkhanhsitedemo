<?php

/**
 * Login And Register.
 *
 */


/* ============== Listingpro Login/Register  ============ */

if (!function_exists('Listingpro_ajax_login_init')) {
	function Listingpro_ajax_login_init()
	{
		global $listingpro_options;
		$dashURL = LP_current_URL();
		if ($dashURL) {
			$dashURL = $dashURL;
		} else {
			$dashURL = home_url();
		}

		wp_register_script('ajax-login-script', get_template_directory_uri() . '/assets/js/login.js', array('jquery'));
		wp_enqueue_script('ajax-login-script');

		wp_localize_script('ajax-login-script', 'ajax_login_object', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'redirecturl' => $dashURL,
			'loadingmessage' => '<span class="alert alert-info">' . esc_html__('Please wait...', 'listingpro') . '<i class="fa fa-spinner fa-spin"></i></span>',
		));

		// Enable the user with no privileges to run ajax_login() in AJAX
		add_action('wp_ajax_nopriv_ajaxlogin', 'ajax_login');


		add_action('wp_ajax_ajax_register',        'ajax_register');
		add_action('wp_ajax_nopriv_ajax_register', 'ajax_register');

		/* forget password */
		add_action('wp_ajax_ajax_forget_pass',        'ajax_forget_pass');
		add_action('wp_ajax_nopriv_ajax_forget_pass', 'ajax_forget_pass');
	}
}


// Execute the action only if the user isn't logged in
if (!is_user_logged_in()) {
	add_action('init', 'Listingpro_ajax_login_init');
}


/* ============== Listingpro Login/Register  ============ */

if (!function_exists('ajax_login')) {
	function ajax_login()
	{

		global $listingpro_options;
		$enableCaptchaLogin = false;
		$processLogin = true;
		if (isset($_POST['g-recaptcha-response'])) {
			if (!empty($_POST['g-recaptcha-response'])) {
				$enableCaptchaLogin = true;
			} else {
				$processLogin = false;
			}
		} else {
			$enableCaptchaLogin = false;
			$processLogin = true;
		}
		$keyResponse = '';

		if ($enableCaptchaLogin == true) {
			if (class_exists('cridio_Recaptcha')) {
				$keyResponse = cridio_Recaptcha_Logic::is_recaptcha_valid(sanitize_text_field($_POST['g-recaptcha-response']));
				if ($keyResponse == false) {
					$processLogin = false;
				} else {
					$processLogin = true;
				}
			}
		}

		if ($processLogin == true) {

			// First check the nonce, if it fails the function will break
			check_ajax_referer('ajax-login-nonce', 'security');

			// Nonce is checked, get the POST data and sign user on
			$info = array();
			$info['user_login'] = sanitize_text_field($_POST['username']);
			$info['user_password'] = sanitize_text_field($_POST['password']);
			$info['remember'] = true;
			if (is_ssl()) {
				$user_signon = wp_signon($info, true);
			} else {
				$user_signon = wp_signon($info, false);
			}

			if (is_wp_error($user_signon)) {
				echo json_encode(array('loggedin' => false, 'message' => '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i>' . esc_html__('Wrong username or password.', 'listingpro') . ' </span>'));
			} else {
				echo json_encode(array('loggedin' => true, 'message' => '<span class="alert alert-success"><i class="fa fa-check" aria-hidden="true"></i> ' . esc_html__('Login successful, redirecting...', 'listingpro') . '</span>'));
			}
		} else {
			echo json_encode(array('loggedin' => false, 'message' => '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i>' . esc_html__('Please check captcha', 'listingpro') . ' </span>'));
		}

		die();
	}
}


/* ============== Listingpro Login/Register  ============ */

if (!function_exists('ajax_register')) {
	function ajax_register()
	{
		global $wpdb, $listingpro_options;
		if (!get_option('users_can_register')) {
			$note = '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i> ' . esc_html__('User registration is not allowed', 'listingpro') . '</span>';
			die(json_encode(array('register' => false, 'message' => $note)));
		}

		$enableCaptcha = false;
		$processRegister = true;
		if (isset($_POST['g-recaptcha-response'])) {
			if (!empty($_POST['g-recaptcha-response'])) {
				$processRegister = true;
			} else {
				$processRegister = false;
			}
		} else {
			$processRegister = true;
			$enableCaptcha = false;
		}

		$gCaptcha = null;
		if (isset($_POST['g-recaptcha-response'])) {
			$gCaptcha = sanitize_text_field($_POST['g-recaptcha-response']);
		}
		if (!empty($gCaptcha)) {
			$enableCaptcha = true;
		}
		$keyResponse = '';




		if ($enableCaptcha == true) {
			if (class_exists('cridio_Recaptcha')) {
				$keyResponse = cridio_Recaptcha_Logic::is_recaptcha_valid(sanitize_text_field($_POST['g-recaptcha-response']));
				if ($keyResponse == false) {
					$processRegister = false;
				} else {
					$processRegister = true;
				}
			}
		}

		if ($processRegister == true) {


			$enablepassword = false;
			if (isset($listingpro_options['lp_register_password'])) {
				if ($listingpro_options['lp_register_password'] == 1) {
					$enablepassword = true;
				}
			}

			$user_email = sanitize_email($_POST['email']);
			$user_name = sanitize_text_field($_POST['username']);
			$user_pass = '';
			if ($enablepassword == true) {
				$user_pass = sanitize_text_field($_POST['upassword']);
			}

			$error = false;
			$note = '';
			$user_id = username_exists($user_name);
			$settings =  get_option('users_can_register');
			if ($settings == 1 &&  !$user_id && email_exists($user_email) == false && !empty($user_email) && !empty($user_name) && is_email($user_email)) {

				$random_password = '';
				if (!empty($user_pass) && $enablepassword == true) {
					$random_password = $user_pass;
				} else {
					$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
				}


				$user_id = wp_create_user($user_name, $random_password, $user_email);

				if ($enablepassword == true) {
					//signing in
					$info = array();
					$info['user_login'] = $user_name;
					$info['user_password'] = $random_password;
					$info['remember'] = true;
					if (is_ssl()) {
						$user_signon = wp_signon($info, true);
					} else {
						$user_signon = wp_signon($info, false);
					}
				}


				/* for user */
				$subject = $listingpro_options['listingpro_subject_new_user_register'];
				$website_url = site_url();
				$website_name = get_option('blogname');

				$formated_subject = lp_sprintf2("$subject", array(
					'website_url' => "$website_url",
					'website_name' => "$website_name",
				));

				$mail_content = $listingpro_options['listingpro_new_user_register'];

				$formated_mail_content = lp_sprintf2("$mail_content", array(
					'website_url' => "$website_url",
					'website_name' => "$website_name",
					'user_login_register' => "$user_name",
					'user_pass_register' => "$random_password",
					'user_email_register' => "$user_email"
				));

				/* for admin */

				$subject2 = $listingpro_options['listingpro_subject_admin_new_user_register'];

				$mail_content2 = $listingpro_options['listingpro_admin_new_user_register'];
				$formated_mail_content2 = lp_sprintf2("$mail_content2", array(
					'website_url' => "$website_url",
					'website_name' => "$website_name",
					'user_login_register' => "$user_name",
					'user_email_register' => "$user_email"
				));

				$from = get_option('admin_email');
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$headers2[] = 'Content-Type: text/html; charset=UTF-8';
				lp_mail_headers_append();
				LP_send_mail($user_email, $formated_subject, $formated_mail_content, $headers);
				/*if(empty($user_pass) && $enablepassword==false){
					}*/
				LP_send_mail($from, $subject2, $formated_mail_content2, $headers2);
				lp_mail_headers_remove();


				$note = '';
				if (empty($user_pass) && $enablepassword == false) {
					$note = '<span class="alert alert-success"><i class="fa fa-check" aria-hidden="true"></i> ' . esc_html__('Go to your inbox or spam/junk and get your password', 'listingpro') . '</span>';
				} else {
					$note = '<span class="alert alert-success"><i class="fa fa-check" aria-hidden="true"></i> ' . esc_html__('Registration and login successfull, redirecting soon...', 'listingpro') . '</span>';
				}
			} elseif (email_exists($user_email) == true) {
				$error = true;
				$note = '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i>' . esc_html__(' This Email already exists', 'listingpro') . '</span>';
			} elseif (username_exists($user_name) == true) {
				$error = true;
				$note = '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i>' . esc_html__(' This Username already exists', 'listingpro') . '</span>';
			} elseif (empty($user_email)) {
				$error = true;
				$note = '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i>' . esc_html__(' Email field is empty', 'listingpro') . '</span>';
			} elseif (!is_email($user_email)) {
				$error = true;
				$note = '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i>' . esc_html__(' Please provide correct email.', 'listingpro') . '</span>';
			} elseif (empty($user_name)) {
				$error = true;
				$note = '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i>' . esc_html__(' Username field is empty.', 'listingpro') . '</span>';
			} elseif ($settings != 1) {
				$error = true;
				$note = '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i>' . esc_html__('User registration is not allowed', 'listingpro') . '</span>';
			}

			if ($error == true) {
				$final = json_encode(array('register' => false, 'message' => $note, 'receptcha' => $keyResponse, 'gString' => $gCaptcha));
			} else {
				$final = json_encode(array('register' => true, 'message' => $note, 'pass' => $user_pass, 'receptcha' => $keyResponse, 'gString' => $gCaptcha, 'password' => $enablepassword));
			}
		} else {
			$note = '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i>' . esc_html__('Please check captcha', 'listingpro') . '</span>';
			$final = json_encode(array('register' => false, 'message' => $note, 'receptcha' => $keyResponse, 'gString' => $gCaptcha));
		}

		die($final);
	}
}


/* ==================for forget password============ */

if (!function_exists('ajax_forget_pass')) {

	function ajax_forget_pass()
	{

		$note = '';
		$error = false;
		$final = '';

		$securityCheck = sanitize_text_field($_POST['security']);
		$user_email = sanitize_email($_POST['email']);
		if (isset($securityCheck) && !empty($securityCheck)) {
			if (email_exists($user_email) != false && !empty($user_email) && is_email($user_email)) {
				$random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);

				$userData = get_user_by('email', $user_email);
				$userID = $userData->ID;
				wp_set_password($random_password, $userID);

				$subject = esc_html__('Password changed', 'listingpro');
				$content = esc_html__('You new password is: ', 'listingpro');
				$content .= $random_password . '<br>';
				$content .= esc_html__('Kindly update your password from your dashboard profile', 'listingpro');
				lp_mail_headers_append();
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				LP_send_mail($user_email,  $subject, $content, $headers);
				lp_mail_headers_remove();
				$note = '<span class="alert alert-success"><i class="fa fa-check" aria-hidden="true"></i> ' . esc_html__('Go to your inbox or spam/junk and get your password', 'listingpro') . '</span>';
			} else {
				$error = true;
				$note = '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i>' . esc_html__(' Email not found in users list.', 'listingpro') . '</span>';
			}
		} else {
			$error = true;
			$note = '<span class="alert alert-danger"><i class="fa fa-times" aria-hidden="true"></i>' . esc_html__(' Security code not found.', 'listingpro') . '</span>';
		}

		if ($error == true) {
			$final = json_encode(array('register' => false, 'message' => $note));
		} else {
			$final = json_encode(array('register' => true, 'message' => $note));
		}

		die($final);
	}
}
