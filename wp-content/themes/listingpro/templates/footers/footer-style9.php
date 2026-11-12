<?php
global $listingpro_options;
	$copy_right = $listingpro_options['copy_right'];
	$footer_logo    =   $listingpro_options['footer_logo'];
    $footer_address = $listingpro_options['footer_address'];
    $phone_number = $listingpro_options['phone_number'];
    $author_info = $listingpro_options['author_info'];
    $fb = $listingpro_options['fb'];
    $tw = $listingpro_options['tw'];
    $insta = $listingpro_options['insta'];
    $tumb = $listingpro_options['tumb'];
    $fyout = $listingpro_options['f-yout'];
    $flinked = $listingpro_options['f-linked'];
    $fpintereset = $listingpro_options['f-pintereset'];
    $fvk = $listingpro_options['f-vk'];
    $footer_style = $listingpro_options['footer_style'];

?>

<!--footer 9-->
<div class="clearfix"></div>
<footer class="footer-style9">
        <div class="footer9-bottom-area padding-top-60 padding-bottom-60">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="lp-footer-logo">
                            <a href="<?php echo esc_url(home_url('/')); ?>">
                                <?php
                                if( isset( $footer_logo ) && !empty( $footer_logo ) ):
                                ?>
                                <img src="<?php echo esc_url($footer_logo['url']); ?>" alt="image">
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                         <?php
                            if(has_nav_menu('footer_menu')):
                        ?>
                        
                            <?php echo listingpro_footer_menu(); ?>
                        
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <?php if(!empty($tw) || !empty($fb) || !empty($insta) || !empty($tumb) || !empty($fpintereset) || !empty($flinked) || !empty($fyout) || !empty($fvk)){ ?>
                                    <ul class="social-icons footer-social-icons">
                                        <?php if(!empty($fb)){ ?>
                                            <li>
                                                <a href="<?php echo esc_url($fb); ?>" target="_blank">
                                                   <i class="fa fa-facebook" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if(!empty($tw)){ ?>
                                            <li>
                                                <a href="<?php echo esc_url($tw); ?>" target="_blank">
                                                    <i class="fa-brands fa-square-x-twitter" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if(!empty($insta)){ ?>
                                            <li>
                                                <a href="<?php echo esc_url($insta); ?>" target="_blank">
                                                    <i class="fa fa-instagram" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if(!empty($fyout)){ ?>
                                            <li>
                                                <a href="<?php echo esc_url($fyout); ?>" target="_blank">
													<i class="fa fa-youtube" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if(!empty($flinked)){ ?>
                                            <li>
                                                <a href="<?php echo esc_url($flinked); ?>" target="_blank">
                                                    <i class="fa fa-linkedin-square" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if(!empty($fpintereset)){ ?>
                                            <li>
                                                <a href="<?php echo esc_url($fpintereset); ?>" target="_blank">
												<i class="fa fa-pinterest" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if(!empty($tumb)){ ?>
                                            <li>
                                                <a href="<?php echo esc_url($tumb); ?>" target="_blank">
													<i class="fa fa-tumblr" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if(!empty($fvk)){ ?>
                                            <li>
                                                <a href="<?php echo esc_url($fvk); ?>" target="_blank">
                                                    <i class="fa fa-vk" aria-hidden="true"></i>
                                                </a>
                                            </li>
                                        <?php } ?>

                                    </ul>
                                <?php } ?>
                    </div>
                   
                </div>
            </div>
        </div>
</footer>