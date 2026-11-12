<?php
// code only use for non-loggedin users
/* ======================= calling offline js ======================== */

if (!function_exists('Listingpro_needlogin_init')) {
    function Listingpro_needlogin_init()
    {


        wp_register_script('ajax-needlogin-ajax', get_template_directory_uri() . '/assets/js/needlogin-ajax.js', array('jquery'));

        wp_enqueue_script('ajax-needlogin-ajax');


        wp_localize_script('ajax-needlogin-ajax', 'needlogin_object', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        ));
    }
    if (!is_admin()) {
        add_action('init', 'Listingpro_needlogin_init');
    }
}



/* ================Login HTML Ajax Callback=================== */
add_action('wp_ajax_nopriv_listingpro_loginpopup', 'listingpro_loginpopup');
if (!function_exists('listingpro_loginpopup')) {
    function listingpro_loginpopup()
    {
        check_ajax_referer('lp_ajax_nonce', 'lpNonce');
        // Nonce is checked, get the POST data and sign user on
        if (!wp_verify_nonce(sanitize_text_field($_POST['lpNonce']), 'lp_ajax_nonce')) {
            $res = json_encode(array('nonceerror' => 'yes'));
            die($res);
        }
        global $listingpro_options;
        $enablepassword = false;
        if (isset($listingpro_options['lp_register_password'])) {
            if ($listingpro_options['lp_register_password'] == 1) {
                $enablepassword = true;
            }
        }
        $gSiteKey = '';
        $gSiteKey = $listingpro_options['lp_recaptcha_site_key'];
        $enableCaptcha = lp_check_receptcha('lp_recaptcha_registration');
        $enableCaptchaLogin = lp_check_receptcha('lp_recaptcha_login');
        $privacy_policy = $listingpro_options['payment_terms_condition'];

        $output = null;
        ob_start();

        //buffered code goes here
?>


        <div class="login-form-popup lp-border-radius-8">
            <div class="siginincontainer">
                <h3 class="text-center"><?php esc_html_e('Sign in', 'listingpro'); ?></h3>
                <form id="login" class="form-horizontal margin-top-30" method="post" data-lp-recaptcha="<?php echo wp_kses_post($enableCaptchaLogin); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>">
                    <p class="status"></p>
                    <div class="form-group">
                        <label for="username"><?php esc_html_e('Username or Email Address *', 'listingpro'); ?></label>
                        <input type="text" class="form-control" id="lpusername" name="lpusername" required />
                    </div>
                    <div class="form-group">
                        <label for="password"><?php esc_html_e('Password *', 'listingpro'); ?></label>
                        <input type="password" class="form-control" id="lppassword" name="lppassword" required />
                    </div>

                    <div class="form-group">
                        <div class="checkbox pad-bottom-10">
                            <input id="check1" type="checkbox" name="remember" value="yes">
                            <label for="check1"><?php esc_html_e('Keep me signed in', 'listingpro'); ?></label>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="submit" value="<?php esc_html_e('Sign in', 'listingpro'); ?>" class="lp-secondary-btn width-full btn-first-hover" />
                    </div>
                    <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                </form>
                <div class="pop-form-bottom">
                    <!-- New update 2.6.10 -->
                    <?php
                    $settings =  get_option('users_can_register');
                    ?>
                    <?php if ($settings == 1) { ?>
                        <div class="bottom-links">
                            <a class="signUpClick"><?php esc_html_e('Not a member? Sign up', 'listingpro'); ?></a>
                            <a class="forgetPasswordClick pull-right"><?php esc_html_e('Forgot Password', 'listingpro'); ?></a>
                        </div>
                    <?php  } else { ?>
                        <div class="bottom-links">
                            <a class="forgetPasswordClick pull-right"><?php esc_html_e('Forgot Password', 'listingpro'); ?></a>
                        </div>
                    <?php } ?>
                    <!-- End New update 2.6.10 -->
                    <?php
                    if (class_exists('NextendSocialLogin')) {
                        if (count(NextendSocialLogin::$enabledProviders) > 0) {
                    ?>
                            <p class="margin-top-15"><?php esc_html_e('Connect with your Social Network', 'listingpro'); ?></p>

                            <ul class="social-login list-style-none">
                                <?php

                                foreach (NextendSocialLogin::$providers as $provider) {

                                    $state         = $provider->getState();
                                    $providerLable = $provider->getLabel();
                                    switch ($state) {
                                        case 'enabled':
                                            if ($providerLable == "Google") {
                                                /* google is configured */
                                ?>
                                                <li>
                                                    <a id="logingoogle" class="google flaticon-googleplus" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=google" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=google&redirect='+window.location.href; return false;">
                                                        <span class="lp-pop-icon-img"><img alt="image" src="<?php echo listingpro_icons_url('google_login'); ?>"></span>
                                                        <span><?php esc_html_e('Google Login', 'listingpro'); ?></span>
                                                    </a>
                                                </li>
                                            <?php
                                            }

                                            if ($providerLable == "Facebook") {
                                                /* facebook is configured */
                                            ?>
                                                <li>
                                                    <a id="loginfacebook" class="facebook flaticon-facebook" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=facebook" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=facebook&redirect='+window.location.href; return false;">
                                                        <span class="lp-pop-icon-img"><img alt="image" src="<?php echo listingpro_icons_url('facebook_login'); ?>"></span>
                                                        <span><?php esc_html_e('Facebook Login', 'listingpro'); ?></span>
                                                    </a>
                                                </li>
                                            <?php
                                            }

                                            if ($providerLable == "X (formerly Twitter)") {
                                                /* twitter is configured */
                                            ?>
                                                <li>
                                                    <a id="logintwitter" class="twitter flaticon-twitter" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=twitter" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=twitter&redirect='+window.location.href; return false;">
                                                        <span class="lp-pop-icon-img"><img alt="image" src="<?php echo listingpro_icons_url('twitter_login'); ?>"></span>
                                                        <span><?php esc_html_e('Twitter Login', 'listingpro'); ?></span>
                                                    </a>
                                                </li>
                                <?php
                                            }
                                    }
                                }
                                ?>
                            </ul>
                    <?php
                        }
                    }
                    ?>
                </div>
                <a class="md-close"><i class="fa fa-close"></i></a>
            </div>

            <div class="siginupcontainer">
                <h3 class="text-center"><?php esc_html_e('Sign Up', 'listingpro'); ?></h3>
                <?php ob_start(); ?>
                <form id="register" class="form-horizontal margin-top-30" method="post" data-lp-recaptcha="<?php echo wp_kses_post($enableCaptcha); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>">
                    <p class="status"></p>
                    <div class="form-group">
                        <label for="username"><?php esc_html_e('Username *', 'listingpro'); ?></label>
                        <input type="text" class="form-control" id="username2" name="username" required />
                    </div>
                    <div class="form-group">
                        <label for="email"><?php esc_html_e('Email Address *', 'listingpro'); ?></label>
                        <input type="email" class="form-control" id="email" name="email" required />
                    </div>
                    <?php if ($enablepassword == true) { ?>
                        <div class="form-group">
                            <label for="upassword"><?php esc_html_e('Password *', 'listingpro'); ?></label>
                            <input type="password" class="form-control" id="upassword" name="upassword" required />
                        </div>
                    <?php } ?>
                    <?php if ($enablepassword == false) { ?>
                        <div class="form-group">
                            <p><?php esc_html_e('Password will be e-mailed to you.', 'listingpro'); ?></p>
                        </div>
                    <?php } ?>

                    <?php
                    if (!empty($privacy_policy)) {
                        $privacy_signup = $listingpro_options['listingpro_privacy_register'];
                        if ($privacy_signup == "yes") {
                            echo '
												<div class="checkbox form-group check_policy termpolicy pull-left termpolicy-wraper">
													<input id="check_policy" type="checkbox" name="policycheck" value="true">
													<label for="check_policy"><a target="_blank" href="' . get_the_permalink($privacy_policy) . '" class="help" target="_blank">' . esc_html__('I Agree', 'listingpro') . '</a></label>
													<div class="help-text">
														<a class="help" target="_blank"><i class="fa fa-question"></i></a>
														<div class="help-tooltip">
															<p>' . esc_html__('You agree & accept our Terms & Conditions to signup.', 'listingpro') . '</p>
														</div>
													</div>
												</div>';
                        }
                    }
                    ?>

                    <div class="clearfix padding-top-20 padding-bottom-20"></div>
                    <div class="form-group">
                        <input type="submit" value="<?php esc_html_e('Register', 'listingpro'); ?>" id="lp_usr_reg_btn" class="lp-secondary-btn width-full btn-first-hover" />
                    </div>
                    <?php wp_nonce_field('ajax-register-nonce', 'security2'); ?>
                </form>
                <?php
                //new code 2.6.15
                $contents = ob_get_contents();
                ob_end_clean();
                echo apply_filters('lp_register_popup_form', $contents);
                //end new code 2.6.15
                ?>
                <div class="pop-form-bottom">
                    <div class="bottom-links">
                        <a class="signInClick"><?php esc_html_e('Already have an account? Sign in', 'listingpro'); ?></a>
                        <a class="forgetPasswordClick pull-right"><?php esc_html_e('Forgot Password', 'listingpro'); ?></a>
                    </div>
                    <?php
                    if (class_exists('NextendSocialLogin')) {
                        if (count(NextendSocialLogin::$enabledProviders) > 0) {
                    ?>
                            <p class="margin-top-15"><?php esc_html_e('Connect with your Social Network', 'listingpro'); ?></p>

                            <ul class="social-login list-style-none">
                                <?php

                                foreach (NextendSocialLogin::$providers as $provider) {

                                    $state         = $provider->getState();
                                    $providerLable = $provider->getLabel();
                                    switch ($state) {
                                        case 'enabled':
                                            if ($providerLable == "Google") {
                                                /* google is configured */
                                ?>
                                                <li>
                                                    <a id="logingoogle" class="google flaticon-googleplus" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=google" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=google&redirect='+window.location.href; return false;">
                                                        <span class="lp-pop-icon-img"><img alt="image" src="<?php echo listingpro_icons_url('google_login'); ?>"></span>
                                                        <span><?php esc_html_e('Google Login', 'listingpro'); ?></span>
                                                    </a>
                                                </li>
                                            <?php
                                            }

                                            if ($providerLable == "Facebook") {
                                                /* facebook is configured */
                                            ?>
                                                <li>
                                                    <a id="loginfacebook" class="facebook flaticon-facebook" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=facebook" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=facebook&redirect='+window.location.href; return false;">
                                                        <span class="lp-pop-icon-img"><img alt="image" src="<?php echo listingpro_icons_url('facebook_login'); ?>"></span>
                                                        <span><?php esc_html_e('sign in With Facebook', 'listingpro'); ?></span>
                                                    </a>
                                                </li>
                                            <?php
                                            }

                                            if ($providerLable == "X (formerly Twitter)") {
                                                /* twitter is configured */
                                            ?>
                                                <li>
                                                    <a id="logintwitter" class="twitter flaticon-twitter" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=twitter" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=twitter&redirect='+window.location.href; return false;">
                                                        <span class="lp-pop-icon-img"><img alt="image" src="<?php echo listingpro_icons_url('twitter_login'); ?>"></span>
                                                        <span><?php esc_html_e('sign in With Twitter', 'listingpro'); ?></span>
                                                    </a>
                                                </li>
                                <?php
                                            }
                                    }
                                }
                                ?>
                            </ul>
                    <?php
                        }
                    }
                    ?>
                </div>
                <a class="md-close"><i class="fa fa-close"></i></a>
            </div>
            <div class="forgetpasswordcontainer">
                <h3 class="text-center"><?php esc_html_e('Forgotten Password', 'listingpro'); ?></h3>
                <form class="form-horizontal margin-top-30" id="lp_forget_pass_form" action="#" method="post">
                    <p class="status"></p>
                    <div class="form-group">
                        <label for="password"><?php esc_html_e('Email Address *', 'listingpro'); ?></label>
                        <input type="email" name="user_login" class="form-control" id="email3" required />
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" value="<?php esc_html_e('Get New Password', 'listingpro'); ?>" class="lp-secondary-btn width-full btn-first-hover" />
                        <?php wp_nonce_field('ajax-forgetpass-nonce', 'security3'); ?>
                    </div>
                </form>
                <div class="pop-form-bottom">
                    <div class="bottom-links">
                        <a class="cancelClick"><?php esc_html_e('Cancel', 'listingpro'); ?></a>
                    </div>
                </div>
                <a class="md-close"><i class="fa fa-close"></i></a>
            </div>
        </div>


<?php
        $output = ob_get_contents();
        ob_end_clean();
        ob_flush();
        exit(json_encode($output));
    }
}

/* ================function for user register================== */

if (!function_exists('lp_create_new_user')) {
    function lp_create_new_user($random_password, $user_name, $email)
    {
        global $listingpro_options;
        lp_mail_headers_append();
        $userinfo = array();
        if (empty($random_password)) {
            $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
        }
        $user_id = wp_create_user($user_name, $random_password, $email);
        if (is_wp_error($user_id)) {

            return false;
        } else {

            //send emails to user and admin on user registeration

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

            LP_send_mail($email, $formated_subject, $formated_mail_content, $headers);
            LP_send_mail($from, $subject2, $formated_mail_content2, $headers);

            //return $user_id;
            $userinfo = array(
                'id' => $user_id,
                'password' => $random_password,
            );
            lp_mail_headers_remove();
            return $userinfo;
        }
    }
}

/* ================function for user register================== */

if (!function_exists('lp_do_user_sign_in')) {
    function lp_do_user_sign_in($upassword, $user_name)
    {

        $creds['user_login'] = $user_name;
        $creds['user_password'] = $upassword;
        $creds['remember'] = true;
        $user = wp_signon($creds, true);
        if (is_wp_error($user)) {
            return false;
        } else {
            $user_id = $user->ID;
            return $user_id;
        }
    }
}
