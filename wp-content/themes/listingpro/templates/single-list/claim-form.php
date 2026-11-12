<?php
global $listingpro_options;
$post_id='';
$post_title='';
$post_url='';
$post_author_id='';
$currentURL='';
$post_author_meta='';
$author_nicename='';
$author_url='';
$privacy_policy = $listingpro_options['payment_terms_condition'];
$privacy_claim = $listingpro_options['listingpro_privacy_claimform'];
$post_id = $post->ID;
$post_title = $post->post_title;
$post_url = get_permalink($post_id);
$currentURL = listingpro_url('listing-author');

$post_author_id= $post->post_author;
$post_author_meta = get_user_by( 'id', $post_author_id );
//print_r($post_author_meta);
if(!empty($post_author_meta)){
    $author_nicename = $post_author_meta->user_nicename;
    $author_user_email = $post_author_meta->user_email;
}

$author_url = get_author_posts_url( $post_author_id);

$claimIMG = $listingpro_options['lp_listing_claim_image']['url'];

//paid claim
$paidclaim = $listingpro_options['lp_listing_paid_claim_switch'];
$claimFormTopImg = lp_theme_option_url('lp_listing_form_top_image');
$claimFoDetailsSlider = lp_theme_option('lp_listing_claim_text_slider');

?>
<?php
if(class_exists('ListingproPlugin')) {
    if($paidclaim == 'yes'){
        ?>
        <div class="md-modal md-effect-3 single-page-popup planclaim-page-popup planclaim-page-popup-st" id="modal-2">
            <div class="md-content claimform-box">
                <!-- <h3><?php //echo esc_html('Claim This Business', 'listingpro'); ?> ( <?php echo get_the_title(); ?> )</h3> -->
                <div class="lp-claim-plan-container">
                    <div class="lp-plan-card">
                        <div class="lp-plan-front lp-plan-face">
                            <div class="lp-claim-plans">
                                <div class="col-md-10 col-md-offset-1 padding-bottom-40 lp-margin-top-case horizontal_view">
                                    <div class="lp-no-title-subtitleeeeeeeee">

                                        <?php echo esc_html__('Choose a Plan to Claim Your Business', 'listingpro'); ?>
                                    </div>
                                    <?php echo do_shortcode('[listingpro_claim_pricing]'); ?>
                                </div>
                            </div>
                            <a class="md-close lp-click-zindex"><i class="fa fa-close"></i></a>
                        </div>
                        <div class="lp-plan-back lp-plan-face">
                            <form class="form-horizontal lp-form-planclaim-st"  method="post" id="claimform" enctype="multipart/form-data">

                                <div class="col-md-6 col-xs-12 padding-0 leftside">
                                    <div class="claim-details insidewrp">
                                        
                                        <h2>
                                            <?php echo esc_html__('Claiming your business Listing', 'listingpro'); ?>
                                        </h2>
                                        <div class="">
                                            <input type="hidden" class="form-control" name="post_title" value="<?php echo esc_attr($post_title); ?>">
                                            <input type="hidden" class="form-control" name="post_url" value="<?php echo esc_attr($post_url); ?>">
                                            <input type="hidden" class="form-control" name="author_nicename" value="<?php echo esc_attr($author_nicename); ?>">
                                            <input type="hidden" class="form-control" name="author_url" value="<?php echo esc_attr($author_url); ?>">
                                            <input type="hidden" class="form-control" name="author_email" value="<?php echo esc_attr($author_user_email); ?>">
                                            <input type="hidden" class="form-control" name="post_id" value="<?php echo esc_attr($post_id); ?>">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo esc_html__('First', 'listingpro'); ?><span>*</span>
                                                        <input type="text" name="firstname" id="fullname" placeholder="<?php echo esc_html__('First Name', 'listingpro'); ?>">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><?php echo esc_html__('Last', 'listingpro'); ?><span>*</span>
                                                        <input type="text" name="lastname" id="lastname" placeholder="<?php echo esc_html__('Last Name', 'listingpro'); ?>">
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label><?php echo esc_html__('Business E-Mail', 'listingpro'); ?><span>*
												<div class="help-text">
													<a href="#" class="help"><i class="fa fa-question"></i></a>
													<div class="help-tooltip">
														<p><?php echo esc_html__("Please provide your business email which will be use for claim procedure.", "listingpro"); ?></p>
													</div>
												</div>
											</span>
                                                <input type="email" name="bemail" id="bemail" placeholder="<?php echo esc_html__('Business Email', 'listingpro'); ?>">
                                            </label>
                                        </div>




                                        <div class="form-group">
                                            <label><?php echo esc_html__('Phone', 'listingpro'); ?><span>*</span>
                                                <input type="text" name="phone" id="phoneClaim" placeholder="<?php echo esc_html__('111-111-234', 'listingpro'); ?>">
                                            </label>
                                        </div>

                                        <?php
                                        $paidClaim = lp_theme_option('lp_listing_paid_claim_switch');
                                        if($paidClaim=="yes"){
                                            ?>
                                            <div class="form-group" style="display: none !important;">

                                                <input type="hidden" value= '' name="lp_claimed_plan" id="lp_claimed_plan">

                                            </div>

                                            <input type="hidden" id="claim_type"  name="claim_type" value="paidclaims">

                                            <?php
                                        }
                                        ?>

                                        <div class="form-group">
                                            <label class="lp-cl-image-label"><?php echo esc_html__('Verfication Details', 'listingpro'); ?>
                                                <span>*<div class="help-text">
													<a href="#" class="help"><i class="fa fa-question"></i></a>
													<div class="help-tooltip">
														<p><?php echo esc_html__("Please provide your verification details which will be used for claim procedure.", "listingpro"); ?></p>
													</div>
												</div></span>
                                            </label>
                                            <div class="claim_file-btn-wrapper">
                                                <label for="my_file" class="custom-file-upload">
                                                    <i class="fa fa-paperclip"></i> <?php echo esc_html__('Attach File', 'listingpro'); ?>
                                                </label>
                                                <input id="my_file" name="claim_attachment" type="file" style="display:none;">
                                            </div>
                                            <textarea class="form-control textarea1" rows="5" name="message" id="message" placeholder="<?php echo esc_html__('Detail description about your listing', 'listingpro'); ?>" required></textarea>

                                           
                                        </div>


                                        <?php
                                        if(!is_user_logged_in()){

                                            ?>
                                            <!--signup-->
                                            <div class="signin-singup-section claim_signup">
                                                <div class="form-group">
                                                    <label><h6 class="newuserlabel"><?php echo esc_html__('NEW USER? TO SIGNUP ENTER AN EMAIL', 'listingpro'); ?></h6>
                                                        <input type="email" name="claim_new_user_email" id="claim_new_user_email" placeholder="<?php echo esc_html__('johndoe@mail.com', 'listingpro'); ?>">
                                                    </label>
                                                </div>
                                            </div>

                                            <!--signin-->

                                            <div class="signin-singup-section claim_signin">
                                                <div class="row">


                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><?php echo esc_html__('USERNAME OR EMAIL', 'listingpro'); ?><span>*</span>
                                                                <input type="text" name="claim_username" id="claim_username" placeholder="<?php echo esc_html__('Username', 'listingpro'); ?>">
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><?php echo esc_html__('PASSWORD', 'listingpro'); ?><span>*</span>
                                                                <input type="password" name="claim_userpass" id="claim_userpass" placeholder="<?php echo esc_html__('PASSWORD', 'listingpro'); ?>">
                                                            </label>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>

                                            <div class="checkbox singincheckboxx">
                                                <div class="form-group lp-claim-form-check-circle-new">
                                                    <label class="lp-signin-on-claim" for="lp-signin-on-claim"><input type="checkbox" id="lp-signin-on-claim" name="lp-signin-on-claim" value=""><span class="lp-new-checkbox-style"></span><span class="lp-new-checkbox-style2"><?php echo esc_html__('Returning user? Check this box to Sign in', 'listingpro'); ?></span></label>
                                                </div>
                                            </div>

                                            <?php
                                        }
                                        if(!empty($privacy_policy) && $privacy_claim=="yes"){
                                            ?>

                                            <div class="form-group lp_privacy_policy_Wrap  lp-claim-form-check-circle-new">
                                                
                                                <label for="reviewpolicycheck2">
												
												<input class="lpprivacycheckboxopt" id="reviewpolicycheck2" type="checkbox" name="reviewpolicycheck" value="true"><span class="lp-new-checkbox-style"></span><a target="_blank" href="<?php echo get_the_permalink($privacy_policy); ?>" class="help" target="_blank"><?php echo esc_html__('I Agree', 'listingpro'); ?></a></label>
                                                <div class="help-text">
                                                    <a class="help" target="_blank"><i class="fa fa-question"></i></a>
                                                    <div class="help-tooltip">
                                                        <p><?php echo esc_html__('You agree & accept our Terms & Conditions for posting this information?.', 'listingpro'); ?></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group mr-bottom-0">
                                                <input type="submit" value="<?php echo esc_html__('Claim your business now!', 'listingpro'); ?>" class="lp-review-btn btn-second-hover" disabled>
                                                <i class="fa fa-circle-o-notch fa-spin fa-2x formsubmitting"></i>

                                            </div>
                                            <?php
                                        }else{
                                            ?>
                                            <div class="form-group mr-bottom-0">
                                                <input type="submit" disabled value="<?php echo esc_html__('Claim your business now!', 'listingpro'); ?>" class="lp-review-btn btn-second-hover">
                                                <i class="fa fa-circle-o-notch fa-spin fa-2x formsubmitting"></i>

                                            </div>
                                            <?php
                                        }
                                        ?>

                                        <p class="claim_shield"><i class="fa fa-shield" aria-hidden="true"></i> <?php echo esc_html__('Claim request is processed after verification..', 'listingpro'); ?></p>
                                    </div>

                                    <div class="statuss text-center" style="display: none;">
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/claimsuccess.png" alt="image"/>
                                        <div class="text-center lp-claim-cuccess">
                                        </div>
                                        <a href="<?php echo esc_url( $currentURL ); ?>" class="lp-claim-cuccess-return"><?php echo esc_html__('Return to Dashboard', 'listingpro'); ?></a>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xs-12 padding-0 claim_formbgimage rightside" style="background: url('<?php echo esc_attr( $claimIMG ); ?>') no-repeat">
									<div class="rightside-overlay"></div>
                                    <div class="topwrap">
                                        <img alt="image" src="<?php echo esc_attr($claimFormTopImg); ?>" class="img-responsive center-block">
                                    </div>
                                    <div class="claim-text">

                                        <div class="claim-detailstext">
                                            <?php
                                            if(!empty($claimFoDetailsSlider)){
                                                ?>
                                                <div class="claim_slider">
                                                    <?php
                                                    foreach($claimFoDetailsSlider as $k=>$v){
                                                        ?>
                                                        <div class="slide">

                                                            <h5><?php echo esc_attr( $claimFoDetailsSlider["$k"]['title'] ); ?></h5>
                                                            <p><?php echo esc_attr( $claimFoDetailsSlider["$k"]['description'] ); ?></p>

                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                            }
                                            ?>

                                        </div>
                                    </div>

                                </div>
                            </form>
                            <a class="md-close lp-click-zindex"><i class="fa fa-close"></i></a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    <?php } ?>
<?php } ?>
<?php if($paidclaim == 'no') {  ?>
    <div class="md-modal md-effect-3 single-page-popup planclaim-page-popup planclaim-page-popup-st" id="modal-2">
        <div class="md-content claimform-box">
            <!-- <h3><?php //echo esc_html('Claim This Business', 'listingpro'); ?> ( <?php echo get_the_title(); ?> )</h3> -->

            <form class="form-horizontal lp-form-planclaim-st"  method="post" id="claimform" enctype="multipart/form-data">

                <div class="col-md-6 col-xs-12 padding-0 leftside">
                    <div class="claim-details insidewrp">
                        
                        <h2>
                            <?php echo esc_html__('Claiming your business Listing', 'listingpro'); ?>
                        </h2>
                        <div class="">
                            <input type="hidden" class="form-control" name="post_title" value="<?php echo esc_attr($post_title); ?>">
                            <input type="hidden" class="form-control" name="post_url" value="<?php echo esc_attr($post_url); ?>">
                            <input type="hidden" class="form-control" name="post_id" value="<?php echo esc_attr($post_id); ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo esc_html__('First', 'listingpro'); ?><span>*</span>
                                        <input required type="text" name="firstname" id="fullname" placeholder="<?php echo esc_html__('First Name', 'listingpro'); ?>">
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo esc_html__('Last', 'listingpro'); ?><span>*</span>
                                        <input required type="text" name="lastname" id="lastname" placeholder="<?php echo esc_html__('Last Name', 'listingpro'); ?>">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo esc_html__('Business E-Mail', 'listingpro'); ?><span>*
								<div class="help-text">
									<a href="#" class="help"><i class="fa fa-question"></i></a>
									<div class="help-tooltip">
										<p><?php echo esc_html__("Please provide your business email which will be use for claim procedure.", "listingpro"); ?></p>
									</div>
								</div>
							</span>
                                <input required type="email" name="bemail" id="bemail" placeholder="<?php echo esc_html__('Business Email', 'listingpro'); ?>">
                            </label>
                        </div>




                        <div class="form-group">
                            <label><?php echo esc_html__('Phone', 'listingpro'); ?><span>*</span>
                                <input required type="text" name="phone" id="phoneClaim" placeholder="<?php echo esc_html__('111-111-234', 'listingpro'); ?>">
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="lp-cl-image-label"><?php echo esc_html__('Verfication Details', 'listingpro'); ?>
                                <span>*<div class="help-text">
									<a href="#" class="help"><i class="fa fa-question"></i></a>
									<div class="help-tooltip">
										<p><?php echo esc_html__("Please provide your verification details which will be used for claim procedure.", "listingpro"); ?></p>
									</div>
								</div></span>
                            </label>
                            <div class="claim_file-btn-wrapper">
								<label for="my_file" class="custom-file-upload">
									<i class="fa fa-paperclip"></i> <?php echo esc_html__('Attach File', 'listingpro'); ?>
								</label>
								<input id="my_file" name="claim_attachment" type="file" style="display:none;">
							</div>
                            <textarea class="form-control textarea1" rows="5" name="message" id="message" placeholder="<?php echo esc_html__('Detail description about your listing', 'listingpro'); ?>" required></textarea>
                        </div>


                        <?php
                        if(!is_user_logged_in()){

                            ?>
                            <!--signup-->
                            <div class="signin-singup-section claim_signup">
                                <div class="form-group">
                                    <label><h6 class="newuserlabel"><?php echo esc_html__('NEW USER? TO SIGNUP ENTER AN EMAIL', 'listingpro'); ?></h6>
                                        <input  type="email" name="claim_new_user_email" id="claim_new_user_email" placeholder="<?php echo esc_html__('johndoe@mail.com', 'listingpro'); ?>">
                                    </label>
                                </div>
                            </div>

                            <!--signin-->

                            <div class="signin-singup-section claim_signin">
                                <div class="row">


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo esc_html__('USERNAME OR EMAIL', 'listingpro'); ?><span>*</span>
                                                <input type="text" name="claim_username" id="claim_username" placeholder="<?php echo esc_html__('Username', 'listingpro'); ?>">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo esc_html__('PASSWORD', 'listingpro'); ?><span>*</span>
                                                <input type="password" name="claim_userpass" id="claim_userpass" placeholder="<?php echo esc_html__('PASSWORD', 'listingpro'); ?>">
                                            </label>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <div class="checkbox singincheckboxx">
                                <div class="form-group lp-claim-form-check-circle-new">
                                    <label class="lp-signin-on-claim" for="lp-signin-on-claim"><input type="checkbox" id="lp-signin-on-claim" name="lp-signin-on-claim" value=""><span class="lp-new-checkbox-style"></span><span class="lp-new-checkbox-style2"><?php echo esc_html__('Returning user? Check this box to Sign in', 'listingpro'); ?></span></label>
                                </div>
                            </div>

                            <?php
                        }
                        if(!empty($privacy_policy) && $privacy_claim=="yes"){
                            ?>

                             <div class="form-group lp_privacy_policy_Wrap  lp-claim-form-check-circle-new">
                                                
								<label for="reviewpolicycheck2">
								<input class="lpprivacycheckboxopt" id="reviewpolicycheck2" type="checkbox" name="reviewpolicycheck" value="true"><span class="lp-new-checkbox-style"></span><a target="_blank" href="<?php echo get_the_permalink($privacy_policy); ?>" class="help" target="_blank"><?php echo esc_html__('I Agree', 'listingpro'); ?></a></label>
								<div class="help-text">
									<a class="help" target="_blank"><i class="fa fa-question"></i></a>
									<div class="help-tooltip">
										<p><?php echo esc_html__('You agree & accept our Terms & Conditions for posting this information?.', 'listingpro'); ?></p>
									</div>
								</div>
							</div>

                            <div class="form-group mr-bottom-0">
                                <input type="submit" disabled value="<?php echo esc_html__('Claim your business now!', 'listingpro'); ?>" class="lp-review-btn btn-second-hover">
                                <i class="fa fa-circle-o-notch fa-spin fa-2x formsubmitting"></i>

                            </div>
                            <?php
                        }else{
                            ?>
                            <div class="form-group mr-bottom-0">
                                <input type="submit" disabled value="<?php echo esc_html__('Claim your business now!', 'listingpro'); ?>" class="lp-review-btn btn-second-hover">
                                <i class="fa fa-circle-o-notch fa-spin fa-2x formsubmitting"></i>
                            </div>
                            <?php
                        }
                        ?>

                        <p class="claim_shield"><i class="fa fa-shield" aria-hidden="true"></i> <?php echo esc_html__('Claim request is processed after verification..', 'listingpro'); ?></p>
                    </div>

                    <div class="statuss text-center" style="display: none;">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/claimsuccess.png" alt="image"/>
                        <div class="text-center lp-claim-cuccess">
                        </div>
                        <a href="<?php echo esc_url( $currentURL ); ?>" class="lp-claim-cuccess-return"><?php echo esc_html__('Return to Dashboard', 'listingpro'); ?></a>
                    </div>

                </div>

                <div class="col-md-6 col-xs-12 padding-0 claim_formbgimage rightside" style="background: url('<?php echo esc_attr( $claimIMG ); ?>') no-repeat">
					<div class="rightside-overlay"></div>
                    <div class="topwrap">
                        <img alt="image" src="<?php echo esc_attr($claimFormTopImg); ?>" class="img-responsive center-block">
                    </div>
                    <div class="claim-text">

                        <div class="claim-detailstext">
                            <?php
                            if(!empty($claimFoDetailsSlider)){
                                ?>
                                <div class="claim_slider">
                                    <?php
                                    foreach($claimFoDetailsSlider as $k=>$v){
                                        ?>
                                        <div class="slide">

                                            <h5><?php echo esc_attr( $claimFoDetailsSlider["$k"]['title'] ); ?></h5>
                                            <p><?php echo esc_attr( $claimFoDetailsSlider["$k"]['description'] ); ?></p>

                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                            ?>

                        </div>
                    </div>

                </div>
            </form>
            <a class="md-close lp-click-zindex"><i class="fa fa-close"></i></a>
        </div>
    </div>
<?php } ?>
<!-- Popup Close -->
<div class="md-overlay md-close lp-click-zindex"></div>
<?php if(is_user_logged_in()) { ?>
<div class="claimform">
    <h3><?php echo esc_html__('Claim This Listing', 'listingpro');?></h3>
    <div class="">
        <form class="form-horizontal"  method="post" id="claimformmobile">
            <div class="form-group">
                <input type="hidden" class="form-control" name="post_title" value="<?php echo esc_attr($post_title); ?>">
                <input type="hidden" class="form-control" name="post_url" value="<?php echo esc_attr($post_url); ?>">
                <input type="hidden" class="form-control" name="post_id" value="<?php echo esc_attr($post_id); ?>">
            </div>



            <div class="form-group">
                <input class="form-control" type="text" name="firstname" id="firstname" placeholder="<?php echo esc_html__('First Name', 'listingpro'); ?>">
            </div>


            <div class="form-group">
                <input class="form-control" type="text" name="lastname" id="lastname" placeholder="<?php echo esc_html__('Last Name', 'listingpro'); ?>">
            </div>



            <div class="form-group">
                <input class="form-control" type="text" name="bemail" id="bemail" placeholder="<?php echo esc_html__('Business Name', 'listingpro'); ?>">
            </div>


            <div class="form-group">
                <input class="form-control" type="text" name="phone" id="phoneClaim" placeholder="<?php echo esc_html__('111-111-234', 'listingpro'); ?>">
            </div>


            <div class="form-group">
                <textarea class="form-control textarea1" rows="5" name="message" id="lpmessage" placeholder="<?php echo esc_html__('Message:','listingpro');?>"></textarea>
            </div>


            <div class="form-group">
                <input type="file" name="claim_attachment" />
            </div>


            <?php
            if(!empty($privacy_policy) && $privacy_claim=="yes"){
                ?>
                <div class="form-group lp_privacy_policy_Wrap">
                    <input class="lpprivacycheckboxopt" id="reviewpolicycheck3" type="checkbox" name="reviewpolicycheck" value="true">
                    <label for="reviewpolicycheck3"><a target="_blank" href="<?php echo get_the_permalink($privacy_policy); ?>" class="help" target="_blank"><?php echo esc_html__('I Agree', 'listingpro'); ?></a></label>
                    <div class="help-text">
                        <a class="help" target="_blank"><i class="fa fa-question"></i></a>
                        <div class="help-tooltip">
                            <p><?php echo esc_html__('You agree & accept our Terms & Conditions for posting this information?.', 'listingpro'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="form-group mr-bottom-0">
                    <input type="submit" value="<?php echo esc_html__('Submit','listingpro');?>" class="lp-review-btn btn-second-hover" disabled>
                    <i class="fa fa-circle-o-notch fa-spin fa-2x formsubmitting"></i>
                </div>
            <?php } else{ ?>
                <div class="form-group mr-bottom-0">
                    <input type="submit" value="<?php echo esc_html__('Submit','listingpro');?>" class="lp-review-btn btn-second-hover">
                    <i class="fa fa-circle-o-notch fa-spin fa-2x formsubmitting"></i>
                </div>
            <?php } ?>
        </form>
    </div>
</div>
<?php
}
if(!empty($privacy_policy) && $privacy_claim=="yes"){
    $text="true";
}else{
    $text="false";
}
?>
<input type="hidden" value="<?php echo esc_attr( $text ); ?>" id="textforjq">
