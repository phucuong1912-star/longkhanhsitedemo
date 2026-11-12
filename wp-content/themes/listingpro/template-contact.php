<?php
/**
 * Template name: Contact Page
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */

get_header(); 
	global $listingpro_options;
	$contact_page_map_switch = $listingpro_options['contact_page_map_switch'];
	$cpFailedMessage = $listingpro_options['cp-failed-message'];
	$cpsuccessMessage = $listingpro_options['cp-success-message'];
	$gSiteKey = '';
	$gSiteKey = $listingpro_options['lp_recaptcha_site_key'];
	$enableCaptchaform = lp_check_receptcha('lp_recaptcha_contact');
	
	$privacy_policy = $listingpro_options['payment_terms_condition'];
	$privacy_contact = $listingpro_options['listingpro_privacy_contactform'];

	$errorMSG = '';
	$successMSG = '';
	



	$addressTitle = $listingpro_options['Address'];
	$cp_address = $listingpro_options['cp-address'];
	$cp_number = $listingpro_options['cp-number'];
	$cp_email = $listingpro_options['cp-email'];
	$cp_social_links = $listingpro_options['cp-social-links'];
	$formTitle = $listingpro_options['form-title'];
	$cpSuccessMessage = $listingpro_options['cp-success-message'];
	
	$cpLat = $listingpro_options['cp-lat'];
	$cpLan = $listingpro_options['cp-lan'];
	$showContactinfo = true;
	$cpShowcontactinfo = $listingpro_options['cp-show-contact-details'];
	if($cpShowcontactinfo=="1"){
		$showContactinfo = true;
	}
	else{
		$showContactinfo = false;
	}
	$lp_map_pin = $listingpro_options['lp_map_pin']['url'];
	$contact_wrap_center = '';
	$contact_wrap_align_center = '';
	if($contact_page_map_switch == false){
		
		$contact_wrap_center    = 'contact_center';
		$contact_wrap_align_center    = 'text-center';
	}
	$header_views_Class =   '';
    $header_views = lp_get_header_views();
    if( $header_views == 'header_with_topbar_menu' )
    {
        $header_views_Class =   'contact-style2';
    }

?>
		<!--==================================Section Open=================================-->
	<section class="clearfix">
		<?php
			if($contact_page_map_switch==true){
			?>
		<div class="contact-left">
			<div class="cp-lat" data-lat="<?php echo esc_attr($cpLat); ?>"></div>
			<div class="cp-lan" data-lan="<?php echo esc_attr($cpLan); ?>"></div>
			<div id="cpmap" class="contactmap" data-pinicon = "<?php echo esc_attr($lp_map_pin); ?>">
			</div>
		</div>
			<?php } ?>
		<div  class="<?php echo esc_attr($contact_wrap_align_center); ?>">
			<div  class="<?php echo esc_attr($header_views_Class); ?> padding-top-60 padding-bottom-50 contact-right <?php echo esc_attr($contact_wrap_center); ?>">
				<?php
					if($showContactinfo==true){
				?>
					<h3 class=" lp-border-bottom padding-bottom-20 margin-bottom-20"><?php echo esc_attr($addressTitle); ?></h3>
					<div class="address-box mr-bottom-30">
					<?php if(!empty($cp_address)){ ?>
						<p>
							<i class="fa fa-map-marker"></i>
							<span><?php echo esc_attr($cp_address); ?></span>
						</p>
					<?php } if(!empty($cp_number)){?>
						<p>
							<i class="fa fa-phone"></i>
                            <a href="tel:<?php echo esc_attr( $cp_number ); ?>"> <span><?php echo esc_attr($cp_number); ?></span></a>
						</p>
					<?php } if(!empty($cp_email)){?>
						<p>
							<i class="fa fa-envelope"></i>
                            <a href="mailto:<?php echo esc_attr($cp_email); ?>"> <span><?php echo esc_attr($cp_email); ?></span></a>
						</p>
					<?php } ?>
                        <?php
                        if( $header_views == 'header_with_topbar_menu' )
                        {
                             ?>
                            <?php
                            if($cp_social_links == 1){
                                $fb = $listingpro_options['fb_co'];
                                $tw = $listingpro_options['tw_co'];
                                $insta = $listingpro_options['insta_co'];
                                $tumb = $listingpro_options['tumb_co'];
                                $fyout = $listingpro_options['f_yout_co'];
                                $flinked = $listingpro_options['f_linked_co'];
                                $fpintereset = $listingpro_options['f_pintereset_co'];
                                $fvk = $listingpro_options['f_vk_co'];

                                ?>
                                <div class="lp-blog-grid-shares-contact-page">
                                    <?php if(!empty($fb)){ ?>
                                        <a href="<?php echo esc_attr($fb); ?>" class="lp-blog-grid-shares-icon icon-fb"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                    <?php } ?>
                                    <?php if(!empty($tw)){ ?>
                                        <a href="<?php echo esc_attr($tw); ?>" class="lp-blog-grid-shares-icon icon-tw"><i class="fa-brands fa-square-x-twitter" aria-hidden="true"></i></a>
                                    <?php } ?>
                                    <?php if(!empty($fpintereset)){ ?>
                                        <a href="<?php echo esc_url($fpintereset); ?>" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a>
                                    <?php } ?>

                                    <?php if(!empty($tumb)){ ?>
                                        <a href="" class="lp-blog-grid-shares-icon icon-pin"><i class="fa fa-delicious"></i></a>
                                    <?php } ?>
									
									<?php if(!empty($fyout)){ ?>
									
											<a href="<?php echo esc_url($fyout); ?>" class="lp-blog-grid-shares-icon icon-youu">
												<i class="fa fa-youtube" aria-hidden="true"></i>
											</a>
										
									<?php } ?>
									<?php if(!empty($flinked)){ ?>
										
											<a href="<?php echo esc_url($flinked); ?>" class="lp-blog-grid-shares-icon icon-lionked">
												<i class="fa fa-linkedin" aria-hidden="true"></i>
											</a>
										
									<?php } ?>
								
									<?php if(!empty($fvk)){ ?>
										
											<a href="<?php echo esc_url($fvk); ?>" class="lp-blog-grid-shares-icon icon-wk">
												<i class="fa fa-vk" aria-hidden="true"></i>
											</a>
										
									<?php } ?>
									<?php if(!empty($insta)){ ?>
										
											<a href="<?php echo esc_attr($insta); ?>" class="lp-blog-grid-shares-icon icon-insta">
											<i class="fa fa-instagram" aria-hidden="true"></i>
											</a>
										
									<?php } ?>

                                </div>
                            <?php } ?>
                            <?php
                        }
                        else
                        {
                            ?>
						<?php
						if($cp_social_links == 1){
                            $fb = $listingpro_options['fb_co'];
                            $tw = $listingpro_options['tw_co'];
                            $insta = $listingpro_options['insta_co'];
                            $tumb = $listingpro_options['tumb_co'];
                            $fyout = $listingpro_options['f_yout_co'];
                            $flinked = $listingpro_options['f_linked_co'];
                            $fpintereset = $listingpro_options['f_pintereset_co'];
                            $fvk = $listingpro_options['f_vk_co'];

						?>
							<ul class="social-icons post-socials contact-social">
							<?php if(!empty($fb)){ ?>
								<li>
									<a href="<?php echo esc_attr($fb); ?>"><!-- Facebook icon by Icons8 -->
										<?php echo listingpro_icons('fb'); ?>
									</a>
								</li>
							<?php } ?>
							<?php if(!empty($tw)){ ?>
								<li>
									<a href="<?php echo esc_attr($tw); ?>"><!-- LinkedIn icon by Icons8 -->
										<?php echo listingpro_icons('tw'); ?>
									</a>
								</li>
							<?php } ?>
							<?php if(!empty($insta)){ ?>
								<li>
									<a href="<?php echo esc_attr($insta); ?>"><!-- Instagram icon by Icons8 -->
										<?php echo listingpro_icons('insta'); ?>
									</a>
								</li>
							<?php } ?>
							<?php if(!empty($fyout)){ ?>
								<li>
									<a href="<?php echo esc_url($fyout); ?>" target="_blank">
										<?php echo listingpro_icons('yt'); ?>
									</a>
								</li>
							<?php } ?>
							<?php if(!empty($flinked)){ ?>
								<li>
									<a href="<?php echo esc_url($flinked); ?>" target="_blank">
										<?php echo listingpro_icons('clinkedin'); ?>
									</a>
								</li>
							<?php } ?>
							<?php if(!empty($fpintereset)){ ?>
								<li>
									<a href="<?php echo esc_url($fpintereset); ?>" target="_blank">
										<?php echo listingpro_icons('cinterest'); ?>
									</a>
								</li>
							<?php } ?>
							<?php if(!empty($tumb)){ ?>
								<li>
									<a href="<?php echo esc_url($tumb); ?>" target="_blank">
										<?php echo listingpro_icons('ctumbler'); ?>
									</a>
								</li>
							<?php } ?>
							<?php if(!empty($fvk)){ ?>
								<li>
									<a href="<?php echo esc_url($fvk); ?>" target="_blank">
										<?php echo listingpro_icons('cvk'); ?>
									</a>
								</li>
							<?php } ?>
							
							</ul><!-- ../social-icons-->
						<?php } ?>
                            <?php
                        }
                        ?>


					</div>
				<?php }?>
				<h3 class="margin-bottom-30 lp-border-bottom padding-bottom-20"><?php echo esc_attr($formTitle); ?></h3>
				<?php
                $show_listingpro_form   =   true;
                $contact_form_platform  =   $listingpro_options['contact_form_settings'];
                if( $contact_form_platform == 'cubewp_forms' && class_exists('CubeWp_Forms_Custom') )
                {
                	$show_listingpro_form   =   false;
                	$wpforms_lite_shortcod  = $listingpro_options['cubewp_forms_shortcode'];
                    
                }
                if( $contact_form_platform == 'wpforms_lite' && class_exists('WPForms') )
                {
                	$show_listingpro_form   =   false;
                	$wpforms_lite_shortcod  = $listingpro_options['wpform_lite_shortcode'];
                    
                }
                ?>	
                <?php
                if( $show_listingpro_form == false && !empty( $wpforms_lite_shortcod ) )
                {
                    echo do_shortcode( $wpforms_lite_shortcod );
                }
                else
                {
                    ?>
                    <form class="form-horizontal pos-relative" id="contactMSGForm" name="contactMSGForm" method="post" novalidate="novalidate" action="" data-lp-recaptcha="<?php echo wp_kses_post($enableCaptchaform); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>">
                        <div class="form-group">
                            <div class="col-sm-6">
                                <input class="form-control nameform" id="nameContacts" name="uname" placeholder="<?php esc_html_e('Name:','listingpro'); ?>" type="text" value="" required="">
                            </div>
                            <div class="col-sm-6">
                                <input class="form-control" id="emailContacts" name="uemail" placeholder="<?php esc_html_e('Email:','listingpro'); ?>" type="email" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input class="form-control" id="subjectContacts" name="usubject" placeholder="<?php esc_html_e('Subject:','listingpro'); ?>" type="text">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <textarea class="form-control" rows="5" id="messageContacts" name="umessage" placeholder="<?php esc_html_e('Message:','listingpro'); ?>"></textarea>
                            </div>
                        </div>
                        
                        <?php
                        if(!empty($privacy_policy) && $privacy_contact=="yes"){
                            ?>

                            <div class="form-group lp_privacy_policy_Wrap">
                                <input class="lpprivacycheckboxopt" id="reviewpolicycheck" type="checkbox" name="reviewpolicycheck" value="true">
                                <label for="reviewpolicycheck"><a target="_blank" href="<?php echo get_the_permalink($privacy_policy); ?>" class="help" target="_blank"><?php echo esc_html__('I Agree', 'listingpro'); ?></a></label>
                                <div class="help-text">
                                    <a class="help" target="_blank"><i class="fa fa-question"></i></a>
                                    <div class="help-tooltip">
                                        <p><?php echo esc_html__('You agree & accept our Terms & Conditions for posting this information?', 'listingpro'); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="submit" id="contactMSG" name="contactMSG" value="<?php esc_html_e('Send Message','listingpro'); ?>" class="lp-review-btn btn-second-hover" disabled>

                                </div>
                            </div>
                        <?php }else{ ?>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="submit" id="contactMSG" name="contactMSG" value="<?php esc_html_e('Send Message','listingpro'); ?>" class="lp-review-btn btn-second-hover">
									
									<div class="statuss" style="display:none">
										<span class="fa-1x">
											<i class="fa fa-spinner fa-spin"></i>
										</span>
									</div>

                                </div>
                            </div>
                        <?php }?>

                            <div id="success" style="display:none">
								<span class="green textcenter">
									<p></p>
								</span>
							</div>
                        
                            <div id="error"style="display:none">
								<span>
									<p></p>
								</span>
							</div>

                    </form>
                    <?php
                }
                ?>
			</div>
		</div>
        <?php the_content(); ?>
	</section>
	<!--==================================Section Close=================================-->
	
			
<?php 
get_footer();
?>