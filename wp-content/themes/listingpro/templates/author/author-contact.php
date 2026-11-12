<?php

$authorID   =   $GLOBALS['authorID'];

$author_fb    =   get_the_author_meta( 'facebook', $authorID );
$author_tw    =   get_the_author_meta( 'twitter', $authorID );
$author_pin    =   get_the_author_meta( 'pinterest', $authorID );
$author_insta    =   get_the_author_meta( 'instagram', $authorID );
$author_link    =   get_the_author_meta( 'linkedin', $authorID );
$author_phone    =   get_the_author_meta( 'phone', $authorID );
$author_website    =   get_the_author_meta( 'url', $authorID );
$author_email    =   get_the_author_meta( 'email', $authorID );
$author_address    =   get_the_author_meta( 'address', $authorID );

?>



<div class="author-contact-wrap">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">


            <?php
            global $listingpro_options, $post;

            $showleadform = true;

            $privacy_policy = $listingpro_options['payment_terms_condition'];
            $privacy_leadform = $listingpro_options['listingpro_privacy_leadform'];
            ?>

            <?php if($showleadform == true) { ?>

                <div class="lp-listing-leadform lp-widget-inner-wrap">



                    <h4><?php echo esc_html__( 'Contact with business owner', 'listingpro' ); ?></h4>

                    <div class="lp-listing-leadform-inner">

                        <form class="form-horizontal hidding-form-feilds margin-top-20"  method="post" id="contactOwner">

                            <?php

                            $author_id = '';

                            $author_email = '';

                            $author_email = get_the_author_meta( 'user_email' );

                            $author_id = get_the_author_meta( 'ID' );




                            ?>

                            <div class="form-group">
                                <input type="text" class="form-control" name="name7" id="name7" placeholder="<?php esc_html_e('Name:','listingpro'); ?>">
                                <span id="name7"></span>
                            </div>

                            <div class="form-group form-group-icon">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                <input type="email" class="form-control" name="email7" id="email7" placeholder="<?php esc_html_e('Email:','listingpro'); ?>">

                            </div>

                            <div class="form-group">
                                <input type="text" class="form-control" name="phone7" id="phone7" placeholder="<?php esc_html_e('Phone','listingpro'); ?>">
                                <span id="phone7"></span>
                            </div>

                            <div class="form-group">
                                <textarea class="form-control" rows="5" name="message7" id="message7" placeholder="<?php esc_html_e('Message:','listingpro'); ?>"></textarea>
                            </div>


                            <?php
                            if( !empty( $privacy_policy  ) && $privacy_leadform == 'yes' )
                            {
                                ?>
                                <div class="form-group lp_privacy_policy_Wrap">
                                    <input class="lpprivacycheckboxopt" id="reviewpolicycheck" type="checkbox" name="reviewpolicycheck" value="true">
                                    <label for="reviewpolicycheck"><a target="_blank" href="<?php echo get_the_permalink($privacy_policy); ?>" class="help" target="_blank"><?php echo esc_html__('I Agree', 'listingpro'); ?></a></label>
                                    <div class="help-text">
                                        <a class="help" target="_blank"><i class="fa fa-question"></i></a>
                                        <div class="help-tooltip">
                                            <p><?php echo esc_html__('You agree & accept our Terms & Conditions for submitting this information?', 'listingpro'); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group margin-bottom-0 pos-relative">
                                    <input type="submit" value="<?php esc_html_e('Send','listingpro'); ?>" class="lp-review-btn btn-second-hover" disabled>
                                    <input type="hidden" value="<?php the_ID(); ?>" name="post_id">
                                    <input type="hidden" value="<?php echo esc_attr($author_id); ?>" name="author_id">
                                    <i class="lp-search-icon fa fa-send"></i>
                                </div>
                                <?php
                            }
                            else
                            {
                                ?>
                                <div class="form-group margin-bottom-0 pos-relative">
                                    <input type="submit" value="<?php esc_html_e('Send','listingpro'); ?>" class="lp-review-btn btn-second-hover">
                                    <input type="hidden" value="<?php the_ID(); ?>" name="post_id">
                                    <input type="hidden" value="<?php echo esc_attr($author_id); ?>" name="author_id">
                                    <i class="lp-search-icon fa fa-send"></i>
                                </div>
                                <?php
                            }
                            ?>

                        </form>
                        <!--start lead form success msg section-->
                        <div class="lp-lead-success-msg-outer">
                            <div class="lp-lead-success-msg">
                                <p><img alt='image' src="<?php echo listingpro_icons_url('lp_lead_success')?>"><?php esc_html_e('Your request has been submitted successfully.', 'listingpro'); ?></p>
                            </div>
                            <span class="lp-cross-suces-layout"><i class="fa fa-times" aria-hidden="true"></i></span>
                        </div>
                        <!--end lead form success msg section-->

                    </div>

                </div>

            <?php } ?>


        </div>

        <div class="clearfix"></div>

    </div>

    <!--    <h4 class="author-contact-second-heading">--><?php //echo esc_html__( 'Social' ); ?><!--</h4>-->



</div>