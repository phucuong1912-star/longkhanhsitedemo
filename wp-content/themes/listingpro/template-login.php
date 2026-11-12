<?php
/**
 * Template name: Login Page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */

get_header(); 
if(!is_user_logged_in()){
	global $listingpro_options;
	$enablepassword = false;
	if(isset($listingpro_options['lp_register_password'])){
		if($listingpro_options['lp_register_password']==1){
			$enablepassword = true;
		}
	}
	$privacy_policy = $listingpro_options['payment_terms_condition'];
	$gSiteKey = '';
	$gSiteKey = $listingpro_options['lp_recaptcha_site_key'];
	$enableCaptcha = lp_check_receptcha('lp_recaptcha_registration');		
	$enableCaptchaLogin = lp_check_receptcha('lp_recaptcha_login');	
?>
			<!--==================================Section Open=================================-->
	<section>
		
		<div class="lp-section-row aliceblue">
			<div class="lp-section-content-container-one">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
                            <?php
                            $popup_style   =   $listingpro_options['login_popup_style'];
                            if( !isset( $popup_style ) || empty( $popup_style ) || !$popup_style )
                            {
                                $popup_style    =   'style1';
                            }
                            if( $popup_style == 'style2' ) {
                                ?>
                                <div id="login-style2-page-wrap">
                                    <div class="style2-popup-login" id="app-view-login-popup">
                                        <?php
                                        get_template_part('mobile/templates/login-reg-popup');
                                        ?>
                                    </div>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="login-form-popup lp-border-radius-8">
                                    <div class="siginincontainer">
                                        <h1 class="text-center"><?php esc_html_e('Sign in','listingpro'); ?></h1>
                                        <form data-lp-recaptcha="<?php echo wp_kses_post($enableCaptchaLogin); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>" id="lp-login-temp" class="form-horizontal margin-top-30"  method="post">
                                            <p class="status"></p>
                                            <div class="form-group">
                                                <label for="lpusernameT"><?php esc_html_e('Username or Email Address *','listingpro'); ?></label>
                                                <input type="text" class="form-control" id="lpusernameT" name="lpusernameT" />
                                            </div>
                                            <div class="form-group">
                                                <label for="lppasswordT"><?php esc_html_e('Password *','listingpro'); ?></label>
                                                <input type="password" class="form-control" id="lppasswordT" name="lppasswordT" />
                                            </div>
                                            <div class="form-group">
                                                <?php
                                                if($enableCaptchaLogin==true){
                                                    if ( class_exists( 'cridio_Recaptcha' ) ){
                                                        if ( cridio_Recaptcha_Logic::is_recaptcha_enabled() ) {
                                                            echo  '<div style="transform:scale(0.88);-webkit-transform:scale(0.88);transform-origin:0 0;-webkit-transform-origin:0 0;" id="recaptcha-'.get_the_ID().'" class="g-recaptcha" data-sitekey="'.$gSiteKey.'"></div>';
                                                        }
                                                    }
                                                }

                                                ?>
                                            </div>
                                            <div class="form-group">
                                                <div class="checkbox pad-bottom-10">
                                                    <input id="check2" type="checkbox" name="remember" value="price-on-call">
                                                    <label for="check2"><?php esc_html_e('Keep me signed in','listingpro'); ?></label>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <input type="submit" value="<?php esc_html_e('Sign in','listingpro'); ?>" class="lp-secondary-btn width-full btn-first-hover" />
                                            </div>
                                            <?php wp_nonce_field( 'ajax-login-nonce', 'lpsecurityT' ); ?>
                                        </form>
                                        <div class="pop-form-bottom">
                                            <div class="bottom-links">
                                                <a  class="signUpClick"><?php esc_html_e('Not a member? Sign up','listingpro'); ?></a>
                                                <a  class="forgetPasswordClick pull-right" ><?php esc_html_e('Forgot Password?','listingpro'); ?></a>
                                            </div>

                                            <?php if ( class_exists( 'NextendSocialLogin' ) ) {
                                                if (count(NextendSocialLogin::$enabledProviders) > 0) {
                                                    ?>
                                                    <p class="margin-top-15"><?php esc_html_e('Connect with your Social Network','listingpro'); ?></p>
                                                    <ul class="social-login list-style-none">

                                                        <?php
                                                        foreach (NextendSocialLogin::$providers AS $provider){
                                                            $state         = $provider->getState();
                                                            $providerLable = $provider->getLabel();
                                                            switch ($state) {
                                                                case 'enabled':
                                                                    if($providerLable=="Google"){
                                                                        ?>


                                                                        <li>
                                                                            <a id="logingoogle" class="google flaticon-googleplus" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=google" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=google&redirect='+window.location.href; return false;">
                                                                                <i class="fa fa-google-plus"></i>
                                                                                <span><?php esc_html_e('Google','listingpro'); ?></span>
                                                                            </a>
                                                                        </li>

                                                                        <?php
                                                                    }
                                                                    if($providerLable=="Facebook"){
                                                                        ?>

                                                                        <li>
                                                                            <a id="loginfacebook" class="facebook flaticon-facebook" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=facebook" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=facebook&redirect='+window.location.href; return false;">
                                                                                <i class="fa fa-facebook"></i>
                                                                                <span><?php esc_html_e('Facebook','listingpro'); ?></span>
                                                                            </a>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    if($providerLable=="Twitter"){
                                                                        ?>

                                                                        <li>
                                                                            <a id="logintwitter" class="twitter flaticon-twitter" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=twitter" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=twitter&redirect='+window.location.href; return false;">
                                                                                <i class="fa-brands fa-square-x-twitter"></i>
                                                                                <span><?php esc_html_e('Twitter','listingpro'); ?></span>
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
                                    </div>
                                    <div class="siginupcontainer">
                                        <h1 class="text-center"><?php esc_html_e('Sign Up','listingpro'); ?></h1>
                                        <form data-lp-recaptcha="<?php echo wp_kses_post($enableCaptcha); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>" id="registertmp" class="form-horizontal margin-top-30"  method="post">
                                            <p class="status"></p>
                                            <div class="form-group">
                                                <label for="username2T"><?php esc_html_e('Username *','listingpro'); ?></label>
                                                <input type="text" class="form-control" id="username2T" name="username2T" />
                                            </div>
                                            <div class="form-group">
                                                <label for="emailT"><?php esc_html_e('Email Address *','listingpro'); ?></label>
                                                <input type="email" class="form-control" id="emailT" name="emailT" />
                                            </div>
                                            <?php if($enablepassword==true){ ?>
                                                <div class="form-group">
                                                    <label for="upasswordT"><?php esc_html_e('Password *','listingpro'); ?></label>
                                                    <input type="password" class="form-control" id="upasswordT" name="upasswordT" />
                                                </div>
                                            <?php } ?>
                                            <?php if($enablepassword==false){ ?>
                                                <div class="form-group">
                                                    <p><?php esc_html_e('Password will be e-mailed to you.','listingpro'); ?></p>
                                                </div>
                                            <?php } ?>
                                            <?php
                                            if(!empty($privacy_policy)){

                                                echo '
												<div class="checkbox form-group check_policyy termpolicy pull-left termpolicy-wraper">
													<input id="check_policyy" type="checkbox" name="policycheck" value="true">
													<label for="check_policyy"><a target="_blank" href="'.get_the_permalink($privacy_policy).'" class="help" target="_blank">'.esc_html__('I Agree', 'listingpro').'</a></label>
													<div class="help-text">
														<a class="help" target="_blank"><i class="fa fa-question"></i></a>
														<div class="help-tooltip">
															<p>'.esc_html__('You agree you accept our Terms & Conditions for posting this ad.', 'listingpro').'</p>
														</div>
													</div>
												</div>';
                                            }
                                            ?>
                                            <div class="form-group pull-left">
                                                <?php
                                                if($enableCaptcha==true){
                                                    if ( class_exists( 'cridio_Recaptcha' ) ){
                                                        if ( cridio_Recaptcha_Logic::is_recaptcha_enabled() ) {
                                                            echo  '<div style="transform:scale(0.88);-webkit-transform:scale(0.88);transform-origin:0 0;-webkit-transform-origin:0 0;" id="recaptcha-'.get_the_ID().'" class="g-recaptcha" data-sitekey="'.$gSiteKey.'"></div>';
                                                        }
                                                    }
                                                }

                                                ?>
                                            </div>
                                            <div class="form-group">
                                                <input id="lp-template-registerbtn" type="submit" value="<?php esc_html_e('Register','listingpro'); ?>" class="lp-secondary-btn width-full btn-first-hover" />
                                            </div>

                                            <?php wp_nonce_field( 'ajax-register-nonce', 'lpsecurityregT' ); ?>
                                        </form>
                                        <div class="pop-form-bottom">
                                            <div class="bottom-links">
                                                <a class="signInClick" ><?php esc_html_e('Already have an account? Sign in','listingpro'); ?></a>
                                                <a class="forgetPasswordClick pull-right" ><?php esc_html_e('Forgot Password?','listingpro'); ?></a>
                                            </div>
                                            <?php if ( class_exists( 'NextendSocialLogin' ) ) {
                                                if (count(NextendSocialLogin::$enabledProviders) > 0) {
                                                    ?>
                                                    <p class="margin-top-15"><?php esc_html_e('Connect with your Social Network','listingpro'); ?></p>
                                                    <ul class="social-login list-style-none">

                                                        <?php
                                                        foreach (NextendSocialLogin::$providers AS $provider){
                                                            $state         = $provider->getState();
                                                            $providerLable = $provider->getLabel();
                                                            switch ($state) {
                                                                case 'enabled':
                                                                    if($providerLable=="Google"){
                                                                        ?>


                                                                        <li>
                                                                            <a id="logingoogle" class="google flaticon-googleplus" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=google" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=google&redirect='+window.location.href; return false;">
                                                                                <i class="fa fa-google-plus"></i>
                                                                                <span><?php esc_html_e('Google','listingpro'); ?></span>
                                                                            </a>
                                                                        </li>

                                                                        <?php
                                                                    }
                                                                    if($providerLable=="Facebook"){
                                                                        ?>

                                                                        <li>
                                                                            <a id="loginfacebook" class="facebook flaticon-facebook" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=facebook" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=facebook&redirect='+window.location.href; return false;">
                                                                                <i class="fa fa-facebook"></i>
                                                                                <span><?php esc_html_e('Facebook','listingpro'); ?></span>
                                                                            </a>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    if($providerLable=="Twitter"){
                                                                        ?>

                                                                        <li>
                                                                            <a id="logintwitter" class="twitter flaticon-twitter" href="<?php echo get_site_url(); ?>/wp-login.php?loginSocial=twitter" onclick="window.location = '<?php echo get_site_url(); ?>/wp-login.php?loginSocial=twitter&redirect='+window.location.href; return false;">
                                                                                <i class="fa-brands fa-square-x-twitter"></i>
                                                                                <span><?php esc_html_e('Twitter','listingpro'); ?></span>
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
                                    </div>
                                    <div class="forgetpasswordcontainer">
                                        <h1 class="text-center"><?php esc_html_e('Forgotten Password','listingpro'); ?></h1>
                                        <form id="lp_forget_pass_formm" class="form-horizontal margin-top-30"  method="post" >
                                            <p class="status"></p>
                                            <div class="form-group">
                                                <label for="password"><?php esc_html_e('Email Address *','listingpro'); ?></label>
                                                <input type="email" class="form-control" id="email2" />
                                            </div>
                                            <div class="form-group">
                                                <input type="submit" value="<?php esc_html_e('Get New Password','listingpro'); ?>" class="lp-secondary-btn width-full btn-first-hover" />
                                                <?php wp_nonce_field( 'ajax-forgetpass-nonce', 'security4' ); ?>
                                            </div>
                                        </form>
                                        <div class="pop-form-bottom">
                                            <div class="bottom-links">
                                                <a class="cancelClick" ><?php esc_html_e('Cancel','listingpro'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
						</div>
					</div>
				</div>
			</div>
		</div><!-- ../section-row -->
	
	</section>
	<!--==================================Section Close=================================-->
	
			
<?php 
}else{
	wp_redirect(esc_url(home_url('/')));
	exit();
}
get_footer();
?>